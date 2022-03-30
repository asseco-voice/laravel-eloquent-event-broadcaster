<?php

declare(strict_types=1);

namespace Asseco\EloquentEventBroadcaster\App\Events;

use Asseco\EloquentEventBroadcaster\App\Contracts\AppendsData;
use Asseco\EloquentEventBroadcaster\App\Contracts\AppendsHeaders;
use Asseco\EloquentEventBroadcaster\Changes;
use Asseco\Stomp\Queue\Contracts\HasHeaders;
use Asseco\Stomp\Queue\Contracts\HasRawData;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class AbstractModelAction implements ShouldBroadcast, HasHeaders, HasRawData
{
    protected const STOMP = 'stomp';
    protected const SYNC = 'sync';

    public const CREATED = 'created';
    public const DELETED = 'deleted';
    public const RESTORED = 'restored';
    public const RETRIEVED = 'retrieved';
    public const UPDATED = 'updated';

    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected Model $model;

    public array $appended = [];
    public array $headers = [];

    /**
     * Create a new event instance.
     *
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        app('broadcasterLog')->info('Action: ' . $this->getActionName() . ' for model ' . get_class($model));

        $this->model = $model;

        if (config('queue.default') !== self::STOMP) {
            $this->handleNonStompEvents();
        }
    }

    protected function handleNonStompEvents(): void
    {
        $this->headers = $this->getHeaders();

        $this->appended = array_merge(
            $this->appendChanges(),
            $this->appendAdditionalData()
        );
    }

    public function broadcastQueue()
    {
        return config('asseco-broadcaster.broadcast_queue');
    }

    protected function getServiceName(): string
    {
        return strtolower(Str::snake(config('app.name')));
    }

    protected function getModelName(): string
    {
        $modelNamespace = get_class($this->model);

        if (config('asseco-broadcaster.full_namespaced_models')) {
            return $modelNamespace;
        }

        $explode = explode('\\', $modelNamespace);
        $modelName = end($explode);

        return Str::snake($modelName);
    }

    abstract protected function getActionName(): string;

    public function getHeaders(): array
    {
        $headers = [
            'service' => $this->getServiceName(),
            'model'   => $this->getModelName(),
            'action'  => $this->getActionName(),
        ];

        if ($this->model instanceof AppendsHeaders) {
            return array_merge($headers, $this->model->appendHeaders());
        }

        return $headers;
    }

    public function getRawData(): array
    {
        $payload = array_merge($this->model->toArray(), $this->appendAdditionalData());

        $rawData = [
            'uuid'    => Str::uuid(),
            'payload' => $payload,
        ];

        return array_merge($rawData, $this->appendChanges());
    }

    protected function appendChanges(): array
    {
        return config('asseco-broadcaster.with_changes') ? $this->getChanges() : [];
    }

    protected function getChanges(): array
    {
        $changedKeys = array_keys($this->model->getChanges());

        $old = Arr::only($this->model->getOriginal(), $changedKeys);
        $new = $this->model->toArray();

        $actionPerformerType = $this->getPerformerType();
        $actionPerformerId = $this->getPerformerId();

        if ($this->model->wasChanged()) {
            $new = Arr::only($this->model->toArray(), $changedKeys);
        }

        $changes = new Changes(
            $this->model->getKey(), $actionPerformerType, $actionPerformerId, $old, $new
        );

        return $changes->generate();
    }

    protected function getPerformerType(): string
    {
        $data = $this->model->toArray();

        return
            Arr::get($data, 'deleter_type') ??
            Arr::get($data, 'updater_type') ??
            Arr::get($data, 'creator_type') ??
            'unknown';
    }

    protected function getPerformerId(): string
    {
        $data = $this->model->toArray();

        return
            Arr::get($data, 'deleted_by') ??
            Arr::get($data, 'updated_by') ??
            Arr::get($data, 'created_by') ??
            'unknown';
    }

    protected function appendAdditionalData(): array
    {
        return ($this->model instanceof AppendsData) ? $this->model->appendData() : [];
    }

    public function broadcastOn()
    {
        return config('asseco-broadcaster.broadcast_on');
    }

    /**
     * Having sync driver and broadcasting deleted event throws an error
     * because it tries to deserialize a model which has already been deleted.
     *
     * @return bool
     */
    public function broadcastWhen()
    {
        return config('queue.default') !== self::SYNC;
    }
}
