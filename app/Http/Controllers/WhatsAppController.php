<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Session;
use WPPConnectTeam\Wppconnect\Facades\Wppconnect;

class WhatsAppController extends Controller
{
    public function runWhatsAppBot(Request $request)
    {
        $recipient = '085156796081';
        $message = 'hari hari bisa';

        $accessToken = 'EAALZBM3Ph2YMBO5eD9JZCJv3pZBSY459onDZCPuZAL5CkEZAj1GPZB2rZAyu91aN08x5ivAfSPR0JO45aRpMSY9ZCZCFrWP9e5j9HyBDZAjzgZCzspwwwoMsaw78EQrQoRMSQgdzaXaCK6lOTyyLn1y5tQP9CmiuBkRNvLJYEwHFe38KZC9sZB8KovhQaOx43rWdSiz5amEm3d2WPdnQZDZD';
        $phoneNumberId = '3663455247238880';

        $client = new Client();

        try {
            $response = $client->get('https://graph.facebook.com/v19.0/' . $phoneNumberId . '/messages', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $recipient,
                    'type' => 'text',
                    'text' => [
                        'body' => $message,
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode == 200) {
                return 'Pesan berhasil dikirim!';
            } else {
                return 'Terjadi kesalahan: ' . $response->getBody();
            }
        } catch (\Exception $e) {
            return 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}
