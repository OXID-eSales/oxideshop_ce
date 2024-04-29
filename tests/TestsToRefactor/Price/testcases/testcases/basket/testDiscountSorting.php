<?php
/*
/**
 * Price enter mode: netto
 * Price view mode:  brutto
 * Product count: 2
 * VAT info: 19% Default VAT for all Products
 * Currency rate: 1.0
 * Discounts: 5
 *  1. shop discount 5.5% for product 10005
 *  2. shop discount 5% for product 1004
 *  3. basket discount 5 abs for product 10005
 *  4. basket discount 6% for product 1004
 *  5. absolute basket discount 5 abs
 *  6. shop discount 5abs for product 10005

 * Vouchers: 1
 *  1.  vouchers 6.00 abs

 * Wrapping: +
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS -
 * Short description:
 * Use 7 different discounts
 *
 *
 * NOTE: this is a copy of case23 with changed discount order (oxdiscount.oxsort) leading to
 *       different end results.
 */
$aData = array(
    'articles' => array(
        0 => array(
            'oxid'                     => 10005,
            'oxprice'                  => 1001,
            'oxvat'                    => 19,
            'amount'                   => 1,
        ),
        1 => array(
            'oxid'                     => 1004,
            'oxprice'                  => 0.5,
            'oxvat'                    => 19,
            'amount'                   => 1,
        ),
    ),

    'discounts' => array(
        0 => array(
            'oxid'         => 'shopdiscount5for10005',
            'oxaddsum'     => 5.5,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 10005 ),
            'oxsort' => 70,
        ),
        1 => array(
            'oxid'         => 'shopdiscount5for1004',
            'oxaddsum'     => 5,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 1004 ),
            'oxsort' => 20,
        ),
        2 => array(
            'oxid'         => 'basketdiscount5for10005',
            'oxaddsum'     => 5,
            'oxaddsumtype' => 'abs',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 10005 ),
            'oxsort' => 60,
        ),
        3 => array(
            'oxid'         => 'basketdiscount5for1004',
            'oxaddsum'     => 6,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 1004 ),
            'oxsort' => 30,
        ),
        4 => array(
            'oxid'         => 'absolutebasketdiscount',
            'oxaddsum'     => 5,
            'oxaddsumtype' => 'abs',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxsort' => 10,
        ),
        5 => array(
            'oxid'         => 'procdiscountfor10005',
            'oxaddsum'     => 5,
            'oxaddsumtype' => 'abs',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 10005 ),
            'oxsort' => 40,
        ),
        6 => array(
            'oxid'         => 'procdiscountfor1004',
            'oxaddsum'     => -10,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array(  1004 ),
            'oxsort' => 50,
        ),

    ),
    'costs' => array(
        'wrapping' => array(
            0 => array(
                'oxtype' => 'WRAP',
                'oxname' => 'testWrap102',
                'oxprice' => 9,
                'oxactive' => 1,
                'oxarticles' => array( 10005 )
            ),
            1 => array(
                'oxtype' => 'WRAP',
                'oxname' => 'testWrap1002',
                'oxprice' => 6,
                'oxactive' => 1,
                'oxarticles' => array( 1004 )
            ),
        ),
        'delivery' => array(
            0 => array(
                'oxtitle' => '6_abs_del',
                'oxactive' => 1,
                'oxaddsum' => 6,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparamend' => 99999
            ),
        ),
        'payment' => array(
            0 => array(
                'oxtitle' => '1 abs payment',
                'oxaddsum' => 1,
                'oxaddsumtype' => 'abs',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
            ),
        ),
        'voucherserie' => array(
            0 => array(
                'oxdiscount' => 6.00,
                'oxdiscounttype' => 'absolute',
                'oxallowsameseries' => 1,
                'oxallowotherseries' => 1,
                'oxallowuseanother' => 1,
                'voucher_count' => 1
            ),
        ),
    ),
    'expected' => array(
        'articles' => array(
            10005 => array( '1.115,95', '1.115,95' ),
            1004 => array( '0,59', '0,59' ),
        ),
        'totals' => array(
            'totalBrutto' => '1.116,54',
            'totalNetto'  => '929,03',
            'vats' => array(
                19 => '176,51'
            ),
            'discounts' => array(
                'absolutebasketdiscount' => '5,00',
            ),
            'wrapping' => array(
                'brutto' => '15,00',
                'netto' => '12,60',
                'vat' => '2,40',
            ),
            'delivery' => array(
                'brutto' => '6,00',
                'netto' => '5,04',
                'vat' => '0,96'
            ),
            'payment' => array(
                'brutto' => '1,00',
                'netto' => '0,84',
                'vat' => '0,16'
            ),
            'voucher' => array(
                'brutto' => '6,00',
            ),
            'grandTotal'  => '1.127,54'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => true,
                'blShowNetPrice' => false,
                'blShowVATForWrapping' => true,
                'blShowVATForPayCharge' => true,
                'blShowVATForDelivery' => true,
        ),
        'activeCurrencyRate' => 1.00,
    ),
);
