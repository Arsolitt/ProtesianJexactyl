<?php

namespace Jexactyl\Observers;

use Jexactyl\Events;
use Jexactyl\Models\User;

class UserObserver
{
    protected string $uuid;

    /**
     * Listen to the User creating event.
     */
    public function creating(User $user): void
    {
        event(new Events\User\Creating($user));
    }

    /**
     * Listen to the User created event.
     */
    public function created(User $user): void
    {
        event(new Events\User\Created($user));
    }

    /**
     * Listen to the User deleting event.
     */
    public function deleting(User $user): void
    {
        event(new Events\User\Deleting($user));
    }

    /**
     * Listen to the User deleted event.
     */
    public function deleted(User $user): void
    {
        event(new Events\User\Deleted($user));
    }

    public function updating(User $user): void
    {
        event(new Events\User\Updating($user));
    }

    /**
     * Listen to the Server saved event.
     */
    public function updated(User $user): void
    {
        event(new Events\User\Updated($user));
    }
}
