<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_3',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'a5e273cd64d63a695e6e2a458567f',
      'oxprice' => 209.77,
      'oxvat' => 22,
      'amount' => 50,
    ),
    1 =>
    array(
      'oxid' => 'c426905974c9f6ab5dd77a56c8b46',
      'oxprice' => 994.05,
      'oxvat' => 24,
      'amount' => 515,
    ),
    2 =>
    array(
      'oxid' => '8e393c944f92e84b01147b35de2be',
      'oxprice' => 603.77,
      'oxvat' => 22,
      'amount' => 895,
    ),
    3 =>
    array(
      'oxid' => 'aa6317984f775d0b10df645943a4e',
      'oxprice' => 160.55,
      'oxvat' => 24,
      'amount' => 474,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 31,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'a5e273cd64d63a695e6e2a458567f',
          1 => 'c426905974c9f6ab5dd77a56c8b46',
          2 => '8e393c944f92e84b01147b35de2be',
          3 => 'aa6317984f775d0b10df645943a4e',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 62,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'a5e273cd64d63a695e6e2a458567f',
          1 => 'c426905974c9f6ab5dd77a56c8b46',
          2 => '8e393c944f92e84b01147b35de2be',
          3 => 'aa6317984f775d0b10df645943a4e',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 64,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'a5e273cd64d63a695e6e2a458567f',
          1 => 'c426905974c9f6ab5dd77a56c8b46',
          2 => '8e393c944f92e84b01147b35de2be',
          3 => 'aa6317984f775d0b10df645943a4e',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 99,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 44,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 12,
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
        'oxaddsum' => 70,
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
        'oxaddsum' => 23,
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
        'oxaddsum' => 98,
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
      'a5e273cd64d63a695e6e2a458567f' =>
      array(
        0 => '209,77',
        1 => '10.488,50',
      ),
      'c426905974c9f6ab5dd77a56c8b46' =>
      array(
        0 => '994,05',
        1 => '511.935,75',
      ),
      '8e393c944f92e84b01147b35de2be' =>
      array(
        0 => '603,77',
        1 => '540.374,15',
      ),
      'aa6317984f775d0b10df645943a4e' =>
      array(
        0 => '160,55',
        1 => '76.100,70',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        22 => '99.335,89',
        24 => '113.813,51',
      ),
      'wrapping' =>
      array(
        'brutto' => '123.776,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '797.350,37',
        'netto' => '643.024,49',
        'vat' => '154.325,88',
      ),
      'payment' =>
      array(
        'brutto' => '99,00',
        'netto' => '79,84',
        'vat' => '19,16',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '925.749,70',
      'totalBrutto' => '1.138.899,10',
      'grandTotal' => '2.060.124,47',
    ),
  ),
);
