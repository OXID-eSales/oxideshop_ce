<?php
/**
 * Price enter mode: brutto;
 * Price view mode: brutto;
 * Product count: 1;
 * VAT info:  count of used vat =1(19% );
 * Currency rate:1-;
 * Discounts: 1
 * 1. 5 abs discount for basket
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
 * Testing basket item price calculator also sets items discounts
 */
$aData = array(
        'articles' => array(
                0 => array(
                        'oxid'        => 'testarticle',
                        'oxprice'     => 19,
                        'oxweight'    => 10,
                        'oxstock'     => 100,
                        'oxstockflag' => 2,
                        'oxskipdiscounts' => 1,
                        'amount'      => 1,
                        'scaleprices' => array(),
                ),
        ),
        'discounts' => array(
                0 => array(
                        'oxid'        => 'testdiscount0',
                        'oxactive'    => 1,
                        'oxtitle'     => 'Test discount 0',
                        'oxamount'    => 1,
                        'oxamountto'  => 99999,
                        'oxprice'     => 1,
                        'oxpriceto'   => 99999,
                        'oxaddsumtype'=> 'abs',
                        'oxaddsum'    => 5,
                        'oxsort'      => 10,
                ),
                1 => array(
                        'oxid'        => 'testdiscount1',
                        'oxactive'    => 1,
                        'oxtitle'     => 'Test discount 1',
                        'oxamount'    => 1,
                        'oxamountto'  => 99999,
                        'oxprice'     => 1,
                        'oxpriceto'   => 99999,
                        'oxaddsumtype'=> 'abs',
                        'oxaddsum'    => 7,
                        'oxsort'      => 20,
                ),
        ),
        'expected' => array(
                'articles' => array(
                        'testarticle' => array( '19,00', '19,00' ),
                ),
                'totals' => array(
                        'totalBrutto' => '19,00',
                        'totalNetto'  => '15,97',
                        'vats' => array(
                                19 => '3,03'
                        ),
                        'discounts' => array(
                                //in this case discounts are skipped
                        ),
                        'grandTotal'  => '19,00'
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
