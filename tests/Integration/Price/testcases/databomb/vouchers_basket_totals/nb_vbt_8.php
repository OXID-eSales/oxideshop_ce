<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_8',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '3d4ecbad760892223749db36cd205',
      'oxprice' => 164.16,
      'oxvat' => 15,
      'amount' => 564,
    ),
    1 =>
    array(
      'oxid' => 'c12fafca011f8e70c14610dc8c4a0',
      'oxprice' => 14.56,
      'oxvat' => 15,
      'amount' => 361,
    ),
    2 =>
    array(
      'oxid' => '448ca461ee8e17112cd04e8addef2',
      'oxprice' => 326.49,
      'oxvat' => 15,
      'amount' => 372,
    ),
    3 =>
    array(
      'oxid' => '1d58c0d202e90a3a6f0b9a201c307',
      'oxprice' => 63.69,
      'oxvat' => 6,
      'amount' => 5,
    ),
    4 =>
    array(
      'oxid' => 'f01c51e614834a303ed7a72554a32',
      'oxprice' => 95.91,
      'oxvat' => 6,
      'amount' => 128,
    ),
    5 =>
    array(
      'oxid' => '693e96ae47751864a62330d4cf192',
      'oxprice' => 401.16,
      'oxvat' => 15,
      'amount' => 259,
    ),
    6 =>
    array(
      'oxid' => 'ee205f8a5988b2d936ab703cd7888',
      'oxprice' => 58.24,
      'oxvat' => 6,
      'amount' => 569,
    ),
    7 =>
    array(
      'oxid' => 'f93fd0bfed2adb406f10b645ba69b',
      'oxprice' => 453.01,
      'oxvat' => 6,
      'amount' => 59,
    ),
    8 =>
    array(
      'oxid' => 'e0f0993e8730a974be0c6107db3a0',
      'oxprice' => 275.3,
      'oxvat' => 15,
      'amount' => 190,
    ),
    9 =>
    array(
      'oxid' => '15a85a5f22a30aefb377dbb0e8586',
      'oxprice' => 77.35,
      'oxvat' => 15,
      'amount' => 271,
    ),
    10 =>
    array(
      'oxid' => '6f323b4f6685c2662522c24196dad',
      'oxprice' => 877.37,
      'oxvat' => 6,
      'amount' => 169,
    ),
    11 =>
    array(
      'oxid' => 'd8f59fec8cef94070789272a99332',
      'oxprice' => 730.61,
      'oxvat' => 15,
      'amount' => 175,
    ),
    12 =>
    array(
      'oxid' => '02e73ad7b91cb87ff5936acb7a15a',
      'oxprice' => 120.47,
      'oxvat' => 15,
      'amount' => 739,
    ),
    13 =>
    array(
      'oxid' => '8bddd21f07c8a2193e9a4122a33fa',
      'oxprice' => 523.18,
      'oxvat' => 15,
      'amount' => 100,
    ),
    14 =>
    array(
      'oxid' => '72da325fee674b550de7e2a5d6355',
      'oxprice' => 660.58,
      'oxvat' => 15,
      'amount' => 471,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 5,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '3d4ecbad760892223749db36cd205',
          1 => 'c12fafca011f8e70c14610dc8c4a0',
          2 => '448ca461ee8e17112cd04e8addef2',
          3 => '1d58c0d202e90a3a6f0b9a201c307',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 32,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '3d4ecbad760892223749db36cd205',
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
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 19,
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
        'oxaddsum' => 12,
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
        'oxaddsum' => 6,
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
        'oxdiscount' => 32,
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
      '3d4ecbad760892223749db36cd205' =>
      array(
        0 => '188,78',
        1 => '106.471,92',
      ),
      'c12fafca011f8e70c14610dc8c4a0' =>
      array(
        0 => '16,74',
        1 => '6.043,14',
      ),
      '448ca461ee8e17112cd04e8addef2' =>
      array(
        0 => '375,46',
        1 => '139.671,12',
      ),
      '1d58c0d202e90a3a6f0b9a201c307' =>
      array(
        0 => '67,51',
        1 => '337,55',
      ),
      'f01c51e614834a303ed7a72554a32' =>
      array(
        0 => '101,66',
        1 => '13.012,48',
      ),
      '693e96ae47751864a62330d4cf192' =>
      array(
        0 => '461,33',
        1 => '119.484,47',
      ),
      'ee205f8a5988b2d936ab703cd7888' =>
      array(
        0 => '61,73',
        1 => '35.124,37',
      ),
      'f93fd0bfed2adb406f10b645ba69b' =>
      array(
        0 => '480,19',
        1 => '28.331,21',
      ),
      'e0f0993e8730a974be0c6107db3a0' =>
      array(
        0 => '316,60',
        1 => '60.154,00',
      ),
      '15a85a5f22a30aefb377dbb0e8586' =>
      array(
        0 => '88,95',
        1 => '24.105,45',
      ),
      '6f323b4f6685c2662522c24196dad' =>
      array(
        0 => '930,01',
        1 => '157.171,69',
      ),
      'd8f59fec8cef94070789272a99332' =>
      array(
        0 => '840,20',
        1 => '147.035,00',
      ),
      '02e73ad7b91cb87ff5936acb7a15a' =>
      array(
        0 => '138,54',
        1 => '102.381,06',
      ),
      '8bddd21f07c8a2193e9a4122a33fa' =>
      array(
        0 => '601,66',
        1 => '60.166,00',
      ),
      '72da325fee674b550de7e2a5d6355' =>
      array(
        0 => '759,67',
        1 => '357.804,57',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        15 => '67.750,65',
        6 => '6.124,02',
      ),
      'wrapping' =>
      array(
        'brutto' => '21.738,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '18,00',
        'netto' => '15,65',
        'vat' => '2,35',
      ),
      'payment' =>
      array(
        'brutto' => '18,00',
        'netto' => '15,65',
        'vat' => '2,35',
      ),
      'voucher' =>
      array(
        'brutto' => '729.681,27',
      ),
      'totalNetto' => '553.738,09',
      'totalBrutto' => '1.357.294,03',
      'grandTotal' => '649.386,76',
    ),
  ),
);
