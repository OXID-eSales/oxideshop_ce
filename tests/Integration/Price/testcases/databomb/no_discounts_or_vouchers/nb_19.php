<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_19',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'c46c909b576035aaff669606bb972',
      'oxprice' => 734.76,
      'oxvat' => 28,
      'amount' => 493,
    ),
    1 =>
    array(
      'oxid' => 'a8a4b6076f362752adb28d2fc6cc0',
      'oxprice' => 870.26,
      'oxvat' => 28,
      'amount' => 774,
    ),
    2 =>
    array(
      'oxid' => '5d43b3337a1b3699eaff7738f6f38',
      'oxprice' => 893.32,
      'oxvat' => 28,
      'amount' => 935,
    ),
    3 =>
    array(
      'oxid' => 'd18994c352edd80dfec2bcd50f406',
      'oxprice' => 374.42,
      'oxvat' => 28,
      'amount' => 11,
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
          0 => 'c46c909b576035aaff669606bb972',
          1 => 'a8a4b6076f362752adb28d2fc6cc0',
          2 => '5d43b3337a1b3699eaff7738f6f38',
          3 => 'd18994c352edd80dfec2bcd50f406',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 24,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'c46c909b576035aaff669606bb972',
          1 => 'a8a4b6076f362752adb28d2fc6cc0',
          2 => '5d43b3337a1b3699eaff7738f6f38',
          3 => 'd18994c352edd80dfec2bcd50f406',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 16,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'c46c909b576035aaff669606bb972',
          1 => 'a8a4b6076f362752adb28d2fc6cc0',
          2 => '5d43b3337a1b3699eaff7738f6f38',
          3 => 'd18994c352edd80dfec2bcd50f406',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 59,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 14,
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
        'oxaddsum' => 61,
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
        'oxaddsum' => 97,
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
        'oxaddsum' => 58,
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
      'c46c909b576035aaff669606bb972' =>
      array(
        0 => '940,49',
        1 => '463.661,57',
      ),
      'a8a4b6076f362752adb28d2fc6cc0' =>
      array(
        0 => '1.113,93',
        1 => '862.181,82',
      ),
      '5d43b3337a1b3699eaff7738f6f38' =>
      array(
        0 => '1.143,45',
        1 => '1.069.125,75',
      ),
      'd18994c352edd80dfec2bcd50f406' =>
      array(
        0 => '479,26',
        1 => '5.271,86',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        28 => '525.052,72',
      ),
      'wrapping' =>
      array(
        'brutto' => '35.408,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '5.184.520,56',
        'netto' => '4.050.406,69',
        'vat' => '1.134.113,87',
      ),
      'payment' =>
      array(
        'brutto' => '59,00',
        'netto' => '46,09',
        'vat' => '12,91',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.875.188,28',
      'totalBrutto' => '2.400.241,00',
      'grandTotal' => '7.620.228,56',
    ),
  ),
);
