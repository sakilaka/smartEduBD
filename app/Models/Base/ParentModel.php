<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Request;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Helpers\GlobalHelper;
use Illuminate\Support\Facades\Auth;

class ParentModel extends Model
{
    use LogsActivity;

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    //--------------------------------------------------------
    // USER LOG INFO
    //--------------------------------------------------------
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($data) {
            $data->created_by = Auth::user()->name ?? '';
            $data->created_ip = Request::ip();
        });
        static::updating(function ($data) {
            $data->updated_by = Auth::user()->name ?? '';
            $data->updated_ip = Request::ip();
        });
    }


    //--------------------------------------------------------
    // ACTIVITY LOG INFO
    //--------------------------------------------------------
    protected static $logOnlyDirty  = true;
    protected static $logAttributes = ['*'];
    public function getDescriptionForEvent(string $eventName): string
    {
        $guard = GlobalHelper::get_guard();
        $name  = Auth::guard($guard)->user()->name ?? "";
        $id    = $this->id ?? "";
        return "{$name} - {$eventName} this (ID - {$id})";
    }
}
