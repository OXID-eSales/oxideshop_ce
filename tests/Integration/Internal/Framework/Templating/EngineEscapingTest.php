<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Templating;

use OxidEsales\Eshop\Core\Field;
use PHPUnit\Framework\TestCase;

final class EngineEscapingTest extends TestCase
{
    public function testFieldValuesWithConfigurationParameterWillBeEscaped(): void
    {
        $stringWithSpecialCharacters = '&';
        $stringWithSpecialCharactersHtmlEncoded = '&amp;';

        $field = new Field($stringWithSpecialCharacters);

        $this->assertEquals($stringWithSpecialCharactersHtmlEncoded, $field->value);
    }
}
