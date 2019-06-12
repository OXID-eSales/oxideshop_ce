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
      'oxid' => '8176aaf484187c03769d9e5cc6550',
      'oxprice' => 534.53,
      'oxvat' => 22,
      'amount' => 114,
    ),
    1 =>
    array(
      'oxid' => '09adb93d36ab632a7a53134e228c2',
      'oxprice' => 20.23,
      'oxvat' => 22,
      'amount' => 957,
    ),
    2 =>
    array(
      'oxid' => 'e980cd16209e6021210d42d9d8c91',
      'oxprice' => 760.76,
      'oxvat' => 22,
      'amount' => 108,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 31,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8176aaf484187c03769d9e5cc6550',
          1 => '09adb93d36ab632a7a53134e228c2',
          2 => 'e980cd16209e6021210d42d9d8c91',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 89,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8176aaf484187c03769d9e5cc6550',
          1 => '09adb93d36ab632a7a53134e228c2',
          2 => 'e980cd16209e6021210d42d9d8c91',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 78,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8176aaf484187c03769d9e5cc6550',
          1 => '09adb93d36ab632a7a53134e228c2',
          2 => 'e980cd16209e6021210d42d9d8c91',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 36,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 23,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 91,
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
        'oxaddsum' => 16,
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
        'oxaddsum' => 5,
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
        'oxaddsum' => 62,
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
      '8176aaf484187c03769d9e5cc6550' =>
      array(
        0 => '652,13',
        1 => '74.342,82',
      ),
      '09adb93d36ab632a7a53134e228c2' =>
      array(
        0 => '24,68',
        1 => '23.618,76',
      ),
      'e980cd16209e6021210d42d9d8c91' =>
      array(
        0 => '928,13',
        1 => '100.238,04',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        22 => '35.740,92',
      ),
      'wrapping' =>
      array(
        'brutto' => '91.962,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '83,00',
        'netto' => '68,03',
        'vat' => '14,97',
      ),
      'payment' =>
      array(
        'brutto' => '36,00',
        'netto' => '29,51',
        'vat' => '6,49',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '162.458,70',
      'totalBrutto' => '198.199,62',
      'grandTotal' => '290.280,62',
    ),
  ),
);
