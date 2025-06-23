<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\SMS;

use App\Models\Base\BaseModel;
use App\Models\System\SiteSetting;

class SmsHistory extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = "SMS History";

    function sms_template()
    {
        return $this->belongsTo(SmsTemplate::class)->select('id', 'name');
    }

    /**
     * SMS history details
     *
     * @return \Illuminate\Http\Response
     */
    public static function store($sms_type, $contacts, $balance, $total_contact)
    {
        $contacts   = explode(',', $contacts);
        $template   = SmsTemplate::where('sms_type', $sms_type)->first();
        $siteSetting = SiteSetting::first();

        $data = [
            'sms_template_id'  => $template->id,
            'sms_cost'  =>  $siteSetting->sms_cost,
            'date'      => date('Y-m-d'),
        ];

        // exists check
        $smsHistory = SmsHistory::whereDate('date', $data['date'])->where('sms_template_id', $template->id)->first();
        if (empty($smsHistory)) {
            $smsHistory = SmsHistory::create($data);
        }

        $totalSendSms   = $smsHistory->total_sending_sms + $total_contact;
        $smsHistory->update(['total_sending_sms' => $totalSendSms]);

        $siteSetting->update(['sms_balance' => $balance]);
    }
}
