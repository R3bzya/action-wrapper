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
            'use Illuminate\Contracts\Support\Arrayable;',
            'class Foo implements Arrayable',
        ], 'app/Dto/Actions/Foo.php');

        $this->assertFileDoesNotContains([
            'readonly class Foo implements Arrayable',
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
            'use Illuminate\Contracts\Support\Arrayable;',
            'readonly class Foo implements Arrayable',
        ], 'app/Dto/Actions/Foo.php');

        $this->assertFilenameExists('app/Dto/Actions/Foo.php');
    }
}