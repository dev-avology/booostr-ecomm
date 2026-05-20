<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventTicket;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use PKPass\PKPass;


class TicketScanController extends Controller
{
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
            'serialNumber' => $ticket->ticket_uuid,
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
    
            'barcodes' => [
                [
                    'format' => 'PKBarcodeFormatQR',
                    'message' => url('/ticket/scan/' . $ticket->ticket_uuid),
                    'messageEncoding' => 'iso-8859-1',
                ],
            ],
        ];
       
        $pass->setData($data);
    
        $pass->addFile(public_path('wallet/icon.png'));
        $pass->addFile(public_path('wallet/logo.png'));
    
        $pkpass = $pass->create(false);
    
        return response($pkpass, 200, [
            'Content-Type' => 'application/vnd.apple.pkpass',
            'Content-Disposition' => 'attachment; filename="ticket-' . $ticket->ticket_uuid . '.pkpass"',
        ]);
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
    
        $payload = [
            'iss' => $serviceAccount['client_email'],
            'aud' => 'google',
            'typ' => 'savetowallet',
            'iat' => time(),
        
            'payload' => [
                'eventTicketObjects' => [
                    [
                        'id' => $objectId,
                        'classId' => $classId,
                        'state' => 'ACTIVE',
        
                        'ticketHolderName' => $ticket->attendee_name,
        
                        'ticketNumber' => $ticket->ticket_uuid,
        
                        'eventName' => [
                            'defaultValue' => [
                                'language' => 'en-US',
                                'value' => $ticket->event_name,
                            ]
                        ],
        
                        'barcode' => [
                            'type' => 'QR_CODE',
                            'value' => $ticket->ticket_uuid,
                        ],
        
                        'validTimeInterval' => [
                            'start' => [
                                'date' => \Carbon\Carbon::parse($ticket->event_start_at)->toIso8601String(),
                            ],
                            'end' => [
                                'date' => \Carbon\Carbon::parse($ticket->event_end_at)->toIso8601String(),
                            ],
                        ],
                    ]
                ]
            ]
        ];
    
        $jwt = JWT::encode($payload, $serviceAccount['private_key'], 'RS256');
    
        return redirect('https://pay.google.com/gp/v/save/' . $jwt);
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
