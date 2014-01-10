<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Maintenance_priceCalculationTest extends OxidTestCase
{
    private static $_aErrors = array();

    final public function setup()
    {
        parent::setUp();
        self::$_aErrors = array();
    }

    private function getErrorLocation($oError)
    {
        $ar = array();
        $i = 0;
        foreach ($oError->getTrace() as $frame) {
            if (!isset($frame['line'])) {
                break;
            }
            $ar[$i++] = array(
              'file' => $frame['file'],
              'line' => $frame['line']
            );
        }
        $out = '';
        foreach (array_slice($ar, -5) as $frame) {
            $out .= $frame['file'].":".$frame['line']."\n";
        }
        return $out;
    }

    final public function assertErrors()
    {
        if ( count( self::$_aErrors ) ) {
            $sFailure = '';
            foreach ( self::$_aErrors as $oError ) {
                $sFailure .= PHPUnit_Framework_TestFailure::exceptionToString( $oError ) . "\n";
                $sFailure .= $this->getErrorLocation( $oError ) . "\n";
            }
            $this->fail($sFailure);
        }
    }

    protected function checkEquals( $expected, $actual, $message = '', $delta = 0, $maxDepth = 10 )
    {
        try {
            $this->assertEquals($expected, $actual, $message, $delta, $maxDepth );
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            self::$_aErrors[] = $e;
        }
    }


    protected function runTests()
    {
    }

    public function testArticleBasePrices()
    {
        $oTest = new testArticleBasePrices();
        $oTest->runTests();
        $this->assertErrors();
    }
    public function testArticlePrices()
    {
        $oTest = new testArticlePrices();
        $oTest->runTests();
        $this->assertErrors();
    }
    public function testBasketPrices()
    {
        //$this->markTestIncomplete('Test impossible, incorrect CSV data file.');
        $oTest = new testBasketPrices();
        $oTest->runTests();
        $this->assertErrors();
    }

    public function testAdvBasketPrices()
    {
        $oTest = new testAdvBasketPrices();
        $oTest->runTests();
        $this->assertErrors();
    }
}


class testArticleBasePrices extends Unit_Maintenance_priceCalculationTest
{
    protected function runTests()
    {

        $blAnyTestRunned = false;
        $sFName = getTestsBasePath().'/unit/maintenance/priceCalc/articleBasePrice.csv';
        $hFile = fopen($sFName, "r");
        while (($data = fgetcsv($hFile, 1000, "\011")) !== false) {
            if (!is_numeric(trim($data[0]))) {
                continue;
            }
            $data[1] = str_replace(',', '.', $data[1]);
            if (!is_numeric(trim($data[1]))) {
                continue;
            }
            $aData = array('oxid'=>$data[0], 'oxprice'=>str_replace(',', '.', $data[1]), 'oxpricea'=>str_replace(',', '.', $data[2]), 'oxpriceb'=>str_replace(',', '.', $data[3]), 'oxpricec'=>str_replace(',', '.', $data[4]));
            switch ($data[5]) {
                case "A":
                    $sGroup = 'oxidpricea';
                    break;
                case "B":
                    $sGroup = 'oxidpriceb';
                    break;
                case "C":
                    $sGroup = 'oxidpricec';
                    break;
                default:
                    $sGroup = 'nooxidpricegroup';
                    break;
            }
            $oUser = $this->getMock('oxuser', array('inGroup'));
            $oUser->expects( $this->any() )->method( 'inGroup')->will( $this->evalFunction( create_function('$g', "return  (\$g == '$sGroup'); ") ) );

            $oArticle = $this->getMock('oxarticle', array('getUser'));
            $oArticle->expects( $this->any() )->method( 'getUser')->will( $this->returnValue( $oUser ) );
            $oArticle->disableLazyLoading();
            $oArticle->assign($aData);

            $this->_runSingleTest($oArticle, str_replace(',', '.', $data[8]));
            $this->_aArticleData[] = $oArticle;
            $this->_aExpectedData[] = str_replace(',', '.', $data[8]);
            $blAnyTestRunned = true;

        }
        fclose($hFile);

        if (!$blAnyTestRunned) {
            $this->fail('No tests had been run.');
        }
    }


    private function _runSingleTest($oArticle, $dExpected)
    {
        $blParam = oxConfig::getInstance()->getConfigParam( 'blOverrideZeroABCPrices' ) ;
        oxConfig::getInstance()->setConfigParam( 'blOverrideZeroABCPrices', 1) ;

        $this->checkEquals($dExpected, $oArticle->getBasePrice(), "from article: ".$oArticle->getId().", if expected: $dExpected");

        oxConfig::getInstance()->setConfigParam( 'blOverrideZeroABCPrices', $blParam) ;
    }

}


class testArticlePrices extends Unit_Maintenance_priceCalculationTest
{
    protected function _getCurencyObject($dRate)
    {
        $oCur = new oxStdClass();
        $oCur->id      = "ocurid";
        $oCur->name     = "aaa";
        $oCur->rate     = $dRate;
        $oCur->dec      = "2";
        $oCur->thousand = "3";
        $oCur->sign     = "A";
        $oCur->decimal  = "2";
        return $oCur;
    }

    protected function runTests()
    {
        $blEnterNetPrice = oxConfig::getInstance()->getConfigParam( 'blEnterNetPrice' );

        $sZeroVatCountry = oxDb::getDb()->getOne('select oxid from oxcountry where oxvatstatus = 0');
        $sNonZeroVatCountry = oxDb::getDb()->getOne('select oxid from oxcountry where oxvatstatus = 1');

        $blAnyTestRunned = false;
        $sFName = getTestsBasePath().'/unit/maintenance/priceCalc/articlePrice.csv';
        $hFile = fopen($sFName, "r");
        while (($data = fgetcsv($hFile, 1000, "\011")) !== false) {
            if (!is_numeric(trim($data[0]))) {
                continue;
            }
            $data[1] = str_replace(',', '.', $data[1]);
            if (!is_numeric($data[1])) {
                continue;
            }
            $aData = array('oxid'=>$data[0], 'oxprice'=>str_replace(',', '.', $data[1]), 'oxvat'=>str_replace(',', '.', $data[3]));

            $oUser = new  oxUser();
            $oUser->setId('test_'.$data[0].rand(0, 1000).time());

            if (strlen(trim($data[4])) && is_numeric(trim($data[4]))) {
                if (trim($data[4]) !== 0) {
                    continue; // we do not support foreign vats, just 0 or false
                }
                $oUser->oxuser__oxcountryid = new oxField($sZeroVatCountry, oxField::T_RAW);
            } else {
                $oUser->oxuser__oxcountryid = new oxField($sNonZeroVatCountry, oxField::T_RAW);
            }

            $aEnv = array('discount'=>trim(str_replace(',', '.', $data[2])), 'enternetprice'=>$data[5], 'currate'=>trim(str_replace(',', '.', $data[7])));

            oxConfig::getInstance()->setConfigParam( 'blEnterNetPrice', $aEnv['enternetprice'] );

            $oConfig = $this->getMock('oxconfig', array('getActShopCurrencyObject'));
            $oConfig->expects( $this->any() )->method( 'getActShopCurrencyObject')->will( $this->returnValue( $this->_getCurencyObject($aEnv['currate']) ) );
            $oConfig->setConfigParam( 'blEnterNetPrice', $aEnv['enternetprice'] );
            $oConfig->setConfigParam( 'bl_perfLoadPrice', true );
            $oArticle = $this->getMock('oxarticle', array('getUser'));
            $oArticle->expects( $this->any() )->method( 'getUser')->will( $this->returnValue( $oUser ) );
            $oArticle->setConfig($oConfig);

            $oArticle->disableLazyLoading();

            $this->_setArticleDiscount($data[0], $aEnv['discount']);

            $oArticle->assign($aData);

            $this->_runSingleTest($oArticle, array(str_replace(',', '.', $data[8]), str_replace(',', '.', $data[9]), str_replace(',', '.', $data[10])));
            $blAnyTestRunned = true;
        }
        fclose($hFile);
        oxConfig::getInstance()->setConfigParam( 'blEnterNetPrice', $blEnterNetPrice );

        if (!$blAnyTestRunned) {
            $this->fail('No tests had been run.');
        }
    }

    protected function _setArticleDiscount($sId, $dDiscount)
    {
        if ( $dDiscount) {
            oxTestModules::addFunction('oxdiscountlist', 'getArticleDiscounts', "{
                \$o = \$aA[0];
                if ( \$o->getId() == '$sId') {
                    \$oDiscount = new oxDiscount();
                    \$oDiscount->setConfig(\$o->getConfig());
                    \$oDiscount->oxdiscount__oxaddsumtype = new oxField('abs');
                    \$oDiscount->oxdiscount__oxaddsum = new oxField($dDiscount);
                    return array('disc_$sId'=>\$oDiscount);
                }
                return array();
            }");
        } else {
            oxTestModules::addFunction('oxdiscountlist', 'getArticleDiscounts', "{ return array(); }");
        }
    }


    private function _runSingleTest($oArticle, $aExpectedData)
    {
        $blEnterNetPrice = oxConfig::getInstance()->getConfigParam( 'blEnterNetPrice' );

        // article prices are loaded and saved, now just take from cache.
        // TODO: move to one fnc
        $this->checkEquals($aExpectedData[0], oxUtils::getInstance()->fRound($oArticle->getPrice()->getBruttoPrice()));
        $this->checkEquals($aExpectedData[1], oxUtils::getInstance()->fRound($oArticle->getPrice()->getNettoPrice()));
        $this->checkEquals($aExpectedData[2], oxUtils::getInstance()->fRound($oArticle->getPrice()->getVatValue()));

        modConfig::getInstance()->cleanup();
        oxConfig::getInstance()->setConfigParam( 'blEnterNetPrice', $blEnterNetPrice );
        oxTestModules::cleanUp();

    }

}

// simple order caclulation test
class testBasketPrices extends Unit_Maintenance_priceCalculationTest
{

    protected function _getCurencyObject($dRate)
    {
        $oCur = new oxStdClass();
        $oCur->id      = "ocurid";
        $oCur->name     = "aaa";
        $oCur->rate     = $dRate;
        $oCur->dec      = "2";
        $oCur->thousand = "3";
        $oCur->sign     = "A";
        $oCur->decimal  = "2";
        return $oCur;
    }

    private function _loadArticleData($type, $data)
    {
        $data[1] = str_replace(',', '.', $data[1]);
        if (!is_numeric($data[1])) {
            continue;
        }

        $aData = array('oxid'=>$data[1], 'oxprice'=>str_replace(',', '.', $data[3]), 'oxvat'=>str_replace(',', '.', $data[4]));

        $oUser = new  oxUser();
        $oUser->setId('test_'.$data[0].rand(0, 1000).time());

        //$aEnv = array('discount'=>trim(str_replace(',', '.', $data[7])), 'enternetprice'=>0, 'currate'=>trim(str_replace(',', '.', $data[2])));

        $oConfig = $this->getMock('oxconfig', array('getActShopCurrencyObject'));
        $oConfig->expects($this->any())->method( 'getActShopCurrencyObject')->will( $this->returnValue( $this->_getCurencyObject(trim(str_replace(',', '.', $data[2])))));
        $oConfig->setConfigParam( 'bl_perfLoadPrice', true );

        /**
        $oArticle = $this->getMock('oxarticle', array('getUser'));
        $oArticle->expects( $this->any() )->method( 'getUser')->will( $this->returnValue( $oUser ) );
        $oArticle->setConfig($oConfig);
        **/
        $oArticle = oxNew('oxArticle');

        $oArticle->disableLazyLoading();

        $oArticle->assign($aData);
        return $oArticle;
    }

    protected function runTests()
    {
        $iOrderType = 0;
        $sOrderID = '';

        $blAnyTestRunned = false;
        $sFName = getTestsBasePath().'/unit/maintenance/priceCalc/basketCalc.csv';
        $hFile = fopen($sFName, "r");
        while (($data = fgetcsv($hFile, 1000, "\011")) !== false) {

            if (trim($data[0]) == 'Simple order calculation') {
                $iOrderType = 1;
            } elseif (trim($data[0]) == 'Complex order calculation') {
                $iOrderType = 2;
            }

            if (!$iOrderType) {
                continue;
            }
                modConfig::getInstance()->setConfigParam( 'bl_perfLoadPrice', true );
                modConfig::getInstance()->setConfigParam( 'blUseStock', false );

                // netto prices ?
                   modConfig::getInstance()->setConfigParam( 'blEnterNetPrice', false );

                // VAT for wrapping ?
                    modConfig::getInstance()->setConfigParam( 'blCalcVatForWrapping', true );

                // VAT for delivery ?
                    modConfig::getInstance()->setConfigParam( 'blCalcVATForDelivery', true );

                // currency rate ?
                if ( strlen(trim( $data[2] )) ) {
                     $aCurr = array( "EUR@{$data[2]}@ ,@ .@EUR@ 2" );
                     modConfig::getInstance()->setConfigParam( 'modaCurrencies', $aCurr );
                }

            if (!$sOrderID && preg_match('/ order$/i', $data[0])) {
                // starting order definition with order id
                $sOrderID = $data[0];
                $aData = array();
            } else {
                $blAllEmpty = true;
                foreach ($data as $val) {
                    $blAllEmpty &= empty($val);
                }
                if ($blAllEmpty) {
                    // ending order definition by empty row
                    $this->_runSingleTest($aData, $iOrderType, $sOrderID);
                    $sOrderID = '';
                    $blAnyTestRunned = true;
                }
            }
            if (!$sOrderID) {
                continue;
            }

            if (is_numeric(trim($data[1]))) {

                // article id
                $oArt = $this->_loadArticleData($iOrderType, $data);
                $aData['articles'][] = $oArt;
                $aData['output']['artnet'][$oArt->getId()] = trim(str_replace(',', '.', $data[5]));
                $aData['env']['artcnt'][$oArt->getId()] = trim(str_replace(',', '.', $data[6]));
                if ($iOrderType == 1) {
                    $aData['env']['disc'][$oArt->getId()] = 0;
                    $aData['env']['wrap'][$oArt->getId()] = 0;
                    $aData['output']['totalbrutto'][$oArt->getId()] = trim(str_replace(',', '.', $data[12]));
                    $aData['output']['totalnetto'][$oArt->getId()] = trim(str_replace(',', '.', $data[13]));

                } elseif ($iOrderType == 2) {
                    $aData['env']['disc'][$oArt->getId()] =  trim(str_replace(',', '.', $data[7])); // /  trim(str_replace(',', '.', $data[6]));
                    $aData['env']['wrap'][$oArt->getId()] =  trim(str_replace(',', '.', $data[9])); // /  trim(str_replace(',', '.', $data[6]));
                    $aData['output']['totalbrutto'][$oArt->getId()] = trim(str_replace(',', '.', $data[16]));
                    $aData['output']['totalnetto'][$oArt->getId()] = trim(str_replace(',', '.', $data[17]));
                }

            }

            if ($iOrderType == 1) {
                $prodsTotalBrutto   = $dBasketTotalBrutto   =  trim(str_replace(',', '.', $data[8]));
                $prodsTotalNetto    = $dBasketTotalNetto    =  trim(str_replace(',', '.', $data[9]));
                $dBasketTotalNettoAbs =  trim(str_replace(',', '.', $data[10]));
                $payment = $deliv = $delivVat = 0;
            } elseif ($iOrderType == 2) {
                $prodsTotalBrutto =    trim(str_replace(',', '.', $data[16]));
                $prodsTotalNetto =     trim(str_replace(',', '.', $data[17]));
                $dBasketTotalBrutto =    trim(str_replace(',', '.', $data[13]));
                $dBasketTotalNetto =     trim(str_replace(',', '.', $data[14]));
                $dBasketTotalNettoAbs =  trim(str_replace(',', '.', $data[15]));
                $deliv    =  trim(str_replace(',', '.', $data[11]));
                $delivVat =  trim(str_replace(',', '.', $data[10]));
                $payment =  trim(str_replace(',', '.', $data[12]));
                $dTotalWrapBrutto = trim(str_replace(',', '.', $data[9]));
            }

            if (is_numeric($dBasketTotalBrutto) && is_numeric($dBasketTotalNetto) && is_numeric($dBasketTotalNettoAbs) && is_numeric($prodsTotalBrutto) && is_numeric($prodsTotalNetto)) {
                // order total values
                $aData['output']['prodstotalbrutto'] = $prodsTotalBrutto;
                $aData['output']['prodstotalnetto'] =  $prodsTotalNetto;
                $aData['output']['baskettotalbrutto'] = $dBasketTotalBrutto;
                $aData['output']['baskettotalnetto'] =  $dBasketTotalNetto;
//                 $aData['output']['baskettotalnettoabs'] = trim(str_replace(',', '.', $data[10]));
            }

            if (is_numeric($delivVat) && is_numeric($delivVat)) {
                $aData['env']['deliv'] = $deliv;
                $aData['env']['delivat'] = $delivVat;
            }
            if (is_numeric($payment)) {
                $aData['env']['payment'] = $payment;
            }

            if ($dBasketTotalBrutto && $dTotalWrapBrutto) {
                $aData['output']['wraptotalbrutto'] = $dTotalWrapBrutto;
            }
        }
        if ($sOrderID && $iOrderType && is_array($aData)) {
            // flush order checking
            $this->_runSingleTest($aData, $iOrderType, $sOrderID);
            $blAnyTestRunned = true;
        }
        fclose($hFile);

        if (!$blAnyTestRunned) {
            $this->fail('No tests had been run.');
        }
    }

    protected function _setArticleDiscount($sId, $dDiscount)
    {
        if ( $dDiscount) {
            $sDiscount = "
                    \$oDiscount = new oxDiscount();
                    \$oDiscount->setConfig(\$o->getConfig());
                    \$oDiscount->oxdiscount__oxtitle = new oxField('Article discount');
                    \$oDiscount->oxdiscount__oxaddsumtype = new oxField('abs');
                    \$oDiscount->oxdiscount__oxaddsum = new oxField($dDiscount);
                    return array('disc_$sId'=>\$oDiscount);
                    ";
        } else {
            $sDiscount = 'return array();';
        }

        oxTestModules::addFunction('oxdiscountlist', 'getArticleDiscounts', "{
                \$o = \$aA[0];
                if ( \$o->getId() == '$sId') {
                    $sDiscount;
                }
                return parent::getArticleDiscounts(\$aA[0], \$aA[1]);
            }");
    }

    protected function _setOrderItemDiscount($sId, $dDiscount)
    {
        if ( $dDiscount) {
            $sDiscount = "
                    \$oDiscount = new oxDiscount();
                    \$oDiscount->setConfig(\$o->getConfig());
                    \$oDiscount->oxdiscount__oxtitle = new oxField('Article order item discount');
                    \$oDiscount->oxdiscount__oxaddsumtype = new oxField('abs');
                    \$oDiscount->oxdiscount__oxaddsum = new oxField($dDiscount);
                    return array('disc_$sId'=>\$oDiscount);
                    ";
        } else {
            $sDiscount = 'return array();';
        }

        oxTestModules::addFunction('oxdiscountlist', 'getBasketItemDiscounts', "{
                \$o = \$aA[0];
                if ( \$o->getId() == '$sId') {
                    $sDiscount;
                }
                return parent::getBasketItemDiscounts(\$aA[0], \$aA[1], \$aA[2] );
            }");
    }

    public static $currentArt = null;

    protected function _runSingleTest($aData, $iOrderType, $sOrderID)
    {
        $myUtils = oxUtils::getInstance();
        oxTestModules::addFunction('oxbasketitem', 'getArticle', '{if (!$this->_oArticle) $this->_oArticle = '.__CLASS__.'::$currentArt; return $this->_oArticle; }');
        oxTestModules::addFunction('oxbasketitem', '_setArticle', '{$this->getArticle();}');

        oxTestModules::addFunction('oxbasket', '_calcDeliveryCost', '{
            $oP = new oxPrice();
            $oP->setVat('.(double)$aData['env']['delivat'].');
            $oP->setPrice('.(double)$aData['env']['deliv'].');
            return $oP;
        }');

        oxTestModules::addFunction('oxbasket', '_calcPaymentCost', '{
            $oP = new oxPrice();
            $oP->setPrice('.(double)$aData['env']['payment'].');
            return $oP;
        }');

        modConfig::getInstance()->setConfigParam( 'blPerfNoBasketSaving', true );
        modConfig::getInstance()->setConfigParam( 'blAllowUnevenAmounts', true );
        $oBasket = oxNew('oxbasket');
        foreach ($aData['articles'] as $oArticle) {
            self::$currentArt = $oArticle;

            $dCount = $aData['env']['artcnt'][$oArticle->getId()];

            $this->_setArticleDiscount($oArticle->getId(), $aData['env']['disc'][$oArticle->getId()] / $dCount);

            if ($wrapcost = $aData['env']['wrap'][$oArticle->getId()]) {
                $wrapcost = $wrapcost / $dCount;
                oxTestModules::addFunction('oxbasketitem', 'getWrapping', '{$o=new oxwrapping; $o->oxwrapping__oxprice = new oxField('.$wrapcost.', oxField::T_RAW); return $o;}');
            } else {
                oxTestModules::addFunction('oxbasketitem', 'getWrapping', '{return null;}');
            }

            $oBasket->addToBasket( $oArticle->getId(), $dCount);
        }

        $oBasket->calculateBasket();
        foreach ($oBasket->getContents() as $oBasketItem) {
            $sId = $oBasketItem->getArticle()->getId();
            $this->checkEquals((double)$aData['output']['totalbrutto'][$sId], round( $oBasketItem->getPrice()->getBruttoPrice(), 2 ), 'article total brutto ('.$sOrderID.' - '.$iOrderType.')', 0.000001);
            $this->checkEquals((double)$aData['output']['totalnetto'][$sId], round( $oBasketItem->getPrice()->getNettoPrice(), 2 ), 'article total netto ('.$sOrderID.')', 0.000001);
        }

        $this->checkEquals((double)$aData['output']['prodstotalbrutto'], round($oBasket->getProductsPrice()->getBruttoSum(), 2), 'basket products price brutto ('.$sOrderID.' order - '.$iOrderType.')', 0.0000001);
        $this->checkEquals((double)$aData['output']['baskettotalbrutto'], round($oBasket->getPrice()->getBruttoPrice(), 2), 'basket price brutto ('.$sOrderID.' - '.$iOrderType.')', 0.0000001);
        $this->checkEquals((double)$aData['output']['wraptotalbrutto'], $oBasket->getCosts( 'oxwrapping' )->getBruttoPrice(), 'basket total wrapping brutto ('.$sOrderID.' - '.$iOrderType.')', 0.0000001);
        //TODO same as above                    $this->assertEquals((double)$aData['output']['baskettotalnetto'], $oBasket->getProductsPrice()->getNettoSum(), 'basket products price netto', 0.0000001);

        oxTestModules::cleanUp();
    }
}

class testAdvBasketPrices extends Unit_Maintenance_priceCalculationTest
{
    protected function _getCurencyObject($dRate)
    {
        $oCur = new oxStdClass();
        $oCur->id      = "ocurid";
        $oCur->name     = "aaa";
        $oCur->rate     = $dRate;
        $oCur->dec      = "2";
        $oCur->thousand = "3";
        $oCur->sign     = "A";
        $oCur->decimal  = "2";
        return $oCur;
    }

    protected function runTests()
    {
        // initial configuration
        modConfig::getInstance()->setConfigParam( 'bl_perfLoadPrice', true );
        modConfig::getInstance()->setConfigParam( 'blUseStock', false );

        $aData = array();
        $sOrderId = $sNewOrderId = null;
        $blAnyTestRunned = false;

        // collecting data
        $sFName = getTestsBasePath() . '/unit/maintenance/priceCalc/advBasketCalc.csv';
        $hFile = fopen( $sFName, "r" );

        $blContinue = ( $data = fgetcsv( $hFile, 1000, "\011" ) );
        while ( $blContinue !== false ) {

            if ( preg_match( '/ order$/i', trim( $data[0] ) ) ) {
                // starting order definition with order id
                $sNewOrderId = trim( $data[0] );

                // reading first file ?
                if ( !isset( $sOrderId ) ) {
                    $sOrderId = $sNewOrderId;
                }
            }

            if ( $sOrderId ) {

                // netto prices ?
                if ( strlen(trim( $data[4] )) ) {
                   modConfig::getInstance()->setConfigParam( 'blEnterNetPrice', (bool)$data[4] );
                   modConfig::getInstance()->setConfigParam( 'blWrappingVatOnTop', (bool)$data[4] );
                }

                // VAT for wrapping ?
                if ( strlen(trim( $data[20] )) ) {
                    modConfig::getInstance()->setConfigParam( 'blShowVATForWrapping', (bool)$data[20] );
                }

                // VAT for delivery ?
                if ( strlen(trim( $data[24] )) ) {
                    modConfig::getInstance()->setConfigParam( 'blShowVATForDelivery', (bool)$data[24] );
                }

                // currency rate ?
                if ( strlen(trim( $data[2] )) ) {
                     $aCurr = array( "EUR@{$data[2]}@ ,@ .@EUR@ 2" );
                     modConfig::getInstance()->setConfigParam( 'modaCurrencies', $aCurr );
                }

                // article info
                if ( is_numeric( trim( $data[1] ) ) ) {

                    $iId = $data[1];

                    /** PRODUCT DATA **/

                    // base article info
                    $aData['articles'][$iId]['fields']['oxid']    = $iId;
                    $aData['articles'][$iId]['fields']['oxprice'] = (double) trim( str_replace( ',', '.', $data[3] ) );
                    $aData['articles'][$iId]['fields']['oxvat']   = (double) trim( str_replace( ',', '.', $data[5] ) );

                    // article discount
                    $aData['articles'][$iId]['adisctype'] = $blAbsArtDiscount = (bool) $data[6];
                    $aData['articles'][$iId]['adisc']     = (double) trim( str_replace( ',', '.', $blAbsArtDiscount? $data[6] : $data[7] ) );

                    // article price
                    $aData['articles'][$iId]['artbrut'] = (double) trim( str_replace( ',', '.', $data[8] ) );
                    $aData['articles'][$iId]['artnet']  = (double) trim( str_replace( ',', '.', $data[9] ) );

                    /** BASKET ITEM DATA **/

                    // qty
                    $aData['basketitem'][$iId]['cnt'] = (int) trim( str_replace( ',', '.', $data[10] ) );

                    // item discount
                    $aData['basketitem'][$iId]['itmdisctype'] = $blAbsBasketItemDiscount = (bool) $data[11];
                    $aData['basketitem'][$iId]['itmdisc']     = (double) trim(str_replace( ',', '.', $blAbsBasketItemDiscount ? $data[11] : $data[12] ) );

                    // total price
                    $aData['basketitem'][$iId]['totalbrut'] = (double) trim( str_replace( ',', '.', $data[17] ) );
                    $aData['basketitem'][$iId]['totalnet']  = (double) trim( str_replace( ',', '.', $data[18] ) );

                    // wrap prices
                    $aData['basketitem'][$iId]['wrapdb']   = (double) trim( str_replace( ',', '.', $data[19] ) );
                    $aData['basketitem'][$iId]['wrapvat']  = (double) trim( str_replace( ',', '.', $data[20] ) );
                    $aData['basketitem'][$iId]['wrapbrut'] = (double) trim( str_replace( ',', '.', $data[21] ) );
                }

                /** WHOLE BASKET DATA **/

                // basket discount
                if ( trim( $data[13] ) || trim( $data[14] ) ) {
                    $aData['basket']['basketdiscounttype'] = $blAbsBasketDiscount = (bool) trim( $data[13] );
                    $aData['basket']['basketdiscount']     = (double) trim( str_replace( ',', '.', $blAbsBasketDiscount ? $data[13] : $data[14] ) );
                }

                // voucher discount
                if ( trim( $data[15] ) || trim( $data[16] ) ) {
                    $aData['basket']['voucherdiscounttype'] = $blAbsVoucherDiscount = (bool) trim( $data[15] );
                    $aData['basket']['voucherdiscount']     = (double) trim( str_replace( ',', '.', $blAbsVoucherDiscount ? $data[15] : $data[16] ) );
                }

                // delivery costs
                if ( trim( $data[22] ) || trim( $data[23] ) ) {
                    $aData['basket']['deltype'] = $blAbsDel = (bool) trim( $data[22] );
                    $aData['basket']['del']     = (double) trim( str_replace( ',', '.', $blAbsDel ? $data[22] : $data[23] ) );
                    $aData['basket']['delvat']  = (double) trim( str_replace( ',', '.', $data[24] ) );
                    $aData['basket']['delbrut'] = (double) trim( str_replace( ',', '.', $data[25] ) );
                }

                // payment costs
                if ( trim( $data[26] ) || trim( $data[27] ) ) {
                    $aData['basket']['paytype'] = $blAbsPay = (bool) trim( $data[26] );
                    $aData['basket']['pay']     = (double) trim( str_replace( ',', '.', $blAbsPay ? $data[26] : $data[27] ) );
                    $aData['basket']['payvat']  = (double) trim( str_replace( ',', '.', $data[28] ) );
                    $aData['basket']['paybrut'] = (double) trim( str_replace( ',', '.', $data[29] ) );
                }

                // total brutto
                if ( trim( $data[30] ) ) {
                    $aData['basket']['totalbrut'] = (double) trim( str_replace( ',', '.', $data[30] ) );
                }

                // total netto
                if ( trim( $data[31] ) ) {
                    $aData['basket']['totalnet'] = (double) trim( str_replace( ',', '.', $data[31] ) );
                }

                // total VAT
                if ( trim( $data[32] ) ) {
                    $aData['basket']['totalvat'] = (double) trim( str_replace( ',', '.', $data[32] ) );
                }
            }

            $blContinue = (bool)( $data = fgetcsv( $hFile, 1000, "\011" ) );

            // for debug
            if ( $blContinue && trim( $data[0] ) == '#' ) {
                $blContinue = false;
            }

            // if new order is taken or the last line of file reached
            if ( ( $sOrderId && preg_match( '/ order$/i', trim( $data[0] ) ) ) || !$blContinue ) {

                $sOrderId = $sNewOrderId;

                // testing
                $this->_runSingleTest( $aData, $sOrderId );

                $blAnyTestRunned = true;

                // clearing up
                $aData = array();
            }

        }
        fclose($hFile);

        if (!$blAnyTestRunned) {
            $this->fail('No tests had been run.');
        }
    }

    protected function _setArticleDiscount($sId, $dDiscount, $blAbsDiscount )
    {
        $sDiscount = 'return array();';
        if ( $dDiscount ) {
            $sDiscountType = $blAbsDiscount ? 'abs' : '%';

            $sDiscount = "
                    \$oDiscount = new oxDiscount();
                    \$oDiscount->setId( md5(uniqid(rand(), true)) );
                    \$oDiscount->oxdiscount__oxtitle = new oxField(\"_setArticleDiscount {$dDiscount}{$sDiscountType} \", oxField::T_RAW);
                    \$oDiscount->oxdiscount__oxaddsumtype = new oxField('{$sDiscountType}');
                    \$oDiscount->oxdiscount__oxaddsum = new oxField($dDiscount);
                    return array('disc_$sId'=>\$oDiscount);
                    ";
        }

        oxTestModules::addFunction('oxdiscountlist', 'getArticleDiscounts', "{
                \$o = \$aA[0];
                if ( \$o->getId() == '$sId') {
                    $sDiscount;
                }
                return parent::getArticleDiscounts(\$aA[0], \$aA[1]);
            }");
    }

    protected function _setBasketItemDiscount( $sId, $dDiscount, $blAbsDiscount )
    {
        $sDiscount = 'return array();';
        if ( $dDiscount) {
            $sDiscountType = $blAbsDiscount ? 'abs' : '%';

            $sDiscount = "
                    \$oDiscount = new oxDiscount();
                    \$oDiscount->setId( md5(uniqid(rand(), true)) );
                    \$oDiscount->oxdiscount__oxtitle = new oxField(\"_setBasketItemDiscount {$dDiscount}{$sDiscountType} \", oxField::T_RAW);
                    \$oDiscount->oxdiscount__oxaddsumtype = new oxField('{$sDiscountType}');
                    \$oDiscount->oxdiscount__oxaddsum = new oxField($dDiscount);
                    return array('disc_$sId'=>\$oDiscount);
                    ";
        }

        oxTestModules::addFunction('oxdiscountlist', 'getBasketItemDiscounts', "{
                \$o = \$aA[0];
                if ( \$o->getId() == '$sId') {
                    $sDiscount;
                }
                return parent::getBasketItemDiscounts(\$aA[0], \$aA[1], \$aA[2] );
            }");
    }

    protected function _setBasketDiscount( $dDiscount, $blAbsDiscount )
    {
        $sDiscount = 'return array();';
        if ( $dDiscount ) {
            $sDiscountType = $blAbsDiscount ? 'abs' : '%';

            $sDiscount = "
                    \$oDiscount = new oxDiscount();
                    \$oDiscount->setId( md5(uniqid(rand(), true)) );
                    \$oDiscount->oxdiscount__oxtitle = new oxField(\"_setBasketDiscount {$dDiscount}{$sDiscountType} \", oxField::T_RAW);
                    \$oDiscount->oxdiscount__oxaddsumtype = new oxField('{$sDiscountType}');
                    \$oDiscount->oxdiscount__oxaddsum     = new oxField($dDiscount);
                    return array('disc_id'=>\$oDiscount);
                    ";
        }

        oxTestModules::addFunction( 'oxdiscountlist', 'getBasketDiscounts', "{
                $sDiscount;
            }");
    }

    protected function _setWrapping( $sId, $wrapcost )
    {
        $oWrapping = null;
        if ( $wrapcost ) {
            $oWrapping = oxNew( 'oxwrapping' );
            $oWrapping->oxwrapping__oxprice = new oxField($wrapcost, oxField::T_RAW);
        }

        self::$aWrapping[$sId] = $oWrapping;
    }

    protected function _setWoucher( $dDiscount, $blAbsDiscount )
    {
        $sDiscountType = $blAbsDiscount ? 'absolute' : '%';
        oxTestModules::addFunction( 'oxvoucher',
                                    'load',
                                    "{ return true; }" );
        oxTestModules::addFunction( 'oxvoucher',
                                    'getSerie',
                                    "{
                                     \$o = oxNew('oxvoucherserie');
                                     \$o->oxvoucherseries__oxdiscount = new oxField('{$dDiscount}', oxField::T_RAW);
                                     \$o->oxvoucherseries__oxdiscounttype = new oxField('{$sDiscountType}', oxField::T_RAW);
                                     return \$o;
                                     }" );
    }

    protected function _setDelivery( $dPrice, $blAbsDel )
    {
        oxTestModules::addFunction( 'oxDelivery',
                                    'getDeliveryPrice',
                                    "{
                                      \$this->_blFreeShipping = false;
                                      return parent::getDeliveryPrice( \$A[0]);
                                     }");

        $sDelType = $blAbsDel ? 'abs' : '%';
        oxTestModules::addFunction( 'oxDeliveryList',
                                    'getDeliveryList',
                                    "{
                                      \$o = oxNew('oxdelivery');
                                      \$o->oxdelivery__oxaddsumtype = new oxField('{$sDelType}', oxField::T_RAW);
                                      \$o->oxdelivery__oxaddsum = new oxField('{$dPrice}', oxField::T_RAW);
                                      return array( \$o );
                                     }");
    }

    protected function _setPayment( $dPrice, $blAbsPay )
    {
        oxTestModules::addFunction( 'oxbasket', 'getPaymentId', '{ return "xxx";}' );

        $sPayType = $blAbsPay ? 'abs' : '%';

        oxTestModules::addFunction( 'oxpayment', 'load', "{
                                      \$this->oxpayments__oxaddsumtype = new oxField('{$sPayType}', oxField::T_RAW);
                                      \$this->oxpayments__oxaddsum = new oxField('{$dPrice}', oxField::T_RAW);
                                      }");


    }

    public static $aArticles = null;
    public static $aWrapping = null;

    protected function _runSingleTest( $aData, $sOrderID )
    {
        // resetting
        self::$aArticles = null;
        self::$aWrapping = null;

        // overriding basket item article getter
        oxTestModules::addFunction( 'oxbasketitem', '_setArticle', '{ $this->__oArticle = '.__CLASS__.'::$aArticles[$aA[0]]; }' );
        oxTestModules::addFunction( 'oxbasketitem', 'getArticle', '{ if ( !$this->__oArticle ) $this->_setArticle(); return $this->__oArticle; }' );

        // overriding wrapping getter
        oxTestModules::addFunction( 'oxbasketitem', 'getWrapping', '{ return '.__CLASS__.'::$aWrapping[$this->getArticle()->getId()]; }' );

        // first part - creating articles and testing them
        foreach ( $aData['articles'] as $sItemId => $aItemData ) {

            // emulating product discount
            $this->_setArticleDiscount( $sItemId, $aItemData['adisc'], $aItemData['adisctype'] );

            // preparing articles for basket
            $aData['basketitem'][$sItemId]['article'] = self::$aArticles[$sItemId] = $oArticle = oxNew( 'oxarticle' );
            $oArticle->disableLazyLoading();
            $oArticle->assign( $aItemData['fields'] );

            // testing article
            $this->checkEquals( $aItemData['artbrut'], round( $oArticle->getPrice()->getBruttoPrice(), 2 ), 'article brutto price ('.$sOrderID.')', 0.000001 );
            $this->checkEquals( $aItemData['artnet'], round( $oArticle->getPrice()->getNettoPrice(), 2 ), 'article netto price ('.$sOrderID.')', 0.000001 );
        }

        // emulating discount for whole basket
        if ( $aData['basket']['basketdiscount'] ) {
            $this->_setBasketDiscount( $aData['basket']['basketdiscount'], $aData['basket']['basketdiscounttype'] );
        }

        // emulating deliveries
        if ( $aData['basket']['del'] ) {
            $this->_setDelivery( $aData['basket']['del'], $aData['basket']['deltype'] );
        }

        // emulating payment
        if ( $aData['basket']['pay'] ) {
            $this->_setPayment( $aData['basket']['pay'], $aData['basket']['paytype'] );
        }

        modConfig::getInstance()->setConfigParam( 'blPerfNoBasketSaving', true );
        $oBasket = oxNew( 'oxbasket' );

        // setting basket user just to force payment calc
        $oUser = oxNew( 'oxuser' );
        $oBasket->setBasketUser( $oUser );
        modConfig::getInstance()->setConfigParam( 'blAllowUnevenAmounts', true );

        // storing articles to basket
        foreach ( $aData['basketitem'] as $sItemId => $aItemData ) {

            // emulating basket item
            $this->_setBasketItemDiscount( $sItemId, $aItemData['itmdisc'], $aItemData['itmdisctype'] );

            // emulating basket item wrapping
            $this->_setWrapping( $sItemId, $aItemData['wrapdb'] );

            $oBasket->addToBasket( $sItemId, $aItemData['cnt'] );
        }

        // adding vouchers
        if ( $aData['basket']['voucherdiscount'] ) {

            // emulating vouchers
            $this->_setWoucher( $aData['basket']['voucherdiscount'], $aData['basket']['voucherdiscounttype'] );

            $oBasket->setSkipVouchersChecking( true );
            $oBasket->addVoucher( $sVoucherId );
        }
        // calculating
        $oBasket->calculateBasket();

        $dBasketProductsBruttoPrice = 0;

        // checking article basket prices
        $aBasketContents = $oBasket->getContents();
        foreach ( $aBasketContents as $oBasketItem ) {
            $sId = $oBasketItem->getArticle()->getId();

            // testing basket items
            $this->checkEquals( $aData['basketitem'][$sId]['totalbrut'], round( $oBasketItem->getPrice()->getBruttoPrice(), 2 ), 'total basket item brutto price ('.$sOrderID.')', 0.000001 );
            $this->checkEquals( $aData['basketitem'][$sId]['totalnet'], round( $oBasketItem->getPrice()->getNettoPrice(), 2 ), 'total basket item netto price ('.$sOrderID.')', 0.000001 );

            $dBasketProductsBruttoPrice += $aData['basketitem'][$sId]['totalbrut'];
        }

        // basket products price
        $this->checkEquals( $dBasketProductsBruttoPrice, round( $oBasket->getProductsPrice()->getBruttoSum(), 2 ), 'total basket product brutto price ('.$sOrderID.')', 0.000001 );

        // checking basket prices
        $this->checkEquals( $aData['basket']['totalbrut'], round( $oBasket->getPrice()->getBruttoPrice(), 2 ), 'total basket brutto price ('.$sOrderID.')', 0.000001 );

        //
        oxTestModules::cleanUp();
    }

}
