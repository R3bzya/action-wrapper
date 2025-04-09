<?php

namespace R3bzya\ActionWrapper\Tests\Console;

use R3bzya\ActionWrapper\Console\MakeDtoCommand;

class MakeDtoCommandTest extends GeneratorTestCase
{
    protected array $files = [
        'app/Dto/Actions/*',
        'app/Models/*',
    ];

    public function testCreateDto(): void
    {
        $this->artisan(MakeDtoCommand::class, [
            'name' => 'Foo',
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Dto\Actions;',
            'class Foo',
            'public function all(): array',
        ], 'app/Dto/Actions/Foo.php');

        $this->assertFileDoesNotContains([
            'readonly class Foo',
        ], 'app/Dto/Actions/Foo.php');

        $this->assertFilenameExists('app/Dto/Actions/Foo.php');
    }

    public function testCreateReadonlyDto(): void
    {
        $this->artisan(MakeDtoCommand::class, [
            'name' => 'Foo',
            '--readonly' => true,
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Dto\Actions;',
            'readonly class Foo',
            'public function all(): array',
        ], 'app/Dto/Actions/Foo.php');

        $this->assertFilenameExists('app/Dto/Actions/Foo.php');
    }

    public function testCreateWithModelDto(): void
    {
        $this->artisan('make:model Foo');

        $this->artisan(MakeDtoCommand::class, [
            'name' => 'Foo',
            '--model' => 'Foo',
        ])->assertSuccessful();

        $this->assertFileContains([
            'namespace App\Dto\Actions;',
            'class Foo',
            'public int $id',
            '\'id\' => $this->id',
        ], 'app/Dto/Actions/Foo.php');

        $this->assertFilenameExists('app/Dto/Actions/Foo.php');
    }
}