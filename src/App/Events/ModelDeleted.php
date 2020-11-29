<?php

declare(strict_types=1);

namespace Asseco\EloquentEventBroadcaster\App\Events;

use Asseco\EloquentEventBroadcaster\ChangesModel;
use Illuminate\Support\Arr;

class ModelDeleted extends AbstractModelAction
{
    protected function getActionName(): string
    {
        return self::DELETED;
    }

    protected function getChanges(): array
    {
        [$old, $new] = $this->modelSoftDeleted() ?
            $this->setSoftDeleteFields() : $this->setDeleteFields();

        $actionPerformerType = $this->getPerformerType();
        $actionPerformerId = $this->getPerformerId();

        $changes = new ChangesModel(
            $this->model->getKey(), $actionPerformerType, $actionPerformerId, $old, $new
        );

        return $changes->generate();
    }

    protected function modelSoftDeleted(): bool
    {
        return isset($this->model->deleted_at);
    }

    protected function setSoftDeleteFields(): array
    {
        $data = $this->model->toArray();

        $old = [
            'deleted_at'   => null,
            'deleted_by'   => null,
            'deleter_type' => null,
        ];

        $new = [
            'deleted_at'   => Arr::get($data, 'deleted_at'),
            'deleted_by'   => Arr::get($data, 'deleted_by'),
            'deleter_type' => Arr::get($data, 'deleter_type'),
        ];

        return [$old, $new];
    }

    /**
     * For 'normal' delete, show only ID as being unset.
     * @return array
     */
    protected function setDeleteFields(): array
    {
        $keyName = $this->model->getKeyName();

        $old = [$keyName => $this->model->getKey()];

        $new = [$keyName => null];

        return [$old, $new];
    }
}
