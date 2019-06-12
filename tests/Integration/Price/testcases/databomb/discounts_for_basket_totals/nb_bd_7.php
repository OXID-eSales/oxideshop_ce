<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_bd_databomb_user_7',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'b1672fa55bf14dab2783bdb1e4db6',
      'oxprice' => 39.79,
      'oxvat' => 28,
      'amount' => 284,
    ),
    1 =>
    array(
      'oxid' => 'ffd9e1dacaca8cc915df72db26338',
      'oxprice' => 561.99,
      'oxvat' => 28,
      'amount' => 272,
    ),
    2 =>
    array(
      'oxid' => 'b0ed1ff73999edc48e2233c8de2d1',
      'oxprice' => 324.07,
      'oxvat' => 28,
      'amount' => 255,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 14,
      'oxid' => 'bombDiscount_0',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    1 =>
    array(
      'oxaddsum' => 7,
      'oxid' => 'bombDiscount_1',
      'oxaddsumtype' => '%',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    2 =>
    array(
      'oxaddsum' => 2,
      'oxid' => 'bombDiscount_2',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    3 =>
    array(
      'oxaddsum' => 6,
      'oxid' => 'bombDiscount_3',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    4 =>
    array(
      'oxaddsum' => 6,
      'oxid' => 'bombDiscount_4',
      'oxaddsumtype' => '%',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 49,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'b1672fa55bf14dab2783bdb1e4db6',
          1 => 'ffd9e1dacaca8cc915df72db26338',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 94,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'b1672fa55bf14dab2783bdb1e4db6',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 69,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'b1672fa55bf14dab2783bdb1e4db6',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 19,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 32,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 24,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
    ),
    'delivery' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 1,
        'oxactive' => 1,
        'oxdeltype' => 'p',
        'oxfinalize' => 0,
        'oxparam' => 0,
        'oxparamend' => 999999999,
        'oxfixed' => 0,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 11,
        'oxactive' => 1,
        'oxdeltype' => 'p',
        'oxfinalize' => 0,
        'oxparam' => 0,
        'oxparamend' => 999999999,
        'oxfixed' => 0,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 8,
        'oxactive' => 1,
        'oxdeltype' => 'p',
        'oxfinalize' => 0,
        'oxparam' => 0,
        'oxparamend' => 999999999,
        'oxfixed' => 0,
      ),
    ),
  ),
  'options' =>
  array(
    'config' =>
    array(
      'blEnterNetPrice' => true,
      'blShowNetPrice' => false,
    ),
    'activeCurrencyRate' => 1,
  ),
  'expected' =>
  array(
    'articles' =>
    array(
      'b1672fa55bf14dab2783bdb1e4db6' =>
      array(
        0 => '50,93',
        1 => '14.464,12',
      ),
      'ffd9e1dacaca8cc915df72db26338' =>
      array(
        0 => '719,35',
        1 => '195.663,20',
      ),
      'b0ed1ff73999edc48e2233c8de2d1' =>
      array(
        0 => '414,81',
        1 => '105.776,55',
      ),
    ),
    'totals' =>
    array(
      'discounts' =>
      array(
        'bombDiscount_0' => '14,00',
        'bombDiscount_1' => '22.112,29',
        'bombDiscount_2' => '2,00',
        'bombDiscount_3' => '6,00',
        'bombDiscount_4' => '17.626,17',
      ),
      'vats' =>
      array(
        28 => '60.406,37',
      ),
      'wrapping' =>
      array(
        'brutto' => '32.924,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '34.758,43',
        'netto' => '27.155,02',
        'vat' => '7.603,41',
      ),
      'payment' =>
      array(
        'brutto' => '59.071,35',
        'netto' => '46.149,49',
        'vat' => '12.921,86',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '215.737,04',
      'totalBrutto' => '315.903,87',
      'grandTotal' => '402.897,19',
    ),
  ),
);
