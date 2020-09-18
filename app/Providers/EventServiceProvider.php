<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // ... other providers
            'SocialiteProviders\\Instagram\\InstagramExtendSocialite@handle',
        ],
        \App\Events\ExampleEvent::class => [
            \App\Listeners\ExampleListener::class,
        ],
    ];
}
