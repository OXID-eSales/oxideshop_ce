<?php
/*
/**
 * Price enter mode: bruto
 * Price view mode:  brutto
 * Product count: 4
 * VAT info: 19%; 10%,l5%
 * Currency rate: 1.0
 * Discounts: 5
 *  1. category discount 5 abs
 *  2. product discount -10%  for 1002 and1003
 * Vouchers: -;
 * Wrapping: -;
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery -
 * from seleniums
 */
$aData = array(
    'articles' => array(
        0 => array(
            'oxid'                     => 1245,
            'oxprice'                  => 98,
            'oxvat'                    => 10,
            'amount'                   => 1,
        ),
        1 => array(
            'oxid'                     => 6565,
            'oxprice'                  => 67,
            'oxvat'                    => 19,
            'amount'                   => 1,
        ),
        2 => array(
            'oxid'                     => 1553,
            'oxprice'                  => 60,
            'oxvat'                    => 19,
            'amount'                   => 6,
        ),
        3 => array(
            'oxid'                     => 1224,
            'oxprice'                  => 50,
            'oxvat'                    => 5,
            'amount'                   => 1,
        ),
    ),

    'discounts' => array(
        0 => array(
            'oxid'         => 'product',
            'oxaddsum'     => 10,
            'oxaddsumtype' => '%',
            'oxprice' => 0,
            'oxpriceto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 6565, 1553 ),
            'oxsort' => 10,
        ),
        1 => array(
            'oxid'         => 'prod2',
            'oxaddsum'     => 5,
            'oxaddsumtype' => 'abs',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxprice' => 0,
            'oxpriceto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 1224, 1245 ),
            'oxsort' => 20,
        ),
    ),

      // Additional costs
    'costs' => array(
        // VOUCHERS
        'voucherserie' => array(
            0 => array(
                // oxvoucherseries DB fields
                'oxdiscount' => 5,
                'oxdiscounttype' => '%',
                'oxallowsameseries' => 1,
                'oxallowotherseries' => 1,
                'oxallowuseanother' => 1,
                // voucher of this voucherserie count
                'voucher_count' => 1
            ),
        ),
    ),
    'expected' => array(
        'articles' => array(
             6565 => array( '60,30', '60,30' ),
             1553 => array( '54,00', '324,00' ),
             1224 => array( '45,00', '45,00' ),
             1245 => array( '93,00', '93,00' ),
        ),

        'totals' => array(
            'totalBrutto' => '522,30',
            'totalNetto'  => '427,82',
            'vats' => array(
                10 => '8,03',
                19 => '58,29',
                5 => '2,04',
            ),
            // Total voucher amounts
            'voucher' => array(
            'brutto' => '26,12',
        ),
            'grandTotal'  => '496,18'

    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
                'blShowVATForWrapping' => true,
                'blShowVATForPayCharge' => false,
                'blShowVATForDelivery' => true,
        ),
        'activeCurrencyRate' => 1.00,
    ),
    ),
);
