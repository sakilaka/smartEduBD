<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Guardian extends Authenticatable
{
    use SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'current_student_id',
        'name_en',
        'name_bn',
        'nid_or_birth_reg',
        'mobile',
        'email',
        'type',
        'relations',
        'password',
        'otp',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'otp', 'created_at', 'updated_at', 'deleted_at'];

    public function setPasswordAttribute($val)
    {
        if (!empty($val)) {
            $this->attributes['password'] = Hash::make($val);
        }
    }

    /**
     * Relations with others table
     *
     * @var array
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'guardian_id');
    }
    public function current_student()
    {
        return $this->belongsTo(Student::class, 'current_student_id');
    }
    public static function loggedProfile()
    {
        $student = auth()->user()->current_student()
            ->with('profile:student_id,profile', 'academic_class', 'institution')
            ->select('id', 'academic_class_id', 'institution_id', 'software_id', 'name_en')
            ->first();

        return [
            'user' => auth()->user(),
            'student' => $student
        ];
    }
}
