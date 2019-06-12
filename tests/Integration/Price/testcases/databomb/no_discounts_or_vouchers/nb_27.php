<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_27',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'f10a57dd413558609a1e0f1d9afa0',
      'oxprice' => 76.91,
      'oxvat' => 12,
      'amount' => 143,
    ),
    1 =>
    array(
      'oxid' => 'f036c3766fc2e0c6dfc4e5a69d14c',
      'oxprice' => 133.53,
      'oxvat' => 3,
      'amount' => 826,
    ),
    2 =>
    array(
      'oxid' => 'bde2ccc63f5b87c908ae493fa4921',
      'oxprice' => 649.92,
      'oxvat' => 3,
      'amount' => 990,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 62,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'f10a57dd413558609a1e0f1d9afa0',
          1 => 'f036c3766fc2e0c6dfc4e5a69d14c',
          2 => 'bde2ccc63f5b87c908ae493fa4921',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 68,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'f10a57dd413558609a1e0f1d9afa0',
          1 => 'f036c3766fc2e0c6dfc4e5a69d14c',
          2 => 'bde2ccc63f5b87c908ae493fa4921',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 10,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'f10a57dd413558609a1e0f1d9afa0',
          1 => 'f036c3766fc2e0c6dfc4e5a69d14c',
          2 => 'bde2ccc63f5b87c908ae493fa4921',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 18,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 34,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 64,
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
        'oxaddsum' => 16,
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
        'oxaddsum' => 60,
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
        'oxaddsum' => 44,
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
      'f10a57dd413558609a1e0f1d9afa0' =>
      array(
        0 => '86,14',
        1 => '12.318,02',
      ),
      'f036c3766fc2e0c6dfc4e5a69d14c' =>
      array(
        0 => '137,54',
        1 => '113.608,04',
      ),
      'bde2ccc63f5b87c908ae493fa4921' =>
      array(
        0 => '669,42',
        1 => '662.725,80',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        12 => '1.319,79',
        3 => '22.611,67',
      ),
      'wrapping' =>
      array(
        'brutto' => '19.590,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '820.213,94',
        'netto' => '796.324,21',
        'vat' => '23.889,73',
      ),
      'payment' =>
      array(
        'brutto' => '289.595,84',
        'netto' => '281.161,01',
        'vat' => '8.434,83',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '764.720,40',
      'totalBrutto' => '788.651,86',
      'grandTotal' => '1.918.051,64',
    ),
  ),
);
