<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_24',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '4614f46cf67e78491f8cf5acb8489',
      'oxprice' => 466.02,
      'oxvat' => 39,
      'amount' => 513,
    ),
    1 =>
    array(
      'oxid' => '0f89bf95ff3a091ab5c02d55ed228',
      'oxprice' => 265.24,
      'oxvat' => 39,
      'amount' => 612,
    ),
    2 =>
    array(
      'oxid' => 'a26028f5b2dd62af1a79b911c085a',
      'oxprice' => 785.87,
      'oxvat' => 39,
      'amount' => 358,
    ),
    3 =>
    array(
      'oxid' => 'adb15790e80bb609b258eddaf9a7d',
      'oxprice' => 371.22,
      'oxvat' => 42,
      'amount' => 681,
    ),
    4 =>
    array(
      'oxid' => '17fc3e1262cfc76f18f73d3dc0d66',
      'oxprice' => 589.47,
      'oxvat' => 42,
      'amount' => 522,
    ),
    5 =>
    array(
      'oxid' => '416274d8719a522f2158d2b420c33',
      'oxprice' => 711.24,
      'oxvat' => 42,
      'amount' => 155,
    ),
    6 =>
    array(
      'oxid' => 'bc834dac8053dc0535c8402e040c9',
      'oxprice' => 639.54,
      'oxvat' => 14,
      'amount' => 860,
    ),
    7 =>
    array(
      'oxid' => 'e9436fa1f506e33d49ce5b03f1da6',
      'oxprice' => 532.57,
      'oxvat' => 42,
      'amount' => 330,
    ),
    8 =>
    array(
      'oxid' => 'f99ad106b1a585f65e18151806393',
      'oxprice' => 628.76,
      'oxvat' => 42,
      'amount' => 445,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 82,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '4614f46cf67e78491f8cf5acb8489',
          1 => '0f89bf95ff3a091ab5c02d55ed228',
          2 => 'a26028f5b2dd62af1a79b911c085a',
          3 => 'adb15790e80bb609b258eddaf9a7d',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 6,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '4614f46cf67e78491f8cf5acb8489',
          1 => '0f89bf95ff3a091ab5c02d55ed228',
          2 => 'a26028f5b2dd62af1a79b911c085a',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 19,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
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
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 14,
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
        'oxaddsum' => 7,
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
        'oxdiscount' => 18,
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
      '4614f46cf67e78491f8cf5acb8489' =>
      array(
        0 => '647,77',
        1 => '332.306,01',
      ),
      '0f89bf95ff3a091ab5c02d55ed228' =>
      array(
        0 => '368,68',
        1 => '225.632,16',
      ),
      'a26028f5b2dd62af1a79b911c085a' =>
      array(
        0 => '1.092,36',
        1 => '391.064,88',
      ),
      'adb15790e80bb609b258eddaf9a7d' =>
      array(
        0 => '527,13',
        1 => '358.975,53',
      ),
      '17fc3e1262cfc76f18f73d3dc0d66' =>
      array(
        0 => '837,05',
        1 => '436.940,10',
      ),
      '416274d8719a522f2158d2b420c33' =>
      array(
        0 => '1.009,96',
        1 => '156.543,80',
      ),
      'bc834dac8053dc0535c8402e040c9' =>
      array(
        0 => '729,08',
        1 => '627.008,80',
      ),
      'e9436fa1f506e33d49ce5b03f1da6' =>
      array(
        0 => '756,25',
        1 => '249.562,50',
      ),
      'f99ad106b1a585f65e18151806393' =>
      array(
        0 => '892,84',
        1 => '397.313,80',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        39 => '179.037,96',
        42 => '318.074,09',
        14 => '51.775,53',
      ),
      'wrapping' =>
      array(
        'brutto' => '64.740,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '222.288,33',
        'netto' => '156.541,08',
        'vat' => '65.747,25',
      ),
      'payment' =>
      array(
        'brutto' => '19,00',
        'netto' => '13,38',
        'vat' => '5,62',
      ),
      'voucher' =>
      array(
        'brutto' => '1.040.243,86',
      ),
      'totalNetto' => '1.586.216,14',
      'totalBrutto' => '3.175.347,58',
      'grandTotal' => '2.422.151,05',
    ),
  ),
);
