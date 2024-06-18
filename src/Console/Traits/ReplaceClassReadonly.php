<?php

namespace R3bzya\ActionWrapper\Console\Traits;

trait ReplaceClassReadonly
{
    protected function replaceClassReadonly(string $stub, string $replace): string
    {
        return str_replace(['ClassReadonly', '{{ classReadonly }}', '{{classReadonly}}'], $replace, $stub);
    }
}