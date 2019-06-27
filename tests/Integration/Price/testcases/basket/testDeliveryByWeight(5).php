<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 2;
 * VAT info:  count of used vat =1(19%);
 * Currency rate:1;
 * Discounts: -
 * Wrapping:  -;
 * Gift cart: -;
 * Costs VAT caclulation rule: biggest_net;
 * Gift cart:  -;
 * Vouchers: -;
 * Costs:
 *  1. Payment -;
 *  2. Delivery +3, for all delivery is set 'oxfixed' => 0(Once per Cart),;
 *  3. TS -
 * Short description:
 * Brutto-Brutto mode.
 * Short description: test added from selenium test (testDeliveryByWeight) ; checking on weight depending delivery costs,
 */
$aData = array(
    'articles' => array(
            0 => array(
                    'oxid'                     => 10011,
                    'oxprice'                  => 1.80,
                    'oxvat'                    => 19,
                    'amount'                   => 1,
                    'oxpricea'       		   => 0,
                    'oxpriceb' 			       => 0,
                    'oxpricec' 			       => 0,
                    'oxweight'                 => 2
            ),
            1 => array(
                    'oxid'                     => 10012,
                    'oxprice'                  => 2.00,
                    'oxvat'                    => 19,
                    'amount'                   => 1,
                    'oxweight'                 => 3
            ),

    ),

    'costs' => array(
        'delivery' => array(
            0 => array(
                'oxactive' => 1,
                'oxaddsum' => 10.00,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'w',
                'oxparam' => 15.00,
                'oxfinalize' => 0,
                'oxparamend' => 999,
                //Once per Cart
                'oxfixed' => 0,
                'oxsort' => 4,
            ),
            1 => array(
                'oxactive' => 1,
                'oxaddsum' => 1.00,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'w',
                'oxparam' => 1.00,
                'oxfinalize' => 0,
                'oxparamend' => 4.99999999,
                //Once per Cart
                'oxfixed' => 0,
                'oxsort' => 1,
            ),
            2 => array(
                'oxactive' => 1,
                'oxaddsum' => 5.00,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'w',
                'oxparam' => 5.00,
                'oxfinalize' => 0,
                'oxparamend' => 14.9999999 ,
                //Once per Cart
                'oxfixed' => 0,
                'oxsort' => 2,
            ),
        ),

    ),
    'expected' => array(
        'articles' => array(
                10011 => array( '1,80', '1,80' ),
                10012 => array( '2,00', '2,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '3,80',
                'totalNetto'  => '3,19',
                'vats' => array(
                        19 => '0,61',
                ),
                'delivery' => array(
                   'brutto' => '5,00',
                ),

                'grandTotal'  => '8,80'
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
