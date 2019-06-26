<?php
/*
/**
 * Price enter mode: brutto
 * Price view mode:  brutto
 * Product count: 1
 * VAT info: 19% Default VAT for all Products
 * Currency rate: 1.0
 * Discounts: -
 * Vouchers: -
 * Wrapping: -
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery -
 *  3. TS -
 * Short description:
 * user with diferent foreign country, adn vat should not be calculated
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 9202,
                'oxprice'                  => 119,
                'oxvat'                    => 19,
                'amount'                   => 1,
        ),
    ),
     // User
    'user' => array(
            'oxactive' => 1,
            'oxusername' => 'basketUser',
            // country id, for example this is United States, make sure country with specified ID is active
            'oxcountryid' => '8f241f11096877ac0.98748826',
    ),
    'expected' => array(
        'articles' => array(
                 9202 => array( '100,00', '100,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '100,00',
                'totalNetto'  => '100,00',
                'vats' => array(
                    0 => '0,00',
                ),
                'grandTotal'  => '100,00'
        ),
    ),

    'options' => array(
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false,
        ),
        'activeCurrencyRate' => 1,
    ),
);
