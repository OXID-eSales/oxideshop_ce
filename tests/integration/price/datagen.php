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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

require_once __DIR__ .'/basketconstruct.php';

/**
 * Class DataGenerator
 */
class DataGenerator extends OxidTestCase
{

    // Shop modes: brutto-brutto or netto-brutto
    private $blEnterNetPrice = true;
    private $blShowNetPrice = false;
    // Test case variants
    private $iVariants = 1;
    // Custom general name of test cases. Will produce files like RandomCase_x for each case.
    private $sCaseName = "nb_";
    // Databomb folder path
    private $sFilepath = "testcases/databomb/netto_brutto/";
    // Price in cents
    private $dPriceFrom = 1;
    private $dPriceTo = 100099;
    // Min basket positions
    private $iBasketPosMin = 1;
    // Max basket positions
    private $iBasketPosMax = 20;
    // Min diff vats
    private $iDiffVatCountMin = 1;
    // Max diff vats
    private $iDiffVatCountMax = 3;
    // Min article amount at one position
    private $iAmountMin = 1;
    // Max article amount at one position
    private $iAmountMax = 250;
    // Active currency rate
    private $activeCurrencyRate = 1;

    // Discount params
    private $iDisVariants = 5;
    private $sDisName = "bombDiscount";
    private $iDisMinAddsum = 1;
    private $iDisMaxAddsum = 99;
    private $aDisTypes = array(
        "abs",
        "%",
        //"itm"
    );
    private $iDisAmount = 1;
    private $iDisAmountTo = 9999999;
    private $iDisPrice = 1;
    private $iDisPriceTo = 9999999;
    private $iDisMaxNrArtsApply = 15;

    // Wrapping params
    private $iWrapMinPrice = 0.1;
    private $iWrapMaxPrice = 9.9;
    private $iWrapMaxNrArtsApply = 2;
    private $aWrapTypes = array(
        "WRAP",
        //"CARD"
    );

    // Payment params
    private $iPayMinAddSum = 1;
    private $iPayMaxAddSum = 33;
    private $aPayAddSumTypes = array("%", "abs");
    private $iPayFromAmount = 0;
    private $iPayToAmount = 1000000;
    private $iPayChecked = 1;

    // Delivery params
    private $iDelMinAddSum = 1;
    private $iDelMaxAddSum = 25;
    private $aDelAddSumTypes = array("abs", "%");
    private $aDelTypes = array(
        //"a", // amount
        //"s", // size
        //"w", // weight
        "p" // price
    );
    private $iDelFinalize = 0;
    private $iDelParam = 0;
    private $iDelParamend = 999999999;
    private $iDelFixed = 0;

    // Voucherseries params + voucher amount
    private $iVouseries = 1;
    private $iVouNumber = 2;
    private $iVouSerieMinDiscount = 0.5;
    private $iVouSerieMaxDiscount = 75;
    private $aVouSerieTypes = array(
        'absolute',
        'percent'
    );
    private $iVouAllowSameSeries = 1;
    private $iVouAllowOtherSeries = 1;
    private $iVouAllowUseAnother = 1;

    // What additional costs to generate
    private $aGenCosts = array(
        array("wrapping", 1),
        //array( "payment",  3 ), 
        //array( "delivery", 3 )
    );
    private $blGenDiscounts = false;
    private $blGenVouchers = false;

    public function __construct()
    {
        $this->sFilepath = __DIR__ .'/'. $this->sFilepath;
    }

    protected function _cleanUpCalcDb()
    {
        $this->_truncateTable("oxarticles");
        $this->_truncateTable("oxdiscount");
        $this->_truncateTable("oxobject2discount");
        $this->_truncateTable("oxwrapping");
        $this->_truncateTable("oxdelivery");
        $this->_truncateTable("oxdel2delset");
        $this->_truncateTable("oxobject2payment");
        $this->_truncateTable("oxvouchers");
        $this->_truncateTable("oxvoucherseries");
        $this->_truncateTable("oxobject2delivery");
        $this->_truncateTable("oxdeliveryset");
    }

    /**
     * Function to return vat set of world's vats
     *
     * @return array of different vats
     */
    protected function _getVatSet()
    {
        return array(
            27, 25.5, 25, 24, 23, 22, 21, 21.2, 20,
            19.6, 19, 18, 17.5, 17, 16, 15, 14.5, 14, 13, 13.5, 12.5, 12, 11, 10.5, 10,
            9, 8.5, 8, 7, 6, 6.5, 5.6, 5.5, 5, 4.8, 4.5, 4, 3.8, 3, 2.5, 2.1, 2, 1, 0
        );
    }

    /**
     * Create test case file
     *
     * @param string $sFilename test case filename
     *
     * @return file resource
     */
    protected function _createFile($sFilename)
    {
        return fopen($this->sFilepath . $sFilename, "w");
    }

    /**
     * Writes data array to file with provided handle
     *
     * @param       file   resource to write $rHandle
     * @param array $aData of data needed to write
     *
     * @return mixed
     */
    protected function _writeToFile($rHandle, $aData)
    {
        $sStart = "<?php\r";
        $sStart .= "\$aData = ";
        $sData = var_export($aData, true);
        $sEnd = ";";

        return fwrite($rHandle, $sStart . $sData . $sEnd);
    }

    /**
     * Main generator startup function, calls other utilities
     *
     * @test
     */
    public function generate()
    {
        if (!is_dir($this->sFilepath)) {
            mkdir($this->sFilepath, '0777');
        }
        for ($i = 1; $i <= $this->iVariants; $i++) {
            parent::setUp();
            $this->_cleanUpCalcDb();
            $aData = $this->_generateData($i);
            $sFilename = "{$this->sCaseName}{$i}.php";
            $rHandle = $this->_createFile($sFilename);
            $this->_writeToFile($rHandle, $aData);
            print("o-");
            parent::tearDown();
        }
    }

    /**
     * Data generator
     *
     * @param integer $i variant number
     *
     * @return array $aData of basket data and expectations
     */
    protected function _generateData($i)
    {
        $oUtil = oxUtilsObject::getInstance();
        // init result array
        $aData = array();

        // new user gen data
        $aData['user'] = array(
            'oxactive'   => 1,
            'oxusername' => $this->sCaseName . 'databomb_user_' . $i,
        );

        // get basket position count
        $iRandArtCount = rand($this->iBasketPosMin, $this->iBasketPosMax);
        // get different vat count
        $iDiffVatCount = rand($this->iDiffVatCountMin, $this->iDiffVatCountMax);
        // get $iDiffVatCount vats from vat set
        $aVats = array_rand($this->_getVatSet(), $iDiffVatCount);
        // create articles array
        for ($i = 0; $i < $iRandArtCount; $i++) {
            $aArticle = array();
            $sUID = $oUtil->generateUId();
            $aArticle['oxid'] = $sUID;
            $aArticle['oxprice'] = mt_rand($this->dPriceFrom, $this->dPriceTo) / 100;
            // check if got any special vat
            if (count($aVats) > 0) {
                // check if got vat set vs single vat
                if (count($aVats) == 1) {
                    $aArticle['oxvat'] = $aVats;
                } else {
                    $aArticle['oxvat'] = $aVats[array_rand($aVats, 1)];
                }
            }
            $aArticle['amount'] = rand($this->iAmountMin, $this->iAmountMax);
            $aData['articles'][$i] = $aArticle;
        }
        if ($this->blGenDiscounts) {
            // create discount array
            $aData['discounts'] = $this->_generateDiscounts($aData);
        }
        if (!empty($this->aGenCosts)) {
            // create costs array
            $aData['costs'] = $this->_generateCosts($aData);
        }
        if ($this->blGenVouchers) {
            // create voucher discounts
            $aData['costs']['voucherserie'] = $this->_generateVouchers($aData);
        }
        // create options array
        $aData['options'] = array();
        $aData['options']['config']['blEnterNetPrice'] = $this->blEnterNetPrice;
        $aData['options']['config']['blShowNetPrice'] = $this->blShowNetPrice;
        $aData['options']['activeCurrencyRate'] = $this->activeCurrencyRate;
        // create expected array
        $aData['expected'] = $this->_gatherExpectedData($aData);

        return $aData;
    }

    /**
     * Generate vouchers
     *
     * @param array $aData
     */
    protected function _generateVouchers($aData)
    {
        $aVouchers = array();
        for ($i = 0; $i < $this->iVouseries; $i++) {
            $aVouchers[$i]['oxdiscount'] = mt_rand($this->iVouSerieMinDiscount, $this->iVouSerieMaxDiscount);
            $aVouchers[$i]['oxdiscounttype'] = $this->aVouSerieTypes[array_rand($this->aVouSerieTypes, 1)];
            $aVouchers[$i]['oxallowsameseries'] = $this->iVouAllowSameSeries;
            $aVouchers[$i]['oxallowotherseries'] = $this->iVouAllowOtherSeries;
            $aVouchers[$i]['oxallowuseanother'] = $this->iVouAllowUseAnother;
            $aVouchers[$i]['voucher_count'] = $this->iVouNumber;
        }

        return $aVouchers;
    }

    /**
     * Generate costs
     *
     * @param array $aData
     */
    protected function _generateCosts($aData)
    {
        $aCosts = array();
        foreach ($this->aGenCosts as $aCostData) {
            switch ($aCostData[0]) {
                case 'wrapping':
                    $aCosts['wrapping'] = array();
                    for ($i = 0; $i < $aCostData[1]; $i++) {
                        $aCost = array();
                        $aCost['oxtype'] = $this->aWrapTypes[array_rand($this->aWrapTypes, 1)];
                        $aCost['oxprice'] = mt_rand($this->iWrapMinPrice, $this->iWrapMaxPrice);
                        $aCost['oxactive'] = 1;
                        if ($this->iWrapMaxNrArtsApply > 0) {
                            $aCost['oxarticles'] = array();
                            if ($this->iWrapMaxNrArtsApply <= count($aData['articles'])) {
                                $iRandCount = mt_rand(1, $this->iWrapMaxNrArtsApply);
                            } else {
                                $iRandCount = mt_rand(1, count($aData['articles']));
                            }
                            $mxRand = array_rand($aData['articles'], $iRandCount);
                            $iMxRandCount = count($mxRand);
                            for ($j = 0; $j < $iMxRandCount; $j++) {
                                array_push($aCost['oxarticles'], $aData['articles'][$j]['oxid']);
                            }
                        }
                        $aCosts['wrapping'][$i] = $aCost;
                    }
                    break;
                case 'payment':
                    $aCosts['payment'] = array();
                    for ($i = 0; $i < $aCostData[1]; $i++) {
                        $aCost = array();
                        $aCost['oxaddsumtype'] = $this->aPayAddSumTypes[array_rand($this->aPayAddSumTypes, 1)];
                        $aCost['oxaddsum'] = mt_rand($this->iPayMinAddSum, $this->iPayMaxAddSum);
                        $aCost['oxactive'] = 1;
                        $aCost['oxchecked'] = $this->iPayChecked;
                        $aCost['oxfromamount'] = $this->iPayFromAmount;
                        $aCost['oxtoamount'] = $this->iPayToAmount;
                        $aCosts['payment'][$i] = $aCost;
                    }
                    break;
                case 'delivery':
                    $aCosts['delivery'] = array();
                    for ($i = 0; $i < $aCostData[1]; $i++) {
                        $aCost = array();
                        $aCost['oxaddsumtype'] = $this->aDelAddSumTypes[array_rand($this->aDelAddSumTypes, 1)];
                        $aCost['oxaddsum'] = mt_rand($this->iDelMinAddSum, $this->iDelMaxAddSum);
                        $aCost['oxactive'] = 1;
                        $aCost['oxdeltype'] = $this->aDelTypes[array_rand($this->aDelTypes, 1)];
                        $aCost['oxfinalize'] = $this->iDelFinalize;
                        $aCost['oxparam'] = $this->iDelParam;
                        $aCost['oxparamend'] = $this->iDelParamend;
                        $aCost['oxfixed'] = $this->iDelFixed;
                        $aCosts['delivery'][$i] = $aCost;
                    }
                    break;
                default:
                    break;
            }
        }

        return $aCosts;
    }

    /**
     * Generate discounts
     *
     * @param array $aData
     */
    protected function _generateDiscounts($aData)
    {
        $aDiscounts = array();
        for ($i = 0; $i < $this->iDisVariants; $i++) {
            $aDiscounts[$i]['oxaddsum'] = mt_rand($this->iDisMinAddsum, $this->iDisMaxAddsum);
            $aDiscounts[$i]['oxid'] = $this->sDisName . '_' . $i;
            $aDiscounts[$i]['oxaddsumtype'] = $this->aDisTypes[array_rand($this->aDisTypes, 1)];
            $aDiscounts[$i]['oxamount'] = $this->iDisAmount;
            $aDiscounts[$i]['oxamountto'] = $this->iDisAmountTo;
            $aDiscounts[$i]['oxprice'] = $this->iDisPrice;
            $aDiscounts[$i]['oxpriceto'] = $this->iDisPriceTo;
            $aDiscounts[$i]['oxactive'] = 1;
            if ($this->iDisMaxNrArtsApply > 0) {
                $aDiscounts[$i]['oxarticles'] = array();
                if ($this->iDisMaxNrArtsApply <= count($aData['articles'])) {
                    $iRandCount = mt_rand(1, $this->iDisMaxNrArtsApply);
                } else {
                    $iRandCount = mt_rand(1, count($aData['articles']));
                }
                $mxRand = array_rand($aData['articles'], $iRandCount);
                $iMxRandCount = count($mxRand);
                for ($j = 0; $j < $iMxRandCount; $j++) {
                    array_push($aDiscounts[$i]['oxarticles'], $aData['articles'][$j]['oxid']);
                }
            }
        }

        return $aDiscounts;
    }

    /**
     * Gathering expectations
     *
     * @param array 's of articles, discounts, costs, options
     *
     * @return array $aExpected of expected data
     */
    protected function _gatherExpectedData($aTestCase)
    {
        // load calculated basket
        $oBasketConstruct = new BasketConstruct();
        $oBasket = $oBasketConstruct->calculateBasket($aTestCase);

        // gathering data arrays
        $aExpected = array();
        // Basket item list
        $aBasketItemList = $oBasket->getContents();
        if ($aBasketItemList) {
            foreach ($aBasketItemList as $iKey => $oBasketItem) {
                $iArtId = $oBasketItem->getArticle()->getID();
                $aExpected['articles'][$iArtId] = array($oBasketItem->getFUnitPrice(), $oBasketItem->getFTotalPrice());
            }
        }
        // Basket total discounts
        $aProductDiscounts = $oBasket->getDiscounts();
        if ($aProductDiscounts) {
            foreach ($aProductDiscounts as $oDiscount) {
                $aExpected['totals']['discounts'][$oDiscount->sOXID] = $oDiscount->fDiscount;
            }
        }
        // VAT's
        $aProductVats = $oBasket->getProductVats();
        if ($aProductVats) {
            foreach ($aProductVats as $sPercent => $sSum) {
                $aExpected['totals']['vats'][$sPercent] = $sSum;
            }
        }

        // Wrapping costs
        $aExpected['totals']['wrapping']['brutto'] = $oBasket->getFWrappingCosts();
        $aExpected['totals']['wrapping']['netto'] = $oBasket->getWrappCostNet();
        $aExpected['totals']['wrapping']['vat'] = $oBasket->getWrappCostVat();
        // Giftcard costs
        $aExpected['totals']['giftcard']['brutto'] = $oBasket->getFGiftCardCosts();
        $aExpected['totals']['giftcard']['netto'] = $oBasket->getGiftCardCostNet();
        $aExpected['totals']['giftcard']['vat'] = $oBasket->getGiftCardCostVat();
        // Delivery costs
        $aExpected['totals']['delivery']['brutto'] = number_format(round($oBasket->getDeliveryCosts(), 2), 2, ',', '.');
        $aExpected['totals']['delivery']['netto'] = $oBasket->getDelCostNet();
        $aExpected['totals']['delivery']['vat'] = $oBasket->getDelCostVat();
        // Payment costs
        $aExpected['totals']['payment']['brutto'] = number_format(round($oBasket->getPaymentCosts(), 2), 2, ',', '.');
        $aExpected['totals']['payment']['netto'] = $oBasket->getPayCostNet();
        $aExpected['totals']['payment']['vat'] = $oBasket->getPayCostVat();
        // Vouchers
        $aExpected['totals']['voucher']['brutto'] = number_format(round($oBasket->getVoucherDiscValue(), 2), 2, ',', '.');
        // Total netto & brutto, grand total
        $aExpected['totals']['totalNetto'] = $oBasket->getProductsNetPrice();
        $aExpected['totals']['totalBrutto'] = $oBasket->getFProductsPrice();
        $aExpected['totals']['grandTotal'] = $oBasket->getFPrice();

        // Finished generating expectations
        return $aExpected;
    }

    /**
     * Generating sql dump of required tables (oxarticles)
     */
    protected function _generateSqlDump()
    {
        $dbhost = $this->getConfigParam("dbHost");
        $dbuser = $this->getConfigParam("dbUser");
        $dbpwd = $this->getConfigParam("dbPwd");
        $dbname = $this->getConfigParam("dbName");
        $dumpfile = "oxarticles.sql";
        passthru("/usr/bin/mysqldump --opt --host=$dbhost --user=$dbuser --password=$dbpwd $dbname oxarticles > $this->sFilepath/$dumpfile");
        echo "$dumpfile ";
        passthru("tail -1 $this->sFilepath/$dumpfile");
    }


    /**
     * Truncates specified table
     *
     * @param string $sTable table name
     */
    protected function _truncateTable($sTable)
    {
        return oxDb::getDb()->execute("TRUNCATE {$sTable}");
    }
}