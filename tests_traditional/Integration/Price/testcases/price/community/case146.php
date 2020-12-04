<?php
/*
 * Price enter mode: brutto
 * Price view mode: netto
 * Product count: 4
 * VAT info: 4, 20
 * Discounts: 8
 *  1. shop + abs : 20, -5.2, 0
 *  2. shop + %   : 20, -5.2, 0
 */
$aData = array(
        'articles' => array(
                1 => array(
                        'oxid'                     => '1001_a',
                        'oxprice'                  => 100,
                        'oxvat'                    => 4,
                ),
                2 => array(
                        'oxid'                     => '1001_b',
                        'oxprice'                  => 100,
                        'oxvat'                    => 4,
                ),
                3 => array(
                        'oxid'                     => '1002_a',
                        'oxprice'                  => 99,
                ),
                4 => array(
                        'oxid'                     => '1002_b',
                        'oxprice'                  => 99,
                ),
                5 => array(
                        'oxid'                     => '1003_a',
                        'oxprice'                  => 299,
                ),
                6 => array(
                        'oxid'                     => '1003_b',
                        'oxprice'                  => 299,
                ),
                7 => array(
                        'oxid'                     => '1004_a',
                        'oxprice'                  => 0.6,
                ),
                8 => array(
                        'oxid'                     => '1004_b',
                        'oxprice'                  => 0.6,
                ),
        ),
        'discounts' => array(
                1 => array(
                        'oxid'         => 'absFor1001',
                        'oxaddsum'     => 0,
                        'oxaddsumtype' => 'abs',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1001_a' ),
                        'oxsort'       => 10,
                ),
                2 => array(
                        'oxid'         => 'percentFor1001',
                        'oxaddsum'     => 0,
                        'oxaddsumtype' => '%',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1001_b' ),
                        'oxsort'       => 20,
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
                        'oxsort'       => 30,
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
                        'oxsort'       => 40,
                ),
                5 => array(
                        'oxid'         => 'absFor1003',
                        'oxaddsum'     => -5.2,
                        'oxaddsumtype' => 'abs',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1003_a' ),
                        'oxsort'       => 50,
                ),
                6 => array(
                        'oxid'         => 'percentFor1003',
                        'oxaddsum'     => -5.2,
                        'oxaddsumtype' => '%',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1003_b' ),
                        'oxsort'       => 60,
                ),
                7 => array(
                        'oxid'         => 'absFor1004',
                        'oxaddsum'     => -5.2,
                        'oxaddsumtype' => 'abs',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1004_a' ),
                        'oxsort'       => 70,
                ),
                8 => array(
                        'oxid'         => 'percentFor1004',
                        'oxaddsum'     => -5.2,
                        'oxaddsumtype' => '%',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1004_b' ),
                        'oxsort'       => 80,
                ),
        ),
        'expected' => array(
                '1001_a' => array(
                        'base_price' => '100,00',
                        'price' => '96,15',
                ),
                '1001_b' => array(
                        'base_price' => '100,00',
                        'price' => '96,15',
                ),
                '1002_a' => array(
                        'base_price' => '99,00',
                        'price' => '62,50',
                ),
                '1002_b' => array(
                        'base_price' => '99,00',
                        'price' => '66,00',
                ),
                '1003_a' => array(
                        'base_price' => '299,00',
                        'price' => '254,37',
                ),
                '1003_b' => array(
                        'base_price' => '299,00',
                        'price' => '262,13',
                ),
                '1004_a' => array(
                        'base_price' => '0,60',
                        'price' => '5,70',
                ),
                '1004_b' => array(
                        'base_price' => '0,60',
                        'price' => '0,53',
                ),
        ),
        'options' => array(
                'config' => array(
                        'blEnterNetPrice' => false,
                        'blShowNetPrice' => true,
                        'dDefaultVAT' => 20,
                ),
                'activeCurrencyRate' => 1,
        ),
);
