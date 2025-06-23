<?php

namespace App\Policies;

use App\Models\AcademicSession;
use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcademicSessionPolicy
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
     * @param  \App\Models\AcademicSession  $academicSession
     * @return mixed
     */
    public function view(Admin $admin, AcademicSession $academicSession)
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
     * @param  \App\Models\AcademicSession  $academicSession
     * @return mixed
     */
    public function update(Admin $admin, AcademicSession $academicSession)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\AcademicSession  $academicSession
     * @return mixed
     */
    public function delete(Admin $admin, AcademicSession $academicSession)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\AcademicSession  $academicSession
     * @return mixed
     */
    public function restore(Admin $admin, AcademicSession $academicSession)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\AcademicSession  $academicSession
     * @return mixed
     */
    public function forceDelete(Admin $admin, AcademicSession $academicSession)
    {
        //
    }
}
