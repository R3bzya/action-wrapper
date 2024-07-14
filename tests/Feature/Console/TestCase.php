<?php

namespace R3bzya\ActionWrapper\Tests\Feature\Console;

use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;

abstract class TestCase extends \R3bzya\ActionWrapper\Tests\Feature\TestCase
{
    use InteractsWithPublishedFiles;
}