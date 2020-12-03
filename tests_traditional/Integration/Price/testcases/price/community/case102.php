<?php
/* RRP = 94.52
 * Price enter mode: netto
 * Price view mode: brutto
 * Product count: 4
 * VAT info: 15
 * Discount number: 4
 *  1. shop; %
 */
$aData = array(
        'articles' => array(
                0 => array(
                        'oxid'            => '1000',
                        'oxprice'         => 100,
                        'oxtprice'        => 94.52
                ),
                1 => array(
                        'oxid'            => '1001',
                        'oxprice'         => 100,
                        'oxtprice'        => 94.52
                ),
                2 => array(
                        'oxid'            => '1002',
                        'oxprice'         => 100,
                        'oxtprice'        => 94.52
                ),
                3 => array(
                        'oxid'            => '1003',
                        'oxprice'         => 100,
                        'oxtprice'        => 94.52
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
                        'oxarticles'       => array( 1002 ),
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
                        'oxarticles' => array( 1003 ),
                        'oxsort'       => 40,
                ),
        ),
        'expected' => array(
                1000 => array(
                        'base_price'        => '100,00',
                        'price'             => '92,00',
                        'rrp_price'         => '108,70',
                        'show_rrp'          => true
                ),
                1001 => array(
                        'base_price'        => '100,00',
                        'price'             => '126,50',
                        'rrp_price'         => '',
                        'show_rrp'          => false
                ),
                1002 => array(
                        'base_price'        => '100,00',
                        'price'             => '120,98',
                        'rrp_price'         => '',
                        'show_rrp'          => false
                ),
                1003 => array(
                        'base_price'        => '100,00',
                        'price'             => '108,68',
                        'rrp_price'         => '108,70',
                        'show_rrp'          => true
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
