<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_sd_databomb_user_14',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '9db3770cd3d173bc1737cafd3a6be',
      'oxprice' => 539.54,
      'oxvat' => 18,
      'amount' => 691,
    ),
    1 =>
    array(
      'oxid' => '7fb746d2fe499b87cfc51d60b57fa',
      'oxprice' => 243.97,
      'oxvat' => 18,
      'amount' => 556,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 11,
      'oxid' => 'bombDiscount_0',
      'oxaddsumtype' => 'abs',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '9db3770cd3d173bc1737cafd3a6be',
      ),
    ),
    1 =>
    array(
      'oxaddsum' => 9,
      'oxid' => 'bombDiscount_1',
      'oxaddsumtype' => 'abs',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '9db3770cd3d173bc1737cafd3a6be',
      ),
    ),
    2 =>
    array(
      'oxaddsum' => 10,
      'oxid' => 'bombDiscount_2',
      'oxaddsumtype' => 'abs',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '9db3770cd3d173bc1737cafd3a6be',
      ),
    ),
    3 =>
    array(
      'oxaddsum' => 1,
      'oxid' => 'bombDiscount_3',
      'oxaddsumtype' => '%',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '9db3770cd3d173bc1737cafd3a6be',
      ),
    ),
    4 =>
    array(
      'oxaddsum' => 10,
      'oxid' => 'bombDiscount_4',
      'oxaddsumtype' => 'abs',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '9db3770cd3d173bc1737cafd3a6be',
        1 => '7fb746d2fe499b87cfc51d60b57fa',
      ),
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 19,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '9db3770cd3d173bc1737cafd3a6be',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 37,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '9db3770cd3d173bc1737cafd3a6be',
          1 => '7fb746d2fe499b87cfc51d60b57fa',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 38,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '9db3770cd3d173bc1737cafd3a6be',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 8,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 4,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
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
        'oxaddsum' => 16,
        'oxactive' => 1,
        'oxdeltype' => 'p',
        'oxfinalize' => 0,
        'oxparam' => 0,
        'oxparamend' => 999999999,
        'oxfixed' => 0,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 12,
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
      'blEnterNetPrice' => true,
      'blShowNetPrice' => false,
    ),
    'activeCurrencyRate' => 1,
  ),
  'expected' =>
  array(
    'articles' =>
    array(
      '9db3770cd3d173bc1737cafd3a6be' =>
      array(
        0 => '590,59',
        1 => '408.097,69',
      ),
      '7fb746d2fe499b87cfc51d60b57fa' =>
      array(
        0 => '277,88',
        1 => '154.501,28',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        18 => '85.820,18',
      ),
      'wrapping' =>
      array(
        'brutto' => '46.830,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '157.542,72',
        'netto' => '133.510,78',
        'vat' => '24.031,94',
      ),
      'payment' =>
      array(
        'brutto' => '8,00',
        'netto' => '6,78',
        'vat' => '1,22',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '476.778,79',
      'totalBrutto' => '562.598,97',
      'grandTotal' => '766.979,69',
    ),
  ),
);
