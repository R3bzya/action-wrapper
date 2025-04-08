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
                'store',
            ],
            self::Update => [
                'update',
                'edit',
                'put',
                'patch',
            ],
            self::Destroy => [
                'destroy',
                'delete',
                'remove',
            ],
        };
    }
}
