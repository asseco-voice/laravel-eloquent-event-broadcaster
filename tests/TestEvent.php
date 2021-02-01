<?php

namespace Asseco\EloquentEventBroadcaster\Tests;

use Asseco\EloquentEventBroadcaster\App\Events\AbstractModelAction;

class TestEvent extends AbstractModelAction
{
    protected function getActionName(): string
    {
        return 'test';
    }
}
