<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_11',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'a4e85db0708e4de09001d4ed3e5d5',
      'oxprice' => 488.29,
      'oxvat' => 11,
      'amount' => 785,
    ),
    1 =>
    array(
      'oxid' => '1d31622e1d451325ab89e81d0a42b',
      'oxprice' => 785.76,
      'oxvat' => 11,
      'amount' => 719,
    ),
    2 =>
    array(
      'oxid' => 'ce40616891cd062273b334adce6db',
      'oxprice' => 889.14,
      'oxvat' => 11,
      'amount' => 670,
    ),
    3 =>
    array(
      'oxid' => '8b49066f045dc3d3cfbec79179ff8',
      'oxprice' => 423.68,
      'oxvat' => 11,
      'amount' => 141,
    ),
    4 =>
    array(
      'oxid' => 'ac74cc4d63166182b842d54144c6c',
      'oxprice' => 880.81,
      'oxvat' => 11,
      'amount' => 637,
    ),
    5 =>
    array(
      'oxid' => '2bbde5bb5b7336ec0a2a36e5df013',
      'oxprice' => 208.18,
      'oxvat' => 11,
      'amount' => 936,
    ),
    6 =>
    array(
      'oxid' => 'd9c00f802fde034f133ed2cfd896e',
      'oxprice' => 339.62,
      'oxvat' => 11,
      'amount' => 142,
    ),
    7 =>
    array(
      'oxid' => 'd05bad13d9a21adca84a8d17e1c80',
      'oxprice' => 327.57,
      'oxvat' => 11,
      'amount' => 602,
    ),
    8 =>
    array(
      'oxid' => 'ac11f7ee70106cca66cd78c5aaa06',
      'oxprice' => 123.76,
      'oxvat' => 11,
      'amount' => 990,
    ),
    9 =>
    array(
      'oxid' => 'c3f32e11a410736ba1cbcd8a23068',
      'oxprice' => 281.23,
      'oxvat' => 11,
      'amount' => 313,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 50,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'a4e85db0708e4de09001d4ed3e5d5',
          1 => '1d31622e1d451325ab89e81d0a42b',
          2 => 'ce40616891cd062273b334adce6db',
          3 => '8b49066f045dc3d3cfbec79179ff8',
          4 => 'ac74cc4d63166182b842d54144c6c',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 89,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'a4e85db0708e4de09001d4ed3e5d5',
          1 => '1d31622e1d451325ab89e81d0a42b',
          2 => 'ce40616891cd062273b334adce6db',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 22,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 31,
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
        'oxaddsum' => 22,
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
        'oxaddsum' => 2,
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
        'oxdiscount' => 4,
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
      'a4e85db0708e4de09001d4ed3e5d5' =>
      array(
        0 => '542,00',
        1 => '425.470,00',
      ),
      '1d31622e1d451325ab89e81d0a42b' =>
      array(
        0 => '872,19',
        1 => '627.104,61',
      ),
      'ce40616891cd062273b334adce6db' =>
      array(
        0 => '986,95',
        1 => '661.256,50',
      ),
      '8b49066f045dc3d3cfbec79179ff8' =>
      array(
        0 => '470,28',
        1 => '66.309,48',
      ),
      'ac74cc4d63166182b842d54144c6c' =>
      array(
        0 => '977,70',
        1 => '622.794,90',
      ),
      '2bbde5bb5b7336ec0a2a36e5df013' =>
      array(
        0 => '231,08',
        1 => '216.290,88',
      ),
      'd9c00f802fde034f133ed2cfd896e' =>
      array(
        0 => '376,98',
        1 => '53.531,16',
      ),
      'd05bad13d9a21adca84a8d17e1c80' =>
      array(
        0 => '363,60',
        1 => '218.887,20',
      ),
      'ac11f7ee70106cca66cd78c5aaa06' =>
      array(
        0 => '137,37',
        1 => '135.996,30',
      ),
      'c3f32e11a410736ba1cbcd8a23068' =>
      array(
        0 => '312,17',
        1 => '97.709,21',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        11 => '285.437,39',
      ),
      'wrapping' =>
      array(
        'brutto' => '232.386,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '62.529,00',
        'netto' => '56.332,43',
        'vat' => '6.196,57',
      ),
      'payment' =>
      array(
        'brutto' => '22,00',
        'netto' => '19,82',
        'vat' => '2,18',
      ),
      'voucher' =>
      array(
        'brutto' => '245.027,46',
      ),
      'totalNetto' => '2.594.885,39',
      'totalBrutto' => '3.125.350,24',
      'grandTotal' => '3.175.259,78',
    ),
  ),
);
