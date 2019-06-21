<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_14',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'be6b2367f97beea16d075ed08d722',
      'oxprice' => 57.33,
      'oxvat' => 3,
      'amount' => 63,
    ),
    1 =>
    array(
      'oxid' => '72e076b1118c40bdc1d60546825af',
      'oxprice' => 157.99,
      'oxvat' => 3,
      'amount' => 102,
    ),
    2 =>
    array(
      'oxid' => 'cb87f716e68fa59b4ee8586dfa2d4',
      'oxprice' => 347.14,
      'oxvat' => 3,
      'amount' => 592,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 1,
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
      'oxaddsum' => 6,
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
      'oxaddsum' => 8,
      'oxid' => 'bombDiscount_2',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    3 =>
    array(
      'oxaddsum' => 10,
      'oxid' => 'bombDiscount_3',
      'oxaddsumtype' => '%',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    4 =>
    array(
      'oxaddsum' => 5,
      'oxid' => 'bombDiscount_4',
      'oxaddsumtype' => 'abs',
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
        'oxprice' => 8,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'be6b2367f97beea16d075ed08d722',
          1 => '72e076b1118c40bdc1d60546825af',
          2 => 'cb87f716e68fa59b4ee8586dfa2d4',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 81,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'be6b2367f97beea16d075ed08d722',
          1 => '72e076b1118c40bdc1d60546825af',
          2 => 'cb87f716e68fa59b4ee8586dfa2d4',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 88,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'be6b2367f97beea16d075ed08d722',
          1 => '72e076b1118c40bdc1d60546825af',
          2 => 'cb87f716e68fa59b4ee8586dfa2d4',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 38,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 34,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 73,
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
        'oxaddsum' => 1,
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
        'oxaddsum' => 6,
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
        'oxaddsum' => 82,
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
      'be6b2367f97beea16d075ed08d722' =>
      array(
        0 => '57,33',
        1 => '3.611,79',
      ),
      '72e076b1118c40bdc1d60546825af' =>
      array(
        0 => '157,99',
        1 => '16.114,98',
      ),
      'cb87f716e68fa59b4ee8586dfa2d4' =>
      array(
        0 => '347,14',
        1 => '205.506,88',
      ),
    ),
    'totals' =>
    array(
      'discounts' =>
      array(
        'bombDiscount_0' => '1,00',
        'bombDiscount_1' => '6,00',
        'bombDiscount_2' => '8,00',
        'bombDiscount_3' => '22.521,87',
        'bombDiscount_4' => '5,00',
      ),
      'vats' =>
      array(
        3 => '5.903,64',
      ),
      'wrapping' =>
      array(
        'brutto' => '66.616,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '186.949,93',
        'netto' => '181.504,79',
        'vat' => '5.445,14',
      ),
      'payment' =>
      array(
        'brutto' => '38,00',
        'netto' => '36,89',
        'vat' => '1,11',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '196.788,14',
      'totalBrutto' => '225.233,65',
      'grandTotal' => '456.295,71',
    ),
  ),
);
