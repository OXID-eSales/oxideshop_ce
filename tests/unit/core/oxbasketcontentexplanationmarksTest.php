<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';


class Unit_Core_oxbasketcontentexplanationmarksTest extends OxidTestCase
{
    public function providerGetExplanationMarks()
    {
        $aResultDownloadable = array(
            'getForArticlesWithSkippedDiscount' => null,
            'getForDownloadableArticles' => '**',
            'getForIntangibleArticles' => null,
        );

        $aResultIntangible = array(
            'getForArticlesWithSkippedDiscount' => null,
            'getForDownloadableArticles' => null,
            'getForIntangibleArticles' => '**',
        );

        $aResultSkippedDiscount = array(
            'getForArticlesWithSkippedDiscount' => '**',
            'getForDownloadableArticles' => null,
            'getForIntangibleArticles' => null,
        );

        $aResultDownloadableAndIntangible = array(
            'getForArticlesWithSkippedDiscount' => null,
            'getForDownloadableArticles' => '**',
            'getForIntangibleArticles' => '***'
        );

        $aResultDownloadableIntangibleAndSkippedDiscount = array(
            'getForArticlesWithSkippedDiscount' => '**',
            'getForDownloadableArticles' => '***',
            'getForIntangibleArticles' => '****'
        );

        return array(
            array(false, true, false, $aResultDownloadable),
            array(true, false, false, $aResultIntangible),
            array(false, false, true, $aResultSkippedDiscount),
            array(true, true, false, $aResultDownloadableAndIntangible),
            array(true, true, true, $aResultDownloadableIntangibleAndSkippedDiscount),
            array(false, false, false, array()),
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
    public function testGetForArticlesWithSkippedDiscount($blIsIntangible, $blIsDownloadable, $blHasSkippedDiscounts, $aResult)
    {
        /** @var oxBasket $oBasket */
        $oBasket = $this->getMock('oxBasket', array('hasArticlesWithIntangibleAgreement', 'hasArticlesWithDownloadableAgreement', 'hasSkipedDiscount'));
        $oBasket->expects($this->any())->method('hasArticlesWithIntangibleAgreement')->will($this->returnValue($blIsIntangible));
        $oBasket->expects($this->any())->method('hasArticlesWithDownloadableAgreement')->will($this->returnValue($blIsDownloadable));
        $oBasket->expects($this->any())->method('hasSkipedDiscount')->will($this->returnValue($blHasSkippedDiscounts));

        /** @var oxBasketContentExplanationMarks $oExplanationMarks */
        $oExplanationMarks = new oxBasketContentExplanationMarks($oBasket);

        $this->assertSame($aResult['getForArticlesWithSkippedDiscount'], $oExplanationMarks->getForArticlesWithSkippedDiscount());
        $this->assertSame($aResult['getForDownloadableArticles'], $oExplanationMarks->getForDownloadableArticles());
        $this->assertSame($aResult['getForIntangibleArticles'], $oExplanationMarks->getForIntangibleArticles());
    }
}