<?php
/**
 * Price enter mode: brutto
 * Price view mode: brutto
 * Product count: 2
 * VAT info: used VAT =20% and VAT = 10%
 * Currency rate: -
 * Discounts: 3
 *  1.  20% discount for product 333
 *  2.  50% discount for product 444,
 *  4.  20% discount for basket
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Short description:
 * Example test case from Basket calculation - brutto mode doc.
 * 2 items in basket, 2 differenct vats, 3 different discounts
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 333,
                'oxtitle'                  => 'item1',
                'oxprice'                  => 60,
                'oxvat'                    => 20,
                'amount'                   => 2,
        ),
        1 => array(
                'oxid'                     => 444,
                'oxtitle'                  => 'item2',
                'oxprice'                  => 110,
                'oxvat'                    => 10,
                'amount'                   => 1,
        ),
    ),
    'discounts' => array(
        0 => array(
                'oxid'         => 'discount20',
                'oxaddsum'     => 20,
                'oxaddsumtype' => '%',
                'oxamount' => 1,
                'oxactive' => 1,
                'oxarticles' => array( 333 ),
                'oxsort' => 10,
        ),
        1 => array(
                'oxid'         => 'discount50',
                'oxaddsum'     => 50,
                'oxaddsumtype' => '%',
                'oxamount' => 1,
                'oxactive' => 1,
                'oxarticles' => array( 444 ),
                'oxsort' => 20,
        ),
        2 => array(
                'oxid' => 'discount20forBasket',
                'oxaddsum' => 20,
                'oxaddsumtype' => '%',
                'oxamount' => 1,
                'oxactive' => 1,
                'oxsort' => 30,
        ),
    ),
    'costs' => array(),
    'expected' => array(
        'articles' => array(
                // article id => [ unit price, total price = unit * amount - discounts ]
                333 => array( '48,00', '96,00' ),
                444 => array( '55,00', '55,00' )
        ),
        'totals' => array(
                'totalBrutto' => '151,00',
                'discounts' => array(
                        'discount20forBasket' => '30,20',
                ),
                'totalNetto'  => '104,00',
                'vats' => array(
                        '20' => '12,80',
                        '10' => '4,00'
                ),
                'grandTotal'  => '120,80'
        ),
    ),
        'options' => array(
                'insertMode' => 'brutto',
                'viewMode' => 'brutto',
        )
);
