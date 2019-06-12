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
            'discounts' => array(
            0 => array(
                    'oxid'         => 'discount1',
                    'oxaddsum'     => 10,
                    'oxaddsumtype' => '%',
                    'oxamount'     => 0,
                    'oxamountto'   => 99999,
                    'oxprice'      => 100,
                    'oxpriceto'    => 99999,
                    'oxactive'     => 1,
                    'oxarticles'   => array( 1002, 1003 ),
                    'oxsort'       => 10,
            ),
            1 => array(
                    'oxid'         => 'discount2',
                    'oxaddsum'     => 5,
                    'oxaddsumtype' => 'abs',
                    'oxamount'     => 1,
                    'oxamountto'   => 99999,
                    'oxactive'     => 1,
                    'oxarticles'   => array( 10013, 1000 ),
                    'oxsort'       => 20,
            ),
    ),

        'expected' => array(
          'articles' => array(
                1003 => array( '61,20', '428,40' ),
            ),

        'totals' => array(
                'totalBrutto' => '428,40',
                'totalNetto'  => '360,00',
                'vats' => array(
                      19 => '68,40',
                ),
                'grandTotal'  => '428,40'
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
