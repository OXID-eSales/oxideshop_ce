<?php
/**
 * Price enter mode: bruto
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: 19%
 * Currency rate: 1.0
 * Discounts: -
 * Vouchers: -
 * Wrapping: -;
 * Gift cart:  -;
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery -
 *  3. TS +
 */
$aData = array(
    'articles' => array(
        0 => array(
            'oxid'                     => 111,
            'oxprice'                  => 24.95,
            'oxvat'                    => 19,
            'amount'                   => 150,
        ),
        1 => array(
            'oxid'                     => 222,
            'oxprice'                  => 7.99,
            'oxvat'                    => 19,
            'amount'                   => 1,
        ),
    ),
    'expected' => array(
        'articles' => array(
             111 => array( '10,49', '1.573,50' ),
             222 => array( '3,36', '3,36' ),
        ),
        'totals' => array(
            'totalBrutto' => '1.876,46',
            'totalNetto'  => '1.576,86',
            'vats' => array(
                19 => '299,60'
            ),
            'grandTotal'  => '1.876,46'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => true,
                'blShowVATForWrapping' => true,
                'blShowVATForPayCharge' => true,
                'blShowVATForDelivery' => true,
                'sAdditionalServVATCalcMethod' => 'biggest_net',
        ),
        'activeCurrencyRate' => 0.50,
    ),
);
