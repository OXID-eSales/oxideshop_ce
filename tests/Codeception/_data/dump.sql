SET @@session.sql_mode = '';
# for frontendMultidimensionalVariantsOnDetailsPage
#Articles demodata
REPLACE INTO `oxarticles` (`OXID`,   `OXSHOPID`,   `OXPARENTID`, `OXACTIVE`, `OXARTNUM`, `OXTITLE`,                     `OXSHORTDESC`,                   `OXPRICE`, `OXPRICEA`, `OXPRICEB`, `OXPRICEC`, `OXTPRICE`, `OXUNITNAME`, `OXUNITQUANTITY`, `OXVAT`, `OXWEIGHT`, `OXSTOCK`, `OXSTOCKFLAG`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`,       `OXDELIVERY`, `OXINSERT`,   `OXTIMESTAMP`,        `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXSEARCHKEYS`, `OXISSEARCH`, `OXVARNAME`,              `OXVARSTOCK`, `OXVARCOUNT`, `OXVARSELECT`, `OXVARMINPRICE`, `OXVARMAXPRICE`, `OXVARNAME_1`,             `OXVARSELECT_1`,   `OXTITLE_1`,                 `OXSHORTDESC_1`,                        `OXSEARCHKEYS_1`, `OXBUNDLEID`, `OXSTOCKTEXT_1`,       `OXNOSTOCKTEXT_1`,         `OXSORT`, `OXVENDORID`,      `OXMANUFACTURERID`, `OXMINDELTIME`, `OXMAXDELTIME`, `OXDELTIMEUNIT`) VALUES
('10014',  1, '',            1,         '10014',    '13 DE product šÄßüл',         '14 DE description',              1.6,       0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            '',              '',                  '0000-00-00', '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              1,           'size[DE] | color | type', 0,            12,          '',             15,               25,                   'size[EN] | color | type', '',                '14 EN product šÄßüл',       '13 EN description šÄßüл',              '',               '',           '',                    '',                         0,       '',                '',                  0,              0,             '');

#demodata for multidimensional variants
REPLACE INTO `oxarticles` (`OXID`,    `OXSHOPID`,   `OXPARENTID`, `OXACTIVE`, `OXARTNUM`, `OXPRICE`, `OXSTOCK`, `OXSTOCKFLAG`, `OXINSERT`,   `OXTIMESTAMP`,         `OXVARSELECT`,               `OXVARSELECT_1`,        `OXSUBCLASS`, `OXSORT`) VALUES
('1001432', 1, '10014',       1,         '10014-3-2', 15,        3,         1,            '2008-04-03', '2008-04-03 12:50:20', 'M | black | material [DE]', 'L | black | material', 'oxarticle',   3002),
('1001424', 1, '10014',       1,         '10014-2-4', 15,        0,         1,            '2008-04-03', '2008-04-03 12:50:20', 'M | red [DE]',              'M | red',              'oxarticle',   2004),
('1001422', 1, '10014',       1,         '10014-2-2', 15,        0,         3,            '2008-04-03', '2008-04-03 12:50:20', 'M | black | material [DE]', 'M | black | material', 'oxarticle',   2002),
('1001421', 1, '10014',       1,         '10014-2-1', 25,        0,         2,            '2008-04-03', '2008-04-03 12:50:20', 'M | black | lether [DE]',   'M | black | lether',   'oxarticle',   2001),
('1001411', 1, '10014',       1,         '10014-1-1', 25,        3,         1,            '2008-04-03', '2008-04-03 12:50:20', 'S | black | lether [DE]',   'S | black | lether',   'oxarticle',   1001),
('1001413', 1, '10014',       1,         '10014-1-3', 15,        3,         1,            '2008-04-03', '2008-04-03 12:50:20', 'S | white [DE]',            'S | white',            'oxarticle',   1003),
('1001412', 1, '10014',       1,         '10014-1-2', 15,        3,         1,            '2008-04-03', '2008-04-03 12:50:20', 'S | black | material [DE]', 'S | black | material', 'oxarticle',   1002),
('1001434', 1, '10014',       0,         '10014-3-4', 15,        3,         1,            '2008-04-03', '2008-04-03 12:50:20', 'L | red [DE]',              'L | red',              'oxarticle',   3004),
('1001423', 1, '10014',       1,         '10014-2-3', 15,        0,         1,            '2008-04-03', '2008-04-03 12:50:20', 'M | white [DE]',            'M | white',            'oxarticle',   2003),
('1001414', 1, '10014',       1,         '10014-1-4', 15,        3,         1,            '2008-04-03', '2008-04-03 12:50:20', 'S | red [DE]',              'S | red',              'oxarticle',   1004),
('1001431', 1, '10014',       1,         '10014-3-1', 15,        3,         1,            '2008-04-03', '2008-04-03 12:50:20', 'L | black | lether [DE]',   'L | black | lether',   'oxarticle',   3001),
('1001433', 1, '10014',       1,         '10014-3-3', 15,        3,         1,            '2008-04-03', '2008-04-03 12:50:20', 'L | white [DE]',            'L | white',            'oxarticle',   3003);


#Articles long desc
REPLACE INTO `oxartextends` (`OXID`,   `OXLONGDESC`, `OXLONGDESC_1`) VALUES
('10014',  '',                                            '');

# for frontendMultidimensionalVariantsOnDetailsPage, createBasketUserAccountWithoutRegistration
#Articles demodata
REPLACE INTO `oxarticles` (`OXID`,   `OXSHOPID`,   `OXPARENTID`, `OXACTIVE`, `OXARTNUM`, `OXTITLE`,                     `OXSHORTDESC`,                   `OXPRICE`, `OXPRICEA`, `OXPRICEB`, `OXPRICEC`, `OXTPRICE`, `OXUNITNAME`, `OXUNITQUANTITY`, `OXVAT`, `OXWEIGHT`, `OXSTOCK`, `OXSTOCKFLAG`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`,       `OXDELIVERY`, `OXINSERT`,   `OXTIMESTAMP`,        `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXSEARCHKEYS`, `OXISSEARCH`, `OXVARNAME`,              `OXVARSTOCK`, `OXVARCOUNT`, `OXVARSELECT`, `OXVARMINPRICE`, `OXVARMAXPRICE`, `OXVARNAME_1`,             `OXVARSELECT_1`,   `OXTITLE_1`,                 `OXSHORTDESC_1`,                        `OXSEARCHKEYS_1`, `OXBUNDLEID`, `OXSTOCKTEXT_1`,       `OXNOSTOCKTEXT_1`,         `OXSORT`, `OXVENDORID`,      `OXMANUFACTURERID`, `OXMINDELTIME`, `OXMAXDELTIME`, `OXDELTIMEUNIT`) VALUES
                         ('1000',   1, '',            1,         '1000',     '[DE 4] Test product 0 šÄßüл', 'Test product 0 short desc [DE]', 50,        35,         45,         55,         0,         'kg',          2,                NULL,    2,          15,        1,            'In stock [DE]', 'Out of stock [DE]', '0000-00-00', '2008-02-04', '2008-02-04 17:07:48', 1,          2,         2,         'search1000',    1,           '',                        0,            0,           '',             50,                0,                   '',                        '',                'Test product 0 [EN] šÄßüл', 'Test product 0 short desc [EN] šÄßüл', 'šÄßüл1000',      '',           'In stock [EN] šÄßüл', 'Out of stock [EN] šÄßüл',  0,       'testdistributor', 'testmanufacturer',  1,              1,             'DAY'),
                         ('1002',   1, '',            1,         '1002',     '[DE 2] Test product 2 šÄßüл', 'Test product 2 short desc [DE]', 55,        0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            'In stock [DE]', 'Out of stock [DE]', '0000-00-00', '2008-02-04', '2008-02-04 17:18:18', 0,          0,         0,         'search1002',    1,           'variants [DE]',           10,           2,           '',             55,                67,                  'variants [EN] šÄßüл',     '',                'Test product 2 [EN] šÄßüл', 'Test product 2 short desc [EN] šÄßüл', 'šÄßüл1002',      '',           'In stock [EN] šÄßüл', 'Out of stock [EN] šÄßüл',  0,       'testdistributor', 'testmanufacturer',  1,              1,             'MONTH'),
                         ('1002-1', 1, '1002',        1,         '1002-1',   '',                            '',                               55,        45,         0,          0,          0,         '',            0,                NULL,    0,          5,         1,            'In stock [DE]', 'Out of stock [DE]', '0000-00-00', '2008-02-04', '2008-02-04 17:34:10', 0,          0,         0,         '',              1,           '',                        0,            0,           'var1 [DE]',    0,                 0,                   '',                        'var1 [EN] šÄßüл', '',                          '',                                     '',               '',           'In stock [EN] šÄßüл', 'Out of stock [EN] šÄßüл',  1,       '',                '',                  0,              0,             ''),
                         ('1002-2', 1, '1002',        1,         '1002-2',   '',                            '',                               67,        47,         0,          0,          0,         '',            0,                NULL,    0,          5,         1,            'In stock [DE]', 'Out of stock [DE]', '0000-00-00', '2008-02-04', '2008-02-04 17:34:36', 0,          0,         0,         '',              1,           '',                        0,            0,           'var2 [DE]',    0,                 0,                   '',                        'var2 [EN] šÄßüл', '',                          '',                                     '',               '',           'In stock [EN] šÄßüл', 'Out of stock [EN] šÄßüл',  2,       '',                '',                  0,              0,             ''),
                         ('1001',   1, '',            1,         '1001',     '[DE 1] Test product 1 šÄßüл', 'Test product 1 short desc [DE]', 100,       0,          0,          0,          150,       '',            0,                10,      0,          0,         1,            '',              '',                  '2030-01-01', '2008-02-04', '2008-02-04 17:35:49', 0,          0,         0,         'search1001',    1,           '',                        0,            0,           '',             100,               0,                   '',                        '',                'Test product 1 [EN] šÄßüл', 'Test product 1 short desc [EN] šÄßüл', 'šÄßüл1001',      '',           '',                    '',                         0,       'testdistributor', 'testmanufacturer',  0,              1,             'WEEK');

#Articles long desc
REPLACE INTO `oxartextends` (`OXID`,   `OXLONGDESC`,                                  `OXLONGDESC_1`) VALUES
                           ('1001',   '<p>Test product 1 long description [DE]</p>', '<p>Test product 1 long description [EN] šÄßüл</p>'),
                           ('1002',   '<p>Test product 2 long description [DE]</p>', '<p>Test product 2 long description [EN] šÄßüл</p>'),
                           ('1002-1', '',                                            ''),
                           ('1002-2', '',                                            ''),
                           ('1000',   '<p>Test product 0 long description [DE]</p>', '<p>Test product 0 long description [EN] šÄßüл</p>');

REPLACE INTO `oxcategories` (`OXID`,          `OXPARENTID`,   `OXLEFT`, `OXRIGHT`, `OXROOTID`,     `OXSORT`, `OXACTIVE`, `OXSHOPID`,   `OXTITLE`,                    `OXDESC`,                    `OXLONGDESC`,                `OXDEFSORT`, `OXDEFSORTMODE`, `OXPRICEFROM`, `OXPRICETO`, `OXACTIVE_1`, `OXTITLE_1`,                  `OXDESC_1`,                        `OXLONGDESC_1`,                    `OXVAT`, `OXSHOWSUFFIX`) VALUES
                           ('testcategory0', 'oxrootid',      1,        4,        'testcategory0', 1,        1,         1, 'Test category 0 [DE] šÄßüл', 'Test category 0 desc [DE]', 'Category 0 long desc [DE]', 'oxartnum',   0,               0,             0,           1,           'Test category 0 [EN] šÄßüл', 'Test category 0 desc [EN] šÄßüл', 'Category 0 long desc [EN] šÄßüл',  5,       1),
                           ('testcategory1', 'testcategory0', 2,        3,        'testcategory0', 2,        1,         1, 'Test category 1 [DE] šÄßüл', 'Test category 1 desc [DE]', 'Category 1 long desc [DE]', 'oxartnum',   1,               0,             0,           1,           'Test category 1 [EN] šÄßüл', 'Test category 1 desc [EN] šÄßüл', 'Category 1 long desc [EN] šÄßüл',  NULL,    1);

#Article2Category
REPLACE INTO `oxobject2category` (`OXID`,                       `OXOBJECTID`, `OXCATNID`,     `OXPOS`, `OXTIME`) VALUES
                                ('6f047a71f53e3b6c2.93342239', '1000',       'testcategory0', 0,       1202134867),
                                ('testobject2category', '1001',       'testcategory0', 0,       1202134867);

#Users demodata
REPLACE INTO `oxuser` (`OXID`,     `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`,   `OXUSERNAME`,         `OXPASSWORD`,                       `OXPASSSALT`,        `OXCUSTNR`, `OXUSTID`, `OXCOMPANY`,          `OXFNAME`,        `OXLNAME`,           `OXSTREET`,        `OXSTREETNR`, `OXADDINFO`,                   `OXCITY`,            `OXCOUNTRYID`,                `OXZIP`, `OXFON`,        `OXFAX`,       `OXSAL`, `OXBONI`, `OXCREATE`,            `OXREGISTER`,          `OXPRIVFON`,   `OXMOBFON`,    `OXBIRTHDATE`) VALUES
                     ('testuser',  1,         'user',     1, 'example_test@oxid-esales.dev', 'c9dadd994241c9e5fa6469547009328a', '7573657275736572',   8,         '',        'UserCompany šÄßüл',  'UserNamešÄßüл',  'UserSurnamešÄßüл',  'Musterstr.šÄßüл', '1',          'User additional info šÄßüл',  'Musterstadt šÄßüл', 'a7c40f631fc920687.20179984', '79098',  '0800 111111', '0800 111112', 'Mr',     500,     '2008-02-05 14:42:42', '2008-02-05 14:42:42', '0800 111113', '0800 111114', '1980-01-01');

#object2Group
REPLACE INTO `oxobject2group` (`OXID`,                       `OXSHOPID`,   `OXOBJECTID`,   `OXGROUPSID`) VALUES
                             ('aad47a85a83749c71.33568408', 1, 'testuser',     'oxidnewcustomer');


# createBasketUserAccountWithoutRegistration
#adding states for germany
REPLACE INTO `oxstates` (`OXID`, `OXCOUNTRYID`, `OXTITLE`, `OXISOALPHA2`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`) VALUES
('BB', 'a7c40f631fc920687.20179984', 'Brandenburg', 'BB', 'Brandenburg', '', ''),
('BE', 'a7c40f631fc920687.20179984', 'Berlin', 'BE', 'Berlin', '', ''),
('BW', 'a7c40f631fc920687.20179984', 'Baden-Württemberg', 'BW', 'Baden-Wurttemberg', '', ''),
('BY', 'a7c40f631fc920687.20179984', 'Bayern', 'BY', 'Bavaria', '', ''),
('HB', 'a7c40f631fc920687.20179984', 'Bremen', 'HB', 'Bremen', '', ''),
('HE', 'a7c40f631fc920687.20179984', 'Hessen', 'HE', 'Hesse', '', ''),
('HH', 'a7c40f631fc920687.20179984', 'Hamburg', 'HH', 'Hamburg', '', ''),
('MV', 'a7c40f631fc920687.20179984', 'Mecklenburg-Vorpommern', 'MV', 'Mecklenburg-Western Pomerania', '', ''),
('NI', 'a7c40f631fc920687.20179984', 'Niedersachsen', 'NI', 'Lower Saxony', '', ''),
('NW', 'a7c40f631fc920687.20179984', 'Nordrhein-Westfalen', 'NW', 'North Rhine-Westphalia', '', ''),
('RP', 'a7c40f631fc920687.20179984', 'Rheinland-Pfalz', 'RP', 'Rhineland-Palatinate', '', ''),
('SH', 'a7c40f631fc920687.20179984', 'Schleswig-Holstein', 'SH', 'Schleswig-Holstein', '', ''),
('SL', 'a7c40f631fc920687.20179984', 'Saarland', 'SL', 'Saarland', '', ''),
('SN', 'a7c40f631fc920687.20179984', 'Sachsen', 'SN', 'Saxony', '', ''),
('ST', 'a7c40f631fc920687.20179984', 'Sachsen-Anhalt', 'ST', 'Saxony-Anhalt', '', ''),
('TH', 'a7c40f631fc920687.20179984', 'Thüringen', 'TH', 'Thuringia', '', '');


# createBasketUserAccountWithoutRegistration
UPDATE `oxconfig` SET `OXVARVALUE` = 0xde         WHERE `OXVARNAME` = 'iNewBasketItemMessage';
UPDATE `oxconfig` SET `OXVARVALUE` = ''           WHERE `OXVARNAME` = 'blDisableNavBars';
REPLACE INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`,   `OXVARNAME`,                     `OXVARTYPE`, `OXVARVALUE`) VALUES
                       ('4742', 1, '', 'blPerfNoBasketSaving',          'bool',       0x93ea1218),
                       ('8563fba1965a219c9.51133344', 1, '', 'blUseStock',          'bool',       0x93ea1218);

# createBasketUserAccountWithoutRegistrationTwice
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Belgium';

# userCompareList
#Article2Attribute
REPLACE INTO `oxobject2attribute` (`OXID`,                       `OXOBJECTID`, `OXATTRID`,       `OXVALUE`,           `OXPOS`, `OXVALUE_1`) VALUES
                                 ('aad47a8511f54e023.54090494', '1000',       'testattribute1', 'attr value 1 [DE]',  0,      'attr value 1 [EN] šÄßüл'),
                                 ('aad47a8511f556f17.20889862', '1001',       'testattribute1', 'attr value 11 [DE]', 0,      'attr value 11 [EN] šÄßüл'),
                                 ('aad47a85125a41ed7.53096100', '1000',       'testattribute2', 'attr value 2 [DE]',  0,      'attr value 2 [EN] šÄßüл'),
                                 ('aad47a85125a4aa05.37412863', '1001',       'testattribute2', 'attr value 12 [DE]', 0,      'attr value 12 [EN] šÄßüл'),
                                 ('aad47a8512d783995.31168870', '1000',       'testattribute3', 'attr value 3 [DE]',  0,      'attr value 3 [EN] šÄßüл'),
                                 ('aad47a8512d78c354.06494034', '1001',       'testattribute3', 'attr value 3 [DE]',  0,      'attr value 3 [EN] šÄßüл');

#Attributes demodata
REPLACE INTO `oxattribute` (`OXID`,           `OXSHOPID`,   `OXTITLE`,                     `OXTITLE_1`,                  `OXPOS`) VALUES
                          ('testattribute1', 1, 'Test attribute 1 [DE] šÄßüл', 'Test attribute 1 [EN] šÄßüл', 1),
                          ('testattribute2', 1, 'Test attribute 2 [DE] šÄßüл', 'Test attribute 2 [EN] šÄßüл', 3),
                          ('testattribute3', 1, 'Test attribute 3 [DE] šÄßüл', 'Test attribute 3 [EN] šÄßüл', 2);
UPDATE `oxattribute` SET `OXDISPLAYINBASKET` = 0;

#set country, username, password for default user
UPDATE oxuser
  SET
      oxcountryid = 'a7c40f631fc920687.20179984',
      oxusername = 'admin@myoxideshop.com',
      oxpassword = '6cb4a34e1b66d3445108cd91b67f98b9',
      oxpasssalt = '6631386565336161636139613634663766383538633566623662613036636539'
  WHERE OXUSERNAME='admin';

REPLACE INTO `oxdiscount` (`OXID`,            `OXSHOPID`,  `OXACTIVE`, `OXTITLE`,                          `OXTITLE_1`,             `OXAMOUNT`, `OXAMOUNTTO`, `OXPRICETO`, `OXPRICE`, `OXADDSUMTYPE`, `OXADDSUM`, `OXITMARTID`, `OXITMAMOUNT`, `OXITMMULTIPLE`, `OXSORT`) VALUES
                         ('testcatdiscount', 1, 0,         'discount for category [DE] šÄßüл', 'discount for category [EN] šÄßüл',  1,          999999,       0,           0,        'abs',           5,         '',            0,             0,               100);

#object2discount
REPLACE INTO `oxobject2discount` (`OXID`,                        `OXDISCOUNTID`,    `OXOBJECTID`,                 `OXTYPE`) VALUES
                                ('fa647a823ce118996.58546955',  'testcatdiscount', 'a7c40f631fc920687.20179984', 'oxcountry'),
                                ('fa647a823d5079104.99115703',  'testcatdiscount', 'testcategory0',              'oxcategories');

#Coupons demodata
REPLACE INTO `oxvoucherseries` (`OXID`,         `OXSHOPID`,   `OXSERIENR`,           `OXSERIEDESCRIPTION`,      `OXDISCOUNT`, `OXDISCOUNTTYPE`, `OXBEGINDATE`,         `OXENDDATE`,          `OXALLOWSAMESERIES`, `OXALLOWOTHERSERIES`, `OXALLOWUSEANOTHER`, `OXMINIMUMVALUE`, `OXCALCULATEONCE`) VALUES
                              ('testvoucher4', 1, '4 Coupon šÄßüл',      '4 Coupon šÄßüл',           50.00,       'percent',        '2008-01-01 00:00:00', '2020-01-01 00:00:00', 0,                   0,                    0,                   45.00,            1);

REPLACE INTO `oxvouchers` (`OXDATEUSED`, `OXRESERVED`, `OXVOUCHERNR`, `OXVOUCHERSERIEID`, `OXID`) VALUES
                         ('0000-00-00',  0,           '123123',      'testvoucher4',     'testcoucher011');

#Gift wrapping demodata
REPLACE INTO `oxwrapping` (`OXID`,         `OXSHOPID`,  `OXACTIVE`, `OXACTIVE_1`, `OXACTIVE_2`, `OXACTIVE_3`, `OXTYPE`, `OXNAME`,                        `OXNAME_1`,                     `OXPRICE`) VALUES
                         ('testwrapping', 1, 1,          1,            1,            1,           'WRAP',   'Test wrapping [DE] šÄßüл',      'Test wrapping [EN] šÄßüл',      0.9),
                         ('testcard',     1, 1,          1,            1,            1,           'CARD',   'Test card [DE] šÄßüл',          'Test card [EN] šÄßüл',          0.2);
