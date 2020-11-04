<?php

declare(strict_types=1);

namespace Voice\EloquentEventBroadcaster\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Voice\Stomp\Queue\Contracts\HasHeaders;
use Voice\Stomp\Queue\Contracts\HasRawData;

abstract class AbstractModelAction implements ShouldBroadcast, HasHeaders, HasRawData
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model $model;

    /**
     * Create a new event instance.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function getServiceName(): string
    {
        return strtolower(Str::snake(Config::get('app.name')));
    }

    protected function getModelName(): string
    {
        return get_class($this->model);
    }

    abstract protected function getActionName(): string;

    public function getHeaders(): array
    {
        return [
            'service' => $this->getServiceName(),
            'model'   => $this->getModelName(),
            'action'  => $this->getActionName(),
        ];
    }

    public function getRawData(): array
    {
        return $this->model->toArray();
    }

    public function broadcastOn()
    {
        return [];
    }
}
