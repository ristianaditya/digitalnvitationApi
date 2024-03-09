<?php

namespace App\Services\Midtrans;

use Midtrans\Snap;

class CreateSnapTokenService extends Midtrans
{

    public function __construct()
    {
        parent::__construct();

    }

    public function getSnapToken($request)
    {
        $snapToken = Snap::getSnapToken($request);

        return $snapToken;
    }
}
