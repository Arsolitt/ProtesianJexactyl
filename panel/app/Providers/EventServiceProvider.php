<?php

namespace Jexactyl\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Jexactyl\Events\Auth\OAuthLogin;
use Jexactyl\Events\Server\Created as ServerCreatedEvent;
use Jexactyl\Events\Server\Creating as ServerCreatingEvent;
use Jexactyl\Events\Server\Deleted as ServerDeletedEvent;
use Jexactyl\Events\Server\Installed as ServerInstalledEvent;
use Jexactyl\Events\Server\Updated as ServerUpdatedEvent;
use Jexactyl\Events\Server\Updating as ServerUpdatingEvent;
use Jexactyl\Events\Store\PaymentCanceled;
use Jexactyl\Events\Store\PaymentPaid;
use Jexactyl\Events\Store\ServerEdit as ServerEditEvent;
use Jexactyl\Events\User\UpdateCredits;
use Jexactyl\Listeners\Auth\AuthenticationListener;
use Jexactyl\Listeners\Auth\OAuthLoginListener;
use Jexactyl\Listeners\Payment\CanceledListener;
use Jexactyl\Listeners\Payment\PaidListener;
use Jexactyl\Listeners\Server\CreatedListener as ServerCreatedListener;
use Jexactyl\Listeners\Server\CreatingListener as ServerCreatingListener;
use Jexactyl\Listeners\Server\DeletedListener as ServerDeletedListener;
use Jexactyl\Listeners\Server\EditListener as ServerEditListener;
use Jexactyl\Listeners\Server\UpdatedListener as ServerUpdatedListener;
use Jexactyl\Listeners\Server\UpdatingListener as ServerUpdatingListener;
use Jexactyl\Listeners\User\UpdateCreditsListener;
use Jexactyl\Models\EggVariable;
use Jexactyl\Models\Server;
use Jexactyl\Models\Subuser;
use Jexactyl\Models\User;
use Jexactyl\Notifications\ServerInstalled as ServerInstalledNotification;
use Jexactyl\Observers\EggVariableObserver;
use Jexactyl\Observers\ServerObserver;
use Jexactyl\Observers\SubuserObserver;
use Jexactyl\Observers\UserObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     */
    protected $listen = [
        ServerInstalledEvent::class => [ServerInstalledNotification::class],
        OAuthLogin::class => [OAuthLoginListener::class],
        ServerCreatingEvent::class => [ServerCreatingListener::class],
        ServerCreatedEvent::class => [ServerCreatedListener::class],
        ServerUpdatingEvent::class => [ServerUpdatingListener::class],
        ServerUpdatedEvent::class => [ServerUpdatedListener::class],
        ServerDeletedEvent::class => [ServerDeletedListener::class],
        ServerEditEvent::class => [ServerEditListener::class],
        PaymentPaid::class => [PaidListener::class],
        PaymentCanceled::class => [CanceledListener::class],
        UpdateCredits::class => [UpdateCreditsListener::class],
    ];

    protected $subscribe = [
        AuthenticationListener::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        User::observe(UserObserver::class);
        Server::observe(ServerObserver::class);
        Subuser::observe(SubuserObserver::class);
        EggVariable::observe(EggVariableObserver::class);
    }
}
