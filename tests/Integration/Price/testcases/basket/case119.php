<?php
/**
 * Price enter mode: bruto
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: 25%
 * Currency rate: 0.50
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
            'oxvat'                    => 25,
            'amount'                   => 250,
        ),
    ),
    'expected' => array(
        'articles' => array(
             111 => array( '12,48', '3.120,00' ),
        ),
        'totals' => array(
            'totalBrutto' => '3.120,00',
            'totalNetto'  => '2.496,00',
            'vats' => array(
                25 => '624,00'
            ),
            'grandTotal'  => '3.120,00'
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
        'activeCurrencyRate' => 0.50,
    ),
);
