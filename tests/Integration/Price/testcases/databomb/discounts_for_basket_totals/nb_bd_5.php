<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_bd_databomb_user_5',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'c41fc269327720e80bbf4a0d8b916',
      'oxprice' => 676.29,
      'oxvat' => 17,
      'amount' => 234,
    ),
    1 =>
    array(
      'oxid' => '36ab94e137a5119cc5508064d10ec',
      'oxprice' => 545.5,
      'oxvat' => 17,
      'amount' => 504,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 14,
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
      'oxaddsum' => 13,
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
      'oxaddsum' => 7,
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
      'oxaddsum' => 9,
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
      'oxaddsum' => 6,
      'oxid' => 'bombDiscount_4',
      'oxaddsumtype' => 'abs',
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
        'oxprice' => 54,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'c41fc269327720e80bbf4a0d8b916',
          1 => '36ab94e137a5119cc5508064d10ec',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 15,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'c41fc269327720e80bbf4a0d8b916',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 4,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'c41fc269327720e80bbf4a0d8b916',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 31,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 7,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 2,
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
        'oxaddsum' => 19,
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
        'oxaddsum' => 15,
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
      'c41fc269327720e80bbf4a0d8b916' =>
      array(
        0 => '791,26',
        1 => '185.154,84',
      ),
      '36ab94e137a5119cc5508064d10ec' =>
      array(
        0 => '638,24',
        1 => '321.672,96',
      ),
    ),
    'totals' =>
    array(
      'discounts' =>
      array(
        'bombDiscount_0' => '70.955,89',
        'bombDiscount_1' => '56.663,35',
        'bombDiscount_2' => '7,00',
        'bombDiscount_3' => '34.128,14',
        'bombDiscount_4' => '6,00',
      ),
      'vats' =>
      array(
        17 => '50.138,00',
      ),
      'wrapping' =>
      array(
        'brutto' => '28.152,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '167.272,17',
        'netto' => '142.967,67',
        'vat' => '24.304,50',
      ),
      'payment' =>
      array(
        'brutto' => '158.825,27',
        'netto' => '135.748,09',
        'vat' => '23.077,18',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '294.929,42',
      'totalBrutto' => '506.827,80',
      'grandTotal' => '699.316,86',
    ),
  ),
);
