<?php
/**
 * Price enter mode: brutto
 * Price view mode:  brutto
 * Product count: 1
 * VAT info: -
 * Currency rate: -
 * Discounts: 1
 * 10abs discount for product(4425)
 * Vouchers: 0
 * Wrapping: -;
 * Gift cart:  -;
 * Costs VAT caclulation rule: -
 * Costs:
 *  1. Payment -
 *  2. Delivery -
 *  3. TS -
 * Actions with basket or order:-
 * Short description:
 * https://bugs.oxid-esales.com/view.php?id=4425
 * @bug #4425
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => '4425',
                'oxprice'                  => 879,
                'amount'                   => 1,
        ),
    ),
    'discounts' => array(
        0 => array(
                'oxid'         => 'discount10euro',
                'oxaddsum'     => 10,
                'oxaddsumtype' => 'abs',
                'oxamount' => 1,
                'oxamountto' => 99999,
                'oxprice' => 0,
                'oxpriceto' => 0,
                'oxactive' => 1,
                'oxarticles' => array( 4425 ),
                'oxsort' => 10,
        ),
    ),
    'expected' => array(
        'articles' => array(
                '4425' => array( '869,00', '869,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '869,00',
                'totalNetto'  => '730,25',
                'vats' => array(
                        '19' => '138,75',
                ),
                'grandTotal'  => '869,00'
        ),
    ),
    'options' => array(
            'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false
            ),
    )
);
