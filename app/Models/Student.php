<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use App\Helpers\GlobalHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\MasterSetup\Shift;
use App\Models\MasterSetup\Campus;
use App\Models\MasterSetup\Group;
use App\Models\MasterSetup\AcademicClass;
use App\Models\MasterSetup\AcademicSession;
use App\Models\MasterSetup\Section;
use Laravel\Sanctum\HasApiTokens;
use App\Models\MasterSetup\Institution;
use App\Models\MasterSetup\Medium;
use Illuminate\Support\Facades\Auth;

class Student extends Authenticatable
{
    use SoftDeletes, HasApiTokens, LogsActivity;

    protected static $logName = "Student";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'institution_id',
        'campus_id',
        'shift_id',
        'medium_id',
        'academic_class_id',
        'academic_session_id',
        'group_id',
        'section_id',
        'guardian_id',
        'software_id',
        'name_en',
        'name_bn',
        'mobile',
        'email',
        'password',
        'otp',
        'status',
        'created_from'
    ];

    protected $appends = ['checked'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'otp', 'deleted_at'];

    public function getCheckedAttribute()
    {
        return false;
    }
    public function setPasswordAttribute($val)
    {
        if (!empty($val)) {
            $this->attributes['password'] = Hash::make($val);
        }
    }

    /**
     * Relations with others table
     */
    public function profile()
    {
        return $this->hasOne(StudentProfile::class, 'student_id');
    }



    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }
    public function institution()
    {
        return $this->belongsTo(Institution::class)->select(
            'id',
            'name',
            'short_name',
            'status',
            'admin_admit_card',
            'seat_card'
        );
    }
    public function getAdminAdmitCardAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }
    public function academic_session()
    {
        return $this->belongsTo(AcademicSession::class)->select('id', 'name', 'institution_category_id');
    }
    public function campus()
    {
        return $this->belongsTo(Campus::class)->select('id', 'name');
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class)->select('id', 'name');
    }
    public function medium()
    {
        return $this->belongsTo(Medium::class)->select('id', 'name');
    }
    public function academic_class()
    {
        return $this->belongsTo(AcademicClass::class)->select('id', 'name', 'institution_category_id');
    }
    public function group()
    {
        return $this->belongsTo(Group::class)->select('id', 'name');
    }
    public function section()
    {
        return $this->belongsTo(Section::class)->select('id', 'name');
    }
    public function promoted()
    {
        return $this->hasMany(StudentPromotion::class, 'student_id')->latest('id');
    }
    public function discounts()
    {
        return $this->hasMany(StudentDiscount::class, 'student_id')
            ->select('student_id', 'account_head_id', 'academic_session_id', 'academic_class_id', 'amount')
            ->whereColumn('institution_id', 'institution_id')
            ->whereColumn('academic_session_id', 'academic_session_id')
            ->whereColumn('academic_class_id', 'academic_class_id');
    }

    /**
     * where conditions
     *
     * @var array
     */
    public static function commonArr($req)
    {
        return [
            'institution_id'    => $req->institution_id ?? '',
            'academic_session_id' => $req->academic_session_id ?? '',
            'campus_id'         => $req->campus_id ?? '',
            'shift_id'          => $req->shift_id ?? '',
            'medium_id'         => $req->medium_id ?? '',
            'academic_class_id' => $req->academic_class_id ?? '',
            'group_id'          => $req->group_id ?? '',
            'section_id'        => $req->section_id ?? '',
        ];
    }
    public static function conditions($req)
    {
        return [
            'shift_id'          => $req->shift_id ?? '',
            'group_id'          => $req->group_id ?? '',
            'medium_id'         => $req->medium_id ?? '',
            'institution_id'    => $req->institution_id ?? '',
            'academic_class_id' => $req->academic_class_id ?? '',
        ];
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
        $sid   = $this->software_id ?? "";

        return "{$name} - {$eventName} this (ID - {$id}) - Software ID -({$sid})";
    }
}
