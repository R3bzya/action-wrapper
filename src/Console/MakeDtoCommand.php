<?php

namespace R3bzya\ActionWrapper\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use R3bzya\ActionWrapper\Console\Guessers\FillableGuesser;
use R3bzya\ActionWrapper\Console\Traits\ReplaceClassReadonly;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function Laravel\Prompts\confirm;

#[AsCommand(name: 'make:dto')]
class MakeDtoCommand extends GeneratorCommand
{
    use ReplaceClassReadonly;

    protected $name = 'make:dto';

    protected $type = 'Dto';

    protected $description = 'Create a new action dto class';

    protected function getStub(): string
    {
        $model = $this->option('model');

        if ($model && $this->alreadyExists($this->qualifyModel($model))) {
            return __DIR__ . '/stubs/dto.stub';
        }

        return __DIR__ . '/stubs/dto.empty.stub';
    }

    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        if ($model = $this->option('model')) {
            $stub = $this->replaceFillable($stub, $model);
        }

        return $this->option('readonly')
            ? $this->replaceClassReadonly($stub, 'readonly class')
            : $this->replaceClassReadonly($stub, 'class');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . DIRECTORY_SEPARATOR . trim(static::path(), '\\/');
    }

    public static function path(): string
    {
        return config('action-wrapper.dto.path');
    }

    protected function getOptions(): array
    {
        return [
            ['readonly', 'r', InputOption::VALUE_NONE, 'Create a readonly class'],
            ['force', 'f', InputOption::VALUE_NONE, 'Create a class even if a DTO already exists'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Add fillable properties to the dto'],
        ];
    }

    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
            return;
        }

        $this->input->setOption(
            'readonly',
            confirm('Would you like to create a readonly dto class?')
        );
    }

    private function replaceFillable(string $stub, string $modelClass): string
    {
        $namespacedModel = $this->qualifyModel($modelClass);

        if (! $this->alreadyExists($namespacedModel)) {
            return $stub;
        }

        [$constructAttributes, $allAttributes, $attributeUses] = FillableGuesser::guess(new $namespacedModel);

        return Str::of($stub)
            ->replace('{{ constructAttributes }}', $constructAttributes)
            ->replace('{{ allAttributes }}', $allAttributes)
            ->replace('{{ uses }}', $this->formatAttributesUses($attributeUses))
            ->replaceMatches('/(namespace\s+[^\n]+;\n)(\s*\n)+/', "$1\n")
            ->value();
    }

    private function formatAttributesUses(array $uses): string
    {
        return collect($uses)->map(fn(string $use) => "use $use;")->implode(PHP_EOL);
    }
}