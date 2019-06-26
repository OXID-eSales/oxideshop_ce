<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_9',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '1aaa8bcbee7156fc3678243560608',
      'oxprice' => 68.46,
      'oxvat' => 31,
      'amount' => 132,
    ),
    1 =>
    array(
      'oxid' => '7ea0b5a9b79ad4490d655e49467b5',
      'oxprice' => 557.33,
      'oxvat' => 22,
      'amount' => 925,
    ),
    2 =>
    array(
      'oxid' => '82ac99f7eaf632b8abd3c04cebdb4',
      'oxprice' => 575.64,
      'oxvat' => 31,
      'amount' => 475,
    ),
    3 =>
    array(
      'oxid' => '85ed833ed92b43dd3ea3f9f13bd82',
      'oxprice' => 462.94,
      'oxvat' => 31,
      'amount' => 766,
    ),
    4 =>
    array(
      'oxid' => '52f7640fb4cf51175971ff0b3b9d7',
      'oxprice' => 520.43,
      'oxvat' => 31,
      'amount' => 205,
    ),
    5 =>
    array(
      'oxid' => '4b7c918db5bc44d914ee6dc9b7904',
      'oxprice' => 693.34,
      'oxvat' => 22,
      'amount' => 543,
    ),
    6 =>
    array(
      'oxid' => 'd956adc6eb94534cf5c74f5dd83fd',
      'oxprice' => 473.64,
      'oxvat' => 22,
      'amount' => 286,
    ),
    7 =>
    array(
      'oxid' => '2217bc08deebe9ebcfc7565c483fa',
      'oxprice' => 733.17,
      'oxvat' => 22,
      'amount' => 882,
    ),
    8 =>
    array(
      'oxid' => 'cb6996ee154191d7b223e419adc92',
      'oxprice' => 450.87,
      'oxvat' => 31,
      'amount' => 336,
    ),
    9 =>
    array(
      'oxid' => 'bb379a602596372909acd0ef5824c',
      'oxprice' => 270.38,
      'oxvat' => 22,
      'amount' => 929,
    ),
    10 =>
    array(
      'oxid' => '73ee076c830d4c14a2a5c7f8abd0b',
      'oxprice' => 648.2,
      'oxvat' => 31,
      'amount' => 735,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 53,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '1aaa8bcbee7156fc3678243560608',
          1 => '7ea0b5a9b79ad4490d655e49467b5',
          2 => '82ac99f7eaf632b8abd3c04cebdb4',
          3 => '85ed833ed92b43dd3ea3f9f13bd82',
          4 => '52f7640fb4cf51175971ff0b3b9d7',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 76,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '1aaa8bcbee7156fc3678243560608',
          1 => '7ea0b5a9b79ad4490d655e49467b5',
          2 => '82ac99f7eaf632b8abd3c04cebdb4',
          3 => '85ed833ed92b43dd3ea3f9f13bd82',
          4 => '52f7640fb4cf51175971ff0b3b9d7',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 36,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 31,
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
        'oxaddsum' => 98,
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
        'oxaddsum' => 5,
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
        'oxdiscount' => 28,
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
      '1aaa8bcbee7156fc3678243560608' =>
      array(
        0 => '68,46',
        1 => '9.036,72',
      ),
      '7ea0b5a9b79ad4490d655e49467b5' =>
      array(
        0 => '557,33',
        1 => '515.530,25',
      ),
      '82ac99f7eaf632b8abd3c04cebdb4' =>
      array(
        0 => '575,64',
        1 => '273.429,00',
      ),
      '85ed833ed92b43dd3ea3f9f13bd82' =>
      array(
        0 => '462,94',
        1 => '354.612,04',
      ),
      '52f7640fb4cf51175971ff0b3b9d7' =>
      array(
        0 => '520,43',
        1 => '106.688,15',
      ),
      '4b7c918db5bc44d914ee6dc9b7904' =>
      array(
        0 => '693,34',
        1 => '376.483,62',
      ),
      'd956adc6eb94534cf5c74f5dd83fd' =>
      array(
        0 => '473,64',
        1 => '135.461,04',
      ),
      '2217bc08deebe9ebcfc7565c483fa' =>
      array(
        0 => '733,17',
        1 => '646.655,94',
      ),
      'cb6996ee154191d7b223e419adc92' =>
      array(
        0 => '450,87',
        1 => '151.492,32',
      ),
      'bb379a602596372909acd0ef5824c' =>
      array(
        0 => '270,38',
        1 => '251.183,02',
      ),
      '73ee076c830d4c14a2a5c7f8abd0b' =>
      array(
        0 => '648,20',
        1 => '476.427,00',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        31 => '324.589,00',
        22 => '347.178,90',
      ),
      'wrapping' =>
      array(
        'brutto' => '190.228,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '103,00',
        'netto' => '84,43',
        'vat' => '18,57',
      ),
      'payment' =>
      array(
        'brutto' => '1.186.926,52',
        'netto' => '972.890,59',
        'vat' => '214.035,93',
      ),
      'voucher' =>
      array(
        'brutto' => '84,00',
      ),
      'totalNetto' => '2.625.147,20',
      'totalBrutto' => '3.296.999,10',
      'grandTotal' => '4.674.172,62',
    ),
  ),
);
