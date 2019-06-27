<?php
/**
 * Price enter mode: brutto
 * Price view mode: brutto
 * Product count: 2
 * VAT info: -
 * Currency rate: 1.0
 * Discounts: 1
 * 1. 3 abs if product price (12eur-24.99eur)
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Scale price:
 * 1. for product (1001), if product amount(2-2), then product price for product is 11.95eur.
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery -
 *  3. TS  -
 * Short description:
 * Calculate scale price.
 * Brutto-Brutto mode.
 */
$aData = array(
    'articles' => array(
        0 => array(
            'oxid'       => 'testarticle',
            'oxprice'    => 12.00,
            'amount'     => 2,
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
             'testarticle' => array( '11,95', '23,90' ),
        ),
        'totals' => array(
            'totalBrutto' => '23,90',
            'totalNetto'  => '17,56',
            'vats' => array(
                19 => '3,34'
            ),
            'discounts' => array(
                    '_testDiscount' => '3,00'
                ),
            'grandTotal'  => '20,90'
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
