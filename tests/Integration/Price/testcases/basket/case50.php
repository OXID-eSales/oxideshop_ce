<?php
/*
 * Price enter mode: brutto
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: count of used vat's (list)
 * Currency rate: 1.0 (change if needed)
 * Wrapping:  +
 * Gift cart: +;
 * Costs VAT caclulation rule: bigest net
 * Short description: 5 products with different vat. Payment, shipping, greeting card and wrapping fees. Calculate VAT according to the biggest net value. Bruto-Bruto mode. Additiona products Bruto-Neto.
 */
$aData = array(
    // Product
    'articles' => array(
        0 => array(
            // oxarticles db fields
            'oxid'                     => 1001,
            'oxprice'                  => 1382.42,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 2,
        ),
        1 => array(
            // oxarticles db fields
            'oxid'                     => 1002,
            'oxprice'                  => 13.58,
            'oxvat'                    => 13,
            // Amount in basket
            'amount'                   => 14,

        ),
        2 => array(
            // oxarticles db fields
            'oxid'                     => 1003,
            'oxprice'                  => 1756.66,
            'oxvat'                    => 3,
            // Amount in basket
            'amount'                   => 13,

        ),
        3 => array(
            // oxarticles db fields
            'oxid'                     => 1004,
            'oxprice'                  => 13.64,
            'oxvat'                    => 17,
            // Amount in basket
            'amount'                   => 62,

        ),
    ),
    // Additional costs
    'costs' => array(
     // Wrappings
        'wrapping' => array(
            // oxwrapping DB fields
            0 => array(
                'oxtype' => 'WRAP',
                'oxname' => 'testWrap9001',
                'oxprice' => 3.98,
                'oxactive' => 1,
               
                // If for article, specify here
                'oxarticles' => array( 1001 )
            ),
            1 => array(
                'oxtype' => 'WRAP',
                'oxname' => 'testWrap9002',
                'oxprice' => 1.47,
                'oxactive' => 1,
              
                // If for article, specify here
                'oxarticles' => array( 1002 )
            ),
           2 => array(
                'oxtype' => 'WRAP',
                'oxname' => 'testWrap9003',
                'oxprice' => 2.14,
                'oxactive' => 1,
           
                // If for article, specify here
                'oxarticles' => array( 1003 )
            ),
            // Giftcard
           3 => array(
                'oxtype' => 'CARD',
                'oxname' => 'testCard9001',
                'oxprice' => 2.97,
                'oxactive' => 1,
            ),
        ),
        // Delivery
        'delivery' => array(
            0 => array(
                // oxdelivery DB fields
                'oxactive' => 1,
                'oxaddsum' => 3.14,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparamend' => 99999,
            ),
        ),
        // Payment
        'payment' => array(
            0 => array(
                // oxpayments DB fields
                'oxaddsum' => 7.59,
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
            1001 => array( '1.382,42', '2.764,84' ),
            1002 => array( '13,58', '190,12' ),
            1003 => array( '1.756,66', '22.836,58' ),
            1004 => array( '13,64', '845,68' ),
           
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '26.637,22',
            // Total NETTO
            'totalNetto'  => '25.385,88',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '441,45',
                13 => '21,87',
                3  => '665,14',
                17 => '122,88',
    
            ),
            // Total delivery amounts
            'delivery' => array(
                'brutto' => '3,14',
                'netto' => '3,05',
                'vat' => '0,09'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '7,59',
                'netto' => '7,37',
                'vat' => '0,22'
            ),
            // Total wrapping amounts
            'wrapping' => array(
                'brutto' => '56,36',
                'netto' => '51,91',
                'vat' => '4,45'
            ),
            // Total giftcard amounts
            'giftcard' => array(
                'brutto' => '2,97',
                'netto' => '2,88',
                'vat' => '0,09'
            ),
            // GRAND TOTAL
            'grandTotal'  => '26.707,28'
        ),
    ),
    // Test case options
    'options' => array(
        // Configs (real named)
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false,
            'blShowVATForDelivery'=> true,
            'blShowVATForPayCharge'=> true,
            'blShowVATForWrapping'=> true,
        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);
