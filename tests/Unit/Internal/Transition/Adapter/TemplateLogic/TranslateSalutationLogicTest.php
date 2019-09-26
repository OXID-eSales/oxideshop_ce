<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateSalutationLogic;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Class TranslateSalutationLogic
 */
class TranslateSalutationLogicTest extends UnitTestCase
{

    /** @var TranslateSalutationLogic */
    private $translateSalutationLogic;

    protected function setUp(): void
    {
        $this->translateSalutationLogic = new TranslateSalutationLogic();
    }

    /**
     * Provides data for testTranslateSalutation
     *
     * @return array
     */
    public function translateSalutationProvider(): array
    {
        return [
            ['MR', 0, 'Herr'],
            ['MRS', 0, 'Frau'],
            ['MR', 1, 'Mr'],
            ['MRS', 1, 'Mrs']
        ];
    }

    /**
     * @param string $ident
     * @param int    $languageId
     * @param string $expected
     *
     * @dataProvider translateSalutationProvider
     */
    public function testTranslateSalutation(string $ident, int $languageId, string $expected): void
    {
        $this->setLanguage($languageId);
        $this->assertEquals($expected, $this->translateSalutationLogic->translateSalutation($ident));
    }
}