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
                    'oxpricea'                 => 100,
                    'inheritToShops'           => array(2)
            ),
            1 => array(
                    'oxid'                     => '_testId_1_child_1',
                    'oxpricea'                 => 50,
                    'oxparentid'               => '_testId_1',
                    'field2shop' => array(
                            'oxshopid' => 2,
                            'oxpricea' => 100,
                    ),
                    'inheritToShops'           => array(2)
            ),
            2 => array(
                    'oxid'                     => '_testId_1_child_2',
                    'oxpricea'                 => 150,
                    'oxparentid'               => '_testId_1',
                    'field2shop' => array(
                            'oxshopid' => 2,
                            'oxpricea' => 100,
                    ),
                    'inheritToShops'           => array(2)
            ),

            3 => array(
                    'oxid'                     => '_testId_2',
                    'oxpricea'                 => 50,
                    'inheritToShops'           => array(2)
            ),
            4 => array(
                    'oxid'                     => '_testId_2_child_1',
                    'oxpricea'                 => 30,
                    'oxparentid'               => '_testId_2',
                    'field2shop' => array(
                            'oxshopid' => 2,
                            'oxpricea' => 60,
                    ),
                    'inheritToShops'           => array(2)
            ),
            5 => array(
                    'oxid'                     => '_testId_2_child_2',
                    'oxpricea'                 => 30,
                    'oxparentid'               => '_testId_2',
                    'field2shop' => array(
                            'oxshopid' => 2,
                            'oxpricea' => 70,
                    ),
                    'inheritToShops'           => array(2)
            ),

            6 => array(
                    'oxid'                     => '_testId_3',
                    'oxpricea'                 => 80,
                    'inheritToShops'           => array(2)
            ),
            7 => array(
                    'oxid'                     => '_testId_3_child_1',
                    'oxpricea'                 => 30,
                    'oxparentid'               => '_testId_3',
                    'field2shop' => array(
                            'oxshopid' => 2,
                            'oxpricea' => 60,
                    ),
                    'inheritToShops'           => array(2)
            ),
    ),

    'shop' => array(
            0 => array(
                    'oxactive'          => 1,
                    'oxid'              => 2,
                    'oxparentid'        => 1,
                    'oxname'            => 'subshop',
                    'oxisinherited'     => 1,
                    'activeshop'        => true
            ),
    ),

    'user' => array(
            'oxid'          => '_testUser',
            'oxactive'      => 1,
            'oxusername'    => 'aGroupUser',

    ),

    'group' => array(
            0 => array(
                    'oxid'              => 'oxidpricea',
                    'oxactive'          => 1,
                    'oxtitle'           => 'Price A',
                    'oxobject2group'    => array( '_testUser' ),
            ),

    ),

    'expected' => array(
            '_testId_1' => array(
                    'base_price'        => '100,00',
                    'price'             => '100,00',
                    'min_price'         => '100,00',
                    'var_min_price'     => '100,00',
                    'is_range_price'    => false
            ),
            '_testId_2' => array(
                    'base_price'        => '50,00',
                    'price'             => '50,00',
                    'min_price'         => '50,00',
                    'var_min_price'     => '60,00',
                    'is_range_price'    => true
            ),
            '_testId_3' => array(
                    'base_price'        => '80,00',
                    'price'             => '80,00',
                    'min_price'         => '60,00',
                    'var_min_price'     => '60,00',
                    'is_range_price'    => true
            ),

    ),

    'options' => array(
            'config' => array(
                    'blEnterNetPrice' => false,
                    'blShowNetPrice' => false,
                    'blVariantParentBuyable' => 1,
                    'blMallCustomPrice' => 1,
            ),
            'activeCurrencyRate' => 1
    ),
);
