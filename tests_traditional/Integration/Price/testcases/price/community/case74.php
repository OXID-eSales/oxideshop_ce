<?php
/*
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 3
 * VAT info: 20, 33.55
 * Discounts: 6
 *  1. shop + abs : 20, 33.55, 5.5
 *  2. shop + %   : 20, 33.55, 5.5
 */
$aData = array(
        'articles' => array(
                1 => array(
                        'oxid'                     => '1001_a',
                        'oxprice'                  => 99,
                        'oxvat'                    => 20
                ),
                2 => array(
                        'oxid'                     => '1001_b',
                        'oxprice'                  => 99,
                        'oxvat'                    => 20
                ),
                3 => array(
                        'oxid'                     => '1002_a',
                        'oxprice'                  => 299,
                        'oxvat'                    => 20
                ),
                4 => array(
                        'oxid'                     => '1002_b',
                        'oxprice'                  => 299,
                        'oxvat'                    => 20
                ),
                5 => array(
                        'oxid'                     => '1003_a',
                        'oxprice'                  => 1,
                        'oxvat'                    => 33.55
                ),
                6 => array(
                        'oxid'                     => '1003_b',
                        'oxprice'                  => 1,
                        'oxvat'                    => 33.55
                ),
        ),
        'discounts' => array(
                1 => array(
                        'oxid'         => 'absFor1001',
                        'oxaddsum'     => 5.5,
                        'oxaddsumtype' => 'abs',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1001_a' ),
                        'oxsort'           => 10,
                ),
                2 => array(
                        'oxid'         => 'percentFor1001',
                        'oxaddsum'     => 5.5,
                        'oxaddsumtype' => '%',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1001_b' ),
                        'oxsort'           => 20,
                ),
                3 => array(
                        'oxid'         => 'absFor1002',
                        'oxaddsum'     => 20,
                        'oxaddsumtype' => 'abs',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1002_a' ),
                        'oxsort'           => 30,
                ),
                4 => array(
                        'oxid'         => 'percentFor1002',
                        'oxaddsum'     => 20,
                        'oxaddsumtype' => '%',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1002_b' ),
                        'oxsort'           => 40,
                ),
                5 => array(
                        'oxid'         => 'absFor1003',
                        'oxaddsum'     => 33.55,
                        'oxaddsumtype' => 'abs',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1003_a' ),
                        'oxsort'           => 50,
                ),
                6 => array(
                        'oxid'         => 'percentFor1003',
                        'oxaddsum'     => 33.55,
                        'oxaddsumtype' => '%',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1003_b' ),
                        'oxsort'           => 60,
                ),
        ),
        'expected' => array(
                '1001_a' => array(
                        'base_price' => '99,00',
                        'price' => '93,50',
                ),
                '1001_b' => array(
                        'base_price' => '99,00',
                        'price' => '93,56',
                ),
                '1002_a' => array(
                        'base_price' => '299,00',
                        'price' => '279,00',
                ),
                '1002_b' => array(
                        'base_price' => '299,00',
                        'price' => '239,20',
                ),
                '1003_a' => array(
                        'base_price' => '1,00',
                        'price' => '0,00',
                ),
                '1003_b' => array(
                        'base_price' => '1,00',
                        'price' => '0,66',
                ),
        ),
        'options' => array(
                'config' => array(
                        'blEnterNetPrice' => true,
                        'blShowNetPrice' => true,
                ),
                'activeCurrencyRate' => 1,
        ),
);
