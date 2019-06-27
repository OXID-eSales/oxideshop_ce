<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_sd_databomb_user_22',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '0efc6876765a060e95e0920591a30',
      'oxprice' => 369.89,
      'oxvat' => 25,
      'amount' => 568,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 2,
      'oxid' => 'bombDiscount_0',
      'oxaddsumtype' => '%',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '0efc6876765a060e95e0920591a30',
      ),
    ),
    1 =>
    array(
      'oxaddsum' => 5,
      'oxid' => 'bombDiscount_1',
      'oxaddsumtype' => 'abs',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '0efc6876765a060e95e0920591a30',
      ),
    ),
    2 =>
    array(
      'oxaddsum' => 13,
      'oxid' => 'bombDiscount_2',
      'oxaddsumtype' => '%',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '0efc6876765a060e95e0920591a30',
      ),
    ),
    3 =>
    array(
      'oxaddsum' => 14,
      'oxid' => 'bombDiscount_3',
      'oxaddsumtype' => 'abs',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '0efc6876765a060e95e0920591a30',
      ),
    ),
    4 =>
    array(
      'oxaddsum' => 1,
      'oxid' => 'bombDiscount_4',
      'oxaddsumtype' => '%',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '0efc6876765a060e95e0920591a30',
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
        'oxprice' => 7,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '0efc6876765a060e95e0920591a30',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 19,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '0efc6876765a060e95e0920591a30',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 19,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '0efc6876765a060e95e0920591a30',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 2,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 11,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
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
        'oxaddsum' => 17,
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
        'oxaddsum' => 18,
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
      '0efc6876765a060e95e0920591a30' =>
      array(
        0 => '372,10',
        1 => '211.352,80',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        25 => '42.270,56',
      ),
      'wrapping' =>
      array(
        'brutto' => '10.792,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '52.873,20',
        'netto' => '42.298,56',
        'vat' => '10.574,64',
      ),
      'payment' =>
      array(
        'brutto' => '5.284,52',
        'netto' => '4.227,62',
        'vat' => '1.056,90',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '169.082,24',
      'totalBrutto' => '211.352,80',
      'grandTotal' => '280.302,52',
    ),
  ),
);
