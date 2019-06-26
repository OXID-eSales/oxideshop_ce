<?php
/**
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 1;
 * Currency rate:1;
 * Costs VAT calculation rule: biggest_net;
 * Costs:
 *  1. Delivery;
 * 0003101: delivery cost is not recalculated after discount in basket
 * 6 with discount total price less than 150, but more without discount
 */
$aData = array(

    'skipped' => 1, // remove when #3101 will be fixed

    'articles' => array(
        0 => array(
            'oxid'                     => 'vine1',
            'oxprice'                  => 27.9,
            'oxvat'                    => 19,
            'amount'                   => 6,
        ),
    ),

    'discounts' => array(
        0 => array(
            'oxid'         => 'discount11',
            'oxaddsum'     => 11,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxsort' => 10,
        ),
    ),

    'costs' => array(
        'delivery' => array(
            0 => array(
                'oxtitle' => 'less than 150 EUR',
                'oxactive' => 1,
                'oxaddsum' => 10,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparam' => 0, //from
                'oxparamend' => 150, //to
                'oxsort' => 1,
            ),
            1 => array(
                'oxtitle' => 'more than 150 EUR',
                'oxactive' => 1,
                'oxaddsum' => 0,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparam' => 150.01, //from
                'oxparamend' => 99999, //to
                'oxsort' => 2,
            ),
        ),
    ),
    'expected' => array(
        'articles' => array(
            'vine1' => array( '27,90', '167,40' ),
        ),
        'totals' => array(
            'totalBrutto' => '167,40',
            'totalNetto'  => '125,20',
            'vats' => array(
                19 => '23,79',
            ),
            'delivery' => array(
                'brutto' => '10,00',
            ),
            'discounts' => array(
                'discount11' => '18,41',
            ),
            'grandTotal'  => '158,99'
        ),
    ),
    'options' => array(
        'activeCurrencyRate' => 1,
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false,
            'blShowVATForWrapping' => false,
            'blShowVATForDelivery' => false,
            'sAdditionalServVATCalcMethod' => 'biggest_net',
        ),
    ),
);
