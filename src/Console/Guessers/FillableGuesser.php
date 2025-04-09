<?php

namespace R3bzya\ActionWrapper\Console\Guessers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class FillableGuesser
{
    const CONSTRUCT_TEMPLATE = 'public {type} ${property},';
    const ARRAY_TEMPLATE = '\'{snake_property}\' => $this->{property},';

    public static function guess(Model $instance): array
    {
        $attributes = collect($instance->getFillable())
            ->mapWithKeys(fn($property) => [$property => 'mixed'])
            ->merge(static::normalizeCasts($instance->getCasts()))
            ->mapWithKeys(fn($type, $property) => [Str::camel($property) => $type])
            ->sortKeys();

        return [
            static::transformAttributes($attributes, self::CONSTRUCT_TEMPLATE, 8),
            static::transformAttributes($attributes, self::ARRAY_TEMPLATE, 12),
            static::getAttributeUses($attributes),
        ];
    }

    private static function normalizeCasts(array $casts): array
    {
        return collect($casts)
            ->mapWithKeys(fn($type, $property) => [$property => static::normalizeCast($type)])
            ->all();
    }

    private static function normalizeCast(string $type): string
    {
        $cast = match ($type) {
            'timestamp',
            'date',
            'datetime',
            'immutable_date',
            'immutable_datetime' => Carbon::class,
            'boolean' => 'bool',
            'decimal',
            'double',
            'float',
            'real' => 'float',
            'int', 'integer' => 'int',
            'string',
            'hashed',
            'encrypted' => 'string',
            'array',
            'encrypted:array' => 'array',
            'collection',
            'encrypted:collection' => 'array|' . Collection::class,
            'AsStringable' => 'string|' . Stringable::class,
            'object'  => 'array|object',
            'encrypted:object' => 'object',
            default => null,
        };

        return Arr::get(config('action-wrapper.dto.casts'), $type, $cast ?: 'mixed');
    }

    private static function transformAttributes(Collection $attributes, string $template, int $indent): string
    {
        return $attributes->map(function (string $type, string $property) use ($template) {
            return Str::replace(
                ['{type}', '{property}', '{snake_property}'],
                [class_basename($type), $property, Str::snake($property)],
                $template,
            );
        })->implode(PHP_EOL . Str::repeat(' ', $indent));
    }

    private static function getAttributeUses(Collection $attributes): array
    {
        return $attributes
            ->map(fn(string $type) => explode('|', $type))
            ->flatten()
            ->filter(fn(string $type) => Str::contains($type, '\\'))
            ->values()
            ->unique()
            ->all();
    }
}