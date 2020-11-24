<?php

declare(strict_types=1);

namespace Voice\EloquentEventBroadcaster\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Voice\EloquentEventBroadcaster\App\Contracts\AppendsData;
use Voice\EloquentEventBroadcaster\App\Contracts\AppendsHeaders;
use Voice\EloquentEventBroadcaster\ChangesModel;
use Voice\Stomp\Queue\Contracts\HasHeaders;
use Voice\Stomp\Queue\Contracts\HasRawData;

abstract class AbstractModelAction implements ShouldBroadcast, HasHeaders, HasRawData
{
    public const STOMP = 'stomp';

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model $model;

    public array $appended = [];
    public array $headers = [];

    /**
     * Create a new event instance.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;

        $this->handleNonStompEvents();
    }

    protected function handleNonStompEvents(): void
    {
        if (config('queue.default') !== self::STOMP) {
            $this->headers = $this->getHeaders();

            $this->appended = array_merge(
                $this->appendChanges(),
                $this->appendAdditionalData()
            );
        }
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
        return get_class($this->model);
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
        return array_merge(
            $this->model->toArray(),
            $this->appendChanges(),
            $this->appendAdditionalData()
        );
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

        $changes = new ChangesModel(
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
}
