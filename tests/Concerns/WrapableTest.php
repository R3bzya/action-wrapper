<?php

namespace R3bzya\ActionWrapper\Tests\Concerns;

use PHPUnit\Framework\TestCase;
use R3bzya\ActionWrapper\Tests\Concerns\Dummies\WrapperDummy;

class WrapableTest extends TestCase
{
    public function testWrap(): void
    {
        $result = wrapper()
            ->wrap(WrapperDummy::class)
            ->execute('Test value');

        $this->assertInstanceOf(WrapperDummy::class, $result);

        $this->assertSame('Test value', $result->value);
    }
}