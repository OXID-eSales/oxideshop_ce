<?php
/**
 * Price enter mode: brutto
 * Price view mode: brutto
 * Product count: 1
 * VAT info: 20%
 * Currency rate: 1.0
 * Discounts: -
 * Short description: Brutto-Brutto user group Price B, Checking option "Use normal article price instead of zero A, B, C price" is OFF
 * Test case is moved from selenium test "testFrontendPriceB"
 */
$aData = array(
        'articles' => array(
                0 => array(
                        'oxid'            => 1003,
                        'oxprice'         => 70.00,
                        'oxpricea'        => 70,
                        'oxpriceb'        => 85,
                        'oxpricec'        => 0,
                        'amount'          => 1,
                        'oxvat'           => 19,
                        'scaleprices' => array(
                            'oxaddabs'     => 75.00,
                            'oxamount'     => 2,
                            'oxamountto'   => 5,
                            'oxartid'      => 1003,
                          ),
                ),
                1 => array(
         // oxarticles db fields
            'oxid'                     => 1112,
            'oxprice'                  => 5.02,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
        ),
        'user' => array(
                'oxid' => '_testUserB',
                'oxactive' => 1,
                'oxusername' => 'groupBUser',
        ),
 
        'group' => array(
                0 => array(
                        'oxid' => 'oxidpricea',
                        'oxactive' => 1,
                        'oxtitle' => 'Price A',
                        'oxobject2group' => array( '_testUserA' ),
                ),
                1 => array(
                        'oxid' => 'oxidpriceb',
                        'oxactive' => 1,
                        'oxtitle' => 'Price B',
                        'oxobject2group' => array(1003, '_testUserB' ),
                ),
                2 => array(
                        'oxid' => 'oxidpricec',
                        'oxactive' => 1,
                        'oxtitle' => 'Price C',
                        'oxobject2group' => array( '_testUserC' ),
                ),
        ),
        
        'expected' => array(
          'articles' => array(
                1003 => array( '85,00', '85,00' ),
                1112 => array( '0,00', '0,00' ),
            ),
        
        'totals' => array(
                'totalBrutto' => '85,00',
                'totalNetto'  => '71,43',
                'vats' => array(
                      19 => '13,57',
                ),
                'grandTotal'  => '85,00'
        ),
        ),
        'options' => array(
                'config' => array(
                        'blEnterNetPrice' => false,
                        'blShowNetPrice' => false,
                        'blOverrideZeroABCPrices' => false,
                        'dDefaultVAT' => 19,
                ),
                'activeCurrencyRate' => 1,
        ),
);
