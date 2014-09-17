<?php
/**
 * Price enter mode: netto 
 * Price view mode:  netto
 * Product count: count of used products
 * VAT info: 19%
 * Currency rate: 1 
 * Discounts: -
 * Vouchers: -
 * Trusted Shop:
 *  1. "TS080501_5000_30_EUR"  "netto" => "8.24", "amount" => "5000" ,
 * Wrapping: -;
 * Gift cart:  -;
 * Costs VAT caclulation rule: max 
 * Costs:
 *  1. Payment + 
 *  2. Delivery + 
 *  3. TS -
 * Short description: 
 * Neto-Neto mode. Additiona products Neto-Neto. Testing trusted shop . If product total price is >5000€, then is used trusted shop with parameters:
 * "netto" => "8.24", "amount" => "5000" ,. 
 */
$aData = array(
    'articles' => array (
        0 => array (
            'oxid'                     => 9001,
            'oxprice'                  => 10,
            'oxvat'                    => 19,
            'amount'                   => 510,
        ),
   
    ),
    'trustedshop' => array (
        'product_id'     => 'TS080501_5000_30_EUR',           // trusted shop product id
        'payments'    => array(                              // paymentids
            'oxidcashondel'  => 'DIRECT_DEBIT',
            'oxidcreditcard' => 'DIRECT_DEBIT',
            'oxiddebitnote'  => 'DIRECT_DEBIT',
            'oxidpayadvance' => 'DIRECT_DEBIT',
            'oxidinvoice'    => 'DIRECT_DEBIT',
            'oxempty'        => 'DIRECT_DEBIT',
        )
    ),
    'costs' => array(
  
        'delivery' => array(
            0 => array(
                'oxtitle' => '6_abs_del',
                'oxactive' => 1,
                'oxaddsum' => 10,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparamend' => 99999
            ),
        ),
        'payment' => array(
            0 => array(
                'oxtitle' => '1 abs payment',
                'oxaddsum' => 10,
                'oxaddsumtype' => 'abs',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
            ),
        ),
    ),
    'expected' => array (
        'articles' => array (
             9001 => array ( '10,00', '5.100,00' ),
        ),
        'totals' => array (
            'totalBrutto' => '6.069,00',
            'totalNetto'  => '5.100,00',
            'vats' => array (
                19 => '969,00'
            ),
            'delivery' => array(
                'brutto' => '11,90',
                'netto' => '10,00',
                'vat' => '1,90'
            ),
            'payment' => array(
                'brutto' => '11,90',
                'netto' => '10,00',
                'vat' => '1,90'
            ),
            'trustedshop' => array(
                'brutto' => '9,81',
                'netto' => '8,24',
                'vat' => '1,57'
            ),
            'grandTotal'  => '6.102,61'
        ),
    ),
    'options' => array (
        // Configs (real named)
        'config' => array(
            'blEnterNetPrice' => true,
            'blShowNetPrice' => true,
            'blShowVATForDelivery'=> true,
            'blShowVATForPayCharge'=> true,
            'blShowVATForWrapping'=> true,
            'sAdditionalServVATCalcMethod' => 'biggest_net', 
            'blDeliveryVatOnTop' => true,
            'blPaymentVatOnTop' => true,
            'blWrappingVatOnTop' => true,
        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);