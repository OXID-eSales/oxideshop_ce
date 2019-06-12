<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_26',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '21ad3087970b3493acfa6975c26cd',
      'oxprice' => 713.85,
      'oxvat' => 42,
      'amount' => 238,
    ),
    1 =>
    array(
      'oxid' => '3bb921cb25400f384b6a01dca9ea8',
      'oxprice' => 630.81,
      'oxvat' => 27,
      'amount' => 961,
    ),
    2 =>
    array(
      'oxid' => 'ada31c01d8ba832b12e4908c5d120',
      'oxprice' => 55.28,
      'oxvat' => 42,
      'amount' => 126,
    ),
    3 =>
    array(
      'oxid' => 'e85b201d4e4406d2fb8ac72b1b211',
      'oxprice' => 50.05,
      'oxvat' => 27,
      'amount' => 653,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 61,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '21ad3087970b3493acfa6975c26cd',
          1 => '3bb921cb25400f384b6a01dca9ea8',
          2 => 'ada31c01d8ba832b12e4908c5d120',
          3 => 'e85b201d4e4406d2fb8ac72b1b211',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 24,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '21ad3087970b3493acfa6975c26cd',
          1 => '3bb921cb25400f384b6a01dca9ea8',
          2 => 'ada31c01d8ba832b12e4908c5d120',
          3 => 'e85b201d4e4406d2fb8ac72b1b211',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 80,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '21ad3087970b3493acfa6975c26cd',
          1 => '3bb921cb25400f384b6a01dca9ea8',
          2 => 'ada31c01d8ba832b12e4908c5d120',
          3 => 'e85b201d4e4406d2fb8ac72b1b211',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 47,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 30,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 22,
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
        'oxaddsum' => 9,
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
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 47,
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
      '21ad3087970b3493acfa6975c26cd' =>
      array(
        0 => '713,85',
        1 => '169.896,30',
      ),
      '3bb921cb25400f384b6a01dca9ea8' =>
      array(
        0 => '630,81',
        1 => '606.208,41',
      ),
      'ada31c01d8ba832b12e4908c5d120' =>
      array(
        0 => '55,28',
        1 => '6.965,28',
      ),
      'e85b201d4e4406d2fb8ac72b1b211' =>
      array(
        0 => '50,05',
        1 => '32.682,65',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        42 => '52.311,17',
        27 => '135.827,23',
      ),
      'wrapping' =>
      array(
        'brutto' => '158.240,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '153,00',
        'netto' => '120,47',
        'vat' => '32,53',
      ),
      'payment' =>
      array(
        'brutto' => '47,00',
        'netto' => '37,01',
        'vat' => '9,99',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '627.614,24',
      'totalBrutto' => '815.752,64',
      'grandTotal' => '974.192,64',
    ),
  ),
);
