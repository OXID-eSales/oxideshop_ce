<?php
/**
 * Price enter mode: netto / netto
 * Price view mode: netto / netto
 * Product count: 1001 and 1002
 * VAT info: count of used vat's (list)
 * Currency rate: 1.0 
 * Discounts: -
 * Wrapping:  -
 * Gift cart: -;
 * Costs VAT caclulation rule: proportiona
  * Trusted Shop:
 *  1. "TS080501_2500_30_EUR"  "netto" => "4.12", "amount" => "2500" ,
 * Wrapping: -;
 * Gift cart:  -;
 * Costs:
 *  1. Payment + 
 *  2. Delivery + 
 *  3. TS -
 * Short description: 
 * Neto-Neto mode. Additiona products Neto-Neto. Calculate VAT according to the proportional value Testing trusted shop
 */
$aData = array(
    // Product
    'articles' => array (
        0 => array (
                // oxarticles db fields
                'oxid'                     => 1001,
                'oxprice'                  => 30.00,
                'oxvat'                    => 25,
                // Amount in basket
                'amount'                   => 15,
        ),
	    1 => array (
	     // oxarticles db fields
		        'oxid'                     => 1002,
		        'oxprice'                  => 100.00,
		        'oxvat'                    => 20,
				'amount'                   => 15,
	    ),

    ),
	'trustedshop' => array (
        'product_id'     => 'TS080501_2500_30_EUR',           // trusted shop product id
        'payments'    => array(                              // paymentids
            'oxidcashondel'  => 'DIRECT_DEBIT',
            'oxidcreditcard' => 'DIRECT_DEBIT',
            'oxiddebitnote'  => 'DIRECT_DEBIT',
            'oxidpayadvance' => 'DIRECT_DEBIT',
            'oxidinvoice'    => 'DIRECT_DEBIT',
            'oxempty'        => 'DIRECT_DEBIT',
        )
    ),

	     // Additional costs
    'costs' => array(
        // oxwrapping db fields
        // Delivery
        'delivery' => array(
              0 => array(
                // oxdelivery DB fields
                'oxactive' => 1,
                'oxaddsum' => 55.00,
                'oxaddsumtype' => '%',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparamend' => 99999,
            ),
        ),
        // Payment
        'payment' => array(
             0 => array(
                // oxpayments DB fields
                'oxaddsum' => 55.00,
                'oxaddsumtype' => '%',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
				'oxaddsumrules'=>1,
            ),
        ),
    ),

    // TEST EXPECTATIONS
    'expected' => array (
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array (
             1001 => array ( '30,00', '450,00' ),
             1002 => array ( '100,00', '1.500,00' ),
        ),
        // Expectations of other totals
        'totals' => array (
            // Total BRUTTO
            'totalBrutto' => '2.362,50',
            // Total NETTO
            'totalNetto'  => '1.950,00',
            // Total VAT amount: vat% => total cost
            'vats' => array (
                25 => '112,50',
				20 => '300,00',

            ),
			  // Total delivery amounts
        'delivery' => array(
                'brutto' => '1.299,38',
                'netto' => '1.072,50',
                'vat' => '226,88'
         ),
            // Total payment amounts
        'payment' => array(
                'brutto' => '1.299,38',
                'netto' => '1.072,50',
                'vat' => '226,88'
        ),
		    'trustedshop' => array(
                'brutto' => '4,99',
                'netto' => '4,12',
                'vat' => '0,87'
            ),
            // GRAND TOTAL
            'grandTotal'  => '4.966,25'
        ),
    ),
    // Test case options
    'options' => array (
        // Configs (real named)
        'config' => array(
            'blEnterNetPrice' => true,
            'blShowNetPrice' => true,
            'blShowVATForDelivery'=> true,
            'blShowVATForPayCharge'=> true,
            'blShowVATForWrapping'=> true,
            'sAdditionalServVATCalcMethod' => 'proportional', 
            'blDeliveryVatOnTop' => true,
            'blPaymentVatOnTop' => true,
            'blWrappingVatOnTop' => true,
        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);