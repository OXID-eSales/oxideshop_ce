<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFunctionLogic;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TranslateFunctionLogicTest extends TestCase
{
    use ContainerTrait;

    private TranslateFunctionLogic $translateFunction;

    public function setUp(): void
    {
        $this->get(Language::class)->setTplLanguage(0);
        parent::setUp();
    }

    public static function dataProvider(): array
    {
        return [
            [[], 'ERROR: Translation for IDENT MISSING not found!'],
            [['ident' => 'foobar'], 'ERROR: Translation for foobar not found!'],
            [['ident' => 'FIRST_NAME', 'suffix' => ''], 'Vorname'],
            [['ident' => 'FIRST_NAME', 'suffix' => '_foo'], 'ERROR: Translation for FIRST_NAME_foo not found!'],
            [['ident' => 'FIRST_NAME', 'suffix' => 'LAST_NAME'], 'VornameNachname'],
            [['ident' => 'foo', 'noerror' => true], 'foo'],
            [['ident' => 'foo', 'noerror' => 'bar'], 'foo'],
            [['ident' => 'VAT_PLUS_PERCENT_AMOUNT', 'args' => 0], 'zzgl. 0% MwSt., Betrag'],
            [['ident' => 'VAT_PLUS_PERCENT_AMOUNT', 'args' => ''], 'zzgl. % MwSt., Betrag'],
            [['ident' => 'VAT_PLUS_PERCENT_AMOUNT'], 'zzgl. %s%% MwSt., Betrag'],
            [['ident' => 'VAT_PLUS_PERCENT_AMOUNT', 'args' => false], 'zzgl. %s%% MwSt., Betrag']
        ];
    }

    #[DataProvider('dataProvider')]
    public function testGetTranslation(array $params, string $expectedTranslation): void
    {
        $translation = $this->get(TranslateFunctionLogic::class)->getTranslation($params);

        $this->assertEquals($expectedTranslation, $translation);
    }
}
