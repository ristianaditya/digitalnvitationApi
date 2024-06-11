<?php

namespace App\Services;

use WPPConnectTeam\Wppconnect\Facades\Wppconnect;

class WhatsAppService
{
    public function sendMessage($phoneNumber, $message)
    {
        try {
            // Kirim pesan menggunakan WPPConnect
            $result = Wppconnect::sendMessage($phoneNumber, $message);

            // Proses hasilnya, jika perlu
            return $result;
        } catch (\Exception $e) {
            // Tangani kesalahan jika terjadi
            return $e->getMessage();
        }
    }
}
