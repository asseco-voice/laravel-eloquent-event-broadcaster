<?php

use Voice\EloquentEventBroadcaster\App\Events\ModelCreated;
use Voice\EloquentEventBroadcaster\App\Events\ModelDeleted;
use Voice\EloquentEventBroadcaster\App\Events\ModelRestored;
use Voice\EloquentEventBroadcaster\App\Events\ModelUpdated;

return [

    /**
     * Registered events.
     */
    'dispatches_events' => [
        'created'  => ModelCreated::class,
        'updated'  => ModelUpdated::class,
        'deleted'  => ModelDeleted::class,
        'restored' => ModelRestored::class,
    ],

    /**
     * Queue on which the events will be broadcast to.
     */
    'broadcast_queue'   => 'eloquent',

    /**
     * Disable if you don't want to have old/new attribute values propagated
     */
    'with_changes'      => true,

    /**
     * Channels to broadcast the events on
     */
    'broadcast_on'      => [],
];
