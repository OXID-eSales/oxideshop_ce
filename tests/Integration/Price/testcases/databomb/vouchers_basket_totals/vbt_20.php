<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_20',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '01c9103a1902bfaae7b950a2bc7cf',
      'oxprice' => 812.2,
      'oxvat' => 21,
      'amount' => 923,
    ),
    1 =>
    array(
      'oxid' => 'd07e6dc66ed60e916d835f72eb91e',
      'oxprice' => 102.85,
      'oxvat' => 21,
      'amount' => 419,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 5,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '01c9103a1902bfaae7b950a2bc7cf',
          1 => 'd07e6dc66ed60e916d835f72eb91e',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 87,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '01c9103a1902bfaae7b950a2bc7cf',
          1 => 'd07e6dc66ed60e916d835f72eb91e',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 32,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 62,
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
        'oxaddsum' => 98,
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
        'oxaddsum' => 47,
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
        'oxdiscount' => 33,
        'oxdiscounttype' => 'absolute',
        'oxallowsameseries' => 1,
        'oxallowotherseries' => 1,
        'oxallowuseanother' => 1,
        'voucher_count' => 3,
      ),
    ),
  ),
  'options' =>
  array(
    'config' =>
    array(
      'blEnterNetPrice' => false,
      'blShowNetPrice' => false,
    ),
    'activeCurrencyRate' => 1,
  ),
  'expected' =>
  array(
    'articles' =>
    array(
      '01c9103a1902bfaae7b950a2bc7cf' =>
      array(
        0 => '812,20',
        1 => '749.660,60',
      ),
      'd07e6dc66ed60e916d835f72eb91e' =>
      array(
        0 => '102,85',
        1 => '43.094,15',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        21 => '137.568,35',
      ),
      'wrapping' =>
      array(
        'brutto' => '116.754,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '776.946,66',
        'netto' => '642.104,68',
        'vat' => '134.841,98',
      ),
      'payment' =>
      array(
        'brutto' => '502.272,77',
        'netto' => '415.101,46',
        'vat' => '87.171,31',
      ),
      'voucher' =>
      array(
        'brutto' => '99,00',
      ),
      'totalNetto' => '655.087,40',
      'totalBrutto' => '792.754,75',
      'grandTotal' => '2.188.629,18',
    ),
  ),
);
