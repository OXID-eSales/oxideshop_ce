<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_15',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'ca3768e5c41f5d7c7e915463ce414',
      'oxprice' => 178.62,
      'oxvat' => 25,
      'amount' => 981,
    ),
    1 =>
    array(
      'oxid' => 'b9787fc1c1321bdc244c10802eae0',
      'oxprice' => 0.31,
      'oxvat' => 25,
      'amount' => 896,
    ),
    2 =>
    array(
      'oxid' => 'fa6eedb7c38ee69b5726c38ccf7c7',
      'oxprice' => 677.42,
      'oxvat' => 22,
      'amount' => 333,
    ),
    3 =>
    array(
      'oxid' => 'c4181051b78e80354491e9aad6cb3',
      'oxprice' => 605.29,
      'oxvat' => 25,
      'amount' => 132,
    ),
    4 =>
    array(
      'oxid' => 'fcf8de3cbe49bedbdad158aa3b187',
      'oxprice' => 881.07,
      'oxvat' => 13,
      'amount' => 368,
    ),
    5 =>
    array(
      'oxid' => '3b784a12f318fc54c8c496c58aaa6',
      'oxprice' => 534.87,
      'oxvat' => 22,
      'amount' => 598,
    ),
    6 =>
    array(
      'oxid' => '4452c0b93562ef055c4f290cc3986',
      'oxprice' => 544.17,
      'oxvat' => 22,
      'amount' => 120,
    ),
    7 =>
    array(
      'oxid' => '0102390a25451472319f770601046',
      'oxprice' => 211.11,
      'oxvat' => 22,
      'amount' => 901,
    ),
    8 =>
    array(
      'oxid' => '653ba561dcc6ca37beb6bcf39e1c1',
      'oxprice' => 557.64,
      'oxvat' => 22,
      'amount' => 242,
    ),
    9 =>
    array(
      'oxid' => 'ef62039d48291b9a1a61087f37cba',
      'oxprice' => 544.98,
      'oxvat' => 22,
      'amount' => 671,
    ),
    10 =>
    array(
      'oxid' => '32298071afe4eaa7b31ecd466c249',
      'oxprice' => 441.31,
      'oxvat' => 13,
      'amount' => 847,
    ),
    11 =>
    array(
      'oxid' => 'b5e57bb7b7f6aa8aa05c57480cffc',
      'oxprice' => 374.57,
      'oxvat' => 25,
      'amount' => 976,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 57,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'ca3768e5c41f5d7c7e915463ce414',
          1 => 'b9787fc1c1321bdc244c10802eae0',
          2 => 'fa6eedb7c38ee69b5726c38ccf7c7',
          3 => 'c4181051b78e80354491e9aad6cb3',
          4 => 'fcf8de3cbe49bedbdad158aa3b187',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 85,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'ca3768e5c41f5d7c7e915463ce414',
          1 => 'b9787fc1c1321bdc244c10802eae0',
          2 => 'fa6eedb7c38ee69b5726c38ccf7c7',
          3 => 'c4181051b78e80354491e9aad6cb3',
          4 => 'fcf8de3cbe49bedbdad158aa3b187',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 20,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 43,
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
        'oxaddsum' => 86,
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
        'oxaddsum' => 66,
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
        'oxdiscount' => 6,
        'oxdiscounttype' => 'absolute',
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
      'ca3768e5c41f5d7c7e915463ce414' =>
      array(
        0 => '178,62',
        1 => '175.226,22',
      ),
      'b9787fc1c1321bdc244c10802eae0' =>
      array(
        0 => '0,31',
        1 => '277,76',
      ),
      'fa6eedb7c38ee69b5726c38ccf7c7' =>
      array(
        0 => '677,42',
        1 => '225.580,86',
      ),
      'c4181051b78e80354491e9aad6cb3' =>
      array(
        0 => '605,29',
        1 => '79.898,28',
      ),
      'fcf8de3cbe49bedbdad158aa3b187' =>
      array(
        0 => '881,07',
        1 => '324.233,76',
      ),
      '3b784a12f318fc54c8c496c58aaa6' =>
      array(
        0 => '534,87',
        1 => '319.852,26',
      ),
      '4452c0b93562ef055c4f290cc3986' =>
      array(
        0 => '544,17',
        1 => '65.300,40',
      ),
      '0102390a25451472319f770601046' =>
      array(
        0 => '211,11',
        1 => '190.210,11',
      ),
      '653ba561dcc6ca37beb6bcf39e1c1' =>
      array(
        0 => '557,64',
        1 => '134.948,88',
      ),
      'ef62039d48291b9a1a61087f37cba' =>
      array(
        0 => '544,98',
        1 => '365.681,58',
      ),
      '32298071afe4eaa7b31ecd466c249' =>
      array(
        0 => '441,31',
        1 => '373.789,57',
      ),
      'b5e57bb7b7f6aa8aa05c57480cffc' =>
      array(
        0 => '374,57',
        1 => '365.580,32',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        25 => '124.195,66',
        22 => '234.708,47',
        13 => '80.303,02',
      ),
      'wrapping' =>
      array(
        'brutto' => '230.350,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '1.729.668,80',
        'netto' => '1.417.761,31',
        'vat' => '311.907,49',
      ),
      'payment' =>
      array(
        'brutto' => '20,00',
        'netto' => '16,39',
        'vat' => '3,61',
      ),
      'voucher' =>
      array(
        'brutto' => '18,00',
      ),
      'totalNetto' => '2.181.354,85',
      'totalBrutto' => '2.620.580,00',
      'grandTotal' => '4.580.600,80',
    ),
  ),
);
