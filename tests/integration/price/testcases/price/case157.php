<?php
/**
 * Price enter mode: brutto
 * Price view mode: brutto
 * Product count: 1
 * VAT info: 20%
 * Currency rate: 1.0
 * Discounts: -
 * Short description: Brutto-Brutto user group Price C, 
 * Test case is moved from selenium test "testFrontendPriceC"
 */
$aData = array (
        'articles' => array (
                0 => array (
                        'oxid'            => 1000,
                        'oxprice'         => 50.00,
                ),
                1 => array (
                        'oxid'            => 1001,
                        'oxparentid'      => 1000,
                        'oxprice'         => 50.00,
                ),
        ),
        'categories' => array (
                0 =>  array (
                        'oxid'       => 'testCategoryId',
                        'oxparentid' => 'oxrootid',
                        'oxshopid'   => 1,
                        'oxshopincl' => 1,
                        'oxactive'   => 1,
                        'oxarticles' => array( 1001 )
                ),
        ),
        'discounts' => array (
                0 => array (
                        'oxid'         => 'testDiscountId',
                        'oxaddsum'     => 20,
                        'oxaddsumtype' => 'abs',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxcategories' => array ( 'testCategoryId' ),
                ),
        ),
        'expected' => array (
            1001 => array (
                        'base_price'        => '50,00',
                        'price'             => '30,00',
                ),
        ),
        'options' => array (
                'config' => array(
                        'blEnterNetPrice' => false,
                        'blShowNetPrice' => false,
                ),
                'activeCurrencyRate' => 1,
        ),
);