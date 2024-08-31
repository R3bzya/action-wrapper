<?php

namespace R3bzya\ActionWrapper\Tests\Console;

use Illuminate\Support\Facades\App;
use R3bzya\ActionWrapper\Console\MakeActionCommand;

class MakeActionCommandTest extends GeneratorTestCase
{
    protected array $files = [
        'app/Actions/*',
        'app/Dto/Actions/*',
        'app/Models/*',
        'database/migrations/*'
    ];

    public function testCanCreateAction(): void
    {
        $this->artisan(MakeActionCommand::class, [
            'name' => 'Foo',
            '--dto' => false
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'class Foo',
            'public function execute(): void',
        ], 'app/Actions/Foo.php');

        $this->assertFileDoesNotContains([
            'use App\Dto\Actions\FooDto;',
            'readonly class Foo',
        ], 'app/Actions/Foo.php');

        $this->assertFilenameDoesNotExists('app/Dto/Actions/FooDto.php');
    }

    public function testCanCreateActionDto(): void
    {
        $this->artisan(MakeActionCommand::class, [
            'name' => 'Foo',
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'use App\Dto\Actions\FooDto;',
            'class Foo',
            'public function execute(FooDto $fooDto): void',
        ], 'app/Actions/Foo.php');

        $this->assertFileDoesNotContains([
            'readonly class Foo',
        ], 'app/Actions/Foo.php');

        $this->assertFilenameExists('app/Dto/Actions/FooDto.php');
    }

    public function testCanCreateActionDtoWithCustomModelKeyType(): void
    {
        App::get('config')->set('action-wrapper.model_key_type', 'string');

        $this->artisan(MakeActionCommand::class, [
            'name' => 'UpdateFoo',
            '--model' => true,
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'use App\Dto\Actions\UpdateFooDto;',
            'use App\Models\Foo;',
            'use R3bzya\ActionWrapper\Support\Traits\HasActionWrapper;',
            'class UpdateFoo',
            'public function execute(string $id, UpdateFooDto $updateFooDto): bool',
            'return Foo::findOrFail($id)->update($updateFooDto->toArray());'
        ], 'app/Actions/UpdateFoo.php');

        $this->assertFileDoesNotContains([
            'readonly class UpdateFoo',
        ], 'app/Actions/UpdateFoo.php');

        $this->assertFilenameExists('app/Dto/Actions/UpdateFooDto.php');

        $this->assertFilenameExists('app/Models/Foo.php');
    }

    public function testCanCreateActionDtoWithCustomReturn(): void
    {
        App::get('config')->set('action-wrapper.action.return_type', 'bool');

        $this->artisan(MakeActionCommand::class, [
            'name' => 'Foo',
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'use App\Dto\Actions\FooDto;',
            'class Foo',
            'public function execute(FooDto $fooDto): bool',
        ], 'app/Actions/Foo.php');

        $this->assertFileDoesNotContains([
            'readonly class Foo',
        ], 'app/Actions/Foo.php');

        $this->assertFilenameExists('app/Dto/Actions/FooDto.php');
    }

    public function testCanReplaceReadonly(): void
    {
        $this->artisan(MakeActionCommand::class, [
            'name' => 'Foo',
            '--readonly' => true,
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'use App\Dto\Actions\FooDto;',
            'readonly class Foo',
            'public function execute(FooDto $fooDto): void',
        ], 'app/Actions/Foo.php');

        $this->assertFilenameExists('app/Dto/Actions/FooDto.php');
    }

    public function testCanCreateWithCustomDtoName(): void
    {
        $this->artisan(MakeActionCommand::class, [
            'name' => 'Foo',
            '--dto' => 'BarDto',
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'use App\Dto\Actions\BarDto',
            'class Foo',
            'public function execute(BarDto $barDto): void',
        ], 'app/Actions/Foo.php');

        $this->assertFileDoesNotContains([
            'readonly class Foo',
        ], 'app/Actions/Foo.php');

        $this->assertFilenameExists('app/Dto/Actions/BarDto.php');
    }

    public function testCanCreateActionWrapped(): void
    {
        $this->artisan(MakeActionCommand::class, [
            'name' => 'Foo',
            '--dto' => false,
            '--wrapper' => true,
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'use R3bzya\ActionWrapper\Support\Traits\HasActionWrapper;',
            'class Foo',
            'public function execute(): void',
        ], 'app/Actions/Foo.php');

        $this->assertFileDoesNotContains([
            'use App\Dto\Actions\FooDto;',
            'readonly class Foo',
        ], 'app/Actions/Foo.php');

        $this->assertFilenameDoesNotExists('app/Dto/Actions/FooDto.php');
    }

    public function testCanCreateActionWrappedDto(): void
    {
        $this->artisan(MakeActionCommand::class, [
            'name' => 'Foo',
            '--dto' => true,
            '--wrapper' => true,
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'use R3bzya\ActionWrapper\Support\Traits\HasActionWrapper;',
            'use App\Dto\Actions\FooDto;',
            'class Foo',
            'public function execute(FooDto $fooDto): void',
        ], 'app/Actions/Foo.php');

        $this->assertFileDoesNotContains([
            'readonly class Foo',
        ], 'app/Actions/Foo.php');

        $this->assertFilenameExists('app/Dto/Actions/FooDto.php');
    }

    public function testCanCreateActionCreate(): void
    {
        $this->artisan(MakeActionCommand::class, [
            'name' => 'CreateFoo',
            '--model' => true,
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'use App\Dto\Actions\CreateFooDto;',
            'use App\Models\Foo;',
            'use R3bzya\ActionWrapper\Support\Traits\HasActionWrapper;',
            'class CreateFoo',
            'public function execute(CreateFooDto $createFooDto): Foo',
            'return Foo::create($createFooDto->toArray());',
        ], 'app/Actions/CreateFoo.php');

        $this->assertFileDoesNotContains([
            'readonly class CreateFoo',
        ], 'app/Actions/CreateFoo.php');

        $this->assertFilenameExists('app/Dto/Actions/CreateFooDto.php');

        $this->assertFilenameExists('app/Models/Foo.php');
    }

    public function testCanCreateActionUpdate(): void
    {
        $this->artisan(MakeActionCommand::class, [
            'name' => 'UpdateFoo',
            '--model' => true,
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'use App\Dto\Actions\UpdateFooDto;',
            'use App\Models\Foo;',
            'use R3bzya\ActionWrapper\Support\Traits\HasActionWrapper;',
            'class UpdateFoo',
            'public function execute(int $id, UpdateFooDto $updateFooDto): bool',
            'return Foo::findOrFail($id)->update($updateFooDto->toArray())',
        ], 'app/Actions/UpdateFoo.php');

        $this->assertFileDoesNotContains([
            'readonly class UpdateFoo',
        ], 'app/Actions/UpdateFoo.php');

        $this->assertFilenameExists('app/Dto/Actions/UpdateFooDto.php');

        $this->assertFilenameExists('app/Models/Foo.php');
    }

    public function testCanCreateActionDestroy(): void
    {
        $this->artisan(MakeActionCommand::class, [
            'name' => 'DestroyFoo',
            '--model' => true,
            '--dto' => false,
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'use App\Models\Foo;',
            'use R3bzya\ActionWrapper\Support\Traits\HasActionWrapper;',
            'class DestroyFoo',
            'public function execute(int $id): bool',
            'return (bool) Foo::destroy($id)',
        ], 'app/Actions/DestroyFoo.php');

        $this->assertFileDoesNotContains([
            'readonly class DestroyFoo',
        ], 'app/Actions/DestroyFoo.php');

        $this->assertFilenameDoesNotExists('app/Dto/Actions/DestroyFooDto.php');

        $this->assertFilenameExists('app/Models/Foo.php');
    }

    public function testCanCreateActionDestroyDto(): void
    {
        $this->artisan(MakeActionCommand::class, [
            'name' => 'DestroyFoo',
            '--model' => true,
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'use App\Dto\Actions\DestroyFooDto;',
            'use App\Models\Foo;',
            'use R3bzya\ActionWrapper\Support\Traits\HasActionWrapper;',
            'class DestroyFoo',
            'public function execute(DestroyFooDto $destroyFooDto): bool',
            'return (bool) Foo::destroy($destroyFooDto->toArray())',
        ], 'app/Actions/DestroyFoo.php');

        $this->assertFileDoesNotContains([
            'readonly class DestroyFoo',
        ], 'app/Actions/DestroyFoo.php');

        $this->assertFilenameExists('app/Dto/Actions/DestroyFooDto.php');

        $this->assertFilenameExists('app/Models/Foo.php');
    }

    public function testCanCreateWithCustomModel(): void
    {
        $this->artisan(MakeActionCommand::class, [
            'name' => 'DestroyFoo',
            '--model' => 'Bar',
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Actions;',
            'use App\Dto\Actions\DestroyFooDto;',
            'use App\Models\Bar;',
            'use R3bzya\ActionWrapper\Support\Traits\HasActionWrapper;',
            'class DestroyFoo',
            'public function execute(DestroyFooDto $destroyFooDto): bool',
            'return (bool) Bar::destroy($destroyFooDto->toArray())',
        ], 'app/Actions/DestroyFoo.php');

        $this->assertFileDoesNotContains([
            'readonly class DestroyFoo',
        ], 'app/Actions/DestroyFoo.php');

        $this->assertFilenameExists('app/Dto/Actions/DestroyFooDto.php');

        $this->assertFilenameExists('app/Models/Bar.php');
    }
}