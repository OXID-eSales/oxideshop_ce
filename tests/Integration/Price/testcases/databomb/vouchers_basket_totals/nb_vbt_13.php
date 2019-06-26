<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_13',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '4523ad3d95f3e124bb36befae6c17',
      'oxprice' => 872.33,
      'oxvat' => 6,
      'amount' => 850,
    ),
    1 =>
    array(
      'oxid' => '92d4dc85b381086b580a8c66687d8',
      'oxprice' => 459.2,
      'oxvat' => 37,
      'amount' => 632,
    ),
    2 =>
    array(
      'oxid' => '3ee4183910119d3f6023f0b279e75',
      'oxprice' => 117.77,
      'oxvat' => 37,
      'amount' => 249,
    ),
    3 =>
    array(
      'oxid' => '09f1a1b2bcfe3bdaa6595631fdf50',
      'oxprice' => 270.3,
      'oxvat' => 6,
      'amount' => 829,
    ),
    4 =>
    array(
      'oxid' => '77b39630ca5568b4ad47d9811af21',
      'oxprice' => 710.56,
      'oxvat' => 6,
      'amount' => 361,
    ),
    5 =>
    array(
      'oxid' => '52fd963e14785916c13988cfcd470',
      'oxprice' => 92.51,
      'oxvat' => 37,
      'amount' => 950,
    ),
    6 =>
    array(
      'oxid' => 'e7671774965175fedabd604a76209',
      'oxprice' => 951.26,
      'oxvat' => 37,
      'amount' => 566,
    ),
    7 =>
    array(
      'oxid' => '6b051fa3397cb6bf2eed8d58b2f53',
      'oxprice' => 138.98,
      'oxvat' => 37,
      'amount' => 51,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 55,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '4523ad3d95f3e124bb36befae6c17',
          1 => '92d4dc85b381086b580a8c66687d8',
          2 => '3ee4183910119d3f6023f0b279e75',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 87,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '4523ad3d95f3e124bb36befae6c17',
          1 => '92d4dc85b381086b580a8c66687d8',
          2 => '3ee4183910119d3f6023f0b279e75',
          3 => '09f1a1b2bcfe3bdaa6595631fdf50',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 1,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 26,
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
        'oxaddsum' => 4,
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
        'oxaddsum' => 20,
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
      '4523ad3d95f3e124bb36befae6c17' =>
      array(
        0 => '924,67',
        1 => '785.969,50',
      ),
      '92d4dc85b381086b580a8c66687d8' =>
      array(
        0 => '629,10',
        1 => '397.591,20',
      ),
      '3ee4183910119d3f6023f0b279e75' =>
      array(
        0 => '161,34',
        1 => '40.173,66',
      ),
      '09f1a1b2bcfe3bdaa6595631fdf50' =>
      array(
        0 => '286,52',
        1 => '237.525,08',
      ),
      '77b39630ca5568b4ad47d9811af21' =>
      array(
        0 => '753,19',
        1 => '271.901,59',
      ),
      '52fd963e14785916c13988cfcd470' =>
      array(
        0 => '126,74',
        1 => '120.403,00',
      ),
      'e7671774965175fedabd604a76209' =>
      array(
        0 => '1.303,23',
        1 => '737.628,18',
      ),
      '6b051fa3397cb6bf2eed8d58b2f53' =>
      array(
        0 => '190,40',
        1 => '9.710,40',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        6 => '44.610,51',
        37 => '214.510,91',
      ),
      'wrapping' =>
      array(
        'brutto' => '222.720,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '24,00',
        'netto' => '17,52',
        'vat' => '6,48',
      ),
      'payment' =>
      array(
        'brutto' => '15.824,13',
        'netto' => '11.550,46',
        'vat' => '4.273,67',
      ),
      'voucher' =>
      array(
        'brutto' => '1.018.513,46',
      ),
      'totalNetto' => '1.323.267,73',
      'totalBrutto' => '2.600.902,61',
      'grandTotal' => '1.820.957,28',
    ),
  ),
);
