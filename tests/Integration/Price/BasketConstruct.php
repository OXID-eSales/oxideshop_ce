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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Integration\Price;

use oxArticle;
use oxBase;
use oxBasket;
use oxField;
use OxidEsales\Eshop\Core\Exception\DatabaseException;
use oxRegistry;
use oxUser;

/**
 * Basket constructor
 */
class BasketConstruct
{
    /**
     * Returns calculated basket
     *
     * @param array $testCase
     *
     * @return oxBasket
     */
    public function calculateBasket($testCase)
    {
        // gather data from test case
        $articles = $testCase['articles'];
        $discounts = $testCase['discounts'];
        $costs = $testCase['costs'];
        $options = $testCase['options'];
        $userData = $testCase['user'];
        $categories = $testCase['categories'];
        // set custom configs
        $this->setOptions($options);

        // categories preparation
        $this->_createCategories($categories);

        // article preparation, returns data required for adding to basket
        $articlesForBasket = $this->_getArticles($articles);

        // create & set discounts
        $this->_setDiscounts($discounts);

        // create & set wrappings
        $wrapping = $this->_setWrappings($costs['wrapping']);

        // create & set delivery costs
        $delivery = $this->_setDeliveryCosts($costs['delivery']);

        // create & set payment costs
        $payment = $this->_setPayments($costs['payment']);

        // create & set vouchers
        $voucherIDs = $this->_setVouchers($costs['voucherserie']);

        // basket preparation
        $basket = oxNew('oxBasket');

        // setup and login user for basket
        if (empty($userData)) {
            $userData = $this->_getDefaultUserData();
        }
        $user = $this->_createUser($userData);

        $this->oUser = $user;
        $basket->setBasketUser($user);

        // group setup
        try {
            $this->createGroup($testCase['group']);
        } catch (DatabaseException $exception) {
            /** We will ignore exceptions that occur because of duplicate keys (MySQL Error 1062)  */
            if ($exception->getCode() != 1062) {
                throw $exception;
            }
        }

        // adding articles to basket
        foreach ($articlesForBasket as $article) {
            if (is_null($article['amount']) || ($article['amount']) == 0) {
                continue;
            }
            $orderArticle = $basket->addToBasket($article['id'], $article['amount']);
            // adding wrapping if need
            if (!empty($wrapping)) {
                $orderArticle->setWrapping($wrapping[$article['id']]);
            }
        }

        // try to add card
        $wrapping['card'] ? $basket->setCardId($wrapping['card']) : '';

        // try to add delivery
        if (!empty($delivery)) {
            $basket->setShipping($delivery[0]);
        }

        // try to add payment
        if (!empty($payment)) {
            $basket->setPayment($payment[0]);
        }

        // try to add vouchers
        $basket->setSkipVouchersChecking(true);
        if (!empty($voucherIDs)) {
            $count = count($voucherIDs);
            for ($i = 0; $i < $count; $i++) {
                $basket->addVoucher($voucherIDs[$i]);
            }
        }

        // calculate basket
        $basket->calculateBasket();

        return $basket;
    }

    /**
     * Create user
     *
     * @param array $userData user data
     *
     * @return oxUser
     */
    protected function _createUser($userData)
    {
        $user = $this->createObj($userData, "oxuser", "oxuser");

        return $user;
    }

    /**
     * Create categories with assigning articles
     *
     * @param array $categories category data
     */
    protected function _createCategories($categories)
    {
        $categories = (array) $categories;
        foreach ($categories as $categoryData) {
            $category = $this->createObj($categoryData, 'oxcategory', 'oxcategories');
            if (!empty($categoryData['oxarticles'])) {
                foreach ($categoryData['oxarticles'] as $articleId) {
                    $data = array(
                        'oxcatnid'   => $category->getId(),
                        'oxobjectid' => $articleId
                    );
                    $this->createObj2Obj($data, 'oxobject2category');
                }
            }
        }
    }

    /**
     * Creates articles
     *
     * @param array $articles
     *
     * @return array $aResult of id's and basket amounts of created articles
     */
    protected function _getArticles($articles)
    {
        $result = array();
        $articles = (array) $articles;
        foreach ($articles as $outerKey => $articleData) {
            $article = oxNew('oxArticle');
            $article->setId($articleData['oxid']);
            foreach ($articleData as $key => $value) {
                if (strstr($key, "ox")) {
                    $field = "oxarticles__{$key}";
                    $article->$field = new oxField($articleData[$key]);
                }
            }
            $article->save();
            if ($articleData['scaleprices']) {
                $this->_createScalePrices(array($articleData['scaleprices']));
            }
            if ($articleData['field2shop']) {
                $this->_createField2Shop($article, $articleData['field2shop']);
            }
            if ($articleData['inheritToShops']) {
                $this->_inheritToShops($article, $articleData['inheritToShops']);
            }
            $result[$outerKey]['id'] = $articleData['oxid'];
            $result[$outerKey]['amount'] = $articleData['amount'];
        }

        return $result;
    }

    /**
     * Creates price 2 article connection needed for scale prices
     *
     * @param array $scalePrices of scale prices needed db fields
     */
    protected function _createScalePrices($scalePrices)
    {
        $this->createObj2Obj($scalePrices, "oxprice2article");
    }

    /**
     * Adds object to mapping table with set shop ids
     *
     * @param object $oObject Object to inherit
     * @param array  $aShops  Array of shop ids
     */
    protected function _inheritToShops($oObject, $aShops)
    {
        $objectId = $oObject->getId();
        $objectTable = $oObject->getCoreTableName();

        $element2ShopRelations = oxNew('oxElement2ShopRelations', $objectTable);
        $element2ShopRelations->setShopIds($aShops);
        $element2ShopRelations->addToShop($objectId);
    }

    /**
     * Creates price 2 article connection needed for scale prices
     *
     * @param oxArticle $article Article data
     * @param array     $options Options
     */
    protected function _createField2Shop($article, $options)
    {
        $field2Shop = oxNew("oxField2Shop");
        $field2Shop->setProductData($article);
        if (!isset($options['oxartid'])) {
            $options['oxartid'] = new oxField($article->getId());
        }
        foreach ($options as $sKey => $sValue) {
            if (strstr($sKey, "ox")) {
                $field = "oxfield2shop__{$sKey}";
                $field2Shop->$field = new oxField($options[$sKey]);
            }
        }
        $field2Shop->save();
    }

    /**
     * Creates discounts and assign them to according objects
     *
     * @param array $discounts discount data
     */
    protected function _setDiscounts($discounts)
    {
        $discounts = (array) $discounts;
        foreach ($discounts as $discountData) {
            // add discounts
            $discount = oxNew('oxDiscount');
            $discount->setId($discountData['oxid']);
            foreach ($discountData as $key => $value) {
                if (!is_array($value)) {
                    $field = "oxdiscount__" . $key;
                    $discount->$field = new oxField("{$value}");
                } else {
                    foreach ($value as $id) {
                        $aData = array(
                            'oxid'         => $discount->getId() . "_" . $id,
                            'oxdiscountid' => $discount->getId(),
                            'oxobjectid'   => $id,
                            'oxtype'       => $key
                        );
                        $this->createObj2Obj($aData, "oxobject2discount");
                    }
                }
            }
            $discount->save();
        }
    }

    /**
     * Creates wrappings
     *
     * @param array $wrappings
     *
     * @return array of wrapping id's
     */
    protected function _setWrappings($wrappings)
    {
        $result = array();
        $wrappings = (array) $wrappings;
        foreach ($wrappings as $wrapping) {
            $card = oxNew('oxBase');
            $card->init('oxwrapping');
            foreach ($wrapping as $key => $value) {
                if (!is_array($value)) {
                    $field = "oxwrapping__" . $key;
                    $card->$field = new oxField($value, oxField::T_RAW);
                }
            }
            $card->save();
            if ($wrapping['oxarticles']) {
                foreach ($wrapping['oxarticles'] as $sArtId) {
                    $result[$sArtId] = $card->getId();
                }
            } else {
                $result['card'] = $card->getId();
            }
        }

        return $result;
    }

    /**
     * Creates deliveries
     *
     * @param array $deliveryCosts
     *
     * @return array of delivery id's
     */
    protected function _setDeliveryCosts($deliveryCosts)
    {
        if (empty($deliveryCosts)) {
            return null;
        }
        $deliveries = array();

        if (!empty($deliveryCosts['oxdeliveryset'])) {
            $data = $deliveryCosts['oxdeliveryset'];
        } else {
            $data = array('oxactive' => 1);
        }
        $deliverySet = $this->createObj($data, 'oxdeliveryset', 'oxdeliveryset');

        foreach ($deliveryCosts as $deliveryData) {
            $delivery = oxNew('oxDelivery');
            $delivery->save();
            foreach ($deliveryData as $key => $value) {
                if (!is_array($value)) {
                    $field = "oxdelivery__" . $key;
                    $delivery->$field = new oxField("{$value}");
                } else {
                    foreach ($value as $sId) {
                        $data = array(
                            'oxdeliveryid' => $delivery->getId(),
                            'oxobjectid'   => $sId,
                            'oxtype'       => $key
                        );
                        $this->createObj2Obj($data, 'oxobject2delivery');
                    }
                }
            }
            $delivery->save();
            $deliveries[] = $delivery->getId();
            $data = array(
                'oxdelid'    => $delivery->getId(),
                'oxdelsetid' => $deliverySet->getId(),
            );
            $this->createObj2Obj($data, "oxdel2delset");
        }

        return $deliveries;
    }

    /**
     * Creates payments
     *
     * @param array $payments
     *
     * @return array of payment id's
     */
    protected function _setPayments($payments)
    {
        $result = array();
        $payments = (array) $payments;
        foreach ($payments as $paymentData) {
            // add discounts
            $payment = oxNew('oxPayment');
            foreach ($paymentData as $key => $value) {
                if (!is_array($value)) {
                    $field = "oxpayments__" . $key;
                    $payment->$field = new oxField("{$value}");
                }
            }
            $payment->save();
            $result[] = $payment->getId();
        }

        return $result;
    }

    /**
     * Creates voucherserie and it's vouchers
     *
     * @param array $voucherSeries voucherserie and voucher data
     *
     * @return array of voucher id's
     */
    protected function _setVouchers($voucherSeries)
    {
        $voucherIDs = array();
        $voucherSeries = (array) $voucherSeries;
        foreach ($voucherSeries as $voucherData) {
            $voucherSerie = oxNew('oxBase');
            $voucherSerie->init('oxvoucherseries');
            foreach ($voucherData as $key => $value) {
                $field = "oxvoucherseries__" . $key;
                $voucherSerie->$field = new oxField($value, oxField::T_RAW);
            }
            $voucherSerie->save();
            // inserting vouchers
            for ($i = 1; $i <= $voucherData['voucher_count']; $i++) {
                $data = array(
                    'oxreserved'       => 0,
                    'oxvouchernr'      => md5(uniqid(rand(), true)),
                    'oxvoucherserieid' => $voucherSerie->getId()
                );
                $voucher = $this->createObj($data, 'oxvoucher', 'oxvouchers');
                $voucherIDs[] = $voucher->getId();
            }
        }

        return $voucherIDs;
    }

    /**
     * @return array
     */
    protected function _getDefaultUserData()
    {
        return array(
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
    }

    /**
     * Getting articles
     *
     * @param array $articles of article objects
     *
     * @return array Created articles id's
     */
    public function getArticles($articles)
    {
        return $this->_getArticles($articles);
    }

    /**
     * Set options
     *
     * @param array $options of config options
     */
    public function setOptions($options)
    {
        $config = oxRegistry::getConfig();
        if (!empty($options['config'])) {
            foreach ($options['config'] as $key => $value) {
                $config->setConfigParam($key, $value);
            }
        }
        if ($options['activeCurrencyRate']) {
            // load active currencies, change if set option
            $currencies = $config->getConfigParam("aCurrencies");
            $currencies[0] = "EUR@ " . $options['activeCurrencyRate'] . "@ ,@ .@ €@ 2";
            $config->setConfigParam("aCurrencies", $currencies);
        }
    }

    /**
     * Apply discounts
     *
     * @param array $discounts of discount data
     */
    public function setDiscounts($discounts)
    {
        $this->_setDiscounts($discounts);
    }

    /**
     * Create categories
     *
     * @param array $categories of categories data
     */
    public function setCategories($categories)
    {
        $this->_createCategories($categories);
    }

    /**
     * Create object 2 object connection in databse
     *
     * @param array  $data               db fields and values
     * @param string $object2ObjectTable table name
     */
    public function createObj2Obj($data, $object2ObjectTable)
    {
        $newData = is_array($data[0]) ? $data : array($data);
        foreach ($newData as $values) {
            $object = oxNew('oxBase');
            $object->init($object2ObjectTable);
            foreach ($values as $key => $value) {
                $field = $object2ObjectTable . "__" . $key;
                $object->$field = new oxField($value, oxField::T_RAW);
            }
            $object->save();
        }
    }

    /**
     * Create group and assign
     *
     * @param array $data
     */
    public function createGroup($data)
    {
        $data = (array) $data;
        foreach ($data as $key => $groupData) {
            $group = $this->createObj($groupData, 'oxgroups', ' oxgroups');
            if (!empty($groupData['oxobject2group'])) {
                $count = count($groupData['oxobject2group']);
                for ($i = 0; $i < $count; $i++) {
                    $connection = array(
                        'oxgroupsid' => $group->getId(),
                        'oxobjectid' => $groupData['oxobject2group'][$i]
                    );
                    $this->createObj2Obj($connection, 'oxobject2group');
                }
            }
        }
    }

    /**
     * Standard object creator
     *
     * @param array  $data
     * @param string $objectClass
     * @param string $table
     *
     * @return oxBase
     */
    public function createObj($data, $objectClass, $table)
    {
        if (empty($data)) {
            return null;
        }
        /** @var oxBase $object */
        $object = new $objectClass();
        if ($data['oxid']) {
            $object->setId($data['oxid']);
        }
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $field = $table . "__" . $key;
                $object->$field = new oxField($value, oxField::T_RAW);
            }
        }
        $object->save();

        return $object;
    }

    /**
     * Create shop
     *
     * @param array $data
     *
     * @return int
     */
    public function createShop($data)
    {
        $activeShopId = 1;
        $shopCnt = count($data);
        for ($i = 0; $i < $shopCnt; $i++) {
            $parameters = array();
            foreach ($data[$i] as $key => $value) {
                $field = "oxshops__" . $key;
                $parameters[$field] = $value;
            }

            $shop = oxNew("oxShop");
            $shop->assign($parameters);
            $shop->save();

            $config = oxNew('oxConfig');
            $config->setShopId($shop->getId());
            $config->saveShopConfVar('aarr', 'aLanguages', array('de' => 'Deutch', 'en' => 'English'));

            $shop->generateViews();
            if ($data[$i]['activeshop']) {
                $activeShopId = $shop->getId();
            }
        }

        return $activeShopId;
    }

    /**
     * Setting active shop
     *
     * @param int $shopId
     */
    public function setActiveShop($shopId)
    {
        if ($shopId) {
            oxRegistry::getConfig()->setShopId($shopId);
        }
    }
}
