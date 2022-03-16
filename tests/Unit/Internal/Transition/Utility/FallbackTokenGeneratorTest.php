<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\FallbackTokenGenerator;
use PHPUnit\Framework\TestCase;

final class FallbackTokenGeneratorTest extends TestCase
{
    private FallbackTokenGenerator $tokenGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tokenGenerator = new FallbackTokenGenerator();
    }

    /** @dataProvider getHexTokenDataProvider */
    public function testGetHexTokenWillReturnExpectedLength(int $length): void
    {
        $token = $this->tokenGenerator->getHexToken($length);
        $this->assertEquals($length, strlen($token));
    }

    public function getHexTokenDataProvider(): array
    {
        return [
            [0], [1], [2], [1024],
        ];
    }

    public function testGetHexTokenWillReturnUniqueValues(): void
    {
        $tokens = [];
        $tokenLength = 32;
        $iterations = 3;

        for ($i = 0; $i < $iterations; $i++) {
            $tokens[] = $this->tokenGenerator->getHexToken($tokenLength);
        }

        $numberOfTokens = count($tokens);
        $uniqueTokens = array_unique($tokens);
        $this->assertCount($numberOfTokens, $uniqueTokens);
    }
}
