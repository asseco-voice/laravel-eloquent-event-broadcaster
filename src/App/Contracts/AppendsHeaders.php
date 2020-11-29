<?php

namespace Asseco\EloquentEventBroadcaster\App\Contracts;

interface AppendsHeaders
{
    public function appendHeaders(): array;
}
