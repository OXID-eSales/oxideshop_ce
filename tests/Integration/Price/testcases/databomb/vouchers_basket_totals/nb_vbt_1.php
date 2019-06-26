<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_1',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'a3d7e308d12afdfc071f700258e80',
      'oxprice' => 154.95,
      'oxvat' => 28,
      'amount' => 485,
    ),
    1 =>
    array(
      'oxid' => '3838734c1b00bb03bf47dcac4bc11',
      'oxprice' => 174.1,
      'oxvat' => 28,
      'amount' => 980,
    ),
    2 =>
    array(
      'oxid' => '033abc72976362fa7c50ca57e7da3',
      'oxprice' => 458.52,
      'oxvat' => 28,
      'amount' => 859,
    ),
    3 =>
    array(
      'oxid' => 'aa1ebb261ef375ae09f2015a8fa07',
      'oxprice' => 185.59,
      'oxvat' => 28,
      'amount' => 367,
    ),
    4 =>
    array(
      'oxid' => 'f8f5fed0e7300bb42be75bed65a5c',
      'oxprice' => 160.9,
      'oxvat' => 28,
      'amount' => 562,
    ),
    5 =>
    array(
      'oxid' => '1f29d5cba0b41f66ba28a823a8914',
      'oxprice' => 207.54,
      'oxvat' => 28,
      'amount' => 711,
    ),
    6 =>
    array(
      'oxid' => 'd4db2e778c367df2a9c86042990bc',
      'oxprice' => 155.66,
      'oxvat' => 28,
      'amount' => 740,
    ),
    7 =>
    array(
      'oxid' => '09875a4fd060420ffa1e9c682cf1e',
      'oxprice' => 764,
      'oxvat' => 28,
      'amount' => 535,
    ),
    8 =>
    array(
      'oxid' => '20b924571ca0bfeeba81eb1675acf',
      'oxprice' => 282.27,
      'oxvat' => 28,
      'amount' => 696,
    ),
    9 =>
    array(
      'oxid' => '54169d1ca8508625030981c48e931',
      'oxprice' => 496.28,
      'oxvat' => 28,
      'amount' => 912,
    ),
    10 =>
    array(
      'oxid' => '991a3a24100bd9ba5a6a18c57f3ed',
      'oxprice' => 359.37,
      'oxvat' => 28,
      'amount' => 20,
    ),
    11 =>
    array(
      'oxid' => 'e333126c14384b84f783e9001f171',
      'oxprice' => 751.07,
      'oxvat' => 28,
      'amount' => 420,
    ),
    12 =>
    array(
      'oxid' => '4d889f7b67680daef4fb69fa23ab5',
      'oxprice' => 458.06,
      'oxvat' => 28,
      'amount' => 793,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 42,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'a3d7e308d12afdfc071f700258e80',
          1 => '3838734c1b00bb03bf47dcac4bc11',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 56,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'a3d7e308d12afdfc071f700258e80',
          1 => '3838734c1b00bb03bf47dcac4bc11',
          2 => '033abc72976362fa7c50ca57e7da3',
          3 => 'aa1ebb261ef375ae09f2015a8fa07',
          4 => 'f8f5fed0e7300bb42be75bed65a5c',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 26,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 33,
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
        'oxaddsum' => 23,
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
        'oxaddsum' => 15,
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
        'oxdiscount' => 14,
        'oxdiscounttype' => 'absolute',
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
      'a3d7e308d12afdfc071f700258e80' =>
      array(
        0 => '198,34',
        1 => '96.194,90',
      ),
      '3838734c1b00bb03bf47dcac4bc11' =>
      array(
        0 => '222,85',
        1 => '218.393,00',
      ),
      '033abc72976362fa7c50ca57e7da3' =>
      array(
        0 => '586,91',
        1 => '504.155,69',
      ),
      'aa1ebb261ef375ae09f2015a8fa07' =>
      array(
        0 => '237,56',
        1 => '87.184,52',
      ),
      'f8f5fed0e7300bb42be75bed65a5c' =>
      array(
        0 => '205,95',
        1 => '115.743,90',
      ),
      '1f29d5cba0b41f66ba28a823a8914' =>
      array(
        0 => '265,65',
        1 => '188.877,15',
      ),
      'd4db2e778c367df2a9c86042990bc' =>
      array(
        0 => '199,24',
        1 => '147.437,60',
      ),
      '09875a4fd060420ffa1e9c682cf1e' =>
      array(
        0 => '977,92',
        1 => '523.187,20',
      ),
      '20b924571ca0bfeeba81eb1675acf' =>
      array(
        0 => '361,31',
        1 => '251.471,76',
      ),
      '54169d1ca8508625030981c48e931' =>
      array(
        0 => '635,24',
        1 => '579.338,88',
      ),
      '991a3a24100bd9ba5a6a18c57f3ed' =>
      array(
        0 => '459,99',
        1 => '9.199,80',
      ),
      'e333126c14384b84f783e9001f171' =>
      array(
        0 => '961,37',
        1 => '403.775,40',
      ),
      '4d889f7b67680daef4fb69fa23ab5' =>
      array(
        0 => '586,32',
        1 => '464.951,76',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        28 => '785.287,03',
      ),
      'wrapping' =>
      array(
        'brutto' => '182.168,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '38,00',
        'netto' => '29,69',
        'vat' => '8,31',
      ),
      'payment' =>
      array(
        'brutto' => '26,00',
        'netto' => '20,31',
        'vat' => '5,69',
      ),
      'voucher' =>
      array(
        'brutto' => '28,00',
      ),
      'totalNetto' => '2.804.596,53',
      'totalBrutto' => '3.589.911,56',
      'grandTotal' => '3.772.115,56',
    ),
  ),
);
