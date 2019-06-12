<?php
/*
 * Price enter mode: brutto
 * Price view mode: brutto
 * Discounts: 0
 * Price type: range
 * User group: oxpriceb
 * config: 0 not override
 */
$aData = array(
        'articles' => array(
                0 => array(
                        'oxid'                     => '_testId_1',
                        'oxprice'                  => 100,
                        'oxpriceb'                 => 10
                ),
                1 => array(
                        'oxid'                     => '_testId_1_child_1',
                        'oxprice'                  => 100,
                        'oxparentid'               => '_testId_1'
                ),
                2 => array(
                        'oxid'                     => '_testId_1_child_2',
                        'oxprice'                  => 100,
                        'oxpriceb'                 => 20,
                        'oxparentid'               => '_testId_1'
                ),
                3 => array(
                        'oxid'                     => '_testId_2',
                        'oxprice'                  => 100,
                        'oxpricea'                 => 20,
                ),
                4 => array(
                        'oxid'                     => '_testId_2_child_1',
                        'oxprice'                  => 120,
                        'oxparentid'               => '_testId_2',
                        'oxpricea'                 => 20,
                ),
                5 => array(
                        'oxid'                     => '_testId_2_child_2',
                        'oxprice'                  => 150,
                        'oxparentid'               => '_testId_2'
                ),
                6 => array(
                        'oxid'                     => '_testId_3',
                        'oxprice'                  => 100
                ),
                7 => array(
                        'oxid'                     => '_testId_3_child_1',
                        'oxprice'                  => 20,
                        'oxparentid'               => '_testId_3',
                        'oxpriceb'                 => 20,
                ),
                8 => array(
                        'oxid'                     => '_testId_3_child_2',
                        'oxprice'                  => 50,
                        'oxparentid'               => '_testId_3',
                        'oxpriceb'                 => 20,
                ),
                9 => array(
                        'oxid'                     => '_testId_4',
                        'oxprice'                  => 100,
                        'oxpriceb'                 => 10,
                ),
                10 => array(
                        'oxid'                     => '_testId_4_child_1',
                        'oxprice'                  => 100,
                        'oxparentid'               => '_testId_4',
                        'oxpriceb'                 => 110,
                ),
                11 => array(
                        'oxid'                     => '_testId_4_child_2',
                        'oxprice'                  => 150,
                        'oxparentid'               => '_testId_4',
                        'oxpriceb'                 => 10,
                ),
                12 => array(
                        'oxid'                     => '_testId_5',
                        'oxprice'                  => 100
                ),
                13 => array(
                        'oxid'                     => '_testId_5_child_1',
                        'oxprice'                  => 100,
                        'oxparentid'               => '_testId_5'
                ),
                14 => array(
                        'oxid'                     => '_testId_5_child_2',
                        'oxprice'                  => 50,
                        'oxparentid'               => '_testId_5'
                ),
                15 => array(
                        'oxid'                     => '_testId_6',
                        'oxprice'                  => 100,
                        'oxpriceb'                 => 1
                ),
                16 => array(
                        'oxid'                     => '_testId_6_child_1',
                        'oxprice'                  => 100,
                        'oxparentid'               => '_testId_6',
                        'oxpriceb'                 => 1
                ),
                17 => array(
                        'oxid'                     => '_testId_6_child_2',
                        'oxprice'                  => 50,
                        'oxparentid'               => '_testId_6',
                        'oxpriceb'                 => 1
                ),
        ),
        'user' => array(
                'oxid' => '_testUser',
                'oxactive' => 1,
                'oxusername' => 'bGroupUser',
        ),
        'group' => array(
                0 => array(
                        'oxid' => 'oxidpriceb',
                        'oxactive' => 1,
                        'oxtitle' => 'Price B',
                        'oxobject2group' => array( '_testUser' ),
                ),
        ),
       'expected' => array(
               '_testId_1' => array(
                        'base_price' => '10,00',
                        'price' => '10,00',
                        'min_price' => '0,00',
                        'var_min_price' => '0,00',
                        'is_range_price' => true
               ),

               '_testId_2' => array(
                        'base_price' => '0,00',
                        'price' => '0,00',
                        'min_price' => '0,00',
                        'var_min_price' => '0,00',
                        'is_range_price' => false
                ),

                '_testId_3' => array(
                        'base_price' => '0,00',
                        'price' => '0,00',
                        'min_price' => '0,00',
                        'var_min_price' => '20,00',
                        'is_range_price' => false
                ),

                '_testId_4' => array(
                        'base_price' => '10,00',
                        'price' => '10,00',
                        'min_price' => '10,00',
                        'var_min_price' => '10,00',
                        'is_range_price' => true
                ),

                '_testId_5' => array(
                        'base_price' => '0,00',
                        'price' => '0,00',
                        'min_price' => '0,00',
                        'var_min_price' => '0,00',
                        'is_range_price' => false
                ),

                '_testId_6' => array(
                        'base_price' => '1,00',
                        'price' => '1,00',
                        'min_price' => '1,00',
                        'var_min_price' => '1,00',
                        'is_range_price' => false
                ),
        ),
        'options' => array(
                'config' => array(
                        'blEnterNetPrice' => false,
                        'blShowNetPrice' => false,
                        'blOverrideZeroABCPrices' => false,
                        'blVariantParentBuyable' => 0
                ),
                'activeCurrencyRate' => 1,
        ),
);
