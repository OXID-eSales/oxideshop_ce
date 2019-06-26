<?php
/*
 * Calculate VAT proportionately . Neto-Neto mode. Additiona products Neto-Neto.
*/
$aData = array(
    // Product
    'articles' => array(
         0 => array(
            // oxarticles db fields
            'oxid'                     => 1001,
            'oxprice'                  => 20.00,
            'oxvat'                    => 10,
            // Amount in basket
            'amount'                   => 15,
        ),
        1 => array(
            // oxarticles db fields
            'oxid'                     => 1002,
            'oxprice'                  => 200.00,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,

        ),

    ),
    // Additional costs
    'costs' => array(
     // Wrappings
        'wrapping' => array(
            // Giftcard
           3 => array(
                'oxtype' => 'CARD',
                'oxname' => 'testCard1001',
                'oxprice' => 2.50,
                'oxactive' => 1,
            ),
        ),
        // Delivery
        'delivery' => array(
            0 => array(
                // oxdelivery DB fields
                'oxactive' => 1,
                'oxaddsum' => 55.00,
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
                'oxaddsum' => 275,
                'oxaddsumtype' => 'abs',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
            ),
        ),
    ),

    // TEST EXPECTATIONS
    'expected' => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
            1001 => array( '20,00', '300,00' ),
            1002 => array( '200,00', '200,00' ),

        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '568,00',
            // Total NETTO
            'totalNetto'  => '500,00',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                10 => '30,00',
                19 => '38,00',

            ),
            // Total delivery amounts
            'delivery' => array(
                'brutto' => '312,40',
                'netto' => '275,00',
                'vat' => '37,40'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '312,40',
                'netto' => '275,00',
                'vat' => '37,40'
            ),

            // Total giftcard amounts
            'giftcard' => array(
                'brutto' => '2,84',
                'netto' => '2,50',
                'vat' => '0,34'
            ),
            // GRAND TOTAL
            'grandTotal'  => '1.195,64'
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
            'sAdditionalServVATCalcMethod' => 'proportional',
            'blDeliveryVatOnTop' => true,
            'blPaymentVatOnTop' => true,
            'blWrappingVatOnTop' => true,
        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);
