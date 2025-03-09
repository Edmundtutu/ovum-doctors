<?php

namespace App\Policies;

use App\Models\CyleHistory;
use App\Models\Doctor;
use Illuminate\Auth\Access\Response;

class CyleHistoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Doctor $doctor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Doctor $doctor, CyleHistory $cyleHistory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Doctor $doctor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Doctor $doctor, CyleHistory $cyleHistory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Doctor $doctor, CyleHistory $cyleHistory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Doctor $doctor, CyleHistory $cyleHistory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Doctor $doctor, CyleHistory $cyleHistory): bool
    {
        return false;
    }
}
