<?php

namespace R3bzya\ActionWrapper\Tests\Console;

use R3bzya\ActionWrapper\Console\MakeDtoCommand;

class MakeDtoCommandTest extends GeneratorTestCase
{
    protected array $files = [
        'app/Dto/Actions/*',
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
}