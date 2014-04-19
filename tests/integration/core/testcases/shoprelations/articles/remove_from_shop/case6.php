<?php
/*
 * Test removing article from shop leaving in another shops.
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
                2, 3, 4,
            ),
        ),
    ),
    'actions'  => array(
        'remove_from_shop' => array(
            '_testArticle1' => array(
                2, 4,
            ),
        ),
    ),
    'expected' => array(
        'article_in_shop'     => array(
            '_testArticle1' => array(
                1, 3,
            ),
        ),
        'article_not_in_shop' => array(
            '_testArticle1' => array(
                2, 4,
            ),
        ),
    ),
);
