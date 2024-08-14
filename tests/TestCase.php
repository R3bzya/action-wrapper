<?php

namespace R3bzya\ActionWrapper\Tests;

use R3bzya\ActionWrapper\Providers\WrapperServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            WrapperServiceProvider::class
        ];
    }
}