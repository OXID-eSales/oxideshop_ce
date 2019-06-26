<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_7',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '945a940d02ebb6ce4ebadd6fc2a00',
      'oxprice' => 931.09,
      'oxvat' => 35,
      'amount' => 345,
    ),
    1 =>
    array(
      'oxid' => 'eaa19bb3e4a02a4cf19533321dee2',
      'oxprice' => 141.31,
      'oxvat' => 35,
      'amount' => 304,
    ),
    2 =>
    array(
      'oxid' => 'e04435e946b7d453fc77da4377caa',
      'oxprice' => 271.84,
      'oxvat' => 35,
      'amount' => 721,
    ),
    3 =>
    array(
      'oxid' => '2bbda61f9b4c32d2159e6afed3875',
      'oxprice' => 889.07,
      'oxvat' => 35,
      'amount' => 586,
    ),
    4 =>
    array(
      'oxid' => '4adb7e0d9281f20581ac02da0b925',
      'oxprice' => 726.06,
      'oxvat' => 35,
      'amount' => 425,
    ),
    5 =>
    array(
      'oxid' => 'cc7e49d9090bd0bc71909c93fb1f3',
      'oxprice' => 896.41,
      'oxvat' => 35,
      'amount' => 609,
    ),
    6 =>
    array(
      'oxid' => 'b85fa1b638df96d6ca4c2a954b4ea',
      'oxprice' => 653.14,
      'oxvat' => 35,
      'amount' => 113,
    ),
    7 =>
    array(
      'oxid' => '9339425ba313d2f4563d99b3ee07d',
      'oxprice' => 514.51,
      'oxvat' => 35,
      'amount' => 719,
    ),
    8 =>
    array(
      'oxid' => 'ece9f94198faa10ceb634ce7ace5e',
      'oxprice' => 554.12,
      'oxvat' => 35,
      'amount' => 688,
    ),
    9 =>
    array(
      'oxid' => '0d958daecac04d816ac7105362be8',
      'oxprice' => 663.69,
      'oxvat' => 35,
      'amount' => 662,
    ),
    10 =>
    array(
      'oxid' => '21856b5fc0da160681965b7ab6018',
      'oxprice' => 629.36,
      'oxvat' => 35,
      'amount' => 633,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 29,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '945a940d02ebb6ce4ebadd6fc2a00',
          1 => 'eaa19bb3e4a02a4cf19533321dee2',
          2 => 'e04435e946b7d453fc77da4377caa',
          3 => '2bbda61f9b4c32d2159e6afed3875',
          4 => '4adb7e0d9281f20581ac02da0b925',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 92,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '945a940d02ebb6ce4ebadd6fc2a00',
          1 => 'eaa19bb3e4a02a4cf19533321dee2',
          2 => 'e04435e946b7d453fc77da4377caa',
          3 => '2bbda61f9b4c32d2159e6afed3875',
          4 => '4adb7e0d9281f20581ac02da0b925',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 97,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 62,
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
        'oxaddsum' => 7,
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
        'oxaddsum' => 5,
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
      '945a940d02ebb6ce4ebadd6fc2a00' =>
      array(
        0 => '931,09',
        1 => '321.226,05',
      ),
      'eaa19bb3e4a02a4cf19533321dee2' =>
      array(
        0 => '141,31',
        1 => '42.958,24',
      ),
      'e04435e946b7d453fc77da4377caa' =>
      array(
        0 => '271,84',
        1 => '195.996,64',
      ),
      '2bbda61f9b4c32d2159e6afed3875' =>
      array(
        0 => '889,07',
        1 => '520.995,02',
      ),
      '4adb7e0d9281f20581ac02da0b925' =>
      array(
        0 => '726,06',
        1 => '308.575,50',
      ),
      'cc7e49d9090bd0bc71909c93fb1f3' =>
      array(
        0 => '896,41',
        1 => '545.913,69',
      ),
      'b85fa1b638df96d6ca4c2a954b4ea' =>
      array(
        0 => '653,14',
        1 => '73.804,82',
      ),
      '9339425ba313d2f4563d99b3ee07d' =>
      array(
        0 => '514,51',
        1 => '369.932,69',
      ),
      'ece9f94198faa10ceb634ce7ace5e' =>
      array(
        0 => '554,12',
        1 => '381.234,56',
      ),
      '0d958daecac04d816ac7105362be8' =>
      array(
        0 => '663,69',
        1 => '439.362,78',
      ),
      '21856b5fc0da160681965b7ab6018' =>
      array(
        0 => '629,36',
        1 => '398.384,88',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        35 => '932.899,04',
      ),
      'wrapping' =>
      array(
        'brutto' => '219.052,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '179.926,24',
        'netto' => '133.278,70',
        'vat' => '46.647,54',
      ),
      'payment' =>
      array(
        'brutto' => '97,00',
        'netto' => '71,85',
        'vat' => '25,15',
      ),
      'voucher' =>
      array(
        'brutto' => '60,00',
      ),
      'totalNetto' => '2.665.425,83',
      'totalBrutto' => '3.598.384,87',
      'grandTotal' => '3.997.400,11',
    ),
  ),
);
