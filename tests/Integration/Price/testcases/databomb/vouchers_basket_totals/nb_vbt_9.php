<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_9',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '40302e1434fe5d6995fad5854022c',
      'oxprice' => 55.13,
      'oxvat' => 35,
      'amount' => 755,
    ),
    1 =>
    array(
      'oxid' => 'c646ce9e73d4f98e3ccf7805189e5',
      'oxprice' => 552.87,
      'oxvat' => 35,
      'amount' => 758,
    ),
    2 =>
    array(
      'oxid' => '50a02cd2304baccdd22cb78336571',
      'oxprice' => 924.45,
      'oxvat' => 35,
      'amount' => 642,
    ),
    3 =>
    array(
      'oxid' => '2736a16296dfd28a720662d86fdec',
      'oxprice' => 810.49,
      'oxvat' => 35,
      'amount' => 417,
    ),
    4 =>
    array(
      'oxid' => 'a0c412cd7c1bd6718a9626774a028',
      'oxprice' => 29.16,
      'oxvat' => 35,
      'amount' => 257,
    ),
    5 =>
    array(
      'oxid' => '33d571d75f57cedf3240b584df65d',
      'oxprice' => 254.11,
      'oxvat' => 35,
      'amount' => 774,
    ),
    6 =>
    array(
      'oxid' => '96f798cde9bdd524d2d8878b63fe3',
      'oxprice' => 23.64,
      'oxvat' => 35,
      'amount' => 253,
    ),
    7 =>
    array(
      'oxid' => 'e615fca214a3ae5c696a50217d12e',
      'oxprice' => 348.6,
      'oxvat' => 35,
      'amount' => 923,
    ),
    8 =>
    array(
      'oxid' => '12dbf4d71d4c704324b8c64b983ce',
      'oxprice' => 270.82,
      'oxvat' => 35,
      'amount' => 276,
    ),
    9 =>
    array(
      'oxid' => '3c289344607bd15558f95dad571ee',
      'oxprice' => 769.22,
      'oxvat' => 35,
      'amount' => 600,
    ),
    10 =>
    array(
      'oxid' => 'fc3c0fe490229d85a42a0af55ce42',
      'oxprice' => 40.51,
      'oxvat' => 35,
      'amount' => 853,
    ),
    11 =>
    array(
      'oxid' => '38159093ffa5842a98101899946ae',
      'oxprice' => 914.54,
      'oxvat' => 35,
      'amount' => 806,
    ),
    12 =>
    array(
      'oxid' => '094569953b5f48cc98d46a69d0dbf',
      'oxprice' => 73.11,
      'oxvat' => 35,
      'amount' => 466,
    ),
    13 =>
    array(
      'oxid' => '9a0c05127282e6fc9e6a245f7a59a',
      'oxprice' => 442.56,
      'oxvat' => 35,
      'amount' => 520,
    ),
    14 =>
    array(
      'oxid' => '82011d7386a4a1876891c64646461',
      'oxprice' => 543.76,
      'oxvat' => 35,
      'amount' => 23,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 6,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '40302e1434fe5d6995fad5854022c',
          1 => 'c646ce9e73d4f98e3ccf7805189e5',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 83,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '40302e1434fe5d6995fad5854022c',
          1 => 'c646ce9e73d4f98e3ccf7805189e5',
          2 => '50a02cd2304baccdd22cb78336571',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 22,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
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
        'oxaddsum' => 23,
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
        'oxaddsum' => 11,
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
        'oxdiscount' => 1,
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
      '40302e1434fe5d6995fad5854022c' =>
      array(
        0 => '74,43',
        1 => '56.194,65',
      ),
      'c646ce9e73d4f98e3ccf7805189e5' =>
      array(
        0 => '746,37',
        1 => '565.748,46',
      ),
      '50a02cd2304baccdd22cb78336571' =>
      array(
        0 => '1.248,01',
        1 => '801.222,42',
      ),
      '2736a16296dfd28a720662d86fdec' =>
      array(
        0 => '1.094,16',
        1 => '456.264,72',
      ),
      'a0c412cd7c1bd6718a9626774a028' =>
      array(
        0 => '39,37',
        1 => '10.118,09',
      ),
      '33d571d75f57cedf3240b584df65d' =>
      array(
        0 => '343,05',
        1 => '265.520,70',
      ),
      '96f798cde9bdd524d2d8878b63fe3' =>
      array(
        0 => '31,91',
        1 => '8.073,23',
      ),
      'e615fca214a3ae5c696a50217d12e' =>
      array(
        0 => '470,61',
        1 => '434.373,03',
      ),
      '12dbf4d71d4c704324b8c64b983ce' =>
      array(
        0 => '365,61',
        1 => '100.908,36',
      ),
      '3c289344607bd15558f95dad571ee' =>
      array(
        0 => '1.038,45',
        1 => '623.070,00',
      ),
      'fc3c0fe490229d85a42a0af55ce42' =>
      array(
        0 => '54,69',
        1 => '46.650,57',
      ),
      '38159093ffa5842a98101899946ae' =>
      array(
        0 => '1.234,63',
        1 => '995.111,78',
      ),
      '094569953b5f48cc98d46a69d0dbf' =>
      array(
        0 => '98,70',
        1 => '45.994,20',
      ),
      '9a0c05127282e6fc9e6a245f7a59a' =>
      array(
        0 => '597,46',
        1 => '310.679,20',
      ),
      '82011d7386a4a1876891c64646461' =>
      array(
        0 => '734,08',
        1 => '16.883,84',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        35 => '1.228.062,18',
      ),
      'wrapping' =>
      array(
        'brutto' => '178.865,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '521.072,46',
        'netto' => '385.979,60',
        'vat' => '135.092,86',
      ),
      'payment' =>
      array(
        'brutto' => '1.156.734,42',
        'netto' => '856.840,31',
        'vat' => '299.894,11',
      ),
      'voucher' =>
      array(
        'brutto' => '2,00',
      ),
      'totalNetto' => '3.508.749,07',
      'totalBrutto' => '4.736.813,25',
      'grandTotal' => '6.593.483,13',
    ),
  ),
);
