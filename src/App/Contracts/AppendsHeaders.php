<?php

declare(strict_types=1);

namespace Asseco\EloquentEventBroadcaster\App\Contracts;

interface AppendsHeaders
{
    public function appendHeaders(): array;
}
