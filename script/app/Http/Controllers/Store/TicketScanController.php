<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventTicket;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use PKPass\PKPass;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;


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

        $issuerId = $this->googleWalletIssuerId();
        $classId = $this->googleWalletClassId($issuerId);
        $objectId = $this->googleWalletObjectId($issuerId, $ticket->ticket_uuid);
        $walletObject = $this->buildGoogleWalletEventTicketObject($ticket, $classId, $objectId);

        $upserted = $this->upsertGoogleWalletEventTicketObject($walletObject);

        $serviceAccount = json_decode(
            file_get_contents(base_path('storage/app/google-wallet/service-account.json')),
            true
        );

        $origins = array_values(array_filter(array_unique([
            request()->getSchemeAndHttpHost(),
            rtrim((string) config('app.url'), '/'),
        ])));

        $jwtObjects = $upserted
            ? [['id' => $objectId, 'classId' => $classId]]
            : [$walletObject];

        $payload = [
            'iss' => $serviceAccount['client_email'],
            'aud' => 'google',
            'typ' => 'savetowallet',
            'iat' => time(),
            'origins' => $origins,
            'payload' => [
                'eventTicketObjects' => $jwtObjects,
            ],
        ];

        $jwt = JWT::encode($payload, $serviceAccount['private_key'], 'RS256');

        return redirect('https://pay.google.com/gp/v/save/' . $jwt);
    }

    private function googleWalletIssuerId(): string
    {
        return trim((string) env('GOOGLE_WALLET_ISSUER_ID', ''));
    }

    private function googleWalletClassId(string $issuerId): string
    {
        $classId = trim((string) env('GOOGLE_WALLET_CLASS_ID', ''));

        if ($classId === '' || $issuerId === '') {
            abort(500, 'Google Wallet issuer/class is not configured.');
        }

        if (strpos($classId, '.') === false) {
            return $issuerId . '.' . $classId;
        }

        return $classId;
    }

    private function googleWalletObjectId(string $issuerId, string $ticketUuid): string
    {
        $suffix = 'ticket_' . strtolower(str_replace('-', '_', $ticketUuid));

        return $issuerId . '.' . $suffix;
    }

    private function googleWalletValidTimeInterval(EventTicket $ticket): ?array
    {
        if (empty($ticket->event_start_at)) {
            return null;
        }

        try {
            $startAt = \Carbon\Carbon::parse($ticket->event_start_at)->utc();
        } catch (\Throwable $e) {
            return null;
        }

        $interval = [
            'start' => [
                'date' => $startAt->format('Y-m-d\TH:i:s\Z'),
            ],
        ];

        if (!empty($ticket->event_end_at)) {
            try {
                $endAt = \Carbon\Carbon::parse($ticket->event_end_at)->utc();
                if ($endAt->greaterThan($startAt)) {
                    $interval['end'] = [
                        'date' => $endAt->format('Y-m-d\TH:i:s\Z'),
                    ];
                }
            } catch (\Throwable $e) {
                // Omit end when invalid; start-only interval is valid.
            }
        }

        return $interval;
    }

    private function buildGoogleWalletEventTicketObject(EventTicket $ticket, string $classId, string $objectId): array
    {
        $object = [
            'id' => $objectId,
            'classId' => $classId,
            'state' => 'ACTIVE',
            'ticketHolderName' => trim((string) ($ticket->attendee_name ?: 'Guest')),
            'ticketNumber' => (string) $ticket->ticket_uuid,
            'barcode' => [
                'type' => 'QR_CODE',
                'value' => url('/ticket/scan/' . $ticket->ticket_uuid),
                'alternateText' => substr($ticket->ticket_uuid, 0, 8),
            ],
        ];

        $validTimeInterval = $this->googleWalletValidTimeInterval($ticket);
        if ($validTimeInterval !== null) {
            $object['validTimeInterval'] = $validTimeInterval;
        }

        return $object;
    }

    private function googleWalletHttpClient()
    {
        $client = new \Google\Client();
        $client->setAuthConfig(base_path('storage/app/google-wallet/service-account.json'));
        $client->addScope('https://www.googleapis.com/auth/wallet_object.issuer');

        return $client->authorize();
    }

    private function upsertGoogleWalletEventTicketObject(array $object): bool
    {
        $httpClient = $this->googleWalletHttpClient();
        $insertUrl = 'https://walletobjects.googleapis.com/walletobjects/v1/eventTicketObject';
        $resourceUrl = $insertUrl . '/' . rawurlencode($object['id']);

        try {
            $httpClient->post($insertUrl, ['json' => $object]);

            return true;
        } catch (ClientException $e) {
            $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $body = $e->getResponse() ? (string) $e->getResponse()->getBody() : '';

            if ($status === 409) {
                try {
                    $httpClient->put($resourceUrl, ['json' => $object]);

                    return true;
                } catch (\Throwable $updateError) {
                    Log::warning('Google Wallet object update failed after conflict', [
                        'objectId' => $object['id'],
                        'message' => $updateError->getMessage(),
                    ]);
                }
            } else {
                Log::warning('Google Wallet object insert failed', [
                    'objectId' => $object['id'],
                    'status' => $status,
                    'body' => $body,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Google Wallet object upsert failed', [
                'objectId' => $object['id'],
                'message' => $e->getMessage(),
            ]);
        }

        return false;
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
