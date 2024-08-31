<?php

namespace R3bzya\ActionWrapper\Console;

use Illuminate\Console\GeneratorCommand;
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
        return __DIR__ . '/stubs/dto.stub';
    }

    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

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
        ];
    }

    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
            return;
        }

        $this->input->setOption(
            'readonly',
            confirm('Would you like to create a readonly dto class?', true)
        );
    }
}