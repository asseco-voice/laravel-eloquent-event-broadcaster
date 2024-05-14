<?php

use Asseco\EloquentEventBroadcaster\App\Events\ModelCreated;
use Asseco\EloquentEventBroadcaster\App\Events\ModelDeleted;
use Asseco\EloquentEventBroadcaster\App\Events\ModelRestored;
use Asseco\EloquentEventBroadcaster\App\Events\ModelUpdated;

return [

    /**
     * Registered events.
     */
    'dispatches_events' => [
        'created' => ModelCreated::class,
        'deleted' => ModelDeleted::class,
        'restored' => ModelRestored::class,
        // 'retrieved' => ModelRetrieved::class,
        'updated' => ModelUpdated::class,
    ],

    /**
     * Queue on which the events will be broadcast to.
     */
    'broadcast_queue' => 'eloquent',

    /**
     * Disable if you don't want to have old/new attribute values propagated.
     */
    'with_changes' => true,

    /**
     * Channels to broadcast the events on.
     */
    'broadcast_on' => [],

    'enable_logs' => env('BROADCASTER_LOGS', false) === true,

    /**
     * Sending model name in headers will evaluate by default to full model namespace.
     * Setting this option to false will throw lowercase singular instead.
     * i.e. 'App\Models\MyModel' vs. 'my_model'.
     */
    'full_namespaced_models' => true,
];
