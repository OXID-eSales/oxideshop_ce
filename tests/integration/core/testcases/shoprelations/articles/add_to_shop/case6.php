<?php
/*
 * Test adding article to another shop.
 */
$aData = array(
    'shops'    => array(
        array(
            'oxname' => '_testShop2',
        ),
        array(
            'oxname' => '_testShop3',
        ),
        array(
            'oxname' => '_testShop4',
        ),
    ),
    'articles' => array(
        array(
            'oxid'   => '_testArticle1',
            'oxshop' => 1,
        ),
    ),
    'setup'    => array(
        'articles2shop' => array(
            '_testArticle1' => array(
                // shop IDs
                4,
            ),
        ),
    ),
    'actions'  => array(
        'add_to_shop' => array(
            '_testArticle1' => array(
                // shop IDs
                2,
            ),
        ),
    ),
    'expected' => array(
        'article_in_shop'     => array(
            '_testArticle1' => array(
                // shop IDs
                1, 2, 4,
            ),
        ),
        'article_not_in_shop' => array(
            '_testArticle1' => array(
                // shop IDs
                3,
            ),
        ),
    ),
);
