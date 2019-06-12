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

    'articles' => array(
        0 => array(
            'oxid'                     => 'vine1',
            'oxprice'                  => 100,
            'oxvat'                    => 19,
            'amount'                   => 1,
        ),

        1 => array(
            'oxid'                     => 'coupon',
            'oxprice'                  => 50,
            'oxvat'                    => 19,
            'amount'                   => 2,
            'oxnonmaterial'            => true
        ),
    ),


    'costs' => array(
        'delivery' => array(
            0 => array(
                'oxtitle' => '10% from total ',
                'oxactive' => 1,
                'oxaddsum' => 10,
                'oxaddsumtype' => '%',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparam' => 0, //from
                'oxparamend' => 1000, //to
                'oxsort' => 1,
            ),

        ),
    ),
    'expected' => array(
        'articles' => array(
            'vine1' => array( '100,00', '100,00' ),
            'coupon' => array( '50,00', '100,00' ),
        ),
        'totals' => array(
            'totalBrutto' => '200,00',
            'totalNetto'  => '168,07',
            'vats' => array(
                19 => '31,93',
            ),
            'delivery' => array(
                'brutto' => '10,00',
            ),
            'grandTotal'  => '210,00'
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
            'blExclNonMaterialFromDelivery' => true,
        ),
    ),
);
