<?php
/**
 * Price enter mode: brutto
 * Price view mode: brutto
 * Product count: 2
 * VAT info: 5%
 * Currency rate: 1.0
 * Discounts: 1 item discount
 * Short description:
 *  Price Calculation of multiplied Itm Discount wrong if out of stock
 *  (https://bugs.oxid-esales.com/view.php?id=5913)
 */
$aData = array(
    'articles' => array(
        0 => array(
            'oxid'                     => 1000,
            'oxprice'                  => 50.00,
            'oxstock'                  => 100,
            'oxvat'                    => 19,
            'oxartnum'                 => '1000',
            'amount'                   => 2,
        ),
        1 => array(
            'oxid'                     => 1003,
            'oxprice'                  => 5.00,
            'oxstock'                  => 1,
            'oxvat'                    => 19,
            'oxstockflag'              => 2,
            'oxartnum'                 => '1003',
        ),
    ),
    'discounts' => array(
        0 => array(
            'oxid'         => 'testitmdiscount',
            'oxshopid' => 1,
            'oxaddsum'     => 0,
            'oxaddsumtype' => 'itm',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxprice' => 0,
            'oxpriceto' => 0,
            'oxactive' => 1,
            'oxitmartid' => 1003,
            'oxitmamount' => 1,
            'oxitmmultiple' => 1,
            'oxarticles' => array(1000),
            'oxsort' => 10,
        ),
    ),
    'expected' => array(
        'articles' => array(
            1000 => array( '50,00', '100,00' ),
            1003 => array( '0,00', '0,00' ),
        ),
        'totals' => array(
            'totalBrutto' => '100,00',
            'totalNetto'  => '84,03',
            'vats' => array(
                19 => '15,97'
            ),
            'grandTotal'  => '100,00'
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
