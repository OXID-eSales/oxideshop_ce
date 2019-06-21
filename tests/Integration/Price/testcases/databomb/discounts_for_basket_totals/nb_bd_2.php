<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_bd_databomb_user_2',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '2109185591b4172eb6336590247d9',
      'oxprice' => 736.97,
      'oxvat' => 8,
      'amount' => 545,
    ),
    1 =>
    array(
      'oxid' => 'a478248086b67f5e97b4cf23f2767',
      'oxprice' => 26.07,
      'oxvat' => 11,
      'amount' => 290,
    ),
    2 =>
    array(
      'oxid' => 'f3007307af68e675131f2b959c0a9',
      'oxprice' => 620.86,
      'oxvat' => 31,
      'amount' => 539,
    ),
    3 =>
    array(
      'oxid' => '8b5e9ae1bb000d333ec754381c3d8',
      'oxprice' => 87.81,
      'oxvat' => 11,
      'amount' => 439,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 15,
      'oxid' => 'bombDiscount_0',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    1 =>
    array(
      'oxaddsum' => 11,
      'oxid' => 'bombDiscount_1',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    2 =>
    array(
      'oxaddsum' => 3,
      'oxid' => 'bombDiscount_2',
      'oxaddsumtype' => '%',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    3 =>
    array(
      'oxaddsum' => 13,
      'oxid' => 'bombDiscount_3',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    4 =>
    array(
      'oxaddsum' => 15,
      'oxid' => 'bombDiscount_4',
      'oxaddsumtype' => '%',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 82,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '2109185591b4172eb6336590247d9',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 59,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '2109185591b4172eb6336590247d9',
          1 => 'a478248086b67f5e97b4cf23f2767',
          2 => 'f3007307af68e675131f2b959c0a9',
          3 => '8b5e9ae1bb000d333ec754381c3d8',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 11,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '2109185591b4172eb6336590247d9',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 23,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 23,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 30,
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
        'oxaddsum' => 19,
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
        'oxaddsum' => 14,
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
        'oxaddsum' => 24,
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
      '2109185591b4172eb6336590247d9' =>
      array(
        0 => '795,93',
        1 => '433.781,85',
      ),
      'a478248086b67f5e97b4cf23f2767' =>
      array(
        0 => '28,94',
        1 => '8.392,60',
      ),
      'f3007307af68e675131f2b959c0a9' =>
      array(
        0 => '813,33',
        1 => '438.384,87',
      ),
      '8b5e9ae1bb000d333ec754381c3d8' =>
      array(
        0 => '97,47',
        1 => '42.789,33',
      ),
    ),
    'totals' =>
    array(
      'discounts' =>
      array(
        'bombDiscount_0' => '15,00',
        'bombDiscount_1' => '11,00',
        'bombDiscount_2' => '27.699,68',
        'bombDiscount_3' => '13,00',
        'bombDiscount_4' => '134.341,50',
      ),
      'vats' =>
      array(
        8 => '26.491,69',
        11 => '4.181,75',
        31 => '85.529,92',
      ),
      'wrapping' =>
      array(
        'brutto' => '80.807,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '304.729,05',
        'netto' => '232.617,60',
        'vat' => '72.111,45',
      ),
      'payment' =>
      array(
        'brutto' => '245.179,43',
        'netto' => '187.159,87',
        'vat' => '58.019,56',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '645.065,11',
      'totalBrutto' => '923.348,65',
      'grandTotal' => '1.391.983,95',
    ),
  ),
);
