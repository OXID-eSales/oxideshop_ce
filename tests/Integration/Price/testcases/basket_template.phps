<?php
/**
 * Price enter mode: netto / brutto
 * Price view mode: netto / brutto
 * Product count: count of used products
 * VAT info: count of used vat's (list)
 * Currency rate: 1.0 (change if needed)
 * Discounts: count
 *  1. shop / basket; abs / %; bargain;
 *  2. ...
 *  ...
 * Vouchers: count
 *  1. voucher rule
 *  2 ...
 *  ...
 * Wrapping: + / -
 * Gift cart: + / -;
 * Costs VAT caclulation rule: max / proportional
 * Costs:
 *  1. Payment + / -
 *  2. Delivery + / -
 *  3. TS + / -
 * Actions with basket or order:
 *  1. update / delete / change config
 *  2. ...
 *  ...
 * Short description: bug entry / support case other info;
 */
$aData = array(
    // Articles
    'articles' => array (
        0 => array (
                // oxarticles db fields
                'oxid'                     => 1001,
                'oxprice'                  => 0.00,
                'oxvat'                    => 0,
                // Amount in basket
                'amount'                   => 1,
                'scaleprices' => array(
                        'oxaddabs'     => 0.00,
                        'oxamount'     => 1,
                        'oxamountto'   => 3,
                        'oxartid'      => 1001
                ),
        ),
        1 => array (

        ),
    ),
    // Categories
    'categories' => array (
            0 =>  array (
                    'oxid'     => '30e44ab8593023055.23928895',
                    'oxactive' => 1,
                    'oxtitle'  => 'Bar-Equipment',
                    'articles' => ( 1126 )
            ),
    ),
    // User
    'user' => array(
            'oxactive' => 1,
            'oxusername' => 'basketUser',
            // country id, for example this is United States, make sure country with specified ID is active
            'oxcountryid' => '8f241f11096877ac0.98748826',
    ),
    // Group
    'group' => array (
            0 => array (
                    'oxid' => 'oxidpricea',
                    'oxactive' => 1,
                    'oxtitle' => 'Price A',
                    'oxobject2group' => array (
                            'oxobjectid' => array( 1001, 'basketUser' ),
                    ),
            ),
            1 => array (
                    'oxid' => 'oxidpriceb',
                    'oxactive' => 1,
                    'oxtitle' => 'Price B',
                    'oxobject2group' => array (
                            'oxobjectid' => array( '30e44ab8593023055.23928895' ),
                    ),
            ),
    ),
    // Discounts
    'discounts' => array (
        // oxdiscount DB fields
        0 => array (
            // ID needed for expectation later on, specify meaningful name
            'oxid'         => 'absolutediscount',
            'oxaddsum'     => 1,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            '...' => '',
            // If for article, specify here
            'oxarticles' => array ( 9001 ),
        ),
        1 => array (

        ),
    ),

    // Additional costs
    'costs' => array(
        // oxwrapping db fields
        'wrapping' => array(
            // Wrapping
            0 => array(
                'oxtype' => 'WRAP',
                'oxname' => 'testWrap9001',
                'oxprice' => 9,
                'oxactive' => 1,
                '...' => '',
                // If for article, specify here
                'oxarticles' => array( 9001 )
            ),
            // Giftcard
            1 => array(
                'oxtype' => 'CARD',
                'oxname' => 'testCard',
                'oxprice' => 0.30,
                'oxactive' => 1,
            ),
        ),
        // Delivery
        'delivery' => array(
            0 => array(
                // oxdelivery DB fields
                'oxactive' => 1,
                'oxaddsum' => 1,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparamend' => 99999,
                'oxcategories' => array(
                    'categoryId1',
                    '...'
                ),
                '...' => ''
            ),
        ),
        // Payment
        'payment' => array(
            0 => array(
                // oxpayments DB fields
                'oxaddsum' => 1,
                'oxaddsumtype' => 'abs',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
                '...' => ''
            ),
        ),
        // VOUCHERS
        'voucherserie' => array (
            0 => array (
                // oxvoucherseries DB fields
                'oxdiscount' => 1.00,
                'oxdiscounttype' => 'absolute',
                'oxallowsameseries' => 1,
                'oxallowotherseries' => 1,
                'oxallowuseanother' => 1,
                '...' => '',
                // voucher of this voucherserie count
                'voucher_count' => 1
            ),
        ),
    ),
    // TEST EXPECTATIONS
    'expected' => array (
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array (
             9001 => array ( '0,00', '0.000,00' ),
             9002 => array ( '0,00', '0.000,00' ),
        ),
        // Expectations of other totals
        'totals' => array (
            // Total BRUTTO
            'totalBrutto' => '0.000,00',
            // Total NETTO
            'totalNetto'  => '0.000,00',
            // Total VAT amount: vat% => total cost
            'vats' => array (
                19 => '0,00'
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array (
                // Expectation for special discount with specified ID
                'absolutediscount' => '0,00',
            ),
            // Total wrapping amounts
            'wrapping' => array(
                'brutto' => '0,00',
                'netto' => '0,00',
                'vat' => '0,00'
            ),
            // Total giftcard amounts
            'giftcard' => array (
                'brutto' => '0,00',
                'netto' => '0,00',
                'vat' => '0,00'
            ),
            // Total delivery amounts
            'delivery' => array(
                'brutto' => '0,00',
                'netto' => '0,00',
                'vat' => '0,00'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '0,00',
                'netto' => '0,00',
                'vat' => '0,00'
            ),
            // Total voucher amounts
            'voucher' => array (
                'brutto' => '0,00',
            ),
            // GRAND TOTAL
            'grandTotal'  => '0.000,00'
        ),
    ),
    // Test case options
    'options' => array (
        // Configs (real named)
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false,
        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);
