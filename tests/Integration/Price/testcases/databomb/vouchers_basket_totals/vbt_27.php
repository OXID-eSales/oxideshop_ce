<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_27',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '3951e7d277f01f8fe846437d4556b',
      'oxprice' => 328.81,
      'oxvat' => 5,
      'amount' => 632,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 81,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '3951e7d277f01f8fe846437d4556b',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 5,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '3951e7d277f01f8fe846437d4556b',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 92,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 28,
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
        'oxaddsum' => 41,
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
        'oxaddsum' => 95,
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
        'oxdiscount' => 6,
        'oxdiscounttype' => 'percent',
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
      '3951e7d277f01f8fe846437d4556b' =>
      array(
        0 => '328,81',
        1 => '207.807,92',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        5 => '8.219,14',
      ),
      'wrapping' =>
      array(
        'brutto' => '3.160,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '85.296,25',
        'netto' => '81.234,52',
        'vat' => '4.061,73',
      ),
      'payment' =>
      array(
        'brutto' => '92,00',
        'netto' => '87,62',
        'vat' => '4,38',
      ),
      'voucher' =>
      array(
        'brutto' => '35.205,99',
      ),
      'totalNetto' => '164.382,79',
      'totalBrutto' => '207.807,92',
      'grandTotal' => '261.150,18',
    ),
  ),
);
