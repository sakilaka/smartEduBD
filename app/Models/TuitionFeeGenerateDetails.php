<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\ParentModel;

class TuitionFeeGenerateDetails extends ParentModel
{
    protected $guarded = ['id'];
    protected $appends = ['month'];

    protected static $logName = "Tuition Fee Generate Details";

    public function invoice_details()
    {
        return $this->hasOne(InvoiceDetails::class, 'tuition_fee_generate_details_id');
    }

    public function getMonthAttribute()
    {
        return date('m', strtotime($this->date));
    }
}
