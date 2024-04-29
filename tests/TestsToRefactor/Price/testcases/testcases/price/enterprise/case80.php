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
                        'oxprice'                  => 100,
                        'inheritToShops'           => array(2)
                ),
                1 => array(
                        'oxid'                     => '_testId_1_child_1',
                        'oxprice'                  => 100,
                        'oxparentid'               => '_testId_1',
                        'field2shop' => array(
                                'oxshopid' => 2,
                                'oxprice' => 50
                        ),
                        'inheritToShops'           => array(2)
                ),

                2 => array(
                        'oxid'                     => '_testId_2',
                        'oxprice'                  => 40,
                        'inheritToShops'           => array(2)
                ),
                3 => array(
                        'oxid'                     => '_testId_2_child_1',
                        'oxprice'                  => 70,
                        'oxparentid'               => '_testId_2',
                        'field2shop' => array(
                                'oxshopid' => 2,
                                'oxprice' => 60
                        ),
                        'inheritToShops'           => array(2)
                ),

                4 => array(
                    'oxid'                     => '_testId_3',
                    'oxprice'                  => 100,
                    'inheritToShops'           => array(2)
                ),
                5 => array(
                        'oxid'                     => '_testId_3_child_1',
                        'oxprice'                  => 60,
                        'oxparentid'               => '_testId_3',
                        'field2shop' => array(
                                'oxshopid' => 2,
                                'oxprice' => 50
                        ),
                        'inheritToShops'           => array(2)
                ),
                6 => array(
                        'oxid'                     => '_testId_3_child_2',
                        'oxprice'                  => 70,
                        'oxparentid'               => '_testId_3',
                        'field2shop' => array(
                                'oxshopid' => 2,
                                'oxprice' => 50
                        ),
                        'inheritToShops'           => array(2)
                ),

                7 => array(
                        'oxid'                     => '_testId_4',
                        'oxprice'                  => 60,
                        'inheritToShops'           => array(2)
                ),
                8 => array(
                        'oxid'                     => '_testId_4_child_1',
                        'oxprice'                  => 70,
                        'oxparentid'               => '_testId_4',
                        'field2shop' => array(
                                'oxshopid' => 2,
                                'oxprice' => 50
                        ),
                        'inheritToShops'           => array(2)
                ),
                9 => array(
                        'oxid'                     => '_testId_4_child_2',
                        'oxprice'                  => 60,
                        'oxparentid'               => '_testId_4',
                        'field2shop' => array(
                                'oxshopid' => 2,
                                'oxprice' => 50
                        ),
                        'inheritToShops'           => array(2)
                ),
        ),

        'shop' => array(
                0 => array(
                        'oxactive'     => 1,
                        'oxid'   => 2,
                        'oxparentid'   => 1,
                        'oxname'       => 'subshop',
                        'oxisinherited' => 1,
                        'activeshop'     => true
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
                       'base_price' => '40,00',
                       'price' => '40,00',
                       'min_price' => '40,00',
                       'var_min_price' => '70,00',
                       'is_range_price' => true
               ),
               '_testId_3' => array(
                       'base_price' => '100,00',
                       'price' => '100,00',
                       'min_price' => '60,00',
                       'var_min_price' => '60,00',
                       'is_range_price' => true
               ),
               '_testId_6' => array(
                       'base_price' => '60,00',
                       'price' => '60,00',
                       'min_price' => '60,00',
                       'var_min_price' => '60,00',
                       'is_range_price' => false
               ),

       ),
       'options' => array(
               'config' => array(
                       'blEnterNetPrice' => false,
                       'blShowNetPrice' => false,
                       'blVariantParentBuyable' => 1,
                       'blMallCustomPrice' => 0
               ),
               'activeCurrencyRate' => 1
       ),
);
