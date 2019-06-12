<?php
/**
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 5;
 * VAT info:  count of used vat =3(20%, 10%);
 * Currency rate:1;
 * Costs VAT calculation rule: biggest_net;
 * Costs:
 *  1. Delivery;
 * 0004730: Orderrules with Quantity -> Items would be count double
 */
$aData = array(

    'categories' => array(
        0 =>  array(
            'oxid'       => 'vine',
            'oxparentid' => 'oxrootid',
            'oxshopid'   => 1,
            'oxactive'   => 1,
            'oxarticles' => array( 'vine1' )
        ),
        1 =>  array(
            'oxid'       => 'supplies',
            'oxparentid' => 'oxrootid',
            'oxshopid'   => 1,
            'oxactive'   => 1,
            'oxarticles' => array( 'supply1' )
        ),
    ),

    'articles' => array(
        0 => array(
            'oxid'                     => 'vine1',
            'oxprice'                  => 5,
            'oxvat'                    => 10,
            'amount'                   => 6,
        ),
        1 => array(
            'oxid'                     => 'supply1',
            'oxprice'                  => 10,
            'oxvat'                    => 10,
            'amount'                   => 1,
        ),
    ),

    'costs' => array(
        'delivery' => array(
            0 => array(
                'oxtitle' => 'more than 12 Bottles',
                'oxactive' => 1,
                'oxaddsum' => 0,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'a',
                'oxfinalize' => 1,
                'oxparam' => 12, //from
                'oxparamend' => 99999, //to
                'oxsort' => 1,
                'oxcategories' => array(
                    'vine'
                ),
            ),
            1 => array(
                'oxtitle' => '4 - 11 Bottles',
                'oxactive' => 1,
                'oxaddsum' => 5.9,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'a',
                'oxfinalize' => 1,
                'oxparam' => 4, //from
                'oxparamend' => 11, //to
                'oxsort' => 1,
                'oxcategories' => array(
                    'vine'
                ),
            ),
            2 => array(
                'oxtitle' => '1 - 3 Bottles',
                'oxactive' => 1,
                'oxaddsum' => 4.9,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'a',
                'oxfinalize' => 1,
                'oxparam' => 1, //from
                'oxparamend' => 3, //to
                'oxsort' => 1,
                'oxcategories' => array(
                    'vine'
                ),
            ),
            3 => array(
                'oxtitle' => 'supplies',
                'oxactive' => 1,
                'oxaddsum' => 2.9,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'a',
                'oxfinalize' => 1,
                'oxparam' => 0, //from
                'oxparamend' => 99999, //to
                'oxsort' => 4,
                'oxcategories' => array(
                    'supplies'
                ),
            ),
        ),
    ),
    'expected' => array(
        'articles' => array(
            'vine1' => array( '5,00', '30,00' ),
            'supply1' => array( '10,00', '10,00' )
        ),
        'totals' => array(
            'totalBrutto' => '40,00',
            'totalNetto'  => '36,36',
            'vats' => array(
                10 => '3,64',
            ),
            'delivery' => array(
                'brutto' => '5,90',
            ),
            'grandTotal'  => '45,90'
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
