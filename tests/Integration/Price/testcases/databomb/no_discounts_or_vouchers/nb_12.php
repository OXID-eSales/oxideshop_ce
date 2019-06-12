<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_12',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'd5ba92448901897fbab5675000bc6',
      'oxprice' => 129.09,
      'oxvat' => 19,
      'amount' => 2,
    ),
    1 =>
    array(
      'oxid' => '5bacc5926fe7e401147f925d540a0',
      'oxprice' => 508.19,
      'oxvat' => 33,
      'amount' => 245,
    ),
    2 =>
    array(
      'oxid' => 'dbad5c2c4ac2df8a233acdbcc9fa4',
      'oxprice' => 36,
      'oxvat' => 19,
      'amount' => 453,
    ),
    3 =>
    array(
      'oxid' => 'd6a8694332096413a1355810c0d3f',
      'oxprice' => 998.58,
      'oxvat' => 23,
      'amount' => 155,
    ),
    4 =>
    array(
      'oxid' => '00afd1558f4e39dbe489e4669ea6e',
      'oxprice' => 899.46,
      'oxvat' => 33,
      'amount' => 205,
    ),
    5 =>
    array(
      'oxid' => '32ce967d4be49fa8ad81b48b48260',
      'oxprice' => 875.18,
      'oxvat' => 19,
      'amount' => 181,
    ),
    6 =>
    array(
      'oxid' => 'a079e6682ccd27e3527ea032899af',
      'oxprice' => 844.99,
      'oxvat' => 23,
      'amount' => 704,
    ),
    7 =>
    array(
      'oxid' => '1572d62f05ab77020a82b0c0eb003',
      'oxprice' => 87.97,
      'oxvat' => 23,
      'amount' => 924,
    ),
    8 =>
    array(
      'oxid' => '55d7787f44b2172900728a7c66e7e',
      'oxprice' => 574.78,
      'oxvat' => 23,
      'amount' => 629,
    ),
    9 =>
    array(
      'oxid' => 'ed0f3f6fa640ac35f3750bbe33146',
      'oxprice' => 126.11,
      'oxvat' => 19,
      'amount' => 84,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 60,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'd5ba92448901897fbab5675000bc6',
          1 => '5bacc5926fe7e401147f925d540a0',
          2 => 'dbad5c2c4ac2df8a233acdbcc9fa4',
          3 => 'd6a8694332096413a1355810c0d3f',
          4 => '00afd1558f4e39dbe489e4669ea6e',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 1,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'd5ba92448901897fbab5675000bc6',
          1 => '5bacc5926fe7e401147f925d540a0',
          2 => 'dbad5c2c4ac2df8a233acdbcc9fa4',
          3 => 'd6a8694332096413a1355810c0d3f',
          4 => '00afd1558f4e39dbe489e4669ea6e',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 22,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'd5ba92448901897fbab5675000bc6',
          1 => '5bacc5926fe7e401147f925d540a0',
          2 => 'dbad5c2c4ac2df8a233acdbcc9fa4',
          3 => 'd6a8694332096413a1355810c0d3f',
          4 => '00afd1558f4e39dbe489e4669ea6e',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 88,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 50,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 89,
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
        'oxaddsum' => 35,
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
        'oxaddsum' => 63,
        'oxactive' => 1,
        'oxdeltype' => 'p',
        'oxfinalize' => 0,
        'oxparam' => 0,
        'oxparamend' => 999999999,
        'oxfixed' => 0,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 64,
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
      'd5ba92448901897fbab5675000bc6' =>
      array(
        0 => '153,62',
        1 => '307,24',
      ),
      '5bacc5926fe7e401147f925d540a0' =>
      array(
        0 => '675,89',
        1 => '165.593,05',
      ),
      'dbad5c2c4ac2df8a233acdbcc9fa4' =>
      array(
        0 => '42,84',
        1 => '19.406,52',
      ),
      'd6a8694332096413a1355810c0d3f' =>
      array(
        0 => '1.228,25',
        1 => '190.378,75',
      ),
      '00afd1558f4e39dbe489e4669ea6e' =>
      array(
        0 => '1.196,28',
        1 => '245.237,40',
      ),
      '32ce967d4be49fa8ad81b48b48260' =>
      array(
        0 => '1.041,46',
        1 => '188.504,26',
      ),
      'a079e6682ccd27e3527ea032899af' =>
      array(
        0 => '1.039,34',
        1 => '731.695,36',
      ),
      '1572d62f05ab77020a82b0c0eb003' =>
      array(
        0 => '108,20',
        1 => '99.976,80',
      ),
      '55d7787f44b2172900728a7c66e7e' =>
      array(
        0 => '706,98',
        1 => '444.690,42',
      ),
      'ed0f3f6fa640ac35f3750bbe33146' =>
      array(
        0 => '150,07',
        1 => '12.605,88',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        19 => '35.257,60',
        33 => '101.935,37',
        23 => '274.268,70',
      ),
      'wrapping' =>
      array(
        'brutto' => '23.320,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '1.322.088,28',
        'netto' => '1.074.868,52',
        'vat' => '247.219,76',
      ),
      'payment' =>
      array(
        'brutto' => '3.010.025,88',
        'netto' => '2.447.175,51',
        'vat' => '562.850,37',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.686.934,01',
      'totalBrutto' => '2.098.395,68',
      'grandTotal' => '6.453.829,84',
    ),
  ),
);
