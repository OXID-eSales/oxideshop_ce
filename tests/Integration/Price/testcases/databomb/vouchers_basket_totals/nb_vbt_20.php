<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_20',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'dc7b9287ae4ec1f0ffb4820f19550',
      'oxprice' => 63.91,
      'oxvat' => 41,
      'amount' => 188,
    ),
    1 =>
    array(
      'oxid' => '44c703a7f5c68a9636fcd180f02fd',
      'oxprice' => 172.57,
      'oxvat' => 28,
      'amount' => 70,
    ),
    2 =>
    array(
      'oxid' => '261303ef10e96c614abaf7e72aa37',
      'oxprice' => 586.91,
      'oxvat' => 37,
      'amount' => 434,
    ),
    3 =>
    array(
      'oxid' => '4d80eaac2ec24b2758ba54145ac83',
      'oxprice' => 757.92,
      'oxvat' => 41,
      'amount' => 248,
    ),
    4 =>
    array(
      'oxid' => '3258cd61e50d222bb3f9efa1927fd',
      'oxprice' => 376.55,
      'oxvat' => 28,
      'amount' => 844,
    ),
    5 =>
    array(
      'oxid' => '37a221cb5c98b1a7e0f9a5714f955',
      'oxprice' => 445.36,
      'oxvat' => 28,
      'amount' => 801,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 28,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'dc7b9287ae4ec1f0ffb4820f19550',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 98,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'dc7b9287ae4ec1f0ffb4820f19550',
          1 => '44c703a7f5c68a9636fcd180f02fd',
          2 => '261303ef10e96c614abaf7e72aa37',
          3 => '4d80eaac2ec24b2758ba54145ac83',
          4 => '3258cd61e50d222bb3f9efa1927fd',
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
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 6,
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
        'oxaddsum' => 21,
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
        'oxaddsum' => 21,
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
        'oxdiscount' => 29,
        'oxdiscounttype' => 'percent',
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
      'dc7b9287ae4ec1f0ffb4820f19550' =>
      array(
        0 => '90,11',
        1 => '16.940,68',
      ),
      '44c703a7f5c68a9636fcd180f02fd' =>
      array(
        0 => '220,89',
        1 => '15.462,30',
      ),
      '261303ef10e96c614abaf7e72aa37' =>
      array(
        0 => '804,07',
        1 => '348.966,38',
      ),
      '4d80eaac2ec24b2758ba54145ac83' =>
      array(
        0 => '1.068,67',
        1 => '265.030,16',
      ),
      '3258cd61e50d222bb3f9efa1927fd' =>
      array(
        0 => '481,98',
        1 => '406.791,12',
      ),
      '37a221cb5c98b1a7e0f9a5714f955' =>
      array(
        0 => '570,06',
        1 => '456.618,06',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        41 => '41.331,93',
        28 => '96.914,81',
        37 => '47.509,61',
      ),
      'wrapping' =>
      array(
        'brutto' => '174.832,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '317.080,83',
        'netto' => '247.719,40',
        'vat' => '69.361,43',
      ),
      'payment' =>
      array(
        'brutto' => '3,00',
        'netto' => '2,34',
        'vat' => '0,66',
      ),
      'voucher' =>
      array(
        'brutto' => '748.714,13',
      ),
      'totalNetto' => '575.338,22',
      'totalBrutto' => '1.509.808,70',
      'grandTotal' => '1.253.010,40',
    ),
  ),
);
