<?php

declare(strict_types=1);

namespace Asseco\EloquentEventBroadcaster\App\Events;

class ModelRetrieved extends AbstractModelAction
{
    protected function getActionName(): string
    {
        return self::RETRIEVED;
    }
}
