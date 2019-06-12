<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_27',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '9e4059f71f479356ddafb03a761c0',
      'oxprice' => 921.96,
      'oxvat' => 28,
      'amount' => 218,
    ),
    1 =>
    array(
      'oxid' => '8f496744a63988d45ddd511ef5f16',
      'oxprice' => 114.94,
      'oxvat' => 10,
      'amount' => 395,
    ),
    2 =>
    array(
      'oxid' => 'e39bd22017a8b0276fbfe8df2c9d9',
      'oxprice' => 497.01,
      'oxvat' => 28,
      'amount' => 794,
    ),
    3 =>
    array(
      'oxid' => 'eca4bd55b7b2118d44d7d5c890088',
      'oxprice' => 511.02,
      'oxvat' => 28,
      'amount' => 418,
    ),
    4 =>
    array(
      'oxid' => '84513abbd7903c4008addf581323e',
      'oxprice' => 347.59,
      'oxvat' => 28,
      'amount' => 895,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 18,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '9e4059f71f479356ddafb03a761c0',
          1 => '8f496744a63988d45ddd511ef5f16',
          2 => 'e39bd22017a8b0276fbfe8df2c9d9',
          3 => 'eca4bd55b7b2118d44d7d5c890088',
          4 => '84513abbd7903c4008addf581323e',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 92,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '9e4059f71f479356ddafb03a761c0',
          1 => '8f496744a63988d45ddd511ef5f16',
          2 => 'e39bd22017a8b0276fbfe8df2c9d9',
          3 => 'eca4bd55b7b2118d44d7d5c890088',
          4 => '84513abbd7903c4008addf581323e',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 48,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '9e4059f71f479356ddafb03a761c0',
          1 => '8f496744a63988d45ddd511ef5f16',
          2 => 'e39bd22017a8b0276fbfe8df2c9d9',
          3 => 'eca4bd55b7b2118d44d7d5c890088',
          4 => '84513abbd7903c4008addf581323e',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 96,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 79,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 20,
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
        'oxaddsum' => 20,
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
        'oxaddsum' => 84,
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
        'oxaddsum' => 29,
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
      '9e4059f71f479356ddafb03a761c0' =>
      array(
        0 => '921,96',
        1 => '200.987,28',
      ),
      '8f496744a63988d45ddd511ef5f16' =>
      array(
        0 => '114,94',
        1 => '45.401,30',
      ),
      'e39bd22017a8b0276fbfe8df2c9d9' =>
      array(
        0 => '497,01',
        1 => '394.625,94',
      ),
      'eca4bd55b7b2118d44d7d5c890088' =>
      array(
        0 => '511,02',
        1 => '213.606,36',
      ),
      '84513abbd7903c4008addf581323e' =>
      array(
        0 => '347,59',
        1 => '311.093,05',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        28 => '245.068,39',
        10 => '4.127,39',
      ),
      'wrapping' =>
      array(
        'brutto' => '130.560,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '233.255,79',
        'netto' => '182.231,09',
        'vat' => '51.024,70',
      ),
      'payment' =>
      array(
        'brutto' => '1.343.010,93',
        'netto' => '1.049.227,29',
        'vat' => '293.783,64',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '916.518,15',
      'totalBrutto' => '1.165.713,93',
      'grandTotal' => '2.872.540,65',
    ),
  ),
);
