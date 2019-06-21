<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_22',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '45d73f3ba15817200a7d09673eddf',
      'oxprice' => 448.35,
      'oxvat' => 36,
      'amount' => 165,
    ),
    1 =>
    array(
      'oxid' => 'a70533566a7115a8b9d318467b85c',
      'oxprice' => 807.37,
      'oxvat' => 36,
      'amount' => 850,
    ),
    2 =>
    array(
      'oxid' => '3941da8092d419c66c44647e39083',
      'oxprice' => 330.8,
      'oxvat' => 36,
      'amount' => 688,
    ),
    3 =>
    array(
      'oxid' => '5f8bf975e67e5152ffaa47f158efd',
      'oxprice' => 183.61,
      'oxvat' => 36,
      'amount' => 295,
    ),
    4 =>
    array(
      'oxid' => '01b7f134674475bc00dfa9e8a9bd5',
      'oxprice' => 734.58,
      'oxvat' => 36,
      'amount' => 813,
    ),
    5 =>
    array(
      'oxid' => '251ca77d2c04d681508fcf15b5b8e',
      'oxprice' => 21.27,
      'oxvat' => 36,
      'amount' => 796,
    ),
    6 =>
    array(
      'oxid' => 'be35a2793fd523e917ed3c266f6fd',
      'oxprice' => 8.73,
      'oxvat' => 36,
      'amount' => 712,
    ),
    7 =>
    array(
      'oxid' => '6aecdac0fe71f97e1727ad06205d7',
      'oxprice' => 25.69,
      'oxvat' => 36,
      'amount' => 766,
    ),
    8 =>
    array(
      'oxid' => 'e979e7033947a4baf9dbbb9d8e14b',
      'oxprice' => 623.39,
      'oxvat' => 36,
      'amount' => 17,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 98,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '45d73f3ba15817200a7d09673eddf',
          1 => 'a70533566a7115a8b9d318467b85c',
          2 => '3941da8092d419c66c44647e39083',
          3 => '5f8bf975e67e5152ffaa47f158efd',
          4 => '01b7f134674475bc00dfa9e8a9bd5',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 34,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '45d73f3ba15817200a7d09673eddf',
          1 => 'a70533566a7115a8b9d318467b85c',
          2 => '3941da8092d419c66c44647e39083',
          3 => '5f8bf975e67e5152ffaa47f158efd',
          4 => '01b7f134674475bc00dfa9e8a9bd5',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 39,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 94,
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
        'oxaddsum' => 88,
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
        'oxaddsum' => 57,
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
      '45d73f3ba15817200a7d09673eddf' =>
      array(
        0 => '448,35',
        1 => '73.977,75',
      ),
      'a70533566a7115a8b9d318467b85c' =>
      array(
        0 => '807,37',
        1 => '686.264,50',
      ),
      '3941da8092d419c66c44647e39083' =>
      array(
        0 => '330,80',
        1 => '227.590,40',
      ),
      '5f8bf975e67e5152ffaa47f158efd' =>
      array(
        0 => '183,61',
        1 => '54.164,95',
      ),
      '01b7f134674475bc00dfa9e8a9bd5' =>
      array(
        0 => '734,58',
        1 => '597.213,54',
      ),
      '251ca77d2c04d681508fcf15b5b8e' =>
      array(
        0 => '21,27',
        1 => '16.930,92',
      ),
      'be35a2793fd523e917ed3c266f6fd' =>
      array(
        0 => '8,73',
        1 => '6.215,76',
      ),
      '6aecdac0fe71f97e1727ad06205d7' =>
      array(
        0 => '25,69',
        1 => '19.678,54',
      ),
      'e979e7033947a4baf9dbbb9d8e14b' =>
      array(
        0 => '623,39',
        1 => '10.597,63',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        36 => '448.025,56',
      ),
      'wrapping' =>
      array(
        'brutto' => '95.574,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '964.889,37',
        'netto' => '709.477,48',
        'vat' => '255.411,89',
      ),
      'payment' =>
      array(
        'brutto' => '1.036.397,84',
        'netto' => '762.057,24',
        'vat' => '274.340,60',
      ),
      'voucher' =>
      array(
        'brutto' => '93,00',
      ),
      'totalNetto' => '1.244.515,43',
      'totalBrutto' => '1.692.633,99',
      'grandTotal' => '3.789.402,20',
    ),
  ),
);
