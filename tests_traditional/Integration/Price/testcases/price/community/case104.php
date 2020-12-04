<?php
/* RRP = 79.90
 * Price enter mode: netto
 * Price view mode: brutto
 * Product count: 8
 * VAT info: 15
 * Discount number: 4
 *  1. shop; %
 */
$aData = array(
        'articles' => array(
                0 => array(
                        'oxid'            => '1000',
                        'oxprice'         => 79.9,
                        'oxtprice'        => 79.9
                ),
                1 => array(
                        'oxid'            => '1001',
                        'oxprice'         => 79.9,
                        'oxtprice'        => 79.9
                ),
                2 => array(
                        'oxid'            => '1002',
                        'oxprice'         => 79.9,
                        'oxtprice'        => 79.9
                ),
                3 => array(
                        'oxid'            => '1003',
                        'oxprice'         => 79.9,
                        'oxtprice'        => 79.9
                ),
                4 => array(
                        'oxid'            => '1004',
                        'oxprice'         => 89.9,
                        'oxtprice'        => 79.9
                ),
                5 => array(
                        'oxid'            => '1005',
                        'oxprice'         => 89.9,
                        'oxtprice'        => 79.9
                ),
                6 => array(
                        'oxid'            => '1006',
                        'oxprice'         => 89.9,
                        'oxtprice'        => 79.9
                ),
                7 => array(
                        'oxid'            => '1007',
                        'oxprice'         => 89.9,
                        'oxtprice'        => 79.9
                ),
        ),
        'discounts' => array(
                0 => array(
                        'oxid'             => 'percentFor1000',
                        'oxaddsum'         => 20,
                        'oxaddsumtype'     => '%',
                        'oxprice'          => 0,
                        'oxpriceto'        => 99999,
                        'oxamount'         => 0,
                        'oxamountto'       => 99999,
                        'oxactive'         => 1,
                        'oxarticles'       => array( 1000, 1004 ),
                        'oxsort'           => 10,
                ),
                1 => array(
                        'oxid'         => 'percentFor1001',
                        'oxaddsum'     => -10,
                        'oxaddsumtype' => '%',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( 1001, 1005 ),
                        'oxsort'       => 20,
                ),
                2 => array(
                        'oxid'             => 'percentFor1002',
                        'oxaddsum'         => -5.2,
                        'oxaddsumtype'     => '%',
                        'oxprice'          => 0,
                        'oxpriceto'        => 99999,
                        'oxamount'         => 0,
                        'oxamountto'       => 99999,
                        'oxactive'         => 1,
                        'oxarticles'       => array( 1002, 1006 ),
                        'oxsort'           => 30,
                ),
                3 => array(
                        'oxid'         => 'percentFor1003',
                        'oxaddsum'     => 5.5,
                        'oxaddsumtype' => '%',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( 1003, 1007 ),
                        'oxsort'       => 40,
                ),
        ),
        'expected' => array(
                1000 => array(
                        'base_price'        => '79,90',
                        'price'             => '73,51',
                        'rrp_price'         => '91,89',
                        'show_rrp'          => true
                ),
                1001 => array(
                        'base_price'        => '79,90',
                        'price'             => '101,08',
                        'rrp_price'         => '',
                        'show_rrp'          => false
                ),
                1002 => array(
                        'base_price'        => '79,90',
                        'price'             => '96,67',
                        'rrp_price'         => '',
                        'show_rrp'          => false
                ),
                1003 => array(
                        'base_price'        => '79,90',
                        'price'             => '86,84',
                        'rrp_price'         => '91,89',
                        'show_rrp'          => true
                ),
                1004 => array(
                        'base_price'        => '89,90',
                        'price'             => '82,71',
                        'rrp_price'         => '91,89',
                        'show_rrp'          => true
                ),
                1005 => array(
                        'base_price'        => '89,90',
                        'price'             => '113,73',
                        'rrp_price'         => '',
                        'show_rrp'          => false
                ),
                1006 => array(
                        'base_price'        => '89,90',
                        'price'             => '108,77',
                        'rrp_price'         => '',
                        'show_rrp'          => false
                ),
                1007 => array(
                        'base_price'        => '89,90',
                        'price'             => '97,70',
                        'rrp_price'         => '',
                        'show_rrp'          => false
                ),
        ),
        'options' => array(
                'config' => array(
                        'blEnterNetPrice' => true,
                        'blShowNetPrice' => false,
                        'dDefaultVAT' => 15,
                ),
                'activeCurrencyRate' => 1,
        ),
);
