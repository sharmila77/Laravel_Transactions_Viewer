<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PipedriveController extends Controller
{
    protected $pipedriveApiToken;

    public function __construct()
    {
        $this->pipedriveApiToken = env('PIPEDRIVE_API_TOKEN'); // Make sure you have this in your .env
    }

    
    public function handleCallback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return response('Missing authorization code', 400);
        }

        $response = Http::asForm()->post('https://oauth.pipedrive.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => env('PIPEDRIVE_REDIRECT_URI'),
            'client_id' => env('PIPEDRIVE_CLIENT_ID'),
            'client_secret' => env('PIPEDRIVE_CLIENT_SECRET'),
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'OAuth failed',
                'details' => $response->json(),
            ], 500);
        }

        $data = $response->json();

        return response()->json([
            'message' => '✅ Successfully connected to Pipedrive',
            'token_data' => $data,
        ]);
    }

    public function showPanel(Request $request)
    {
        $tab = $request->query('tab', 'invoices');
        $personId = $request->query('selectedIds') ?? $request->query('personId');

        if (!$personId) {
            return response('<p>Error: personId parameter is missing.</p>', 400);
        }

        $email = $this->getEmailFromPersonId($personId);
        if (!$email) {
            return '<p>Error: Could not find email for personId ' . e($personId) . '</p>';
        }

        $response = Http::get('https://octopus-app-3hac5.ondigitalocean.app/api/stripe_data', [
            'email' => $email,
        ]);

        $invoices = $charges = [];
        $data = '';
        if ($response->status() == 429) {
            // Rate limit exceeded
            $retryAfter = $response->header('Retry-After', 60);
            $data = '<div class="alert alert-warning">Rate limit exceeded. Please try again after ' . $retryAfter . ' seconds.</div>';
        } elseif ($response->successful()) {
            $stripeData = $response->json();
            $invoices = $stripeData['invoices'] ?? [];
            $charges = $stripeData['charges'] ?? [];

            if ($tab === 'invoices') {
                $data = view('pipedrive.partials.invoices_card', [
                    'invoices' => $invoices,
                ])->render();
            } else {
                $data = view('pipedrive.partials.payments_card', [
                    'payments' => $charges,
                ])->render();
            }
        } else {
            $data = '<p>Error fetching data.</p>';
        }

        return response()
            ->view('pipedrive.panel', [
                'data' => $data,
                'tab' => $tab,
                'personId' => $personId,
                'invoiceCount' => count($invoices),
                'paymentCount' => count($charges),
            ])
            ->header('ngrok-skip-browser-warning', 'true')
            ->header('X-Frame-Options', 'ALLOWALL')
            ->header('Content-Security-Policy', "frame-ancestors 'self' https://*.pipedrive.com");
    }

    private function getEmailFromPersonId($personId)
    {
        $apiKey = env('PIPEDRIVE_API_TOKEN');
        $response = Http::get("https://api.pipedrive.com/v1/persons/{$personId}", [
            'api_token' => $apiKey,
        ]);

        if ($response->ok()) {
            $person = $response->json('data');
            return $person['email'][0]['value'] ?? null;
        }

        return null;
    }

    public function getStripeData(Request $request)
    {
        $email = $request->query('email');
        if (!$email) {
            return response()->json(['error' => 'Email parameter is required'], 400);
        }

        // Call your internal API to get stripe data
        $apiUrl = 'https://octopus-app-3hac5.ondigitalocean.app/api/stripe_data?email=' . urlencode($email);

        try {
          $response = Http::get($apiUrl);

          if ($response->failed()) {
              return response()->json(['error' => 'Failed to fetch Stripe data'], 500);
          }

        $data = $response->json();
        // Return only invoices and charges
        return response()->json([
          'invoices' => $data['invoices'] ?? [],
          'payments' => $data['charges'] ?? [],
        ]);

        } catch (\Exception $e) {
           return response()->json(['error' => 'Exception: ' . $e->getMessage()], 500);
        }
    }
}
