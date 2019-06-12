<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_10',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '4329f43cc904b0f03f9d1a870b619',
      'oxprice' => 314.53,
      'oxvat' => 27,
      'amount' => 793,
    ),
    1 =>
    array(
      'oxid' => '2cde62f93f125ced628f15767b00c',
      'oxprice' => 439.44,
      'oxvat' => 27,
      'amount' => 788,
    ),
    2 =>
    array(
      'oxid' => 'e19130df8d859280de49761a32cfd',
      'oxprice' => 81.14,
      'oxvat' => 27,
      'amount' => 125,
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
          0 => '4329f43cc904b0f03f9d1a870b619',
          1 => '2cde62f93f125ced628f15767b00c',
          2 => 'e19130df8d859280de49761a32cfd',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 65,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '4329f43cc904b0f03f9d1a870b619',
          1 => '2cde62f93f125ced628f15767b00c',
          2 => 'e19130df8d859280de49761a32cfd',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 66,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 47,
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
        'oxaddsum' => 23,
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
        'oxdiscount' => 21,
        'oxdiscounttype' => 'absolute',
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
      '4329f43cc904b0f03f9d1a870b619' =>
      array(
        0 => '314,53',
        1 => '249.422,29',
      ),
      '2cde62f93f125ced628f15767b00c' =>
      array(
        0 => '439,44',
        1 => '346.278,72',
      ),
      'e19130df8d859280de49761a32cfd' =>
      array(
        0 => '81,14',
        1 => '10.142,50',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        27 => '128.787,98',
      ),
      'wrapping' =>
      array(
        'brutto' => '110.890,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '24.256,74',
        'netto' => '19.099,80',
        'vat' => '5.156,94',
      ),
      'payment' =>
      array(
        'brutto' => '66,00',
        'netto' => '51,97',
        'vat' => '14,03',
      ),
      'voucher' =>
      array(
        'brutto' => '63,00',
      ),
      'totalNetto' => '476.992,53',
      'totalBrutto' => '605.843,51',
      'grandTotal' => '740.993,25',
    ),
  ),
);
