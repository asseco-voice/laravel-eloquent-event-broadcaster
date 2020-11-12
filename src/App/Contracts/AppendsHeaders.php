<?php

namespace Voice\EloquentEventBroadcaster\App\Contracts;

interface AppendsHeaders
{
    public function appendHeaders(): array;
}
