<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('users.view.any');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('users.view.any') || 
               ($user->can('users.view.own') && $user->id === $model->id);
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('users.update.any') || 
               ($user->can('users.update.own') && $user->id === $model->id);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('users.delete.any') || 
               ($user->can('users.delete.own') && $user->id === $model->id);
    }
} 