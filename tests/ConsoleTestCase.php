<?php

namespace R3bzya\ActionWrapper\Tests;

use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use Orchestra\Testbench\TestCase;
use R3bzya\ActionWrapper\Providers\WrapperServiceProvider;

abstract class ConsoleTestCase extends TestCase
{
    use InteractsWithPublishedFiles;

    protected function getPackageProviders($app): array
    {
        return [
            WrapperServiceProvider::class
        ];
    }
}