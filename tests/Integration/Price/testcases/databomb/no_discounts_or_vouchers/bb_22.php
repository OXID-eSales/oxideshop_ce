<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_22',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '402346f1058bf466db98c10004587',
      'oxprice' => 702.59,
      'oxvat' => 43,
      'amount' => 902,
    ),
    1 =>
    array(
      'oxid' => '5159ae17ce6246d4988a9ab1a4fd2',
      'oxprice' => 324.75,
      'oxvat' => 43,
      'amount' => 757,
    ),
    2 =>
    array(
      'oxid' => '33937de9dc2fac37533993a77911b',
      'oxprice' => 264.99,
      'oxvat' => 43,
      'amount' => 353,
    ),
    3 =>
    array(
      'oxid' => '5cd3679dfb2ed6f5c25c4bbc08592',
      'oxprice' => 382.32,
      'oxvat' => 43,
      'amount' => 697,
    ),
    4 =>
    array(
      'oxid' => '24a1faba2b3dcab2c79e94d18de05',
      'oxprice' => 359.33,
      'oxvat' => 43,
      'amount' => 315,
    ),
    5 =>
    array(
      'oxid' => 'db64c802b551a803eca11a1371655',
      'oxprice' => 575.45,
      'oxvat' => 43,
      'amount' => 745,
    ),
    6 =>
    array(
      'oxid' => '4f0df56c2c80efca0264f040e7488',
      'oxprice' => 709.63,
      'oxvat' => 43,
      'amount' => 146,
    ),
    7 =>
    array(
      'oxid' => '27b3ac3c190c28063a3ed7058c821',
      'oxprice' => 164.83,
      'oxvat' => 43,
      'amount' => 973,
    ),
    8 =>
    array(
      'oxid' => '89f63609bce9c7b2a4160579e0706',
      'oxprice' => 104.63,
      'oxvat' => 43,
      'amount' => 528,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 92,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '402346f1058bf466db98c10004587',
          1 => '5159ae17ce6246d4988a9ab1a4fd2',
          2 => '33937de9dc2fac37533993a77911b',
          3 => '5cd3679dfb2ed6f5c25c4bbc08592',
          4 => '24a1faba2b3dcab2c79e94d18de05',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 44,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '402346f1058bf466db98c10004587',
          1 => '5159ae17ce6246d4988a9ab1a4fd2',
          2 => '33937de9dc2fac37533993a77911b',
          3 => '5cd3679dfb2ed6f5c25c4bbc08592',
          4 => '24a1faba2b3dcab2c79e94d18de05',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 39,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '402346f1058bf466db98c10004587',
          1 => '5159ae17ce6246d4988a9ab1a4fd2',
          2 => '33937de9dc2fac37533993a77911b',
          3 => '5cd3679dfb2ed6f5c25c4bbc08592',
          4 => '24a1faba2b3dcab2c79e94d18de05',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 45,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 34,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 84,
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
        'oxaddsum' => 55,
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
        'oxaddsum' => 30,
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
        'oxaddsum' => 54,
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
      '402346f1058bf466db98c10004587' =>
      array(
        0 => '702,59',
        1 => '633.736,18',
      ),
      '5159ae17ce6246d4988a9ab1a4fd2' =>
      array(
        0 => '324,75',
        1 => '245.835,75',
      ),
      '33937de9dc2fac37533993a77911b' =>
      array(
        0 => '264,99',
        1 => '93.541,47',
      ),
      '5cd3679dfb2ed6f5c25c4bbc08592' =>
      array(
        0 => '382,32',
        1 => '266.477,04',
      ),
      '24a1faba2b3dcab2c79e94d18de05' =>
      array(
        0 => '359,33',
        1 => '113.188,95',
      ),
      'db64c802b551a803eca11a1371655' =>
      array(
        0 => '575,45',
        1 => '428.710,25',
      ),
      '4f0df56c2c80efca0264f040e7488' =>
      array(
        0 => '709,63',
        1 => '103.605,98',
      ),
      '27b3ac3c190c28063a3ed7058c821' =>
      array(
        0 => '164,83',
        1 => '160.379,59',
      ),
      '89f63609bce9c7b2a4160579e0706' =>
      array(
        0 => '104,63',
        1 => '55.244,64',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        43 => '631.684,99',
      ),
      'wrapping' =>
      array(
        'brutto' => '117.936,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '630.324,96',
        'netto' => '440.786,69',
        'vat' => '189.538,27',
      ),
      'payment' =>
      array(
        'brutto' => '45,00',
        'netto' => '31,47',
        'vat' => '13,53',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.469.034,86',
      'totalBrutto' => '2.100.719,85',
      'grandTotal' => '2.849.025,81',
    ),
  ),
);
