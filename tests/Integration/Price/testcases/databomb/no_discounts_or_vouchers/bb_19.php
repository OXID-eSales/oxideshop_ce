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
      'oxid' => '8613f67b4f13ce79ac0b8d3526730',
      'oxprice' => 809.69,
      'oxvat' => 0,
      'amount' => 272,
    ),
    1 =>
    array(
      'oxid' => '1055ef563fe97bc160550d67f85e0',
      'oxprice' => 759.05,
      'oxvat' => 0,
      'amount' => 24,
    ),
    2 =>
    array(
      'oxid' => '1e0ab487d5a20b3f189f71adb3fd9',
      'oxprice' => 248.87,
      'oxvat' => 0,
      'amount' => 315,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 20,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8613f67b4f13ce79ac0b8d3526730',
          1 => '1055ef563fe97bc160550d67f85e0',
          2 => '1e0ab487d5a20b3f189f71adb3fd9',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 98,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8613f67b4f13ce79ac0b8d3526730',
          1 => '1055ef563fe97bc160550d67f85e0',
          2 => '1e0ab487d5a20b3f189f71adb3fd9',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 17,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8613f67b4f13ce79ac0b8d3526730',
          1 => '1055ef563fe97bc160550d67f85e0',
          2 => '1e0ab487d5a20b3f189f71adb3fd9',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 26,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 42,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 70,
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
        'oxaddsum' => 45,
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
        'oxaddsum' => 63,
        'oxactive' => 1,
        'oxdeltype' => 'p',
        'oxfinalize' => 0,
        'oxparam' => 0,
        'oxparamend' => 999999999,
        'oxfixed' => 0,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 32,
        'oxactive' => 1,
        'oxdeltype' => 'p',
        'oxfinalize' => 0,
        'oxparam' => 0,
        'oxparamend' => 999999999,
        'oxfixed' => 0,
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
      '8613f67b4f13ce79ac0b8d3526730' =>
      array(
        0 => '809,69',
        1 => '220.235,68',
      ),
      '1055ef563fe97bc160550d67f85e0' =>
      array(
        0 => '759,05',
        1 => '18.217,20',
      ),
      '1e0ab487d5a20b3f189f71adb3fd9' =>
      array(
        0 => '248,87',
        1 => '78.394,05',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        0 => '0,00',
      ),
      'wrapping' =>
      array(
        'brutto' => '10.387,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '342.226,69',
        'netto' => '342.226,69',
        'vat' => false,
      ),
      'payment' =>
      array(
        'brutto' => '171.359,14',
        'netto' => '171.359,14',
        'vat' => false,
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '316.846,93',
      'totalBrutto' => '316.846,93',
      'grandTotal' => '840.819,76',
    ),
  ),
);
