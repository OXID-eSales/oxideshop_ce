<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_4',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '2741f19ab53497333a677d5882db7',
      'oxprice' => 233.02,
      'oxvat' => 42,
      'amount' => 237,
    ),
    1 =>
    array(
      'oxid' => 'c25e027d314d15e8770f84e4ceaf9',
      'oxprice' => 447.03,
      'oxvat' => 37,
      'amount' => 184,
    ),
    2 =>
    array(
      'oxid' => '32fa56b63f6d748e4d7409a101a54',
      'oxprice' => 128.71,
      'oxvat' => 37,
      'amount' => 765,
    ),
    3 =>
    array(
      'oxid' => '400114f248b42dde6796128499013',
      'oxprice' => 919.74,
      'oxvat' => 32,
      'amount' => 255,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 78,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '2741f19ab53497333a677d5882db7',
          1 => 'c25e027d314d15e8770f84e4ceaf9',
          2 => '32fa56b63f6d748e4d7409a101a54',
          3 => '400114f248b42dde6796128499013',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 66,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '2741f19ab53497333a677d5882db7',
          1 => 'c25e027d314d15e8770f84e4ceaf9',
          2 => '32fa56b63f6d748e4d7409a101a54',
          3 => '400114f248b42dde6796128499013',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 18,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 37,
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
        'oxaddsum' => 50,
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
        'oxaddsum' => 27,
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
        'oxdiscount' => 21,
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
      '2741f19ab53497333a677d5882db7' =>
      array(
        0 => '233,02',
        1 => '55.225,74',
      ),
      'c25e027d314d15e8770f84e4ceaf9' =>
      array(
        0 => '447,03',
        1 => '82.253,52',
      ),
      '32fa56b63f6d748e4d7409a101a54' =>
      array(
        0 => '128,71',
        1 => '98.463,15',
      ),
      '400114f248b42dde6796128499013' =>
      array(
        0 => '919,74',
        1 => '234.533,70',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        42 => '8.053,48',
        37 => '24.063,60',
        32 => '28.032,55',
      ),
      'wrapping' =>
      array(
        'brutto' => '95.106,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '235.265,06',
        'netto' => '178.231,11',
        'vat' => '57.033,95',
      ),
      'payment' =>
      array(
        'brutto' => '18,00',
        'netto' => '13,64',
        'vat' => '4,36',
      ),
      'voucher' =>
      array(
        'brutto' => '238.513,04',
      ),
      'totalNetto' => '171.813,44',
      'totalBrutto' => '470.476,11',
      'grandTotal' => '562.352,13',
    ),
  ),
);
