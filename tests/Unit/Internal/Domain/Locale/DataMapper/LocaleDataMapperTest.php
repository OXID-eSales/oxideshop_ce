<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Locale\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataMapper\LocaleDataMapper;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use PHPUnit\Framework\TestCase;

final class LocaleDataMapperTest extends TestCase
{
    public function testToData(): void
    {
        $locale = new Locale(code: 'de_DE', name: 'Deutsch', fallbackCode: 'en_GB');

        $data = (new LocaleDataMapper())->toData($locale);

        $this->assertSame('de_DE', $data['code']);
        $this->assertSame('Deutsch', $data['name']);
        $this->assertSame('en_GB', $data['fallback']);
    }

    public function testFromData(): void
    {
        $locale = (new LocaleDataMapper())->fromData([
            'code' => 'en_GB',
            'name' => 'English',
            'fallback'   => 'de_DE',
        ]);

        $this->assertSame('en_GB', $locale->getCode());
        $this->assertSame('English', $locale->getName());
        $this->assertSame('de_DE', $locale->getFallbackCode());
    }
}
