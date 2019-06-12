<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_6',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '6760de02aa356bb4398267716c6e9',
      'oxprice' => 719.24,
      'oxvat' => 19,
      'amount' => 987,
    ),
    1 =>
    array(
      'oxid' => '0d34c1348dc410946f51107052c65',
      'oxprice' => 827.52,
      'oxvat' => 19,
      'amount' => 939,
    ),
    2 =>
    array(
      'oxid' => 'e1b0f2bb8e99a514841bf94112cc3',
      'oxprice' => 322.78,
      'oxvat' => 19,
      'amount' => 510,
    ),
    3 =>
    array(
      'oxid' => 'd8f4b2ee7bed49a19774e4a392301',
      'oxprice' => 588.89,
      'oxvat' => 19,
      'amount' => 370,
    ),
    4 =>
    array(
      'oxid' => '22e19fda98880c581d1edbef97546',
      'oxprice' => 40.08,
      'oxvat' => 19,
      'amount' => 626,
    ),
    5 =>
    array(
      'oxid' => '368ffb3b0f194e5e531807de13246',
      'oxprice' => 475.44,
      'oxvat' => 19,
      'amount' => 586,
    ),
    6 =>
    array(
      'oxid' => '84922b5ef044f719a6041389f651a',
      'oxprice' => 286.22,
      'oxvat' => 19,
      'amount' => 929,
    ),
    7 =>
    array(
      'oxid' => 'e2926522b4404049e80849fd30e4a',
      'oxprice' => 739.14,
      'oxvat' => 19,
      'amount' => 600,
    ),
    8 =>
    array(
      'oxid' => 'be7ac1ff2727b0eca4f22a4e7324b',
      'oxprice' => 50.92,
      'oxvat' => 19,
      'amount' => 705,
    ),
    9 =>
    array(
      'oxid' => 'dc338657c247c79e4a7c791f20dfa',
      'oxprice' => 792.85,
      'oxvat' => 19,
      'amount' => 414,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 69,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '6760de02aa356bb4398267716c6e9',
          1 => '0d34c1348dc410946f51107052c65',
          2 => 'e1b0f2bb8e99a514841bf94112cc3',
          3 => 'd8f4b2ee7bed49a19774e4a392301',
          4 => '22e19fda98880c581d1edbef97546',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 44,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '6760de02aa356bb4398267716c6e9',
          1 => '0d34c1348dc410946f51107052c65',
          2 => 'e1b0f2bb8e99a514841bf94112cc3',
          3 => 'd8f4b2ee7bed49a19774e4a392301',
          4 => '22e19fda98880c581d1edbef97546',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 2,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 29,
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
        'oxaddsum' => 35,
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
        'oxaddsum' => 13,
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
      '6760de02aa356bb4398267716c6e9' =>
      array(
        0 => '719,24',
        1 => '709.889,88',
      ),
      '0d34c1348dc410946f51107052c65' =>
      array(
        0 => '827,52',
        1 => '777.041,28',
      ),
      'e1b0f2bb8e99a514841bf94112cc3' =>
      array(
        0 => '322,78',
        1 => '164.617,80',
      ),
      'd8f4b2ee7bed49a19774e4a392301' =>
      array(
        0 => '588,89',
        1 => '217.889,30',
      ),
      '22e19fda98880c581d1edbef97546' =>
      array(
        0 => '40,08',
        1 => '25.090,08',
      ),
      '368ffb3b0f194e5e531807de13246' =>
      array(
        0 => '475,44',
        1 => '278.607,84',
      ),
      '84922b5ef044f719a6041389f651a' =>
      array(
        0 => '286,22',
        1 => '265.898,38',
      ),
      'e2926522b4404049e80849fd30e4a' =>
      array(
        0 => '739,14',
        1 => '443.484,00',
      ),
      'be7ac1ff2727b0eca4f22a4e7324b' =>
      array(
        0 => '50,92',
        1 => '35.898,60',
      ),
      'dc338657c247c79e4a7c791f20dfa' =>
      array(
        0 => '792,85',
        1 => '328.239,90',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        19 => '285.814,73',
      ),
      'wrapping' =>
      array(
        'brutto' => '151.008,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '1.136.342,97',
        'netto' => '954.910,06',
        'vat' => '181.432,91',
      ),
      'payment' =>
      array(
        'brutto' => '2,00',
        'netto' => '1,68',
        'vat' => '0,32',
      ),
      'voucher' =>
      array(
        'brutto' => '1.456.554,25',
      ),
      'totalNetto' => '1.504.288,08',
      'totalBrutto' => '3.246.657,06',
      'grandTotal' => '3.077.455,78',
    ),
  ),
);
