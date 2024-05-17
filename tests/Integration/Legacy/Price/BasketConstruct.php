<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Price;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\Delivery;
use OxidEsales\Eshop\Application\Model\Discount;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Application\Model\VoucherSerie;
use OxidEsales\Eshop\Application\Model\Wrapping;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Registry;

use function is_array;

class BasketConstruct
{
    public User $user;

    public function calculateBasket(array $testCase): Basket
    {
        // gather data from test case
        $products = $testCase['articles'] ?? [];
        $discounts = $testCase['discounts'] ?? [];
        $costs = $testCase['costs'] ?? [];
        $options = $testCase['options'] ?? [];
        $userData = $testCase['user'] ?? [];
        $categories = $testCase['categories'] ?? [];
        // set custom configs
        $this->setOptions($options);

        // categories preparation
        $this->createCategories($categories);

        // products preparation, returns data required for adding to basket
        $productsForBasket = $this->createProducts($products);

        // create & set discounts
        $this->createDiscounts($discounts);

        // create & set wrappings
        $wrapping = $this->createWrappings($costs['wrapping'] ?? []);

        // create & set delivery costs
        $delivery = $this->createDeliveryCosts($costs['delivery'] ?? []);

        // create & set payment costs
        $payment = $this->createPayments($costs['payment'] ?? []);

        // create & set vouchers
        $voucherIDs = $this->createVouchers($costs['voucherserie'] ?? []);

        // basket preparation
        $basket = oxNew(Basket::class);

        // setup and login user for basket
        if (empty($userData)) {
            $userData = $this->getDefaultUserData();
        }
        $basket->setBasketUser($this->createUser($userData));

        // group setup
        $this->createGroup($testCase['group'] ?? []);

        // adding products to basket
        foreach ($productsForBasket as $productData) {
            if ($productData['amount'] === null) {
                continue;
            }
            if (($productData['amount']) === 0) {
                continue;
            }
            $orderProduct = $basket->addToBasket($productData['id'], $productData['amount']);
            // adding wrapping if need
            if ($wrapping !== []) {
                $orderProduct->setWrapping($wrapping[$productData['id']] ?? null);
            }
        }

        if (isset($wrapping['card']) && $wrapping['card']) {
            $basket->setCardId($wrapping['card']);
        }

        // try to add delivery
        if (!empty($delivery)) {
            $basket->setShipping($delivery[0]);
        }

        // try to add payment
        if ($payment !== []) {
            $basket->setPayment($payment[0]);
        }

        // try to add vouchers
        $basket->setSkipVouchersChecking(true);
        if ($voucherIDs !== []) {
            foreach ($voucherIDs as $iValue) {
                $basket->addVoucher($iValue);
            }
        }

        // calculate basket
        $basket->calculateBasket();

        return $basket;
    }

    public function setOptions(array $options): void
    {
        Registry::getConfig()->init();
        if (!empty($options['config'])) {
            foreach ($options['config'] as $key => $value) {
                Registry::getConfig()->setConfigParam($key, $value);
            }
        }
        if (isset($options['activeCurrencyRate']) && $options['activeCurrencyRate']) {
            // load active currencies, change if set option
            $currencies = Registry::getConfig()->getConfigParam('aCurrencies');
            $currencies[0] = 'EUR@ ' . $options['activeCurrencyRate'] . '@ ,@ .@ €@ 2';
            Registry::getConfig()->setConfigParam('aCurrencies', $currencies);
        }
    }

    public function createObj2Obj(array $data, string $object2ObjectTable): void
    {
        $newData = is_array($data[0] ?? null) ? $data : [$data];
        foreach ($newData as $values) {
            $object = oxNew(BaseModel::class);
            $object->init($object2ObjectTable);
            foreach ($values as $key => $value) {
                $field = $object2ObjectTable . '__' . $key;
                $object->{$field} = new Field($value, Field::T_RAW);
            }
            $object->save();
        }
    }

    public function createGroup(array $data): void
    {
        foreach ($data as $groupData) {
            $group = $this->createObj($groupData, 'oxgroups', ' oxgroups');
            if (!empty($groupData['oxobject2group'])) {
                foreach ($groupData['oxobject2group'] as $iValue) {
                    $connection = [
                        'oxgroupsid' => $group->getId(),
                        'oxobjectid' => $iValue ?? null,
                    ];
                    $this->createObj2Obj($connection, 'oxobject2group');
                }
            }
        }
    }

    public function createObj(array $data, string $objectClass, string $table): ?BaseModel
    {
        if (empty($data)) {
            return null;
        }
        $object = oxNew($objectClass);
        if (isset($data['oxid']) && $data['oxid']) {
            $object->setId($data['oxid']);
        }
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $field = $table . '__' . $key;
                $object->{$field} = new Field($value, Field::T_RAW);
            }
        }
        $object->save();

        return $object;
    }

    private function createUser(array $userData): User
    {
        if (empty($userData['oxcountryid'])) {
            $userData['oxcountryid'] = DatabaseProvider::getDb()
                ->select('SELECT OXID FROM oxcountry WHERE OXTITLE = "Deutschland"')
                ->fetchRow();
        }
        return $this->createObj($userData, 'oxuser', 'oxuser');
    }

    public function createCategories(array $categories): void
    {
        foreach ($categories as $categoryData) {
            $category = $this->createObj($categoryData, Category::class, 'oxcategories');
            if (!empty($categoryData['oxarticles'])) {
                foreach ($categoryData['oxarticles'] as $productId) {
                    $data = [
                        'oxcatnid' => $category->getId(),
                        'oxobjectid' => $productId,
                    ];
                    $this->createObj2Obj($data, 'oxobject2category');
                }
            }
        }
    }

    /**
     * @return array of id's and basket amounts of created products
     */
    public function createProducts(array $products): array
    {
        $result = [];
        foreach ($products as $outerKey => $productData) {
            $product = oxNew(Article::class);
            $product->setId($productData['oxid']);
            foreach ($productData as $key => $value) {
                if (str_contains((string)$key, 'ox')) {
                    $field = "oxarticles__$key";
                    $product->{$field} = new Field($value);
                }
            }
            $product->save();
            if (isset($productData['scaleprices']) && $productData['scaleprices']) {
                $this->createScalePrices([$productData['scaleprices']]);
            }
            $result[$outerKey]['id'] = $productData['oxid'];
            $result[$outerKey]['amount'] = $productData['amount'] ?? null;
        }

        return $result;
    }

    private function createScalePrices(array $scalePrices): void
    {
        $this->createObj2Obj($scalePrices, 'oxprice2article');
    }

    public function createDiscounts(array $discounts): void
    {
        foreach ($discounts as $discountData) {
            // add discounts
            $discount = oxNew(Discount::class);
            $discount->setId($discountData['oxid']);
            foreach ($discountData as $key => $value) {
                if (!is_array($value)) {
                    $field = 'oxdiscount__' . $key;
                    $discount->{$field} = new Field((string)($value));
                } else {
                    foreach ($value as $id) {
                        $aData = [
                            'oxid' => $discount->getId() . '_' . $id,
                            'oxdiscountid' => $discount->getId(),
                            'oxobjectid' => $id,
                            'oxtype' => $key,
                        ];
                        $this->createObj2Obj($aData, 'oxobject2discount');
                    }
                }
            }
            $discount->save();
        }
    }

    private function createWrappings(array $wrappings): array
    {
        $result = [];
        foreach ($wrappings as $wrapping) {
            $card = oxNew(Wrapping::class);
            foreach ($wrapping as $key => $value) {
                if (!is_array($value)) {
                    $field = 'oxwrapping__' . $key;
                    $card->{$field} = new Field($value, Field::T_RAW);
                }
            }
            $card->save();
            if (isset($wrapping['oxarticles']) && $wrapping['oxarticles']) {
                foreach ($wrapping['oxarticles'] as $productId) {
                    $result[$productId] = $card->getId();
                }
            } else {
                $result['card'] = $card->getId();
            }
        }

        return $result;
    }

    private function createDeliveryCosts(array $deliveryCosts): ?array
    {
        if (empty($deliveryCosts)) {
            return null;
        }
        $deliveries = [];

        $data = empty($deliveryCosts['oxdeliveryset']) ? [
            'oxactive' => 1,
        ] : $deliveryCosts['oxdeliveryset'];
        $deliverySet = $this->createObj($data, 'oxdeliveryset', 'oxdeliveryset');

        foreach ($deliveryCosts as $deliveryData) {
            $delivery = oxNew(Delivery::class);
            $delivery->save();
            foreach ($deliveryData as $key => $value) {
                if (!is_array($value)) {
                    $field = 'oxdelivery__' . $key;
                    $delivery->{$field} = new Field("{$value}");
                } else {
                    foreach ($value as $sId) {
                        $data = [
                            'oxdeliveryid' => $delivery->getId(),
                            'oxobjectid' => $sId,
                            'oxtype' => $key,
                        ];
                        $this->createObj2Obj($data, 'oxobject2delivery');
                    }
                }
            }
            $delivery->save();
            $deliveries[] = $delivery->getId();
            $data = [
                'oxdelid' => $delivery->getId(),
                'oxdelsetid' => $deliverySet->getId(),
            ];
            $this->createObj2Obj($data, 'oxdel2delset');
        }

        return $deliveries;
    }

    private function createPayments(array $payments): array
    {
        $result = [];
        foreach ($payments as $paymentData) {
            $payment = oxNew(Payment::class);
            foreach ($paymentData as $key => $value) {
                if (!is_array($value)) {
                    $field = 'oxpayments__' . $key;
                    $payment->{$field} = new Field((string)($value));
                }
            }
            $payment->save();
            $result[] = $payment->getId();
        }

        return $result;
    }

    private function createVouchers(array $voucherSeries): array
    {
        $voucherIDs = [];
        foreach ($voucherSeries as $voucherData) {
            $voucherSerie = oxNew(VoucherSerie::class);
            foreach ($voucherData as $key => $value) {
                $field = 'oxvoucherseries__' . $key;
                $voucherSerie->{$field} = new Field($value, Field::T_RAW);
            }
            $voucherSerie->save();
            // inserting vouchers
            for ($i = 1; $i <= $voucherData['voucher_count']; $i++) {
                $data = [
                    'oxreserved' => 0,
                    'oxvouchernr' => md5(uniqid((string)random_int(0, mt_getrandmax()), true)),
                    'oxvoucherserieid' => $voucherSerie->getId(),
                ];
                $voucher = $this->createObj($data, 'oxvoucher', 'oxvouchers');
                $voucherIDs[] = $voucher->getId();
            }
        }

        return $voucherIDs;
    }

    private function getDefaultUserData(): array
    {
        return [
            'oxrights' => 'malladmin',
            'oxactive' => '1',
            'oxusername' => 'admin',
            'oxpassword' => 'f6fdffe48c908deb0f4c3bd36c032e72',
            'oxpasssalt' => '61646D696E',
            'oxcompany' => 'Your Company Name',
            'oxfname' => 'John',
            'oxlname' => 'Doe',
            'oxstreet' => 'Maple Street',
            'oxstreetnr' => '10',
            'oxcity' => 'Any City',
            'oxcountryid' => 'a7c40f631fc920687.20179984',
            'oxzip' => '9041',
            'oxfon' => '217-8918712',
            'oxfax' => '217-8918713',
            'oxstateid' => null,
            'oxaddinfo' => null,
            'oxustid' => null,
            'oxsal' => 'MR',
            'oxustidstatus' => '0',
        ];
    }
}
