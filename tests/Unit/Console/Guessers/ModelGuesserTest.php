<?php

namespace R3bzya\ActionWrapper\Tests\Unit\Console\Guessers;

use PHPUnit\Framework\Attributes\DataProvider;
use R3bzya\ActionWrapper\Console\Guessers\ModelGuesser;
use R3bzya\ActionWrapper\Tests\Feature\TestCase;

class ModelGuesserTest extends TestCase
{
    #[DataProvider('parseNameData')]
    public function testParseName(string $name): void
    {
        $this->assertSame('User', ModelGuesser::guess($name));
    }

    public static function parseNameData(): array
    {
        return [
            ['CreateUser'],
            ['CreateUserAction'],
            ['MakeUser'],
            ['MakeUserAction'],
            ['UpdateUser'],
            ['UpdateUserAction'],
            ['EditUser'],
            ['EditUserAction'],
            ['DestroyUser'],
            ['DestroyUserAction'],
            ['DeleteUser'],
            ['DeleteUserAction'],
            ['RemoveUser'],
            ['RemoveUserAction'],
        ];
    }

    #[DataProvider('parseNamespacedNameData')]
    public function testParseNamespacedName(string $name): void
    {
        $this->assertSame('Foo/Bar/User', ModelGuesser::guess($name));
    }

    public static function parseNamespacedNameData(): array
    {
        return [
            ['Foo/Bar/CreateUserAction'],
            ['Foo/Bar/MakeUserAction'],
            ['Foo/Bar/UpdateUserAction'],
            ['Foo/Bar/EditUserAction'],
            ['Foo/Bar/DestroyUserAction'],
            ['Foo/Bar/DeleteUserAction'],
            ['Foo/Bar/RemoveUserAction'],
        ];
    }

    #[DataProvider('incorrectParseData')]
    public function testIncorrectParse(string $name): void
    {
        $this->assertNull(ModelGuesser::guess($name));
    }

    public static function incorrectParseData(): array
    {
        return [
            ['UserCreateAction'],
            ['Foo/Bar/UserCreateAction'],
        ];
    }
}