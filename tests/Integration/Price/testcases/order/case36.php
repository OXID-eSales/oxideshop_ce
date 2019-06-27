<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 2
 * VAT info: count of used vat's =19%
 * Currency rate: 1.0
 * Discounts: 0
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -
 * Short description:
 * Calculate VAT according to the max value.
 * For products (1001, 1002) is set parameter "free shipping" ;
 * Netto - Netto start case, after order saving,
 * Add additional article(1003) updating,
*/
#bug
$aData = array(
  'skipped' => 1,
    // Articles
    'articles' => array(
        0 => array(
                // oxarticles db fields
                'oxid'                     => 1001,
                'oxprice'                  => 20.00,
                'oxvat'                    => 19,
                // Amount in basket
                'amount'                   => 1,
                'oxfreeshipping'        => 1,
  
        ),
        1 => array(
         // oxarticles db fields
                'oxid'                  => 1002,
                'oxprice'               => 200.00,
                'oxvat'                 => 19,
                // Amount in basket
                'amount'                => 1,
              //  'oxfreeshipping'        => 1,
                

        ),
     ),
 
    // Additional costs
    'costs' => array(

        // Delivery
        'delivery' => array(
              0 => array(
                // oxdelivery DB fields
                'oxactive' => 1,
                'oxaddsum' => 10.00,
                'oxaddsumtype' => '%',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparamend' => 99999,
            ),
        ),
        // Payment
        'payment' => array(
             0 => array(
                // oxpayments DB fields
                'oxaddsum' => 10.00,
                'oxaddsumtype' => '%',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
                'oxaddsumrules'=>1,
            ),
        ),
    ),
    // TEST EXPECTATIONS
    'expected' => array(
        1 => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
             1001 => array( '20,00', '20,00' ),
             1002 => array( '200,00', '200,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '261,80',
            // Total NETTO
            'totalNetto'  => '220,00',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '41,80',
            ),
            // Total discount amounts: discount id => total cost
                // Expectation for special discount with specified ID
                'discount'  => '0,00',
           
      
            // Total delivery amounts
            'delivery' => array(
                'brutto' => '23,80',
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '26,18',
            ),
  
            // GRAND TOTAL
            'grandTotal'  => '311,78'
        ),
        ),
        2 => array(
           // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
             1001 => array( '20,00', '20,00' ),
             1002 => array( '200,00', '200,00' ),
             1003 => array( '2,00', '2,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '264,18',
            // Total NETTO
            'totalNetto'  => '222,00',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '42,18',
            ),
            // Total discount amounts: discount id => total cost
                // Expectation for special discount with specified ID
                'discount'  => '0,00',

            // Total delivery amounts
            'delivery' => array(
                'brutto' => '24,04',
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '26,42',
            ),
  
            // GRAND TOTAL
            'grandTotal'  => '314,64'
        ),
        ),
    ),
    // Test case options
    'options' => array(
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
        'actions' => array(
      /*  '_changeConfigs' => array (
            'blShowNetPrice' => false,
            'blEnterNetPrice' => true,
        ),*/
        '_addArticles' => array(
                0 => array(
                        'oxid'       => '1003',
                        'oxtitle'    => '1003',
                        'oxprice'    => 2.00,
                        'oxvat'      => 19,
                        'oxstock'    => 999,
                        'amount' => 1,
                ),
        ),
        ),
);
