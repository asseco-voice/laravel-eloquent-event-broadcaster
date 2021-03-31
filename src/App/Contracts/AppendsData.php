<?php

declare(strict_types=1);

namespace Asseco\EloquentEventBroadcaster\App\Contracts;

interface AppendsData
{
    public function appendData(): array;
}
