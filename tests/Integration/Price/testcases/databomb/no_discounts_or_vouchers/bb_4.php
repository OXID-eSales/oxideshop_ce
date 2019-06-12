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
      'oxid' => 'be03ff50fad81646ff9548e7c67a2',
      'oxprice' => 561.88,
      'oxvat' => 42,
      'amount' => 893,
    ),
    1 =>
    array(
      'oxid' => '0e181c47bd990f5605f02ea7a30b8',
      'oxprice' => 172.99,
      'oxvat' => 42,
      'amount' => 132,
    ),
    2 =>
    array(
      'oxid' => '37427e09f14fe8ee394235f57bc37',
      'oxprice' => 501.18,
      'oxvat' => 11,
      'amount' => 510,
    ),
    3 =>
    array(
      'oxid' => 'ae4fc1114759a41c48c953feb906e',
      'oxprice' => 200.09,
      'oxvat' => 28,
      'amount' => 24,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 36,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'be03ff50fad81646ff9548e7c67a2',
          1 => '0e181c47bd990f5605f02ea7a30b8',
          2 => '37427e09f14fe8ee394235f57bc37',
          3 => 'ae4fc1114759a41c48c953feb906e',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 15,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'be03ff50fad81646ff9548e7c67a2',
          1 => '0e181c47bd990f5605f02ea7a30b8',
          2 => '37427e09f14fe8ee394235f57bc37',
          3 => 'ae4fc1114759a41c48c953feb906e',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 16,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'be03ff50fad81646ff9548e7c67a2',
          1 => '0e181c47bd990f5605f02ea7a30b8',
          2 => '37427e09f14fe8ee394235f57bc37',
          3 => 'ae4fc1114759a41c48c953feb906e',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 7,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 94,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 25,
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
        'oxaddsum' => 9,
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
        'oxaddsum' => 68,
        'oxactive' => 1,
        'oxdeltype' => 'p',
        'oxfinalize' => 0,
        'oxparam' => 0,
        'oxparamend' => 999999999,
        'oxfixed' => 0,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 39,
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
      'blEnterNetPrice' => false,
      'blShowNetPrice' => false,
    ),
    'activeCurrencyRate' => 1,
  ),
  'expected' =>
  array(
    'articles' =>
    array(
      'be03ff50fad81646ff9548e7c67a2' =>
      array(
        0 => '561,88',
        1 => '501.758,84',
      ),
      '0e181c47bd990f5605f02ea7a30b8' =>
      array(
        0 => '172,99',
        1 => '22.834,68',
      ),
      '37427e09f14fe8ee394235f57bc37' =>
      array(
        0 => '501,18',
        1 => '255.601,80',
      ),
      'ae4fc1114759a41c48c953feb906e' =>
      array(
        0 => '200,09',
        1 => '4.802,16',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        42 => '155.161,46',
        11 => '25.329,91',
        28 => '1.050,47',
      ),
      'wrapping' =>
      array(
        'brutto' => '24.944,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '910.597,08',
        'netto' => '641.265,55',
        'vat' => '269.331,53',
      ),
      'payment' =>
      array(
        'brutto' => '118.691,62',
        'netto' => '83.585,65',
        'vat' => '35.105,97',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '603.455,64',
      'totalBrutto' => '784.997,48',
      'grandTotal' => '1.839.230,18',
    ),
  ),
);
