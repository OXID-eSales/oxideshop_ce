<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup\Language;

use OxidEsales\EshopCommunity\Internal\Setup\Language\DefaultLanguage;
use OxidEsales\EshopCommunity\Internal\Setup\Language\IncorrectLanguageException;
use PHPUnit\Framework\TestCase;

final class DefaultLanguageTest extends TestCase
{
    public function testReturnsCorrectCode(): void
    {
        $language = new DefaultLanguage('de');

        $this->assertEquals(0, $language->getCode());
    }

    public function testThrowsExceptionOnIncorrectLanguage(): void
    {
        $this->expectException(IncorrectLanguageException::class);

        new DefaultLanguage('esperanto');
    }
}
