<?php
/*
/**
 * Price enter mode: netto
 * Price view mode:  brutto
 * Product count: 2
 * VAT info: 19% Default VAT for all Products
 * Currency rate: 0.68
 * Discounts: 5
 *  1. shop discount 5abs for product 9007
 *  2. shop discount 5% for product 9008
 *  3. basket discount 1 abs for product 9007
 *  4. basket discount 6% for product 9008
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
            'oxid'                     => 9007,
            'oxprice'                  => 100,
            'oxvat'                    => 19,
            'amount'                   => 33,
        ),
        1 => array(
            'oxid'                     => 9008,
            'oxprice'                  => 66,
            'oxvat'                    => 19,
            'amount'                   => 16,
        ),
    ),
    'discounts' => array(
        0 => array(
            'oxid'         => 'shopdiscount5for9007',
            'oxaddsum'     => 5,
            'oxaddsumtype' => 'abs',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9007 ),
            'oxsort' => 10,
        ),
        1 => array(
            'oxid'         => 'shopdiscount5for9008',
            'oxaddsum'     => 5,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9008 ),
            'oxsort' => 20,
        ),
        2 => array(
            'oxid'         => 'basketdiscount5for9007',
            'oxaddsum'     => 1,
            'oxaddsumtype' => 'abs',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9007 ),
            'oxsort' => 30,
        ),
        3 => array(
            'oxid'         => 'basketdiscount5for9008',
            'oxaddsum'     => 6,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9008 ),
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
                'oxname' => 'testWrap9007',
                'oxprice' => 9,
                'oxactive' => 1,
                'oxarticles' => array( 9007 )
            ),
            1 => array(
                'oxtype' => 'WRAP',
                'oxname' => 'testWrap9008',
                'oxprice' => 6,
                'oxactive' => 1,
                'oxarticles' => array( 9008 )
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
             9007 => array( '76,84', '2.535,72' ),
             9008 => array( '47,70', '763,20' ),
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
        ),
        'activeCurrencyRate' => 0.68,
    ),
);
