<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_19',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'fc78a1ce5fcfe88aea8f998320b69',
      'oxprice' => 481.61,
      'oxvat' => 32,
      'amount' => 681,
    ),
    1 =>
    array(
      'oxid' => '51c2aa138902d5e704cda29611b15',
      'oxprice' => 508.45,
      'oxvat' => 19,
      'amount' => 938,
    ),
    2 =>
    array(
      'oxid' => '08ef3a396c503b7c64f2ebde74683',
      'oxprice' => 888.12,
      'oxvat' => 19,
      'amount' => 709,
    ),
    3 =>
    array(
      'oxid' => '923f3b89727e8c9928e1fd332e785',
      'oxprice' => 97.18,
      'oxvat' => 32,
      'amount' => 220,
    ),
    4 =>
    array(
      'oxid' => 'c539c774e35f05fc2f4a06ebbe828',
      'oxprice' => 286.92,
      'oxvat' => 19,
      'amount' => 707,
    ),
    5 =>
    array(
      'oxid' => '6bd8eed729227ac9337978cf03a59',
      'oxprice' => 355.13,
      'oxvat' => 19,
      'amount' => 383,
    ),
    6 =>
    array(
      'oxid' => '914d5038380073d23e0342c7e972c',
      'oxprice' => 292.65,
      'oxvat' => 19,
      'amount' => 218,
    ),
    7 =>
    array(
      'oxid' => '1d16fbb989e307ed43be18c53661c',
      'oxprice' => 15.09,
      'oxvat' => 32,
      'amount' => 517,
    ),
    8 =>
    array(
      'oxid' => '5b17a1d1492fd672340a98f00ecd4',
      'oxprice' => 125.18,
      'oxvat' => 32,
      'amount' => 271,
    ),
    9 =>
    array(
      'oxid' => '6c4a1c7f5ea7f3a3a91c733eedb57',
      'oxprice' => 543.84,
      'oxvat' => 32,
      'amount' => 1,
    ),
    10 =>
    array(
      'oxid' => '44d49056cb2418ef56b6f26b1386c',
      'oxprice' => 767.2,
      'oxvat' => 19,
      'amount' => 155,
    ),
    11 =>
    array(
      'oxid' => 'ec264b8c3ff25470da4bc1d847091',
      'oxprice' => 214.78,
      'oxvat' => 32,
      'amount' => 906,
    ),
    12 =>
    array(
      'oxid' => '7c67fe22b54cb732ecd46f7476e07',
      'oxprice' => 255.8,
      'oxvat' => 32,
      'amount' => 744,
    ),
    13 =>
    array(
      'oxid' => '3bef99ac68fa7bfdbb1e939c5c8e5',
      'oxprice' => 962.43,
      'oxvat' => 19,
      'amount' => 109,
    ),
    14 =>
    array(
      'oxid' => '09e21af4a6bbb2a75c4b76177e974',
      'oxprice' => 151.97,
      'oxvat' => 32,
      'amount' => 817,
    ),
    15 =>
    array(
      'oxid' => '8f63846e6dd056ace658633a6325b',
      'oxprice' => 662.5,
      'oxvat' => 19,
      'amount' => 918,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 70,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'fc78a1ce5fcfe88aea8f998320b69',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 32,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'fc78a1ce5fcfe88aea8f998320b69',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 31,
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
        'oxaddsumtype' => '%',
        'oxaddsum' => 18,
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
        'oxaddsum' => 22,
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
        'oxdiscount' => 22,
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
      'fc78a1ce5fcfe88aea8f998320b69' =>
      array(
        0 => '635,73',
        1 => '432.932,13',
      ),
      '51c2aa138902d5e704cda29611b15' =>
      array(
        0 => '605,06',
        1 => '567.546,28',
      ),
      '08ef3a396c503b7c64f2ebde74683' =>
      array(
        0 => '1.056,86',
        1 => '749.313,74',
      ),
      '923f3b89727e8c9928e1fd332e785' =>
      array(
        0 => '128,28',
        1 => '28.221,60',
      ),
      'c539c774e35f05fc2f4a06ebbe828' =>
      array(
        0 => '341,43',
        1 => '241.391,01',
      ),
      '6bd8eed729227ac9337978cf03a59' =>
      array(
        0 => '422,60',
        1 => '161.855,80',
      ),
      '914d5038380073d23e0342c7e972c' =>
      array(
        0 => '348,25',
        1 => '75.918,50',
      ),
      '1d16fbb989e307ed43be18c53661c' =>
      array(
        0 => '19,92',
        1 => '10.298,64',
      ),
      '5b17a1d1492fd672340a98f00ecd4' =>
      array(
        0 => '165,24',
        1 => '44.780,04',
      ),
      '6c4a1c7f5ea7f3a3a91c733eedb57' =>
      array(
        0 => '717,87',
        1 => '717,87',
      ),
      '44d49056cb2418ef56b6f26b1386c' =>
      array(
        0 => '912,97',
        1 => '141.510,35',
      ),
      'ec264b8c3ff25470da4bc1d847091' =>
      array(
        0 => '283,51',
        1 => '256.860,06',
      ),
      '7c67fe22b54cb732ecd46f7476e07' =>
      array(
        0 => '337,66',
        1 => '251.219,04',
      ),
      '3bef99ac68fa7bfdbb1e939c5c8e5' =>
      array(
        0 => '1.145,29',
        1 => '124.836,61',
      ),
      '09e21af4a6bbb2a75c4b76177e974' =>
      array(
        0 => '200,60',
        1 => '163.890,20',
      ),
      '8f63846e6dd056ace658633a6325b' =>
      array(
        0 => '788,38',
        1 => '723.732,84',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        32 => '288.219,74',
        19 => '444.835,39',
      ),
      'wrapping' =>
      array(
        'brutto' => '21.792,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '715.526,45',
        'netto' => '601.282,73',
        'vat' => '114.243,72',
      ),
      'payment' =>
      array(
        'brutto' => '1.454.057,22',
        'netto' => '1.221.896,82',
        'vat' => '232.160,40',
      ),
      'voucher' =>
      array(
        'brutto' => '44,00',
      ),
      'totalNetto' => '3.241.925,58',
      'totalBrutto' => '3.975.024,71',
      'grandTotal' => '6.166.356,38',
    ),
  ),
);
