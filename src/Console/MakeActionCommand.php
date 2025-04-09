<?php

namespace R3bzya\ActionWrapper\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use R3bzya\ActionWrapper\Console\Enums\ActionEnum;
use R3bzya\ActionWrapper\Console\Guessers\ActionGuesser;
use R3bzya\ActionWrapper\Console\Guessers\ModelGuesser;
use R3bzya\ActionWrapper\Console\Traits\ReplaceClassReadonly;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function Laravel\Prompts\multiselect;

#[AsCommand(name: 'make:action')]
class MakeActionCommand extends GeneratorCommand
{
    use ReplaceClassReadonly;

    protected $name = 'make:action';

    protected $type = 'Action';

    protected $description = 'Create a new action class';

    public function handle(): int
    {
        if (parent::handle() === false && ! $this->option('force')) {
            return static::SUCCESS;
        }

        if ($this->option('all')) {
            $this->input->setOption('dto', true);
            $this->input->setOption('wrapper', true);
            $this->input->setOption('readonly', true);
            $this->input->setOption('model', true);
        }

        if ($this->option('model') !== false) {
            $this->createModel();
        }

        if ($this->option('dto') !== false) {
            $this->createDto();
        }

        return static::SUCCESS;
    }

    protected function getOptions(): array
    {
        return [
            ['all', null, InputOption::VALUE_NONE, 'Generate a new wrapped action class with DTO'],
            ['readonly', 'r', InputOption::VALUE_NONE, 'Create a readonly action class'],
            ['wrapper', 'w', InputOption::VALUE_NONE, 'Create a new wrapped action class'],
            ['dto', 'd', InputOption::VALUE_OPTIONAL, 'Create a new DTO for an action class'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Create a new wrapped action class with a model', false],
            ['force', 'f', InputOption::VALUE_NONE, 'Create a class even if an action class already exists'],
        ];
    }

    protected function getStub(): string
    {
        if ($this->getNamespacedModel() && $action = ActionGuesser::guess($this->getNameInput())) {
            return $this->resolveStubPath($this->getStubByAction($action));
        }

        if ($this->option('wrapper')) {
            return $this->option('dto') === false
                ? $this->resolveStubPath('/stubs/action.wrapped.stub')
                : $this->resolveStubPath('/stubs/action.wrapped.dto.stub');
        }

        return $this->option('dto') === false
            ? $this->resolveStubPath('/stubs/action.stub')
            : $this->resolveStubPath('/stubs/action.dto.stub');
    }

    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = base_path(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    protected function getStubByAction(ActionEnum $action): string
    {
        return match ($action) {
            ActionEnum::Create => '/stubs/action.create.stub',
            ActionEnum::Update => '/stubs/action.update.stub',
            ActionEnum::Destroy => $this->option('dto') === false
                ? '/stubs/action.destroy.stub'
                : '/stubs/action.destroy.dto.stub',
        };
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Actions';
    }

    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        if ($namespacedDto = $this->getNamespacedDto()) {
            $stub = $this->replaceDto($stub, $namespacedDto);
        }

        if ($namespacedModel = $this->getNamespacedModel()) {
            $stub = $this->replaceModel($stub, $namespacedModel);
        }

        $stub = $this->replaceType($stub, $namespacedModel);

        return $this->option('readonly')
            ? $this->replaceClassReadonly($stub, 'readonly class')
            : $this->replaceClassReadonly($stub, 'class');
    }

    protected function replaceDto(string $stub, string $name): string
    {
        $class = class_basename($name);

        $dtoVariable = $this->getDtoVariable($class);

        $replace = [
            'DummyDtoNamespace' => $name,
            '{{ dtoNamespace }}' => $name,
            '{{dtoNamespace}}' => $name,
            'DummyDtoClass' => $class,
            '{{ dtoClass }}' => $class,
            '{{dtoClass}}' => $class,
            'DummyDtoVariable' => $dtoVariable,
            '{{ dtoVariable }}' => $dtoVariable,
            '{{dtoVariable}}' => $dtoVariable,
        ];

        $stub = str_replace(
            array_keys($replace), array_values($replace), $stub,
        );

        return preg_replace(
            vsprintf('/use %s;[\r\n]+use %s;/', [
                preg_quote($name, '/'),
                preg_quote($name, '/'),
            ]),
            "use $name;",
            $stub,
        );
    }

    protected function getDtoVariable(string $class): string
    {
        return config('action-wrapper.action.dto_variable_placeholder') ?: lcfirst($class);
    }

    protected function replaceModel(string $stub, string $name): string
    {
        $class = class_basename($name);

        $modelKeyType = class_exists($name, false)
            ? (new $name)->getKeyType()
            : config('action-wrapper.model_key_type', 'int');

        $replace = [
            'DummyModelNamespace' => $name,
            '{{ modelNamespace }}' => $name,
            '{{modelNamespace}}' => $name,
            'DummyModel' => $class,
            '{{ model }}' => $class,
            '{{model}}' => $class,
            'DummyModelKeyType' => $modelKeyType,
            '{{ modelKeyType }}' => $modelKeyType,
            '{{modelKeyType}}' => $modelKeyType,
        ];

        $stub = str_replace(
            array_keys($replace), array_values($replace), $stub,
        );

        return preg_replace(
            vsprintf('/use %s;[\r\n]+use %s;/', [
                preg_quote($name, '/'),
                preg_quote($name, '/'),
            ]),
            "use $name;",
            $stub,
        );
    }

    protected function replaceType(string $stub, ?string $namespacedModel): string
    {
        $replace = $namespacedModel
            ? class_basename($namespacedModel)
            : config('action-wrapper.action.return_type', 'void');

        return str_replace(['DummyType', '{{ type }}', '{{type}}'], $replace, $stub);
    }

    protected function createDto(): void
    {
        $this->call('make:dto', [
            'name' => $this->getNamespacedDto(),
            '--model' => $this->getNamespacedModel(),
            '--readonly' => config('action-wrapper.action.readonly_dto', false),
        ]);
    }

    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
            return;
        }

        collect(multiselect('Would you like any of the following?', [
            'dto' => 'Action Dto',
            'wrapper' => 'Action Wrapper',
            'readonly' => 'Readonly Class',
        ]))->each(fn($option) => $input->setOption($option, true));
    }

    protected function getNamespacedDto(): ?string
    {
        $dto = $this->option('dto');

        if ($dto === false) {
            return null;
        }

        return $this->qualifyDto(is_string($dto) ? $dto : $this->getNameInput() . 'Dto');
    }

    protected function qualifyDto(string $name): string
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return sprintf(
            '%s\\%s\\%s',
            trim($this->rootNamespace(), '\\'),
            trim(MakeDtoCommand::path(), '\\/'),
            $name
        );
    }

    protected function getNamespacedModel(): ?string
    {
        $model = $this->option('model');

        if ($model === false) {
            return null;
        }

        if (is_string($model)) {
            return $this->qualifyModel($model);
        }

        if ($namespacedModel = ModelGuesser::guess($this->getNameInput())) {
            return $this->qualifyModel($namespacedModel);
        }

        return null;
    }

    protected function createModel(): void
    {
        if ($this->alreadyExists($namespacedModel = $this->getNamespacedModel())) {
            return;
        }

        $this->call('make:model', [
            'name' => $namespacedModel,
            '--migration' => true,
        ]);
    }
}