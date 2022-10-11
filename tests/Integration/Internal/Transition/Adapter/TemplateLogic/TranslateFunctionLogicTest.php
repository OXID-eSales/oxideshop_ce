<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFunctionLogic;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class TranslateFunctionLogicTest extends TestCase
{
    use ContainerTrait;

    private TranslateFunctionLogic $translateFunction;

    public function dataProvider(): array
    {
        return [
            [[], 'ERROR: Translation for IDENT MISSING not found!'],
            [['ident' => 'foobar'], 'ERROR: Translation for foobar not found!'],
            [['ident' => 'FIRST_NAME', 'suffix' => ''], 'Vorname'],
            [['ident' => 'FIRST_NAME', 'suffix' => '_foo'], 'ERROR: Translation for FIRST_NAME_foo not found!'],
            [['ident' => 'FIRST_NAME', 'suffix' => 'LAST_NAME'], 'VornameNachname'],
            [['ident' => 'foo', 'noerror' => true], 'foo'],
            [['ident' => 'foo', 'noerror' => 'bar'], 'foo']
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetTranslation(array $params, string $expectedTranslation): void
    {
        $translation = $this->get(TranslateFunctionLogic::class)->getTranslation($params);

        $this->assertEquals($expectedTranslation, $translation);
    }
}
