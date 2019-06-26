<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_5',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'cd45226d3f0a8d913888d8e24a662',
      'oxprice' => 342.47,
      'oxvat' => 26,
      'amount' => 948,
    ),
    1 =>
    array(
      'oxid' => '549579b99b5ece1302ce5625af0ee',
      'oxprice' => 438.61,
      'oxvat' => 26,
      'amount' => 376,
    ),
    2 =>
    array(
      'oxid' => '082dbff5db3ee4718eaa26ef951bc',
      'oxprice' => 882.48,
      'oxvat' => 6,
      'amount' => 682,
    ),
    3 =>
    array(
      'oxid' => 'a77e6b75ec65afafbc19abdeae5b2',
      'oxprice' => 274.5,
      'oxvat' => 5,
      'amount' => 155,
    ),
    4 =>
    array(
      'oxid' => '58017ac311e3ca38ad79cd3f28f04',
      'oxprice' => 490.97,
      'oxvat' => 43,
      'amount' => 751,
    ),
    5 =>
    array(
      'oxid' => '2ed97d34538a6f745d6a73091e807',
      'oxprice' => 568.71,
      'oxvat' => 26,
      'amount' => 612,
    ),
    6 =>
    array(
      'oxid' => '22cf5c61c760c9265fae21e97e795',
      'oxprice' => 871.16,
      'oxvat' => 26,
      'amount' => 978,
    ),
    7 =>
    array(
      'oxid' => '97af35088957e406928e7f7ca00be',
      'oxprice' => 66.11,
      'oxvat' => 5,
      'amount' => 268,
    ),
    8 =>
    array(
      'oxid' => '68d5c6fbe6f159b3014d960bc091d',
      'oxprice' => 320.15,
      'oxvat' => 26,
      'amount' => 833,
    ),
    9 =>
    array(
      'oxid' => 'c82b8a888ee2c52e360518c10d56a',
      'oxprice' => 155.34,
      'oxvat' => 43,
      'amount' => 556,
    ),
    10 =>
    array(
      'oxid' => 'd7548486d38b0601497fd0a6f9533',
      'oxprice' => 745.69,
      'oxvat' => 6,
      'amount' => 159,
    ),
    11 =>
    array(
      'oxid' => 'da49393eafd971698c2103c2e7518',
      'oxprice' => 237.55,
      'oxvat' => 5,
      'amount' => 844,
    ),
    12 =>
    array(
      'oxid' => 'fb48f75030cb990446efad907baba',
      'oxprice' => 323.67,
      'oxvat' => 5,
      'amount' => 346,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 24,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'cd45226d3f0a8d913888d8e24a662',
          1 => '549579b99b5ece1302ce5625af0ee',
          2 => '082dbff5db3ee4718eaa26ef951bc',
          3 => 'a77e6b75ec65afafbc19abdeae5b2',
          4 => '58017ac311e3ca38ad79cd3f28f04',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 61,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'cd45226d3f0a8d913888d8e24a662',
          1 => '549579b99b5ece1302ce5625af0ee',
          2 => '082dbff5db3ee4718eaa26ef951bc',
          3 => 'a77e6b75ec65afafbc19abdeae5b2',
          4 => '58017ac311e3ca38ad79cd3f28f04',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 70,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 6,
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
        'oxaddsum' => 40,
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
        'oxaddsum' => 27,
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
        'oxdiscount' => 10,
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
      'cd45226d3f0a8d913888d8e24a662' =>
      array(
        0 => '342,47',
        1 => '324.661,56',
      ),
      '549579b99b5ece1302ce5625af0ee' =>
      array(
        0 => '438,61',
        1 => '164.917,36',
      ),
      '082dbff5db3ee4718eaa26ef951bc' =>
      array(
        0 => '882,48',
        1 => '601.851,36',
      ),
      'a77e6b75ec65afafbc19abdeae5b2' =>
      array(
        0 => '274,50',
        1 => '42.547,50',
      ),
      '58017ac311e3ca38ad79cd3f28f04' =>
      array(
        0 => '490,97',
        1 => '368.718,47',
      ),
      '2ed97d34538a6f745d6a73091e807' =>
      array(
        0 => '568,71',
        1 => '348.050,52',
      ),
      '22cf5c61c760c9265fae21e97e795' =>
      array(
        0 => '871,16',
        1 => '851.994,48',
      ),
      '97af35088957e406928e7f7ca00be' =>
      array(
        0 => '66,11',
        1 => '17.717,48',
      ),
      '68d5c6fbe6f159b3014d960bc091d' =>
      array(
        0 => '320,15',
        1 => '266.684,95',
      ),
      'c82b8a888ee2c52e360518c10d56a' =>
      array(
        0 => '155,34',
        1 => '86.369,04',
      ),
      'd7548486d38b0601497fd0a6f9533' =>
      array(
        0 => '745,69',
        1 => '118.564,71',
      ),
      'da49393eafd971698c2103c2e7518' =>
      array(
        0 => '237,55',
        1 => '200.492,20',
      ),
      'fb48f75030cb990446efad907baba' =>
      array(
        0 => '323,67',
        1 => '111.989,82',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        26 => '403.679,33',
        6 => '40.777,92',
        5 => '17.749,71',
        43 => '136.843,32',
      ),
      'wrapping' =>
      array(
        'brutto' => '177.632,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '67,00',
        'netto' => '53,17',
        'vat' => '13,83',
      ),
      'payment' =>
      array(
        'brutto' => '2.453.217,52',
        'netto' => '1.946.998,03',
        'vat' => '506.219,49',
      ),
      'voucher' =>
      array(
        'brutto' => '30,00',
      ),
      'totalNetto' => '2.905.479,17',
      'totalBrutto' => '3.504.559,45',
      'grandTotal' => '6.135.445,97',
    ),
  ),
);
