<?php

/**
 * @OSHIT SUTRA DHAR
 */

namespace App\Models\MasterSetup;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstitutionCategory extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
}
