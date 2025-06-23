<?php

/**
 * Dev: @OSHIT SUTRA DAR
 */

namespace App\Traits\Lib;

trait CustomDataTrait
{
    protected $genders = [
        'Male',
        'Female',
        'Others',
    ];
    protected $religions = [
        'Islam',
        'Hinduism',
        'Christian',
        'Buddhist',
        'Others',
    ];
    protected $blood_groups = [
        'A-',
        'A+',
        'B-',
        'B+',
        'O-',
        'O+',
        'AB-',
        'AB+',
    ];
    public $notice_types = [
        "public"  => "Public Notice",
        // "office"  => "Office Notice",
        "student" => "Student Notice",
    ];
    private $status = [
        'draft'    => 'Draft',
        'active'   => 'Active',
        'deactive' => 'Deactive',
    ];
    private $gateway = [
        'SSL'   => 'SslCommerz',
        'SPAY'  => 'ShurjoPay',
    ];
    private $payment_durations = [
        'Yearly'  => 'Yearly',
        'Monthly' => 'Monthly',
        'Anytime' => 'Any Time',
    ];
    private $months = [
        ['key' => 1, 'name' => 'January'],
        ['key' => 2, 'name' => 'February'],
        ['key' => 3, 'name' => 'March'],
        ['key' => 4, 'name' => 'April'],
        ['key' => 5, 'name' => 'May'],
        ['key' => 6, 'name' => 'June'],
        ['key' => 7, 'name' => 'July'],
        ['key' => 8, 'name' => 'August'],
        ['key' => 9, 'name' => 'September'],
        ['key' => 10, 'name' => 'October'],
        ['key' => 11, 'name' => 'November'],
        ['key' => 12, 'name' => 'December'],
    ];
    private $sms_types = [
        "OTPInfo"           => "Update Information OTP",
        "OTP"               => "Forget OTP",
        "OnlineAdmission"   => "Online Admission",
        "StudentCreate"     => "Student Create",
        "Absent"            => "Absent",
        "Migration"         => "Migration",
        "FailedPayment"     => "Failed Payment",
        "TeacherCreate"     => "Teacher Create",
        "Others"            => "Others"
    ];
    private $sms_keywords = [
        "[_Student_Name_]",
        "[_College_Roll_]",
        "[_Mobile_]",
        "[_Email_]",
        "[_Password_]",
        "[_Date_]",
        "[_Invoice_ID_]",
        "[_OTP_]"
    ];

    protected function customData()
    {
        return [
            'religions'         => $this->religions,
            'notice_types'      => $this->notice_types,
            'status'            => $this->status,
            'gateway'           => $this->gateway,
            'payment_durations' => $this->payment_durations,
            'sms_types'         => $this->sms_types,
            'sms_keywords'      => $this->sms_keywords,
        ];
    }

    protected function commonCustomData()
    {
        return [
            'genders'           => $this->genders,
            'religions'         => $this->religions,
            'blood_groups'      => $this->blood_groups,
            'months'            => $this->months,
        ];
    }
}
