<?php

/**
 * Dev: @OSHIT SUTRA DAR
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $layout;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $layout)
    {
        $this->data     = $data;
        $this->layout   = $layout;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from([
            'address' => 'oshitsd99@gmail.com',
            'name' => config('app.name')
        ])->subject($this->data['subject'])
            ->markdown('emails.' . $this->layout)
            ->with('data', $this->data);
    }
}


/**
 * use your controller.
 * 
 * @return $this
 */

/*
    use App\Mail\SendMail;
    use Illuminate\Support\Facades\Mail;
    public function contactMail(Request $request)
    {
        $layout = "contact";
        $email  = [
            'name'      => "OSHIT SUTRA DAR",
            'email'     => "oshit@nogorsolutionos.com",
            'mobile'    => "01883847733",
            'subject'   => "Feedback From Website",
            'message'   => "Mail send successfully",
        ];

        try {
            Mail::to('oshit@nogorsolutions.com')->send(new SendMail($email, $layout));
            return response()->json("Mail send successfully");
        } catch (\Exception $e) {
            return 'Error - ' . $e;
        }
    }
*/
