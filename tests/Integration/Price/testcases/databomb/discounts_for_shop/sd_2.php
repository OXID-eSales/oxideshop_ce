<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_2',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '23fcdb3146c06af51542cce15a10b',
      'oxprice' => 401.41,
      'oxvat' => 13,
      'amount' => 735,
    ),
    1 =>
    array(
      'oxid' => '2e2e8744bb85cc0c3f20d68930438',
      'oxprice' => 681.69,
      'oxvat' => 13,
      'amount' => 310,
    ),
    2 =>
    array(
      'oxid' => '9a767ed7a0930250ee0d0e9b616d2',
      'oxprice' => 632.32,
      'oxvat' => 40,
      'amount' => 667,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 10,
      'oxid' => 'bombDiscount_0',
      'oxaddsumtype' => '%',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '23fcdb3146c06af51542cce15a10b',
      ),
    ),
    1 =>
    array(
      'oxaddsum' => 11,
      'oxid' => 'bombDiscount_1',
      'oxaddsumtype' => 'abs',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '23fcdb3146c06af51542cce15a10b',
        1 => '2e2e8744bb85cc0c3f20d68930438',
        2 => '9a767ed7a0930250ee0d0e9b616d2',
      ),
    ),
    2 =>
    array(
      'oxaddsum' => 7,
      'oxid' => 'bombDiscount_2',
      'oxaddsumtype' => '%',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '23fcdb3146c06af51542cce15a10b',
      ),
    ),
    3 =>
    array(
      'oxaddsum' => 7,
      'oxid' => 'bombDiscount_3',
      'oxaddsumtype' => 'abs',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '23fcdb3146c06af51542cce15a10b',
        1 => '2e2e8744bb85cc0c3f20d68930438',
        2 => '9a767ed7a0930250ee0d0e9b616d2',
      ),
    ),
    4 =>
    array(
      'oxaddsum' => 10,
      'oxid' => 'bombDiscount_4',
      'oxaddsumtype' => 'abs',
      'oxamount' => 0,
      'oxamountto' => 9999999,
      'oxprice' => 0,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
      'oxarticles' =>
      array(
        0 => '23fcdb3146c06af51542cce15a10b',
        1 => '2e2e8744bb85cc0c3f20d68930438',
        2 => '9a767ed7a0930250ee0d0e9b616d2',
      ),
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 20,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '23fcdb3146c06af51542cce15a10b',
          1 => '2e2e8744bb85cc0c3f20d68930438',
          2 => '9a767ed7a0930250ee0d0e9b616d2',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 94,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '23fcdb3146c06af51542cce15a10b',
          1 => '2e2e8744bb85cc0c3f20d68930438',
          2 => '9a767ed7a0930250ee0d0e9b616d2',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 98,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '23fcdb3146c06af51542cce15a10b',
          1 => '2e2e8744bb85cc0c3f20d68930438',
          2 => '9a767ed7a0930250ee0d0e9b616d2',
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
        'oxaddsum' => 84,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
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
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 27,
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
        'oxaddsum' => 71,
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
        'oxaddsum' => 79,
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
      '23fcdb3146c06af51542cce15a10b' =>
      array(
        0 => '308,75',
        1 => '226.931,25',
      ),
      '2e2e8744bb85cc0c3f20d68930438' =>
      array(
        0 => '653,69',
        1 => '202.643,90',
      ),
      '9a767ed7a0930250ee0d0e9b616d2' =>
      array(
        0 => '604,32',
        1 => '403.081,44',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        13 => '49.420,15',
        40 => '115.166,13',
      ),
      'wrapping' =>
      array(
        'brutto' => '167.776,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '1.249.011,89',
        'netto' => '1.105.320,26',
        'vat' => '143.691,63',
      ),
      'payment' =>
      array(
        'brutto' => '6,00',
        'netto' => '5,31',
        'vat' => '0,69',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '668.070,31',
      'totalBrutto' => '832.656,59',
      'grandTotal' => '2.249.450,48',
    ),
  ),
);
