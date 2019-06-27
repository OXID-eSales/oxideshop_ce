<?php
/**
 * Price enter mode: brutto;
 * Price view mode: brutto;
 * Product count: 3;
 * VAT info:  count of used vat =2(19% and additional Vat for product 10%);
 * Currency rate:1;
 * Discounts: 1
 * 1. 5 abs discount for products( 2000, '_tArticle')
 * Wrapping:  -;
 * Gift cart: -;
 * Costs VAT caclulation rule: -;
 * Wrapping: -;
 * Gift cart:  -;
 * Vouchers: -;
 * Costs:
 *  1. Payment -;
 *  2. Delivery- ;
 *  3. TS -
 * Short description:
 * Brutto-Brutto mode.
 * Testing basket item price calculation, if only two from three articles have discounts
 */

$aData = array(
        'articles' => array(
                0 => array(
                        'oxid'        => '_tArticle',
                        'oxprice'     => 50,
                        'oxweight'    => 10,
                        'oxstock'     => 100,
                        'oxstockflag' => 2,
                        'oxvat'       => 10,
                        'amount'      => 2,
                ),
                1 => array(
                        'oxid'        => 2000,
                        'oxprice'     => 29.9,
                        'oxtitle'     => "Wall Clock ROBOT",
                        'oxstock'     => 3,
                        'oxstockflag' => 1,
                        'amount'      => 1,
                ),
                2 => array(
                        'oxid'        => '_t1651',
                        'oxprice'     => 29.9,
                        'oxtitle'     => "Beer homebrew kit CHEERS!",
                        'oxstock'     => 10000,
                        'oxstockflag' => 1,
                        'amount'      => 1,
                ),
        ),
        'discounts' => array(
                0 => array(
                        'oxid'        => 'testdiscount0',
                        'oxactive'    => 1,
                        'oxtitle'     => 'Test discount 0',
                        'oxamount'    => 1,
                        'oxamountto'    => 99999,
                        'oxprice'     => 1,
                        'oxpriceto'   => 99999,
                        'oxaddsumtype'=> 'abs',
                        'oxaddsum'    => 5,
                        'oxarticles'  => array( 2000, '_tArticle' ),
                        'oxsort' => 10,
                ),
        ),
        'expected' => array(
                'articles' => array(
                        '_tArticle' => array( '45,00', '90,00' ),
                        2000          => array( '24,90', '24,90' ),
                        '_t1651'          => array( '29,90', '29,90' ),
                ),
                'totals' => array(
                        'totalBrutto' => '144,80',
                        'totalNetto'  => '127,87',
                        'vats' => array(
                                19 => '8,75',
                                10 => '8,18'
                        ),
                        'discounts' => array(
                        ),
                        'grandTotal'  => '144,80'
                ),
        ),
        'options' => array(
                'config' => array(
                        'blEnterNetPrice' => false,
                        'blShowNetPrice' => false,
                ),
                'activeCurrencyRate' => 1,
        ),
);
