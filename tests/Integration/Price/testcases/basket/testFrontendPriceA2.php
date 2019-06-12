<?php
/**
 * Price enter mode: brutto
 * Price view mode: brutto
 * Product count: 1
 * VAT info: 19%
 * Currency rate: 1.0
 * Discounts: -
 * Short description: Brutto-Brutto user group Price B,  option "Use normal article price instead of zero A, B, C price" is ON
 * Test case is moved from selenium test "testFrontendPriceA"
 */
$aData = array(
        'articles' => array(
                0 => array(
                        'oxid'            => 1002,
                        'oxprice'         => 50.00,
                        'oxpricea'        => 35,
                        'oxpriceb'        => 45,
                        'oxpricec'        => 55,
                        'amount'          => 1,
                        'oxvat'           => 19,
                        'oxtitle'     => "Wall Clock ROBOT",
                        
                        'oxunitname'               => 'kg',
                        'oxunitquantity'           => 2,
                        'oxweight'        => 10
                    //	'oxheight'        => 2,
                ),
        ),
        'user' => array(
                'oxid' => '_testUserA',
                'oxactive' => 1,
                'oxusername' => 'groupAUser',
        ),
 
        'group' => array(
                0 => array(
                        'oxid' => 'oxidpricea',
                        'oxactive' => 1,
                        'oxtitle' => 'Price A',
                        'oxobject2group' => array(1002, '_testUserA' ),
                ),
                1 => array(
                        'oxid' => 'oxidpriceb',
                        'oxactive' => 1,
                        'oxtitle' => 'Price B',
                        'oxobject2group' => array( '_testUserB' ),
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
                1002 => array( '35,00', '35,00' ),
            ),
        
        'totals' => array(
                'totalBrutto' => '35,00',
                'totalNetto'  => '29,41',
                'vats' => array(
                      19 => '5,59',
                ),
                'grandTotal'  => '35,00'
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
