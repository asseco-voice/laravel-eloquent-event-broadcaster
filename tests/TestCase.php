<?php

namespace Asseco\EloquentEventBroadcaster\Tests;

use Asseco\EloquentEventBroadcaster\BroadcasterServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [BroadcasterServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
