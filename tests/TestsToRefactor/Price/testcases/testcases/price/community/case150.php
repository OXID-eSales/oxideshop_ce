<?php
/*
 * Price enter mode: brutto
 * Price view mode: brutto
 * Price type: range
 * Articles: 1 with price 13.00
 * Variants: 0-2
 * Parent buyable: yes
 */
$aData = array(
        'articles' => array(

                0 => array(
                        'oxid'                     => '_testId_1',
                        'oxprice'                  => 13
                ),

                1 => array(
                        'oxid'                     => '_testId_2',
                        'oxprice'                  => 13
                ),
                2 => array(
                        'oxid'                     => '_testId_2_child_1',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_2'
                ),

                3 => array(
                        'oxid'                     => '_testId_3',
                        'oxprice'                  => 13
                ),
                4 => array(
                        'oxid'                     => '_testId_3_child_1',
                        'oxprice'                  => 6,
                        'oxparentid'               => '_testId_3'
                ),

                5 => array(
                        'oxid'                     => '_testId_4',
                        'oxprice'                  => 13
                ),
                6 => array(
                        'oxid'                     => '_testId_4_child_1',
                        'oxprice'                  => 13,
                        'oxparentid'               => '_testId_4'
                ),

                7 => array(
                        'oxid'                     => '_testId_5',
                        'oxprice'                  => 13
                ),
                8 => array(
                        'oxid'                     => '_testId_5_child_1',
                        'oxprice'                  => 27,
                        'oxparentid'               => '_testId_5'
                ),

                9 => array(
                        'oxid'                     => '_testId_6',
                        'oxprice'                  => 13
                ),
                10 => array(
                        'oxid'                     => '_testId_6_child_1',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_6'
                ),
                11 => array(
                        'oxid'                     => '_testId_6_child_2',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_6'
                ),

                12 => array(
                        'oxid'                     => '_testId_7',
                        'oxprice'                  => 13
                ),
                13 => array(
                        'oxid'                     => '_testId_7_child_1',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_7'
                ),
                14 => array(
                        'oxid'                     => '_testId_7_child_2',
                        'oxprice'                  => 6,
                        'oxparentid'               => '_testId_7'
                ),

                15 => array(
                        'oxid'                     => '_testId_8',
                        'oxprice'                  => 13
                ),
                16 => array(
                        'oxid'                     => '_testId_8_child_1',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_8'
                ),
                17 => array(
                        'oxid'                     => '_testId_8_child_2',
                        'oxprice'                  => 13,
                        'oxparentid'               => '_testId_8'
                ),

                18 => array(
                        'oxid'                     => '_testId_9',
                        'oxprice'                  => 13
                ),
                19 => array(
                        'oxid'                     => '_testId_9_child_1',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_9'
                ),
                20 => array(
                        'oxid'                     => '_testId_9_child_2',
                        'oxprice'                  => 27,
                        'oxparentid'               => '_testId_9'
                ),

                21 => array(
                        'oxid'                     => '_testId_10',
                        'oxprice'                  => 13
                ),
                22 => array(
                        'oxid'                     => '_testId_10_child_1',
                        'oxprice'                  => 6,
                        'oxparentid'               => '_testId_10'
                ),
                23 => array(
                        'oxid'                     => '_testId_10_child_2',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_10'
                ),

                24 => array(
                        'oxid'                     => '_testId_11',
                        'oxprice'                  => 13
                ),
                25 => array(
                        'oxid'                     => '_testId_11_child_1',
                        'oxprice'                  => 6,
                        'oxparentid'               => '_testId_11'
                ),
                26 => array(
                        'oxid'                     => '_testId_11_child_2',
                        'oxprice'                  => 27,
                        'oxparentid'               => '_testId_11'
                ),

                27 => array(
                        'oxid'                     => '_testId_12',
                        'oxprice'                  => 13
                ),
                28 => array(
                        'oxid'                     => '_testId_12_child_1',
                        'oxprice'                  => 27,
                        'oxparentid'               => '_testId_12'
                ),
                29 => array(
                        'oxid'                     => '_testId_12_child_2',
                        'oxprice'                  => 27,
                        'oxparentid'               => '_testId_12'
                ),

        ),

        'expected' => array(

                '_testId_1' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '13,00',
                        'var_min_price' => '13,00',
                        'is_range_price' => false
                ),

                '_testId_2' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '13,00',
                        'var_min_price' => '13,00',
                        'is_range_price' => false
                ),

                '_testId_3' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '6,00',
                        'var_min_price' => '6,00',
                        'is_range_price' => false
                ),

                '_testId_4' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '13,00',
                        'var_min_price' => '13,00',
                        'is_range_price' => false
                ),

                '_testId_5' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '13,00',
                        'var_min_price' => '27,00',
                        'is_range_price' => false
                ),

                '_testId_6' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '13,00',
                        'var_min_price' => '13,00',
                        'is_range_price' => false
                ),

                '_testId_7' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '6,00',
                        'var_min_price' => '6,00',
                        'is_range_price' => true
                ),

                '_testId_8' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '13,00',
                        'var_min_price' => '13,00',
                        'is_range_price' => false
                ),

                '_testId_9' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '13,00',
                        'var_min_price' => '13,00',
                        'is_range_price' => true
                ),

                '_testId_10' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '6,00',
                        'var_min_price' => '6,00',
                        'is_range_price' => true
                ),

                '_testId_11' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '6,00',
                        'var_min_price' => '6,00',
                        'is_range_price' => true
                ),

                '_testId_12' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '13,00',
                        'var_min_price' => '27,00',
                        'is_range_price' => false
                ),

        ),
        'options' => array(
                'config' => array(
                        'blEnterNetPrice' => false,
                        'blShowNetPrice' => false,
                        'blVariantParentBuyable' => 0
                ),

        ),
);
