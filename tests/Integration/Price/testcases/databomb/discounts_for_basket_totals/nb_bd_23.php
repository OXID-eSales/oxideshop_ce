<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_bd_databomb_user_23',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'ac79c98e4e4faee4e0e9f803881fd',
      'oxprice' => 980.61,
      'oxvat' => 5,
      'amount' => 412,
    ),
    1 =>
    array(
      'oxid' => '77e3c62c268ce2f50068c7422496e',
      'oxprice' => 284.78,
      'oxvat' => 43,
      'amount' => 709,
    ),
    2 =>
    array(
      'oxid' => '44357025bb40a164e4aae57fa9162',
      'oxprice' => 471.17,
      'oxvat' => 34,
      'amount' => 620,
    ),
    3 =>
    array(
      'oxid' => '35fd6b516a4b27efae5f69a2ac6c6',
      'oxprice' => 369.15,
      'oxvat' => 34,
      'amount' => 745,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 10,
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
      'oxaddsum' => 10,
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
      'oxaddsum' => 10,
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
      'oxaddsum' => 1,
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
      'oxaddsum' => 8,
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
        'oxprice' => 81,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'ac79c98e4e4faee4e0e9f803881fd',
          1 => '77e3c62c268ce2f50068c7422496e',
          2 => '44357025bb40a164e4aae57fa9162',
          3 => '35fd6b516a4b27efae5f69a2ac6c6',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 80,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'ac79c98e4e4faee4e0e9f803881fd',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 50,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'ac79c98e4e4faee4e0e9f803881fd',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 2,
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
        'oxaddsum' => 10,
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
        'oxaddsum' => 15,
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
        'oxaddsum' => 2,
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
      'ac79c98e4e4faee4e0e9f803881fd' =>
      array(
        0 => '1.029,64',
        1 => '424.211,68',
      ),
      '77e3c62c268ce2f50068c7422496e' =>
      array(
        0 => '407,24',
        1 => '288.733,16',
      ),
      '44357025bb40a164e4aae57fa9162' =>
      array(
        0 => '631,37',
        1 => '391.449,40',
      ),
      '35fd6b516a4b27efae5f69a2ac6c6' =>
      array(
        0 => '494,66',
        1 => '368.521,70',
      ),
    ),
    'totals' =>
    array(
      'discounts' =>
      array(
        'bombDiscount_0' => '10,00',
        'bombDiscount_1' => '10,00',
        'bombDiscount_2' => '10,00',
        'bombDiscount_3' => '14.728,86',
        'bombDiscount_4' => '116.652,57',
      ),
      'vats' =>
      array(
        5 => '18.398,29',
        43 => '79.075,74',
        34 => '175.624,61',
      ),
      'wrapping' =>
      array(
        'brutto' => '188.594,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '294.583,19',
        'netto' => '219.838,20',
        'vat' => '74.744,99',
      ),
      'payment' =>
      array(
        'brutto' => '32.721,75',
        'netto' => '24.419,22',
        'vat' => '8.302,53',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.068.405,87',
      'totalBrutto' => '1.472.915,94',
      'grandTotal' => '1.857.403,45',
    ),
  ),
);
