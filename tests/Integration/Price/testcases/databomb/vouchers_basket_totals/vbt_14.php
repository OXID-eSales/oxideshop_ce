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
      'oxid' => 'cb85ca40ff23f551f99895e95db4a',
      'oxprice' => 985.13,
      'oxvat' => 32,
      'amount' => 16,
    ),
    1 =>
    array(
      'oxid' => 'b26f32669ba0103f40e7ad3308236',
      'oxprice' => 335.41,
      'oxvat' => 30,
      'amount' => 326,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 57,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'cb85ca40ff23f551f99895e95db4a',
          1 => 'b26f32669ba0103f40e7ad3308236',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 75,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'cb85ca40ff23f551f99895e95db4a',
          1 => 'b26f32669ba0103f40e7ad3308236',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 34,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 6,
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
        'oxaddsum' => 71,
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
        'oxaddsum' => 56,
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
        'oxdiscount' => 11,
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
      'cb85ca40ff23f551f99895e95db4a' =>
      array(
        0 => '985,13',
        1 => '15.762,08',
      ),
      'b26f32669ba0103f40e7ad3308236' =>
      array(
        0 => '335,41',
        1 => '109.343,66',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        32 => '2.693,76',
        30 => '17.788,59',
      ),
      'wrapping' =>
      array(
        'brutto' => '25.650,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '88.881,08',
        'netto' => '68.370,06',
        'vat' => '20.511,02',
      ),
      'payment' =>
      array(
        'brutto' => '60.206,10',
        'netto' => '46.312,38',
        'vat' => '13.893,72',
      ),
      'voucher' =>
      array(
        'brutto' => '36.910,07',
      ),
      'totalNetto' => '67.713,32',
      'totalBrutto' => '125.105,74',
      'grandTotal' => '262.932,85',
    ),
  ),
);
