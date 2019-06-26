<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_4',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '8ad74ea73c88bef7876c84059efbd',
      'oxprice' => 963.8,
      'oxvat' => 20,
      'amount' => 71,
    ),
    1 =>
    array(
      'oxid' => '82fafd53c7f14d0634168704e1219',
      'oxprice' => 352.5,
      'oxvat' => 7,
      'amount' => 675,
    ),
    2 =>
    array(
      'oxid' => 'ff373a74158c13fe1824f36d1ffcd',
      'oxprice' => 824.98,
      'oxvat' => 7,
      'amount' => 930,
    ),
    3 =>
    array(
      'oxid' => '044a9ceb151383c0dca0887a1a9a3',
      'oxprice' => 878.42,
      'oxvat' => 20,
      'amount' => 317,
    ),
    4 =>
    array(
      'oxid' => 'af6ec24a63c45d3dbfa332e195438',
      'oxprice' => 184.23,
      'oxvat' => 20,
      'amount' => 411,
    ),
    5 =>
    array(
      'oxid' => 'ffdb8198056d31e26559c3b311f17',
      'oxprice' => 95.14,
      'oxvat' => 7,
      'amount' => 465,
    ),
    6 =>
    array(
      'oxid' => '6400b55ce1367229393e55b125c03',
      'oxprice' => 31.51,
      'oxvat' => 20,
      'amount' => 472,
    ),
    7 =>
    array(
      'oxid' => '16560f6e06646aa2bbe58673cc073',
      'oxprice' => 522.18,
      'oxvat' => 7,
      'amount' => 75,
    ),
    8 =>
    array(
      'oxid' => '2dd45a9f5f0c286c0710e90e99769',
      'oxprice' => 833.93,
      'oxvat' => 7,
      'amount' => 988,
    ),
    9 =>
    array(
      'oxid' => 'de45f9398cca286c7d6b50f224259',
      'oxprice' => 507.51,
      'oxvat' => 7,
      'amount' => 172,
    ),
    10 =>
    array(
      'oxid' => 'bf1ec7ee509d3ece756ddaef561fa',
      'oxprice' => 705.18,
      'oxvat' => 20,
      'amount' => 271,
    ),
    11 =>
    array(
      'oxid' => 'f06bc3b740e32290efb8c93118e6b',
      'oxprice' => 52.27,
      'oxvat' => 7,
      'amount' => 657,
    ),
    12 =>
    array(
      'oxid' => 'c4a2bf84ea0604cb1f3d4e898fd2a',
      'oxprice' => 598.11,
      'oxvat' => 20,
      'amount' => 273,
    ),
    13 =>
    array(
      'oxid' => 'aad18b1d8e8ee8f6efc68e2ddc642',
      'oxprice' => 245.92,
      'oxvat' => 7,
      'amount' => 348,
    ),
    14 =>
    array(
      'oxid' => '7a7d21c4468be18acbb7d3b2e5937',
      'oxprice' => 3.2,
      'oxvat' => 7,
      'amount' => 150,
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
          0 => '8ad74ea73c88bef7876c84059efbd',
          1 => '82fafd53c7f14d0634168704e1219',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 29,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8ad74ea73c88bef7876c84059efbd',
          1 => '82fafd53c7f14d0634168704e1219',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 32,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 4,
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
        'oxdiscount' => 8,
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
      '8ad74ea73c88bef7876c84059efbd' =>
      array(
        0 => '1.156,56',
        1 => '82.115,76',
      ),
      '82fafd53c7f14d0634168704e1219' =>
      array(
        0 => '377,18',
        1 => '254.596,50',
      ),
      'ff373a74158c13fe1824f36d1ffcd' =>
      array(
        0 => '882,73',
        1 => '820.938,90',
      ),
      '044a9ceb151383c0dca0887a1a9a3' =>
      array(
        0 => '1.054,10',
        1 => '334.149,70',
      ),
      'af6ec24a63c45d3dbfa332e195438' =>
      array(
        0 => '221,08',
        1 => '90.863,88',
      ),
      'ffdb8198056d31e26559c3b311f17' =>
      array(
        0 => '101,80',
        1 => '47.337,00',
      ),
      '6400b55ce1367229393e55b125c03' =>
      array(
        0 => '37,81',
        1 => '17.846,32',
      ),
      '16560f6e06646aa2bbe58673cc073' =>
      array(
        0 => '558,73',
        1 => '41.904,75',
      ),
      '2dd45a9f5f0c286c0710e90e99769' =>
      array(
        0 => '892,31',
        1 => '881.602,28',
      ),
      'de45f9398cca286c7d6b50f224259' =>
      array(
        0 => '543,04',
        1 => '93.402,88',
      ),
      'bf1ec7ee509d3ece756ddaef561fa' =>
      array(
        0 => '846,22',
        1 => '229.325,62',
      ),
      'f06bc3b740e32290efb8c93118e6b' =>
      array(
        0 => '55,93',
        1 => '36.746,01',
      ),
      'c4a2bf84ea0604cb1f3d4e898fd2a' =>
      array(
        0 => '717,73',
        1 => '195.940,29',
      ),
      'aad18b1d8e8ee8f6efc68e2ddc642' =>
      array(
        0 => '263,13',
        1 => '91.569,24',
      ),
      '7a7d21c4468be18acbb7d3b2e5937' =>
      array(
        0 => '3,42',
        1 => '513,00',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        20 => '134.047,41',
        7 => '125.617,42',
      ),
      'wrapping' =>
      array(
        'brutto' => '21.634,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '193.140,13',
        'netto' => '180.504,79',
        'vat' => '12.635,34',
      ),
      'payment' =>
      array(
        'brutto' => '32,00',
        'netto' => '29,91',
        'vat' => '2,09',
      ),
      'voucher' =>
      array(
        'brutto' => '494.415,69',
      ),
      'totalNetto' => '2.464.771,61',
      'totalBrutto' => '3.218.852,13',
      'grandTotal' => '2.939.242,57',
    ),
  ),
);
