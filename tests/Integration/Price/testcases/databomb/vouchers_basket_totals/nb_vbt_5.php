<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_5',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '5b55a2459d4a000b6976489ff940b',
      'oxprice' => 184.09,
      'oxvat' => 40,
      'amount' => 461,
    ),
    1 =>
    array(
      'oxid' => '70850191c46987125b2dead620ae5',
      'oxprice' => 825.55,
      'oxvat' => 40,
      'amount' => 35,
    ),
    2 =>
    array(
      'oxid' => 'bb6a79f53626ff07b96e76fc12b52',
      'oxprice' => 882.6,
      'oxvat' => 40,
      'amount' => 647,
    ),
    3 =>
    array(
      'oxid' => 'e981f48a068f1d9a2b5b5e1fc8226',
      'oxprice' => 951.23,
      'oxvat' => 40,
      'amount' => 219,
    ),
    4 =>
    array(
      'oxid' => 'e468e74dfe43f9c7d9f48721e059b',
      'oxprice' => 565.85,
      'oxvat' => 40,
      'amount' => 633,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 42,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '5b55a2459d4a000b6976489ff940b',
          1 => '70850191c46987125b2dead620ae5',
          2 => 'bb6a79f53626ff07b96e76fc12b52',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 54,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '5b55a2459d4a000b6976489ff940b',
          1 => '70850191c46987125b2dead620ae5',
          2 => 'bb6a79f53626ff07b96e76fc12b52',
          3 => 'e981f48a068f1d9a2b5b5e1fc8226',
          4 => 'e468e74dfe43f9c7d9f48721e059b',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 6,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
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
        'oxaddsum' => 3,
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
        'oxaddsum' => 7,
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
        'oxdiscount' => 16,
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
      '5b55a2459d4a000b6976489ff940b' =>
      array(
        0 => '257,73',
        1 => '118.813,53',
      ),
      '70850191c46987125b2dead620ae5' =>
      array(
        0 => '1.155,77',
        1 => '40.451,95',
      ),
      'bb6a79f53626ff07b96e76fc12b52' =>
      array(
        0 => '1.235,64',
        1 => '799.459,08',
      ),
      'e981f48a068f1d9a2b5b5e1fc8226' =>
      array(
        0 => '1.331,72',
        1 => '291.646,68',
      ),
      'e468e74dfe43f9c7d9f48721e059b' =>
      array(
        0 => '792,19',
        1 => '501.456,27',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        40 => '500.513,00',
      ),
      'wrapping' =>
      array(
        'brutto' => '107.730,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '52.561,83',
        'netto' => '37.544,16',
        'vat' => '15.017,67',
      ),
      'payment' =>
      array(
        'brutto' => '6,00',
        'netto' => '4,29',
        'vat' => '1,71',
      ),
      'voucher' =>
      array(
        'brutto' => '32,00',
      ),
      'totalNetto' => '1.251.282,51',
      'totalBrutto' => '1.751.827,51',
      'grandTotal' => '1.912.093,34',
    ),
  ),
);
