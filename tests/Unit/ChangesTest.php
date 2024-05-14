<?php

declare(strict_types=1);

namespace Asseco\EloquentEventBroadcaster\Tests\Unit;

use Asseco\EloquentEventBroadcaster\Changes;
use Asseco\EloquentEventBroadcaster\Tests\TestCase;

class ChangesTest extends TestCase
{
    /** @test */
    public function returns_changes_array()
    {
        $changes = new Changes(1, 'user', '1', [], []);

        $expected = ['_changes' => [
            'id' => 1,
            'action_performer_type' => 'user',
            'action_performer_id' => '1',
            'old' => [],
            'new' => [],
        ]];

        $this->assertEquals($expected, $changes->generate());
    }
}
