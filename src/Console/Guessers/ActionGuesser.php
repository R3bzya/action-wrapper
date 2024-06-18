<?php

namespace R3bzya\ActionWrapper\Console\Guessers;

use Illuminate\Support\Str;
use R3bzya\ActionWrapper\Console\Enums\ActionEnum;

class ActionGuesser
{
    public static function guess(string $name): ?ActionEnum
    {
        foreach (ActionEnum::cases() as $action) {
            if (Str::contains(class_basename($name), $action->aliases(), true)) {
                return $action;
            }
        }

        return null;
    }
}