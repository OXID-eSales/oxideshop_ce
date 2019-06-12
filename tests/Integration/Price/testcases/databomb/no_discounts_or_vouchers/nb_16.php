<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_16',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '56bb86bd3bce352657017827eee7b',
      'oxprice' => 503.74,
      'oxvat' => 0,
      'amount' => 344,
    ),
    1 =>
    array(
      'oxid' => 'f771a641b151b5b87735b15ec93cb',
      'oxprice' => 10.3,
      'oxvat' => 17,
      'amount' => 988,
    ),
    2 =>
    array(
      'oxid' => '717917edc917a026c1f3cf96ca1e7',
      'oxprice' => 993.04,
      'oxvat' => 0,
      'amount' => 121,
    ),
    3 =>
    array(
      'oxid' => '24cabc3aef9f9c9d627717da40693',
      'oxprice' => 857.73,
      'oxvat' => 27,
      'amount' => 239,
    ),
    4 =>
    array(
      'oxid' => '398f1b51d96144458a7a074994ab5',
      'oxprice' => 446.56,
      'oxvat' => 27,
      'amount' => 81,
    ),
    5 =>
    array(
      'oxid' => 'f65399ebfec0897f0086d507c6445',
      'oxprice' => 780.06,
      'oxvat' => 0,
      'amount' => 939,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 44,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '56bb86bd3bce352657017827eee7b',
          1 => 'f771a641b151b5b87735b15ec93cb',
          2 => '717917edc917a026c1f3cf96ca1e7',
          3 => '24cabc3aef9f9c9d627717da40693',
          4 => '398f1b51d96144458a7a074994ab5',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 68,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '56bb86bd3bce352657017827eee7b',
          1 => 'f771a641b151b5b87735b15ec93cb',
          2 => '717917edc917a026c1f3cf96ca1e7',
          3 => '24cabc3aef9f9c9d627717da40693',
          4 => '398f1b51d96144458a7a074994ab5',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 81,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '56bb86bd3bce352657017827eee7b',
          1 => 'f771a641b151b5b87735b15ec93cb',
          2 => '717917edc917a026c1f3cf96ca1e7',
          3 => '24cabc3aef9f9c9d627717da40693',
          4 => '398f1b51d96144458a7a074994ab5',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 86,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 55,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 30,
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
        'oxaddsum' => 39,
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
        'oxaddsum' => 56,
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
        'oxaddsum' => 25,
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
      '56bb86bd3bce352657017827eee7b' =>
      array(
        0 => '503,74',
        1 => '173.286,56',
      ),
      'f771a641b151b5b87735b15ec93cb' =>
      array(
        0 => '12,05',
        1 => '11.905,40',
      ),
      '717917edc917a026c1f3cf96ca1e7' =>
      array(
        0 => '993,04',
        1 => '120.157,84',
      ),
      '24cabc3aef9f9c9d627717da40693' =>
      array(
        0 => '1.089,32',
        1 => '260.347,48',
      ),
      '398f1b51d96144458a7a074994ab5' =>
      array(
        0 => '567,13',
        1 => '45.937,53',
      ),
      'f65399ebfec0897f0086d507c6445' =>
      array(
        0 => '780,06',
        1 => '732.476,34',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        0 => '0,00',
        17 => '1.729,84',
        27 => '65.115,71',
      ),
      'wrapping' =>
      array(
        'brutto' => '143.613,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '336.122,79',
        'netto' => '336.122,79',
        'vat' => false,
      ),
      'payment' =>
      array(
        'brutto' => '1.445.001,19',
        'netto' => '1.445.001,19',
        'vat' => false,
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.277.265,60',
      'totalBrutto' => '1.344.111,15',
      'grandTotal' => '3.268.848,13',
    ),
  ),
);
