<?php
/*
/**
 * Price enter mode: netto;
 * Price view mode: brutto;
 * Product count: 1 ;
 * VAT info: 5%;
 * Currency rate: 1.0;
 * Vouchers: -;
 * Wrapping: +;
 * Gift cart: +;
 * Discounts: -;
 * Short description: test added from selenium test (testFrontendNettoPrices) ;Checking when prices are entered in NETTO
 */
$aData = array(
    // Articles
    'articles' => array(
        0 => array(
            // oxarticles db fields
            'oxid'                     => 1000,
            'oxprice'                  => 50.00,
            'oxvat'                    => 5,
            // Amount in basket
            'amount'                   => 3,
        ),

    ),
    // Additional costs
    'costs' => array(
        // oxwrapping db fields
        'wrapping' => array(
            // Wrapping
            0 => array(
                'oxtype' => 'WRAP',
                'oxname' => 'Test wrapping [EN] ðÄßü?',
                'oxprice' => 0.9,
                'oxactive' => 1,
                // If for article, specify here
                'oxarticles' => array( 1000 )
            ),
            // Giftcard
            1 => array(
                'oxtype' => 'CARD',
                'oxname' => 'Test card [EN] ðÄßü',
                'oxprice' => 0.20,
                'oxactive' => 1,
            ),
        ),


    ),
    // TEST EXPECTATIONS
    'expected' => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
            1000 => array( '52,50', '157,50' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '157,50',
            // Total NETTO
            'totalNetto'  => '150,00',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                5 => '7,50'
            ),
            // Total wrapping amounts
            'wrapping' => array(
                'brutto' => '2,84',
            ),
            // Total giftcard amounts
            'giftcard' => array(
                'brutto' => '0,21',

            ),
            // GRAND TOTAL
            'grandTotal'  => '160,55'
        ),
    ),
       'options' => array(
            'config' => array(
                'blShowNetPrice' => false,
                'blEnterNetPrice' => true,
                'blWrappingVatOnTop' =>true,
                'blDeliveryVatOnTop' => true,
            ),
                'activeCurrencyRate' => 1,
        ),
);
