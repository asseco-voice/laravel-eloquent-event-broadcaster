<?php

declare(strict_types=1);

namespace Voice\EloquentEventBroadcaster\App\Events;

use Voice\EloquentEventBroadcaster\ChangesModel;

class ModelRestored extends AbstractModelAction
{
    protected function getActionName(): string
    {
        return self::RESTORED;
    }

    protected function getChanges(): array
    {
        [$old, $new] = $this->setRestoreFields();

        $actionPerformerType = $this->getPerformerType();
        $actionPerformerId = $this->getPerformerId();

        $changes = new ChangesModel(
            $this->model->getKey(), $actionPerformerType, $actionPerformerId, $old, $new
        );

        return $changes->generate();
    }

    /**
     * For 'normal' delete, show only ID as being unset.
     * @return array
     */
    protected function setRestoreFields(): array
    {
        $keyName = $this->model->getKeyName();

        $old = [$keyName => null];

        $new = [$keyName => $this->model->getKey()];

        return [$old, $new];
    }
}
