<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_22',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'edd11c6e1b2f34b15e29d024203de',
      'oxprice' => 170.25,
      'oxvat' => 0,
      'amount' => 578,
    ),
    1 =>
    array(
      'oxid' => 'e4b5abae990a72d91a5be5608c8d9',
      'oxprice' => 241.02,
      'oxvat' => 0,
      'amount' => 931,
    ),
    2 =>
    array(
      'oxid' => '4c1e8e0a377587784f8e2ea84628c',
      'oxprice' => 14.5,
      'oxvat' => 0,
      'amount' => 805,
    ),
    3 =>
    array(
      'oxid' => 'b637f227fa46bf09b1c304fe4e6c5',
      'oxprice' => 512.97,
      'oxvat' => 43,
      'amount' => 134,
    ),
    4 =>
    array(
      'oxid' => 'c6483fb3181a7deadce8801737b41',
      'oxprice' => 150.94,
      'oxvat' => 43,
      'amount' => 620,
    ),
    5 =>
    array(
      'oxid' => '0620613a1da20e30c8490e920c379',
      'oxprice' => 753.28,
      'oxvat' => 0,
      'amount' => 628,
    ),
    6 =>
    array(
      'oxid' => '12abe6ec1424cde10397782d5f654',
      'oxprice' => 998.34,
      'oxvat' => 0,
      'amount' => 511,
    ),
    7 =>
    array(
      'oxid' => 'f619dd551e0f4954e455cb9163535',
      'oxprice' => 845.34,
      'oxvat' => 43,
      'amount' => 624,
    ),
    8 =>
    array(
      'oxid' => '4c37d0fedab9f5ec60ded9d3216a0',
      'oxprice' => 410.53,
      'oxvat' => 0,
      'amount' => 344,
    ),
    9 =>
    array(
      'oxid' => 'e38f12ec2145d5ea71b04863cca80',
      'oxprice' => 230.17,
      'oxvat' => 0,
      'amount' => 49,
    ),
    10 =>
    array(
      'oxid' => 'ce49afdf4cff9236c351ec9ccce11',
      'oxprice' => 953.84,
      'oxvat' => 43,
      'amount' => 934,
    ),
    11 =>
    array(
      'oxid' => 'e6aaff779adafb7210c93fa3d7bd4',
      'oxprice' => 766.28,
      'oxvat' => 0,
      'amount' => 691,
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
          0 => 'edd11c6e1b2f34b15e29d024203de',
          1 => 'e4b5abae990a72d91a5be5608c8d9',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 46,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'edd11c6e1b2f34b15e29d024203de',
          1 => 'e4b5abae990a72d91a5be5608c8d9',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 24,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 2,
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
        'oxaddsum' => 20,
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
        'oxaddsum' => 13,
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
        'oxdiscount' => 26,
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
      'edd11c6e1b2f34b15e29d024203de' =>
      array(
        0 => '170,25',
        1 => '98.404,50',
      ),
      'e4b5abae990a72d91a5be5608c8d9' =>
      array(
        0 => '241,02',
        1 => '224.389,62',
      ),
      '4c1e8e0a377587784f8e2ea84628c' =>
      array(
        0 => '14,50',
        1 => '11.672,50',
      ),
      'b637f227fa46bf09b1c304fe4e6c5' =>
      array(
        0 => '733,55',
        1 => '98.295,70',
      ),
      'c6483fb3181a7deadce8801737b41' =>
      array(
        0 => '215,84',
        1 => '133.820,80',
      ),
      '0620613a1da20e30c8490e920c379' =>
      array(
        0 => '753,28',
        1 => '473.059,84',
      ),
      '12abe6ec1424cde10397782d5f654' =>
      array(
        0 => '998,34',
        1 => '510.151,74',
      ),
      'f619dd551e0f4954e455cb9163535' =>
      array(
        0 => '1.208,84',
        1 => '754.316,16',
      ),
      '4c37d0fedab9f5ec60ded9d3216a0' =>
      array(
        0 => '410,53',
        1 => '141.222,32',
      ),
      'e38f12ec2145d5ea71b04863cca80' =>
      array(
        0 => '230,17',
        1 => '11.278,33',
      ),
      'ce49afdf4cff9236c351ec9ccce11' =>
      array(
        0 => '1.363,99',
        1 => '1.273.966,66',
      ),
      'e6aaff779adafb7210c93fa3d7bd4' =>
      array(
        0 => '766,28',
        1 => '529.499,48',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        0 => '0,00',
        43 => '679.692,20',
      ),
      'wrapping' =>
      array(
        'brutto' => '69.414,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '1.405.825,62',
        'netto' => '983.094,84',
        'vat' => '422.730,78',
      ),
      'payment' =>
      array(
        'brutto' => '24,00',
        'netto' => '16,78',
        'vat' => '7,22',
      ),
      'voucher' =>
      array(
        'brutto' => '52,00',
      ),
      'totalNetto' => '3.580.333,45',
      'totalBrutto' => '4.260.077,65',
      'grandTotal' => '5.735.289,27',
    ),
  ),
);
