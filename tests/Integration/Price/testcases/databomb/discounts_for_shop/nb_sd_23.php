<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_sd_databomb_user_23',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '8620c24b066d751d785ffd53ab2e9',
      'oxprice' => 456.88,
      'oxvat' => 27,
      'amount' => 388,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 13,
      'oxid' => 'bombDiscount_0',
      'oxaddsumtype' => 'abs',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '8620c24b066d751d785ffd53ab2e9',
      ),
    ),
    1 =>
    array(
      'oxaddsum' => 11,
      'oxid' => 'bombDiscount_1',
      'oxaddsumtype' => '%',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '8620c24b066d751d785ffd53ab2e9',
      ),
    ),
    2 =>
    array(
      'oxaddsum' => 7,
      'oxid' => 'bombDiscount_2',
      'oxaddsumtype' => 'abs',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '8620c24b066d751d785ffd53ab2e9',
      ),
    ),
    3 =>
    array(
      'oxaddsum' => 2,
      'oxid' => 'bombDiscount_3',
      'oxaddsumtype' => 'abs',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '8620c24b066d751d785ffd53ab2e9',
      ),
    ),
    4 =>
    array(
      'oxaddsum' => 12,
      'oxid' => 'bombDiscount_4',
      'oxaddsumtype' => '%',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '8620c24b066d751d785ffd53ab2e9',
      ),
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 18,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8620c24b066d751d785ffd53ab2e9',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 64,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8620c24b066d751d785ffd53ab2e9',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 98,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8620c24b066d751d785ffd53ab2e9',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 29,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 18,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 17,
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
        'oxaddsum' => 20,
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
        'oxaddsum' => 19,
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
        'oxaddsum' => 24,
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
      '8620c24b066d751d785ffd53ab2e9' =>
      array(
        0 => '436,34',
        1 => '169.299,92',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        27 => '35.992,90',
      ),
      'wrapping' =>
      array(
        'brutto' => '38.024,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '74.510,96',
        'netto' => '58.670,05',
        'vat' => '15.840,91',
      ),
      'payment' =>
      array(
        'brutto' => '70.705,16',
        'netto' => '55.673,35',
        'vat' => '15.031,81',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '133.307,02',
      'totalBrutto' => '169.299,92',
      'grandTotal' => '352.540,04',
    ),
  ),
);
