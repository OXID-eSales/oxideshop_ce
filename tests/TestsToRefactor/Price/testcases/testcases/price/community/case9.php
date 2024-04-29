<?php
/*
 * Price enter mode: brutto
 * Price view mode: brutto
 * Discounts: 2
 *  1. shop; abs
 *  2. shop; %
 */
$aData = array(
        'articles' => array(
                0 => array(
                        'oxid'                     => '1001_a',
                        'oxprice'                  => 5.02,
                        'oxvat'                    => 20,
                ),
                1 => array(
                        'oxid'                     => '1001_b',
                        'oxprice'                  => 5.02,
                        'oxvat'                    => 20,
                ),
        ),
        'discounts' => array(
                0 => array(
                        'oxid'         => 'abs',
                        'oxaddsum'     => 20,
                        'oxaddsumtype' => 'abs',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1001_a' ),
                        'oxsort'           => 10,
                ),
                1 => array(
                        'oxid'         => 'percent',
                        'oxaddsum'     => 20,
                        'oxaddsumtype' => '%',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( '1001_b' ),
                        'oxsort'           => 20,
                ),
        ),
        'expected' => array(
                '1001_a' => array(
                        'base_price' => '5,02',
                        'price' => '0,00',
                ),
                '1001_b' => array(
                        'base_price' => '5,02',
                        'price' => '4,02',
                ),
        ),
        'options' => array(
                'config' => array(
                        'blEnterNetPrice' => false,
                        'blShowNetPrice' => false,
                ),
                'activeCurrencyRate' => 1,
        ),
);
