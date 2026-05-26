<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventTicket;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use PKPass\PKPass;
use Illuminate\Support\Facades\Log;


class TicketScanController extends Controller
{
    /**
     * Serve public/tickets/qr_{uuid}.png (legacy URL used in ticket emails).
     */
    public function ticketQrPng($uuid)
    {
        $path = ticket_email_qr_disk_path($uuid);

        if (!is_file($path)) {
            $ticket = EventTicket::where('ticket_uuid', $uuid)->firstOrFail();
            $scanUrl = url('/ticket/scan/' . $ticket->ticket_uuid);
            $clubLogo = function_exists('tenant_club_logo') ? tenant_club_logo() : null;
            $qrBase64 = ticket_email_qr_png_base64($scanUrl, $clubLogo);

            if (empty($qrBase64) || !ticket_email_qr_save_file($uuid, $qrBase64)) {
                abort(404);
            }
        }

        return response()->file($path, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

       public function scan($uuid)
    {
        $ticket = EventTicket::where('ticket_uuid', $uuid)->first();
    
        if (!$ticket) {
            return 'Invalid Ticket';
        }
    
        if ($ticket->status == 'used') {
            return 'Ticket Already Used';
        }
    
        if ($ticket->status == 'cancelled') {
            return 'Ticket Cancelled';
        }
    
        $ticket->update([
            'status' => 'used',
            'used_at' => now(),
        ]);
    
        return view('ticket.scan-successs', compact('ticket'));
    }
    
    public function calendar($uuid)
    {
        $ticket = EventTicket::where('ticket_uuid', $uuid)->firstOrFail();
 
        $title = $ticket->event_name ?? 'Event Ticket';
        $description = 'Ticket ID: ' . $ticket->ticket_uuid . '\nAttendee: ' . $ticket->attendee_name;
        $location = $ticket->event_location ?? 'Event Location';
    
        $start = $ticket->event_start_at
            ? \Carbon\Carbon::parse($ticket->event_start_at)->utc()->format('Ymd\THis\Z')
            : now()->utc()->format('Ymd\THis\Z');
    
        $end = $ticket->event_end_at
            ? \Carbon\Carbon::parse($ticket->event_end_at)->utc()->format('Ymd\THis\Z')
            : now()->addHours(2)->utc()->format('Ymd\THis\Z');
    
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Booostr//Ticket Calendar//EN\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:ticket-{$ticket->ticket_uuid}@booostr\r\n";
        $ics .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        $ics .= "SUMMARY:{$title}\r\n";
        $ics .= "DESCRIPTION:{$description}\r\n";
        $ics .= "LOCATION:{$location}\r\n";
        $ics .= "DTSTART:{$start}\r\n";
        $ics .= "DTEND:{$end}\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";
    
        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="ticket-' . $ticket->ticket_uuid . '.ics"',
        ]);
    }
    
    public function appleWallet($uuid)
    {
        $ticket = EventTicket::where('ticket_uuid', $uuid)->firstOrFail();
    
        $pass = new PKPass(
            base_path('storage/app/apple-wallet/pass-certificate.p12'),
            env('APPLE_WALLET_CERT_PASSWORD')
        );
       
       
        $data = [
            'formatVersion' => 1,
            'passTypeIdentifier' => env('APPLE_WALLET_PASS_TYPE_ID'),
            'serialNumber' => $ticket->id . '-' . $ticket->ticket_uuid,
            'teamIdentifier' => env('APPLE_WALLET_TEAM_ID'),
            'organizationName' => 'Booostr',
            'description' => $ticket->event_name ?? 'Event Ticket',
            'logoText' => 'Booostr Ticket',
            'foregroundColor' => 'rgb(0,0,0)',
            'backgroundColor' => 'rgb(255,255,255)',
    
            'eventTicket' => [
                'primaryFields' => [
                    [
                        'key' => 'event',
                        'label' => 'EVENT',
                        'value' => $ticket->event_name ?? 'Event Ticket',
                    ],
                ],
                'secondaryFields' => [
                    [
                        'key' => 'name',
                        'label' => 'NAME',
                        'value' => $ticket->attendee_name ?? 'Guest',
                    ],
                    [
                        'key' => 'date',
                        'label' => 'DATE',
                        'value' => $ticket->event_start_at
                            ? \Carbon\Carbon::parse($ticket->event_start_at)->format('M d, Y')
                            : '',
                    ],
                ],
                'backFields' => [
                    [
                        'key' => 'ticket_id',
                        'label' => 'Ticket ID',
                        'value' => $ticket->ticket_uuid,
                    ],
                ],
            ],
            
            'barcode' => [
                'format' => 'PKBarcodeFormatQR',
                'message' => url('/ticket/scan/' . $ticket->ticket_uuid),
                'messageEncoding' => 'iso-8859-1',
            ],

    
        'barcodes' => [
            [
                'format' => 'PKBarcodeFormatQR',
                'message' => url('/ticket/scan/' . $ticket->ticket_uuid),
                'messageEncoding' => 'iso-8859-1',
            ],
            ],
        ];
       
        $pass->setData($data);
    
        $pass->addFile(base_path('public/wallet/icon.png'));
        $pass->addFile(base_path('public/wallet/logo.png'));
    
        $pkpass = $pass->create(false);
        if (!$pkpass) {
            dd($pass->getErrors());
        }
    
        return response($pkpass, 200)
            ->header('Content-Type', 'application/vnd.apple.pkpass')
            ->header('Content-Disposition', 'inline; filename="ticket.pkpass"')
            ->header('Content-Transfer-Encoding', 'binary')
            ->header('Content-Length', strlen($pkpass))
            ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
            ->header('Pragma', 'public');
    }

    // public function appleWallet($uuid)
    // {
    //     $path = base_path(
    //         'storage/app/apple-wallet/Pass-Example-Generic.pkpass'
    //     );
    
    //     return response()->download($path, 'ticket-test.pkpass', [
    //         'Content-Type' => 'application/vnd.apple.pkpass',
    //         'Content-Disposition' => 'attachment; filename="ticket-test.pkpass"',
    //     ]);
    // }
    public function googleWallet($uuid)
    {
        $ticket = EventTicket::where('ticket_uuid', $uuid)->firstOrFail();

        $issuerId = env('GOOGLE_WALLET_ISSUER_ID');
        $classId = env('GOOGLE_WALLET_CLASS_ID');
        $objectId = $issuerId . '.ticket_' . str_replace('-', '_', $ticket->ticket_uuid);

        $serviceAccount = json_decode(
            file_get_contents(base_path('storage/app/google-wallet/service-account.json')),
            true
        );

        $eventTicketObject = [
            'id' => $objectId,
            'classId' => $classId,
            'state' => 'ACTIVE',
            'ticketHolderName' => $ticket->attendee_name ?: 'Guest',
            'ticketNumber' => $ticket->ticket_uuid,
            'barcode' => [
                'type' => 'QR_CODE',
                'value' => $ticket->ticket_uuid,
                'alternateText' => substr($ticket->ticket_uuid, 0, 8),
            ],
        ];

        $eventLabel = $ticket->event_name ?: 'Event Ticket';
        $eventTicketObject['ticketType'] = [
            'defaultValue' => [
                'language' => 'en-US',
                'value' => $eventLabel,
            ],
        ];
        $eventTicketObject['textModulesData'] = [
            [
                'id' => 'event_name',
                'header' => 'Event',
                'body' => $eventLabel,
            ],
        ];

        if (!empty($ticket->event_start_at)) {
            try {
                $startIso = \Carbon\Carbon::parse($ticket->event_start_at)->toIso8601String();
                $interval = ['start' => ['date' => $startIso]];

                if (!empty($ticket->event_end_at)) {
                    $endIso = \Carbon\Carbon::parse($ticket->event_end_at)->toIso8601String();
                    $interval['end'] = ['date' => $endIso];
                }

                $eventTicketObject['validTimeInterval'] = $interval;
            } catch (\Throwable $e) {
                // Skip validTimeInterval if dates are malformed
            }
        }

        if (!$this->ensureGoogleWalletEventTicketObject($eventTicketObject)) {
            abort(500, 'Unable to create Google Wallet pass. Please try again later.');
        }

        $payload = [
            'iss' => $serviceAccount['client_email'],
            'aud' => 'google',
            'typ' => 'savetowallet',
            'iat' => time(),
            'payload' => [
                'eventTicketObjects' => [$eventTicketObject],
            ],
        ];

        $jwt = JWT::encode($payload, $serviceAccount['private_key'], 'RS256');

        return redirect('https://pay.google.com/gp/v/save/' . $jwt);
    }

    /**
     * GET first → if object exists skip insert/patch.
     * If 404 → INSERT (POST).
     * If insert returns 409 → PATCH with rawurlencoded id.
     * Returns true on success, false on hard failure.
     */
    private function ensureGoogleWalletEventTicketObject(array $object): bool
    {
        $objectId = $object['id'];

        try {
            $client = new \Google\Client();
            $client->setAuthConfig(base_path('storage/app/google-wallet/service-account.json'));
            $client->addScope('https://www.googleapis.com/auth/wallet_object.issuer');
            $httpClient = $client->authorize();

            $baseUrl = 'https://walletobjects.googleapis.com/walletobjects/v1/eventTicketObject';
            $resourceUrl = $baseUrl . '/' . rawurlencode($objectId);

            // 1. GET — does the object already exist? Avoid repeated insert/patch loops.
            try {
                $getResponse = $httpClient->get($resourceUrl);
                $getStatus = $getResponse->getStatusCode();

                if ($getStatus >= 200 && $getStatus < 300) {
                    Log::info('Google Wallet object GET hit (exists, skipping insert/patch)', [
                        'objectId' => $objectId,
                        'status' => $getStatus,
                    ]);
                    return true;
                }

                Log::info('Google Wallet object GET non-2xx', [
                    'objectId' => $objectId,
                    'status' => $getStatus,
                ]);
            } catch (\GuzzleHttp\Exception\ClientException $getError) {
                $getStatus = $getError->getResponse() ? $getError->getResponse()->getStatusCode() : 0;
                $getBody = $getError->getResponse() ? (string) $getError->getResponse()->getBody() : '';

                if ($getStatus !== 404) {
                    Log::error('Google Wallet object GET failed (non-404)', [
                        'objectId' => $objectId,
                        'url' => $resourceUrl,
                        'status' => $getStatus,
                        'body' => $getBody,
                    ]);
                    // Continue to INSERT attempt anyway; INSERT failure will be logged.
                } else {
                    Log::info('Google Wallet object GET 404 (will INSERT)', [
                        'objectId' => $objectId,
                    ]);
                }
            }

            // 2. INSERT (POST)
            try {
                $insertResponse = $httpClient->post($baseUrl, ['json' => $object]);
                Log::info('Google Wallet object INSERT success', [
                    'objectId' => $objectId,
                    'status' => $insertResponse->getStatusCode(),
                ]);
                return true;
            } catch (\GuzzleHttp\Exception\ClientException $insertError) {
                $insertStatus = $insertError->getResponse() ? $insertError->getResponse()->getStatusCode() : 0;
                $insertBody = $insertError->getResponse() ? (string) $insertError->getResponse()->getBody() : '';

                if ($insertStatus === 409) {
                    Log::info('Google Wallet object INSERT 409 conflict (will PATCH)', [
                        'objectId' => $objectId,
                    ]);

                    // 3. PATCH — object already exists; update with rawurlencoded URL.
                    try {
                        $patchResponse = $httpClient->patch($resourceUrl, ['json' => $object]);
                        Log::info('Google Wallet object PATCH success', [
                            'objectId' => $objectId,
                            'status' => $patchResponse->getStatusCode(),
                        ]);
                        return true;
                    } catch (\GuzzleHttp\Exception\ClientException $patchClientError) {
                        Log::error('Google Wallet object PATCH failed', [
                            'objectId' => $objectId,
                            'url' => $resourceUrl,
                            'status' => $patchClientError->getResponse()
                                ? $patchClientError->getResponse()->getStatusCode()
                                : 0,
                            'body' => $patchClientError->getResponse()
                                ? (string) $patchClientError->getResponse()->getBody()
                                : '',
                        ]);
                        return false;
                    } catch (\Throwable $patchError) {
                        Log::error('Google Wallet object PATCH exception', [
                            'objectId' => $objectId,
                            'url' => $resourceUrl,
                            'message' => $patchError->getMessage(),
                        ]);
                        return false;
                    }
                }

                Log::error('Google Wallet object INSERT failed', [
                    'objectId' => $objectId,
                    'url' => $baseUrl,
                    'status' => $insertStatus,
                    'body' => $insertBody,
                ]);
                return false;
            }
        } catch (\Throwable $e) {
            Log::error('Google Wallet object upsert exception', [
                'objectId' => $objectId,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function createGoogleWalletClass()
{
    $classId = env('GOOGLE_WALLET_CLASS_ID');

    $client = new \Google\Client();
    $client->setAuthConfig(base_path('storage/app/google-wallet/service-account.json'));
    $client->addScope('https://www.googleapis.com/auth/wallet_object.issuer');

    $httpClient = $client->authorize();

    $response = $httpClient->post(
        'https://walletobjects.googleapis.com/walletobjects/v1/eventTicketClass',
        [
            'json' => [
                'id' => $classId,
                'issuerName' => 'Booostr',
                'reviewStatus' => 'UNDER_REVIEW',
                'eventName' => [
                    'defaultValue' => [
                        'language' => 'en-US',
                        'value' => 'Booostr Event Ticket'
                    ]
                ],
                'hexBackgroundColor' => '#000000'
            ]
        ]
    );

    return json_decode($response->getBody(), true);
}
    public function print($uuid)
    {
        $ticket = EventTicket::where('ticket_uuid', $uuid)->firstOrFail();
    
        $tickets = EventTicket::where('order_id', $ticket->order_id)->get();
    
        return view('ticket.print', compact('tickets'));
    }

}
