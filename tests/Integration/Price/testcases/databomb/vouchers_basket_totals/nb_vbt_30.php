<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_30',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'f5b61ffc11e383746a222d857c67a',
      'oxprice' => 836.49,
      'oxvat' => 11,
      'amount' => 257,
    ),
    1 =>
    array(
      'oxid' => 'be7e05ff47e37407a6d0d54295ed7',
      'oxprice' => 994.06,
      'oxvat' => 43,
      'amount' => 902,
    ),
    2 =>
    array(
      'oxid' => 'cbe9fc2120d518a02015979341063',
      'oxprice' => 298.59,
      'oxvat' => 35,
      'amount' => 739,
    ),
    3 =>
    array(
      'oxid' => 'df436dab201c7a4f83a3f023201a9',
      'oxprice' => 624.51,
      'oxvat' => 43,
      'amount' => 442,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 90,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'f5b61ffc11e383746a222d857c67a',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 31,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'f5b61ffc11e383746a222d857c67a',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 3,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 14,
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
        'oxaddsumtype' => '%',
        'oxaddsum' => 23,
        'oxactive' => 1,
        'oxdeltype' => 'p',
        'oxfinalize' => 0,
        'oxparam' => 0,
        'oxparamend' => 999999999,
        'oxfixed' => 0,
      ),
    ),
    'voucherserie' =>
    array(
      0 =>
      array(
        'oxdiscount' => 23,
        'oxdiscounttype' => 'absolute',
        'oxallowsameseries' => 1,
        'oxallowotherseries' => 1,
        'oxallowuseanother' => 1,
        'voucher_count' => 2,
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
      'f5b61ffc11e383746a222d857c67a' =>
      array(
        0 => '928,50',
        1 => '238.624,50',
      ),
      'be7e05ff47e37407a6d0d54295ed7' =>
      array(
        0 => '1.421,51',
        1 => '1.282.202,02',
      ),
      'cbe9fc2120d518a02015979341063' =>
      array(
        0 => '403,10',
        1 => '297.890,90',
      ),
      'df436dab201c7a4f83a3f023201a9' =>
      array(
        0 => '893,05',
        1 => '394.728,10',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        11 => '23.646,98',
        43 => '504.241,24',
        35 => '77.229,37',
      ),
      'wrapping' =>
      array(
        'brutto' => '7.967,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '509.112,47',
        'netto' => '356.022,71',
        'vat' => '153.089,76',
      ),
      'payment' =>
      array(
        'brutto' => '3,00',
        'netto' => '2,10',
        'vat' => '0,90',
      ),
      'voucher' =>
      array(
        'brutto' => '46,00',
      ),
      'totalNetto' => '1.608.281,93',
      'totalBrutto' => '2.213.445,52',
      'grandTotal' => '2.730.481,99',
    ),
  ),
);
