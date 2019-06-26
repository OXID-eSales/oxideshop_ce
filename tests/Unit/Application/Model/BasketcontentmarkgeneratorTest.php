<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxBasketContentMarkGenerator;

class BasketcontentmarkgeneratorTest extends \OxidTestCase
{
    public function providerGetExplanationMarks()
    {
        $aResultDownloadable = array(
            'skippedDiscount' => null,
            'downloadable'    => '**',
            'intangible'      => null
        );

        $aResultIntangible = array(
            'skippedDiscount' => null,
            'downloadable'    => null,
            'intangible'      => '**',
        );

        $aResultSkippedDiscount = array(
            'skippedDiscount' => '**',
            'downloadable'    => null,
            'intangible'      => null,
        );

        $aResultDownloadableAndIntangible = array(
            'skippedDiscount' => null,
            'downloadable'    => '**',
            'intangible'      => '***'
        );

        $aResultDownloadableIntangibleAndSkippedDiscount = array(
            'skippedDiscount' => '**',
            'downloadable'    => '***',
            'intangible'      => '****'
        );

        $ResultEmptyArray = array(
            'skippedDiscount'   => null,
            'downloadable'      => null,
            'intangible'        => null,
            'thisDoesNotExists' => null
        );

        return array(
            array(false, true, false, $aResultDownloadable),
            array(true, false, false, $aResultIntangible),
            array(false, false, true, $aResultSkippedDiscount),
            array(true, true, false, $aResultDownloadableAndIntangible),
            array(true, true, true, $aResultDownloadableIntangibleAndSkippedDiscount),
            array(false, false, false, $ResultEmptyArray),
        );
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
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\Basket::class, array('hasArticlesWithIntangibleAgreement', 'hasArticlesWithDownloadableAgreement', 'hasSkipedDiscount'));
        $oBasket->expects($this->any())->method('hasArticlesWithIntangibleAgreement')->will($this->returnValue($blIsIntangible));
        $oBasket->expects($this->any())->method('hasArticlesWithDownloadableAgreement')->will($this->returnValue($blIsDownloadable));
        $oBasket->expects($this->any())->method('hasSkipedDiscount')->will($this->returnValue($blHasSkippedDiscounts));

        $oExplanationMarks = new oxBasketContentMarkGenerator($oBasket);

        foreach ($aResult as $sMarkName => $sMark) {
            $this->assertSame($sMark, $oExplanationMarks->getMark($sMarkName));
        }
    }
}
