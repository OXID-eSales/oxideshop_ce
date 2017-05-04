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

/**
 * Basket constructor
 *
 */
class BasketConstruct
{

    /**
     * Returns calculated basket
     *
     * @param array $aArticles  article data
     * @param array $aDiscounts discount data
     *
     * @return oxBasket
     */
    public function calculateBasket($aTestCase)
    {
        // getting config
        $oConfig = oxRegistry::getConfig();

        // gather data from test case
        $aExpected = $aTestCase['expected'];
        $aArticles = $aTestCase['articles'];
        $aDiscounts = $aTestCase['discounts'];
        $aCosts = $aTestCase['costs'];
        $aOptions = $aTestCase['options'];
        $aUser = $aTestCase['user'];
        $aCats = $aTestCase['categories'];
        $aTrustedShops = $aTestCase['trustedshop'];
        // set custom configs
        $this->setOptions($aOptions);

        // categories preparation
        $this->_createCategories($aCats);

        // article preparation, returns data required for adding to basket
        $aArtsForBasket = $this->_getArticles($aArticles);

        // create & set discounts
        $this->_setDiscounts($aDiscounts);

        // create & set wrappings
        $aWrap = $this->_setWrappings($aCosts['wrapping']);

        // create & set delivery costs
        $aDelivery = $this->_setDeliveryCosts($aCosts['delivery']);

        // create & set payment costs
        $aPayment = $this->_setPayments($aCosts['payment']);

        // set trusted shop
        $sTSProductId = $this->_setTrustedShop($aTrustedShops);

        // create & set vouchers
        $aVoucherIDs = $this->_setVouchers($aCosts['voucherserie']);

        // basket preparation
        $oBasket = new oxBasket();

        // setup and login user for basket
        if (empty($aUser)) {
            $aUser = $this->_getDefaultUserData();
        }
        $oUser = $this->_createUser($aUser);

        $this->oUser = $oUser;
        $oBasket->setBasketUser($oUser);

        // group setup
        $this->createGroup($aTestCase['group']);

        // adding articles to basket      
        foreach ($aArtsForBasket as $aArt) {
            if (is_null($aArt['amount']) || ($aArt['amount']) == 0) {
                continue;
            }
            $oItem = $oBasket->addToBasket($aArt['id'], $aArt['amount']);
            // adding wrapping if need
            if (!empty($aWrap)) {
                $oItem->setWrapping($aWrap[$aArt['id']]);
            }
        }

        // try to add card
        $aWrap['card'] ? $oBasket->setCardId($aWrap['card']) : '';

        // try to add delivery
        if (!empty($aDelivery)) {
            $oBasket->setShipping($aDelivery[0]);
        }

        // try to add payment
        if (!empty($aPayment)) {
            $oBasket->setPayment($aPayment[0]);
        }

        // see if we can add trusted shop product
        if (!empty($sTSProductId)) {
            $oBasket->setTsProductId($sTSProductId);
        }

        // try to add vouchers
        $oBasket->setSkipVouchersChecking(true);
        if (!empty($aVoucherIDs)) {
            $iCount = count($aVoucherIDs);
            for ($i = 0; $i < $iCount; $i++) {
                $oBasket->addVoucher($aVoucherIDs[$i]);
            }
        }

        // calculate basket
        $oBasket->calculateBasket();

        return $oBasket;
    }

    /**
     * Create user
     *
     * @param array $aUser user data
     *
     * @return oxUser
     */
    protected function _createUser($aUser)
    {
        $oUser = $this->createObj($aUser, "oxuser", "oxuser");

        return $oUser;
    }

    /**
     * Create categories with assigning articles
     *
     * @param array $aCategories category data
     */
    protected function _createCategories($aCategories)
    {
        if (empty($aCategories)) {
            return;
        }
        foreach ($aCategories as $aCategory) {
            $oCat = $this->createObj($aCategory, 'oxcategory', 'oxcategories');
            if (!empty($aCategory['oxarticles'])) {
                foreach ($aCategory['oxarticles'] as $sArticleId) {
                    $aData = array(
                        'oxcatnid'   => $oCat->getId(),
                        'oxobjectid' => $sArticleId
                    );
                    $this->createObj2Obj($aData, 'oxobject2category');
                }
            }
        }
    }

    /**
     * Creates articles
     *
     * @param array $aArtDemoData
     *
     * @return array $aResult of id's and basket amounts of created articles
     */
    protected function _getArticles($aArtDemoData)
    {
        if (empty($aArtDemoData)) {
            return false;
        }
        $aResult = array();
        foreach ($aArtDemoData as $sOuterKey => $aArt) {
            $oArt = new oxArticle();
            $oArt->setId($aArt['oxid']);
            foreach ($aArt as $sKey => $sValue) {
                if (strstr($sKey, "ox")) {
                    $sField = "oxarticles__{$sKey}";
                    $oArt->$sField = new oxField($aArt[$sKey]);
                }
            }
            $oArt->save();
            if ($aArt['scaleprices']) {
                $this->_createScalePrices(array($aArt['scaleprices']));
            }
            if ($aArt['field2shop']) {
                $this->_createField2Shop($oArt, $aArt['field2shop']);
            }
            if ($aArt['inheritToShops']) {
                $this->_inheritToShops($oArt, $aArt['inheritToShops']);
            }
            $aResult[$sOuterKey]['id'] = $aArt['oxid'];
            $aResult[$sOuterKey]['amount'] = $aArt['amount'];
        }

        return $aResult;
    }

    /**
     * Creates price 2 article connection needed for scale prices
     *
     * @param array $aScalePrices of scale prices needed db fields
     */
    protected function _createScalePrices($aScalePrices)
    {
        $this->createObj2Obj($aScalePrices, "oxprice2article");
    }

    /**
     * Adds object to mapping table with set shop ids
     *
     * @param object $oObject Object to inherit
     * @param array  $aShops  Array of shop ids
     */
    protected function _inheritToShops($oObject, $aShops)
    {
        $iOxId = $oObject->getId();
        $sObjectTable = $oObject->getCoreTableName();

        /** @var oxElement2ShopRelations $oElement2ShopRelations */
        $oElement2ShopRelations = oxNew('oxElement2ShopRelations', $sObjectTable);
        $oElement2ShopRelations->setShopIds($aShops);
        $oElement2ShopRelations->addToShop($iOxId);
    }

    /**
     * Creates price 2 article connection needed for scale prices
     *
     * @param object $oArt     Article data
     * @param array  $aOptions Options
     */
    protected function _createField2Shop($oArt, $aOptions)
    {
        $oField2Shop = oxNew("oxfield2shop");
        $oField2Shop->setProductData($oArt);
        if (!isset($aOptions['oxartid'])) {
            $aOptions['oxartid'] = new oxField($oArt->getId());
        }
        foreach ($aOptions as $sKey => $sValue) {
            if (strstr($sKey, "ox")) {
                $sField = "oxfield2shop__{$sKey}";
                $oField2Shop->$sField = new oxField($aOptions[$sKey]);
            }
        }
        $oField2Shop->save();
    }

    /**
     * Creates discounts and assign them to according objects
     *
     * @param array $aDiscounts discount data
     */
    protected function _setDiscounts($aDiscounts)
    {
        if (empty($aDiscounts)) {
            return;
        }
        foreach ($aDiscounts as $aDiscount) {
            // add discounts
            $oDiscount = new oxDiscount();
            $oDiscount->setId($aDiscount['oxid']);
            foreach ($aDiscount as $sKey => $mxValue) {
                if (!is_array($mxValue)) {
                    $sField = "oxdiscount__" . $sKey;
                    $oDiscount->$sField = new oxField("{$mxValue}");
                } else {
                    foreach ($mxValue as $iId) {
                        $aData = array(
                            'oxid'         => $oDiscount->getId() . "_" . $iId,
                            'oxdiscountid' => $oDiscount->getId(),
                            'oxobjectid'   => $iId,
                            'oxtype'       => $sKey
                        );
                        $this->createObj2Obj($aData, "oxobject2discount");
                    }
                }
            }
            $oDiscount->save();
        }
    }

    /**
     * Set up trusted shop
     *
     * @param array $aTrustedShop of trusted shops data
     *
     * @return string selected product id
     */
    protected function _setTrustedShop($aTrustedShop)
    {
        if (empty($aTrustedShop)) {
            return null;
        }
        if ($aTrustedShop['payments']) {
            foreach ($aTrustedShop['payments'] as $sShopPayId => $sTsPayId) {
                $aPayment = new oxPayment();
                if ($aPayment->load($sShopPayId)) {
                    $aPayment->oxpayments__oxtspaymentid = new oxField($sTsPayId);
                    $aPayment->save();
                }
            }
        }

        return $aTrustedShop['product_id'];
    }

    /**
     * Creates wrappings
     *
     * @param array $aWrappings
     *
     * @return array of wrapping id's
     */
    protected function _setWrappings($aWrappings)
    {
        if (empty($aWrappings)) {
            return false;
        }
        $aWrap = array();
        foreach ($aWrappings as $aWrapping) {
            $oCard = oxNew('oxbase');
            $oCard->init('oxwrapping');
            foreach ($aWrapping as $sKey => $mxValue) {
                if (!is_array($mxValue)) {
                    $sField = "oxwrapping__" . $sKey;
                    $oCard->$sField = new oxField($mxValue, oxField::T_RAW);
                }
            }
            $oCard->save();
            if ($aWrapping['oxarticles']) {
                foreach ($aWrapping['oxarticles'] as $sArtId) {
                    $aWrap[$sArtId] = $oCard->getId();
                }
            } else {
                $aWrap['card'] = $oCard->getId();
            }
        }

        return $aWrap;
    }

    /**
     * Creates deliveries
     *
     * @param array $aDeliveryCosts
     *
     * @return array of delivery id's
     */
    protected function _setDeliveryCosts($aDeliveryCosts)
    {
        if (empty($aDeliveryCosts)) {
            return;
        }
        $aDel = array();

        if (!empty($aDeliveryCosts['oxdeliveryset'])) {
            $aData = $aDeliveryCosts['oxdeliveryset'];
        } else {
            $aData = array(
                'oxactive' => 1
            );
        }
        $oDeliverySet = $this->createObj($aData, 'oxdeliveryset', 'oxdeliveryset');

        foreach ($aDeliveryCosts as $iKey => $aDelivery) {
            $oDelivery = new oxDelivery();
            $oDelivery->save();
            foreach ($aDelivery as $sKey => $mxValue) {
                if (!is_array($mxValue)) {
                    $sField = "oxdelivery__" . $sKey;
                    $oDelivery->$sField = new oxField("{$mxValue}");
                } else {
                    foreach ($mxValue as $sId) {
                        $aData = array(
                            'oxdeliveryid' => $oDelivery->getId(),
                            'oxobjectid'   => $sId,
                            'oxtype'       => $sKey
                        );
                        $this->createObj2Obj($aData, 'oxobject2delivery');
                    }
                }
            }
            $oDelivery->save();
            $aDel[] = $oDelivery->getId();
            $aData = array(
                'oxdelid'    => $oDelivery->getId(),
                'oxdelsetid' => $oDeliverySet->getId(),
            );
            $this->createObj2Obj($aData, "oxdel2delset");
        }

        return $aDel;
    }

    /**
     * Creates payments
     *
     * @param array $aPayments
     *
     * @return array of payment id's
     */
    protected function _setPayments($aPayments)
    {
        if (empty($aPayments)) {
            return false;
        }
        $aPay = array();
        foreach ($aPayments as $iKey => $aPayment) {
            // add discounts
            $oPayment = new oxPayment();
            foreach ($aPayment as $sKey => $mxValue) {
                if (!is_array($mxValue)) {
                    $sField = "oxpayments__" . $sKey;
                    $oPayment->$sField = new oxField("{$mxValue}");
                }
            }
            $oPayment->save();
            $aPay[] = $oPayment->getId();
        }

        return $aPay;
    }

    /**
     * Creates voucherserie and it's vouchers
     *
     * @param array $aVoucherSeries voucherserie and voucher data
     *
     * @return array of voucher id's
     */
    protected function _setVouchers($aVoucherSeries)
    {
        if (empty($aVoucherSeries)) {
            return;
        }
        $aVoucherIDs = array();
        foreach ($aVoucherSeries as $aVoucherSerie) {
            $oVoucherSerie = oxNew('oxbase');
            $oVoucherSerie->init('oxvoucherseries');
            foreach ($aVoucherSerie as $sKey => $mxValue) {
                $sField = "oxvoucherseries__" . $sKey;
                $oVoucherSerie->$sField = new oxField($mxValue, oxField::T_RAW);
            }
            $oVoucherSerie->save();
            // inserting vouchers
            for ($i = 1; $i <= $aVoucherSerie['voucher_count']; $i++) {
                $aData = array(
                    'oxreserved'       => 0,
                    'oxvouchernr'      => md5(uniqid(rand(), true)),
                    'oxvoucherserieid' => $oVoucherSerie->getId()
                );
                $oVoucher = $this->createObj($aData, 'oxvoucher', 'oxvouchers');
                $aVoucherIDs[] = $oVoucher->getId();
            }
        }

        return $aVoucherIDs;
    }

    protected function _getDefaultUserData()
    {
        $aUser = array(
            'oxrights'      => 'malladmin',
            'oxactive'      => '1',
            'oxusername'    => 'admin',
            'oxpassword'    => 'f6fdffe48c908deb0f4c3bd36c032e72',
            'oxpasssalt'    => '61646D696E',
            'oxcompany'     => 'Your Company Name',
            'oxfname'       => 'John',
            'oxlname'       => 'Doe',
            'oxstreet'      => 'Maple Street',
            'oxstreetnr'    => '10',
            'oxcity'        => 'Any City',
            'oxcountryid'   => 'a7c40f631fc920687.20179984',
            'oxzip'         => '9041',
            'oxfon'         => '217-8918712',
            'oxfax'         => '217-8918713',
            'oxstateid'     => null,
            'oxaddinfo'     => null,
            'oxustid'       => null,
            'oxsal'         => 'MR',
            'oxustidstatus' => '0'
        );

        return $aUser;
    }

    /**
     * Getting articles
     *
     * @param array $aArts of article objects
     *
     * @return created articles id's
     */
    public function getArticles($aArts)
    {
        return $this->_getArticles($aArts);
    }

    /**
     * Set options
     *
     * @param array $aOptions of config options
     */
    public function setOptions($aOptions)
    {
        $oConfig = oxRegistry::getConfig();
        if (!empty($aOptions['config'])) {
            foreach ($aOptions['config'] as $sKey => $sValue) {
                $oConfig->setConfigParam($sKey, $sValue);
            }
        }
        if ($aOptions['activeCurrencyRate']) {
            // load active currencies, change if set option
            $aCurrencies = $oConfig->getConfigParam("aCurrencies");
            $aCurrencies[0] = "EUR@ " . $aOptions['activeCurrencyRate'] . "@ ,@ .@ €@ 2";
            $oConfig->setConfigParam("aCurrencies", $aCurrencies);
            $aCurrencies = $oConfig->getConfigParam("aCurrencies");
        }
    }

    /**
     * Apply discounts
     *
     * @param array $aDiscounts of discount data
     */
    public function setDiscounts($aDiscounts)
    {
        $this->_setDiscounts($aDiscounts);
    }

    /**
     * Create categories
     *
     * @param array $aCategories of categories data
     */
    public function setCategories($aCategories)
    {
        $this->_createCategories($aCategories);
    }

    /**
     * Create object 2 object connection in databse
     *
     * @param array  $aData         db fields and values
     * @param string $sObj2ObjTable table name
     */
    public function createObj2Obj($aData, $sObj2ObjTable)
    {
        if (empty($aData)) {
            return;
        }

        $aNewData = is_array($aData[0]) ? $aData : array($aData);
        foreach ($aNewData as $aValues) {
            $oObj = new oxBase();
            $oObj->init($sObj2ObjTable);
            foreach ($aValues as $sKey => $sValue) {
                $sField = $sObj2ObjTable . "__" . $sKey;
                $oObj->$sField = new oxField($sValue, oxField::T_RAW);
            }
            $oObj->save();
        }
    }

    /**
     * Create group and assign
     *
     * @param array $aData
     */
    public function createGroup($aData)
    {
        if (empty($aData)) {
            return;
        }
        foreach ($aData as $iKey => $aGroup) {
            $oGroup = $this->createObj($aGroup, 'oxgroups', ' oxgroups');
            if (!empty($aGroup['oxobject2group'])) {
                $iCnt = count($aGroup['oxobject2group']);
                for ($i = 0; $i < $iCnt; $i++) {
                    $aCon = array(
                        'oxgroupsid' => $oGroup->getId(),
                        'oxobjectid' => $aGroup['oxobject2group'][$i]
                    );
                    $this->createObj2Obj($aCon, 'oxobject2group');
                }
            }
        }
    }

    /**
     * Standard object creator
     *
     * @param array  $aData   data
     * @param string $sObject object name
     * @param string $sTable  table name
     *
     * @return object $oObj
     */
    public function createObj($aData, $sObject, $sTable)
    {
        if (empty($aData)) {
            return;
        }
        $oObj = new $sObject();
        if ($aData['oxid']) {
            $oObj->setId($aData['oxid']);
        }
        foreach ($aData as $sKey => $sValue) {
            if (!is_array($sValue)) {
                $sField = $sTable . "__" . $sKey;
                $oObj->$sField = new oxField($sValue, oxField::T_RAW);
            }
        }
        $oObj->save();

        return $oObj;
    }

    /**
     * Create shop
     *
     * @param array $aData
     */
    public function createShop($aData)
    {
        $iActiveShopId = 1;
        $iShopCnt = count($aData);
        for ($i = 0; $i < $iShopCnt; $i++) {
            $aParams = array();
            foreach ($aData[$i] as $sKey => $sValue) {
                $sField = "oxshops__" . $sKey;
                $aParams[$sField] = $sValue;
            }

            $oShop = oxNew("oxshop");
            $oShop->assign($aParams);
            $oShop->save();

            $oConfig = new oxConfig();
            $oConfig->setShopId($oShop->getId());
            $oConfig->saveShopConfVar('aarr', 'aLanguages', array('de' => 'Deutch', 'en' => 'English'));

            $oShop->generateViews();
            if ($aData[$i]['activeshop']) {
                $iActiveShopId = $oShop->getId();
            }
        }

        return $iActiveShopId;
    }

    /**
     * Setting active shop
     *
     * @param int $iShopId
     */
    public function setActiveShop($iShopId)
    {
        if ($iShopId) {
            oxRegistry::getConfig()->setShopId($iShopId);
        }
    }
}