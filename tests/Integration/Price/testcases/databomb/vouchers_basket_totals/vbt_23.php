<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_23',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'bb2426c11c2467b94a84861e49d24',
      'oxprice' => 1000.14,
      'oxvat' => 19,
      'amount' => 582,
    ),
    1 =>
    array(
      'oxid' => 'f4e94902c63465def6c03a0468cfe',
      'oxprice' => 159.62,
      'oxvat' => 19,
      'amount' => 694,
    ),
    2 =>
    array(
      'oxid' => 'f4dd99d98368a0d1a614534604190',
      'oxprice' => 309.7,
      'oxvat' => 0,
      'amount' => 994,
    ),
    3 =>
    array(
      'oxid' => 'bdc685948245a91bb3ff6c00057eb',
      'oxprice' => 3.71,
      'oxvat' => 19,
      'amount' => 798,
    ),
    4 =>
    array(
      'oxid' => '2871ccd6d4f3c7cae0c14657fba44',
      'oxprice' => 482.52,
      'oxvat' => 19,
      'amount' => 700,
    ),
    5 =>
    array(
      'oxid' => '1e1acce50dab351c942e9e9f04ba2',
      'oxprice' => 966.52,
      'oxvat' => 0,
      'amount' => 579,
    ),
    6 =>
    array(
      'oxid' => 'adfaa61ba75260dd5b6b75076e251',
      'oxprice' => 84.65,
      'oxvat' => 15,
      'amount' => 146,
    ),
    7 =>
    array(
      'oxid' => '6c05c76caf67dd3357a4a030c5ddc',
      'oxprice' => 776.43,
      'oxvat' => 0,
      'amount' => 932,
    ),
    8 =>
    array(
      'oxid' => 'a814f6f550a0095e082217435a364',
      'oxprice' => 441.15,
      'oxvat' => 15,
      'amount' => 167,
    ),
    9 =>
    array(
      'oxid' => 'c7bd1800b966e6ab9a3cfa10ecaa8',
      'oxprice' => 248.03,
      'oxvat' => 19,
      'amount' => 446,
    ),
    10 =>
    array(
      'oxid' => 'b2ebd8e8efe82f353da3c61a13643',
      'oxprice' => 418.13,
      'oxvat' => 15,
      'amount' => 28,
    ),
    11 =>
    array(
      'oxid' => 'f737615e83fa3825bf10cd124ff51',
      'oxprice' => 69.94,
      'oxvat' => 19,
      'amount' => 281,
    ),
    12 =>
    array(
      'oxid' => '889b3096092a9f6aa66acfe7d7ba7',
      'oxprice' => 286.02,
      'oxvat' => 0,
      'amount' => 300,
    ),
    13 =>
    array(
      'oxid' => 'd988dc20da2439bd3c94f2ceb8827',
      'oxprice' => 302.77,
      'oxvat' => 15,
      'amount' => 672,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 39,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'bb2426c11c2467b94a84861e49d24',
          1 => 'f4e94902c63465def6c03a0468cfe',
          2 => 'f4dd99d98368a0d1a614534604190',
          3 => 'bdc685948245a91bb3ff6c00057eb',
          4 => '2871ccd6d4f3c7cae0c14657fba44',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 74,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'bb2426c11c2467b94a84861e49d24',
          1 => 'f4e94902c63465def6c03a0468cfe',
          2 => 'f4dd99d98368a0d1a614534604190',
          3 => 'bdc685948245a91bb3ff6c00057eb',
          4 => '2871ccd6d4f3c7cae0c14657fba44',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 28,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 59,
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
        'oxaddsum' => 75,
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
        'oxdiscount' => 18,
        'oxdiscounttype' => 'percent',
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
      'bb2426c11c2467b94a84861e49d24' =>
      array(
        0 => '1.000,14',
        1 => '582.081,48',
      ),
      'f4e94902c63465def6c03a0468cfe' =>
      array(
        0 => '159,62',
        1 => '110.776,28',
      ),
      'f4dd99d98368a0d1a614534604190' =>
      array(
        0 => '309,70',
        1 => '307.841,80',
      ),
      'bdc685948245a91bb3ff6c00057eb' =>
      array(
        0 => '3,71',
        1 => '2.960,58',
      ),
      '2871ccd6d4f3c7cae0c14657fba44' =>
      array(
        0 => '482,52',
        1 => '337.764,00',
      ),
      '1e1acce50dab351c942e9e9f04ba2' =>
      array(
        0 => '966,52',
        1 => '559.615,08',
      ),
      'adfaa61ba75260dd5b6b75076e251' =>
      array(
        0 => '84,65',
        1 => '12.358,90',
      ),
      '6c05c76caf67dd3357a4a030c5ddc' =>
      array(
        0 => '776,43',
        1 => '723.632,76',
      ),
      'a814f6f550a0095e082217435a364' =>
      array(
        0 => '441,15',
        1 => '73.672,05',
      ),
      'c7bd1800b966e6ab9a3cfa10ecaa8' =>
      array(
        0 => '248,03',
        1 => '110.621,38',
      ),
      'b2ebd8e8efe82f353da3c61a13643' =>
      array(
        0 => '418,13',
        1 => '11.707,64',
      ),
      'f737615e83fa3825bf10cd124ff51' =>
      array(
        0 => '69,94',
        1 => '19.653,14',
      ),
      '889b3096092a9f6aa66acfe7d7ba7' =>
      array(
        0 => '286,02',
        1 => '85.806,00',
      ),
      'd988dc20da2439bd3c94f2ceb8827' =>
      array(
        0 => '302,77',
        1 => '203.461,44',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        19 => '102.458,45',
        0 => '0,00',
        15 => '21.661,57',
      ),
      'wrapping' =>
      array(
        'brutto' => '278.832,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '2.356.488,40',
        'netto' => '2.356.488,40',
        'vat' => false,
      ),
      'payment' =>
      array(
        'brutto' => '28,00',
        'netto' => '28,00',
        'vat' => false,
      ),
      'voucher' =>
      array(
        'brutto' => '1.409.580,45',
      ),
      'totalNetto' => '1.608.252,06',
      'totalBrutto' => '3.141.952,53',
      'grandTotal' => '4.367.720,48',
    ),
  ),
);
