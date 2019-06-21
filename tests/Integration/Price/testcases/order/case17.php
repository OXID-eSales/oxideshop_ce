<?php
/*
/**
 * Price enter mode: netto
 * Price view mode:  netto
 * Product count: 2
 * VAT info: 19% Default VAT for all Products , additional VAT=10% for product 1001
 * Currency rate: 1.0
 * Discounts: 1
 *  1. discount item for product 1002
 * Vouchers: +
 * Wrapping: +
 * Costs VAT caclulation rule: biggest_net
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS -
 * Short description:
 * Netto - Netto start case, after order saving, added two product's,
 * updating, changed product (1001) amound from 15 to 10, additional discount for order 20eur.
*/
# need prepared integration test for order additional discount:
$aData = array(
    // Product
        'skipped' => 1,
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
            'oxid'                     => 1004,
            'oxprice'                  => 200.00,
            'oxvat'                    => 19,
            // Amount in basket
        ),

    ),
    // Discounts
    'discounts' => array(
        // oxdiscount DB fields
        0 => array(
            // item discount for basket
            'oxid'         => 'discountitm',
            'oxaddsum'     => 0,
            'oxaddsumtype' => 'itm',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxitmartid' => 1004,
            'oxitmamount' => 1,
            'oxitmultiple' => 1,
            'oxarticles' => array( 1002 ),
            'oxsort' => 10,
        ),
    ),
    // Additional costs
    'costs' => array(
     // Wrappings
        'wrapping' => array(
            // Giftcard
           0 => array(
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
                'oxaddsum' => 10.00,
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
                'oxaddsum' => 275,
                'oxaddsumtype' => 'abs',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
            ),
        ),
        'voucherserie' => array(
            0 => array(
                'oxdiscount' => 10.00,
                'oxdiscounttype' => '%',
                'oxallowsameseries' => 1,
                'oxallowotherseries' => 1,
                'oxallowuseanother' => 1,
                'voucher_count' => 1
            ),
        ),

    ),

    // TEST EXPECTATIONS
    'expected' => array(
     1 => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
            1001 => array( '20,00', '300,00' ),


        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '297,00',
            // Total NETTO
            'totalNetto'  => '300,00',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                10 => '27,00',
            ),

            // Total delivery amounts
            'delivery' => array(
                'brutto' => '11,00',
                'netto' => '10,00',
                'vat' => '1,00'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '302,50',
                'netto' => '275,00',
                'vat' => '27,50'
            ),
            'discount'  => '0,00',
                'voucher' => array(
                'brutto' => '30,00',
            ),
            // Total giftcard amounts
            'giftcard' => array(
                'brutto' => '2,75',
                'netto' => '2,50',
                'vat' => '0,25'
            ),
            // GRAND TOTAL
            'grandTotal'  => '613,25'
            ),
        ),
    2 => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
            1001 => array( '20,00', '200,00' ),
            1002 => array( '200,00', '200,00' ),
            1004 => array( '0,00', '0,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '412,20',
            // Total NETTO
            'totalNetto'  => '400,00',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '34,20',
                10 => '18,00',

            ),

            // Total delivery amounts
            'delivery' => array(
                'brutto' => '11,90',
                'netto' => '10,00',
                'vat' => '0,90'
            ),
            // Total payment amounts
            'discount'  => '20,00',
            'payment' => array(
                'brutto' => '327,25',
                'netto' => '275,00',
                'vat' => '52,25'
            ),
                'voucher' => array(
                'brutto' => '40,00',
            ),
            // Total giftcard amounts
            'giftcard' => array(
                'brutto' => '2,98',
                'netto' => '2,50',
                'vat' => '0,48'
            ),
            // GRAND TOTAL
            'grandTotal'  => '734,33'
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
            '_changeArticles' => array(
                    0 => array(
                            'oxid'       => '1001',
                            'amount'     => 10,
                    ),
            ),
                //	Discount should be 20 eur
        //	'_changeDiscount' => array (
        //        editval[oxorder__oxdiscount],
       // ),
            '_addArticles' => array(
                    0 => array(
                            'oxid'       => '1002',
                            'oxtitle'    => '1002',
                            'oxprice'    => 200,
                            'oxvat'      => 19,
                            'oxstock'    => 999,
                            'amount' => 1,
                    ),
            ),
    ),
);
