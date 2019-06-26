<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_16',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'e94a1f9049d06a24f789ab55b8d67',
      'oxprice' => 378.93,
      'oxvat' => 6,
      'amount' => 583,
    ),
    1 =>
    array(
      'oxid' => '5cf9515c822f768d253106a620e6d',
      'oxprice' => 988.85,
      'oxvat' => 6,
      'amount' => 9,
    ),
    2 =>
    array(
      'oxid' => '12ac2910e36ee862efd218fc20261',
      'oxprice' => 101.07,
      'oxvat' => 6,
      'amount' => 552,
    ),
    3 =>
    array(
      'oxid' => '128c23b525cb20f4b84ad9167ed5a',
      'oxprice' => 389.71,
      'oxvat' => 6,
      'amount' => 490,
    ),
    4 =>
    array(
      'oxid' => '847e07b634b3f2314c4740e21002d',
      'oxprice' => 378.87,
      'oxvat' => 6,
      'amount' => 325,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 3,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'e94a1f9049d06a24f789ab55b8d67',
          1 => '5cf9515c822f768d253106a620e6d',
          2 => '12ac2910e36ee862efd218fc20261',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 19,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'e94a1f9049d06a24f789ab55b8d67',
          1 => '5cf9515c822f768d253106a620e6d',
          2 => '12ac2910e36ee862efd218fc20261',
          3 => '128c23b525cb20f4b84ad9167ed5a',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 14,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
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
        'oxaddsum' => 8,
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
        'oxaddsum' => 14,
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
        'oxdiscount' => 1,
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
      'e94a1f9049d06a24f789ab55b8d67' =>
      array(
        0 => '401,67',
        1 => '234.173,61',
      ),
      '5cf9515c822f768d253106a620e6d' =>
      array(
        0 => '1.048,18',
        1 => '9.433,62',
      ),
      '12ac2910e36ee862efd218fc20261' =>
      array(
        0 => '107,13',
        1 => '59.135,76',
      ),
      '128c23b525cb20f4b84ad9167ed5a' =>
      array(
        0 => '413,09',
        1 => '202.414,10',
      ),
      '847e07b634b3f2314c4740e21002d' =>
      array(
        0 => '401,60',
        1 => '130.520,00',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        6 => '35.265,69',
      ),
      'wrapping' =>
      array(
        'brutto' => '31.046,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '89.002,79',
        'netto' => '83.964,90',
        'vat' => '5.037,89',
      ),
      'payment' =>
      array(
        'brutto' => '99.684,19',
        'netto' => '94.041,69',
        'vat' => '5.642,50',
      ),
      'voucher' =>
      array(
        'brutto' => '12.649,97',
      ),
      'totalNetto' => '587.761,43',
      'totalBrutto' => '635.677,09',
      'grandTotal' => '842.760,10',
    ),
  ),
);
