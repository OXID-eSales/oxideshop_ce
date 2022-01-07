<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Authentication\Generator;

use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Generator\RandomTokenGenerator;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class RandomTokenGeneratorTest extends TestCase
{
    use ContainerTrait;

    public function testGetAlphanumericTokenWillReturnExpectedType(): void
    {
        $token = (new RandomTokenGenerator())->getAlphanumericToken(256);

        $this->assertTrue(ctype_alnum($token));
    }

    public function testGetAlphanumericTokenWithShortTokenWillReturnsExpectedLength(): void
    {
        $tokenLength = 1;

        $token = (new RandomTokenGenerator())->getAlphanumericToken($tokenLength);

        $this->assertEquals($tokenLength, strlen($token));
    }

    public function testGetAlphanumericTokenWithLongTokenWillReturnsExpectedLength(): void
    {
        $tokenLength = 1024;

        $token = (new RandomTokenGenerator())->getAlphanumericToken($tokenLength);

        $this->assertEquals($tokenLength, strlen($token));
    }

    public function testGetAlphanumericTokenWillPadResultToExpectedLength(): void
    {
        $tokens = [];
        $tokenLength = 3;
        $tokenGenerator = new RandomTokenGenerator();

        /**
         * Strings with multiple non-alphanumeric characters (e.g. "/+E4", "+s+w", "//w5", etc.) will be too short after cleaning.
         * Get some array of results, big enough to contain at least one such combination of characters.
         */
        for ($i = 0; $i < 1024; $i++) {
            $tokens[] = $tokenGenerator->getAlphanumericToken($tokenLength);
        }

        $arrayOfTokenLengths = array_map('strlen', $tokens);
        $lengthOfTheShortestToken = min($arrayOfTokenLengths);
        $this->assertEquals($tokenLength, $lengthOfTheShortestToken);
    }

    public function testGetAlphanumericTokenWillReturnUniqueValues(): void
    {
        $tokens = [];
        $tokenLength = 3;
        $tokenGenerator = new RandomTokenGenerator();
        $iterations = 3;

        for ($i = 0; $i < $iterations; $i++) {
            $tokens[] = $tokenGenerator->getAlphanumericToken($tokenLength);
        }

        $numberOfTokens = count($tokens);
        $uniqueTokens = array_unique($tokens);
        $this->assertCount($numberOfTokens, $uniqueTokens);
    }

    public function testGetHexTokenWillReturnExpectedType(): void
    {
        $token = (new RandomTokenGenerator())->getHexToken(32);

        $this->assertTrue(ctype_xdigit($token));
    }

    public function testGetHexTokenWithShortTokenWillReturnExpectedLength(): void
    {
        $length = 1;
        $token = (new RandomTokenGenerator())->getHexToken($length);

        $this->assertEquals($length, strlen($token));
    }

    public function testGetHexTokenWithLongTokensWillReturnExpectedLength(): void
    {
        $length = 1024;
        $token = (new RandomTokenGenerator())->getHexToken($length);

        $this->assertEquals($length, strlen($token));
    }

    public function testGetHexTokenWillReturnUniqueValues(): void
    {
        $tokens = [];
        $tokenLength = 3;
        $tokenGenerator = new RandomTokenGenerator();
        $iterations = 3;

        for ($i = 0; $i < $iterations; $i++) {
            $tokens[] = $tokenGenerator->getHexToken($tokenLength);
        }

        $numberOfTokens = count($tokens);
        $uniqueTokens = array_unique($tokens);
        $this->assertCount($numberOfTokens, $uniqueTokens);
    }
}
