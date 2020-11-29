<?php

declare(strict_types=1);

namespace Asseco\EloquentEventBroadcaster\App\Events;

class ModelCreated extends AbstractModelAction
{
    protected function getActionName(): string
    {
        return self::CREATED;
    }
}
