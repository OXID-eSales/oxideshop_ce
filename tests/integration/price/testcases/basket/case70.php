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
 *  1. "TS080501_500_30_EUR"  "netto" => "0.82", "amount" => "500" ,
 * Wrapping: -;
 * Gift cart:  -;
 * Costs VAT caclulation rule: max 
 * Costs:
 *  1. Payment + 
 *  2. Delivery + 
 *  3. TS -
 * Short description: 
 * Neto-Neto mode. Additiona products Neto-Neto. If product total price is =500€, then is used trusted shop with parameters:
 * "netto" => "0.82", "amount" => "500" ,. 
 */
$aData = array(
    'articles' => array (
        0 => array (
            'oxid'                     => 9001,
            'oxprice'                  => 10,
            'oxvat'                    => 19,
            'amount'                   => 50,
        ),
   
    ),
    'trustedshop' => array (
        'product_id'     => 'TS080501_500_30_EUR',           // trusted shop product id
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
             9001 => array ( '10,00', '500,00' ),
        ),
        'totals' => array (
            'totalBrutto' => '595,00',
            'totalNetto'  => '500,00',
            'vats' => array (
                19 => '95,00'
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
                'brutto' => '0,98',
                'netto' => '0,82',
                'vat' => '0,16'
            ),
            'grandTotal'  => '619,78'
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