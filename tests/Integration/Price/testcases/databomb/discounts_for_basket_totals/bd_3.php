<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_3',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '963c5bbc9caebce6196474408a065',
      'oxprice' => 297.42,
      'oxvat' => 21,
      'amount' => 761,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 8,
      'oxid' => 'bombDiscount_0',
      'oxaddsumtype' => 'abs',
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
      'oxaddsum' => 6,
      'oxid' => 'bombDiscount_2',
      'oxaddsumtype' => '%',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    3 =>
    array(
      'oxaddsum' => 4,
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
      'oxaddsum' => 7,
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
        'oxprice' => 68,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '963c5bbc9caebce6196474408a065',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 17,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '963c5bbc9caebce6196474408a065',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 78,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '963c5bbc9caebce6196474408a065',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 37,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 27,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 77,
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
        'oxaddsum' => 13,
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
        'oxaddsum' => 1,
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
        'oxaddsum' => 71,
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
      '963c5bbc9caebce6196474408a065' =>
      array(
        0 => '297,42',
        1 => '226.336,62',
      ),
    ),
    'totals' =>
    array(
      'discounts' =>
      array(
        'bombDiscount_0' => '8,00',
        'bombDiscount_1' => '2.263,29',
        'bombDiscount_2' => '13.443,92',
        'bombDiscount_3' => '4,00',
        'bombDiscount_4' => '14.743,22',
      ),
      'vats' =>
      array(
        21 => '33.994,69',
      ),
      'wrapping' =>
      array(
        'brutto' => '59.358,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '192.386,13',
        'netto' => '158.996,80',
        'vat' => '33.389,33',
      ),
      'payment' =>
      array(
        'brutto' => '37,00',
        'netto' => '30,58',
        'vat' => '6,42',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '161.879,50',
      'totalBrutto' => '226.336,62',
      'grandTotal' => '447.655,32',
    ),
  ),
);
