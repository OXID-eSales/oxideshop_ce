<?php
/**
 * Price enter mode: brutto
 * Price view mode: brutto
 * Product count: 1
 * VAT info: 19%
 * Currency rate: 1.0
 * Discounts: -
 * Short description: Brutto-Brutto user group Price B, option "Use normal article price instead of zero A, B, C price" is ON
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
                        'amount'          => 7,
                        'oxvat'           => 19,
                       'scaleprices' => array(
                            'oxamount'     => 6,
                            'oxamountto'   => 999999,
                            'oxartid'      => 1003,
                            'oxaddperc'    => 20,
                          ),
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
                1003 => array( '68,00', '476,00' ),
            ),
        
        'totals' => array(
                'totalBrutto' => '476,00',
                'totalNetto'  => '400,00',
                'vats' => array(
                      19 => '76,00',
                ),
                'grandTotal'  => '476,00'
        ),
        ),
        'options' => array(
                'config' => array(
                        'blEnterNetPrice' => false,
                        'blShowNetPrice' => false,
                        'blOverrideZeroABCPrices' => true,
                        'dDefaultVAT' => 19,
                ),
                'activeCurrencyRate' => 1,
        ),
);
