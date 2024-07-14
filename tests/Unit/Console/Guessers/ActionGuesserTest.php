<?php

namespace R3bzya\ActionWrapper\Tests\Unit\Console\Guessers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use R3bzya\ActionWrapper\Console\Enums\ActionEnum;
use R3bzya\ActionWrapper\Console\Guessers\ActionGuesser;

class ActionGuesserTest extends TestCase
{
    #[DataProvider('parseCreateData')]
    public function testParseCreate(string $name): void
    {
        $this->assertSame(ActionEnum::Create, ActionGuesser::guess($name));
    }

    public static function parseCreateData(): array
    {
        return [
            ['/Foo/Bar/CreateUserAction'],
            ['CreateUserAction'],
            ['MakeUserAction'],
        ];
    }

    #[DataProvider('parseUpdateData')]
    public function testParseUpdate(string $name): void
    {
        $this->assertSame(ActionEnum::Update, ActionGuesser::guess($name));
    }

    public static function parseUpdateData(): array
    {
        return [
            ['/Foo/Bar/UpdateUserAction'],
            ['UpdateUserAction'],
            ['EditUserAction'],
        ];
    }

    #[DataProvider('parseDestroyData')]
    public function testParseDestroy(string $name): void
    {
        $this->assertSame(ActionEnum::Destroy, ActionGuesser::guess($name));
    }

    public static function parseDestroyData(): array
    {
        return [
            ['/Foo/Bar/DestroyUserAction'],
            ['DestroyUserAction'],
            ['DeleteUserAction'],
            ['RemoveUserAction'],
        ];
    }

    public function testUndefinedAction(): void
    {
        $this->assertNull(ActionGuesser::guess('UserAction'));
    }
}