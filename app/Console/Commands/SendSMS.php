<?php

namespace App\Console\Commands;

use App\Jobs\SendSMSJob;
use App\Models\Student;
use Illuminate\Console\Command;

class SendSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $students = Student::select('guardian_id', 'id', 'software_id')->where('institution_id', 1)->get();

        foreach ($students ?? [] as $student) {
            $mobile = $student->guardian->mobile ?? '';
            $software_id = $student->software_id ?? '';
            if (!empty($mobile)) {
                SendSMSJob::dispatch($mobile, $software_id);
            }
        }

        $this->info('Added in queue successfully');
    }
}
