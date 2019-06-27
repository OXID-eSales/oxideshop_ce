<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_9',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '6a9a6e3f680f9b747ac36863b7752',
      'oxprice' => 755.38,
      'oxvat' => 29,
      'amount' => 795,
    ),
    1 =>
    array(
      'oxid' => '09bd8260ce43ba510e592ea80d6a8',
      'oxprice' => 84.3,
      'oxvat' => 42,
      'amount' => 429,
    ),
    2 =>
    array(
      'oxid' => '382722b3ad841f56770ef81353bda',
      'oxprice' => 791.13,
      'oxvat' => 29,
      'amount' => 634,
    ),
    3 =>
    array(
      'oxid' => '6cca4757ea3f96d359b2c942e8820',
      'oxprice' => 31.82,
      'oxvat' => 42,
      'amount' => 181,
    ),
    4 =>
    array(
      'oxid' => '44bf410906f81f9c26e79177360ef',
      'oxprice' => 344.33,
      'oxvat' => 42,
      'amount' => 548,
    ),
    5 =>
    array(
      'oxid' => '809fed271f8d1cf3197b8e6f4b324',
      'oxprice' => 557.57,
      'oxvat' => 32,
      'amount' => 212,
    ),
    6 =>
    array(
      'oxid' => '60b5bdb5d40e54ef7f94d7f34e2a2',
      'oxprice' => 125.33,
      'oxvat' => 32,
      'amount' => 196,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 71,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '6a9a6e3f680f9b747ac36863b7752',
          1 => '09bd8260ce43ba510e592ea80d6a8',
          2 => '382722b3ad841f56770ef81353bda',
          3 => '6cca4757ea3f96d359b2c942e8820',
          4 => '44bf410906f81f9c26e79177360ef',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 43,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '6a9a6e3f680f9b747ac36863b7752',
          1 => '09bd8260ce43ba510e592ea80d6a8',
          2 => '382722b3ad841f56770ef81353bda',
          3 => '6cca4757ea3f96d359b2c942e8820',
          4 => '44bf410906f81f9c26e79177360ef',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 23,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '6a9a6e3f680f9b747ac36863b7752',
          1 => '09bd8260ce43ba510e592ea80d6a8',
          2 => '382722b3ad841f56770ef81353bda',
          3 => '6cca4757ea3f96d359b2c942e8820',
          4 => '44bf410906f81f9c26e79177360ef',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 42,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 98,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 49,
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
        'oxaddsum' => 18,
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
        'oxaddsum' => 20,
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
        'oxaddsum' => 87,
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
      '6a9a6e3f680f9b747ac36863b7752' =>
      array(
        0 => '974,44',
        1 => '774.679,80',
      ),
      '09bd8260ce43ba510e592ea80d6a8' =>
      array(
        0 => '119,71',
        1 => '51.355,59',
      ),
      '382722b3ad841f56770ef81353bda' =>
      array(
        0 => '1.020,56',
        1 => '647.035,04',
      ),
      '6cca4757ea3f96d359b2c942e8820' =>
      array(
        0 => '45,18',
        1 => '8.177,58',
      ),
      '44bf410906f81f9c26e79177360ef' =>
      array(
        0 => '488,95',
        1 => '267.944,60',
      ),
      '809fed271f8d1cf3197b8e6f4b324' =>
      array(
        0 => '735,99',
        1 => '156.029,88',
      ),
      '60b5bdb5d40e54ef7f94d7f34e2a2' =>
      array(
        0 => '165,44',
        1 => '32.426,24',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        29 => '319.610,31',
        42 => '96.859,62',
        32 => '45.686,33',
      ),
      'wrapping' =>
      array(
        'brutto' => '59.501,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '736.393,52',
        'netto' => '570.847,69',
        'vat' => '165.545,83',
      ),
      'payment' =>
      array(
        'brutto' => '1.123.097,75',
        'netto' => '870.618,41',
        'vat' => '252.479,34',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.475.492,47',
      'totalBrutto' => '1.937.648,73',
      'grandTotal' => '3.856.641,00',
    ),
  ),
);
