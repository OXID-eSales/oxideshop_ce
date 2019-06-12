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
      'oxid' => 'c7874f4fdbfad9dd34df208d7f463',
      'oxprice' => 678.46,
      'oxvat' => 20,
      'amount' => 307,
    ),
    1 =>
    array(
      'oxid' => 'a2e8ac7e0b473ca4fde78d54fb04a',
      'oxprice' => 491.12,
      'oxvat' => 35,
      'amount' => 684,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 70,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'c7874f4fdbfad9dd34df208d7f463',
          1 => 'a2e8ac7e0b473ca4fde78d54fb04a',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 12,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'c7874f4fdbfad9dd34df208d7f463',
          1 => 'a2e8ac7e0b473ca4fde78d54fb04a',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 16,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'c7874f4fdbfad9dd34df208d7f463',
          1 => 'a2e8ac7e0b473ca4fde78d54fb04a',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 19,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 98,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 9,
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
        'oxaddsum' => 80,
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
        'oxaddsum' => 47,
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
        'oxaddsum' => 49,
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
      'c7874f4fdbfad9dd34df208d7f463' =>
      array(
        0 => '814,15',
        1 => '249.944,05',
      ),
      'a2e8ac7e0b473ca4fde78d54fb04a' =>
      array(
        0 => '663,01',
        1 => '453.498,84',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        20 => '41.657,34',
        35 => '117.573,77',
      ),
      'wrapping' =>
      array(
        'brutto' => '15.856,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '176,00',
        'netto' => '130,37',
        'vat' => '45,63',
      ),
      'payment' =>
      array(
        'brutto' => '133.687,59',
        'netto' => '99.027,84',
        'vat' => '34.659,75',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '544.211,78',
      'totalBrutto' => '703.442,89',
      'grandTotal' => '853.162,48',
    ),
  ),
);
