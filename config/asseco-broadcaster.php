<?php

use Voice\EloquentEventBroadcaster\App\Events\ModelCreated;
use Voice\EloquentEventBroadcaster\App\Events\ModelDeleted;
use Voice\EloquentEventBroadcaster\App\Events\ModelUpdated;

return [

    'dispatches_events' => [
        'created' => ModelCreated::class,
        'updated' => ModelUpdated::class,
        'deleted' => ModelDeleted::class,
    ],
];
