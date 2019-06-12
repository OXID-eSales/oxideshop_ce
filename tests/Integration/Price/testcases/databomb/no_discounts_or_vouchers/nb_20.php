<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_20',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '136f723643dd899ba17d1feb9e818',
      'oxprice' => 361.65,
      'oxvat' => 19,
      'amount' => 887,
    ),
    1 =>
    array(
      'oxid' => '87bacb8051e38f09c1eb688c84e85',
      'oxprice' => 663.89,
      'oxvat' => 14,
      'amount' => 590,
    ),
    2 =>
    array(
      'oxid' => '65cec2c353e5c204f9ba5dc8cb26f',
      'oxprice' => 146.05,
      'oxvat' => 19,
      'amount' => 904,
    ),
    3 =>
    array(
      'oxid' => '108eb0a161e79b422973b399777e9',
      'oxprice' => 240.27,
      'oxvat' => 14,
      'amount' => 510,
    ),
    4 =>
    array(
      'oxid' => '321a23bac152e82637d74adcf49dd',
      'oxprice' => 986.96,
      'oxvat' => 19,
      'amount' => 301,
    ),
    5 =>
    array(
      'oxid' => '0975f92a6655d6c5f90b9b21fcf0f',
      'oxprice' => 736.32,
      'oxvat' => 19,
      'amount' => 622,
    ),
    6 =>
    array(
      'oxid' => 'baba24bdf4a031c0218f937d83b8c',
      'oxprice' => 165.44,
      'oxvat' => 19,
      'amount' => 488,
    ),
    7 =>
    array(
      'oxid' => '8ba19ff4b537cf9d9af9bc2a65842',
      'oxprice' => 741.75,
      'oxvat' => 38,
      'amount' => 127,
    ),
    8 =>
    array(
      'oxid' => 'db44f4cfaacc6b8e97d66a301e278',
      'oxprice' => 440.91,
      'oxvat' => 38,
      'amount' => 807,
    ),
    9 =>
    array(
      'oxid' => '500746f5794a0aeb88e1580817eb1',
      'oxprice' => 324.11,
      'oxvat' => 38,
      'amount' => 704,
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
          0 => '136f723643dd899ba17d1feb9e818',
          1 => '87bacb8051e38f09c1eb688c84e85',
          2 => '65cec2c353e5c204f9ba5dc8cb26f',
          3 => '108eb0a161e79b422973b399777e9',
          4 => '321a23bac152e82637d74adcf49dd',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 27,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '136f723643dd899ba17d1feb9e818',
          1 => '87bacb8051e38f09c1eb688c84e85',
          2 => '65cec2c353e5c204f9ba5dc8cb26f',
          3 => '108eb0a161e79b422973b399777e9',
          4 => '321a23bac152e82637d74adcf49dd',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 30,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '136f723643dd899ba17d1feb9e818',
          1 => '87bacb8051e38f09c1eb688c84e85',
          2 => '65cec2c353e5c204f9ba5dc8cb26f',
          3 => '108eb0a161e79b422973b399777e9',
          4 => '321a23bac152e82637d74adcf49dd',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 68,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 30,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 99,
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
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 10,
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
        'oxaddsum' => 66,
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
      '136f723643dd899ba17d1feb9e818' =>
      array(
        0 => '430,36',
        1 => '381.729,32',
      ),
      '87bacb8051e38f09c1eb688c84e85' =>
      array(
        0 => '756,83',
        1 => '446.529,70',
      ),
      '65cec2c353e5c204f9ba5dc8cb26f' =>
      array(
        0 => '173,80',
        1 => '157.115,20',
      ),
      '108eb0a161e79b422973b399777e9' =>
      array(
        0 => '273,91',
        1 => '139.694,10',
      ),
      '321a23bac152e82637d74adcf49dd' =>
      array(
        0 => '1.174,48',
        1 => '353.518,48',
      ),
      '0975f92a6655d6c5f90b9b21fcf0f' =>
      array(
        0 => '876,22',
        1 => '545.008,84',
      ),
      'baba24bdf4a031c0218f937d83b8c' =>
      array(
        0 => '196,87',
        1 => '96.072,56',
      ),
      '8ba19ff4b537cf9d9af9bc2a65842' =>
      array(
        0 => '1.023,62',
        1 => '129.999,74',
      ),
      'db44f4cfaacc6b8e97d66a301e278' =>
      array(
        0 => '608,46',
        1 => '491.027,22',
      ),
      '500746f5794a0aeb88e1580817eb1' =>
      array(
        0 => '447,27',
        1 => '314.878,08',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        19 => '244.835,66',
        14 => '71.992,40',
        38 => '257.712,98',
      ),
      'wrapping' =>
      array(
        'brutto' => '95.760,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '733.413,58',
        'netto' => '616.313,93',
        'vat' => '117.099,65',
      ),
      'payment' =>
      array(
        'brutto' => '2.576.511,04',
        'netto' => '2.165.135,33',
        'vat' => '411.375,71',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '2.481.032,20',
      'totalBrutto' => '3.055.573,24',
      'grandTotal' => '6.461.257,86',
    ),
  ),
);
