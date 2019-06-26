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
      'oxid' => '22bbb5558e7e61b1756242c3d1585',
      'oxprice' => 126.85,
      'oxvat' => 12,
      'amount' => 972,
    ),
    1 =>
    array(
      'oxid' => '1947575dfda953182d93fe6c705de',
      'oxprice' => 871.44,
      'oxvat' => 12,
      'amount' => 156,
    ),
    2 =>
    array(
      'oxid' => 'ff6ab4a9289817d63b60a34943fde',
      'oxprice' => 984.2,
      'oxvat' => 12,
      'amount' => 994,
    ),
    3 =>
    array(
      'oxid' => '489e8a9b7c0d99bf03898480ed7bf',
      'oxprice' => 56.38,
      'oxvat' => 12,
      'amount' => 809,
    ),
    4 =>
    array(
      'oxid' => '11c906ba7c7acffaf7cef864246cb',
      'oxprice' => 445.59,
      'oxvat' => 12,
      'amount' => 552,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 83,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '22bbb5558e7e61b1756242c3d1585',
          1 => '1947575dfda953182d93fe6c705de',
          2 => 'ff6ab4a9289817d63b60a34943fde',
          3 => '489e8a9b7c0d99bf03898480ed7bf',
          4 => '11c906ba7c7acffaf7cef864246cb',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 5,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '22bbb5558e7e61b1756242c3d1585',
          1 => '1947575dfda953182d93fe6c705de',
          2 => 'ff6ab4a9289817d63b60a34943fde',
          3 => '489e8a9b7c0d99bf03898480ed7bf',
          4 => '11c906ba7c7acffaf7cef864246cb',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 30,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '22bbb5558e7e61b1756242c3d1585',
          1 => '1947575dfda953182d93fe6c705de',
          2 => 'ff6ab4a9289817d63b60a34943fde',
          3 => '489e8a9b7c0d99bf03898480ed7bf',
          4 => '11c906ba7c7acffaf7cef864246cb',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 34,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 10,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 97,
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
        'oxaddsum' => 86,
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
        'oxaddsum' => 48,
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
        'oxaddsum' => 84,
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
      '22bbb5558e7e61b1756242c3d1585' =>
      array(
        0 => '142,07',
        1 => '138.092,04',
      ),
      '1947575dfda953182d93fe6c705de' =>
      array(
        0 => '976,01',
        1 => '152.257,56',
      ),
      'ff6ab4a9289817d63b60a34943fde' =>
      array(
        0 => '1.102,30',
        1 => '1.095.686,20',
      ),
      '489e8a9b7c0d99bf03898480ed7bf' =>
      array(
        0 => '63,15',
        1 => '51.088,35',
      ),
      '11c906ba7c7acffaf7cef864246cb' =>
      array(
        0 => '499,06',
        1 => '275.481,12',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        12 => '183.493,42',
      ),
      'wrapping' =>
      array(
        'brutto' => '104.490,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '218,00',
        'netto' => '194,64',
        'vat' => '23,36',
      ),
      'payment' =>
      array(
        'brutto' => '34,00',
        'netto' => '30,36',
        'vat' => '3,64',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.529.111,85',
      'totalBrutto' => '1.712.605,27',
      'grandTotal' => '1.817.347,27',
    ),
  ),
);
