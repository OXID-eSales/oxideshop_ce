<?php
/*
 * Price enter mode: brutto / netto
 * Price view mode: brutto / netto
 * Discount number
 *  1. shop; abs/%
 */
$aData = array (
        'articles' => array (
                0 => array (
                        'oxid'            => '1001_a',
                        'oxshopid'        => 2,
                        'oxprice'         => 100.55,
                        'oxvat'           => 20,
                        'oxunitname'      => 'kg',
                        'oxunitquantity'  => 30,
                        'oxweight'        => 10,
                        'scaleprices' => array (
                                'oxartid'      => '1001_a',
                                'oxaddabs'     => '5',
                                'oxaddperc'    => '35',
                                'oxamount'     => '1',
                                'oxamountto'   => '20',
                        ),
                        'field2shops' => array (
                                'oxartid'      => '1001_a',
                                'oxshopid'     => '5',
                                'oxprice'    => '35',
                                'oxpricea'     => '1',
                                'oxpriceb'   => '20',
                                'oxpricec'   => '20',
                        ),
                ),
        ),
        'user' => array(
                'oxid' => '_testUser',
                'oxactive' => 1,
                'oxusername' => 'bGroupUser',
        ),
        'group' => array (
                0 => array (
                        'oxid' => 'oxidpriceb',
                        'oxactive' => 1,
                        'oxtitle' => 'Price B',
                        'oxobject2group' => array ( '_testUser' ),
                ),
        ),
        // shop id's will be from 2
        'shop' => array (
                0 => array (
                        'oxactive'     => 1,
                        'oxparentid'   => 0,
                        'oxname'       => 'subshop',
                        // this option sets shop to active or not
                        'activeshop'     => true
                ),
        ),
        'discounts' => array (
                0 => array (
                        'oxid'         => 'abs',
                        'oxaddsum'     => 20,
                        'oxaddsumtype' => 'abs',
                        'oxprice'    => 0,
                        'oxpriceto' => 99999,
                        'oxamount' => 0,
                        'oxamountto' => 99999,
                        'oxactive' => 1,
                        'oxarticles' => array ( '1001_a' ),
                ),
        ),
        'expected' => array (
                '1001_a' => array (
                        'base_price'        => '65,36',     // getBasePrice() custom formatted
                        'price'             => '58,43',     // getFPrice()
                        'unit_price'        => '1,95',      // getFUnitPrice()
                        'min_price'         => '58,43',     // getFMinPrice()
                        'var_min_price'     => '58,43',     // getFVarMinPrice()
                        'is_range_price'    => false,       // isRangePrice()
                        'rrp_price'         => '0,00',      // getTPrice()->getPrice()
                        'show_rrp'          => false        // result of comparing rrp_price > price
                ),
        ),
        'options' => array (
                'config' => array(
                        'blEnterNetPrice' => false,
                        'blShowNetPrice' => false,
                        'dDefaultVAT' => 20,
                ),
                'activeCurrencyRate' => 1,
        ),
);