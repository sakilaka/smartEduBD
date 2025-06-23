<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use App\Models\Base\BaseModel;
use App\Models\MasterSetup\AccountHead;

class FeeSetupDetails extends BaseModel
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $appends = ['fine_amount'];

    protected static $logName = "Fee Setup Details";

    // Fine Amount
    public function getFineAmountAttribute()
    {
        $config = App::make('siteSettingObj');
        $date   = date('Y-m-d');

        if ($date > $this->expire_date) {
            return $config['fine_amount'] ?? 0;
        }
        return 0;
    }

    /**
     * Relations
     *
     * @var array
     */
    public function fees()
    {
        return $this->hasOne(FeeSetup::class, 'id', 'fee_setup_id');
    }
    public function account_head()
    {
        return $this->belongsTo(AccountHead::class)->select('id', 'name_en', 'type');
    }
    public function depend_head()
    {
        return $this->belongsTo(AccountHead::class, 'depend_head_id')->select('id', 'name_en');
    }
    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }

    public function setStartDateAttribute($date)
    {
        $this->attributes['start_date'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }
    public function setExpireDateAttribute($date)
    {
        $this->attributes['expire_date'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }
    public function setAdditionalDateAttribute($date)
    {
        $this->attributes['additional_date'] = !empty($date) ? date('Y-m-d', strtotime($date)) : null;
    }
}
