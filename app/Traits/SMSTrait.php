<?php

/**
 * Dev: @OSHIT SUTRA DAR
 */

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait SMSTrait
{
    protected function sendSms($contacts = null, $message = null)
    {
        // message config
        $baseurl    = "https://sms.mram.com.bd/smsapi";
        $senderid   = "8809601013371";
        $api_key    = "R70000366523c4b607c679.06551844";

        $res  = Http::post($baseurl, [
            "api_key"   => "{$api_key}",
            "type"      => "text",
            "contacts"  => "{$contacts}",
            "senderid"  => "{$senderid}",
            "msg"       => "{$message}",
        ]);

        return $res->successful();
    }
}
