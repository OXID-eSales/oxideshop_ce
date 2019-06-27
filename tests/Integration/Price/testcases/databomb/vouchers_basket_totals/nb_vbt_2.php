<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_2',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '05a9f4307deab438415517388b1ad',
      'oxprice' => 615.39,
      'oxvat' => 29,
      'amount' => 232,
    ),
    1 =>
    array(
      'oxid' => '12c836f3d6e8bfbe739375d0b277b',
      'oxprice' => 368.54,
      'oxvat' => 41,
      'amount' => 613,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 40,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '05a9f4307deab438415517388b1ad',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 47,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '05a9f4307deab438415517388b1ad',
          1 => '12c836f3d6e8bfbe739375d0b277b',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 6,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 25,
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
        'oxaddsum' => 21,
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
        'oxaddsum' => 4,
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
        'oxdiscount' => 14,
        'oxdiscounttype' => 'absolute',
        'oxallowsameseries' => 1,
        'oxallowotherseries' => 1,
        'oxallowuseanother' => 1,
        'voucher_count' => 2,
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
      '05a9f4307deab438415517388b1ad' =>
      array(
        0 => '793,85',
        1 => '184.173,20',
      ),
      '12c836f3d6e8bfbe739375d0b277b' =>
      array(
        0 => '519,64',
        1 => '318.539,32',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        29 => '41.400,97',
        41 => '92.619,75',
      ),
      'wrapping' =>
      array(
        'brutto' => '39.715,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '105.573,63',
        'netto' => '74.874,91',
        'vat' => '30.698,72',
      ),
      'payment' =>
      array(
        'brutto' => '6,00',
        'netto' => '4,26',
        'vat' => '1,74',
      ),
      'voucher' =>
      array(
        'brutto' => '28,00',
      ),
      'totalNetto' => '368.663,80',
      'totalBrutto' => '502.712,52',
      'grandTotal' => '647.979,15',
    ),
  ),
);
