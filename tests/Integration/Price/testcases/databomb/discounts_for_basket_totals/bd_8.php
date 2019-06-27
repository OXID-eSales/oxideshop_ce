<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_8',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '1c7ff3d57f934bf75dca35e024d96',
      'oxprice' => 425.23,
      'oxvat' => 4,
      'amount' => 479,
    ),
    1 =>
    array(
      'oxid' => '03acd98737107763039f4d77be35c',
      'oxprice' => 985.76,
      'oxvat' => 23,
      'amount' => 21,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 3,
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
      'oxaddsum' => 4,
      'oxid' => 'bombDiscount_1',
      'oxaddsumtype' => 'abs',
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
      'oxaddsum' => 11,
      'oxid' => 'bombDiscount_3',
      'oxaddsumtype' => '%',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    4 =>
    array(
      'oxaddsum' => 2,
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
        'oxprice' => 62,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '1c7ff3d57f934bf75dca35e024d96',
          1 => '03acd98737107763039f4d77be35c',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 94,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '1c7ff3d57f934bf75dca35e024d96',
          1 => '03acd98737107763039f4d77be35c',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 92,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '1c7ff3d57f934bf75dca35e024d96',
          1 => '03acd98737107763039f4d77be35c',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 26,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 12,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 70,
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
        'oxaddsum' => 47,
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
        'oxaddsum' => 85,
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
        'oxaddsum' => 91,
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
      '1c7ff3d57f934bf75dca35e024d96' =>
      array(
        0 => '425,23',
        1 => '203.685,17',
      ),
      '03acd98737107763039f4d77be35c' =>
      array(
        0 => '985,76',
        1 => '20.700,96',
      ),
    ),
    'totals' =>
    array(
      'discounts' =>
      array(
        'bombDiscount_0' => '6.731,58',
        'bombDiscount_1' => '4,00',
        'bombDiscount_2' => '2,00',
        'bombDiscount_3' => '23.941,34',
        'bombDiscount_4' => '3.874,14',
      ),
      'vats' =>
      array(
        4 => '6.627,69',
        23 => '3.274,83',
      ),
      'wrapping' =>
      array(
        'brutto' => '46.000,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '204.323,38',
        'netto' => '196.464,79',
        'vat' => '7.858,59',
      ),
      'payment' =>
      array(
        'brutto' => '102.480,68',
        'netto' => '98.539,12',
        'vat' => '3.941,56',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '179.930,55',
      'totalBrutto' => '224.386,13',
      'grandTotal' => '542.637,13',
    ),
  ),
);
