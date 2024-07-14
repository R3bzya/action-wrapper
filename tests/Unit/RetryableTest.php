<?php

namespace R3bzya\ActionWrapper\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class RetryableTest extends TestCase
{
    public function testAttemptsAreOver(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Foo');

        try {
            wrapper()
                ->retry(3)
                ->execute(function () {
                    $this->addToAssertionCount(1);

                    throw new RuntimeException('Foo');
                });
        } finally {
            $this->assertSame(3, $this->numberOfAssertionsPerformed());
        }
    }

    public function testLastChanceForSuccess(): void
    {
        $result = wrapper()
            ->retry(3)
            ->execute(function () {
                $this->addToAssertionCount(1);

                if ($this->numberOfAssertionsPerformed() == 3) {
                    return 'finally';
                }

                throw new RuntimeException;
            });

        $this->assertSame(3, $this->numberOfAssertionsPerformed());
        $this->assertSame('finally', $result);
    }

    public function testMinimumNumberOfAttempts(): void
    {
        $this->assertTrue(wrapper()->retry(1)->execute(true));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to retry. Minimum number of attempts: 1.');

        wrapper()
            ->retry(0)
            ->execute(true);
    }
}