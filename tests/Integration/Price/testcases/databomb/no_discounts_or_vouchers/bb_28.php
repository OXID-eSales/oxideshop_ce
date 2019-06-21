<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_28',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '99dec338f61c74afd5ae6f239b2a0',
      'oxprice' => 157.75,
      'oxvat' => 31,
      'amount' => 529,
    ),
    1 =>
    array(
      'oxid' => 'dc62cd53e55e5ebf2e75fc08ee68a',
      'oxprice' => 853.1,
      'oxvat' => 22,
      'amount' => 359,
    ),
    2 =>
    array(
      'oxid' => '793753fb050623cc6ba3e3a16b360',
      'oxprice' => 769,
      'oxvat' => 31,
      'amount' => 832,
    ),
    3 =>
    array(
      'oxid' => '4b0d40d33d714df6314441433061b',
      'oxprice' => 146.47,
      'oxvat' => 31,
      'amount' => 402,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 46,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '99dec338f61c74afd5ae6f239b2a0',
          1 => 'dc62cd53e55e5ebf2e75fc08ee68a',
          2 => '793753fb050623cc6ba3e3a16b360',
          3 => '4b0d40d33d714df6314441433061b',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 10,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '99dec338f61c74afd5ae6f239b2a0',
          1 => 'dc62cd53e55e5ebf2e75fc08ee68a',
          2 => '793753fb050623cc6ba3e3a16b360',
          3 => '4b0d40d33d714df6314441433061b',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 97,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '99dec338f61c74afd5ae6f239b2a0',
          1 => 'dc62cd53e55e5ebf2e75fc08ee68a',
          2 => '793753fb050623cc6ba3e3a16b360',
          3 => '4b0d40d33d714df6314441433061b',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 85,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 21,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 55,
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
        'oxaddsum' => 80,
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
        'oxaddsum' => 65,
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
        'oxaddsum' => 93,
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
      '99dec338f61c74afd5ae6f239b2a0' =>
      array(
        0 => '157,75',
        1 => '83.449,75',
      ),
      'dc62cd53e55e5ebf2e75fc08ee68a' =>
      array(
        0 => '853,10',
        1 => '306.262,90',
      ),
      '793753fb050623cc6ba3e3a16b360' =>
      array(
        0 => '769,00',
        1 => '639.808,00',
      ),
      '4b0d40d33d714df6314441433061b' =>
      array(
        0 => '146,47',
        1 => '58.880,94',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        31 => '185.086,25',
        22 => '55.227,74',
      ),
      'wrapping' =>
      array(
        'brutto' => '205.834,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '2.590.395,78',
        'netto' => '1.977.401,36',
        'vat' => '612.994,42',
      ),
      'payment' =>
      array(
        'brutto' => '85,00',
        'netto' => '64,89',
        'vat' => '20,11',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '848.087,60',
      'totalBrutto' => '1.088.401,59',
      'grandTotal' => '3.884.716,37',
    ),
  ),
);
