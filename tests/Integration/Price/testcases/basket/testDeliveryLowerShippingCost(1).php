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
 * Brutto-Brutto mode.
 * Short description:
 * http://www.oxid-esales.com/en/support-services/documentation-and-help/archive-oxid-eshop/administer-eshop/set-shipping/lower-shipping-cost.html
 * https://bugs.oxid-esales.com/view.php?id=4123
 *
 * books and stuff added
 */
$aData = array(

    'categories' => array(
        0 =>  array(
            'oxid'       => 'books',
            'oxparentid' => 'oxrootid',
            'oxshopid'   => 1,
            'oxactive'   => 1,
            'oxarticles' => array( 'book' )
        ),
        1 =>  array(
            'oxid'       => 'otherStuff',
            'oxparentid' => 'oxrootid',
            'oxshopid'   => 1,
            'oxactive'   => 1,
            'oxarticles' => array( 'stuff' )
        ),
    ),

    'articles' => array(
        0 => array(
            'oxid'                     => 'stuff',
            'oxprice'                  => 20,
            'oxvat'                    => 20,
            'amount'                   => 6,
        ),
        1 => array(
            'oxid'                     => 'book',
            'oxprice'                  => 10,
            'oxvat'                    => 10,
            'amount'                   => 1,
        ),
    ),

    'costs' => array(
        'delivery' => array(
            0 => array(
                'oxactive' => 1,
                'oxaddsum' => 2,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'a',
                'oxparam' => 0, //from
                'oxparamend' => 99999, //to
                'oxsort' => 1,
            ),
            1 => array(
                'oxactive' => 1,
                'oxaddsum' => 3,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'a',
                'oxparam' => 0, //from
                'oxparamend' => 99999, //to
                'oxsort' => 2,
                'oxcategories' => array(
                    'otherStuff'
                ),
            ),
        ),
    ),
    'expected' => array(
        'articles' => array(
            'book' => array( '10,00', '10,00' ),
            'stuff' => array( '20,00', '120,00' )
        ),
        'totals' => array(
            'totalBrutto' => '130,00',
            'totalNetto'  => '109,09',
            'vats' => array(
                10 => '0,91',
                20 => '20,00'
            ),
            'delivery' => array(
                'brutto' => '5,00',
            ),
            'grandTotal'  => '135,00'
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
