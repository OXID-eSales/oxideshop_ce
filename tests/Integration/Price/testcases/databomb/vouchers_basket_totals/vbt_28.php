<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_28',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '895dbb1219c53252481faacbff994',
      'oxprice' => 25.03,
      'oxvat' => 30,
      'amount' => 157,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 86,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '895dbb1219c53252481faacbff994',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 73,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '895dbb1219c53252481faacbff994',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 56,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 50,
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
        'oxaddsum' => 26,
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
        'oxaddsum' => 16,
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
        'oxdiscount' => 17,
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
      '895dbb1219c53252481faacbff994' =>
      array(
        0 => '25,03',
        1 => '3.929,71',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        30 => '518,53',
      ),
      'wrapping' =>
      array(
        'brutto' => '11.461,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '1.037,72',
        'netto' => '798,25',
        'vat' => '239,47',
      ),
      'payment' =>
      array(
        'brutto' => '56,00',
        'netto' => '43,08',
        'vat' => '12,92',
      ),
      'voucher' =>
      array(
        'brutto' => '1.682,75',
      ),
      'totalNetto' => '1.728,43',
      'totalBrutto' => '3.929,71',
      'grandTotal' => '14.801,68',
    ),
  ),
);
