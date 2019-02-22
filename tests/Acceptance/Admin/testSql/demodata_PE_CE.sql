SET @@session.sql_mode = '';

# Activate en and de languages
UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba832f744c5786a371d9df33778f956ef30fb1e8bb85d97b3b5de43e6bad688dfc6f63a8af34b33290cdd6fc889c8e77cfee0e8a17ade6b94130fda30d062d03e35d8d1bda1c2dc4dd5281fcb1c9538cf114050a3e7118e16151bfe94f5a0706d2eb3d9ff8b4a24f88963788f5dd1c33c573a1ebe3f5b06c072c6a373aaecb11755d907b50a79bbac613054871af686a7d3dbe0b6e1a3e292a109e2f5bc31bcd26ebbe42dac8c9cac3fa53c6fae3c8c7c3c113a4f1a8823d13c78c27dc WHERE `OXVARNAME` = 'aLanguageParams';

# Set en as default language
UPDATE `oxconfig` SET `OXVARVALUE` = 0x07 WHERE `OXVARNAME` = 'sDefaultLang';

# Activate all coutries
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXISOALPHA2` in ('DE', 'AT', 'CH', 'GB', 'US');

#set country, username, password for default user
UPDATE oxuser
  SET
      oxcountryid = 'a7c40f631fc920687.20179984',
      oxusername = 'admin@myoxideshop.com',
      oxpassword = '6cb4a34e1b66d3445108cd91b67f98b9',
      oxpasssalt = '6631386565336161636139613634663766383538633566623662613036636539'
  WHERE OXUSERNAME='admin';

#Articles demodata
REPLACE INTO `oxarticles` (`OXID`,   `OXSHOPID`,   `OXPARENTID`, `OXACTIVE`, `OXARTNUM`, `OXTITLE`,                     `OXSHORTDESC`,                   `OXPRICE`, `OXPRICEA`, `OXPRICEB`, `OXPRICEC`, `OXTPRICE`, `OXUNITNAME`, `OXUNITQUANTITY`, `OXVAT`, `OXWEIGHT`, `OXSTOCK`, `OXSTOCKFLAG`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`,       `OXDELIVERY`, `OXINSERT`,   `OXTIMESTAMP`,        `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXSEARCHKEYS`, `OXISSEARCH`, `OXVARNAME`,              `OXVARSTOCK`, `OXVARCOUNT`, `OXVARSELECT`, `OXVARMINPRICE`, `OXVARMAXPRICE`, `OXVARNAME_1`,             `OXVARSELECT_1`,   `OXTITLE_1`,                 `OXSHORTDESC_1`,                        `OXSEARCHKEYS_1`, `OXBUNDLEID`, `OXSTOCKTEXT_1`,       `OXNOSTOCKTEXT_1`,         `OXSORT`, `OXVENDORID`,      `OXMANUFACTURERID`, `OXMINDELTIME`, `OXMAXDELTIME`, `OXDELTIMEUNIT`) VALUES
                         ('1000',   1, '',            1,         '1000',     '[DE 4] Test product 0 šÄßüл', 'Test product 0 short desc [DE]', 50,        35,         45,         55,         0,         'kg',          2,                NULL,    2,          15,        1,            'In stock [DE]', 'Out of stock [DE]', '0000-00-00', '2008-02-04', '2008-02-04 17:07:48', 1,          2,         2,         'search1000',    1,           '',                        0,            0,           '',             50,                0,                   '',                        '',                'Test product 0 [EN] šÄßüл', 'Test product 0 short desc [EN] šÄßüл', 'šÄßüл1000',      '',           'In stock [EN] šÄßüл', 'Out of stock [EN] šÄßüл',  0,       'testdistributor', 'testmanufacturer',  1,              1,             'DAY'),
                         ('1001',   1, '',            1,         '1001',     '[DE 1] Test product 1 šÄßüл', 'Test product 1 short desc [DE]', 100,       0,          0,          0,          150,       '',            0,                10,      0,          0,         1,            '',              '',                  '2008-01-01', '2008-02-04', '2008-02-04 17:35:49', 0,          0,         0,         'search1001',    1,           '',                        0,            0,           '',             100,               0,                   '',                        '',                'Test product 1 [EN] šÄßüл', 'Test product 1 short desc [EN] šÄßüл', 'šÄßüл1001',      '',           '',                    '',                         0,       'testdistributor', 'testmanufacturer',  0,              1,             'WEEK'),
                         ('1002',   1, '',            1,         '1002',     '[DE 2] Test product 2 šÄßüл', 'Test product 2 short desc [DE]', 55,        0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            'In stock [DE]', 'Out of stock [DE]', '0000-00-00', '2008-02-04', '2008-02-04 17:18:18', 0,          0,         0,         'search1002',    1,           'variants [DE]',           10,           2,           '',             55,                67,                  'variants [EN] šÄßüл',     '',                'Test product 2 [EN] šÄßüл', 'Test product 2 short desc [EN] šÄßüл', 'šÄßüл1002',      '',           'In stock [EN] šÄßüл', 'Out of stock [EN] šÄßüл',  0,       'testdistributor', 'testmanufacturer',  1,              1,             'MONTH'),
                         ('1003',   1, '',            1,         '1003',     '[DE 3] Test product 3 šÄßüл', 'Test product 3 short desc [DE]', 75,        70,         85,         0,          0,         '',            0,                NULL,    0,          5,         1,            '',              '',                  '0000-00-00', '2008-02-04', '2008-02-04 17:48:38', 0,          0,         0,         'search1003',    1,           '',                        0,            0,           '',             75,                0,                   '',                        '',                'Test product 3 [EN] šÄßüл', 'Test product 3 short desc [EN] šÄßüл', 'šÄßüл1003',      '',           '',                    '',                         0,       '',                'testmanufacturer',  4,              9,             'DAY'),
                         ('1002-1', 1, '1002',        1,         '1002-1',   '',                            '',                               55,        45,         0,          0,          0,         '',            0,                NULL,    0,          5,         1,            'In stock [DE]', 'Out of stock [DE]', '0000-00-00', '2008-02-04', '2008-02-04 17:34:10', 0,          0,         0,         '',              1,           '',                        0,            0,           'var1 [DE]',    0,                 0,                   '',                        'var1 [EN] šÄßüл', '',                          '',                                     '',               '',           'In stock [EN] šÄßüл', 'Out of stock [EN] šÄßüл',  1,       '',                '',                  0,              0,             ''),
                         ('1002-2', 1, '1002',        1,         '1002-2',   '',                            '',                               67,        47,         0,          0,          0,         '',            0,                NULL,    0,          5,         1,            'In stock [DE]', 'Out of stock [DE]', '0000-00-00', '2008-02-04', '2008-02-04 17:34:36', 0,          0,         0,         '',              1,           '',                        0,            0,           'var2 [DE]',    0,                 0,                   '',                        'var2 [EN] šÄßüл', '',                          '',                                     '',               '',           'In stock [EN] šÄßüл', 'Out of stock [EN] šÄßüл',  2,       '',                '',                  0,              0,             ''),
                         ('10010',  1, '',            0,         '10010',    '[last] DE product šÄßüл',     '1 DE description',               1.5,       0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            '',              '',                  '0000-00-00', '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              1,           '',                        0,            0,           '',             1.5,               0,                   '',                        '',                '1 EN product šÄßüл',        '[last] EN description šÄßüл',          '',               '',           '',                    '',                         0,       '',                '',                  0,              0,             ''),
                         ('10011',  1, '',            0,         '10011',    '10 DE product šÄßüл',         '11 DE description',              1.8,       0,          0,          0,          0,         '',            0,                NULL,    2,          0,         1,            '',              '',                  '0000-00-00', '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              1,           '',                        0,            0,           '',             1.8,               0,                   '',                        '',                '11 EN product šÄßüл',       '10 EN description šÄßüл',              '',               '',           '',                    '',                         0,       '',                '',                  0,              0,             ''),
                         ('10012',  1, '',            0,         '10012',    '11 DE product šÄßüл',         '12 DE description',              2.0,       0,          0,          0,          0,         '',            0,                NULL,    3,          0,         1,            '',              '',                  '0000-00-00', '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              1,           '',                        0,            0,           '',             2.0,               0,                   '',                        '',                '12 EN product šÄßüл',       '11 EN description šÄßüл',              '',               '',           '',                    '',                         0,       '',                '',                  0,              0,             ''),
                         ('10013',  1, '',            0,         '10013',    '12 DE product šÄßüл',         '13 DE description',              1.7,       0,          0,          0,          0,         '',            0,                NULL,    20,         0,         1,            '',              '',                  '0000-00-00', '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              1,           '',                        0,            0,           '',             1.7,               0,                   '',                        '',                '13 EN product šÄßüл',       '12 EN description šÄßüл',              '',               '',           '',                    '',                         0,       '',                '',                  0,              0,             ''),
                         ('10014',  1, '',            0,         '10014',    '13 DE product šÄßüл',         '14 DE description',              1.6,       0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            '',              '',                  '0000-00-00', '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              1,           'size[DE] | color | type', 0,            12,          '',             15,               25,                   'size[EN] | color | type', '',                '14 EN product šÄßüл',       '13 EN description šÄßüл',              '',               '',           '',                    '',                         0,       '',                '',                  0,              0,             ''),
                         ('10015',  1, '',            0,         '10015',    '14 DE product šÄßüл',         '15 DE description',              2.1,       0,          0,          0,          0,         '',            0,                NULL,    0,          1,         1,            '',              '',                  '0000-00-00', '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              1,           '',                        0,            0,           '',             2.1,               0,                   '',                        '',                '15 EN product šÄßüл',       '14 EN description šÄßüл',              '',               '',           '',                    '',                         0,       '',                '',                  0,              0,             ''),
                         ('10016',  1, '',            0,         '10016',    '15 DE product šÄßüл',         '10 DE description',              1.9,       0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            '',              '',                  '0000-00-00', '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              1,           '',                        0,            0,           '',             1.9,               0,                   '',                        '',                '10 EN product šÄßüл',       '15 EN description šÄßüл',              '',               '',           '',                    '',                         0,       '',                '',                  0,              0,             '');
REPLACE INTO `oxfiles` (`OXID`, `OXARTID`, `OXFILENAME`, `OXPURCHASEDONLY`) VALUES ('1000l', '1002-1', 'testFile3', '1');
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

#persparam
REPLACE INTO `oxarticles` (`OXID`,  `OXSHOPID`,   `OXPARENTID`, `OXACTIVE`, `OXARTNUM`, `OXTITLE`,          `OXSHORTDESC`,     `OXPRICE`, `OXSTOCKFLAG`, `OXINSERT`,   `OXTIMESTAMP`,        `OXISSEARCH`, `OXVARMINPRICE`, `OXTITLE_1`,        `OXSHORTDESC_1`,    `OXSORT`, `OXISCONFIGURABLE`) VALUES
                         ('20016', 1, '',            1,         '20016',    'perspara DE_prod', 'perspara DE_desc', 1.9,       1,            '2008-04-03', '2008-04-03 12:50:20', 1,            1.9,            'perspara EN_prod', 'perspara EN_desc',  0,       1);
REPLACE INTO `oxartextends` (`OXID`,  `OXLONGDESC`) VALUES
                           ('20016', '');

#Articles long desc
REPLACE INTO `oxartextends` (`OXID`,   `OXLONGDESC`,                                  `OXLONGDESC_1`) VALUES
                           ('1000',   '<p>Test product 0 long description [DE]</p>', '<p>Test product 0 long description [EN] šÄßüл</p>'),
                           ('1001',   '<p>Test product 1 long description [DE]</p>', '<p>Test product 1 long description [EN] šÄßüл</p>'),
                           ('1002',   '<p>Test product 2 long description [DE]</p>', '<p>Test product 2 long description [EN] šÄßüл</p>'),
                           ('1003',   '<p>Test product 3 long description [DE]</p>', '<p>Test product 3 long description [EN] šÄßüл</p>'),
                           ('1002-1', '',                                            ''),
                           ('1002-2', '',                                            ''),
                           ('10010',  '',                                            ''),
                           ('10011',  '',                                            ''),
                           ('10012',  '',                                            ''),
                           ('10013',  '',                                            ''),
                           ('10014',  '',                                            ''),
                           ('10015',  '',                                            ''),
                           ('10016',  '',                                            '');

#Attributes demodata
REPLACE INTO `oxattribute` (`OXID`,           `OXSHOPID`,   `OXTITLE`,                     `OXTITLE_1`,                  `OXPOS`) VALUES
                          ('testattribute1', 1, 'Test attribute 1 [DE] šÄßüл', 'Test attribute 1 [EN] šÄßüл', 1),
                          ('testattribute2', 1, 'Test attribute 2 [DE] šÄßüл', 'Test attribute 2 [EN] šÄßüл', 3),
                          ('testattribute3', 1, 'Test attribute 3 [DE] šÄßüл', 'Test attribute 3 [EN] šÄßüл', 2),
                          ('testattr1',      1, '1 [DE] Attribute šÄßüл',      '[last] [EN] Attribute šÄßüл', 5),
                          ('testattr2',      1, '2 [DE] Attribute šÄßüл',      '3 [EN] Attribute šÄßüл',      4),
                          ('testattr3',      1, '3 [DE] Attribute šÄßüл',      '4 [EN] Attribute šÄßüл',      6),
                          ('testattr4',      1, '4 [DE] Attribute šÄßüл',      '5 [EN] Attribute šÄßüл',      7),
                          ('testattr5',      1, '5 [DE] Attribute šÄßüл',      '6 [EN] Attribute šÄßüл',      9),
                          ('testattr6',      1, '6 [DE] Attribute šÄßüл',      '7 [EN] Attribute šÄßüл',      11),
                          ('testattr7',      1, '7 [DE] Attribute šÄßüл',      '2 [EN] Attribute šÄßüл',      10),
                          ('testattr8',      1, '[last] [DE] Attribute šÄßüл', '1 [EN] Attribute šÄßüл',      8);
UPDATE `oxattribute` SET `OXDISPLAYINBASKET` = 0;

#Categories demodata
REPLACE INTO `oxcategories` (`OXID`,          `OXPARENTID`,   `OXLEFT`, `OXRIGHT`, `OXROOTID`,     `OXSORT`, `OXACTIVE`, `OXSHOPID`,   `OXTITLE`,                    `OXDESC`,                    `OXLONGDESC`,                `OXDEFSORT`, `OXDEFSORTMODE`, `OXPRICEFROM`, `OXPRICETO`, `OXACTIVE_1`, `OXTITLE_1`,                  `OXDESC_1`,                        `OXLONGDESC_1`,                    `OXVAT`, `OXSHOWSUFFIX`) VALUES
                           ('testcategory0', 'oxrootid',      1,        4,        'testcategory0', 1,        1,         1, 'Test category 0 [DE] šÄßüл', 'Test category 0 desc [DE]', 'Category 0 long desc [DE]', 'oxartnum',   0,               0,             0,           1,           'Test category 0 [EN] šÄßüл', 'Test category 0 desc [EN] šÄßüл', 'Category 0 long desc [EN] šÄßüл',  5,       1),
                           ('testcategory1', 'testcategory0', 2,        3,        'testcategory0', 2,        1,         1, 'Test category 1 [DE] šÄßüл', 'Test category 1 desc [DE]', 'Category 1 long desc [DE]', 'oxartnum',   1,               0,             0,           1,           'Test category 1 [EN] šÄßüл', 'Test category 1 desc [EN] šÄßüл', 'Category 1 long desc [EN] šÄßüл',  NULL,    1),
                           ('testcat1',      'oxrootid',      1,        2,        'testcat1',      2,        0,         1, '1 [DE] category šÄßüл',      '',                          '',                          '',           0,               0,             0,           0,           '[last] [EN] category šÄßüл', '',                                 '',                                NULL,    1),
                           ('testcat2',      'oxrootid',      1,        2,        'testcat2',      5,        0,         1, '2 [DE] category šÄßüл',      '',                          '',                          '',           0,               0,             0,           0,           '3 [EN] category šÄßüл',      '',                                 '',                                NULL,    1),
                           ('testcat3',      'oxrootid',      1,        2,        'testcat3',      1,        0,         1, '3 [DE] category šÄßüл',      '',                          '',                          '',           0,               0,             0,           0,           '4 [EN] category šÄßüл',      '',                                 '',                                NULL,    1),
                           ('testcat4',      'oxrootid',      1,        2,        'testcat4',      7,        0,         1, '4 [DE] category šÄßüл',      '',                          '',                          '',           0,               0,             0,           0,           '5 [EN] category šÄßüл',      '',                                 '',                                NULL,    1),
                           ('testcat5',      'oxrootid',      1,        2,        'testcat5',      1,        0,         1, '5 [DE] category šÄßüл',      '',                          '',                          '',           0,               0,             0,           0,           '6 [EN] category šÄßüл',      '',                                 '',                                NULL,    1),
                           ('testcat7',      'oxrootid',      1,        2,        'testcat7',      6,        0,         1, '7 [DE] category šÄßüл',      '',                          '',                          '',           0,               0,             0,           0,           '8 [EN] category šÄßüл',      '',                                 '',                                NULL,    1),
                           ('testcat8',      'oxrootid',      1,        2,        'testcat8',      6,        0,         1, '8 [DE] category šÄßüл',      '',                          '',                          '',           0,               0,             0,           0,           '2 [EN] category šÄßüл',      '',                                 '',                                NULL,    1),
                           ('testcat9',      'oxrootid',      1,        2,        'testcat9',      3,        0,         1, '[last] [DE] category šÄßüл', '',                          '',                          '',           0,               0,             0,           0,           '1 [EN] category šÄßüл',      '',                                 '',                                NULL,    1),
                           ('testpricecat',  'oxrootid',      1,        2,        'testpricecat',  99999,    1,         1, 'price šÄßüл [DE]',           'price category [DE]',       '',                          '',           0,               49,            60,          1,           'price [EN] šÄßüл',           'price category [EN] šÄßüл',       '',                                 NULL,    1);

#Delivery set demodata
REPLACE INTO `oxdeliveryset` (`OXID`,       `OXSHOPID`,  `OXACTIVE`, `OXTITLE`,                      `OXTITLE_1`) VALUES
                            ('testdelset', 1, 1,         'Test S&H set [DE] šÄßüл',      'Test S&H set [EN] šÄßüл'),
                            ('testshset1', 1, 0,         '1 DE test S&H set šÄßüл',      '[last] EN test S&H set šÄßüл'),
                            ('testshset2', 1, 0,         '2 DE test S&H set šÄßüл',      '3 EN test S&H set šÄßüл'),
                            ('testshset3', 1, 0,         '3 DE test S&H set šÄßüл',      '4 EN test S&H set šÄßüл'),
                            ('testshset5', 1, 0,         '5 DE test S&H set šÄßüл',      '6 EN test S&H set šÄßüл'),
                            ('testshset4', 1, 0,         '4 DE test S&H set šÄßüл',      '2 EN test S&H set šÄßüл'),
                            ('testshset7', 1, 0,         '[last] DE test S&H set šÄßüл', '1 EN test S&H set šÄßüл'),
                            ('testshset8', 1, 0,         '7 DE test S&H set šÄßüл',      '7 EN test S&H set šÄßüл');

#Delivery demodata
#UPDATE `oxdelivery` SET `OXTITLE_1` = `OXTITLE`;
REPLACE INTO `oxdelivery` (`OXID`,       `OXSHOPID`,  `OXACTIVE`, `OXTITLE`,                           `OXTITLE_1`,                         `OXADDSUMTYPE`, `OXADDSUM`, `OXDELTYPE`, `OXPARAM`, `OXPARAMEND`, `OXFIXED`, `OXSORT`, `OXFINALIZE`) VALUES
                         ('testdel',    1, 1,         'Test delivery category [DE] šÄßüл', 'Test delivery category [EN] šÄßüл', 'abs',           1.5,       'a',          1,         99999,        0,         9998,     1),
                         ('testdelart', 1, 1,         'Test delivery product [DE] šÄßüл',  'Test delivery product [EN] šÄßüл',  '%',             1,         'a',          1,         99999,        0,         9999,     0),
                         ('testsh1',    1, 0,         '1 DE S&H šÄßüл',                    '[last] EN S&H šÄßüл',               'abs',           10,        'w',          15,        999,          2,         4,        0),
                         ('testsh2',    1, 0,         '2 DE S&H šÄßüл',                    '3 EN S&H šÄßüл',                    'abs',           1,         'w',          1,         4.99999999,   2,         1,        0),
                         ('testsh3',    1, 0,         '3 DE S&H šÄßüл',                    '4 EN S&H šÄßüл',                    'abs',           0,         'a',          0,         0,            0,         999999,   0),
                         ('testsh5',    1, 0,         '[last] DE S&H šÄßüл',               '1 EN S&H šÄßüл',                    'abs',           5,         'w',          5,         14.9999999,   2,         2,        0);

#Coupons demodata
REPLACE INTO `oxvoucherseries` (`OXID`,         `OXSHOPID`,   `OXSERIENR`,           `OXSERIEDESCRIPTION`,      `OXDISCOUNT`, `OXDISCOUNTTYPE`, `OXBEGINDATE`,         `OXENDDATE`,          `OXALLOWSAMESERIES`, `OXALLOWOTHERSERIES`, `OXALLOWUSEANOTHER`, `OXMINIMUMVALUE`, `OXCALCULATEONCE`) VALUES
                              ('testcoupon1',  1, 'Test coupon 1 šÄßüл', 'Test coupon 1 desc šÄßüл', 10.00,       'absolute',       '2008-01-01 00:00:00', '2020-01-01 00:00:00', 1,                   1,                    1,                   75.00,            1),
                              ('testcoupon2',  1, 'Test coupon 2 šÄßüл', 'Test coupon 2 desc šÄßüл', 5.00,        'percent',        '2008-01-01 00:00:00', '2020-01-01 00:00:00', 0,                   0,                    0,                   75.00,            1),
                              ('testvoucher1', 1, '1 Coupon šÄßüл',      '1 Description šÄßüл',      5.00,        'absolute',       '2007-01-01 00:00:00', '2020-12-31 00:00:00', 1,                   1,                    1,                   10.00,            1),
                              ('testvoucher2', 1, '2 Coupon šÄßüл',      '2 Coupon šÄßüл',           3.00,        'absolute',       '2009-01-01 00:00:00', '2020-10-10 00:00:00', 1,                   1,                    1,                   25.00,            0),
                              ('testvoucher3', 1, '3 Coupon šÄßüл',      '3 Coupon šÄßüл',           15.00,       'percent',        '2007-12-31 00:00:00', '2019-12-31 00:00:00', 0,                   0,                    0,                   100.00,           1),
                              ('testvoucher4', 1, '4 Coupon šÄßüл',      '4 Coupon šÄßüл',           50.00,       'percent',        '2008-01-01 00:00:00', '2020-01-01 00:00:00', 0,                   0,                    0,                   45.00,            1),
                              ('testvoucher5', 1, '5 Coupon šÄßüл',      '5 Coupon šÄßüл',           30.00,       'percent',        '2008-01-01 00:00:00', '2020-01-01 00:00:00', 0,                   0,                    0,                   300.00,           1),
                              ('testvoucher6', 1, '6 Coupon šÄßüл',      '6 Coupon šÄßüл',           20.00,       'percent',        '2008-01-01 00:00:00', '2020-01-01 00:00:00', 0,                   0,                    0,                   300.00,           1),
                              ('testvoucher7', 1, '7 Coupon šÄßüл',      '7 Coupon šÄßüл',           25.00,       'absolute',       '2008-01-01 00:00:00', '2020-01-01 00:00:00', 0,                   0,                    0,                   300.00,           1),
                              ('testvoucher8', 1, '8 Coupon šÄßüл',      '8 Coupon šÄßüл',           54.00,       'absolute',       '2008-01-01 00:00:00', '2020-01-01 00:00:00', 0,                   0,                    0,                   300.00,           1),
                              ('testvoucher9', 1, '[last] Coupon šÄßüл', '9 Coupon šÄßüл',           64.00,       'absolute',       '2008-01-01 00:00:00', '2020-01-01 00:00:00', 0,                   0,                    0,                   300.00,           1);

REPLACE INTO `oxvouchers` (`OXDATEUSED`, `OXRESERVED`, `OXVOUCHERNR`, `OXVOUCHERSERIEID`, `OXID`) VALUES
                         ('0000-00-00',  0,           '111111',      'testcoupon1',      'testvoucher001'),
                         ('0000-00-00',  0,           '111111',      'testcoupon1',      'testvoucher002'),
                         ('0000-00-00',  0,           '111111',      'testcoupon1',      'testvoucher003'),
                         ('0000-00-00',  0,           '111111',      'testcoupon1',      'testvoucher004'),
                         ('0000-00-00',  0,           '111111',      'testcoupon1',      'testvoucher005'),
                         ('0000-00-00',  0,           '222222',      'testcoupon2',      'testvoucher006'),
                         ('0000-00-00',  0,           '222222',      'testcoupon2',      'testvoucher007'),
                         ('0000-00-00',  0,           '222222',      'testcoupon2',      'testvoucher008'),
                         ('0000-00-00',  0,           '222222',      'testcoupon2',      'testvoucher009'),
                         ('0000-00-00',  0,           '222222',      'testcoupon2',      'testvoucher010'),
                         ('0000-00-00',  0,           '123123',      'testvoucher4',     'testcoucher011'),
                         ('0000-00-00',  0,           'test111',     'testvoucher1',     'testcoucher012'),
                         ('0000-00-00',  0,           'test222',     'testvoucher2',     'testcoucher013');

#News demodata
REPLACE INTO `oxnews` (`OXID`,       `OXSHOPID`,  `OXACTIVE`, `OXDATE`,     `OXSHORTDESC`,                 `OXLONGDESC`,                  `OXACTIVE_1`, `OXSHORTDESC_1`,               `OXLONGDESC_1`) VALUES
                     ('testnews1',  1, 1,         '2008-01-01', 'Test news 1 [DE] šÄßüл',      '<p>Test news text 1 [DE]</p>', 1,           'Test news 1 [EN] šÄßüл',      '<p>Test news text 1 [EN] šÄßüл</p>'),
                     ('testnews2',  1, 1,         '2008-01-02', 'Test news 2 [DE] šÄßüл',      '<p>Test news text 2 [DE]</p>', 1,           'Test news 2 [EN] šÄßüл',      '<p>Test news text 2 [EN] šÄßüл</p>'),
                     ('testnews3',  1, 0,         '2007-11-02', '1 [DE] Test news šÄßüл',      '<p>Test news text</p>',        0,           '[last] [EN] Test news šÄßüл', '<p>Test news text</p>'),
                     ('testnews4',  1, 0,         '2008-01-05', '2 [DE] Test news šÄßüл',      '<p>Test news text</p>',        0,           '8 [EN] Test news šÄßüл',      '<p>Test news text</p>'),
                     ('testnews5',  1, 0,         '2007-12-02', '3 [DE] Test news šÄßüл',      '<p>Test news text</p>',        0,           '1 [EN] Test news šÄßüл',      '<p>Test news text</p>'),
                     ('testnews6',  1, 0,         '2008-01-02', '4 [DE] Test news šÄßüл',      '<p>Test news text</p>',        0,           '2 [EN] Test news šÄßüл',      '<p>Test news text</p>'),
                     ('testnews7',  1, 0,         '2007-12-20', '5 [DE] Test news šÄßüл',      '<p>Test news text</p>',        0,           '3 [EN] Test news šÄßüл',      '<p>Test news text</p>'),
                     ('testnews8',  1, 0,         '2008-02-03', '6 [DE] Test news šÄßüл',      '<p>Test news text</p>',        0,           '4 [EN] Test news šÄßüл',      '<p>Test news text</p>'),
                     ('testnews9',  1, 0,         '2008-02-02', '7 [DE] Test news šÄßüл',      '<p>Test news text</p>',        0,           '5 [EN] Test news šÄßüл',      '<p>Test news text</p>'),
                     ('testnews10', 1, 0,         '2008-01-17', '8 [DE] Test news šÄßüл',      '<p>Test news text</p>',        0,           '6 [EN] Test news šÄßüл',      '<p>Test news text</p>'),
                     ('testnews11', 1, 0,         '2008-02-12', '[last] [DE] Test news šÄßüл', '<p>Test news text</p>',        0,           '7 [EN] Test news šÄßüл',      '<p>Test news text</p>');
#News subscribbed demodata
REPLACE INTO `oxnewssubscribed` (`OXID`,                       `OXSHOPID`,   `OXUSERID`,  `OXSAL`, `OXFNAME`,   `OXLNAME`,      `OXEMAIL`,           `OXDBOPTIN`, `OXSUBSCRIBED`) VALUES
                               ('aad47a859fa2fd5d2.73169490', 1, 'testuser',  'Mr',    'UserName',  'UserSurname',  'example_test@oxid-esales.dev', 0,          '2008-02-05 14:43:38'),
                               ('aad47a85b24b7fbc4.83843468', 1, 'testusera', 'Mrs',   'UserAName', 'UserASurname', 'example0a@oxid-esales.dev',    0,          '2008-02-05 14:48:36'),
                               ('aad47a862e176ec58.80688736', 1, 'testuserb', 'Mr',    'UserBName', 'UserBSurname', 'example0b@oxid-esales.dev',    0,          '2008-02-05 15:21:37'),
                               ('aad47a86412506061.78359483', 1, 'testuserc', 'Mrs',   'UserCName', 'UserCSurname', 'example0c@oxid-esales.dev',    0,          '2008-02-05 15:26:42'),
                               ('15947a85ab4658ce7.11111111', 1, 'testuser1',  'Mr',   'UserName',  'UserSurname',  'example_test@oxid-esales.dev', 0,          '2008-02-05 14:46:44'),
                               ('15947a85ab4658ce7.22222222', 1, 'testuser2',  'Mr',   'UserName',  'UserSurname',  'example_test@oxid-esales.dev', 0,          '2008-02-05 14:46:44'),

                               ('15947a85ab4658ce7.33333333', 1, 'testuser3',  'Mr',   'UserName',  'UserSurname',  'example_test@oxid-esales.dev', 0,          '2008-02-05 14:46:44'),

                               ('15947a85ab4658ce7.44444444', 1, 'testuser3',  'Mr',   'UserName',  'UserSurname',  'example_test@oxid-esales.dev', 0,          '2008-02-05 14:46:44'),

                               ('15947a85ab4658ce7.55555555', 1, 'testuser5',  'Mr',   'UserName',  'UserSurname',  'example_test@oxid-esales.dev', 0,          '2008-02-05 14:46:44'),

                               ('15947a85ab4658ce7.66666666', 1, 'testuser6',  'Mr',   'UserName',  'UserSurname',  'example_test@oxid-esales.dev', 0,          '2008-02-05 14:46:44'),

                               ('15947a85ab4658ce7.77777777', 1, 'testuser7',  'Mr',   'UserName',  'UserSurname',  'example_test@oxid-esales.dev', 0,          '2008-02-05 14:46:44'),

                               ('15947a85ab4658ce7.88888888', 1, 'testuser8',  'Mr',   'UserName',  'UserSurname',  'example_test@oxid-esales.dev', 0,          '2008-02-05 14:46:44'),

                               ('15947a85ab4658ce7.99999999', 1, 'testuser9',  'Mr',   'UserName',  'UserSurname',  'example_test@oxid-esales.dev', 0,          '2008-02-05 14:46:44'),

                               ('15947a85ab4658ce7.11111110', 1, 'testuser10', 'Mr',   'UserName',  'UserSurname',  'example_test@oxid-esales.dev', 0,          '2008-02-05 14:46:44');

#Users demodata
REPLACE INTO `oxuser` (`OXID`,     `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`,   `OXUSERNAME`,       `OXPASSWORD`,                                                   `OXPASSSALT`,                         `OXCUSTNR`, `OXUSTID`, `OXCOMPANY`,          `OXFNAME`,        `OXLNAME`,           `OXSTREET`,        `OXSTREETNR`, `OXADDINFO`,                   `OXCITY`,            `OXCOUNTRYID`,                `OXZIP`, `OXFON`,        `OXFAX`,       `OXSAL`, `OXBONI`, `OXCREATE`,            `OXREGISTER`,          `OXPRIVFON`,   `OXMOBFON`,    `OXBIRTHDATE`) VALUES
                     ('testuser',  1,         'user',     1, 'example_test@oxid-esales.dev', '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef',   8,         '',        'UserCompany šÄßüл',  'UserNamešÄßüл',  'UserSurnamešÄßüл',  'Musterstr.šÄßüл', '1',          'User additional info šÄßüл',  'Musterstadt šÄßüл', 'a7c40f631fc920687.20179984', '79098',  '0800 111111', '0800 111112', 'Mr',     500,     '2008-02-05 14:42:42', '2008-02-05 14:42:42', '0800 111113', '0800 111114', '1980-01-01'),
                     ('testusera', 1,         'user',     1, 'example0a@oxid-esales.dev',    '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef',   9,         '',        'UserACompany šÄßüл', 'UserANamešÄßüл', 'UserASurnamešÄßüл', 'Musterstr.šÄßüл', '2',          'UserA additional info šÄßüл', 'Musterstadt šÄßüл', 'a7c40f631fc920687.20179984', '79098',  '0800 222221', '0800 222222', 'Mrs',    0,       '2008-02-05 14:49:31', '2008-02-05 14:49:31', '0800 222223', '0800 222224', '1960-02-02'),
                     ('testuserb', 1,         'user',     1, 'example0b@oxid-esales.dev',    '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef',   10,        '',        'UserBCompany šÄßüл', 'UserBNamešÄßüл', 'UserBSurnamešÄßüл', 'Musterstr.šÄßüл', '3',          'UserB additional info šÄßüл', 'Musterstadt šÄßüл', 'a7c40f631fc920687.20179984', '79098',  '0800 333331', '0800 333332', 'Mr',     0,       '2008-02-05 15:19:46', '2008-02-05 15:19:46', '0800 333333', '0800 333334', '1952-03-03'),
                     ('testuserc', 1,         'user',     1, 'example0c@oxid-esales.dev',    '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef',   11,        '',        'UserCCompany šÄßüл', 'UserCNamešÄßüл', 'UserCSurnamešÄßüл', 'Musterstr.šÄßüл', '4',          'UserC additional info šÄßüл', 'Musterstadt šÄßüл', 'a7c40f631fc920687.20179984', '79098',  '0800 444441', '0800 444442', 'Mrs',    0,       '2008-02-05 15:26:06', '2008-02-05 15:26:06', '0800 444443', '0800 444444', '1985-04-04'),
                     ('testuser1', 0,         'user',     1, 'example02@oxid-esales.dev',    '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef',   12,        '',        '',                   '1useršÄßüл',     '1UserSurnamešÄßüл', '1 Street.šÄßüл',  '1',          '',                            '2 City šÄßüл',      'a7c40f631fc920687.20179984', '333000', '444444',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2008-02-01 00:00:00', '',            '',            '0000-00-00'),
                     ('testuser2', 0,         'user',     1, 'example03@oxid-esales.dev',    '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef',   13,        '',        '',                   '2useršÄßüл',     '2UserSurnamešÄßüл', '2 Street.šÄßüл',  '1',          '',                            '3 City šÄßüл',      'a7c40f631fc920687.20179984', '444000', '555555',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2008-01-10 00:00:00', '',            '',            '0000-00-00'),
                     ('testuser3', 0,         'user',     1, 'example07@oxid-esales.dev',    '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef',   14,        '',        '',                   '6useršÄßüл',     '6UserSurnamešÄßüл', '3 Street.šÄßüл',  '1',          '',                            '4 City šÄßüл',      'a7c40f631fc920687.20179984', '555000', '666666',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2008-01-10 11:10:00', '',            '',            '0000-00-00'),
                     ('testuser4', 0,         'user',     1, 'example05@oxid-esales.dev',    '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef',   15,        '',        '',                   '4useršÄßüл',     '4UserSurnamešÄßüл', '4 Street.šÄßüл',  '1',          '',                            '5 City šÄßüл',      'a7c40f631fc920687.20179984', '666000', '777777',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2008-01-10 00:00:01', '',            '',            '0000-00-00'),
                     ('testuser5', 0,         'user',     1, 'example08@oxid-esales.dev',    '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef',   16,        '',        '',                   '7useršÄßüл',     '7UserSurnamešÄßüл', '5 Street.šÄßüл',  '1',          '',                            '6 City šÄßüл',      'a7c40f631fc920687.20179984', '777000', '111111',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2008-01-10 00:00:02', '',            '',            '0000-00-00'),
                     ('testuser6', 0,         'user',     1, 'example04@oxid-esales.dev',    '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef',   17,        '',        '',                   '3useršÄßüл',     '3UserSurnamešÄßüл', '6 Street.šÄßüл',  '1',          '',                            '7 City šÄßüл',      'a7c40f631fc920687.20179984', '111000', '222222',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2008-01-10 00:00:03', '',            '',            '0000-00-00'),
                     ('testuser7', 0,         'user',     1, 'example06@oxid-esales.dev',    '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef',   18,        '',        '',                   '5useršÄßüл',     '5UserSurnamešÄßüл', '7 Street.šÄßüл',  '1',          '',                            '1 City šÄßüл',      'a7c40f631fc920687.20179984', '222000', '333333',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2007-06-20 00:00:00', '',            '',            '0000-00-00');

#Select list demodata
REPLACE INTO `oxselectlist` (`OXID`,          `OXSHOPID`,   `OXTITLE`,                        `OXIDENT`,              `OXVALDESC`,                                                                      `OXTITLE_1`,                      `OXVALDESC_1`) VALUES
                           ('testsellist',   1, 'test selection list [DE] šÄßüл', 'test sellist šÄßüл',   'selvar1 [DE]!P!1__@@selvar2 [DE]__@@selvar3 [DE]!P!-2__@@selvar4 [DE]!P!2%__@@', 'test selection list [EN] šÄßüл', 'selvar1 [EN] šÄßüл!P!1__@@selvar2 [EN] šÄßüл__@@selvar3 [EN] šÄßüл!P!-2__@@selvar4 [EN] šÄßüл!P!2%__@@'),
                           ('testsellist1',  1, '1 [DE] sellist šÄßüл',           '1 sellist šÄßüл',      '',                                                                               '[last] [EN] sellist šÄßüл',      ''),
                           ('testsellist2',  1, '2 [DE] sellist šÄßüл',           '6 sellist šÄßüл',      '',                                                                               '7 [EN] sellist šÄßüл',           ''),
                           ('testsellist3',  1, '3 [DE] sellist šÄßüл',           '2 sellist šÄßüл',      '',                                                                               '6 [EN] sellist šÄßüл',           ''),
                           ('testsellist4',  1, '4 [DE] sellist šÄßüл',           '9 sellist šÄßüл',      '',                                                                               '2 [EN] sellist šÄßüл',           ''),
                           ('testsellist5',  1, '5 [DE] sellist šÄßüл',           '4 sellist šÄßüл',      '',                                                                               '3 [EN] sellist šÄßüл',           ''),
                           ('testsellist6',  1, '6 [DE] sellist šÄßüл',           '8 sellist šÄßüл',      '',                                                                               '5 [EN] sellist šÄßüл',           ''),
                           ('testsellist7',  1, '7 [DE] sellist šÄßüл',           '3 sellist šÄßüл',      '',                                                                               '4 [EN] sellist šÄßüл',           ''),
                           ('testsellist8',  1, '8 [DE] sellist šÄßüл',           '[last] sellist šÄßüл', '',                                                                               '8 [EN] sellist šÄßüл',           ''),
                           ('testsellist9',  1, '9 [DE] sellist šÄßüл',           '5 sellist šÄßüл',      '',                                                                               '1 [EN] sellist šÄßüл',           ''),
                           ('testsellist10', 1, '[last] [DE] sellist šÄßüл',      '7 sellist šÄßüл',      '',                                                                               '9 [EN] sellist šÄßüл',           '');

#Vendors demodata
REPLACE INTO `oxvendor` (`OXID`,             `OXSHOPID`,  `OXACTIVE`, `OXTITLE`,                     `OXSHORTDESC`,                        `OXTITLE_1`,                   `OXSHORTDESC_1`,                          `OXSHOWSUFFIX`) VALUES
                       ('testdistributor',  1, 1,         'Distributor [DE] šÄßüл',      'Distributor description [DE]',       'Distributor [EN] šÄßüл',      'Distributor description [EN] šÄßüл',      1),
                       ('testdistributor1', 1, 0,         '1 DE distributor šÄßüл',      '[last] DE distributor description',  '[last] EN distributor šÄßüл', '1 EN distributor description šÄßüл',      1),
                       ('testdistributor2', 1, 0,         '2 DE distributor šÄßüл',      '4 DE distributor description',       '4 EN distributor šÄßüл',      '2 EN distributor description šÄßüл',      1),
                       ('testdistributor4', 1, 0,         '4 DE distributor šÄßüл',      '2 DE distributor description',       '2 EN distributor šÄßüл',      '4 EN distributor description šÄßüл',      1),
                       ('testdistributor5', 1, 0,         '5 DE distributor šÄßüл',      '7 DE distributor description',       '7 EN distributor šÄßüл',      '5 EN distributor description šÄßüл',      1),
                       ('testdistributor6', 1, 0,         '6 DE distributor šÄßüл',      '6 DE distributor description',       '6 EN distributor šÄßüл',      '6 EN distributor description šÄßüл',      1),
                       ('testdistributor7', 1, 0,         '7 DE distributor šÄßüл',      '3 DE distributor description',       '3 EN distributor šÄßüл',      '7 EN distributor description šÄßüл',      1),
                       ('testdistributor8', 1, 0,         '[last] DE distributor šÄßüл', '1 DE distributor description',       '1 EN distributor šÄßüл',      '[last] EN distributor description šÄßüл', 1),
                       ('testdistributor3', 1, 0,         '3 DE distributor šÄßüл',      '5 DE distributor description',       '5 EN distributor šÄßüл',      '3 EN distributor description šÄßüл',      1),
                       ('testdistributor9', 1, 1,         'www.DE_distributor šÄßüл',    'www.DE_distributor description',     'some_EN_distributor šÄßüл',   '',                                        0);

#Manufacturers demodata
REPLACE INTO `oxmanufacturers` (`OXID`,              `OXSHOPID`,  `OXACTIVE`, `OXTITLE`,                      `OXSHORTDESC`,                        `OXTITLE_1`,                    `OXSHORTDESC_1`,                           `OXSHOWSUFFIX`) VALUES
                              ('testmanufacturer',  1, 1,         'Manufacturer [DE] šÄßüл',      'Manufacturer description [DE]',      'Manufacturer [EN] šÄßüл',      'Manufacturer description [EN] šÄßüл',      1),
                              ('testmanufacturer1', 1, 0,         '1 DE manufacturer šÄßüл',      '[last] DE manufacturer description', '[last] EN manufacturer šÄßüл', '1 EN manufacturer description šÄßüл',      1),
                              ('testmanufacturer2', 1, 0,         '2 DE manufacturer šÄßüл',      '4 DE manufacturer description',      '4 EN manufacturer šÄßüл',      '2 EN manufacturer description šÄßüл',      1),
                              ('testmanufacturer4', 1, 0,         '4 DE manufacturer šÄßüл',      '2 DE manufacturer description',      '2 EN manufacturer šÄßüл',      '4 EN manufacturer description šÄßüл',      1),
                              ('testmanufacturer5', 1, 0,         '5 DE manufacturer šÄßüл',      '7 DE manufacturer description',      '7 EN manufacturer šÄßüл',      '5 EN manufacturer description šÄßüл',      1),
                              ('testmanufacturer6', 1, 0,         '6 DE manufacturer šÄßüл',      '6 DE manufacturer description',      '6 EN manufacturer šÄßüл',      '6 EN manufacturer description šÄßüл',      1),
                              ('testmanufacturer7', 1, 0,         '7 DE manufacturer šÄßüл',      '3 DE manufacturer description',      '3 EN manufacturer šÄßüл',      '7 EN manufacturer description šÄßüл',      1),
                              ('testmanufacturer8', 1, 0,         '[last] DE manufacturer šÄßüл', '1 DE manufacturer description',      '1 EN manufacturer šÄßüл',      '[last] EN manufacturer description šÄßüл', 1),
                              ('testmanufacturer3', 1, 0,         '3 DE manufacturer šÄßüл',      '5 DE manufacturer description',      '5 EN manufacturer šÄßüл',      '3 EN manufacturer description šÄßüл',      1);

#Gift wrapping demodata
REPLACE INTO `oxwrapping` (`OXID`,         `OXSHOPID`,  `OXACTIVE`, `OXACTIVE_1`, `OXACTIVE_2`, `OXACTIVE_3`, `OXTYPE`, `OXNAME`,                        `OXNAME_1`,                     `OXPRICE`) VALUES
                         ('testwrapping', 1, 1,          1,            1,            1,           'WRAP',   'Test wrapping [DE] šÄßüл',      'Test wrapping [EN] šÄßüл',      0.9),
                         ('testcard',     1, 1,          1,            1,            1,           'CARD',   'Test card [DE] šÄßüл',          'Test card [EN] šÄßüл',          0.2),
                         ('testwrap2',    1, 0,          0,            0,            0,           'WRAP',   '2 DE Gift Wrapping šÄßüл',      '4 EN Gift Wrapping šÄßüл',      2),
                         ('testwrap1',    1, 0,          0,            0,            0,           'WRAP',   '1 DE Gift Wrapping šÄßüл',      '3 EN Gift Wrapping šÄßüл',      1),
                         ('testwrap3',    1, 0,          0,            0,            0,           'WRAP',   '3 DE Gift Wrapping šÄßüл',      '1 EN Gift Wrapping šÄßüл',      3),
                         ('testwrap4',    1, 0,          0,            0,            0,           'WRAP',   '4 DE Gift Wrapping šÄßüл',      '[last] EN Gift Wrapping šÄßüл', 4),
                         ('testwrap5',    1, 0,          0,            0,            0,           'WRAP',   '[last] DE Gift Wrapping šÄßüл', '2 EN Gift Wrapping šÄßüл',      5);

#Payments demodata
REPLACE INTO `oxpayments` (`OXID`,         `OXACTIVE`, `OXDESC`,                        `OXADDSUM`, `OXADDSUMTYPE`, `OXFROMBONI`, `OXFROMAMOUNT`, `OXTOAMOUNT`, `OXVALDESC`,             `OXCHECKED`, `OXDESC_1`,                       `OXVALDESC_1`,                  `OXLONGDESC`,                     `OXLONGDESC_1`,                        `OXSORT`) VALUES
                         ('testpayment',   1,         'Test payment method [DE] šÄßüл', 0.7,       'abs',           0,            55,             99999,       'payment field [DE]__@@', 0,          'Test payment method [EN] šÄßüл', 'payment field [EN] šÄßüл__@@', 'Short payment description [DE]', 'Short payment description [EN] šÄßüл', 0),
                         ('testpayment3',  0,         '3 DE test payment šÄßüл',        0,         'abs',           0,            0,              0,           '',                       0,          '2 EN test payment šÄßüл',        '',                             '',                               '',                                     1),
                         ('testpayment2',  0,         '2 DE test payment šÄßüл',        0,         'abs',           0,            0,              0,           '',                       0,          '3 EN test payment šÄßüл',        '',                             '',                               '',                                     2),
                         ('testpayment1',  0,         '1 DE test payment šÄßüл',        0,         'abs',           0,            0,              0,           '',                       0,          '[last] EN test payment šÄßüл',   '',                             '',                               '',                                     3),
                         ('testpayment4',  0,         '[last] DE test payment šÄßüл',   0,         'abs',           0,            0,              0,           '',                       0,          '1 EN test payment šÄßüл',        '',                             '',                               '',                                     4),
                         ('testpayment5',  0,         '4 DE test payment šÄßüл',        0,         'abs',           0,            0,              0,           '',                       0,          '4 EN test payment šÄßüл',        '',                             '',                               '',                                     5),
                         ('testpayment6',  0,         '5 DE test payment šÄßüл',        0,         'abs',           0,            0,              0,           '',                       0,          '5 EN test payment šÄßüл',        '',                             '',                               '',                                     6),
                         ('testpayment7',  0,         '6 DE test payment šÄßüл',        0,         'abs',           0,            0,              0,           '',                       0,          '6 EN test payment šÄßüл',        '',                             '',                               '',                                     7),
                         ('testpayment8',  0,         '7 DE test payment šÄßüл',        0,         'abs',           0,            0,              0,           '',                       0,          '7 EN test payment šÄßüл',        '',                             '',                               '',                                     8),
                         ('testpayment9',  0,         '8 DE test payment šÄßüл',        0,         'abs',           0,            0,              0,           '',                       0,          '8 EN test payment šÄßüл',        '',                             '',                               '',                                     9),
                         ('testpayment10', 0,         '9 DE test payment šÄßüл',        0,         'abs',           0,            0,              0,           '',                       0,          '9 EN test payment šÄßüл',        '',                             '',                               '',                                     10);

#Discount demodata
REPLACE INTO `oxdiscount` (`OXID`,            `OXSHOPID`,  `OXACTIVE`, `OXTITLE`,                          `OXTITLE_1`,                        `OXAMOUNT`, `OXAMOUNTTO`, `OXPRICETO`, `OXPRICE`, `OXADDSUMTYPE`, `OXADDSUM`, `OXITMARTID`, `OXITMAMOUNT`, `OXITMMULTIPLE`, `OXSORT`) VALUES
                         ('testcatdiscount', 1, 1,         'discount for category [DE] šÄßüл', 'discount for category [EN] šÄßüл',  1,          999999,       0,           0,        'abs',           5,         '',            0,             0, 100),
                         ('testartdiscount', 1, 1,         'discount for product [DE] šÄßüл',  'discount for product [EN] šÄßüл',   0,          0,            999999,      100,      '%',             10,        '',            0,             0, 110),
                         ('testdiscount4',   1, 0,         '4 DE test discount šÄßüл',         '4 EN test discount šÄßüл',          0,          0,            0,           0,        'abs',           0,         '',            0,             0, 120),
                         ('testdiscount3',   1, 0,         '3 DE test discount šÄßüл',         '2 EN test discount šÄßüл',          0,          0,            0,           0,        'abs',           0,         '',            0,             0, 130),
                         ('testdiscount1',   1, 1,         '1 DE test discount šÄßüл',         '[last] EN test discount šÄßüл',     0,          999999,       999999,      0,        'abs',           5,         '',            0,             0, 140),
                         ('testdiscount2',   1, 0,         '2 DE test discount šÄßüл',         '3 EN test discount šÄßüл',          0,          0,            0,           0,        'abs',           0,         '',            0,             0, 150),
                         ('testdiscount5',   1, 0,         '[last] DE test discount šÄßüл',    '1 EN test discount šÄßüл',          1,          999999,       0,           0,        'abs',           10,        '',            0,             0, 160),
                         ('testitmdiscount', 1, 1,         'Itm discount [DE] šÄßüл',          'Itm discount [EN] šÄßüл',           5,          999999,       0,           0,        'itm',           0,         '1003',        1,             0, 170);

#Links demodata
REPLACE INTO `oxlinks` (`OXID`,      `OXSHOPID`,  `OXACTIVE`, `OXURL`,                    `OXURLDESC`,                          `OXURLDESC_1`,                        `OXINSERT`) VALUES
                      ('testlink',  1, 1,         'http://www.google.com',    'Demo link description [DE] šÄßüл',   'Demo link description [EN] šÄßüл',   '2008-01-01 00:00:01'),
                      ('testlink1', 1, 0,         'http://www.1google.com',   '1 [DE] link description šÄßüл',      '4 [EN] link description šÄßüл',      '2008-01-01 00:00:04'),
                      ('testlink2', 1, 0,         'http://www.2google.com',   '2 [DE] link description šÄßüл',      '5 [EN] link description šÄßüл',      '2008-02-01 00:00:01'),
                      ('testlink3', 1, 0,         'http://www.3google.com',   '3 [DE] link description šÄßüл',      '6 [EN] link description šÄßüл',      '2004-01-01 00:00:02'),
                      ('testlink4', 1, 0,         'http://www.4google.com',   '4 [DE] link description šÄßüл',      '7 [EN] link description šÄßüл',      '2007-12-10 00:00:01'),
                      ('testlink5', 1, 0,         'http://www.5google.com',   '5 [DE] link description šÄßüл',      '8 [EN] link description šÄßüл',      '2008-01-01 00:00:02'),
                      ('testlink6', 1, 0,         'http://www.6google.com',   '6 [DE] link description šÄßüл',      '[last] [EN] link description šÄßüл', '2008-02-13 00:00:01'),
                      ('testlink7', 1, 0,         'http://www.zzzgoogle.com', '7 [DE] link description šÄßüл',      '1 [EN] link description šÄßüл',      '2008-01-17 00:00:01'),
                      ('testlink8', 1, 0,         'http://www.zzgoogle.com',  '8 [DE] link description šÄßüл',      '2 [EN] link description šÄßüл',      '2008-03-01 00:00:01'),
                      ('testlink9', 1, 0,         'http://www.zgoogle.com',   '[last] [DE] link description šÄßüл', '3 [EN] link description šÄßüл',      '2008-01-01 00:00:03');

#Countries demodata
REPLACE INTO `oxcountry` (`OXID`,        `OXACTIVE`, `OXTITLE`,                     `OXISOALPHA2`, `OXISOALPHA3`, `OXUNNUM3`, `OXORDER`, `OXSHORTDESC`,                 `OXTITLE_1`,                    `OXSHORTDESC_1`,                     `OXVATSTATUS`) VALUES
                        ('testcountry1', 0,         '1 DE test Country šÄßüл',      '',           '111',         '',          0,        '[last] DE test Country desc', '[last] EN test Country šÄßüл', '1 EN test Country desc šÄßüл',       0),
                        ('testcountry2', 0,         '[last] DE test Country šÄßüл', '',           '000',         '',          0,        '1 DE test Country desc',      '1 EN test Country šÄßüл',      '[last] EN test Country desc šÄßüл',  0);

#category2attribute
REPLACE INTO `oxcategory2attribute` (`OXID`,                       `OXOBJECTID`,    `OXATTRID`,      `OXSORT`) VALUES
                                   ('aad47a8513455b7d6.10119791', 'testcategory0', 'testattribute3', 2),
                                   ('aad47a85137a4df07.58761869', 'testcategory0', 'testattribute2', 3),
                                   ('aad47a8513b78e399.56325257', 'testcategory0', 'testattribute1', 1);

#Delivery2DeliverySet
REPLACE INTO `oxdel2delset` (`OXID`,                       `OXDELID`,    `OXDELSETID`) VALUES
                           ('aad47a84ad9a58285.78216385', 'testdel',    'testdelset'),
                           ('aad47a84ad9a622c6.82815540', 'testdelart', 'testdelset'),
                           ('b92c5126779a999b44b1168d6a', 'testsh5',    'testshset7'),
                           ('b9218462d4fb58adad0617a421', 'testsh2',    'testshset7'),
                           ('b92652b32c9da0b2b8af34a4d8', 'testsh1',    'testshset7');

#Actions2Article
UPDATE `oxactions2article` SET `OXSORT` = `OXSORT`+2  WHERE `OXACTIONID` = 'oxnewest';
DELETE FROM `oxactions2article` WHERE `OXACTIONID` = 'oxnewest' AND `OXSORT` > 3;
UPDATE `oxactions2article` SET `OXSORT` = `OXSORT`+1  WHERE `OXACTIONID` = 'oxbargain';
UPDATE `oxactions2article` SET `OXSORT` = `OXSORT`+1  WHERE `OXACTIONID` = 'oxtop5';
REPLACE INTO `oxactions2article` (`OXID`,                       `OXSHOPID`,   `OXACTIONID`,   `OXARTID`, `OXSORT`) VALUES
                                ('fa647a81f9183b795.92189723', 1, 'oxnewest',     '1000',     0),
                                ('fa647725tgk83b795.92189723', 1, 'oxnewest',     '1001',     1),
                                ('fa647a821cbb759a3.34804917', 1, 'oxbargain',    '1001',     0),
                                ('fa647a82200b834f6.31174235', 1, 'oxtop5',       '1000',     0),
                                ('fa647a821bcd6e225.88450924', 1, 'oxcatoffer',   '1003',     5),
                                ('fa647a821e4b8fa81.64242121', 1, 'oxstart',      '1002',     0),
                                ('fa647a821f27dbb38.79902594', 1, 'oxtopstart',   '1001',     0);

#Article2Attribute
REPLACE INTO `oxobject2attribute` (`OXID`,                       `OXOBJECTID`, `OXATTRID`,       `OXVALUE`,           `OXPOS`, `OXVALUE_1`) VALUES
                                 ('aad47a8511f54e023.54090494', '1000',       'testattribute1', 'attr value 1 [DE]',  0,      'attr value 1 [EN] šÄßüл'),
                                 ('aad47a8511f556f17.20889862', '1001',       'testattribute1', 'attr value 11 [DE]', 0,      'attr value 11 [EN] šÄßüл'),
                                 ('aad47a85125a41ed7.53096100', '1000',       'testattribute2', 'attr value 2 [DE]',  0,      'attr value 2 [EN] šÄßüл'),
                                 ('aad47a85125a4aa05.37412863', '1001',       'testattribute2', 'attr value 12 [DE]', 0,      'attr value 12 [EN] šÄßüл'),
                                 ('aad47a8512d783995.31168870', '1000',       'testattribute3', 'attr value 3 [DE]',  0,      'attr value 3 [EN] šÄßüл'),
                                 ('aad47a8512d78c354.06494034', '1001',       'testattribute3', 'attr value 3 [DE]',  0,      'attr value 3 [EN] šÄßüл');

#Article2Category
REPLACE INTO `oxobject2category` (`OXID`,                       `OXOBJECTID`, `OXCATNID`,     `OXPOS`, `OXTIME`) VALUES
                                ('6f047a71f53e3b6c2.93342239', '1000',       'testcategory0', 0,       1202134867),
                                ('6f047a727222a8779.49668732', '1001',       'testcategory0', 0,       1202136866),
                                ('fa647a8289f289701.98087425', '1003',       'testcategory1', 0,       1202202783),
                                ('fa647a8289f27d717.39544758', '1002',       'testcategory1', 0,       1202202783);

#object2Delivery
REPLACE INTO `oxobject2delivery` (`OXID`,                       `OXDELIVERYID`, `OXOBJECTID`,                 `OXTYPE`) VALUES
                                ('aad47a8495555ed87.12642087', 'testdel',      'a7c40f631fc920687.20179984', 'oxcountry'),
                                ('aad47a84a1001a5e9.75786355', 'testdel',      'testcategory1',              'oxcategories'),
                                ('aad47a84ac9134f27.15315975', 'testdelart',   '1001',                       'oxarticles'),
                                ('aad47a84b027f44c2.52893233', 'testdelset',   'a7c40f631fc920687.20179984', 'oxdelset'),
                                ('b94aa1478381d27d5642cb1dab', 'testshset7',   'a7c40f631fc920687.20179984', 'oxdelset');

#object2Group
REPLACE INTO `oxobject2group` (`OXID`,                       `OXSHOPID`,   `OXOBJECTID`,   `OXGROUPSID`) VALUES
                             ('6f047a71c6bbe7f16.16184766', 1, 'testpayment',  'oxidnewcustomer'),
                             ('aad47a85a83749c71.33568408', 1, 'testuser',     'oxidnewcustomer'),
                             ('15947a85a7ce45851.42160470', 1, 'testuser1',    'oxidnewcustomer'),
                             ('aad47a861da01a378.98461160', 1, 'testusera',    'oxidnewcustomer'),
                             ('aad47a861da025017.57895315', 1, 'testusera',    'oxidpricea'),
                             ('aad47a86343b2c222.48056509', 1, 'testuserb',    'oxidnewcustomer'),
                             ('aad47a86343b36f87.26955455', 1, 'testuserb',    'oxidpriceb'),
                             ('aad47a868601ed778.03775700', 1, 'testuserc',    'oxidnewcustomer'),
                             ('aad47a868601f8420.26771882', 1, 'testuserc',    'oxidpricec'),
                             ('aad47a869f4828b24.16206885', 1, 'testnews1',    'oxidnewcustomer'),
                             ('aad47a86bb3afd568.93683720', 1, 'testnews2',    'oxidnewcustomer'),
                             ('aad47a87240deb323.16605440', 1, 'testcoupon2',  'oxidnewcustomer'),
                             ('aad47a872468a9285.09755579', 1, 'testcoupon1',  'oxidnewcustomer'),
                             ('eace688cdde82a5412e1e3b8e5', 1, 'testvoucher1', 'oxidnewcustomer'),
                             ('ea245d146646f7bebec1c41138', 1, 'testvoucher2', 'oxidnewcustomer');

#object2Payment
REPLACE INTO `oxobject2payment` (`OXID`,                       `OXPAYMENTID`,    `OXOBJECTID`,                 `OXTYPE`) VALUES
                               ('fa647a82238638526.04996237', 'testpayment',    'a7c40f631fc920687.20179984', 'oxcountry'),
                               ('aad47a84ae8d74c27.43568020', 'testpayment',    'testdelset',                 'oxdelset'),
                               ('aad47a84aec2fcea4.29746738', 'oxidcashondel',  'testdelset',                 'oxdelset'),
                               ('aad47a84afba84197.13924353', 'oxidcreditcard', 'testdelset',                 'oxdelset'),
                               ('b36fc8be21394477f5b3200e88', 'oxidinvoice',    'testshset7',                 'oxdelset'),
                               ('b928f0782117f251fdb5c43e05', 'oxidcashondel',  'testshset7',                 'oxdelset');

#Article2SelectList
REPLACE INTO `oxobject2selectlist` (`OXID`,                       `OXOBJECTID`, `OXSELNID`,   `OXSORT`) VALUES
                                  ('aad47a85773148362.54224248', '1001',       'testsellist', 0);

#price2article
REPLACE INTO `oxprice2article` (`OXID`,                       `OXSHOPID`,   `OXARTID`, `OXADDABS`, `OXADDPERC`, `OXAMOUNT`, `OXAMOUNTTO`) VALUES
                              ('6f047a734ea2f2cd9.42793951', 1, '1003',     75,         0,           2,          5),
                              ('6f047a73548de8581.49743013', 1, '1003',     0,          20,          6,          9999999);

#object2discount
REPLACE INTO `oxobject2discount` (`OXID`,                        `OXDISCOUNTID`,    `OXOBJECTID`,                 `OXTYPE`) VALUES
                                ('fa647a823ce118996.58546955',  'testcatdiscount', 'a7c40f631fc920687.20179984', 'oxcountry'),
                                ('fa647a823d5079104.99115703',  'testcatdiscount', 'testcategory0',              'oxcategories'),
                                ('fa647a825889edd04.54821317',  'testartdiscount', 'a7c40f631fc920687.20179984', 'oxcountry'),
                                ('fa647a825889f47a5.25840152',  'testartdiscount', 'a7c40f6321c6f6109.43859248', 'oxcountry'),
                                ('fa647a825b4bfe8d8.63031920',  'testartdiscount', '1003',                       'oxarticles'),
                                ('fa647a825bc525754.62252573',  'testartdiscount', '1002',                       'oxarticles'),
                                ('b0047c42fb0a02a65.13519853',  'testitmdiscount', 'a7c40f631fc920687.20179984', 'oxcountry'),
                                ('b0047c42fc71aa294.67674839',  'testitmdiscount', '1000',                       'oxarticles'),
                                ('58cebcec6bf240d3e19509eb47d', 'testdiscount1',   '8a142c3e60a535f16.78077188', 'oxcategories'),
                                ('eac525b7a39bbce8d3227038752', 'testvoucher1',    '1002',                       'oxarticles'),
                                ('eaca696888e6e2694a3d750e0cb', 'testvoucher1',    '1003',                       'oxarticles'),
                                ('eac8236ab2a3e1e07cd2c9120e7', 'testvoucher2',    'testcategory0',              'oxcategories'),
                                ('eac5b1e16a20dd19cd8beebe1f6', 'testvoucher2',    '1003',                       'oxarticles');

#updating oxconfig settings
UPDATE `oxconfig` SET `OXVARVALUE` = 0x07a1       WHERE `OXVARNAME` = 'dDefaultVAT';
UPDATE `oxconfig` SET `OXVARVALUE` = 0xde         WHERE `OXVARNAME` = 'iNewBasketItemMessage';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'bl_perfUseSelectlistPrice';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'bl_perfShowActionCatArticleCnt';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'blOtherCountryOrder';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blCheckTemplates';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'blDisableNavBars';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'blAllowUnevenAmounts';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x07         WHERE `OXVARNAME` = 'blConfirmAGB';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba222b70e349f0c9d1aba6133981af1e8d79724d7309a19dd3eed099418943829510e114c4f6ffcb2543f5856ec4fea325d58b96e406decb977395c57d7cc79eec7f9f8dd6e30e2f68d198bd9d079dbe8b4f WHERE `OXVARNAME` = 'aNrofCatArticles';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dbace29724a51b6af7d09aac117301142e91c3c5b7eed9a850f85c1e3d58739aa9ea92523f05320a95060d60d57fbb027bad88efdaa0b928ebcd6aacf58084d31dd6ed5e718b833f1079b3805d28203f284492955c82cea3405879ea7588ec610ccde56acede495 WHERE `OXVARNAME` = 'aInterfaceProfiles';
DELETE FROM `oxconfig` WHERE `OXVARNAME`='blBasketExcludeEnabled';
REPLACE INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`,   `OXVARNAME`,                     `OXVARTYPE`, `OXVARVALUE`) VALUES
                       ('fb54', 1, '', 'perf_LoadFullVariantsInLists',  'bool',       0x7900fdf51e),
                       ('fh90', 1, '', 'bl_perfLoadSelectListsInAList', 'bool',       0x93ea1218),
                       ('a910', 1, '', 'blLoadSelectBoxAlways',         'bool',       0x93ea1218),
                       ('4742', 1, '', 'blPerfNoBasketSaving',          'bool',       0x93ea1218),
                       ('d084', 1, '', 'iMinOrderPrice',                'str',        0xfba4),
                       ('33bd', 1, '', 'blOverrideZeroABCPrices',       'bool',       0x93ea1218),
                       ('3c9f', 1, '', 'blShowOrderButtonOnTop',        'bool',       0x93ea1218),
                       ('24d5', 1, '', 'bl_rssBargain',                 'bool',       0x07),
                       ('2bf5', 1, '', 'bl_rssRecommLists',             'bool',       0x07),
                       ('64i5', 1, '', 'bl_rssRecommListArts',          'bool',       0x07),
                       ('c5iu', 1, '', 'blVariantParentBuyable',        'bool',       ''),
                       ('czzz', 1, '', 'blShowVariantReviews',          'bool',       ''),
                       ('a6ba', 1, '', 'blOrderDisWithoutReg',          'bool',       ''),
                       ('a85z', 1, '', 'blShowVATForWrapping',          'bool',       ''),
                       ('asdf', 1, '', 'blBasketExcludeEnabled',        'str',        '');

#review demodata
REPLACE INTO `oxreviews` (`OXID`,       `OXACTIVE`, `OXOBJECTID`, `OXTYPE`,    `OXTEXT`,                           `OXUSERID`, `OXCREATE`,           `OXLANG`, `OXRATING`) VALUES
                        ('testreview1', 0,         '10010',      'oxarticle', '1 [DE] comment for product šÄßüл', 'testuser', '2006-01-01 10:10:10', 0,        0),
                        ('testreview2', 0,         '10011',      'oxarticle', '3 [EN] product comment šÄßüл',     'testuser', '2006-02-01 08:10:10', 1,        0),
                        ('testreview3', 0,         '10012',      'oxarticle', '3 [DE] comment for product šÄßüл', 'testuser', '2005-12-01 11:11:00', 0,        0),
                        ('testreview4', 0,         '10011',      'oxarticle', '1 [EN] product comment šÄßüл',     'testuser', '2006-02-05 10:10:10', 1,        0),
                        ('testreview5', 0,         '10010',      'oxarticle', '2 [DE] comment for product šÄßüл', 'testuser', '2005-12-30 11:11:00', 0,        0),
                        ('testreview6', 0,         '10012',      'oxarticle', '2 [EN] product comment šÄßüл',     'testuser', '2005-12-05 10:10:10', 1,        0),
                        ('testreview7', 0,         '1002',       'oxarticle', 'review for parent product šÄßüл',  'testuser', '2009-01-15 14:36:19', 1,        0),
                        ('testreview8', 0,         '1002-1',     'oxarticle', 'review for var1 šÄßüл',            'testuser', '2009-01-15 14:36:55', 1,        0),
                        ('testreview9', 0,         '1002-2',     'oxarticle', 'review for var2 šÄßüл',            'testuser', '2009-01-15 14:36:56', 1,        0);

#user group demodata
REPLACE INTO `oxgroups` (`OXID`,      `OXACTIVE`, `OXTITLE`,                 `OXTITLE_1`) VALUES
                       ('testgroup1', 0,         '1 user Group šÄßüл',      '1 user Group šÄßüл'),
                       ('testgroup2', 0,         '2 user Group šÄßüл',      '2 user Group šÄßüл'),
                       ('testgroup3', 0,         '3 user Group šÄßüл',      '3 user Group šÄßüл'),
                       ('testgroup4', 0,         'Z user Group šÄßüл',      'Z user Group šÄßüл'),
                       ('testgroup5', 0,         '[last] user Group šÄßüл', '[last] user Group šÄßüл');

#CMS pages demodata
REPLACE INTO `oxcontents` (`OXID`,         `OXLOADID`,          `OXSHOPID`,  `OXSNIPPET`, `OXTYPE`, `OXACTIVE`, `OXACTIVE_1`, `OXTITLE`,                   `OXCONTENT`,                `OXTITLE_1`,                 `OXCONTENT_1`,                    `OXCATID`,                    `OXFOLDER`) VALUES
                         ('testcontent1', '[last]testcontent', 1, 0,           2,        0,          0,           '1 [DE] content šÄßüл',      '<p>content [DE] 1</p>',    '[last] [EN] content šÄßüл', '<p>content [EN] 1  šÄßüл</p>',   'testcategory0',              'CMSFOLDER_USERINFO'),
                         ('testcontent2', '1testcontent',      1, 0,           2,        0,          0,           '[last] [DE] content šÄßüл', '<p>content [DE] last</p>', '3 [EN] content šÄßüл',      '<p>content [EN] last šÄßüл</p>', 'testcategory1',              'CMSFOLDER_USERINFO'),
                         ('testcontent3', 't3testcontent',     1, 0,           3,        0,          0,           'T2 [DE] content šÄßüл',     '',                         'T4 [EN] content šÄßüл',     '',                               '8a142c3e4143562a5.46426637', 'CMSFOLDER_USERINFO'),
                         ('testcontent4', 't4testcontent',     1, 0,           3,        0,          0,           'T4 [DE] content šÄßüл',     '',                         'T1 [EN] content šÄßüл',     '',                               '30e44ab834ea42417.86131097', 'CMSFOLDER_USERINFO'),
                         ('testcontent5', 't9testcontent',     1, 0,           3,        0,          0,           'T5 [DE] content šÄßüл',     '',                         'T1 [EN] content šÄßüл',     '',                               '30e44ab834ea42417.86131097', 'CMSFOLDER_USERINFO'),
                         ('testcontent6', 't5testcontent',     1, 0,           3,        0,          0,           'T6 [DE] content šÄßüл',     '',                         'T5 [EN] content šÄßüл',     '',                               '30e44ab834ea42417.86131097', 'CMSFOLDER_USERINFO');

#Price alert demodata
REPLACE INTO `oxpricealarm` (`OXID`,             `OXSHOPID`,   `OXUSERID`,  `OXEMAIL`,         `OXARTID`, `OXPRICE`, `OXCURRENCY`, `OXLANG`, `OXINSERT`,            `OXSENDED`) VALUES
                           ('testpricealert1',  1, 'testuser4', 'example05@oxid-esales.dev', '10013',    11,       'EUR',         1,       '2007-12-12 00:00:01', '0000-00-00 00:00:00'),
                           ('testpricealert2',  1, 'testuser4', 'example05@oxid-esales.dev', '10015',    4,        'EUR',         1,       '2007-12-05 00:00:02', '2008-01-04 00:00:02'),
                           ('testpricealert3',  1, 'testuser6', 'example04@oxid-esales.dev', '10013',    7,        'EUR',         1,       '2007-12-07 00:00:07', '0000-00-00 00:00:00'),
                           ('testpricealert4',  1, 'testuser1', 'example02@oxid-esales.dev', '10010',    1,        'EUR',         1,       '2007-12-17 00:00:09', '2008-01-03 00:00:05'),
                           ('testpricealert5',  1, 'testuser3', 'example07@oxid-esales.dev', '10013',    3,        'EUR',         1,       '2007-11-11 00:00:06', '0000-00-00 00:00:00'),
                           ('testpricealert6',  1, 'testuser5', 'example08@oxid-esales.dev', '10012',    10,       'EUR',         1,       '2007-12-10 00:00:05', '2008-01-05 00:00:01'),
                           ('testpricealert7',  1, 'testuser7', 'example06@oxid-esales.dev', '10010',    6,        'EUR',         1,       '2007-12-06 00:00:08', '2008-01-06 00:00:02'),
                           ('testpricealert8',  1, 'testuser2', 'example03@oxid-esales.dev', '10011',    2,        'EUR',         1,       '2007-10-09 00:00:08', '2008-01-08 00:00:01'),
                           ('testpricealert9',  1, 'testuser1', 'example02@oxid-esales.dev', '10010',    5,        'EUR',         1,       '2007-12-13 00:00:04', '2008-01-01 00:00:01'),
                           ('testpricealert10', 1, 'testuser3', 'example07@oxid-esales.dev', '10011',    8,        'EUR',         1,       '2007-09-14 00:00:08', '2008-01-02 00:00:01'),
                           ('testpricealert11', 1, 'testuser2', 'example03@oxid-esales.dev', '10012',    9,        'EUR',         1,       '2007-12-13 00:00:03', '2008-01-01 00:00:05');

#Accessories and crossselling
REPLACE INTO `oxaccessoire2article` (`OXID`,                       `OXOBJECTID`, `OXARTICLENID`, `OXSORT`) VALUES
                                   ('e2647c561ffb990a8.18051802', '1002',       '1000',          0);
REPLACE INTO `oxobject2article` (`OXID`,                       `OXOBJECTID`, `OXARTICLENID`, `OXSORT`) VALUES
                               ('e2647c561fa38ba79.72969512', '1003',       '1000',          0);

#Orders demodata
DELETE FROM `oxorder` WHERE `OXORDERNR` = 1;
REPLACE INTO `oxorder` (`OXID`,        `OXSHOPID`,   `OXUSERID`,  `OXORDERDATE`,        `OXORDERNR`, `OXBILLEMAIL`,     `OXBILLFNAME`, `OXBILLLNAME`,       `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLCITY`, `OXBILLCOUNTRYID`,            `OXBILLSTATEID`, `OXBILLZIP`, `OXBILLFON`, `OXBILLSAL`, `OXDELFNAME`,        `OXDELLNAME`,           `OXDELSTREET`, `OXDELSTREETNR`, `OXDELCITY`, `OXDELCOUNTRYID`,             `OXDELSTATEID`, `OXDELZIP`, `OXDELSAL`, `OXPAYMENTID`,                      `OXPAYMENTTYPE`, `OXTOTALNETSUM`, `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`, `OXDELCOST`, `OXDELVAT`, `OXPAYCOST`, `OXDISCOUNT`, `OXCURRENCY`, `OXCURRATE`, `OXFOLDER`,             `OXPAID`,              `OXIP`,          `OXTRANSSTATUS`, `OXLANG`, `OXDELTYPE`) VALUES
                      ('testorder1',  1, 'testuser6', '2008-04-21 15:02:54', 10, 'example04@oxid-esales.dev',    '3useršÄßüл',  '3UserSurnamešÄßüл', '6 Street',     '1',              '7 City',     'a7c40f6320aeb2ec2.72885259', '',              '111000',    '222222',    'MR',        '',                  '',                     '',            '1',             'City',      'a7c40f631fc920687.20179984', '',             '1',        'MR',       'oxuserpayments1',                  'oxidcashondel',  16.806722689076, 20,               53.8,              12.9,        0,          20.9,        0,           'EUR',         1,          'ORDERFOLDER_NEW',      '0000-00-00 00:00:00', '',              'OK',             1,       '1b842e732a23255b1.91207750'),
                      ('testorder2',  1, 'testuser6', '2008-04-21 15:07:46', 11, 'example04@oxid-esales.dev',    '3useršÄßüл',  '3UserSurnamešÄßüл', '6 Street',     '1',              '7 City',     'a7c40f6320aeb2ec2.72885259', '',              '111000',    '222222',    'MR',        '',                  '',                     '',            '1',             'City',      'a7c40f631fc920687.20179984', '',             '1',        'MR',       'oxuserpayments2',                  'oxidcashondel',  3.0252100840336, 3.6,              37.4,              12.9,        0,          20.9,        0,           'EUR',         1,          'ORDERFOLDER_NEW',      '2008-04-21 15:08:47', '',              'OK',             1,       '1b842e732a23255b1.91207750'),
                      ('testorder3',  1, 'testuser6', '2008-04-21 14:52:59', 5,  'example04@oxid-esales.dev',    '3useršÄßüл',  '3UserSurnamešÄßüл', '6 Street',     '1',              '7 City',     'a7c40f6320aeb2ec2.72885259', '',              '111000',    '222222',    'MR',        '',                  '',                     '',            '1',             'City',      'a7c40f631fc920687.20179984', '',             '1',        'MR',       'oxuserpayments3',                  'oxidcashondel',  2.5210084033613, 3,                36.8,              12.9,        0,          20.9,        0,           'EUR',         1,          'ORDERFOLDER_NEW',      '0000-00-00 00:00:00', '',              'OK',             1,       '1b842e732a23255b1.91207750'),
                      ('testorder4',  1, 'testuser1', '2008-04-21 15:00:38', 8,  'example02@oxid-esales.dev',    '1useršÄßüл',  '1UserSurnamešÄßüл', '1 Street',     '1',              '2 City',     'a7c40f631fc920687.20179984', '',              '333000',    '444444',    'MR',        '',                  '',                     '',            '1',             'City',      'a7c40f631fc920687.20179984', '',             '1',        'MR',       'oxuserpayments4',                  'oxidcashondel',  3.3613445378151, 4,                37.8,              12.9,        0,          20.9,        0,           'EUR',         1,          'ORDERFOLDER_NEW',      '0000-00-00 00:00:00', '',              'OK',             1,       '1b842e732a23255b1.91207750'),
                      ('testorder5',  1, 'testuser6', '2008-04-21 14:54:33', 6,  'example04@oxid-esales.dev',    '3useršÄßüл',  '3UserSurnamešÄßüл', '6 Street',     '1',              '7 City',     'a7c40f6320aeb2ec2.72885259', '',              '111000',    '222222',    'MR',        '',                  '',                     '',            '1',             'City',      'a7c40f631fc920687.20179984', '',             '1',        'MR',       'oxuserpayments5',                  'oxidcashondel',  4.2857142857143, 5.1,              38.9,              12.9,        0,          20.9,        0,           'EUR',         1,          'ORDERFOLDER_NEW',      '2008-04-21 15:08:26', '',              'OK',             1,       '1b842e732a23255b1.91207750'),
                      ('testorder6',  1, 'testuser6', '2008-04-21 14:51:51', 4,  'example04@oxid-esales.dev',    '3useršÄßüл',  '3UserSurnamešÄßüл', '6 Street',     '1',              '7 City',     'a7c40f6320aeb2ec2.72885259', '',              '111000',    '222222',    'MR',        '',                  '',                     '',            '1',             'City',      'a7c40f631fc920687.20179984', '',             '1',        'MR',       'oxuserpayments6',                  'oxidcashondel',  7.563025210084,  9,                42.8,              12.9,        0,          20.9,        0,           'EUR',         1,          'ORDERFOLDER_NEW',      '0000-00-00 00:00:00', '',              'OK',             1,       '1b842e732a23255b1.91207750'),
                      ('testorder7',  1, 'testuser1', '2008-04-21 14:35:56', 1,  'example02@oxid-esales.dev',    '1useršÄßüл',  '1UserSurnamešÄßüл', '1 Street',     '1',              '2 City',     'a7c40f631fc920687.20179984', 'HE',            '333000',    '444444',    'MR',        'shippingUseršÄßüл', 'shippingSurnamešÄßüл', 'Street',      '1',             'City',      'a7c40f631fc920687.20179984', 'NI',           '1',        'MR',       'f992b36d7256dc5814e7866a7e4645e5', 'oxidcashondel',  95.24,           100,              97.5,              0,           0,          7.5,         10,          'EUR',         1,          'ORDERFOLDER_NEW',      '0000-00-00 00:00:00', '192.168.1.999', 'OK',             1,       'oxidstandard'),
                      ('testorder8',  1, 'testuser2', '2008-04-21 14:59:08', 7,  'example03@oxid-esales.dev',    '2useršÄßüл',  '2UserSurnamešÄßüл', '2 Street',     '1',              '3 City',     'a7c40f6320aeb2ec2.72885259', '',              '444000',    '555555',    'MR',        '',                  '',                     '',            '1',             'City',      'a7c40f631fc920687.20179984', '',             '1',        'MR',       'oxuserpayments8',                  'oxidcashondel',  1.6806722689076, 2,                35.8,              12.9,        0,          20.9,        0,           'EUR',         1,          'ORDERFOLDER_PROBLEMS', '0000-00-00 00:00:00', '',              'OK',             1,       '1b842e732a23255b1.91207750'),
                      ('testorder9',  1, 'testuser1', '2008-04-21 14:48:51', 2,  'example02@oxid-esales.dev',    '1useršÄßüл',  '1UserSurnamešÄßüл', '1 Street',     '1',              '2 City',     'a7c40f631fc920687.20179984', '',              '333000',    '444444',    'MR',        '',                  '',                     '',            '1',             'City',      'a7c40f631fc920687.20179984', '',             '1',        'MR',       'oxuserpayments9',                  'oxidinvoice',    1.5126050420168, 1.8,              5.7,               3.9,         0,          0,           0,           'EUR',         1,          'ORDERFOLDER_NEW',      '2008-04-21 15:14:02', '',              'OK',             1,       'oxidstandard'),
                      ('testorder10', 1, 'testuser6', '2008-04-21 15:02:12', 9,  'example04@oxid-esales.dev',    '3useršÄßüл',  '3UserSurnamešÄßüл', '6 Street',     '1',              '7 City',     'a7c40f6320aeb2ec2.72885259', '',              '111000',    '222222',    'MR',        '',                  '',                     '',            '1',             'City',      'a7c40f631fc920687.20179984', '',             '1',        'MR',       'oxuserpayments10',                 'oxidinvoice',    1.5126050420168, 1.8,              14.7,              12.9,        0,          0,           0,           'EUR',         1,          'ORDERFOLDER_NEW',      '2008-04-21 15:08:11', '',              'OK',             1,       '1b842e732a23255b1.91207750'),
                      ('testorder11', 1, 'testuser2', '2008-04-21 14:50:44', 3,          'example03@oxid-esales.dev', '2useršÄßüл',  '2UserSurnamešÄßüл', '2 Street',     '1',              '3 City',     'a7c40f6320aeb2ec2.72885259', '',              '444000',    '555555',    'MR',        '',                  '',                     '',            '1',             'City',      'a7c40f631fc920687.20179984', '',             '1',        'MR',       'oxuserpayments11',                 'oxidinvoice',    5.0420168067227, 6,                18.9,              12.9,        0,          0,           0,           'EUR',         1,          'ORDERFOLDER_FINISHED', '0000-00-00 00:00:00', '',              'OK',             1,       '1b842e732a23255b1.91207750');

UPDATE `oxcounters` SET oxcount = 11 WHERE oxident = 'oxOrder';

REPLACE INTO `oxorderarticles` (`OXID`,         `OXORDERID`,  `OXAMOUNT`, `OXARTID`, `OXARTNUM`, `OXTITLE`,                   `OXSHORTDESC`,                          `OXNETPRICE`,     `OXBRUTPRICE`, `OXVATPRICE`,      `OXVAT`, `OXPRICE`, `OXBPRICE`, `OXNPRICE`,       `OXWEIGHT`, `OXSTOCK`, `OXINSERT`,   `OXTIMESTAMP`,        `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXSEARCHKEYS`, `OXISSEARCH`, `OXSUBCLASS`, `OXORDERSHOPID`) VALUES
                              ('testordart1',  'testorder4',  2,         '10012',   '10012',    '12 EN product šÄßüл',       '11 EN description šÄßüл',               3.3613445378151,  4,             0.63865546218487,  19,      2,         4,          3.3613445378151,  0,          0,        '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              0,            '',          1),
                              ('testordart2',  'testorder6',  5,         '10011',   '10011',    '11 EN product šÄßüл',       '10 EN description šÄßüл',               7.563025210084,   9,             1.436974789916,    19,      1.8,       9,          7.563025210084,   0,          0,        '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              0,            '',          1),
                              ('testordart3',  'testorder7',  2,         '1000',    '1000',     'Test product 0 [EN] šÄßüл', 'Test product 0 short desc [EN] šÄßüл',  95.238095238095,  100,           4.7619047619048,   5,       50,        50,         47.619047619048,  24,         15,       '2008-02-04', '2008-02-04 17:07:29', 1,          2,         2,         'šÄßüл1000',     1,            'oxarticle', 1),
                              ('testordart4',  'testorder1',  10,        '10012',   '10012',    '12 EN product šÄßüл',       '11 EN description šÄßüл',               16.806722689076,  20,            3.1932773109244,   19,      2,         20,         16.806722689076,  0,          0,        '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              0,            '',          1),
                              ('testordart5',  'testorder9',  1,         '10011',   '10011',    '11 EN product šÄßüл',       '10 EN description šÄßüл',               1.5126050420168,  1.8,           0.28739495798319,  19,      1.8,       1.8,        1.5126050420168,  0,          0,        '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              0,            '',          1),
                              ('testordart6',  'testorder10', 1,         '10011',   '10011',    '11 EN product šÄßüл',       '10 EN description šÄßüл',               1.5126050420168,  1.8,           0.28739495798319,  19,      1.8,       1.8,        1.5126050420168,  0,          0,        '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              0,            '',          1),
                              ('testordart7',  'testorder11', 3,         '10012',   '10012',    '12 EN product šÄßüл',       '11 EN description šÄßüл',               5.0420168067227,  6,             0.95798319327731,  19,      2,         6,          5.0420168067227,  0,          0,        '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              0,            '',          1),
                              ('testordart8',  'testorder3',  2,         '10010',   '10010',    '1 EN product šÄßüл',        '[last] EN description šÄßüл',           2.5210084033613,  3,             0.47899159663866,  19,      1.5,       3,          2.5210084033613,  0,          0,        '2008-04-03', '2008-04-17 17:40:02', 0,          0,         0,         '',              0,            '',          1),
                              ('testordart9',  'testorder8',  1,         '10012',   '10012',    '12 EN product šÄßüл',       '11 EN description šÄßüл',               1.6806722689076,  2,             0.31932773109244,  19,      2,         2,          1.6806722689076,  0,          0,        '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              0,            '',          1),
                              ('testordart10', 'testorder2',  2,         '10011',   '10011',    '11 EN product šÄßüл',       '10 EN description šÄßüл',               3.0252100840336,  3.6,           0.57478991596639,  19,      1.8,       3.6,        3.0252100840336,  0,          0,        '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              0,            '',          1),
                              ('testordart11', 'testorder5',  1,         '10010',   '10010',    '1 EN product šÄßüл',        '[last] EN description šÄßüл',           1.2605042016807,  1.5,           0.23949579831933,  19,      1.5,       1.5,        1.2605042016807,  0,          0,        '2008-04-03', '2008-04-17 17:40:02', 0,          0,         0,         '',              0,            '',          1),
                              ('testordart12', 'testorder5',  2,         '10011',   '10011',    '11 EN product šÄßüл',       '10 EN description šÄßüл',               3.0252100840336,  3.6,           0.57478991596639,  19,      1.8,       3.6,        3.0252100840336,  0,          0,        '2008-04-03', '2008-04-03 12:50:20', 0,          0,         0,         '',              0,            '',          1);

REPLACE INTO `oxuserpayments` (`OXID`,             `OXUSERID`,  `OXPAYMENTSID`,  `OXVALUE`) VALUES
                             ('oxuserpayments1',  'testuser6', 'oxidcashondel', ''),
                             ('oxuserpayments2',  'testuser6', 'oxidcashondel', ''),
                             ('oxuserpayments3',  'testuser6', 'oxidcashondel', ''),
                             ('oxuserpayments4',  'testuser1', 'oxidcashondel', ''),
                             ('oxuserpayments5',  'testuser6', 'oxidcashondel', ''),
                             ('oxuserpayments6',  'testuser6', 'oxidcashondel', ''),
                             ('oxuserpayments7',  'testuser1', 'oxidinvoice',   ''),
                             ('oxuserpayments8',  'testuser2', 'oxidcashondel', ''),
                             ('oxuserpayments9',  'testuser1', 'oxidinvoice',   ''),
                             ('oxuserpayments10', 'testuser6', 'oxidinvoice',   ''),
                             ('oxuserpayments11', 'testuser2', 'oxidinvoice',   '');

#Newsletter templates demodata
REPLACE INTO `oxnewsletter` (`OXID`,             `OXSHOPID`,   `OXTITLE`,                      `OXTEMPLATE`, `OXPLAINTEMPLATE`) VALUES
                           ('testnewsletter1',  1, '2 Test Newsletter šÄßüл',      '',           ''),
                           ('testnewsletter2',  1, '5 Test Newsletter šÄßüл',      '',           ''),
                           ('testnewsletter3',  1, '[last] Test Newsletter šÄßüл', '',           ''),
                           ('testnewsletter4',  1, '1 Test Newsletter šÄßüл',      '',           ''),
                           ('testnewsletter5',  1, '9 Test Newsletter šÄßüл',      '',           ''),
                           ('testnewsletter6',  1, '4 Test Newsletter šÄßüл',      '',           ''),
                           ('testnewsletter7',  1, '8 Test Newsletter šÄßüл',      '',           ''),
                           ('testnewsletter8',  1, '3 Test Newsletter šÄßüл',      '',           ''),
                           ('testnewsletter9',  1, '6 Test Newsletter šÄßüл',      '',           ''),
                           ('testnewsletter10', 1, '7 Test Newsletter šÄßüл',      '',           '');

#recommendation lists demodata
REPLACE INTO `oxrecommlists` (`OXID`,       `OXSHOPID`,   `OXUSERID`, `OXAUTHOR`,      `OXTITLE`,      `OXDESC`,            `OXRATINGCNT`, `OXRATING`) VALUES
                            ('testrecomm', 1, 'testuser', 'recomm author', 'recomm title', 'recom introduction', 0,             0);
REPLACE INTO `oxobject2list` (`OXID`,             `OXOBJECTID`, `OXLISTID`,   `OXDESC`,                   `OXTIMESTAMP`) VALUES
                            ('testrecomarticle', '1000',       'testrecomm', 'comment for product 1000', '2008-11-09 17:04:47');
REPLACE INTO `oxratings` (`OXID`,            `OXSHOPID`,   `OXUSERID`, `OXTYPE`,       `OXOBJECTID`, `OXRATING`, `OXTIMESTAMP`) VALUES
                        ('testrecomrating', 1, 'testuser', 'oxrecommlist', 'testrecomm',  3,         '2009-11-10 12:18:29');
REPLACE INTO `oxreviews` (`OXID`,           `OXACTIVE`, `OXOBJECTID`, `OXTYPE`,       `OXTEXT`,                       `OXUSERID`, `OXCREATE`,           `OXLANG`, `OXRATING`) VALUES
                        ('testrecomreview', 0,         'testrecomm', 'oxrecommlist', 'recommendation for this list', 'testuser', '2009-11-10 12:18:29', 1,        3);

#updating smtp and email information
UPDATE `oxshops` SET `OXPRODUCTIVE` = 0, `OXINFOEMAIL` = 'example_test@oxid-esales.dev', `OXORDEREMAIL` = 'example_test@oxid-esales.dev', `OXOWNEREMAIL` = 'example_test@oxid-esales.dev', `OXSMTP` = 'localhost', `OXDEFCAT` = '' WHERE `OXID` = 1;
UPDATE `oxcountry` SET `OXVATSTATUS` = 0 WHERE `OXTITLE_1` = 'Austria';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Germany';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Austria';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Switzerland';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Liechtenstein';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Italy';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Luxembourg';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'France';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Sweden';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Finland';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'United Kingdom';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Ireland';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Netherlands';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Belgium';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Portugal';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Spain';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE_1` = 'Greece';

#additional for features testing
#UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'blCheckForUpdates';
#UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'blSendTechnicalInformationToOxid';

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
