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
      'oxid' => '4a1abbd9a4dc5b4e15d291a2e7c67',
      'oxprice' => 890.68,
      'oxvat' => 40,
      'amount' => 202,
    ),
    1 =>
    array(
      'oxid' => 'c1d26feaab22553070d82101bd614',
      'oxprice' => 84.96,
      'oxvat' => 32,
      'amount' => 634,
    ),
    2 =>
    array(
      'oxid' => '30e84f9ebbcccf181b37cfb34b726',
      'oxprice' => 182.52,
      'oxvat' => 40,
      'amount' => 387,
    ),
  ),
  'discounts' =>
  array(
    0 =>
    array(
      'oxaddsum' => 1,
      'oxid' => 'bombDiscount_0',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    1 =>
    array(
      'oxaddsum' => 6,
      'oxid' => 'bombDiscount_1',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    2 =>
    array(
      'oxaddsum' => 7,
      'oxid' => 'bombDiscount_2',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    3 =>
    array(
      'oxaddsum' => 11,
      'oxid' => 'bombDiscount_3',
      'oxaddsumtype' => '%',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
    4 =>
    array(
      'oxaddsum' => 5,
      'oxid' => 'bombDiscount_4',
      'oxaddsumtype' => 'abs',
      'oxamount' => 1,
      'oxamountto' => 9999999,
      'oxprice' => 1,
      'oxpriceto' => 9999999,
      'oxactive' => 1,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 84,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '4a1abbd9a4dc5b4e15d291a2e7c67',
          1 => 'c1d26feaab22553070d82101bd614',
          2 => '30e84f9ebbcccf181b37cfb34b726',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 50,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '4a1abbd9a4dc5b4e15d291a2e7c67',
          1 => 'c1d26feaab22553070d82101bd614',
          2 => '30e84f9ebbcccf181b37cfb34b726',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 69,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '4a1abbd9a4dc5b4e15d291a2e7c67',
          1 => 'c1d26feaab22553070d82101bd614',
          2 => '30e84f9ebbcccf181b37cfb34b726',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 5,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 47,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 38,
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
        'oxaddsum' => 9,
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
        'oxaddsum' => 18,
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
        'oxaddsum' => 47,
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
      '4a1abbd9a4dc5b4e15d291a2e7c67' =>
      array(
        0 => '890,68',
        1 => '179.917,36',
      ),
      'c1d26feaab22553070d82101bd614' =>
      array(
        0 => '84,96',
        1 => '53.864,64',
      ),
      '30e84f9ebbcccf181b37cfb34b726' =>
      array(
        0 => '182,52',
        1 => '70.635,24',
      ),
    ),
    'totals' =>
    array(
      'discounts' =>
      array(
        'bombDiscount_0' => '1,00',
        'bombDiscount_1' => '6,00',
        'bombDiscount_2' => '7,00',
        'bombDiscount_3' => '33.484,36',
        'bombDiscount_4' => '5,00',
      ),
      'vats' =>
      array(
        40 => '63.707,84',
        32 => '11.620,96',
      ),
      'wrapping' =>
      array(
        'brutto' => '84.387,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '74,00',
        'netto' => '52,86',
        'vat' => '21,14',
      ),
      'payment' =>
      array(
        'brutto' => '13.549,39',
        'netto' => '9.678,14',
        'vat' => '3.871,25',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '195.585,08',
      'totalBrutto' => '304.417,24',
      'grandTotal' => '368.924,27',
    ),
  ),
);
