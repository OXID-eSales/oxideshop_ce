<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_3',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'ca6c01c0ff7bc9b2324f35f87bced',
      'oxprice' => 863.83,
      'oxvat' => 1,
      'amount' => 861,
    ),
    1 =>
    array(
      'oxid' => '329a40f543795f781250cf2ca6c09',
      'oxprice' => 458.17,
      'oxvat' => 29,
      'amount' => 46,
    ),
    2 =>
    array(
      'oxid' => '9ef9f6bac80c2bce9f519d9b7236b',
      'oxprice' => 367.32,
      'oxvat' => 1,
      'amount' => 443,
    ),
    3 =>
    array(
      'oxid' => 'fb41e1aa295393325bcd73526eb96',
      'oxprice' => 59.84,
      'oxvat' => 35,
      'amount' => 26,
    ),
    4 =>
    array(
      'oxid' => 'ca981b316be3383f55677e544856c',
      'oxprice' => 398.12,
      'oxvat' => 1,
      'amount' => 947,
    ),
    5 =>
    array(
      'oxid' => '8a148f9f985047a943116d8a4d90f',
      'oxprice' => 713.35,
      'oxvat' => 1,
      'amount' => 599,
    ),
    6 =>
    array(
      'oxid' => 'ff132b4d802f171e9d0cc6f5cfb84',
      'oxprice' => 475.44,
      'oxvat' => 29,
      'amount' => 985,
    ),
    7 =>
    array(
      'oxid' => '508591fe45fee06e7c19891bcd30c',
      'oxprice' => 735.52,
      'oxvat' => 35,
      'amount' => 101,
    ),
    8 =>
    array(
      'oxid' => '1ded0429f9e7db7826d997165e8fc',
      'oxprice' => 570.37,
      'oxvat' => 1,
      'amount' => 644,
    ),
    9 =>
    array(
      'oxid' => '6bce0b2375d0bb8531bb3f5b5ed90',
      'oxprice' => 713.16,
      'oxvat' => 1,
      'amount' => 964,
    ),
    10 =>
    array(
      'oxid' => 'eeb47da6fb249d93bdc17e3e2913c',
      'oxprice' => 334.67,
      'oxvat' => 29,
      'amount' => 203,
    ),
    11 =>
    array(
      'oxid' => '5feb79013addb2bf77ea7840a74ec',
      'oxprice' => 255.03,
      'oxvat' => 29,
      'amount' => 244,
    ),
    12 =>
    array(
      'oxid' => '4d7a25920f67f5cc9a77c6b5f97c8',
      'oxprice' => 448.98,
      'oxvat' => 29,
      'amount' => 706,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 6,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'ca6c01c0ff7bc9b2324f35f87bced',
          1 => '329a40f543795f781250cf2ca6c09',
          2 => '9ef9f6bac80c2bce9f519d9b7236b',
          3 => 'fb41e1aa295393325bcd73526eb96',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 95,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'ca6c01c0ff7bc9b2324f35f87bced',
          1 => '329a40f543795f781250cf2ca6c09',
          2 => '9ef9f6bac80c2bce9f519d9b7236b',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 9,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 1,
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
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 12,
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
        'oxdiscount' => 11,
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
      'ca6c01c0ff7bc9b2324f35f87bced' =>
      array(
        0 => '872,47',
        1 => '751.196,67',
      ),
      '329a40f543795f781250cf2ca6c09' =>
      array(
        0 => '591,04',
        1 => '27.187,84',
      ),
      '9ef9f6bac80c2bce9f519d9b7236b' =>
      array(
        0 => '370,99',
        1 => '164.348,57',
      ),
      'fb41e1aa295393325bcd73526eb96' =>
      array(
        0 => '80,78',
        1 => '2.100,28',
      ),
      'ca981b316be3383f55677e544856c' =>
      array(
        0 => '402,10',
        1 => '380.788,70',
      ),
      '8a148f9f985047a943116d8a4d90f' =>
      array(
        0 => '720,48',
        1 => '431.567,52',
      ),
      'ff132b4d802f171e9d0cc6f5cfb84' =>
      array(
        0 => '613,32',
        1 => '604.120,20',
      ),
      '508591fe45fee06e7c19891bcd30c' =>
      array(
        0 => '992,95',
        1 => '100.287,95',
      ),
      '1ded0429f9e7db7826d997165e8fc' =>
      array(
        0 => '576,07',
        1 => '370.989,08',
      ),
      '6bce0b2375d0bb8531bb3f5b5ed90' =>
      array(
        0 => '720,29',
        1 => '694.359,56',
      ),
      'eeb47da6fb249d93bdc17e3e2913c' =>
      array(
        0 => '431,72',
        1 => '87.639,16',
      ),
      '5feb79013addb2bf77ea7840a74ec' =>
      array(
        0 => '328,99',
        1 => '80.273,56',
      ),
      '4d7a25920f67f5cc9a77c6b5f97c8' =>
      array(
        0 => '579,18',
        1 => '408.901,08',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        1 => '27.655,79',
        29 => '271.591,83',
        35 => '26.544,95',
      ),
      'wrapping' =>
      array(
        'brutto' => '128.406,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '369.350,42',
        'netto' => '365.693,49',
        'vat' => '3.656,93',
      ),
      'payment' =>
      array(
        'brutto' => '402.577,97',
        'netto' => '398.592,05',
        'vat' => '3.985,92',
      ),
      'voucher' =>
      array(
        'brutto' => '22,00',
      ),
      'totalNetto' => '3.777.945,60',
      'totalBrutto' => '4.103.760,17',
      'grandTotal' => '5.004.072,56',
    ),
  ),
);
