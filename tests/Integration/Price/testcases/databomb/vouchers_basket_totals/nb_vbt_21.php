<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_21',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '7d842fe98a19bc1a60922273034b0',
      'oxprice' => 218.88,
      'oxvat' => 22,
      'amount' => 696,
    ),
    1 =>
    array(
      'oxid' => '126562681471bf59e97c1e12accf6',
      'oxprice' => 296.13,
      'oxvat' => 22,
      'amount' => 645,
    ),
    2 =>
    array(
      'oxid' => 'd026fd6f6c1926a4708402c281a48',
      'oxprice' => 605.89,
      'oxvat' => 22,
      'amount' => 896,
    ),
    3 =>
    array(
      'oxid' => '48fe91e3759176da8f7d98d4a36e8',
      'oxprice' => 353.94,
      'oxvat' => 22,
      'amount' => 676,
    ),
    4 =>
    array(
      'oxid' => '48f40f1846b51ebbf32f0d1db9022',
      'oxprice' => 139.28,
      'oxvat' => 22,
      'amount' => 203,
    ),
    5 =>
    array(
      'oxid' => '8455d5a013403a2a7d7ddbac0859d',
      'oxprice' => 809.95,
      'oxvat' => 22,
      'amount' => 183,
    ),
    6 =>
    array(
      'oxid' => '04639678710e7973a2487b98cc1fe',
      'oxprice' => 167.03,
      'oxvat' => 22,
      'amount' => 325,
    ),
    7 =>
    array(
      'oxid' => 'dd6b63be057528ad0b44c4f948f91',
      'oxprice' => 131.34,
      'oxvat' => 22,
      'amount' => 525,
    ),
    8 =>
    array(
      'oxid' => '79c184d5dd345fa10a5a67ad4dd05',
      'oxprice' => 293.59,
      'oxvat' => 22,
      'amount' => 322,
    ),
    9 =>
    array(
      'oxid' => '130603793412e05a7a16e701de627',
      'oxprice' => 237.7,
      'oxvat' => 22,
      'amount' => 221,
    ),
    10 =>
    array(
      'oxid' => 'cfda1ef8776ab20d0dca339860b25',
      'oxprice' => 355.21,
      'oxvat' => 22,
      'amount' => 112,
    ),
    11 =>
    array(
      'oxid' => '3bb32f97f76d15ecd3370e277e173',
      'oxprice' => 622,
      'oxvat' => 22,
      'amount' => 305,
    ),
    12 =>
    array(
      'oxid' => '40fdcac197a2e40c44a86ede9882a',
      'oxprice' => 640.48,
      'oxvat' => 22,
      'amount' => 344,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 92,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '7d842fe98a19bc1a60922273034b0',
          1 => '126562681471bf59e97c1e12accf6',
          2 => 'd026fd6f6c1926a4708402c281a48',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 66,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '7d842fe98a19bc1a60922273034b0',
          1 => '126562681471bf59e97c1e12accf6',
          2 => 'd026fd6f6c1926a4708402c281a48',
          3 => '48fe91e3759176da8f7d98d4a36e8',
          4 => '48f40f1846b51ebbf32f0d1db9022',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 2,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 20,
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
        'oxaddsum' => 6,
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
        'oxaddsum' => 10,
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
        'oxdiscount' => 31,
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
      '7d842fe98a19bc1a60922273034b0' =>
      array(
        0 => '267,03',
        1 => '185.852,88',
      ),
      '126562681471bf59e97c1e12accf6' =>
      array(
        0 => '361,28',
        1 => '233.025,60',
      ),
      'd026fd6f6c1926a4708402c281a48' =>
      array(
        0 => '739,19',
        1 => '662.314,24',
      ),
      '48fe91e3759176da8f7d98d4a36e8' =>
      array(
        0 => '431,81',
        1 => '291.903,56',
      ),
      '48f40f1846b51ebbf32f0d1db9022' =>
      array(
        0 => '169,92',
        1 => '34.493,76',
      ),
      '8455d5a013403a2a7d7ddbac0859d' =>
      array(
        0 => '988,14',
        1 => '180.829,62',
      ),
      '04639678710e7973a2487b98cc1fe' =>
      array(
        0 => '203,78',
        1 => '66.228,50',
      ),
      'dd6b63be057528ad0b44c4f948f91' =>
      array(
        0 => '160,23',
        1 => '84.120,75',
      ),
      '79c184d5dd345fa10a5a67ad4dd05' =>
      array(
        0 => '358,18',
        1 => '115.333,96',
      ),
      '130603793412e05a7a16e701de627' =>
      array(
        0 => '289,99',
        1 => '64.087,79',
      ),
      'cfda1ef8776ab20d0dca339860b25' =>
      array(
        0 => '433,36',
        1 => '48.536,32',
      ),
      '3bb32f97f76d15ecd3370e277e173' =>
      array(
        0 => '758,84',
        1 => '231.446,20',
      ),
      '40fdcac197a2e40c44a86ede9882a' =>
      array(
        0 => '781,39',
        1 => '268.798,16',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        22 => '211.799,60',
      ),
      'wrapping' =>
      array(
        'brutto' => '205.656,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '148.028,28',
        'netto' => '121.334,66',
        'vat' => '26.693,62',
      ),
      'payment' =>
      array(
        'brutto' => '2,00',
        'netto' => '1,64',
        'vat' => '0,36',
      ),
      'voucher' =>
      array(
        'brutto' => '1.292.446,29',
      ),
      'totalNetto' => '962.725,45',
      'totalBrutto' => '2.466.971,34',
      'grandTotal' => '1.528.211,33',
    ),
  ),
);
