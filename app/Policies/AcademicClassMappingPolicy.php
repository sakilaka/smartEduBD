<?php

namespace App\Policies;

use App\Models\AcademicClassMapping;
use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcademicClassMappingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Admin  $admin
     * @return mixed
     */
    public function viewAny(Admin $admin)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\AcademicClassMapping  $academicClassMapping
     * @return mixed
     */
    public function view(Admin $admin, AcademicClassMapping $academicClassMapping)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Admin  $admin
     * @return mixed
     */
    public function create(Admin $admin)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\AcademicClassMapping  $academicClassMapping
     * @return mixed
     */
    public function update(Admin $admin, AcademicClassMapping $academicClassMapping)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\AcademicClassMapping  $academicClassMapping
     * @return mixed
     */
    public function delete(Admin $admin, AcademicClassMapping $academicClassMapping)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\AcademicClassMapping  $academicClassMapping
     * @return mixed
     */
    public function restore(Admin $admin, AcademicClassMapping $academicClassMapping)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\AcademicClassMapping  $academicClassMapping
     * @return mixed
     */
    public function forceDelete(Admin $admin, AcademicClassMapping $academicClassMapping)
    {
        //
    }
}
