<?php
/*
/**
 * Price enter mode: netto
 * Price view mode:  brutto
 * Product count: 2
 * VAT info: 19% Default VAT for all Products
 * Currency rate: 1.0
 * Discounts: 5
 *  1. shop discount 5abs for product 9005
 *  2. shop discount 5% for product 9006
 *  3. basket discount 1 abs for product 9005
 *  4. basket discount 6% for product 9006
 *  5. absolute basket discount 5 abs

 * Vouchers: 1
 *  1.  vouchers 6.00 abs

 * Wrapping: +
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS -
 * Short description:
 * From advBasketCalc.csv: Complex order calculation IV order.
 */
$aData = array(
    'articles' => array(
        0 => array(
            'oxid'                     => 9005,
            'oxprice'                  => 100,
            'oxvat'                    => 19,
            'amount'                   => 33,
        ),
        1 => array(
            'oxid'                     => 9006,
            'oxprice'                  => 66,
            'oxvat'                    => 19,
            'amount'                   => 16,
        ),
    ),
    'discounts' => array(
        0 => array(
            'oxid'         => 'shopdiscount5for9005',
            'oxaddsum'     => 5,
            'oxaddsumtype' => 'abs',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9005 ),
            'oxsort' => 10,
        ),
        1 => array(
            'oxid'         => 'shopdiscount5for9006',
            'oxaddsum'     => 5,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9006 ),
            'oxsort' => 20,
        ),
        2 => array(
            'oxid'         => 'basketdiscount5for9005',
            'oxaddsum'     => 1,
            'oxaddsumtype' => 'abs',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9005 ),
            'oxsort' => 30,
        ),
        3 => array(
            'oxid'         => 'basketdiscount5for9006',
            'oxaddsum'     => 6,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9006 ),
            'oxsort' => 40,
        ),
        4 => array(
            'oxid'         => 'absolutebasketdiscount',
            'oxaddsum'     => 5,
            'oxaddsumtype' => 'abs',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxsort' => 50,
        ),
    ),
    'costs' => array(
        'wrapping' => array(
            0 => array(
                'oxtype' => 'WRAP',
                'oxname' => 'testWrap9005',
                'oxprice' => 9,
                'oxactive' => 1,
                'oxarticles' => array( 9005 )
            ),
            1 => array(
                'oxtype' => 'WRAP',
                'oxname' => 'testWrap9006',
                'oxprice' => 6,
                'oxactive' => 1,
                'oxarticles' => array( 9006 )
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
             9005 => array( '113,00', '3.729,00' ),
             9006 => array( '70,13', '1.122,08' ),
        ),
        'totals' => array(
            'totalBrutto' => '4.851,08',
            'totalNetto'  => '4.067,29',
            'vats' => array(
                19 => '772,79'
            ),
            'discounts' => array(
                'absolutebasketdiscount' => '5,00',
            ),
            'wrapping' => array(
                'brutto' => '393,00',
                'netto' => '330,25',
                'vat' => '62,75'
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
            'grandTotal'  => '5.240,08'
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
