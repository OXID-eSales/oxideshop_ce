<?php
/* RRP = 10
 * Price enter mode: brutto
 * Price view mode: netto
 * Product count: 6
 * VAT info: 15
 * Discount number: 6
 *  1. shop; %
 */
$aData = array(
        'articles' => array(
                0 => array(
                        'oxid'            => '1000',
                        'oxprice'         => 200,
                        'oxtprice'        => 10
                ),
                1 => array(
                        'oxid'            => '1001',
                        'oxprice'         => 200,
                        'oxtprice'        => 10
                ),
                2 => array(
                        'oxid'            => '1002',
                        'oxprice'         => 0.05,
                        'oxtprice'        => 10
                ),
                3 => array(
                        'oxid'            => '1003',
                        'oxprice'         => 10,
                        'oxtprice'        => 10
                ),
                4 => array(
                        'oxid'            => '1004',
                        'oxprice'         => 200,
                        'oxtprice'        => 10
                ),
                5 => array(
                        'oxid'            => '1005',
                        'oxprice'         => 200,
                        'oxtprice'        => 10
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
                        'oxarticles'       => array( 1000 ),
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
                        'oxarticles' => array( 1001 ),
                        'oxsort'           => 20,
                ),
                2 => array(
                        'oxid'             => 'percentFor1002',
                        'oxaddsum'         => 20,
                        'oxaddsumtype'     => '%',
                        'oxprice'          => 0,
                        'oxpriceto'        => 99999,
                        'oxamount'         => 0,
                        'oxamountto'       => 99999,
                        'oxactive'         => 1,
                        'oxarticles'       => array( 1002 ),
                        'oxsort'           => 30,
                ),
                3 => array(
                        'oxid'         => 'percentFor1003',
                        'oxaddsum'     => 10,
                        'oxaddsumtype' => '%',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( 1003 ),
                        'oxsort'           => 40,
                ),
                4 => array(
                        'oxid'             => 'percentFor1004',
                        'oxaddsum'         => -5.2,
                        'oxaddsumtype'     => '%',
                        'oxprice'          => 0,
                        'oxpriceto'        => 99999,
                        'oxamount'         => 0,
                        'oxamountto'       => 99999,
                        'oxactive'         => 1,
                        'oxarticles'       => array( 1004 ),
                        'oxsort'           => 50,
                ),
                5 => array(
                        'oxid'         => 'percentFor1005',
                        'oxaddsum'     => 5.5,
                        'oxaddsumtype' => '%',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( 1005 ),
                        'oxsort'           => 60,
                ),
        ),
        'expected' => array(
                1000 => array(
                        'base_price'        => '200,00',
                        'price'             => '139,13',
                        'rrp_price'         => '',
                        'show_rrp'          => false
                ),
                1001 => array(
                        'base_price'        => '200,00',
                        'price'             => '191,30',
                        'rrp_price'         => '',
                        'show_rrp'          => false
                ),
                1002 => array(
                        'base_price'        => '0,05',
                        'price'             => '0,03',
                        'rrp_price'         => '8,70',
                        'show_rrp'          => true
                ),
                1003 => array(
                        'base_price'        => '10,00',
                        'price'             => '7,83',
                        'rrp_price'         => '8,70',
                        'show_rrp'          => true
                ),
                1004 => array(
                        'base_price'        => '200,00',
                        'price'             => '182,95',
                        'rrp_price'         => '',
                        'show_rrp'          => false
                ),
                1005 => array(
                        'base_price'        => '200,00',
                        'price'             => '164,34',
                        'rrp_price'         => '',
                        'show_rrp'          => false
                ),

        ),
        'options' => array(
                'config' => array(
                        'blEnterNetPrice' => false,
                        'blShowNetPrice' => true,
                        'dDefaultVAT' => 15,
                ),
                'activeCurrencyRate' => 1,
        ),
);
