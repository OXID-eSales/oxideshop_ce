<?php
/**
 * Price enter mode: bruto
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: 19%
 * Currency rate: 0.68
 * Discounts: count
 *  1. bascet 5 abs
 *  2. shop 5 abs for 9001
 *  3. bascet 1 abs for 9001
 *  4. shop 5% for 9002
 *  5. bascet 6% for 9002
 * Vouchers: count
 *  1. 6 abs
 * Wrapping: +;
 * Gift cart:  -;
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS -
 * Actions with basket or order:
 *  1. update / delete / change config
 *  2. ...
 *  ...
 * Short description: bug entry / support case other info;
 */
$aData = array(
    'articles' => array(
        0 => array(
            'oxid'                     => 9001,
            'oxprice'                  => 100,
            'oxvat'                    => 19,
            'amount'                   => 33,
        ),
        1 => array(
            'oxid'                     => 9002,
            'oxprice'                  => 66,
            'oxvat'                    => 19,
            'amount'                   => 16,
        ),
    ),
    'discounts' => array(
        0 => array(
            'oxid'         => 'shopdiscount5for9001',
            'oxaddsum'     => 5,
            'oxaddsumtype' => 'abs',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9001 ),
            'oxsort' => 10,
        ),
        1 => array(
            'oxid'         => 'shopdiscount5for9002',
            'oxaddsum'     => 5,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9002 ),
            'oxsort' => 20,
        ),
        2 => array(
            'oxid'         => 'basketdiscount5for9001',
            'oxaddsum'     => 1,
            'oxaddsumtype' => 'abs',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9001 ),
            'oxsort' => 30,
        ),
        3 => array(
            'oxid'         => 'basketdiscount5for9002',
            'oxaddsum'     => 6,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9002 ),
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
                'oxname' => 'testWrap9001',
                'oxprice' => 9,
                'oxactive' => 1,
                'oxarticles' => array( 9001 )
            ),
            1 => array(
                'oxtype' => 'WRAP',
                'oxname' => 'testWrap9002',
                'oxprice' => 6,
                'oxactive' => 1,
                'oxarticles' => array( 9002 )
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
                'oxarticles' => array( 9001, 9002 ),
            ),
        ),
        'voucherserie' => array(
            0 => array(
                'oxserienr' => 'abs_4_voucher_serie',
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
             9001 => array( '63,92', '2.109,36' ),
             9002 => array( '40,08', '641,28' ),
        ),
        'totals' => array(
            'totalBrutto' => '2.750,64',
            'totalNetto'  => '2.305,18',
            'vats' => array(
                19 => '437,98'
            ),
            'discounts' => array(
                'absolutebasketdiscount' => '3,40',
            ),
            'wrapping' => array(
                'brutto' => '267,24',
                'netto' => '224,57',
                'vat' => '42,67'
            ),
            'delivery' => array(
                'brutto' => '4,08',
                'netto' => '3,43',
                'vat' => '0,65'
            ),
            'payment' => array(
                'brutto' => '0,68',
                'netto' => '0,57',
                'vat' => '0,11'
            ),
            'voucher' => array(
                'brutto' => '4,08',
            ),
            'grandTotal'  => '3.015,16'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
                'blShowVATForWrapping' => true,
                'blShowVATForPayCharge' => true,
                'blShowVATForDelivery' => true,
                'sAdditionalServVATCalcMethod' => 'biggest_net',
        ),
        'activeCurrencyRate' => 0.68,
    ),
);
