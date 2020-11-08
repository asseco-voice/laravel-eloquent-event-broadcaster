<?php

use Voice\EloquentEventBroadcaster\App\Events\ModelCreated;
use Voice\EloquentEventBroadcaster\App\Events\ModelDeleted;
use Voice\EloquentEventBroadcaster\App\Events\ModelUpdated;

return [

    /**
     * Registered events
     */
    'dispatches_events' => [
        'created' => ModelCreated::class,
        'updated' => ModelUpdated::class,
        'deleted' => ModelDeleted::class,
    ],

    /**
     * Queue on which the events will be broadcast to
     */
    'broadcast_queue' => 'eloquent::model_events',
];
