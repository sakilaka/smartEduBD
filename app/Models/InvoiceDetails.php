<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Models\Base\BaseModel;
use App\Models\MasterSetup\AccountHead;

class InvoiceDetails extends BaseModel
{
    protected $guarded = ['id'];

    protected static $logName = "Invoice Details";

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    public function head()
    {
        return $this->belongsTo(AccountHead::class, 'account_head_id')->select('id', 'name_bn', 'name_en');
    }
}
