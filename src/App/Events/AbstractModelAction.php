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
use Voice\EloquentEventBroadcaster\App\Contracts\AppendsHeaders;
use Voice\EloquentEventBroadcaster\App\Contracts\AppendsData;
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

    public function broadcastQueue()
    {
        return Config::get('asseco-broadcaster.broadcast_queue');
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
        $data = $this->model->toArray();

        if ($this->model instanceof AppendsData) {
            return array_merge($data, $this->model->appendData());
        }

        return $data;
    }

    public function broadcastOn()
    {
        return [];
    }
}
