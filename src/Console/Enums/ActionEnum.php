<?php

namespace R3bzya\ActionWrapper\Console\Enums;

enum ActionEnum
{
    case Create;
    case Update;
    case Destroy;

    public function aliases(): array
    {
        return match ($this) {
            self::Create => [
                'create',
                'make',
            ],
            self::Update => [
                'update',
                'edit',
            ],
            self::Destroy => [
                'destroy',
                'delete',
                'remove',
            ],
        };
    }
}
