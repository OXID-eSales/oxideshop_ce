<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_7',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'd7bcd0e6b569d1adf35d59501bd48',
      'oxprice' => 653.38,
      'oxvat' => 28,
      'amount' => 65,
    ),
    1 =>
    array(
      'oxid' => '4b4dc681e4f5ea7ebe00e560ea599',
      'oxprice' => 436.49,
      'oxvat' => 42,
      'amount' => 303,
    ),
    2 =>
    array(
      'oxid' => '4d442a6eec220f21dc18e8f3dad0c',
      'oxprice' => 605.46,
      'oxvat' => 42,
      'amount' => 829,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 95,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'd7bcd0e6b569d1adf35d59501bd48',
          1 => '4b4dc681e4f5ea7ebe00e560ea599',
          2 => '4d442a6eec220f21dc18e8f3dad0c',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 4,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'd7bcd0e6b569d1adf35d59501bd48',
          1 => '4b4dc681e4f5ea7ebe00e560ea599',
          2 => '4d442a6eec220f21dc18e8f3dad0c',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 9,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 32,
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
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 16,
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
        'oxdiscount' => 19,
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
      'd7bcd0e6b569d1adf35d59501bd48' =>
      array(
        0 => '836,33',
        1 => '54.361,45',
      ),
      '4b4dc681e4f5ea7ebe00e560ea599' =>
      array(
        0 => '619,82',
        1 => '187.805,46',
      ),
      '4d442a6eec220f21dc18e8f3dad0c' =>
      array(
        0 => '859,75',
        1 => '712.732,75',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        28 => '11.891,09',
        42 => '266.345,77',
      ),
      'wrapping' =>
      array(
        'brutto' => '4.788,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '143.250,95',
        'netto' => '100.880,95',
        'vat' => '42.370,00',
      ),
      'payment' =>
      array(
        'brutto' => '9,00',
        'netto' => '6,34',
        'vat' => '2,66',
      ),
      'voucher' =>
      array(
        'brutto' => '38,00',
      ),
      'totalNetto' => '676.624,80',
      'totalBrutto' => '954.899,66',
      'grandTotal' => '1.102.909,61',
    ),
  ),
);
