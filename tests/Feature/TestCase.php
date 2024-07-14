<?php

namespace R3bzya\ActionWrapper\Tests\Feature;

use R3bzya\ActionWrapper\Providers\WrapperServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            WrapperServiceProvider::class
        ];
    }
}