<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_13',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'c82460755c4763b3e60791263bbdb',
      'oxprice' => 383.97,
      'oxvat' => 18,
      'amount' => 238,
    ),
    1 =>
    array(
      'oxid' => '2ac6db7a0fd01f0a0107cd67f92f9',
      'oxprice' => 958.45,
      'oxvat' => 7,
      'amount' => 439,
    ),
    2 =>
    array(
      'oxid' => 'f2285b648b48881f4c1aa8b3c8986',
      'oxprice' => 88.09,
      'oxvat' => 33,
      'amount' => 810,
    ),
    3 =>
    array(
      'oxid' => '2e3d2e2735ae242189626a0dc54d5',
      'oxprice' => 784.24,
      'oxvat' => 33,
      'amount' => 47,
    ),
    4 =>
    array(
      'oxid' => 'f90f5c4cef25176188ea854fe9a46',
      'oxprice' => 949.46,
      'oxvat' => 18,
      'amount' => 51,
    ),
    5 =>
    array(
      'oxid' => '31a488e2f23541539aeafcb7f911a',
      'oxprice' => 287,
      'oxvat' => 7,
      'amount' => 667,
    ),
    6 =>
    array(
      'oxid' => '95deb4c7f9b0858db97cf84aa8cf8',
      'oxprice' => 549.38,
      'oxvat' => 33,
      'amount' => 518,
    ),
    7 =>
    array(
      'oxid' => 'f9871741e3680875233643cc76fce',
      'oxprice' => 749.94,
      'oxvat' => 18,
      'amount' => 400,
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
          0 => 'c82460755c4763b3e60791263bbdb',
          1 => '2ac6db7a0fd01f0a0107cd67f92f9',
          2 => 'f2285b648b48881f4c1aa8b3c8986',
          3 => '2e3d2e2735ae242189626a0dc54d5',
          4 => 'f90f5c4cef25176188ea854fe9a46',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 83,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'c82460755c4763b3e60791263bbdb',
          1 => '2ac6db7a0fd01f0a0107cd67f92f9',
          2 => 'f2285b648b48881f4c1aa8b3c8986',
          3 => '2e3d2e2735ae242189626a0dc54d5',
          4 => 'f90f5c4cef25176188ea854fe9a46',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 98,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'c82460755c4763b3e60791263bbdb',
          1 => '2ac6db7a0fd01f0a0107cd67f92f9',
          2 => 'f2285b648b48881f4c1aa8b3c8986',
          3 => '2e3d2e2735ae242189626a0dc54d5',
          4 => 'f90f5c4cef25176188ea854fe9a46',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 95,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 88,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 88,
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
        'oxaddsum' => 1,
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
        'oxaddsum' => 66,
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
        'oxaddsum' => 99,
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
      'c82460755c4763b3e60791263bbdb' =>
      array(
        0 => '383,97',
        1 => '91.384,86',
      ),
      '2ac6db7a0fd01f0a0107cd67f92f9' =>
      array(
        0 => '958,45',
        1 => '420.759,55',
      ),
      'f2285b648b48881f4c1aa8b3c8986' =>
      array(
        0 => '88,09',
        1 => '71.352,90',
      ),
      '2e3d2e2735ae242189626a0dc54d5' =>
      array(
        0 => '784,24',
        1 => '36.859,28',
      ),
      'f90f5c4cef25176188ea854fe9a46' =>
      array(
        0 => '949,46',
        1 => '48.422,46',
      ),
      '31a488e2f23541539aeafcb7f911a' =>
      array(
        0 => '287,00',
        1 => '191.429,00',
      ),
      '95deb4c7f9b0858db97cf84aa8cf8' =>
      array(
        0 => '549,38',
        1 => '284.578,84',
      ),
      'f9871741e3680875233643cc76fce' =>
      array(
        0 => '749,94',
        1 => '299.976,00',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        18 => '67.085,59',
        7 => '40.049,72',
        33 => '97.459,43',
      ),
      'wrapping' =>
      array(
        'brutto' => '155.330,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '2.398.306,40',
        'netto' => '2.241.407,85',
        'vat' => '156.898,55',
      ),
      'payment' =>
      array(
        'brutto' => '3.650.915,83',
        'netto' => '3.412.070,87',
        'vat' => '238.844,96',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.240.168,15',
      'totalBrutto' => '1.444.762,89',
      'grandTotal' => '7.649.315,12',
    ),
  ),
);
