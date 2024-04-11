SET @@session.sql_mode = '';

#demodata for articles
REPLACE INTO `oxarticles` (`OXID`,   `OXSHOPID`,   `OXPARENTID`, `OXACTIVE`, `OXARTNUM`, `OXTITLE`,                     `OXSHORTDESC`,                   `OXPRICE`, `OXPRICEA`, `OXPRICEB`, `OXPRICEC`, `OXTPRICE`, `OXUNITNAME`, `OXUNITQUANTITY`, `OXVAT`, `OXWEIGHT`, `OXSTOCK`, `OXSTOCKFLAG`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`,       `OXDELIVERY`, `OXINSERT`,   `OXTIMESTAMP`,        `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXSEARCHKEYS`, `OXISSEARCH`, `OXVARNAME`,              `OXVARSTOCK`, `OXVARCOUNT`, `OXVARSELECT`, `OXVARMINPRICE`, `OXVARMAXPRICE`, `OXVARNAME_1`,             `OXVARSELECT_1`,   `OXTITLE_1`,                 `OXSHORTDESC_1`,                        `OXSEARCHKEYS_1`, `OXBUNDLEID`, `OXSTOCKTEXT_1`,       `OXNOSTOCKTEXT_1`,         `OXSORT`, `OXVENDORID`,      `OXMANUFACTURERID`, `OXMINDELTIME`, `OXMAXDELTIME`, `OXDELTIMEUNIT`) VALUES
                          (  '1000',          1,           '',          1,     '1000', '[DE 4] Test product 0 šÄßüл', 'Test product 0 short desc [DE]',        50,         35,         45,         55,          0,         'kg',                2,    NULL,          2,        15,             1, 'In stock [DE]', 'Out of stock [DE]', '0000-00-00', '2008-02-04', '2008-02-04 17:07:48',          1,         2,          2,   'search1000',            1,                        '',            0,            0,            '',              50,               0,                        '',                '', 'Test product 0 [EN] šÄßüл', 'Test product 0 short desc [EN] šÄßüл',      'šÄßüл1000',           '', 'In stock [EN] šÄßüл', 'Out of stock [EN] šÄßüл',        0, 'testdistributor', 'testmanufacturer',              1,              1,           'DAY'),
                          (  '1002',          1,           '',          1,     '1002', '[DE 2] Test product 2 šÄßüл', 'Test product 2 short desc [DE]',        55,          0,          0,          0,          0,           '',                0,    NULL,          0,         0,             1, 'In stock [DE]', 'Out of stock [DE]', '0000-00-00', '2008-02-04', '2008-02-04 17:18:18',          0,         0,          0,   'search1002',            1,           'variants [DE]',           10,            2,            '',              55,              67,     'variants [EN] šÄßüл',                '', 'Test product 2 [EN] šÄßüл', 'Test product 2 short desc [EN] šÄßüл',      'šÄßüл1002',           '', 'In stock [EN] šÄßüл', 'Out of stock [EN] šÄßüл',        0, 'testdistributor', 'testmanufacturer',              1,              1,         'MONTH'),
                          ('1002-1',          1,       '1002',          1,   '1002-1',                            '',                               '',        55,         45,          0,          0,          0,           '',                0,    NULL,          0,         5,             1, 'In stock [DE]', 'Out of stock [DE]', '0000-00-00', '2008-02-04', '2008-02-04 17:34:10',          0,         0,          0,             '',            1,                        '',            0,            0,   'var1 [DE]',               0,               0,                        '', 'var1 [EN] šÄßüл',                          '',                                     '',               '',           '', 'In stock [EN] šÄßüл', 'Out of stock [EN] šÄßüл',        1,                '',                 '',              0,              0,              ''),
                          ('1002-2',          1,       '1002',          1,   '1002-2',                            '',                               '',        67,         47,          0,          0,          0,           '',                0,    NULL,          0,         5,             1, 'In stock [DE]', 'Out of stock [DE]', '0000-00-00', '2008-02-04', '2008-02-04 17:34:36',          0,         0,          0,             '',            1,                        '',            0,            0,   'var2 [DE]',               0,               0,                        '', 'var2 [EN] šÄßüл',                          '',                                     '',               '',           '', 'In stock [EN] šÄßüл', 'Out of stock [EN] šÄßüл',        2,                '',                 '',              0,              0,              ''),
                          (  '1001',          1,           '',          1,     '1001', '[DE 1] Test product 1 šÄßüл', 'Test product 1 short desc [DE]',       100,          0,          0,          0,        150,           '',                0,      10,          0,         0,             1,              '',                  '', '2030-01-01', '2008-02-04', '2008-02-04 17:35:49',          0,         0,          0,   'search1001',            1,                        '',           0,            0,            '',             100,               0,                        '',                '', 'Test product 1 [EN] šÄßüл', 'Test product 1 short desc [EN] šÄßüл',      'šÄßüл1001',           '',                    '',                        '',        0, 'testdistributor', 'testmanufacturer',              0,              1,          'WEEK'),
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

REPLACE INTO `oxartextends` (`OXID`,   `OXLONGDESC`,                                  `OXLONGDESC_1`) VALUES
                           ('1001',   '<p>Test product 1 long description [DE]</p>', '<p>Test product 1 long description [EN] šÄßüл</p>'),
                           ('1002',   '<p>Test product 2 long description [DE]</p>', '<p>Test product 2 long description [EN] šÄßüл</p>'),
                           ('1002-1', '',                                            ''),
                           ('1002-2', '',                                            ''),
                           ('1000', '<p>Test product 0 long description [DE]</p>', '<p>Test product 0 long description [EN] šÄßüл</p>'),
                           ('10014',                                            '',                                                  '');


REPLACE INTO `oxcategories` (         `OXID`,    `OXPARENTID`, `OXLEFT`, `OXRIGHT`,      `OXROOTID`, `OXSORT`, `OXACTIVE`, `OXSHOPID`,                    `OXTITLE`,                    `OXDESC`,                `OXLONGDESC`, `OXDEFSORT`, `OXDEFSORTMODE`, `OXPRICEFROM`, `OXPRICETO`, `OXACTIVE_1`,                  `OXTITLE_1`,                        `OXDESC_1`,                    `OXLONGDESC_1`, `OXVAT`, `OXSHOWSUFFIX`) VALUES
                            ('testcategory0',      'oxrootid',        1,         4, 'testcategory0',        1,          1,          1, 'Test category 0 [DE] šÄßüл', 'Test category 0 desc [DE]', 'Category 0 long desc [DE]',  'oxartnum',               0,             0,           0,            1, 'Test category 0 [EN] šÄßüл', 'Test category 0 desc [EN] šÄßüл', 'Category 0 long desc [EN] šÄßüл',       5,              1),
                            ('testcategory1', 'testcategory0',        2,         3, 'testcategory0',        2,          1,          1, 'Test category 1 [DE] šÄßüл', 'Test category 1 desc [DE]', 'Category 1 long desc [DE]',  'oxartnum',               1,             0,           0,            1, 'Test category 1 [EN] šÄßüл', 'Test category 1 desc [EN] šÄßüл', 'Category 1 long desc [EN] šÄßüл',    NULL,              1),
                            ('testcategory2',      'oxrootid',        1,         2, 'testcategory2',        1,          0,          1, 'Test category 2 [DE] šÄßüл', 'Test category 2 desc [DE]', 'Category 2 long desc [DE]',  'oxartnum',               0,             0,           0,            1, 'Test category 2 [EN] šÄßüл', 'Test category 2 desc [EN] šÄßüл', 'Category 2 long desc [EN] šÄßüл',    NULL,              1),
                            ( 'testpricecat',      'oxrootid',        1,         2,  'testpricecat',    99999,          0,          1,           'price šÄßüл [DE]',       'price category [DE]',                          '',          '',               0,            49,          60,            0,           'price [EN] šÄßüл',       'price category [EN] šÄßüл',                                '',    NULL,              1);

REPLACE INTO `oxobject2category` (`OXID`,                       `OXOBJECTID`, `OXCATNID`,     `OXPOS`, `OXTIME`) VALUES
                                ('6f047a71f53e3b6c2.93342239', '1000',       'testcategory0', 0,       1202134867),
                                ('testobject2category', '1001',       'testcategory0', 0,       1202134867);

REPLACE INTO `oxuser` (`OXID`,     `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`,   `OXUSERNAME`,         `OXPASSWORD`,                       `OXPASSSALT`,        `OXCUSTNR`, `OXUSTID`, `OXCOMPANY`,          `OXFNAME`,        `OXLNAME`,           `OXSTREET`,        `OXSTREETNR`, `OXADDINFO`,                   `OXCITY`,            `OXCOUNTRYID`,                `OXZIP`, `OXFON`,        `OXFAX`,       `OXSAL`, `OXBONI`, `OXCREATE`,            `OXREGISTER`,          `OXPRIVFON`,   `OXMOBFON`,    `OXBIRTHDATE`) VALUES
                      (      'testuser',          1,      'user',          1, 'example_test@oxid-esales.dev', 'c9dadd994241c9e5fa6469547009328a',                                                '7573657275736572',          8,         '', 'UserCompany šÄßüл',    'UserNamešÄßüл', 'UserSurnamešÄßüл', 'Musterstr.šÄßüл',          '1', 'User additional info šÄßüл', 'Musterstadt šÄßüл', 'testcountry_de', '79098', '0800 111111', '0800 111112',    'Mr',      500, '2008-02-05 14:42:42', '2008-02-05 14:42:42', '0800 111113', '0800 111114', '1980-01-01'),
                      ('oxdefaultadmin',          1, 'malladmin',          1,        'admin@myoxideshop.com', '6cb4a34e1b66d3445108cd91b67f98b9','6631386565336161636139613634663766383538633566623662613036636539',          1,         '', 'Your Company Name',             'John',              'Doe',    'Maple Street',       '2425',                           '',          'Any City', 'testcountry_de',  '9041', '217-8918712', '217-8918713',    'MR',     1000, '2003-01-01 00:00:00', '2003-01-01 00:00:00',            '',            '', '0000-00-00');

#object2Group
REPLACE INTO `oxobject2group` (`OXID`,                       `OXSHOPID`,   `OXOBJECTID`,   `OXGROUPSID`) VALUES
                              ('aad47a85a83749c71.33568408',          1,       'testuser',     'oxidnewcustomer'),
                              ('34f5e54f695bf109454aa152d9',          1, 'oxdefaultadmin', 'oxidforeigncustomer'),
                              ('e913fdd8443ed43e1.51222316',          1, 'oxdefaultadmin',           'oxidadmin');

# createBasketUserAccountWithoutRegistration
#adding states for germany
REPLACE INTO `oxstates` (`OXID`, `OXCOUNTRYID`, `OXTITLE`, `OXISOALPHA2`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`) VALUES
('BB', 'testcountry_de', 'Brandenburg', 'BB', 'Brandenburg', '', ''),
('BE', 'testcountry_de', 'Berlin', 'BE', 'Berlin', '', ''),
('BW', 'testcountry_de', 'Baden-Württemberg', 'BW', 'Baden-Wurttemberg', '', ''),
('BY', 'testcountry_de', 'Bayern', 'BY', 'Bavaria', '', ''),
('HB', 'testcountry_de', 'Bremen', 'HB', 'Bremen', '', ''),
('HE', 'testcountry_de', 'Hessen', 'HE', 'Hesse', '', ''),
('HH', 'testcountry_de', 'Hamburg', 'HH', 'Hamburg', '', ''),
('MV', 'testcountry_de', 'Mecklenburg-Vorpommern', 'MV', 'Mecklenburg-Western Pomerania', '', ''),
('NI', 'testcountry_de', 'Niedersachsen', 'NI', 'Lower Saxony', '', ''),
('NW', 'testcountry_de', 'Nordrhein-Westfalen', 'NW', 'North Rhine-Westphalia', '', ''),
('RP', 'testcountry_de', 'Rheinland-Pfalz', 'RP', 'Rhineland-Palatinate', '', ''),
('SH', 'testcountry_de', 'Schleswig-Holstein', 'SH', 'Schleswig-Holstein', '', ''),
('SL', 'testcountry_de', 'Saarland', 'SL', 'Saarland', '', ''),
('SN', 'testcountry_de', 'Sachsen', 'SN', 'Saxony', '', ''),
('ST', 'testcountry_de', 'Sachsen-Anhalt', 'ST', 'Saxony-Anhalt', '', ''),
('TH', 'testcountry_de', 'Thüringen', 'TH', 'Thuringia', '', '');


# createBasketUserAccountWithoutRegistration
UPDATE `oxconfig` SET `OXVARVALUE` = '0'         WHERE `OXVARNAME` = 'iNewBasketItemMessage';
UPDATE `oxconfig` SET `OXVARVALUE` = ''           WHERE `OXVARNAME` = 'blDisableNavBars';
UPDATE `oxconfig` SET `OXVARVALUE` = '0'           WHERE `OXVARNAME` = 'blUseMultidimensionVariants';
UPDATE `oxconfig` SET `OXVARVALUE` = 'a:2:{s:2:"de";a:3:{s:6:"baseId";i:0;s:6:"active";s:1:"1";s:4:"sort";s:1:"1";}s:2:"en";a:3:{s:6:"baseId";i:1;s:6:"active";s:1:"1";s:4:"sort";s:1:"2";}}' WHERE `OXVARNAME` = 'aLanguageParams';
UPDATE `oxconfig` SET `OXVARVALUE` = 'a:2:{s:2:"de";s:7:"Deutsch";s:2:"en";s:7:"English";}'           WHERE `OXVARNAME` = 'aLanguages';

REPLACE INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`,   `OXVARNAME`,                     `OXVARTYPE`, `OXVARVALUE`) VALUES
                       ('4742', 1, '', 'blPerfNoBasketSaving',          'bool',       'true'),
                       ('8563fba1965a219c9.51133344', 1, '', 'blUseStock',          'bool',       'true');

UPDATE `oxcountry` SET `OXACTIVE` = 1 , `OXID` = 'testcountry_be' WHERE `OXISOALPHA2` = 'BE';
UPDATE `oxcountry` SET `OXACTIVE` = 1 , `OXID` = 'testcountry_de' WHERE `OXISOALPHA2` = 'DE';

REPLACE INTO `oxdeliveryset` (           `OXID`, `OXSHOPID`, `OXACTIVE`,        `OXACTIVEFROM`,          `OXACTIVETO`,     `OXTITLE`,   `OXTITLE_1`, `OXPOS`) VALUES
                             ('oxidalternative',          1,          1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Alternative', 'Alternative',      20);

REPLACE INTO `oxdelivery` (        `OXID`, `OXSHOPID`, `OXACTIVE`, `OXFIXED`, `OXPARAM`, `OXPARAMEND`) VALUES
                               ('testdelivery',          1,          1,         0,         0,      9999999);

REPLACE INTO `oxdel2delset` (           `OXID`,      `OXDELID`,      `OXDELSETID`) VALUES
                            (   'teststandart', 'testdelivery',    'oxidstandard'),
                            ('testalternative', 'testdelivery', 'oxidalternative');

REPLACE INTO `oxobject2delivery` (  `OXID`,    `OXDELIVERYID`,     `OXOBJECTID`,    `OXTYPE`) VALUES
                       (  'testdelcountry',    'testdelivery', 'testcountry_de', 'oxcountry'),
                       (  'standartdelset',    'oxidstandard', 'testcountry_de',  'oxdelset'),
                       ('alternativdelset', 'oxidalternative', 'testcountry_de',  'oxdelset');

REPLACE INTO `oxobject2payment` (               `OXID`,    `OXPAYMENTID`,     `OXOBJECTID`,     `OXTYPE`) VALUES
                                ( 'testcodalternative',  'oxidcashondel', 'oxidalternative',  'oxdelset'),
                                (    'testcodstandard',  'oxidcashondel',    'oxidstandard',  'oxdelset'),
                                (   'testcodcountryde',  'oxidcashondel',  'testcountry_de', 'oxcountry'),
                                ( 'testpiaalternative', 'oxidpayadvance', 'oxidalternative',  'oxdelset'),
                                (    'testpiastandard', 'oxidpayadvance',    'oxidstandard',  'oxdelset'),
                                (   'testpiacountryde', 'oxidpayadvance',  'testcountry_de', 'oxcountry'),
                                ( 'testinvalternative',    'oxidinvoice', 'oxidalternative',  'oxdelset'),
                                (    'testinvstandard',    'oxidinvoice',    'oxidstandard',  'oxdelset'),
                                (   'testinvcountryde',    'oxidinvoice',  'testcountry_de', 'oxcountry');


REPLACE INTO `oxattribute` (`OXID`,           `OXSHOPID`,   `OXTITLE`,                     `OXTITLE_1`,                  `OXPOS`) VALUES
                          ('testattribute1', 1, 'Test attribute 1 [DE] šÄßüл', 'Test attribute 1 [EN] šÄßüл', 1),
                          ('testattribute2', 1, 'Test attribute 2 [DE] šÄßüл', 'Test attribute 2 [EN] šÄßüл', 3),
                          ('testattribute3', 1, 'Test attribute 3 [DE] šÄßüл', 'Test attribute 3 [EN] šÄßüл', 2);
UPDATE `oxattribute` SET `OXDISPLAYINBASKET` = 0;
#Article2Attribute
REPLACE INTO `oxobject2attribute` (`OXID`,                       `OXOBJECTID`, `OXATTRID`,       `OXVALUE`,           `OXPOS`, `OXVALUE_1`) VALUES
                                 ('aad47a8511f54e023.54090494', '1000',       'testattribute1', 'attr value 1 [DE]',  0,      'attr value 1 [EN] šÄßüл'),
                                 ('aad47a8511f556f17.20889862', '1001',       'testattribute1', 'attr value 11 [DE]', 0,      'attr value 11 [EN] šÄßüл'),
                                 ('aad47a85125a41ed7.53096100', '1000',       'testattribute2', 'attr value 2 [DE]',  0,      'attr value 2 [EN] šÄßüл'),
                                 ('aad47a85125a4aa05.37412863', '1001',       'testattribute2', 'attr value 12 [DE]', 0,      'attr value 12 [EN] šÄßüл'),
                                 ('aad47a8512d783995.31168870', '1000',       'testattribute3', 'attr value 3 [DE]',  0,      'attr value 3 [EN] šÄßüл'),
                                 ('aad47a8512d78c354.06494034', '1001',       'testattribute3', 'attr value 3 [DE]',  0,      'attr value 3 [EN] šÄßüл');

INSERT INTO `oxcategory2attribute` (`OXID`, `OXOBJECTID`, `OXATTRID`, `OXSORT`) VALUES
                                   ('testcategory0attribute3',	'testcategory0',	'testattribute3',	2),
                                   ('testcategory0attribute1',	'testcategory0',	'testattribute1',	0),
                                   ('testcategory0attribute2',	'testcategory0',	'testattribute2',	1);

REPLACE INTO `oxdiscount` (`OXID`,            `OXSHOPID`,  `OXACTIVE`, `OXTITLE`,                          `OXTITLE_1`,             `OXAMOUNT`, `OXAMOUNTTO`, `OXPRICETO`, `OXPRICE`, `OXADDSUMTYPE`, `OXADDSUM`, `OXITMARTID`, `OXITMAMOUNT`, `OXITMMULTIPLE`, `OXSORT`) VALUES
                         ('testcatdiscount', 1, 0,         'discount for category [DE] šÄßüл', 'discount for category [EN] šÄßüл',  1,          999999,       0,           0,        'abs',           5,         '',            0,             0,               100);

#object2discount
REPLACE INTO `oxobject2discount` (`OXID`,                        `OXDISCOUNTID`,    `OXOBJECTID`,                 `OXTYPE`) VALUES
                                 ('fa647a823ce118996.58546955', 'testcatdiscount', 'testcountry_de',    'oxcountry'),
                                ('fa647a823d5079104.99115703',  'testcatdiscount', 'testcategory0',              'oxcategories');

#Coupons demodata
REPLACE INTO `oxvoucherseries` (`OXID`,         `OXSHOPID`,   `OXSERIENR`,           `OXSERIEDESCRIPTION`,      `OXDISCOUNT`, `OXDISCOUNTTYPE`, `OXBEGINDATE`,         `OXENDDATE`,          `OXALLOWSAMESERIES`, `OXALLOWOTHERSERIES`, `OXALLOWUSEANOTHER`, `OXMINIMUMVALUE`, `OXCALCULATEONCE`) VALUES
                              ('testvoucher4', 1, '4 Coupon šÄßüл',      '4 Coupon šÄßüл',           50.00,       'percent',        '2008-01-01 00:00:00', now() + interval 1 day, 0,                   0,                    0,                   45.00,            1);

REPLACE INTO `oxvouchers` (`OXDATEUSED`, `OXRESERVED`, `OXVOUCHERNR`, `OXVOUCHERSERIEID`, `OXID`) VALUES
                         ('0000-00-00',  0,           '123123',      'testvoucher4',     'testcoucher011');

#Gift wrapping demodata
REPLACE INTO `oxwrapping` (`OXID`,         `OXSHOPID`,  `OXACTIVE`, `OXACTIVE_1`, `OXACTIVE_2`, `OXACTIVE_3`, `OXTYPE`, `OXNAME`,                        `OXNAME_1`,                     `OXPRICE`) VALUES
                         ('testwrapping', 1, 1,          1,            1,            1,           'WRAP',   'Test wrapping [DE] šÄßüл',      'Test wrapping [EN] šÄßüл',      0.9),
                         ('testcard',     1, 1,          1,            1,            1,           'CARD',   'Test card [DE] šÄßüл',          'Test card [EN] šÄßüл',          0.2);

#Select list demodata
REPLACE INTO `oxselectlist` (`OXID`,          `OXSHOPID`,   `OXTITLE`,                        `OXIDENT`,              `OXVALDESC`,                                                                      `OXTITLE_1`,                      `OXVALDESC_1`) VALUES
                           ('testsellist',   1, 'test selection list [DE] šÄßüл', 'test sellist šÄßüл',   'selvar1 [DE]!P!1__@@selvar2 [DE]__@@selvar3 [DE]!P!-2__@@selvar4 [DE]!P!2%__@@', 'test selection list [EN] šÄßüл', 'selvar1 [EN] šÄßüл!P!1__@@selvar2 [EN] šÄßüл__@@selvar3 [EN] šÄßüл!P!-2__@@selvar4 [EN] šÄßüл!P!2%__@@');

#Article2SelectList
REPLACE INTO `oxobject2selectlist` (`OXID`,                       `OXOBJECTID`, `OXSELNID`,   `OXSORT`) VALUES
                                  ('testsellist.1001', '1001',       'testsellist', 0);
#Manufacturer demodata
REPLACE INTO `oxmanufacturers` (`OXID`,             `OXSHOPID`, `OXACTIVE`,                 `OXTITLE`,                   `OXSHORTDESC`,               `OXTITLE_1`,                       `OXSHORTDESC_1`, `OXSHOWSUFFIX`,   `OXICON`) VALUES
                               ('testmanufacturer',          1,          1, 'Manufacturer [DE] šÄßüл', 'Manufacturer description [DE]', 'Manufacturer [EN] šÄßüл', 'Manufacturer description [EN] šÄßüл',              1, 'test.png');

#Vendor demodata
REPLACE INTO `oxvendor` (          `OXID`, `OXSHOPID`, `OXACTIVE`,                `OXTITLE`,                  `OXSHORTDESC`,              `OXTITLE_1`,                      `OXSHORTDESC_1`, `OXSHOWSUFFIX`) VALUES
                       ('testdistributor',          1,          1, 'Distributor [DE] šÄßüл', 'Distributor description [DE]', 'Distributor [EN] šÄßüл', 'Distributor description [EN] šÄßüл',              1);
#Order demodata
REPLACE INTO `oxorder` (`OXID`,     `OXSHOPID`, `OXUSERID`,`OXORDERDATE`,       `OXORDERNR`, `OXBILLCOMPANY`,          `OXBILLEMAIL`,       `OXBILLFNAME`,    `OXBILLLNAME`,    `OXBILLSTREET`,       `OXBILLSTREETNR`,`OXBILLADDINFO`,    `OXBILLCITY`,       `OXBILLCOUNTRYID`,`OXBILLZIP`,`OXREMARK`,                `OXFOLDER`,       `OXBILLSTATEID`,`OXCARDTEXT`) VALUES
                       ('testorder',1,          'testuser','2023-03-30 11:00:04',123,        'test bill company_name', 'test@billemail.com','test bill fname','test bill lname','test address street','streetNBR',     'test address info','test address city','testcountry_de', '55555',    'custom user order remark','ORDERFOLDER_NEW','BB',           '');

#OrderArticles demodata
REPLACE INTO `oxorderarticles` (`OXID`,                            `OXORDERID`,`OXAMOUNT`,`OXARTID`,`OXARTNUM`,`OXTITLE`) VALUES
                               ('919edbc539f414bdefc7f6975bbdf2a1','testorder', 100,        '1000', '1000',    '[DE 4] Test product 0 šÄßüл'),
                               ('919edbc539f414bdefc7f6975bbdf2b6','testorder', 150,        '1001', '1001',    '[DE 1] Test product 1 šÄßüл');
