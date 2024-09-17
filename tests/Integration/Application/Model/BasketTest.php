<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Voucher;
use OxidEsales\Eshop\Application\Model\VoucherSerie;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class BasketTest extends IntegrationTestCase
{
    private string $productId = '123';
    private string $voucherSeriesId = 'voucher-series-example';
    private array $vouchers;
    private Basket $basket;

    public function setUp(): void
    {
        parent::setUp();

        $this->createProduct();
        $this->initializeBasket();
    }

    public function testBasketWithGraterVoucherDiscount(): void
    {
        $this->createVoucherSeries(100, 1);
        $this->assignVoucherSeriesToProduct();

        $this->basket->addVoucher($this->vouchers[0]);
        $this->basket->calculateBasket();

        $this->assertEquals(0, $this->basket->getPrice()->getPrice());
    }

    public function testExceedBasketAmountWithAssignedVoucherSeries(): void
    {
        $this->createVoucherSeries(5, 2);
        $this->assignVoucherSeriesToProduct();

        $this->basket->addVoucher($this->vouchers[0]);
        $this->basket->calculateBasket();

        $this->assertGreaterThan(0, $this->basket->getPrice()->getPrice());

        $this->basket->addVoucher($this->vouchers[1]);
        $this->basket->calculateBasket();

        $this->assertEquals(0, $this->basket->getPrice()->getPrice());
    }

    private function createProduct(): void
    {
        $product = oxNew(Article::class);
        $product->setId($this->productId);
        $product->oxarticles__oxstock = new Field(5);
        $product->oxarticles__oxprice  = new Field(7);
        $product->oxarticles__oxstockflag  = new Field(1);
        $product->save();
    }

    private function createVoucherSeries(float $voucherDiscount, int $voucherQuantity): void
    {
        $voucherSeries = oxNew(VoucherSerie::class);
        $voucherSeries->setId($this->voucherSeriesId);
        $voucherSeries->oxvoucherseries__oxdiscount = new Field($voucherDiscount);
        $voucherSeries->oxvoucherseries__oxallowsameseries = new Field(1);
        $voucherSeries->oxvoucherseries__oxallowuseanother = new Field(1);
        $voucherSeries->save();

        for ($i = 0; $i < $voucherQuantity; ++$i) {
            $voucherNumber = sprintf('VOUCHER_%d', $i + 1);
            $voucher = oxNew(Voucher::class);
            $voucher->oxvouchers__oxvoucherserieid = new Field($this->voucherSeriesId);
            $voucher->oxvouchers__oxvouchernr = new Field($voucherNumber);
            $voucher->save();
            $this->vouchers[] = $voucherNumber;
        }
    }

    private function assignVoucherSeriesToProduct(): void
    {
        $object2Discount = oxNew(BaseModel::class);
        $object2Discount->init('oxobject2discount');
        $object2Discount->oxobject2discount__oxdiscountid = new Field($this->voucherSeriesId);
        $object2Discount->oxobject2discount__oxobjectid = new Field($this->productId);
        $object2Discount->oxobject2discount__oxtype = new Field('oxarticles');
        $object2Discount->save();
    }

    private function initializeBasket(): void
    {
        $this->basket = oxNew(Basket::class);
        $this->basket->setSkipVouchersChecking(false);
        Registry::getSession()->setBasket($this->basket);

        $this->basket->addToBasket($this->productId, 1);
    }
}
