<?php

namespace R3bzya\ActionWrapper\Tests\Console;

use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use R3bzya\ActionWrapper\Tests\TestCase;

abstract class GeneratorTestCase extends TestCase
{
    use InteractsWithPublishedFiles;
}