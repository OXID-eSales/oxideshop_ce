<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_18',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '9c6055d5ad7f009b7d0c41ea5fa5c',
      'oxprice' => 322.79,
      'oxvat' => 41,
      'amount' => 21,
    ),
    1 =>
    array(
      'oxid' => '2f55586ec03ae89efda0df04348dc',
      'oxprice' => 692.69,
      'oxvat' => 41,
      'amount' => 293,
    ),
    2 =>
    array(
      'oxid' => 'b9bb7b8bcdb4c0c89f8a5d67327a7',
      'oxprice' => 257.57,
      'oxvat' => 41,
      'amount' => 349,
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
          0 => '9c6055d5ad7f009b7d0c41ea5fa5c',
          1 => '2f55586ec03ae89efda0df04348dc',
          2 => 'b9bb7b8bcdb4c0c89f8a5d67327a7',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 60,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '9c6055d5ad7f009b7d0c41ea5fa5c',
          1 => '2f55586ec03ae89efda0df04348dc',
          2 => 'b9bb7b8bcdb4c0c89f8a5d67327a7',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 23,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '9c6055d5ad7f009b7d0c41ea5fa5c',
          1 => '2f55586ec03ae89efda0df04348dc',
          2 => 'b9bb7b8bcdb4c0c89f8a5d67327a7',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 55,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 49,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 83,
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
        'oxaddsum' => 14,
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
        'oxaddsum' => 68,
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
        'oxaddsum' => 27,
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
      '9c6055d5ad7f009b7d0c41ea5fa5c' =>
      array(
        0 => '322,79',
        1 => '6.778,59',
      ),
      '2f55586ec03ae89efda0df04348dc' =>
      array(
        0 => '692,69',
        1 => '202.958,17',
      ),
      'b9bb7b8bcdb4c0c89f8a5d67327a7' =>
      array(
        0 => '257,57',
        1 => '89.891,93',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        41 => '87.126,07',
      ),
      'wrapping' =>
      array(
        'brutto' => '15.249,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '42.043,02',
        'netto' => '29.817,74',
        'vat' => '12.225,28',
      ),
      'payment' =>
      array(
        'brutto' => '187.919,44',
        'netto' => '133.276,20',
        'vat' => '54.643,24',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '212.502,62',
      'totalBrutto' => '299.628,69',
      'grandTotal' => '544.840,15',
    ),
  ),
);
