<?php
/**
 * Price enter mode: bruto
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: 17,55%
 * Currency rate: 0.55
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
            'oxvat'                    => 17.55,
            'amount'                   => 250,
        ),
    ),
    'expected' => array(
        'articles' => array(
             111 => array( '12,48', '3.120,00' ),
        ),
        'totals' => array(
            'totalBrutto' => '3.120,00',
            'totalNetto'  => '2.654,19',
            'vats' => array(
                '17.55' => '465,81'
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
