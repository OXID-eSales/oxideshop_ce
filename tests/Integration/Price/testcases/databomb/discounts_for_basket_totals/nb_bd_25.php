<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_bd_databomb_user_25',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'c99a36d9b1b519590aa33d2f82819',
      'oxprice' => 149.39,
      'oxvat' => 19,
      'amount' => 439,
    ),
    1 =>
    array(
      'oxid' => 'e1fbd99264e441a1793d1f8a3d5ce',
      'oxprice' => 947.84,
      'oxvat' => 19,
      'amount' => 969,
    ),
    2 =>
    array(
      'oxid' => 'd1d3349e7530f149d3356591ce6c2',
      'oxprice' => 271.37,
      'oxvat' => 19,
      'amount' => 460,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 9,
      'oxid' => 'bombDiscount_0',
      'oxaddsumtype' => '%',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    1 =>
    array(
      'oxaddsum' => 1,
      'oxid' => 'bombDiscount_1',
      'oxaddsumtype' => '%',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    2 =>
    array(
      'oxaddsum' => 2,
      'oxid' => 'bombDiscount_2',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    3 =>
    array(
      'oxaddsum' => 6,
      'oxid' => 'bombDiscount_3',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    4 =>
    array(
      'oxaddsum' => 3,
      'oxid' => 'bombDiscount_4',
      'oxaddsumtype' => '%',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
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
          0 => 'c99a36d9b1b519590aa33d2f82819',
          1 => 'e1fbd99264e441a1793d1f8a3d5ce',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 93,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'c99a36d9b1b519590aa33d2f82819',
          1 => 'e1fbd99264e441a1793d1f8a3d5ce',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 34,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'c99a36d9b1b519590aa33d2f82819',
          1 => 'e1fbd99264e441a1793d1f8a3d5ce',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 30,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 3,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
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
        'oxaddsum' => 2,
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
        'oxaddsum' => 18,
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
        'oxaddsum' => 13,
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
      'c99a36d9b1b519590aa33d2f82819' =>
      array(
        0 => '177,77',
        1 => '78.041,03',
      ),
      'e1fbd99264e441a1793d1f8a3d5ce' =>
      array(
        0 => '1.127,93',
        1 => '1.092.964,17',
      ),
      'd1d3349e7530f149d3356591ce6c2' =>
      array(
        0 => '322,93',
        1 => '148.547,80',
      ),
    ),
    'totals' =>
    array(
      'discounts' =>
      array(
        'bombDiscount_0' => '118.759,77',
        'bombDiscount_1' => '12.007,93',
        'bombDiscount_2' => '2,00',
        'bombDiscount_3' => '6,00',
        'bombDiscount_4' => '35.663,32',
      ),
      'vats' =>
      array(
        19 => '184.110,64',
      ),
      'wrapping' =>
      array(
        'brutto' => '47.872,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '197.950,95',
        'netto' => '166.345,34',
        'vat' => '31.605,61',
      ),
      'payment' =>
      array(
        'brutto' => '405.319,48',
        'netto' => '340.604,61',
        'vat' => '64.714,87',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '969.003,34',
      'totalBrutto' => '1.319.553,00',
      'grandTotal' => '1.804.256,41',
    ),
  ),
);
