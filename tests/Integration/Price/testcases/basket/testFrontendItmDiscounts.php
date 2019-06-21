<?php
/*
/**
 * Price enter mode: brutto
 * Price view mode: brutto
 * Product count: 3
 * VAT info: 5%
 * Currency rate: 1.0
 * Vouchers: -;
 * Wrapping: -;
 * Gift cart: -;
 * Discounts: 2 item discount
 * Short description: test added from selenium test (testFrontendItmDiscounts) ;Is testing one Itm discount for products (special case according Mantis#320)
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
            'amount'                   => 5,
        ),
        1 => array(
            // oxarticles db fields
            'oxid'                     => 1003,
            'oxprice'                  => 50.00,
            'oxvat'                    => 5,
            // Amount in basket
          //  'amount'                   => 1,
        ),
    ),
    // Discounts
    'discounts' => array(
        // oxdiscount DB fields
        0 => array(
            'oxid'         => 'testitmdiscount',
            'oxshopid' => 1,
            'oxaddsum'     => 0,
            'oxaddsumtype' => 'itm',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxprice' => 0,
            'oxpriceto' => 0,
            'oxactive' => 1,
            'oxitmartid' => 1003,
            'oxitmamount' => 1,
            'oxitmmultiple' => 0,
            'oxarticles' => array( 1000 ),
            'oxsort' => 10,

        ),
        1 => array(
            // Discount 10% on 200 Euro or more
            'oxid'         => 'testdiscountfrom200',
            'oxshopid' => 1,
            'oxaddsum'     => 10,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxprice' => 200,
            'oxpriceto'=> 999999,
            'oxactive' => 1,
            'oxsort' => 20,
        ),
    ),
    // TEST EXPECTATIONS
    'expected' => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
             1000 => array( '50,00', '250,00' ),
             1003 => array( '0,00', '0,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '250,00',
            // Total NETTO
            'totalNetto'  => '214,29',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                5 => '10,71'
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                'testdiscountfrom200' => '25,00',
            ),

            // GRAND TOTAL
            'grandTotal'  => '225,00'
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
