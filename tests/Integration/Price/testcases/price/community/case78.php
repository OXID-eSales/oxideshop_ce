<?php
/*
 * Price enter mode: brutto
 * Price view mode: brutto
 * Discounts: 0
 * Price type: range
 *
 */
$aData = array(
        'articles' => array(
                0 => array(
                        'oxid'                     => '_testId_1',
                        'oxprice'                  => 100
                ),
                1 => array(
                        'oxid'                     => '_testId_1_child_1',
                        'oxprice'                  => 100,
                        'oxparentid'               => '_testId_1'
                ),
                2 => array(
                        'oxid'                     => '_testId_1_child_2',
                        'oxprice'                  => 100,
                        'oxparentid'               => '_testId_1'
                ),
                3 => array(
                        'oxid'                     => '_testId_2',
                        'oxprice'                  => 100
                ),
                4 => array(
                        'oxid'                     => '_testId_2_child_1',
                        'oxprice'                  => 120,
                        'oxparentid'               => '_testId_2'
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
                        'oxparentid'               => '_testId_3'
                ),
                8 => array(
                        'oxid'                     => '_testId_3_child_2',
                        'oxprice'                  => 50,
                        'oxparentid'               => '_testId_3'
                ),
                9 => array(
                        'oxid'                     => '_testId_4',
                        'oxprice'                  => 100
                ),
                10 => array(
                        'oxid'                     => '_testId_4_child_1',
                        'oxprice'                  => 100,
                        'oxparentid'               => '_testId_4'
                ),
                11 => array(
                        'oxid'                     => '_testId_4_child_2',
                        'oxprice'                  => 150,
                        'oxparentid'               => '_testId_4'
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
                        'oxprice'                  => 100
                ),
                16 => array(
                        'oxid'                     => '_testId_6_child_1',
                        'oxprice'                  => 50,
                        'oxparentid'               => '_testId_6'
                ),
                17 => array(
                        'oxid'                     => '_testId_6_child_2',
                        'oxprice'                  => 50,
                        'oxparentid'               => '_testId_6'
                ),

                18 => array(
                        'oxid'                     => '_testId_7',
                        'oxprice'                  => 40
                ),
                19 => array(
                        'oxid'                     => '_testId_7_child_1',
                        'oxprice'                  => 50,
                        'oxparentid'               => '_testId_7'
                ),
                20 => array(
                        'oxid'                     => '_testId_7_child_2',
                        'oxprice'                  => 50,
                        'oxparentid'               => '_testId_7'
                ),

        ),

       'expected' => array(

                '_testId_1' => array(
                        'base_price' => '100,00',
                        'price' => '100,00',
                        'min_price' => '100,00',
                        'var_min_price' => '100,00',
                        'is_range_price' => false
                ),

                '_testId_2' => array(
                        'base_price' => '100,00',
                        'price' => '100,00',
                        'min_price' => '100,00',
                        'var_min_price' => '120,00',
                        'is_range_price' => true
                ),

                '_testId_3' => array(
                        'base_price' => '100,00',
                        'price' => '100,00',
                        'min_price' => '20,00',
                        'var_min_price' => '20,00',
                        'is_range_price' => true
                ),

                '_testId_4' => array(
                        'base_price' => '100,00',
                        'price' => '100,00',
                        'min_price' => '100,00',
                        'var_min_price' => '100,00',
                        'is_range_price' => true
                ),

                '_testId_5' => array(
                        'base_price' => '100,00',
                        'price' => '100,00',
                        'min_price' => '50,00',
                        'var_min_price' => '50,00',
                        'is_range_price' => true
                ),

                '_testId_6' => array(
                        'base_price' => '100,00',
                        'price' => '100,00',
                        'min_price' => '50,00',
                        'var_min_price' => '50,00',
                        'is_range_price' => true
                ),

                '_testId_7' => array(
                        'base_price' => '40,00',
                        'price' => '40,00',
                        'min_price' => '40,00',
                        'var_min_price' => '50,00',
                        'is_range_price' => true
                ),

        ),
        'options' => array(
                'config' => array(
                        'blEnterNetPrice' => false,
                        'blShowNetPrice' => false,
                        'blVariantParentBuyable' => 1
                ),
                'activeCurrencyRate' => 1

        ),
);
