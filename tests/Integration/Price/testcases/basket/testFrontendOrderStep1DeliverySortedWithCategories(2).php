<?php
/**
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 5;
 * VAT info:  count of used vat =3(20%, 10%);
 * Currency rate:1;
 * Costs VAT caclulation rule: biggest_net;
 * Costs:
 *  1. Delivery;
 * Brutto-Brutto mode.
 * Short description:
 * Given 2 products, 2 categories and 2 delivery costs.
 * When in basket are added 2 items and cost rules are active for these items, also cost rules are sorted desc.
 * Then prices are calculated with shipping cost.
 */
$aData = array(

    //'skipped' => 1,

    'categories' => array(
        0 =>  array(
            'oxid'       => 'testCategory1',
            'oxparentid' => 'oxrootid',
            'oxshopid'   => 1,
            'oxactive'   => 1,
            'oxarticles' => array( '_test_10012' )
        ),
        1 =>  array(
            'oxid'       => 'testCategory2',
            'oxparentid' => 'oxrootid',
            'oxshopid'   => 1,
            'oxactive'   => 1,
            'oxarticles' => array( '_test_1002' )
        ),
    ),

    'articles' => array(
        0 => array(
            'oxid'                     => '_test_1002',
            'oxprice'                  => 20,
            'oxvat'                    => 20,
            'amount'                   => 6,
        ),
        1 => array(
            'oxid'                     => '_test_10012',
            'oxprice'                  => 10,
            'oxvat'                    => 10,
            'amount'                   => 1,
        ),
    ),

    'costs' => array(
        'delivery' => array(
            0 => array(
                'oxactive' => 1,
                'oxaddsum' => 0,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'a',
                'oxfinalize' => 1,
                'oxparam' => 5, //from
                'oxparamend' => 99999, //to
                'oxsort' => 2,
                'oxcategories' => array(
                    'testCategory2' //uses article '_test_1002'
                ),
            ),
            1 => array(
                'oxactive' => 1,
                'oxaddsum' => 2,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'a',
                'oxfinalize' => 1,
                'oxparam' => 0, //from
                'oxparamend' => 99999, //to
                'oxsort' => 1,
                'oxcategories' => array(
                    'testCategory1' //uses article '_test_10012'
                ),
            ),
        ),
    ),
    'expected' => array(
        'articles' => array(
            '_test_10012' => array( '10,00', '10,00' ),
            '_test_1002' => array( '20,00', '120,00' )
        ),
        'totals' => array(
            'totalBrutto' => '130,00',
            'totalNetto'  => '109,09',
            'vats' => array(
                10 => '0,91',
                20 => '20,00'
            ),
            'delivery' => array(
                'brutto' => '2,00',
            ),
            'grandTotal'  => '132,00'
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
