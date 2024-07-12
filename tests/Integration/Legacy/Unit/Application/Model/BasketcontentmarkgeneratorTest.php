<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxBasketContentMarkGenerator;

class BasketcontentmarkgeneratorTest extends \PHPUnit\Framework\TestCase
{
    public function providerGetExplanationMarks()
    {
        $aResultDownloadable = ['skippedDiscount' => null, 'downloadable'    => '**', 'intangible'      => null];

        $aResultIntangible = ['skippedDiscount' => null, 'downloadable'    => null, 'intangible'      => '**'];

        $aResultSkippedDiscount = ['skippedDiscount' => '**', 'downloadable'    => null, 'intangible'      => null];

        $aResultDownloadableAndIntangible = ['skippedDiscount' => null, 'downloadable'    => '**', 'intangible'      => '***'];

        $aResultDownloadableIntangibleAndSkippedDiscount = ['skippedDiscount' => '**', 'downloadable'    => '***', 'intangible'      => '****'];

        $ResultEmptyArray = ['skippedDiscount'   => null, 'downloadable'      => null, 'intangible'        => null, 'thisDoesNotExists' => null];

        return [[false, true, false, $aResultDownloadable], [true, false, false, $aResultIntangible], [false, false, true, $aResultSkippedDiscount], [true, true, false, $aResultDownloadableAndIntangible], [true, true, true, $aResultDownloadableIntangibleAndSkippedDiscount], [false, false, false, $ResultEmptyArray]];
    }

    /**
     * @param $blIsIntangible
     * @param $blIsDownloadable
     * @param $blHasSkippedDiscounts
     * @param $aResult
     *
     * @dataProvider providerGetExplanationMarks
     */
    public function testGetExplanationMarks($blIsIntangible, $blIsDownloadable, $blHasSkippedDiscounts, $aResult)
    {
        /** @var oxBasket $oBasket */
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, ['hasArticlesWithIntangibleAgreement', 'hasArticlesWithDownloadableAgreement', 'hasSkipedDiscount']);
        $oBasket->expects($this->any())->method('hasArticlesWithIntangibleAgreement')->will($this->returnValue($blIsIntangible));
        $oBasket->expects($this->any())->method('hasArticlesWithDownloadableAgreement')->will($this->returnValue($blIsDownloadable));
        $oBasket->expects($this->any())->method('hasSkipedDiscount')->will($this->returnValue($blHasSkippedDiscounts));

        $oExplanationMarks = new oxBasketContentMarkGenerator($oBasket);

        foreach ($aResult as $sMarkName => $sMark) {
            $this->assertSame($sMark, $oExplanationMarks->getMark($sMarkName));
        }
    }
}
