<?php

namespace App\Jobs;

use App\Traits\SMSTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SMSTrait;

    protected $mobile = "";
    protected $software_id = "";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mobile, $software_id)
    {
        $this->mobile = $mobile;
        $this->software_id = $software_id;

        $this->onConnection('database');
        $this->onQueue('send-sms');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $message =  "Dear Guardian,\nPlease pay Police Lines School & College Tuition Fees of January,February month.\nApp Download: https://shorturl.at/guzCM\nWebsite: https://guardian.smartedubd.net\nfor online payment.\n\nStudent ID:{$this->software_id}\nUser ID:{$this->mobile}\nPass: abc123\nYou can pay Dhaka Bank Branch.\n\nHotline: 01710409659 (PLSCF).";
        $message =  "Dear Guardian,\nPlease pay Police Lines School & College Tuition Fees of January,February and March within 21-03-24.\n\nApp download link: https://shorturl.at/guzCM\n\nStudent ID: {$this->software_id}\nUser ID: {$this->mobile}\nPass: abc123\n\nYou can pay Dhaka Bank Branch also.\n\nHotline: 01710409659 (PLSCF).";
        $this->sendSms($this->mobile, $message);
    }
}
