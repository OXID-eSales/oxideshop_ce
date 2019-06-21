<?php$aData = array(
  'user' =>
  array(
    'oxactive' => 1,
    'oxusername' => 'databomb_user_21',
  ),
  'articles' =>
  array(
    0 =>
    array(
      'oxid' => '1507180dd4dac8612be93799f08c5',
      'oxprice' => 887.92,
      'oxvat' => 7,
      'amount' => 759,
    ),
    1 =>
    array(
      'oxid' => 'e4d1dd616ce526a7955f9ad02d9e3',
      'oxprice' => 640.65,
      'oxvat' => 7,
      'amount' => 492,
    ),
    2 =>
    array(
      'oxid' => '243dcd596c63b8dd4c8a90cbf7c7d',
      'oxprice' => 480.42,
      'oxvat' => 25,
      'amount' => 266,
    ),
    3 =>
    array(
      'oxid' => '1879e7300a421f890599bc121cb94',
      'oxprice' => 377.53,
      'oxvat' => 25,
      'amount' => 147,
    ),
    4 =>
    array(
      'oxid' => '6fccf2f848615e1c4581f921992c5',
      'oxprice' => 511.14,
      'oxvat' => 21,
      'amount' => 250,
    ),
    5 =>
    array(
      'oxid' => '8d1e6a3ec1e00898665a891568f4f',
      'oxprice' => 223.39,
      'oxvat' => 7,
      'amount' => 408,
    ),
    6 =>
    array(
      'oxid' => 'dd77fb3af80285a13be22a9ed65e4',
      'oxprice' => 467,
      'oxvat' => 25,
      'amount' => 677,
    ),
  ),
  'costs' =>
  array(
    'wrapping' =>
    array(
      0 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 26,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '1507180dd4dac8612be93799f08c5',
          1 => 'e4d1dd616ce526a7955f9ad02d9e3',
          2 => '243dcd596c63b8dd4c8a90cbf7c7d',
          3 => '1879e7300a421f890599bc121cb94',
          4 => '6fccf2f848615e1c4581f921992c5',
        ),
      ),
      1 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 52,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '1507180dd4dac8612be93799f08c5',
          1 => 'e4d1dd616ce526a7955f9ad02d9e3',
          2 => '243dcd596c63b8dd4c8a90cbf7c7d',
          3 => '1879e7300a421f890599bc121cb94',
          4 => '6fccf2f848615e1c4581f921992c5',
        ),
      ),
      2 =>
      array(
        'oxtype' => 'WRAP',
        'oxprice' => 73,
        'oxactive' => 1,
        'oxarticles' =>
        array(
          0 => '1507180dd4dac8612be93799f08c5',
          1 => 'e4d1dd616ce526a7955f9ad02d9e3',
          2 => '243dcd596c63b8dd4c8a90cbf7c7d',
          3 => '1879e7300a421f890599bc121cb94',
          4 => '6fccf2f848615e1c4581f921992c5',
        ),
      ),
    ),
    'payment' =>
    array(
      0 =>
      array(
        'oxaddsumtype' => '%',
        'oxaddsum' => 69,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      1 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 4,
        'oxactive' => 1,
        'oxchecked' => 1,
        'oxfromamount' => 0,
        'oxtoamount' => 1000000,
      ),
      2 =>
      array(
        'oxaddsumtype' => 'abs',
        'oxaddsum' => 61,
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
        'oxaddsum' => 33,
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
        'oxaddsum' => 54,
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
        'oxaddsum' => 63,
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
      '1507180dd4dac8612be93799f08c5' =>
      array(
        0 => '950,07',
        1 => '721.103,13',
      ),
      'e4d1dd616ce526a7955f9ad02d9e3' =>
      array(
        0 => '685,50',
        1 => '337.266,00',
      ),
      '243dcd596c63b8dd4c8a90cbf7c7d' =>
      array(
        0 => '600,53',
        1 => '159.740,98',
      ),
      '1879e7300a421f890599bc121cb94' =>
      array(
        0 => '471,91',
        1 => '69.370,77',
      ),
      '6fccf2f848615e1c4581f921992c5' =>
      array(
        0 => '618,48',
        1 => '154.620,00',
      ),
      '8d1e6a3ec1e00898665a891568f4f' =>
      array(
        0 => '239,03',
        1 => '97.524,24',
      ),
      'dd77fb3af80285a13be22a9ed65e4' =>
      array(
        0 => '583,75',
        1 => '395.198,75',
      ),
    ),
    'totals' =>
    array(
      'vats' =>
      array(
        7 => '75.619,19',
        25 => '124.862,10',
        21 => '26.834,88',
      ),
      'wrapping' =>
      array(
        'brutto' => '139.722,00',
        'netto' => false,
        'vat' => false,
      ),
      'delivery' =>
      array(
        'brutto' => '1.044.900,89',
        'netto' => '976.542,89',
        'vat' => '68.358,00',
      ),
      'payment' =>
      array(
        'brutto' => '2.056.010,08',
        'netto' => '1.921.504,75',
        'vat' => '134.505,33',
      ),
      'voucher' =>
      array(
        'brutto' => '0,00',
      ),
      'totalNetto' => '1.707.507,70',
      'totalBrutto' => '1.934.823,87',
      'grandTotal' => '5.175.456,84',
    ),
  ),
);
