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
      'oxid' => '8ebceec2f852b1c1449439adf4f88',
      'oxprice' => 619.23,
      'oxvat' => 38,
      'amount' => 852,
    ),
    1 =>
    array(
      'oxid' => 'b6d04623b6f97f1b44bf8ea6724e0',
      'oxprice' => 496.76,
      'oxvat' => 35,
      'amount' => 63,
    ),
    2 =>
    array(
      'oxid' => '1c6066e5788f32d6262351a325e95',
      'oxprice' => 184.73,
      'oxvat' => 38,
      'amount' => 93,
    ),
    3 =>
    array(
      'oxid' => '26602683ae0cc32c09e27d1876bdd',
      'oxprice' => 444.1,
      'oxvat' => 30,
      'amount' => 740,
    ),
    4 =>
    array(
      'oxid' => '0eee620229b3f697a9fdb47222476',
      'oxprice' => 842.93,
      'oxvat' => 30,
      'amount' => 777,
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
          0 => '8ebceec2f852b1c1449439adf4f88',
          1 => 'b6d04623b6f97f1b44bf8ea6724e0',
          2 => '1c6066e5788f32d6262351a325e95',
          3 => '26602683ae0cc32c09e27d1876bdd',
          4 => '0eee620229b3f697a9fdb47222476',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 63,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8ebceec2f852b1c1449439adf4f88',
          1 => 'b6d04623b6f97f1b44bf8ea6724e0',
          2 => '1c6066e5788f32d6262351a325e95',
          3 => '26602683ae0cc32c09e27d1876bdd',
          4 => '0eee620229b3f697a9fdb47222476',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 21,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8ebceec2f852b1c1449439adf4f88',
          1 => 'b6d04623b6f97f1b44bf8ea6724e0',
          2 => '1c6066e5788f32d6262351a325e95',
          3 => '26602683ae0cc32c09e27d1876bdd',
          4 => '0eee620229b3f697a9fdb47222476',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 13,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 77,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
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
        'oxaddsum' => 82,
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
        'oxaddsum' => 71,
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
        'oxaddsum' => 82,
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
      '8ebceec2f852b1c1449439adf4f88' =>
      array(
        0 => '619,23',
        1 => '527.583,96',
      ),
      'b6d04623b6f97f1b44bf8ea6724e0' =>
      array(
        0 => '496,76',
        1 => '31.295,88',
      ),
      '1c6066e5788f32d6262351a325e95' =>
      array(
        0 => '184,73',
        1 => '17.179,89',
      ),
      '26602683ae0cc32c09e27d1876bdd' =>
      array(
        0 => '444,10',
        1 => '328.634,00',
      ),
      '0eee620229b3f697a9fdb47222476' =>
      array(
        0 => '842,93',
        1 => '654.956,61',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        38 => '150.007,44',
        35 => '8.113,75',
        30 => '226.982,45',
      ),
      'wrapping' =>
      array(
        'brutto' => '53.025,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '3.665.178,30',
        'netto' => '2.819.367,92',
        'vat' => '845.810,38',
      ),
      'payment' =>
      array(
        'brutto' => '679.227,72',
        'netto' => '522.482,86',
        'vat' => '156.744,86',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.174.546,70',
      'totalBrutto' => '1.559.650,34',
      'grandTotal' => '5.957.081,36',
    ),
  ),
);
