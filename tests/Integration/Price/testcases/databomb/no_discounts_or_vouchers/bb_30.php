<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_30',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '7e076c788f13b026f3653266762b4',
      'oxprice' => 173.68,
      'oxvat' => 13,
      'amount' => 960,
    ),
    1 =>
    array(
      'oxid' => '79eac2ad242d91639ffe7ed9ed1a0',
      'oxprice' => 386.85,
      'oxvat' => 13,
      'amount' => 662,
    ),
    2 =>
    array(
      'oxid' => 'fa4e0e3617a76f355c211189c53a1',
      'oxprice' => 762.84,
      'oxvat' => 13,
      'amount' => 769,
    ),
    3 =>
    array(
      'oxid' => '9d7bf621ac0bc16a75a409a052787',
      'oxprice' => 912.95,
      'oxvat' => 13,
      'amount' => 905,
    ),
    4 =>
    array(
      'oxid' => 'ddf59509768e99e0297b3f485396e',
      'oxprice' => 845.79,
      'oxvat' => 13,
      'amount' => 48,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 3,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '7e076c788f13b026f3653266762b4',
          1 => '79eac2ad242d91639ffe7ed9ed1a0',
          2 => 'fa4e0e3617a76f355c211189c53a1',
          3 => '9d7bf621ac0bc16a75a409a052787',
          4 => 'ddf59509768e99e0297b3f485396e',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 31,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '7e076c788f13b026f3653266762b4',
          1 => '79eac2ad242d91639ffe7ed9ed1a0',
          2 => 'fa4e0e3617a76f355c211189c53a1',
          3 => '9d7bf621ac0bc16a75a409a052787',
          4 => 'ddf59509768e99e0297b3f485396e',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 29,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '7e076c788f13b026f3653266762b4',
          1 => '79eac2ad242d91639ffe7ed9ed1a0',
          2 => 'fa4e0e3617a76f355c211189c53a1',
          3 => '9d7bf621ac0bc16a75a409a052787',
          4 => 'ddf59509768e99e0297b3f485396e',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 78,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 8,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 80,
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
        'oxaddsum' => 37,
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
        'oxaddsum' => 40,
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
        'oxaddsum' => 86,
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
      '7e076c788f13b026f3653266762b4' =>
      array(
        0 => '173,68',
        1 => '166.732,80',
      ),
      '79eac2ad242d91639ffe7ed9ed1a0' =>
      array(
        0 => '386,85',
        1 => '256.094,70',
      ),
      'fa4e0e3617a76f355c211189c53a1' =>
      array(
        0 => '762,84',
        1 => '586.623,96',
      ),
      '9d7bf621ac0bc16a75a409a052787' =>
      array(
        0 => '912,95',
        1 => '826.219,75',
      ),
      'ddf59509768e99e0297b3f485396e' =>
      array(
        0 => '845,79',
        1 => '40.597,92',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        13 => '215.853,97',
      ),
      'wrapping' =>
      array(
        'brutto' => '96.976,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '2.307.851,03',
        'netto' => '2.042.346,04',
        'vat' => '265.504,99',
      ),
      'payment' =>
      array(
        'brutto' => '3.263.613,72',
        'netto' => '2.888.153,73',
        'vat' => '375.459,99',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.660.415,16',
      'totalBrutto' => '1.876.269,13',
      'grandTotal' => '7.544.709,88',
    ),
  ),
);
