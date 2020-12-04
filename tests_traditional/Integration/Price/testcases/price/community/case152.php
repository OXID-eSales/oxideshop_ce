<?php
/*
 * Price enter mode: brutto
 * Price view mode: brutto
 * Price type: range
 * Articles: 1 with price 13.00
 * Variants: 1-2
 * Parent buyable: yes
 * Short description: 1st variant is not active
 */
$aData = array(
        'articles' => array(

                1 => array(
                        'oxid'                     => '_testId_1',
                        'oxprice'                  => 13
                ),
                2 => array(
                        'oxid'                     => '_testId_1_child_1',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_1',
                        'oxactive'                 => true
                ),

                3 => array(
                        'oxid'                     => '_testId_2',
                        'oxprice'                  => 13
                ),
                4 => array(
                        'oxid'                     => '_testId_2_child_1',
                        'oxprice'                  => 6,
                        'oxparentid'               => '_testId_2',
                        'oxactive'                 => false
                ),

                5 => array(
                        'oxid'                     => '_testId_3',
                        'oxprice'                  => 13
                ),
                6 => array(
                        'oxid'                     => '_testId_3_child_1',
                        'oxprice'                  => 13,
                        'oxparentid'               => '_testId_3',
                        'oxactive'                 => false
                ),

                7 => array(
                        'oxid'                     => '_testId_4',
                        'oxprice'                  => 13
                ),
                8 => array(
                        'oxid'                     => '_testId_4_child_1',
                        'oxprice'                  => 27,
                        'oxparentid'               => '_testId_4',
                        'oxactive'                 => false
                ),

                9 => array(
                        'oxid'                     => '_testId_5',
                        'oxprice'                  => 13
                ),
                10 => array(
                        'oxid'                     => '_testId_5_child_1',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_5',
                        'oxactive'                 => false
                ),
                11 => array(
                        'oxid'                     => '_testId_5_child_2',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_5'
                ),

                12 => array(
                        'oxid'                     => '_testId_6',
                        'oxprice'                  => 13
                ),
                13 => array(
                        'oxid'                     => '_testId_6_child_1',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_6',
                        'oxactive'                 => false
                ),
                14 => array(
                        'oxid'                     => '_testId_6_child_2',
                        'oxprice'                  => 6,
                        'oxparentid'               => '_testId_6'
                ),

                15 => array(
                        'oxid'                     => '_testId_7',
                        'oxprice'                  => 13
                ),
                16 => array(
                        'oxid'                     => '_testId_7_child_1',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_7',
                        'oxactive'                 => false
                ),
                17 => array(
                        'oxid'                     => '_testId_7_child_2',
                        'oxprice'                  => 13,
                        'oxparentid'               => '_testId_7'
                ),

                18 => array(
                        'oxid'                     => '_testId_8',
                        'oxprice'                  => 13
                ),
                19 => array(
                        'oxid'                     => '_testId_8_child_1',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_8',
                        'oxactive'                 => false
                ),
                20 => array(
                        'oxid'                     => '_testId_8_child_2',
                        'oxprice'                  => 27,
                        'oxparentid'               => '_testId_8'
                ),

                21 => array(
                        'oxid'                     => '_testId_9',
                        'oxprice'                  => 13
                ),
                22 => array(
                        'oxid'                     => '_testId_9_child_1',
                        'oxprice'                  => 6,
                        'oxparentid'               => '_testId_9',
                        'oxactive'                 => false
                ),
                23 => array(
                        'oxid'                     => '_testId_9_child_2',
                        'oxprice'                  => 0,
                        'oxparentid'               => '_testId_9'
                ),

                24 => array(
                        'oxid'                     => '_testId_10',
                        'oxprice'                  => 13
                ),
                25 => array(
                        'oxid'                     => '_testId_10_child_1',
                        'oxprice'                  => 6,
                        'oxparentid'               => '_testId_10',
                        'oxactive'                 => false
                ),
                26 => array(
                        'oxid'                     => '_testId_10_child_2',
                        'oxprice'                  => 27,
                        'oxparentid'               => '_testId_10'
                ),

                27 => array(
                        'oxid'                     => '_testId_11',
                        'oxprice'                  => 13
                ),
                28 => array(
                        'oxid'                     => '_testId_11_child_1',
                        'oxprice'                  => 27,
                        'oxparentid'               => '_testId_11',
                        'oxactive'                 => false
                ),
                29 => array(
                        'oxid'                     => '_testId_11_child_2',
                        'oxprice'                  => 27,
                        'oxparentid'               => '_testId_11'
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
                        'min_price' => '13,00',
                        'var_min_price' => '13,00',
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
                        'var_min_price' => '13,00',
                        'is_range_price' => false
                ),

                '_testId_6' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '6,00',
                        'var_min_price' => '6,00',
                        'is_range_price' => false
                ),

                '_testId_7' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '13,00',
                        'var_min_price' => '13,00',
                        'is_range_price' => false
                ),

                '_testId_8' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '13,00',
                        'var_min_price' => '27,00',
                        'is_range_price' => false
                ),

                '_testId_9' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '13,00',
                        'var_min_price' => '13,00',
                        'is_range_price' => false
                ),

                '_testId_10' => array(
                        'base_price' => '13,00',
                        'price' => '13,00',
                        'min_price' => '13,00',
                        'var_min_price' => '27,00',
                        'is_range_price' => false
                ),

                '_testId_11' => array(
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
