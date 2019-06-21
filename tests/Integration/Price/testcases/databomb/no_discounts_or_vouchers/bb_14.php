<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_14',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '1a03ceb89f8245661095bb2aa41fc',
      'oxprice' => 930.15,
      'oxvat' => 33,
      'amount' => 978,
    ),
    1 =>
    array(
      'oxid' => 'b93d51ff16c8903bdbd115936a3f6',
      'oxprice' => 706.69,
      'oxvat' => 33,
      'amount' => 48,
    ),
    2 =>
    array(
      'oxid' => '0af867366b8396d96b94174f0a33f',
      'oxprice' => 73.08,
      'oxvat' => 33,
      'amount' => 259,
    ),
    3 =>
    array(
      'oxid' => 'da7b620f93bed884199dfeba39ef0',
      'oxprice' => 834.7,
      'oxvat' => 33,
      'amount' => 82,
    ),
    4 =>
    array(
      'oxid' => '56a21a271a2b96f612840957e1f57',
      'oxprice' => 365.8,
      'oxvat' => 33,
      'amount' => 102,
    ),
    5 =>
    array(
      'oxid' => '6c51c60be742a6feca74a6e6b67f0',
      'oxprice' => 523.97,
      'oxvat' => 33,
      'amount' => 703,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 0,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '1a03ceb89f8245661095bb2aa41fc',
          1 => 'b93d51ff16c8903bdbd115936a3f6',
          2 => '0af867366b8396d96b94174f0a33f',
          3 => 'da7b620f93bed884199dfeba39ef0',
          4 => '56a21a271a2b96f612840957e1f57',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 40,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '1a03ceb89f8245661095bb2aa41fc',
          1 => 'b93d51ff16c8903bdbd115936a3f6',
          2 => '0af867366b8396d96b94174f0a33f',
          3 => 'da7b620f93bed884199dfeba39ef0',
          4 => '56a21a271a2b96f612840957e1f57',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 49,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '1a03ceb89f8245661095bb2aa41fc',
          1 => 'b93d51ff16c8903bdbd115936a3f6',
          2 => '0af867366b8396d96b94174f0a33f',
          3 => 'da7b620f93bed884199dfeba39ef0',
          4 => '56a21a271a2b96f612840957e1f57',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 69,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 5,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 3,
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
        'oxaddsum' => 24,
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
        'oxaddsum' => 76,
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
        'oxaddsum' => 73,
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
      '1a03ceb89f8245661095bb2aa41fc' =>
      array(
        0 => '930,15',
        1 => '909.686,70',
      ),
      'b93d51ff16c8903bdbd115936a3f6' =>
      array(
        0 => '706,69',
        1 => '33.921,12',
      ),
      '0af867366b8396d96b94174f0a33f' =>
      array(
        0 => '73,08',
        1 => '18.927,72',
      ),
      'da7b620f93bed884199dfeba39ef0' =>
      array(
        0 => '834,70',
        1 => '68.445,40',
      ),
      '56a21a271a2b96f612840957e1f57' =>
      array(
        0 => '365,80',
        1 => '37.311,60',
      ),
      '6c51c60be742a6feca74a6e6b67f0' =>
      array(
        0 => '523,97',
        1 => '368.350,91',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        33 => '356.460,40',
      ),
      'wrapping' =>
      array(
        'brutto' => '71.981,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '2.485.393,17',
        'netto' => '1.868.716,67',
        'vat' => '616.676,50',
      ),
      'payment' =>
      array(
        'brutto' => '2.706.205,27',
        'netto' => '2.034.740,80',
        'vat' => '671.464,47',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.080.183,05',
      'totalBrutto' => '1.436.643,45',
      'grandTotal' => '6.700.222,89',
    ),
  ),
);
