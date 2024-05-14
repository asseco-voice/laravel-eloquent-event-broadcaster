<?php

declare(strict_types=1);

namespace Asseco\EloquentEventBroadcaster\Tests\Unit;

use Asseco\EloquentEventBroadcaster\App\Contracts\AppendsData;
use Asseco\EloquentEventBroadcaster\App\Contracts\AppendsHeaders;
use Asseco\EloquentEventBroadcaster\Tests\TestCase;
use Asseco\EloquentEventBroadcaster\Tests\TestEvent;
use Illuminate\Database\Eloquent\Model;
use Mockery;

class ModelActionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config(['asseco-broadcaster.with_changes' => false]);
    }

    /** @test */
    public function it_has_headers()
    {
        $testModel = Mockery::mock(Model::class);

        $modelEvent = new TestEvent($testModel);

        $expected = [
            'service' => 'laravel',
            'model' => get_class($testModel),
            'action' => 'test',
        ];

        $this->assertEquals($expected, $modelEvent->getHeaders());
    }

    /** @test */
    public function it_has_plain_model_headers()
    {
        config(['asseco-broadcaster.full_namespaced_models' => false]);

        $testModel = Mockery::mock(Model::class);

        $modelEvent = new TestEvent($testModel);

        $expected = [
            'service' => 'laravel',
            'model' => 'mockery_0__illuminate__database__eloquent__model',
            'action' => 'test',
        ];

        $this->assertEquals($expected, $modelEvent->getHeaders());
    }

    /** @test */
    public function it_has_appended_headers()
    {
        $testModel = Mockery::mock(Model::class, AppendsHeaders::class);

        $testModel->shouldReceive('appendHeaders')->andReturn([
            'appended_header' => '123',
        ]);

        $modelEvent = new TestEvent($testModel);

        $expected = [
            'service' => 'laravel',
            'model' => get_class($testModel),
            'action' => 'test',
            'appended_header' => '123',
        ];

        $this->assertEquals($expected, $modelEvent->getHeaders());
    }

    /** @test */
    public function it_returns_raw_data()
    {
        $testModel = Mockery::mock(Model::class);

        $testModel->shouldReceive('toArray')->andReturn([
            'serialized_model' => '123',
        ]);

        $modelEvent = new TestEvent($testModel);

        $expected = [
            'serialized_model' => '123',
        ];

        $this->assertArrayHasKey('uuid', $modelEvent->getRawData());
        $this->assertArrayHasKey('payload', $modelEvent->getRawData());
        $this->assertEquals($expected, $modelEvent->getRawData()['payload']);
    }

    /** @test */
    public function it_returns_appended_data()
    {
        $testModel = Mockery::mock(Model::class, AppendsData::class);

        $testModel->shouldReceive('toArray')->andReturn([
            'serialized_model' => '123',
        ]);

        $testModel->shouldReceive('appendData')->andReturn([
            'appended_data' => '123',
        ]);

        $modelEvent = new TestEvent($testModel);

        $expected = [
            'serialized_model' => '123',
            'appended_data' => '123',
        ];

        $this->assertArrayHasKey('uuid', $modelEvent->getRawData());
        $this->assertArrayHasKey('payload', $modelEvent->getRawData());
        $this->assertEquals($expected, $modelEvent->getRawData()['payload']);
    }

    /** @test */
    public function it_returns_model_changes()
    {
        config(['asseco-broadcaster.with_changes' => true]);

        $testModel = Mockery::mock(Model::class);

        $testModel->shouldReceive('getOriginal')->andReturn([
            'attribute' => 'test-before',
        ]);

        $testModel->shouldReceive('getChanges')->andReturn([
            'attribute' => 'test-after',
        ]);

        $testModel->shouldReceive('toArray')->andReturn([
            'attribute' => 'test-after',
        ]);

        $testModel->shouldReceive('wasChanged')->andReturn(true);
        $testModel->shouldReceive('getKey')->andReturn('id');

        $modelEvent = new TestEvent($testModel);

        $expected = [
            'id' => 'id',
            'action_performer_type' => 'unknown',
            'action_performer_id' => 'unknown',
            'old' => ['attribute' => 'test-before'],
            'new' => ['attribute' => 'test-after'],
        ];

        $rawData = $modelEvent->getRawData();

        $this->assertArrayHasKey('uuid', $rawData);
        $this->assertArrayHasKey('payload', $rawData);
        $this->assertArrayHasKey('_changes', $rawData);
        $this->assertEquals($expected, $rawData['_changes']);
    }
}
