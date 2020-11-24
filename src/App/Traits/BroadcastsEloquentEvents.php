<?php

declare(strict_types=1);

namespace Voice\EloquentEventBroadcaster\App\Traits;

trait BroadcastsEloquentEvents
{
    public function __construct(array $attributes = [])
    {
        $this->dispatchesEvents = array_merge(
            $this->dispatchesEvents,
            config('asseco-broadcaster.dispatches_events')
        );

        parent::__construct($attributes);
    }
}
