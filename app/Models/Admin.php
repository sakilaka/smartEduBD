<?php

namespace App\Models;

use Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use App\Models\System\Role;
use App\Models\MasterSetup\Institution;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard  = 'admin';

    protected $fillable = [
        'institution_id',
        'role_id',
        'name',
        'father_name',
        'email',
        'password',
        'role_id',
        'profile',
        'mobile',
        'address',
        'status',
        'type'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'admin_id');
    }

    function subject_assigns()
    {
        return $this->hasMany(TeacherSubjectAssign::class, 'admin_id');
    }

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
    public function institution()
    {
        return $this->belongsTo(Institution::class)->select('id', 'name', 'logo');
    }

    public function getProfileAttribute($value)
    {
        $asset = config('app.do_asset_url');
        return !empty($value) ? "{$asset}/{$value}" : null;
    }
    public function setPasswordAttribute($val)
    {
        $this->attributes['password'] = Hash::make($val);
    }
}
