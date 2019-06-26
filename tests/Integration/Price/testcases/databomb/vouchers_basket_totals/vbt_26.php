<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_26',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '8979f9d91fc21341ae6c4b04f40f9',
      'oxprice' => 125.62,
      'oxvat' => 35,
      'amount' => 301,
    ),
    1 =>
    array(
      'oxid' => '24a2e33648e6a23977f8ee5178517',
      'oxprice' => 171.12,
      'oxvat' => 3,
      'amount' => 909,
    ),
    2 =>
    array(
      'oxid' => '142cf2a9470e3dc54831ae8f19476',
      'oxprice' => 954.01,
      'oxvat' => 35,
      'amount' => 165,
    ),
    3 =>
    array(
      'oxid' => 'dc2f522e4c642e596750c1b525bf3',
      'oxprice' => 636.37,
      'oxvat' => 35,
      'amount' => 835,
    ),
    4 =>
    array(
      'oxid' => '341fe91ae1a6d52028407f25cd60b',
      'oxprice' => 835.97,
      'oxvat' => 3,
      'amount' => 745,
    ),
    5 =>
    array(
      'oxid' => '889fe39cd23794f05018ae2cfc304',
      'oxprice' => 207.65,
      'oxvat' => 3,
      'amount' => 197,
    ),
    6 =>
    array(
      'oxid' => 'ddb2c8940879288a2c33fe5e16d1f',
      'oxprice' => 662.47,
      'oxvat' => 3,
      'amount' => 749,
    ),
    7 =>
    array(
      'oxid' => 'bbf25dcf516a17c0a268f07531658',
      'oxprice' => 85.62,
      'oxvat' => 35,
      'amount' => 950,
    ),
    8 =>
    array(
      'oxid' => '173889077f60d4a9bdbb2f23f04ea',
      'oxprice' => 52.05,
      'oxvat' => 3,
      'amount' => 810,
    ),
    9 =>
    array(
      'oxid' => 'b97d79accd3caccd66958e96b3a5e',
      'oxprice' => 358.37,
      'oxvat' => 35,
      'amount' => 946,
    ),
    10 =>
    array(
      'oxid' => '4acb9cf9bc30f8bdc0f2e533565a4',
      'oxprice' => 305.71,
      'oxvat' => 3,
      'amount' => 362,
    ),
    11 =>
    array(
      'oxid' => 'a59f9fbe39b69c137b1e53b35b202',
      'oxprice' => 328.25,
      'oxvat' => 3,
      'amount' => 452,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 71,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8979f9d91fc21341ae6c4b04f40f9',
          1 => '24a2e33648e6a23977f8ee5178517',
          2 => '142cf2a9470e3dc54831ae8f19476',
          3 => 'dc2f522e4c642e596750c1b525bf3',
          4 => '341fe91ae1a6d52028407f25cd60b',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 42,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '8979f9d91fc21341ae6c4b04f40f9',
          1 => '24a2e33648e6a23977f8ee5178517',
          2 => '142cf2a9470e3dc54831ae8f19476',
          3 => 'dc2f522e4c642e596750c1b525bf3',
          4 => '341fe91ae1a6d52028407f25cd60b',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 20,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 93,
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
        'oxaddsum' => 47,
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
        'oxaddsum' => 58,
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
        'oxdiscount' => 19,
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
      '8979f9d91fc21341ae6c4b04f40f9' =>
      array(
        0 => '125,62',
        1 => '37.811,62',
      ),
      '24a2e33648e6a23977f8ee5178517' =>
      array(
        0 => '171,12',
        1 => '155.548,08',
      ),
      '142cf2a9470e3dc54831ae8f19476' =>
      array(
        0 => '954,01',
        1 => '157.411,65',
      ),
      'dc2f522e4c642e596750c1b525bf3' =>
      array(
        0 => '636,37',
        1 => '531.368,95',
      ),
      '341fe91ae1a6d52028407f25cd60b' =>
      array(
        0 => '835,97',
        1 => '622.797,65',
      ),
      '889fe39cd23794f05018ae2cfc304' =>
      array(
        0 => '207,65',
        1 => '40.907,05',
      ),
      'ddb2c8940879288a2c33fe5e16d1f' =>
      array(
        0 => '662,47',
        1 => '496.190,03',
      ),
      'bbf25dcf516a17c0a268f07531658' =>
      array(
        0 => '85,62',
        1 => '81.339,00',
      ),
      '173889077f60d4a9bdbb2f23f04ea' =>
      array(
        0 => '52,05',
        1 => '42.160,50',
      ),
      'b97d79accd3caccd66958e96b3a5e' =>
      array(
        0 => '358,37',
        1 => '339.018,02',
      ),
      '4acb9cf9bc30f8bdc0f2e533565a4' =>
      array(
        0 => '305,71',
        1 => '110.667,02',
      ),
      'a59f9fbe39b69c137b1e53b35b202' =>
      array(
        0 => '328,25',
        1 => '148.369,00',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        35 => '297.351,08',
        3 => '47.085,61',
      ),
      'wrapping' =>
      array(
        'brutto' => '124.110,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '1.602.928,37',
        'netto' => '1.556.241,14',
        'vat' => '46.687,23',
      ),
      'payment' =>
      array(
        'brutto' => '873.291,99',
        'netto' => '847.856,30',
        'vat' => '25.435,69',
      ),
      'voucher' =>
      array(
        'brutto' => '57,00',
      ),
      'totalNetto' => '2.419.094,88',
      'totalBrutto' => '2.763.588,57',
      'grandTotal' => '5.363.861,93',
    ),
  ),
);
