<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_23',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'f3ef565757375457fe3bc9bd859b8',
      'oxprice' => 139.51,
      'oxvat' => 19,
      'amount' => 262,
    ),
    1 =>
    array(
      'oxid' => '44e409d94cedfdb42ba7d1b455867',
      'oxprice' => 120.55,
      'oxvat' => 40,
      'amount' => 931,
    ),
    2 =>
    array(
      'oxid' => 'daadc536db833ff7cd2f62942b416',
      'oxprice' => 570.95,
      'oxvat' => 19,
      'amount' => 441,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 26,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'f3ef565757375457fe3bc9bd859b8',
          1 => '44e409d94cedfdb42ba7d1b455867',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 56,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'f3ef565757375457fe3bc9bd859b8',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 29,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 4,
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
        'oxaddsumtype' => '%',
        'oxaddsum' => 17,
        'oxactive' => 1,
        'oxdeltype' => 'p',
        'oxfinalize' => 0,
        'oxparam' => 0,
        'oxparamend' => 999999999,
        'oxfixed' => 0,
      ),
      1 =>
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
    'voucherserie' =>
    array(
      0 =>
      array(
        'oxdiscount' => 25,
        'oxdiscounttype' => 'percent',
        'oxallowsameseries' => 1,
        'oxallowotherseries' => 1,
        'oxallowuseanother' => 1,
        'voucher_count' => 2,
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
      'f3ef565757375457fe3bc9bd859b8' =>
      array(
        0 => '166,02',
        1 => '43.497,24',
      ),
      '44e409d94cedfdb42ba7d1b455867' =>
      array(
        0 => '168,77',
        1 => '157.124,87',
      ),
      'daadc536db833ff7cd2f62942b416' =>
      array(
        0 => '679,43',
        1 => '299.628,63',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        19 => '30.816,45',
        40 => '25.252,21',
      ),
      'wrapping' =>
      array(
        'brutto' => '38.878,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '85.050,63',
        'netto' => '71.471,12',
        'vat' => '13.579,51',
      ),
      'payment' =>
      array(
        'brutto' => '106.268,08',
        'netto' => '89.300,91',
        'vat' => '16.967,17',
      ),
      'voucher' =>
      array(
        'brutto' => '218.859,70',
      ),
      'totalNetto' => '225.322,38',
      'totalBrutto' => '500.250,74',
      'grandTotal' => '511.587,75',
    ),
  ),
);
