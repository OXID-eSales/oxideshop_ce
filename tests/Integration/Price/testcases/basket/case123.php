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
            'amount'                   => 100,
        ),
    ),
    'expected' => array(
        'articles' => array(
             111 => array( '10,49', '1.049,00' ),
        ),
        'totals' => array(
            'totalBrutto' => '1.248,31',
            'totalNetto'  => '1.049,00',
            'vats' => array(
                19 => '199,31'
            ),
            'grandTotal'  => '1.248,31'
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
