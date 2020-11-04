<?php

declare(strict_types=1);

namespace Voice\EloquentEventBroadcaster\App\Events;

class ModelDeleted extends AbstractModelAction
{
    protected function getActionName(): string
    {
        return 'deleted';
    }
}
