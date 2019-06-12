<?php
/**
 * Price enter mode: neto
 * Price view mode:  bruto
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
            'oxid'                     => 9003,
            'oxprice'                  => 100,
            'oxvat'                    => 19,
            'amount'                   => 33,
        ),
        1 => array(
            'oxid'                     => 9004,
            'oxprice'                  => 66,
            'oxvat'                    => 19,
            'amount'                   => 16,
        ),
    ),
    'discounts' => array(
        0 => array(
            'oxid'         => 'shopdiscount5for9003',
            'oxaddsum'     => 5,
            'oxaddsumtype' => 'abs',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9003 ),
            'oxsort' => 10,
        ),
        1 => array(
            'oxid'         => 'shopdiscount5for9004',
            'oxaddsum'     => 5,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9004 ),
            'oxsort' => 20,
        ),
        2 => array(
            'oxid'         => 'basketdiscount5for9003',
            'oxaddsum'     => 1,
            'oxaddsumtype' => 'abs',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9003 ),
            'oxsort' => 30,
        ),
        3 => array(
            'oxid'         => 'basketdiscount5for9004',
            'oxaddsum'     => 6,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9004 ),
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
                'oxname' => 'testWrap9003',
                'oxprice' => 9,
                'oxactive' => 1,
                'oxarticles' => array( 9003 )
            ),
            1 => array(
                'oxtype' => 'WRAP',
                'oxname' => 'testWrap9003',
                'oxprice' => 6,
                'oxactive' => 1,
                'oxarticles' => array( 9004 )
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
                'oxarticles' => array( 9003, 9004 ),
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
             9003 => array( '76,84', '2.535,72' ),
             9004 => array( '47,70', '763,20' ),
        ),
        'totals' => array(
            'totalBrutto' => '3.298,92',
            'totalNetto'  => '2.765,92',
            'vats' => array(
                19 => '525,52'
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
            'grandTotal'  => '3.563,44'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => true,
                'blShowNetPrice' => false,
                'blShowVATForWrapping' => true,
                'blShowVATForPayCharge' => true,
                'blShowVATForDelivery' => true,
                'sAdditionalServVATCalcMethod' => 'biggest_net',
        ),
        'activeCurrencyRate' => 0.68,
    ),
);
