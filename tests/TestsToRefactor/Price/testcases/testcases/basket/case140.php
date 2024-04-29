<?php
/*
 * Price enter mode: brutto;
 * Price view mode: brutto;
 * Product count: 3;
 * VAT info:  count of used vat =1(19%);
 * Currency rate:1;
 * Discounts: 1
 * 1. 3 abs discount
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
 * #1456: Discount validity is wrong if article in basket has Scale Prices
 */
$aData = array(
    'articles' => array(
        0 => array(
            'oxid'       => 'testarticle',
            'oxprice'    => 12.95,
            'amount'     => 1,
            'scaleprices' => array(
                    'oxaddabs'     => 11.95,
                    'oxamount'     => 2,
                    'oxamountto'   => 2,
                    'oxartid'      => 'testarticle'
            ),
        ),
    ),
    'discounts' => array(
        0 => array(
            'oxid'         => '_testDiscount',
            'oxactive'     => 1,
            'oxtitle'      => 'new discount',
            'oxprice'      => 12,
            'oxpriceto'    => 24.99,
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => 3,
            'oxsort'       => 10,
        ),
    ),
    'expected' => array(
        'articles' => array(
             'testarticle' => array( '12,95', '12,95' ),
        ),
        'totals' => array(
            'totalBrutto' => '12,95',
            'totalNetto'  => '8,36',
            'vats' => array(
                19 => '1,59'
            ),
            'discounts' => array(
                    '_testDiscount' => '3,00'
                ),
            'grandTotal'  => '9,95'
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
