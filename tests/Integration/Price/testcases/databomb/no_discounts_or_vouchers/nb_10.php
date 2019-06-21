<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_10',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '2dd083b5845ab89455d02eaa98cab',
      'oxprice' => 511.45,
      'oxvat' => 21,
      'amount' => 732,
    ),
    1 =>
    array(
      'oxid' => '1d7cc66ec90c8f524e5f8f3b999e4',
      'oxprice' => 899.48,
      'oxvat' => 21,
      'amount' => 947,
    ),
    2 =>
    array(
      'oxid' => 'ce771aa8f567933f84e1825fa6cb1',
      'oxprice' => 42.33,
      'oxvat' => 21,
      'amount' => 324,
    ),
    3 =>
    array(
      'oxid' => '9cfef76a0a1b58eb9bf9fb365c693',
      'oxprice' => 698.37,
      'oxvat' => 21,
      'amount' => 658,
    ),
    4 =>
    array(
      'oxid' => 'cc2132384ff59be0c771c50d273ac',
      'oxprice' => 135.31,
      'oxvat' => 5,
      'amount' => 41,
    ),
    5 =>
    array(
      'oxid' => '90bcc16c274cdd6c0fe8565d62d70',
      'oxprice' => 910.61,
      'oxvat' => 21,
      'amount' => 848,
    ),
    6 =>
    array(
      'oxid' => '0f000591f43cb0802ad64cd48f597',
      'oxprice' => 235.71,
      'oxvat' => 5,
      'amount' => 809,
    ),
    7 =>
    array(
      'oxid' => '294222e55623d576408e2cb71960a',
      'oxprice' => 632.3,
      'oxvat' => 21,
      'amount' => 620,
    ),
    8 =>
    array(
      'oxid' => '83846af37f11503d5f401bf6cd216',
      'oxprice' => 175.62,
      'oxvat' => 5,
      'amount' => 132,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 50,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '2dd083b5845ab89455d02eaa98cab',
          1 => '1d7cc66ec90c8f524e5f8f3b999e4',
          2 => 'ce771aa8f567933f84e1825fa6cb1',
          3 => '9cfef76a0a1b58eb9bf9fb365c693',
          4 => 'cc2132384ff59be0c771c50d273ac',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 75,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '2dd083b5845ab89455d02eaa98cab',
          1 => '1d7cc66ec90c8f524e5f8f3b999e4',
          2 => 'ce771aa8f567933f84e1825fa6cb1',
          3 => '9cfef76a0a1b58eb9bf9fb365c693',
          4 => 'cc2132384ff59be0c771c50d273ac',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 61,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '2dd083b5845ab89455d02eaa98cab',
          1 => '1d7cc66ec90c8f524e5f8f3b999e4',
          2 => 'ce771aa8f567933f84e1825fa6cb1',
          3 => '9cfef76a0a1b58eb9bf9fb365c693',
          4 => 'cc2132384ff59be0c771c50d273ac',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 45,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 52,
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
        'oxaddsum' => 11,
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
        'oxaddsum' => 6,
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
        'oxaddsum' => 91,
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
      '2dd083b5845ab89455d02eaa98cab' =>
      array(
        0 => '618,85',
        1 => '452.998,20',
      ),
      '1d7cc66ec90c8f524e5f8f3b999e4' =>
      array(
        0 => '1.088,37',
        1 => '1.030.686,39',
      ),
      'ce771aa8f567933f84e1825fa6cb1' =>
      array(
        0 => '51,22',
        1 => '16.595,28',
      ),
      '9cfef76a0a1b58eb9bf9fb365c693' =>
      array(
        0 => '845,03',
        1 => '556.029,74',
      ),
      'cc2132384ff59be0c771c50d273ac' =>
      array(
        0 => '142,08',
        1 => '5.825,28',
      ),
      '90bcc16c274cdd6c0fe8565d62d70' =>
      array(
        0 => '1.101,84',
        1 => '934.360,32',
      ),
      '0f000591f43cb0802ad64cd48f597' =>
      array(
        0 => '247,50',
        1 => '200.227,50',
      ),
      '294222e55623d576408e2cb71960a' =>
      array(
        0 => '765,08',
        1 => '474.349,60',
      ),
      '83846af37f11503d5f401bf6cd216' =>
      array(
        0 => '184,40',
        1 => '24.340,80',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        21 => '601.367,03',
        5 => '10.971,12',
      ),
      'wrapping' =>
      array(
        'brutto' => '164.822,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '3.362.842,93',
        'netto' => '2.779.209,03',
        'vat' => '583.633,90',
      ),
      'payment' =>
      array(
        'brutto' => '3.176.215,22',
        'netto' => '2.624.971,26',
        'vat' => '551.243,96',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '3.083.074,96',
      'totalBrutto' => '3.695.413,11',
      'grandTotal' => '10.399.293,26',
    ),
  ),
);
