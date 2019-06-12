<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_25',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'da342c8bd5e4a092056d9d1dd1846',
      'oxprice' => 409.09,
      'oxvat' => 1,
      'amount' => 945,
    ),
    1 =>
    array(
      'oxid' => 'c39d83d3b9ec101e0f686e0537c30',
      'oxprice' => 56.31,
      'oxvat' => 1,
      'amount' => 364,
    ),
    2 =>
    array(
      'oxid' => 'c69ee0210811d27628d2ef9f179a1',
      'oxprice' => 896.52,
      'oxvat' => 1,
      'amount' => 514,
    ),
    3 =>
    array(
      'oxid' => '757ab7c5a5c8e85004604fb4e0813',
      'oxprice' => 160.72,
      'oxvat' => 0,
      'amount' => 699,
    ),
    4 =>
    array(
      'oxid' => 'df62867a1d8e1132a4f681dc74e82',
      'oxprice' => 715.23,
      'oxvat' => 0,
      'amount' => 152,
    ),
    5 =>
    array(
      'oxid' => 'e1a3d878aba8252c7f139c862b41e',
      'oxprice' => 922.85,
      'oxvat' => 1,
      'amount' => 441,
    ),
    6 =>
    array(
      'oxid' => '5d4211178ec925231bc3b7687563e',
      'oxprice' => 139.21,
      'oxvat' => 0,
      'amount' => 932,
    ),
    7 =>
    array(
      'oxid' => '7cf0737f57d26690ce3680182fb7e',
      'oxprice' => 414.03,
      'oxvat' => 0,
      'amount' => 397,
    ),
    8 =>
    array(
      'oxid' => '8e98bf325325168a99f2830b3c207',
      'oxprice' => 306.39,
      'oxvat' => 0,
      'amount' => 540,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 67,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'da342c8bd5e4a092056d9d1dd1846',
          1 => 'c39d83d3b9ec101e0f686e0537c30',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 14,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'da342c8bd5e4a092056d9d1dd1846',
          1 => 'c39d83d3b9ec101e0f686e0537c30',
          2 => 'c69ee0210811d27628d2ef9f179a1',
          3 => '757ab7c5a5c8e85004604fb4e0813',
          4 => 'df62867a1d8e1132a4f681dc74e82',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 27,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 12,
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
        'oxaddsum' => 15,
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
        'oxdiscount' => 20,
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
      'da342c8bd5e4a092056d9d1dd1846' =>
      array(
        0 => '413,18',
        1 => '390.455,10',
      ),
      'c39d83d3b9ec101e0f686e0537c30' =>
      array(
        0 => '56,87',
        1 => '20.700,68',
      ),
      'c69ee0210811d27628d2ef9f179a1' =>
      array(
        0 => '905,49',
        1 => '465.421,86',
      ),
      '757ab7c5a5c8e85004604fb4e0813' =>
      array(
        0 => '160,72',
        1 => '112.343,28',
      ),
      'df62867a1d8e1132a4f681dc74e82' =>
      array(
        0 => '715,23',
        1 => '108.714,96',
      ),
      'e1a3d878aba8252c7f139c862b41e' =>
      array(
        0 => '932,08',
        1 => '411.047,28',
      ),
      '5d4211178ec925231bc3b7687563e' =>
      array(
        0 => '139,21',
        1 => '129.743,72',
      ),
      '7cf0737f57d26690ce3680182fb7e' =>
      array(
        0 => '414,03',
        1 => '164.369,91',
      ),
      '8e98bf325325168a99f2830b3c207' =>
      array(
        0 => '306,39',
        1 => '165.450,60',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        1 => '8.159,21',
        0 => '0,00',
      ),
      'wrapping' =>
      array(
        'brutto' => '37.436,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '137.792,32',
        'netto' => '136.428,04',
        'vat' => '1.364,28',
      ),
      'payment' =>
      array(
        'brutto' => '27,00',
        'netto' => '26,73',
        'vat' => '0,27',
      ),
      'voucher' =>
      array(
        'brutto' => '708.569,06',
      ),
      'totalNetto' => '1.251.519,12',
      'totalBrutto' => '1.968.247,39',
      'grandTotal' => '1.434.933,65',
    ),
  ),
);
