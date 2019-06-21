<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_25',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'f902dfb2f7f69b5ef7dfff40439ae',
      'oxprice' => 808.95,
      'oxvat' => 15,
      'amount' => 326,
    ),
    1 =>
    array(
      'oxid' => '2b574e975fbc77e1a0b250d0332f8',
      'oxprice' => 355.97,
      'oxvat' => 15,
      'amount' => 27,
    ),
    2 =>
    array(
      'oxid' => 'c3d028065f6deb470ef414cdfd484',
      'oxprice' => 301.89,
      'oxvat' => 15,
      'amount' => 885,
    ),
    3 =>
    array(
      'oxid' => 'c1afcf7cd275567d4780b9a2a4ba7',
      'oxprice' => 362.56,
      'oxvat' => 15,
      'amount' => 555,
    ),
    4 =>
    array(
      'oxid' => 'b898d0bd0f7f87203209aa1de517a',
      'oxprice' => 376.19,
      'oxvat' => 15,
      'amount' => 999,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 4,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'f902dfb2f7f69b5ef7dfff40439ae',
          1 => '2b574e975fbc77e1a0b250d0332f8',
          2 => 'c3d028065f6deb470ef414cdfd484',
          3 => 'c1afcf7cd275567d4780b9a2a4ba7',
          4 => 'b898d0bd0f7f87203209aa1de517a',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 35,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'f902dfb2f7f69b5ef7dfff40439ae',
          1 => '2b574e975fbc77e1a0b250d0332f8',
          2 => 'c3d028065f6deb470ef414cdfd484',
          3 => 'c1afcf7cd275567d4780b9a2a4ba7',
          4 => 'b898d0bd0f7f87203209aa1de517a',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 10,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'f902dfb2f7f69b5ef7dfff40439ae',
          1 => '2b574e975fbc77e1a0b250d0332f8',
          2 => 'c3d028065f6deb470ef414cdfd484',
          3 => 'c1afcf7cd275567d4780b9a2a4ba7',
          4 => 'b898d0bd0f7f87203209aa1de517a',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 38,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 42,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 90,
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
        'oxaddsum' => 41,
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
        'oxaddsum' => 70,
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
        'oxaddsum' => 3,
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
      'f902dfb2f7f69b5ef7dfff40439ae' =>
      array(
        0 => '930,29',
        1 => '303.274,54',
      ),
      '2b574e975fbc77e1a0b250d0332f8' =>
      array(
        0 => '409,37',
        1 => '11.052,99',
      ),
      'c3d028065f6deb470ef414cdfd484' =>
      array(
        0 => '347,17',
        1 => '307.245,45',
      ),
      'c1afcf7cd275567d4780b9a2a4ba7' =>
      array(
        0 => '416,94',
        1 => '231.401,70',
      ),
      'b898d0bd0f7f87203209aa1de517a' =>
      array(
        0 => '432,62',
        1 => '432.187,38',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        15 => '167.629,83',
      ),
      'wrapping' =>
      array(
        'brutto' => '27.920,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '1.465.084,74',
        'netto' => '1.273.986,73',
        'vat' => '191.098,01',
      ),
      'payment' =>
      array(
        'brutto' => '38,00',
        'netto' => '33,04',
        'vat' => '4,96',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.117.532,23',
      'totalBrutto' => '1.285.162,06',
      'grandTotal' => '2.778.204,80',
    ),
  ),
);
