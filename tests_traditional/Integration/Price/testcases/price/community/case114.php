<?php
/*
 * Price enter mode: brutto
 * Price view mode: brutto
 * Discounts: 1
 *  1. shop 10 %
 */
$aData = array(
        'articles' => array(
                0 => array(
                        'oxid'                     => 'tomatoes',
                        'oxprice'                  => 1001,
                        'oxvat'                    => 20,
                        'oxunitname'               => 'kg',
                        'oxunitquantity'           => 10,
                        'oxweight'                 => 10
                ),
        ),
        'discounts' => array(
                0 => array(
                        'oxid'         => 'percent',
                        'oxaddsum'     => -10,
                        'oxaddsumtype' => '%',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array( 'tomatoes' ),
                        'oxsort'       => 10
                ),
        ),
        'expected' => array(
                'tomatoes' => array(
                        'base_price'    => '1.001,00',
                        'price'         => '1.101,10',
                        'unit_price'    => '110,11'
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
