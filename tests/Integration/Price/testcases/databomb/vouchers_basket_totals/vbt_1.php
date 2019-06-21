<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_1',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'd5471a0f2815bf01b1c10cc969fd5',
      'oxprice' => 836.57,
      'oxvat' => 20,
      'amount' => 319,
    ),
    1 =>
    array(
      'oxid' => 'd16dd576e5f8ded3a5695ec50c71d',
      'oxprice' => 411.66,
      'oxvat' => 20,
      'amount' => 993,
    ),
    2 =>
    array(
      'oxid' => '2916c6a07c353e9f574c7f7121ef8',
      'oxprice' => 780.64,
      'oxvat' => 20,
      'amount' => 826,
    ),
    3 =>
    array(
      'oxid' => 'ce117faa0c62af91251960c6c1ce2',
      'oxprice' => 354.55,
      'oxvat' => 20,
      'amount' => 723,
    ),
    4 =>
    array(
      'oxid' => '760e9837f9e01fd31303d5ec08058',
      'oxprice' => 280.58,
      'oxvat' => 40,
      'amount' => 801,
    ),
    5 =>
    array(
      'oxid' => '3f3fa9fe030cbeb6123b9b9e7020a',
      'oxprice' => 106.92,
      'oxvat' => 40,
      'amount' => 381,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 99,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'd5471a0f2815bf01b1c10cc969fd5',
          1 => 'd16dd576e5f8ded3a5695ec50c71d',
          2 => '2916c6a07c353e9f574c7f7121ef8',
          3 => 'ce117faa0c62af91251960c6c1ce2',
          4 => '760e9837f9e01fd31303d5ec08058',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 96,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'd5471a0f2815bf01b1c10cc969fd5',
          1 => 'd16dd576e5f8ded3a5695ec50c71d',
          2 => '2916c6a07c353e9f574c7f7121ef8',
          3 => 'ce117faa0c62af91251960c6c1ce2',
          4 => '760e9837f9e01fd31303d5ec08058',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 13,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 11,
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
        'oxaddsum' => 25,
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
        'oxdiscount' => 30,
        'oxdiscounttype' => 'absolute',
        'oxallowsameseries' => 1,
        'oxallowotherseries' => 1,
        'oxallowuseanother' => 1,
        'voucher_count' => 3,
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
      'd5471a0f2815bf01b1c10cc969fd5' =>
      array(
        0 => '836,57',
        1 => '266.865,83',
      ),
      'd16dd576e5f8ded3a5695ec50c71d' =>
      array(
        0 => '411,66',
        1 => '408.778,38',
      ),
      '2916c6a07c353e9f574c7f7121ef8' =>
      array(
        0 => '780,64',
        1 => '644.808,64',
      ),
      'ce117faa0c62af91251960c6c1ce2' =>
      array(
        0 => '354,55',
        1 => '256.339,65',
      ),
      '760e9837f9e01fd31303d5ec08058' =>
      array(
        0 => '280,58',
        1 => '224.744,58',
      ),
      '3f3fa9fe030cbeb6123b9b9e7020a' =>
      array(
        0 => '106,92',
        1 => '40.736,52',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        20 => '262.785,91',
        40 => '75.848,04',
      ),
      'wrapping' =>
      array(
        'brutto' => '351.552,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '736.909,44',
        'netto' => '614.091,20',
        'vat' => '122.818,24',
      ),
      'payment' =>
      array(
        'brutto' => '13,00',
        'netto' => '10,83',
        'vat' => '2,17',
      ),
      'voucher' =>
      array(
        'brutto' => '90,00',
      ),
      'totalNetto' => '1.503.549,65',
      'totalBrutto' => '1.842.273,60',
      'grandTotal' => '2.930.658,04',
    ),
  ),
);
