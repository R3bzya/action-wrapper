<?php

namespace R3bzya\ActionWrapper\Console;

use Illuminate\Console\GeneratorCommand as BaseGeneratorCommand;

abstract class GeneratorCommand extends BaseGeneratorCommand
{
    protected function qualifyModel(string $model): string
    {
        $namespacedModel = parent::qualifyModel($model);

        if ($this->alreadyExists($namespacedModel)) {
            return $namespacedModel;
        }

        $baseModel = parent::qualifyModel(class_basename($namespacedModel));

        if ($this->alreadyExists($baseModel)) {
            return $baseModel;
        }

        return $namespacedModel;
    }
}