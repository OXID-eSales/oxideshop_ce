<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxBasketContentMarkGenerator;

class BasketcontentmarkgeneratorTest extends \PHPUnit\Framework\TestCase
{
    public function providerGetExplanationMarks(): \Iterator
    {
        $aResultDownloadable = ['skippedDiscount' => null, 'downloadable'    => '**', 'intangible'      => null];

        $aResultIntangible = ['skippedDiscount' => null, 'downloadable'    => null, 'intangible'      => '**'];

        $aResultSkippedDiscount = ['skippedDiscount' => '**', 'downloadable'    => null, 'intangible'      => null];

        $aResultDownloadableAndIntangible = ['skippedDiscount' => null, 'downloadable'    => '**', 'intangible'      => '***'];

        $aResultDownloadableIntangibleAndSkippedDiscount = ['skippedDiscount' => '**', 'downloadable'    => '***', 'intangible'      => '****'];

        $ResultEmptyArray = ['skippedDiscount'   => null, 'downloadable'      => null, 'intangible'        => null, 'thisDoesNotExists' => null];
        yield [false, true, false, $aResultDownloadable];
        yield [true, false, false, $aResultIntangible];
        yield [false, false, true, $aResultSkippedDiscount];
        yield [true, true, false, $aResultDownloadableAndIntangible];
        yield [true, true, true, $aResultDownloadableIntangibleAndSkippedDiscount];
        yield [false, false, false, $ResultEmptyArray];
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
        $oBasket->method('hasArticlesWithIntangibleAgreement')->willReturn($blIsIntangible);
        $oBasket->method('hasArticlesWithDownloadableAgreement')->willReturn($blIsDownloadable);
        $oBasket->method('hasSkipedDiscount')->willReturn($blHasSkippedDiscounts);

        $oExplanationMarks = new oxBasketContentMarkGenerator($oBasket);

        foreach ($aResult as $sMarkName => $sMark) {
            $this->assertSame($sMark, $oExplanationMarks->getMark($sMarkName));
        }
    }
}
