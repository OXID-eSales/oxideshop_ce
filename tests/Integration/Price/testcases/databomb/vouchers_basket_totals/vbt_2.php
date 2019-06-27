<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_2',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'f1f865b24a1d1b1bf23065f928fda',
      'oxprice' => 478.62,
      'oxvat' => 21,
      'amount' => 53,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 79,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'f1f865b24a1d1b1bf23065f928fda',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 90,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'f1f865b24a1d1b1bf23065f928fda',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 76,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 52,
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
        'oxaddsum' => 54,
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
        'oxaddsum' => 69,
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
        'oxdiscount' => 24,
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
      'f1f865b24a1d1b1bf23065f928fda' =>
      array(
        0 => '478,62',
        1 => '25.366,86',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        21 => '1.932,60',
      ),
      'wrapping' =>
      array(
        'brutto' => '4.770,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '17.557,13',
        'netto' => '14.510,02',
        'vat' => '3.047,11',
      ),
      'payment' =>
      array(
        'brutto' => '21.806,35',
        'netto' => '18.021,78',
        'vat' => '3.784,57',
      ),
      'voucher' =>
      array(
        'brutto' => '14.231,43',
      ),
      'totalNetto' => '9.202,83',
      'totalBrutto' => '25.366,86',
      'grandTotal' => '55.268,91',
    ),
  ),
);
