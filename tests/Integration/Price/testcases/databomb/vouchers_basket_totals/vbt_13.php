<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_13',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '91b4534a2a01917916402498d0156',
      'oxprice' => 291.81,
      'oxvat' => 31,
      'amount' => 816,
    ),
    1 =>
    array(
      'oxid' => 'b89b29d487bca22752e4447053892',
      'oxprice' => 850.39,
      'oxvat' => 31,
      'amount' => 108,
    ),
    2 =>
    array(
      'oxid' => '5fc521eff9802676dc5c440a1ff81',
      'oxprice' => 233.01,
      'oxvat' => 31,
      'amount' => 886,
    ),
    3 =>
    array(
      'oxid' => '3846dc4ee26ddc6e1ff8db30f401b',
      'oxprice' => 478.27,
      'oxvat' => 31,
      'amount' => 251,
    ),
    4 =>
    array(
      'oxid' => 'ca4de92a59a176aec8fa4fde578b5',
      'oxprice' => 670.36,
      'oxvat' => 31,
      'amount' => 979,
    ),
    5 =>
    array(
      'oxid' => '29cc1b35b209864b48479667e2a5c',
      'oxprice' => 167.65,
      'oxvat' => 31,
      'amount' => 716,
    ),
    6 =>
    array(
      'oxid' => '953a46c5ff12de2bf58f04e6b7c3c',
      'oxprice' => 884.28,
      'oxvat' => 31,
      'amount' => 487,
    ),
    7 =>
    array(
      'oxid' => '6be997a886c26b08248e37bef137b',
      'oxprice' => 232.77,
      'oxvat' => 31,
      'amount' => 756,
    ),
    8 =>
    array(
      'oxid' => '097c1938c4e06f38a315db25be3b8',
      'oxprice' => 9.89,
      'oxvat' => 31,
      'amount' => 390,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 35,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '91b4534a2a01917916402498d0156',
          1 => 'b89b29d487bca22752e4447053892',
          2 => '5fc521eff9802676dc5c440a1ff81',
          3 => '3846dc4ee26ddc6e1ff8db30f401b',
          4 => 'ca4de92a59a176aec8fa4fde578b5',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 56,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '91b4534a2a01917916402498d0156',
          1 => 'b89b29d487bca22752e4447053892',
          2 => '5fc521eff9802676dc5c440a1ff81',
          3 => '3846dc4ee26ddc6e1ff8db30f401b',
          4 => 'ca4de92a59a176aec8fa4fde578b5',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 58,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 78,
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
        'oxaddsum' => 33,
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
        'oxaddsum' => 83,
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
        'oxdiscount' => 7,
        'oxdiscounttype' => 'percent',
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
      '91b4534a2a01917916402498d0156' =>
      array(
        0 => '291,81',
        1 => '238.116,96',
      ),
      'b89b29d487bca22752e4447053892' =>
      array(
        0 => '850,39',
        1 => '91.842,12',
      ),
      '5fc521eff9802676dc5c440a1ff81' =>
      array(
        0 => '233,01',
        1 => '206.446,86',
      ),
      '3846dc4ee26ddc6e1ff8db30f401b' =>
      array(
        0 => '478,27',
        1 => '120.045,77',
      ),
      'ca4de92a59a176aec8fa4fde578b5' =>
      array(
        0 => '670,36',
        1 => '656.282,44',
      ),
      '29cc1b35b209864b48479667e2a5c' =>
      array(
        0 => '167,65',
        1 => '120.037,40',
      ),
      '953a46c5ff12de2bf58f04e6b7c3c' =>
      array(
        0 => '884,28',
        1 => '430.644,36',
      ),
      '6be997a886c26b08248e37bef137b' =>
      array(
        0 => '232,77',
        1 => '175.974,12',
      ),
      '097c1938c4e06f38a315db25be3b8' =>
      array(
        0 => '9,89',
        1 => '3.857,10',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        31 => '388.919,88',
      ),
      'wrapping' =>
      array(
        'brutto' => '170.240,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '1.695.928,12',
        'netto' => '1.294.601,62',
        'vat' => '401.326,50',
      ),
      'payment' =>
      array(
        'brutto' => '1.936.868,39',
        'netto' => '1.478.525,49',
        'vat' => '458.342,90',
      ),
      'voucher' =>
      array(
        'brutto' => '399.747,00',
      ),
      'totalNetto' => '1.254.580,25',
      'totalBrutto' => '2.043.247,13',
      'grandTotal' => '5.446.536,64',
    ),
  ),
);
