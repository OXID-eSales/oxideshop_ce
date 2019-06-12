<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_29',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '78ed277e411c84dfda6580f8e34f6',
      'oxprice' => 551.82,
      'oxvat' => 5,
      'amount' => 208,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 4,
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
      'oxaddsum' => 7,
      'oxid' => 'bombDiscount_1',
      'oxaddsumtype' => '%',
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
      'oxaddsumtype' => '%',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    3 =>
    array(
      'oxaddsum' => 4,
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
      'oxaddsum' => 1,
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
        'oxprice' => 25,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '78ed277e411c84dfda6580f8e34f6',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 98,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '78ed277e411c84dfda6580f8e34f6',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 15,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '78ed277e411c84dfda6580f8e34f6',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 82,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 28,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 3,
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
        'oxaddsum' => 25,
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
        'oxaddsum' => 55,
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
        'oxaddsum' => 48,
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
      '78ed277e411c84dfda6580f8e34f6' =>
      array(
        0 => '551,82',
        1 => '114.778,56',
      ),
    ),
    'totals' =>
    array(
      'discounts' =>
      array(
        'bombDiscount_0' => '4,00',
        'bombDiscount_1' => '8.034,22',
        'bombDiscount_2' => '8.539,23',
        'bombDiscount_3' => '3.928,04',
        'bombDiscount_4' => '1,00',
      ),
      'vats' =>
      array(
        5 => '4.489,15',
      ),
      'wrapping' =>
      array(
        'brutto' => '3.120,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '128,00',
        'netto' => '121,90',
        'vat' => '6,10',
      ),
      'payment' =>
      array(
        'brutto' => '82,00',
        'netto' => '78,10',
        'vat' => '3,90',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '89.782,92',
      'totalBrutto' => '114.778,56',
      'grandTotal' => '97.602,07',
    ),
  ),
);
