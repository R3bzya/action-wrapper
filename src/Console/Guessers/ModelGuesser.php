<?php

namespace R3bzya\ActionWrapper\Console\Guessers;

use R3bzya\ActionWrapper\Console\Enums\ActionEnum;

class ModelGuesser
{
    public static function guess(string $name): ?string
    {
        foreach (static::patterns() as $pattern) {
            if (preg_match($pattern, $name, $matches)) {
                return str_replace(class_basename($name), $matches[1], $name);
            }
        }

        return null;
    }

    private static function patterns(): array
    {
        $result = [];
        foreach (ActionEnum::cases() as $action) {
            foreach ($action->aliases() as $alias) {
                $result = array_merge($result, static::makePatterns(ucfirst($alias)));
            }
        }

        return $result;
    }

    private static function makePatterns(string $alias): array
    {
        return [
            "/^$alias(\w+)Action$/",
            "/^$alias(\w+)$/",
            "/.+\/$alias(\w+)Action$/",
            "/.+\/$alias(\w+)$/",
        ];
    }
}