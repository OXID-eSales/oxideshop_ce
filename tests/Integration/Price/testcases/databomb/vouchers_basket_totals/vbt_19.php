<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_19',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '561316c0063f5680668e37c39030a',
      'oxprice' => 488.67,
      'oxvat' => 30,
      'amount' => 321,
    ),
    1 =>
    array(
      'oxid' => '438df2a060487c24026e75b8369af',
      'oxprice' => 473.88,
      'oxvat' => 30,
      'amount' => 453,
    ),
    2 =>
    array(
      'oxid' => 'e911cdba2b5e0ef982b931aa60720',
      'oxprice' => 610.43,
      'oxvat' => 19,
      'amount' => 814,
    ),
    3 =>
    array(
      'oxid' => 'b3663d09b90672e8aba11184c83dc',
      'oxprice' => 160.24,
      'oxvat' => 30,
      'amount' => 956,
    ),
    4 =>
    array(
      'oxid' => 'd8b5769372ad1d897a4121376bc77',
      'oxprice' => 975.87,
      'oxvat' => 19,
      'amount' => 609,
    ),
    5 =>
    array(
      'oxid' => 'c64ad81b3938ea75dc538a60e753a',
      'oxprice' => 770.18,
      'oxvat' => 19,
      'amount' => 617,
    ),
    6 =>
    array(
      'oxid' => 'aea9828d414c9151012d131bbb01b',
      'oxprice' => 347.45,
      'oxvat' => 30,
      'amount' => 208,
    ),
    7 =>
    array(
      'oxid' => '9bf77cd6e6827dd6264c4055e3511',
      'oxprice' => 366.92,
      'oxvat' => 19,
      'amount' => 386,
    ),
    8 =>
    array(
      'oxid' => 'df9ba36d9d2ece4f33264026f11c6',
      'oxprice' => 23.16,
      'oxvat' => 19,
      'amount' => 473,
    ),
    9 =>
    array(
      'oxid' => 'a6587526f665946a8ad8341430c7a',
      'oxprice' => 373.24,
      'oxvat' => 19,
      'amount' => 695,
    ),
    10 =>
    array(
      'oxid' => '912868c256dd011b5072e1fa14a70',
      'oxprice' => 743.29,
      'oxvat' => 30,
      'amount' => 975,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 96,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '561316c0063f5680668e37c39030a',
          1 => '438df2a060487c24026e75b8369af',
          2 => 'e911cdba2b5e0ef982b931aa60720',
          3 => 'b3663d09b90672e8aba11184c83dc',
          4 => 'd8b5769372ad1d897a4121376bc77',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 3,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '561316c0063f5680668e37c39030a',
          1 => '438df2a060487c24026e75b8369af',
          2 => 'e911cdba2b5e0ef982b931aa60720',
          3 => 'b3663d09b90672e8aba11184c83dc',
          4 => 'd8b5769372ad1d897a4121376bc77',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 31,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 64,
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
        'oxaddsum' => 71,
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
        'oxaddsum' => 99,
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
        'oxdiscount' => 12,
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
      '561316c0063f5680668e37c39030a' =>
      array(
        0 => '488,67',
        1 => '156.863,07',
      ),
      '438df2a060487c24026e75b8369af' =>
      array(
        0 => '473,88',
        1 => '214.667,64',
      ),
      'e911cdba2b5e0ef982b931aa60720' =>
      array(
        0 => '610,43',
        1 => '496.890,02',
      ),
      'b3663d09b90672e8aba11184c83dc' =>
      array(
        0 => '160,24',
        1 => '153.189,44',
      ),
      'd8b5769372ad1d897a4121376bc77' =>
      array(
        0 => '975,87',
        1 => '594.304,83',
      ),
      'c64ad81b3938ea75dc538a60e753a' =>
      array(
        0 => '770,18',
        1 => '475.201,06',
      ),
      'aea9828d414c9151012d131bbb01b' =>
      array(
        0 => '347,45',
        1 => '72.269,60',
      ),
      '9bf77cd6e6827dd6264c4055e3511' =>
      array(
        0 => '366,92',
        1 => '141.631,12',
      ),
      'df9ba36d9d2ece4f33264026f11c6' =>
      array(
        0 => '23,16',
        1 => '10.954,68',
      ),
      'a6587526f665946a8ad8341430c7a' =>
      array(
        0 => '373,24',
        1 => '259.401,80',
      ),
      '912868c256dd011b5072e1fa14a70' =>
      array(
        0 => '743,29',
        1 => '724.707,75',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        30 => '305.003,79',
        19 => '315.872,91',
      ),
      'wrapping' =>
      array(
        'brutto' => '9.459,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '170,00',
        'netto' => '142,86',
        'vat' => '27,14',
      ),
      'payment' =>
      array(
        'brutto' => '31,00',
        'netto' => '26,05',
        'vat' => '4,95',
      ),
      'voucher' =>
      array(
        'brutto' => '36,00',
      ),
      'totalNetto' => '2.679.168,31',
      'totalBrutto' => '3.300.081,01',
      'grandTotal' => '3.309.705,01',
    ),
  ),
);
