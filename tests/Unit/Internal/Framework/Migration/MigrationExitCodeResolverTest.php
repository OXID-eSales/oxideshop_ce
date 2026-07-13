<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExitCodeResolver;
use PHPUnit\Framework\TestCase;

final class MigrationExitCodeResolverTest extends TestCase
{
    private MigrationExitCodeResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new MigrationExitCodeResolver();
    }

    public function testCombineReturnSuccessWhenBothSucceed(): void
    {
        $this->assertSame(0, $this->resolver->combine(0, 0));
    }

    public function testCombineReturnsHardFailureWhenFirstFails(): void
    {
        $this->assertSame(1, $this->resolver->combine(1, 0));
    }

    public function testCombineReturnsHardFailureWhenSecondFails(): void
    {
        $this->assertSame(1, $this->resolver->combine(0, 1));
    }

    public function testCombineReturnsCancelledCodeWhenFirstIsCancelled(): void
    {
        $this->assertSame(3, $this->resolver->combine(3, 0));
    }

    public function testCombineReturnsCancelledCodeWhenSecondIsCancelled(): void
    {
        $this->assertSame(3, $this->resolver->combine(0, 3));
    }

    public function testCombineReturnsHardFailureOverCancelled(): void
    {
        $this->assertSame(1, $this->resolver->combine(1, 3));
    }

    public function testCombineReturnsHardFailureOverCancelledReversed(): void
    {
        $this->assertSame(1, $this->resolver->combine(3, 1));
    }
}
