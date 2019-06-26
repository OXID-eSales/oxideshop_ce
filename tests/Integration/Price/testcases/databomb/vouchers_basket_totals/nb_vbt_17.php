<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_17',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '6546e5b5e8689f5aed3921f58e792',
      'oxprice' => 744.53,
      'oxvat' => 23,
      'amount' => 302,
    ),
    1 =>
    array(
      'oxid' => '5f96a911ab1c81f2fb0948abf37ef',
      'oxprice' => 720.26,
      'oxvat' => 23,
      'amount' => 97,
    ),
    2 =>
    array(
      'oxid' => '95d0841365b840edd6c1dcc198df6',
      'oxprice' => 584.29,
      'oxvat' => 23,
      'amount' => 458,
    ),
    3 =>
    array(
      'oxid' => 'e82f565b34ac7538eb0803d9486a2',
      'oxprice' => 267.02,
      'oxvat' => 23,
      'amount' => 463,
    ),
    4 =>
    array(
      'oxid' => 'a5cb902e50b3f1807e5606f2b8c57',
      'oxprice' => 460.5,
      'oxvat' => 23,
      'amount' => 558,
    ),
    5 =>
    array(
      'oxid' => '24c5dd31d94401edffb58a1859a86',
      'oxprice' => 527.07,
      'oxvat' => 23,
      'amount' => 999,
    ),
    6 =>
    array(
      'oxid' => 'aaa675bc0e436c6ef616904a8d2d0',
      'oxprice' => 58.8,
      'oxvat' => 23,
      'amount' => 700,
    ),
    7 =>
    array(
      'oxid' => 'f4fde6eabeaa35480edb003b69f30',
      'oxprice' => 441.76,
      'oxvat' => 23,
      'amount' => 40,
    ),
    8 =>
    array(
      'oxid' => 'e0eade7431c627fa8a7b768d3302b',
      'oxprice' => 991.11,
      'oxvat' => 23,
      'amount' => 123,
    ),
    9 =>
    array(
      'oxid' => 'd1ebb1b1def048415c9e954ebc8e1',
      'oxprice' => 278.06,
      'oxvat' => 23,
      'amount' => 946,
    ),
    10 =>
    array(
      'oxid' => '02621f80f05003f75e8cf83f2781a',
      'oxprice' => 301.58,
      'oxvat' => 23,
      'amount' => 74,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 78,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '6546e5b5e8689f5aed3921f58e792',
          1 => '5f96a911ab1c81f2fb0948abf37ef',
          2 => '95d0841365b840edd6c1dcc198df6',
          3 => 'e82f565b34ac7538eb0803d9486a2',
          4 => 'a5cb902e50b3f1807e5606f2b8c57',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 27,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '6546e5b5e8689f5aed3921f58e792',
          1 => '5f96a911ab1c81f2fb0948abf37ef',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 25,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 13,
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
        'oxaddsum' => 18,
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
        'oxaddsum' => 22,
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
        'oxdiscount' => 30,
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
      '6546e5b5e8689f5aed3921f58e792' =>
      array(
        0 => '915,77',
        1 => '276.562,54',
      ),
      '5f96a911ab1c81f2fb0948abf37ef' =>
      array(
        0 => '885,92',
        1 => '85.934,24',
      ),
      '95d0841365b840edd6c1dcc198df6' =>
      array(
        0 => '718,68',
        1 => '329.155,44',
      ),
      'e82f565b34ac7538eb0803d9486a2' =>
      array(
        0 => '328,43',
        1 => '152.063,09',
      ),
      'a5cb902e50b3f1807e5606f2b8c57' =>
      array(
        0 => '566,42',
        1 => '316.062,36',
      ),
      '24c5dd31d94401edffb58a1859a86' =>
      array(
        0 => '648,30',
        1 => '647.651,70',
      ),
      'aaa675bc0e436c6ef616904a8d2d0' =>
      array(
        0 => '72,32',
        1 => '50.624,00',
      ),
      'f4fde6eabeaa35480edb003b69f30' =>
      array(
        0 => '543,36',
        1 => '21.734,40',
      ),
      'e0eade7431c627fa8a7b768d3302b' =>
      array(
        0 => '1.219,07',
        1 => '149.945,61',
      ),
      'd1ebb1b1def048415c9e954ebc8e1' =>
      array(
        0 => '342,01',
        1 => '323.541,46',
      ),
      '02621f80f05003f75e8cf83f2781a' =>
      array(
        0 => '370,94',
        1 => '27.449,56',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        23 => '445.164,89',
      ),
      'wrapping' =>
      array(
        'brutto' => '126.135,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '40,00',
        'netto' => '32,52',
        'vat' => '7,48',
      ),
      'payment' =>
      array(
        'brutto' => '25,00',
        'netto' => '20,33',
        'vat' => '4,67',
      ),
      'voucher' =>
      array(
        'brutto' => '60,00',
      ),
      'totalNetto' => '1.935.499,51',
      'totalBrutto' => '2.380.724,40',
      'grandTotal' => '2.506.864,40',
    ),
  ),
);
