<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_5',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => 'a8fcd1bf02c6aab38539e6a0f138f',
      'oxprice' => 39.83,
      'oxvat' => 11,
      'amount' => 91,
    ),
    1 =>
    array(
      'oxid' => '287d40669ce4bf5bbdeaa776d2750',
      'oxprice' => 802.25,
      'oxvat' => 11,
      'amount' => 193,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 56,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'a8fcd1bf02c6aab38539e6a0f138f',
          1 => '287d40669ce4bf5bbdeaa776d2750',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 22,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'a8fcd1bf02c6aab38539e6a0f138f',
          1 => '287d40669ce4bf5bbdeaa776d2750',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 24,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => 'a8fcd1bf02c6aab38539e6a0f138f',
          1 => '287d40669ce4bf5bbdeaa776d2750',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 27,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 53,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 34,
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
        'oxaddsum' => 54,
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
        'oxaddsum' => 92,
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
        'oxaddsum' => 24,
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
      'a8fcd1bf02c6aab38539e6a0f138f' =>
      array(
        0 => '39,83',
        1 => '3.624,53',
      ),
      '287d40669ce4bf5bbdeaa776d2750' =>
      array(
        0 => '802,25',
        1 => '154.834,25',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        11 => '15.703,12',
      ),
      'wrapping' =>
      array(
        'brutto' => '6.816,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '85.683,74',
        'netto' => '77.192,56',
        'vat' => '8.491,18',
      ),
      'payment' =>
      array(
        'brutto' => '27,00',
        'netto' => '24,32',
        'vat' => '2,68',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '142.755,66',
      'totalBrutto' => '158.458,78',
      'grandTotal' => '250.985,52',
    ),
  ),
);
