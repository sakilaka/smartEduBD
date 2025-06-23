<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\Base;

use App\Helpers\GlobalHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseModel extends Model
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
