<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'nb_vbt_databomb_user_29',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '6d24a9225f33072467cc226a92a97',
      'oxprice' => 698.04,
      'oxvat' => 26,
      'amount' => 999,
    ),
    1 =>
    array(
      'oxid' => '8cdd518ae632f63219dce7b1eb5ad',
      'oxprice' => 931.6,
      'oxvat' => 2,
      'amount' => 609,
    ),
    2 =>
    array(
      'oxid' => '3e15b3444111ea8e0202318649e2d',
      'oxprice' => 651.53,
      'oxvat' => 2,
      'amount' => 932,
    ),
    3 =>
    array(
      'oxid' => 'a5bcc7b26494e0442551b17c9dc74',
      'oxprice' => 87.82,
      'oxvat' => 26,
      'amount' => 785,
    ),
    4 =>
    array(
      'oxid' => '755e89b8a7f0b11207285e05ca914',
      'oxprice' => 988.37,
      'oxvat' => 2,
      'amount' => 956,
    ),
    5 =>
    array(
      'oxid' => '50bc1bdbc6e8fe096b5a066e58990',
      'oxprice' => 742.08,
      'oxvat' => 37,
      'amount' => 869,
    ),
    6 =>
    array(
      'oxid' => 'e5d1e33204f958031f0efabc6a938',
      'oxprice' => 328.16,
      'oxvat' => 2,
      'amount' => 272,
    ),
    7 =>
    array(
      'oxid' => 'b29597a4d4726e29201076efe5317',
      'oxprice' => 828.07,
      'oxvat' => 26,
      'amount' => 682,
    ),
    8 =>
    array(
      'oxid' => 'b30c605ea90594cafdfb4c2336a98',
      'oxprice' => 320.21,
      'oxvat' => 37,
      'amount' => 694,
    ),
    9 =>
    array(
      'oxid' => '9f764be862cbf322c30d5181e051c',
      'oxprice' => 689.27,
      'oxvat' => 26,
      'amount' => 280,
    ),
    10 =>
    array(
      'oxid' => 'b6a3d6f052b42d7dbd66cd4f2c137',
      'oxprice' => 967.98,
      'oxvat' => 2,
      'amount' => 542,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 3,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '6d24a9225f33072467cc226a92a97',
          1 => '8cdd518ae632f63219dce7b1eb5ad',
          2 => '3e15b3444111ea8e0202318649e2d',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 88,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '6d24a9225f33072467cc226a92a97',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 5,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 22,
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
        'oxaddsum' => 15,
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
        'oxdiscount' => 15,
        'oxdiscounttype' => 'absolute',
        'oxallowsameseries' => 1,
        'oxallowotherseries' => 1,
        'oxallowuseanother' => 1,
        'voucher_count' => 2,
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
      '6d24a9225f33072467cc226a92a97' =>
      array(
        0 => '879,53',
        1 => '878.650,47',
      ),
      '8cdd518ae632f63219dce7b1eb5ad' =>
      array(
        0 => '950,23',
        1 => '578.690,07',
      ),
      '3e15b3444111ea8e0202318649e2d' =>
      array(
        0 => '664,56',
        1 => '619.369,92',
      ),
      'a5bcc7b26494e0442551b17c9dc74' =>
      array(
        0 => '110,65',
        1 => '86.860,25',
      ),
      '755e89b8a7f0b11207285e05ca914' =>
      array(
        0 => '1.008,14',
        1 => '963.781,84',
      ),
      '50bc1bdbc6e8fe096b5a066e58990' =>
      array(
        0 => '1.016,65',
        1 => '883.468,85',
      ),
      'e5d1e33204f958031f0efabc6a938' =>
      array(
        0 => '334,72',
        1 => '91.043,84',
      ),
      'b29597a4d4726e29201076efe5317' =>
      array(
        0 => '1.043,37',
        1 => '711.578,34',
      ),
      'b30c605ea90594cafdfb4c2336a98' =>
      array(
        0 => '438,69',
        1 => '304.450,86',
      ),
      '9f764be862cbf322c30d5181e051c' =>
      array(
        0 => '868,48',
        1 => '243.174,40',
      ),
      'b6a3d6f052b42d7dbd66cd4f2c137' =>
      array(
        0 => '987,34',
        1 => '535.138,28',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        26 => '396.242,82',
        2 => '54.666,86',
        37 => '320.823,40',
      ),
      'wrapping' =>
      array(
        'brutto' => '92.535,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '1.415.104,71',
        'netto' => '1.387.357,56',
        'vat' => '27.747,15',
      ),
      'payment' =>
      array(
        'brutto' => '5,00',
        'netto' => '4,90',
        'vat' => '0,10',
      ),
      'voucher' =>
      array(
        'brutto' => '30,00',
      ),
      'totalNetto' => '5.124.444,04',
      'totalBrutto' => '5.896.207,12',
      'grandTotal' => '7.403.821,83',
    ),
  ),
);
