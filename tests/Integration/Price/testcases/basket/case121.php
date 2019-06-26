<?php
/**
 * Price enter mode: bruto
 * Price view mode:  neto
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
             111 => array( '20,97', '2.097,00' ),
        ),
        'totals' => array(
            'totalBrutto' => '2.495,43',
            'totalNetto'  => '2.097,00',
            'vats' => array(
                19 => '398,43'
            ),
            'grandTotal'  => '2.495,43'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => true,
                'blShowVATForWrapping' => true,
                'blShowVATForPayCharge' => true,
                'blShowVATForDelivery' => true,
                'sAdditionalServVATCalcMethod' => 'proportional',
        ),
        'activeCurrencyRate' => 1.00,
    ),
);
