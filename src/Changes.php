<?php

declare(strict_types=1);

namespace Asseco\EloquentEventBroadcaster;

class Changes
{
    public const CHANGES = '_changes';

    public string $modelId;
    public string $actionPerformerType;
    public string $actionPerformerId;
    public array $old;
    public array $new;

    public function __construct($modelId, string $actionPerformerType, string $actionPerformerId, array $old, array $new)
    {
        $this->modelId = "$modelId";
        $this->actionPerformerType = $actionPerformerType;
        $this->actionPerformerId = $actionPerformerId;
        $this->old = $old;
        $this->new = $new;
    }

    public function generate(): array
    {
        return [self::CHANGES => [
            'id'                    => $this->modelId,
            'action_performer_type' => $this->actionPerformerType,
            'action_performer_id'   => $this->actionPerformerId,
            'old'                   => $this->old,
            'new'                   => $this->new,
        ]];
    }
}
