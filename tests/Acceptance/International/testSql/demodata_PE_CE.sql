SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

# Activate Azure theme
UPDATE `oxconfig` SET `OXVARVALUE` = 0x4db70f6d1a WHERE `OXVARNAME` = 'sTheme';

#set country, username, password for default user
UPDATE oxuser
  SET
      oxcountryid = 'a7c40f631fc920687.20179984',
      oxusername = 'admin@myoxideshop.com',
      oxpassword = '6cb4a34e1b66d3445108cd91b67f98b9',
      oxpasssalt = '6631386565336161636139613634663766383538633566623662613036636539'
  WHERE OXUSERNAME='admin';

INSERT INTO `oxarticles` (`OXID`,   `OXSHOPID`,   `OXPARENTID`, `OXACTIVE`, `OXARTNUM`, `OXTITLE`,                    `OXSHORTDESC`,                          `OXPRICE`, `OXPRICEA`, `OXPRICEB`, `OXPRICEC`, `OXTPRICE`, `OXUNITNAME`, `OXUNITQUANTITY`, `OXVAT`, `OXWEIGHT`, `OXSTOCK`, `OXSTOCKFLAG`, `OXSTOCKTEXT`,          `OXNOSTOCKTEXT`,            `OXDELIVERY`, `OXINSERT`,   `OXTIMESTAMP`,        `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXSEARCHKEYS`, `OXISSEARCH`, `OXISCONFIGURABLE`, `OXVARNAME`,           `OXVARSTOCK`, `OXVARCOUNT`, `OXVARSELECT`,     `OXVARMINPRICE`, `OXVARNAME_1`,   `OXVARSELECT_1`, `OXTITLE_1`,                    `OXSHORTDESC_1`,                  `OXSEARCHKEYS_1`, `OXSTOCKTEXT_1`, `OXNOSTOCKTEXT_1`,   `OXSORT`, `OXVENDORID`,      `OXMANUFACTURERID`, `OXSKIPDISCOUNTS`, `OXRATING`, `OXRATINGCNT`, `OXMINDELTIME`, `OXMAXDELTIME`, `OXDELTIMEUNIT`) VALUES
                         ('1000',   1, '',            1,         '1000',     'Test product 0 [EN] šųößлы', 'Test product 0 short desc [EN] šųößлы', 50,        35,         45,         55,         0,         'kg',          2,                NULL,    24,         15,        1,            'In stock [EN] šųößлы', 'Out of stock [EN] šųößлы', '0000-00-00', '2008-02-04', '2010-03-16 10:53:43', 1,          2,         2,         'šųößлы1000',    1,            0,                 '',                     0,            0,           '',                 50,             '',              '',              '[DE 4] Test product 0 šųößлы', 'Test product 0 short desc [DE]', 'search1000',     'In stock [DE]', 'Out of stock [DE]', 0,        'testdistributor', 'testmanufacturer', 0,                  0,          0,             1,              1,             'DAY'),
                         ('1001',   1, '',            1,         '1001',     'Test product 1 [EN] šųößлы', 'Test product 1 short desc [EN] šųößлы', 100,       0,          0,          0,          150,       '',            0,                10,      0,          0,         1,            '',                     '',                         '2008-01-01', '2008-02-04', '2010-03-16 10:53:43', 0,          0,         0,         'šųößлы1001',    1,            0,                 '',                     0,            0,           '',                 100,            '',              '',              '[DE 1] Test product 1 šųößлы', 'Test product 1 short desc [DE]', 'search1001',     '',              '',                  0,        'testdistributor', 'testmanufacturer', 0,                  0,          0,             0,              1,             'WEEK'),
                         ('1002',   1, '',            1,         '1002',     'Test product 2 [EN] šųößлы', 'Test product 2 short desc [EN] šųößлы', 55,        0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            'In stock [EN] šųößлы', 'Out of stock [EN] šųößлы', '0000-00-00', '2008-02-04', '2010-03-16 10:53:43', 0,          0,         0,         'šųößлы1002',    1,            0,                 'variants [EN] šųößлы', 10,           2,           '',                 55,             'variants [DE]', '',              '[DE 2] Test product 2 šųößлы', 'Test product 2 short desc [DE]', 'search1002',     'In stock [DE]', 'Out of stock [DE]', 0,        'testdistributor', 'testmanufacturer', 0,                  0,          0,             1,              1,             'MONTH'),
                         ('1003',   1, '',            1,         '1003',     'Test product 3 [EN] šųößлы', 'Test product 3 short desc [EN] šųößлы', 75,        70,         85,         0,          0,         '',            0,                NULL,    0,          5,         1,            '',                     '',                         '0000-00-00', '2008-02-04', '2010-03-16 10:53:43', 0,          0,         0,         'šųößлы1003',    1,            0,                 '',                     0,            0,           '',                 75,             '',              '',              '[DE 3] Test product 3 šųößлы', 'Test product 3 short desc [DE]', 'search1003',     '',              '',                  0,        '',                'testmanufacturer', 0,                  0,          0,             4,              9,             'DAY'),
                         ('1002-1', 1, '1002',        1,         '1002-1',   '',                           '',                                      55,        0,          0,          0,          0,         '',            0,                NULL,    0,          5,         1,            'In stock [EN] šųößлы', 'Out of stock [EN] šųößлы', '0000-00-00', '2008-02-04', '2010-03-16 10:53:43', 0,          0,         0,         '',              1,            0,                 '',                     0,            0,           'var1 [EN] šųößлы', 0,              '',              'var1 [DE]',     '',                             '',                               '',               'In stock [DE]', 'Out of stock [DE]', 1,        '',                '',                 0,                  0,          0,             0,              0,             ''),
                         ('1002-2', 1, '1002',        1,         '1002-2',   '',                           '',                                      67,        0,          0,          0,          0,         '',            0,                NULL,    0,          5,         1,            'In stock [EN] šųößлы', 'Out of stock [EN] šųößлы', '0000-00-00', '2008-02-04', '2010-03-16 10:53:43', 0,          0,         0,         '',              1,            0,                 '',                     0,            0,           'var2 [EN] šųößлы', 0,              '',              'var2 [DE]',     '',                             '',                               '',               'In stock [DE]', 'Out of stock [DE]', 2,        '',                '',                 0,                  0,          0,             0,              0,             ''),
                         ('10010',  1, '',            0,         '10010',    '1 EN product šųößлы',        '[last] EN description šųößлы',          1.5,       0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            '',                     '',                         '0000-00-00', '2008-04-03', '2010-03-16 10:53:43', 0,          0,         0,         '',              1,            0,                 '',                     0,            0,           '',                 1.5,            '',              '',              '[last] DE product šųößлы',     '1 DE description',               '',               '',              '',                  0,        '',                '',                 0,                  0,          0,             0,              0,             ''),
                         ('10011',  1, '',            0,         '10011',    '11 EN product šųößлы',       '10 EN description šųößлы',              1.8,       0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            '',                     '',                         '0000-00-00', '2008-04-03', '2010-03-16 10:53:43', 0,          0,         0,         '',              1,            0,                 '',                     0,            0,           '',                 1.8,            '',              '',              '10 DE product šųößлы',         '11 DE description',              '',               '',              '',                  0,        '',                '',                 0,                  0,          0,             0,              0,             ''),
                         ('10012',  1, '',            0,         '10012',    '12 EN product šųößлы',       '11 EN description šųößлы',              2,         0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            '',                     '',                         '0000-00-00', '2008-04-03', '2010-03-16 10:53:43', 0,          0,         0,         '',              1,            0,                 '',                     0,            0,           '',                 2,              '',              '',              '11 DE product šųößлы',         '12 DE description',              '',               '',              '',                  0,        '',                '',                 0,                  0,          0,             0,              0,             ''),
                         ('10013',  1, '',            0,         '10013',    '13 EN product šųößлы',       '12 EN description šųößлы',              1.7,       0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            '',                     '',                         '0000-00-00', '2008-04-03', '2010-03-16 10:53:43', 0,          0,         0,         '',              1,            0,                 '',                     0,            0,           '',                 1.7,            '',              '',              '12 DE product šųößлы',         '13 DE description',              '',               '',              '',                  0,        '',                '',                 0,                  0,          0,             0,              0,             ''),
                         ('10014',  1, '',            0,         '10014',    '14 EN product šųößлы',       '13 EN description šųößлы',              1.6,       0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            '',                     '',                         '0000-00-00', '2008-04-03', '2010-03-16 10:53:43', 0,          0,         0,         '',              1,            0,                 '',                     0,            0,           '',                 1.6,            '',              '',              '13 DE product šųößлы',         '14 DE description',              '',               '',              '',                  0,        '',                '',                 0,                  0,          0,             0,              0,             ''),
                         ('10015',  1, '',            0,         '10015',    '15 EN product šųößлы',       '14 EN description šųößлы',              2.1,       0,          0,          0,          0,         '',            0,                NULL,    0,          1,         1,            '',                     '',                         '0000-00-00', '2008-04-03', '2010-03-16 10:53:43', 0,          0,         0,         '',              1,            0,                 '',                     0,            0,           '',                 2.1,            '',              '',              '14 DE product šųößлы',         '15 DE description',              '',               '',              '',                  0,        '',                '',                 0,                  0,          0,             0,              0,             ''),
                         ('10016',  1, '',            0,         '10016',    '10 EN product šųößлы',       '15 EN description šųößлы',              1.9,       0,          0,          0,          0,         '',            0,                NULL,    0,          0,         1,            '',                     '',                         '0000-00-00', '2008-04-03', '2010-03-16 10:53:43', 0,          0,         0,         '',              1,            0,                 '',                     0,            0,           '',                 1.9,            '',              '',              '15 DE product šųößлы',         '10 DE description',              '',               '',              '',                  0,        '',                '',                 0,                  0,          0,             0,              0,             '');

INSERT INTO `oxartextends` (`OXID`,   `OXLONGDESC`,                                         `OXLONGDESC_1`) VALUES
                           ('1000',   '<p>Test product 0 long description [EN] šųößлы</p>', '<p>Test product 0 long description [DE]</p>'),
                           ('1001',   '<p>Test product 1 long description [EN] šųößлы</p>', '<p>Test product 1 long description [DE]</p>'),
                           ('1002',   '<p>Test product 2 long description [EN] šųößлы</p>', '<p>Test product 2 long description [DE]</p>'),
                           ('1003',   '<p>Test product 3 long description [EN] šųößлы</p>', '<p>Test product 3 long description [DE]</p>'),
                           ('1002-1', '',                                                   ''),
                           ('1002-2', '',                                                   ''),
                           ('10010',  '',                                                   ''),
                           ('10011',  '',                                                   ''),
                           ('10012',  '',                                                   ''),
                           ('10013',  '',                                                   ''),
                           ('10014',  '',                                                   ''),
                           ('10015',  '',                                                   ''),
                           ('10016',  '',                                                   '');

INSERT INTO `oxaccessoire2article` (`OXID`,                       `OXOBJECTID`, `OXARTICLENID`, `OXSORT`) VALUES
                                   ('e2647c561ffb990a8.18051802', '1002',       '1000',          0);

INSERT INTO `oxattribute` (`OXID`,           `OXSHOPID`,   `OXTITLE`,                      `OXTITLE_1`,                    `OXPOS`) VALUES
                          ('testattribute1', 1, 'Test attribute 1 [EN] šųößлы', 'Test attribute 1 [DE] šųößлы',  1),
                          ('testattribute2', 1, 'Test attribute 2 [EN] šųößлы', 'Test attribute 2 [DE] šųößлы',  3),
                          ('testattribute3', 1, 'Test attribute 3 [EN] šųößлы', 'Test attribute 3 [DE] šųößлы',  2),
                          ('testattr1',      1, '[last] [EN] Attribute šųößлы', '1 [DE] Attribute šųößлы',       5),
                          ('testattr2',      1, '3 [EN] Attribute šųößлы',      '2 [DE] Attribute šųößлы',       4),
                          ('testattr3',      1, '4 [EN] Attribute šųößлы',      '3 [DE] Attribute šųößлы',       6),
                          ('testattr4',      1, '5 [EN] Attribute šųößлы',      '4 [DE] Attribute šųößлы',       7),
                          ('testattr5',      1, '6 [EN] Attribute šųößлы',      '5 [DE] Attribute šųößлы',       9),
                          ('testattr6',      1, '7 [EN] Attribute šųößлы',      '6 [DE] Attribute šųößлы',       11),
                          ('testattr7',      1, '2 [EN] Attribute šųößлы',      '7 [DE] Attribute šųößлы',       10),
                          ('testattr8',      1, '1 [EN] Attribute šųößлы',      '[last] [DE] Attribute šųößлы',  8);
UPDATE `oxattribute` SET `OXDISPLAYINBASKET` = 0;

INSERT INTO `oxcategories` (`OXID`,          `OXPARENTID`,   `OXLEFT`, `OXRIGHT`, `OXROOTID`,     `OXSORT`, `OXACTIVE`, `OXHIDDEN`, `OXSHOPID`,   `OXTITLE`,                     `OXDESC`,                           `OXLONGDESC`,                       `OXDEFSORT`, `OXDEFSORTMODE`, `OXPRICEFROM`, `OXPRICETO`, `OXACTIVE_1`, `OXTITLE_1`,                   `OXDESC_1`,                  `OXLONGDESC_1`,             `OXVAT`, `OXSKIPDISCOUNTS`, `OXSHOWSUFFIX`) VALUES
                           ('testcategory0', 'oxrootid',      1,        4,        'testcategory0', 1,        1,          0,         1, 'Test category 0 [EN] šųößлы', 'Test category 0 desc [EN] šųößлы', 'Category 0 long desc [EN] šųößлы', 'oxartnum',   0,               0,             0,           1,           'Test category 0 [DE] šųößлы', 'Test category 0 desc [DE]', 'Category 0 long desc [DE]', 5,       0,                 1),
                           ('testcategory1', 'testcategory0', 2,        3,        'testcategory0', 2,        1,          0,         1, 'Test category 1 [EN] šųößлы', 'Test category 1 desc [EN] šųößлы', 'Category 1 long desc [EN] šųößлы', 'oxartnum',   1,               0,             0,           1,           'Test category 1 [DE] šųößлы', 'Test category 1 desc [DE]', 'Category 1 long desc [DE]', NULL,    0,                 1),
                           ('testcat1',      'oxrootid',      1,        2,        'testcat1',      2,        0,          0,         1, '[last] [EN] category šųößлы', '',                                 '',                                 '',           0,               0,             0,           0,           '1 [DE] category šųößлы',      '',                          '',                          NULL,    0,                 1),
                           ('testcat2',      'oxrootid',      1,        2,        'testcat2',      5,        0,          0,         1, '3 [EN] category šųößлы',      '',                                 '',                                 '',           0,               0,             0,           0,           '2 [DE] category šųößлы',      '',                          '',                          NULL,    0,                 1),
                           ('testcat3',      'oxrootid',      1,        2,        'testcat3',      1,        0,          0,         1, '4 [EN] category šųößлы',      '',                                 '',                                 '',           0,               0,             0,           0,           '3 [DE] category šųößлы',      '',                          '',                          NULL,    0,                 1),
                           ('testcat4',      'oxrootid',      1,        2,        'testcat4',      7,        0,          0,         1, '5 [EN] category šųößлы',      '',                                 '',                                 '',           0,               0,             0,           0,           '4 [DE] category šųößлы',      '',                          '',                          NULL,    0,                 1),
                           ('testcat5',      'oxrootid',      1,        2,        'testcat5',      1,        0,          0,         1, '6 [EN] category šųößлы',      '',                                 '',                                 '',           0,               0,             0,           0,           '5 [DE] category šųößлы',      '',                          '',                          NULL,    0,                 1),
                           ('testcat7',      'oxrootid',      1,        2,        'testcat7',      6,        0,          0,         1, '8 [EN] category šųößлы',      '',                                 '',                                 '',           0,               0,             0,           0,           '7 [DE] category šųößлы',      '',                          '',                          NULL,    0,                 1),
                           ('testcat8',      'oxrootid',      1,        2,        'testcat8',      6,        0,          0,         1, '2 [EN] category šųößлы',      '',                                 '',                                 '',           0,               0,             0,           0,           '8 [DE] category šųößлы',      '',                          '',                          NULL,    0,                 1),
                           ('testcat9',      'oxrootid',      1,        2,        'testcat9',      3,        0,          0,         1, '1 [EN] category šųößлы',      '',                                 '',                                 '',           0,               0,             0,           0,           '[last] [DE] category šųößлы', '',                          '',                          NULL,    0,                 1),
                           ('testpricecat',  'oxrootid',      1,        2,        'testpricecat',  99999,    1,          0,         1, 'price [EN] šųößлы',           'price category [EN] šųößлы',       '',                                 '',           0,               49,            60,          1,           'price šųößлы [DE]',           'price category [DE]',       '',                          NULL,    0,                 1);

INSERT INTO `oxcategory2attribute` (`OXID`,                       `OXOBJECTID`,    `OXATTRID`,      `OXSORT`) VALUES
                                   ('aad47a8513455b7d6.10119791', 'testcategory0', 'testattribute3', 2),
                                   ('aad47a85137a4df07.58761869', 'testcategory0', 'testattribute2', 3),
                                   ('aad47a8513b78e399.56325257', 'testcategory0', 'testattribute1', 1);

INSERT INTO `oxcontents` (`OXID`,         `OXLOADID`,          `OXSHOPID`,  `OXSNIPPET`, `OXTYPE`, `OXACTIVE`, `OXACTIVE_1`, `OXPOSITION`, `OXTITLE`,                    `OXCONTENT`,                       `OXTITLE_1`,                  `OXCONTENT_1`,              `OXCATID`,                    `OXFOLDER`) VALUES
                         ('testcontent1', '[last]testcontent', 1, 0,           2,        0,          0,           '',           '[last] [EN] content šųößлы', '<p>content [EN] 1  šųößлы</p>',   '1 [DE] content šųößлы',      '<p>content [DE] 1</p>',    'testcategory0',              'CMSFOLDER_USERINFO'),
                         ('testcontent2', '1testcontent',      1, 0,           2,        0,          0,           '',           '3 [EN] content šųößлы',      '<p>content [EN] last šųößлы</p>', '[last] [DE] content šųößлы', '<p>content [DE] last</p>', 'testcategory1',              'CMSFOLDER_USERINFO'),
                         ('testcontent3', 't3testcontent',     1, 0,           3,        0,          0,           '',           'T4 [EN] content šųößлы',     '',                                'T2 [DE] content šųößлы',     '',                         '8a142c3e4143562a5.46426637', 'CMSFOLDER_USERINFO'),
                         ('testcontent4', 't4testcontent',     1, 0,           3,        0,          0,           '',           'T1 [EN] content šųößлы',     '',                                'T4 [DE] content šųößлы',     '',                         '30e44ab834ea42417.86131097', 'CMSFOLDER_USERINFO'),
                         ('testcontent6', 't5testcontent',     1, 0,           3,        0,          0,           '',           'T5 [EN] content šųößлы',     '',                                'T6 [DE] content šųößлы',     '',                         '30e44ab834ea42417.86131097', 'CMSFOLDER_USERINFO');

INSERT INTO `oxcountry` (`OXID`,        `OXACTIVE`, `OXTITLE`,                       `OXISOALPHA2`, `OXISOALPHA3`, `OXUNNUM3`, `OXORDER`, `OXSHORTDESC`,                        `OXTITLE_1`,                     `OXSHORTDESC_1`,               `OXVATSTATUS`) VALUES
                        ('testcountry1', 0,         '[last] EN test Country šųößлы', '',            '111',         '',          0,        '1 EN test Country desc šųößлы',      '1 DE test Country šųößлы',      '[last] DE test Country desc',  0),
                        ('testcountry2', 0,         '1 EN test Country šųößлы',      '',            '000',         '',          0,        '[last] EN test Country desc šųößлы', '[last] DE test Country šųößлы', '1 DE test Country desc',       0);

INSERT INTO `oxdel2delset` (`OXID`,                       `OXDELID`,    `OXDELSETID`) VALUES
                           ('aad47a84ad9a58285.78216385', 'testdel',    'testdelset'),
                           ('aad47a84ad9a622c6.82815540', 'testdelart', 'testdelset');

INSERT INTO `oxdelivery` (`OXID`,       `OXSHOPID`,  `OXACTIVE`, `OXTITLE`,                            `OXTITLE_1`,                          `OXADDSUMTYPE`, `OXADDSUM`, `OXDELTYPE`, `OXPARAM`, `OXPARAMEND`, `OXFIXED`, `OXSORT`, `OXFINALIZE`) VALUES
                         ('testdel',    1, 1,         'Test delivery category [EN] šųößлы', 'Test delivery category [DE] šųößлы', 'abs',           1.5,       'a',          1,         99999,        0,         9998,     1),
                         ('testdelart', 1, 1,         'Test delivery product [EN] šųößлы',  'Test delivery product [DE] šųößлы',  '%',             1,         'a',          1,         99999,        0,         9999,     0),
                         ('testsh1',    1, 0,         '[last] EN S&H šųößлы',               '1 DE S&H šųößлы',                    'abs',           0,         'a',          0,         0,            0,         4,        0),
                         ('testsh2',    1, 0,         '3 EN S&H šųößлы',                    '2 DE S&H šųößлы',                    'abs',           0,         'a',          0,         0,            0,         1,        0),
                         ('testsh3',    1, 0,         '4 EN S&H šųößлы',                    '3 DE S&H šųößлы',                    'abs',           0,         'a',          0,         0,            0,         999999,   0),
                         ('testsh5',    1, 0,         '1 EN S&H šųößлы',                    '[last] DE S&H šųößлы',               'abs',           0,         'a',          0,         0,            0,         2,        0);

INSERT INTO `oxdeliveryset` (`OXID`,       `OXSHOPID`,  `OXACTIVE`, `OXTITLE`,                       `OXTITLE_1`,                    `OXPOS`) VALUES
                            ('testdelset', 1, 1,         'Test S&H set [EN] šųößлы',      'Test S&H set [DE] šųößлы',      0),
                            ('testshset1', 1, 0,         '[last] EN test S&H set šųößлы', '1 DE test S&H set šųößлы',      0),
                            ('testshset2', 1, 0,         '3 EN test S&H set šųößлы',      '2 DE test S&H set šųößлы',      0),
                            ('testshset3', 1, 0,         '4 EN test S&H set šųößлы',      '3 DE test S&H set šųößлы',      0),
                            ('testshset5', 1, 0,         '6 EN test S&H set šųößлы',      '5 DE test S&H set šųößлы',      0),
                            ('testshset4', 1, 0,         '2 EN test S&H set šųößлы',      '4 DE test S&H set šųößлы',      0),
                            ('testshset7', 1, 0,         '1 EN test S&H set šųößлы',      '[last] DE test S&H set šųößлы', 0),
                            ('testshset8', 1, 0,         '7 EN test S&H set šųößлы',      '7 DE test S&H set šųößлы',      0);

INSERT INTO `oxdiscount` (`OXID`,            `OXSHOPID`,  `OXACTIVE`, `OXTITLE`,                           `OXTITLE_1`,              `OXAMOUNT`, `OXAMOUNTTO`, `OXPRICETO`, `OXPRICE`, `OXADDSUMTYPE`, `OXADDSUM`, `OXITMARTID`, `OXITMAMOUNT`, `OXITMMULTIPLE`, `OXSORT`) VALUES
                         ('testcatdiscount', 1, 1,         'discount for category [EN] šųößлы', 'discount for category [DE] šųößлы', 1,          999999,       0,           0,        'abs',           5,          '',           0,             0,               100),
                         ('testartdiscount', 1, 1,         'discount for product [EN] šųößлы',  'discount for product [DE] šųößлы',  0,          0,            999999,      100,      '%',             10,         '',           0,             0,               110),
                         ('testdiscount1',   1, 1,         '[last] EN test discount šųößлы',    '1 DE test discount šųößлы',         0,          999999,       999999,      0,        'abs',           5,          '',           0,             0,               120),
                         ('testdiscount2',   1, 0,         '3 EN test discount šųößлы',         '2 DE test discount šųößлы',         0,          0,            0,           0,        'abs',           0,          '',           0,             0,               130),
                         ('testdiscount3',   1, 0,         '2 EN test discount šųößлы',         '3 DE test discount šųößлы',         0,          0,            0,           0,        'abs',           0,          '',           0,             0,               140),
                         ('testdiscount4',   1, 0,         '4 EN test discount šųößлы',         '4 DE test discount šųößлы',         0,          0,            0,           0,        'abs',           0,          '',           0,             0,               150),
                         ('testdiscount5',   1, 0,         '1 EN test discount šųößлы',         '[last] DE test discount šųößлы',    0,          0,            0,           0,        'abs',           0,          '',           0,             0,               160),
                         ('testitmdiscount', 1, 1,         'Itm discount [EN] šųößлы',          'Itm discount [DE] šųößлы',          5,          999999,       0,           0,        'itm',           0,          '1003',       1,             0,               170);






INSERT INTO `oxgroups` (`OXID`,      `OXACTIVE`, `OXTITLE`,                  `OXTITLE_1`) VALUES
                       ('testgroup1', 0,         '1 user Group šųößлы',      '1 user Group šųößлы'),
                       ('testgroup2', 0,         '2 user Group šųößлы',      '2 user Group šųößлы'),
                       ('testgroup3', 0,         '3 user Group šųößлы',      '3 user Group šųößлы'),
                       ('testgroup4', 0,         'Z user Group šųößлы',      'Z user Group šųößлы'),
                       ('testgroup5', 0,         '[last] user Group šųößлы', '[last] user Group šųößлы');

INSERT INTO `oxmanufacturers` (`OXID`,              `OXSHOPID`,  `OXACTIVE`, `OXTITLE`,                       `OXSHORTDESC`,                               `OXTITLE_1`,                     `OXSHORTDESC_1`,                     `OXSHOWSUFFIX`) VALUES
                              ('testmanufacturer',  1, 1,         'Manufacturer [EN] šųößлы',      'Manufacturer description [EN] šųößлы',      'Manufacturer [DE] šųößлы',      'Manufacturer description [DE]',      1),
                              ('testmanufacturer1', 1, 0,         '[last] EN manufacturer šųößлы', '1 EN manufacturer description šųößлы',      '1 DE manufacturer šųößлы',      '[last] DE manufacturer description', 1),
                              ('testmanufacturer2', 1, 0,         '4 EN manufacturer šųößлы',      '2 EN manufacturer description šųößлы',      '2 DE manufacturer šųößлы',      '4 DE manufacturer description',      1),
                              ('testmanufacturer4', 1, 0,         '2 EN manufacturer šųößлы',      '4 EN manufacturer description šųößлы',      '4 DE manufacturer šųößлы',      '2 DE manufacturer description',      1),
                              ('testmanufacturer5', 1, 0,         '7 EN manufacturer šųößлы',      '5 EN manufacturer description šųößлы',      '5 DE manufacturer šųößлы',      '7 DE manufacturer description',      1),
                              ('testmanufacturer6', 1, 0,         '6 EN manufacturer šųößлы',      '6 EN manufacturer description šųößлы',      '6 DE manufacturer šųößлы',      '6 DE manufacturer description',      1),
                              ('testmanufacturer7', 1, 0,         '3 EN manufacturer šųößлы',      '7 EN manufacturer description šųößлы',      '7 DE manufacturer šųößлы',      '3 DE manufacturer description',      1),
                              ('testmanufacturer8', 1, 0,         '1 EN manufacturer šųößлы',      '[last] EN manufacturer description šųößлы', '[last] DE manufacturer šųößлы', '1 DE manufacturer description',      1),
                              ('testmanufacturer3', 1, 0,         '5 EN manufacturer šųößлы',      '3 EN manufacturer description šųößлы',      '3 DE manufacturer šųößлы',      '5 DE manufacturer description',      1);

INSERT INTO `oxobject2article` (`OXID`,                       `OXOBJECTID`, `OXARTICLENID`, `OXSORT`) VALUES
                               ('e2647c561fa38ba79.72969512', '1003',       '1000',          0);

INSERT INTO `oxobject2attribute` (`OXID`,                       `OXOBJECTID`, `OXATTRID`,       `OXVALUE`,                  `OXPOS`, `OXVALUE_1`) VALUES
                                 ('aad47a8511f54e023.54090494', '1000',       'testattribute1', 'attr value 1 [EN] šųößлы',  0,      'attr value 1 [DE]'),
                                 ('aad47a8511f556f17.20889862', '1001',       'testattribute1', 'attr value 11 [EN] šųößлы', 0,      'attr value 11 [DE]'),
                                 ('aad47a85125a41ed7.53096100', '1000',       'testattribute2', 'attr value 2 [EN] šųößлы',  0,      'attr value 2 [DE]'),
                                 ('aad47a85125a4aa05.37412863', '1001',       'testattribute2', 'attr value 12 [EN] šųößлы', 0,      'attr value 12 [DE]'),
                                 ('aad47a8512d783995.31168870', '1000',       'testattribute3', 'attr value 3 [EN] šųößлы',  0,      'attr value 3 [DE]'),
                                 ('aad47a8512d78c354.06494034', '1001',       'testattribute3', 'attr value 3 [EN] šųößлы',  0,      'attr value 3 [DE]');

INSERT INTO `oxobject2category` (`OXID`,                       `OXOBJECTID`, `OXCATNID`,     `OXPOS`, `OXTIME`) VALUES
                                ('6f047a71f53e3b6c2.93342239', '1000',       'testcategory0', 0,       1202134867),
                                ('6f047a727222a8779.49668732', '1001',       'testcategory0', 0,       1202136866),
                                ('fa647a8289f289701.98087425', '1003',       'testcategory1', 0,       1202202783),
                                ('fa647a8289f27d717.39544758', '1002',       'testcategory1', 0,       1202202783);

INSERT INTO `oxobject2delivery` (`OXID`,                       `OXDELIVERYID`, `OXOBJECTID`,                 `OXTYPE`) VALUES
                                ('aad47a8495555ed87.12642087', 'testdel',      'a7c40f631fc920687.20179984', 'oxcountry'),
                                ('aad47a84a1001a5e9.75786355', 'testdel',      'testcategory1',              'oxcategories'),
                                ('aad47a84ac9134f27.15315975', 'testdelart',   '1001',                       'oxarticles'),
                                ('aad47a84b027f44c2.52893233', 'testdelset',   'a7c40f631fc920687.20179984', 'oxdelset');

INSERT INTO `oxobject2discount` (`OXID`,                       `OXDISCOUNTID`,    `OXOBJECTID`,                 `OXTYPE`) VALUES
                                ('fa647a823ce118996.58546955', 'testcatdiscount', 'a7c40f631fc920687.20179984', 'oxcountry'),
                                ('fa647a823d5079104.99115703', 'testcatdiscount', 'testcategory0',              'oxcategories'),
                                ('fa647a825889edd04.54821317', 'testartdiscount', 'a7c40f631fc920687.20179984', 'oxcountry'),
                                ('fa647a825889f47a5.25840152', 'testartdiscount', 'a7c40f6321c6f6109.43859248', 'oxcountry'),
                                ('fa647a825b4bfe8d8.63031920', 'testartdiscount', '1003',                       'oxarticles'),
                                ('fa647a825bc525754.62252573', 'testartdiscount', '1002',                       'oxarticles'),
                                ('b0047c42fb0a02a65.13519853', 'testitmdiscount', 'a7c40f631fc920687.20179984', 'oxcountry'),
                                ('b0047c42fc71aa294.67674839', 'testitmdiscount', '1000',                       'oxarticles'),
                                ('58cebc1b37ecc6bf240195b47d', 'testdiscount1',   '8a142c3e60a535f16.78077188', 'oxcategories');

INSERT INTO `oxobject2group` (`OXID`,                       `OXSHOPID`,   `OXOBJECTID`,  `OXGROUPSID`) VALUES
                             ('6f047a71c6bbe7f16.16184766', 1, 'testpayment', 'oxidnewcustomer'),
                             ('aad47a85a83749c71.33568408', 1, 'testuser',    'oxidnewcustomer'),
                             ('aad47a861da01a378.98461160', 1, 'testusera',   'oxidnewcustomer'),
                             ('aad47a861da025017.57895315', 1, 'testusera',   'oxidpricea'),
                             ('aad47a86343b2c222.48056509', 1, 'testuserb',   'oxidnewcustomer'),
                             ('aad47a86343b36f87.26955455', 1, 'testuserb',   'oxidpriceb'),
                             ('aad47a868601ed778.03775700', 1, 'testuserc',   'oxidnewcustomer'),
                             ('aad47a868601f8420.26771882', 1, 'testuserc',   'oxidpricec'),
                             ('aad47a869f4828b24.16206885', 1, 'testnews1',   'oxidnewcustomer'),
                             ('aad47a86bb3afd568.93683720', 1, 'testnews2',   'oxidnewcustomer'),
                             ('aad47a87240deb323.16605440', 1, 'testcoupon2', 'oxidnewcustomer'),
                             ('aad47a872468a9285.09755579', 1, 'testcoupon1', 'oxidnewcustomer');

INSERT INTO `oxobject2payment` (`OXID`,                       `OXPAYMENTID`,    `OXOBJECTID`,                 `OXTYPE`) VALUES
                               ('fa647a82238638526.04996237', 'testpayment',    'a7c40f631fc920687.20179984', 'oxcountry'),
                               ('aad47a84ae8d74c27.43568020', 'testpayment',    'testdelset',                 'oxdelset'),
                               ('aad47a84aec2fcea4.29746738', 'oxidcashondel',  'testdelset',                 'oxdelset'),
                               ('aad47a84afba84197.13924353', 'oxidcreditcard', 'testdelset',                 'oxdelset');

INSERT INTO `oxobject2selectlist` (`OXID`,                       `OXOBJECTID`, `OXSELNID`,   `OXSORT`) VALUES
                                  ('aad47a85773148362.54224248', '1001',       'testsellist', 0);

INSERT INTO `oxorder` (`OXID`,        `OXSHOPID`,   `OXUSERID`,  `OXORDERDATE`,        `OXORDERNR`, `OXBILLEMAIL`,     `OXBILLFNAME`, `OXBILLLNAME`,     `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLCITY`, `OXBILLCOUNTRYID`,            `OXBILLZIP`, `OXBILLFON`, `OXBILLSAL`, `OXPAYMENTID`,      `OXPAYMENTTYPE`, `OXTOTALNETSUM`,  `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`, `OXDELCOST`, `OXPAYCOST`, `OXCURRENCY`, `OXCURRATE`, `OXFOLDER`,             `OXPAID`,             `OXSTORNO`,`OXTRANSSTATUS`, `OXDELTYPE`) VALUES
                      ('testorder1',  1, 'testuser6', '2008-04-21 15:02:54', 10,         'example04@oxid-esales.dev',    '3userįÄк',    '3UserSurnameįÄк', '6 Street',       '1',              '7 City',     'a7c40f6320aeb2ec2.72885259', '111000',    '222222',    'Mr',        'oxuserpayments1',  'oxidcashondel',  16.806722689076,  20,               53.8,              12.9,        20.9,       'EUR',         1,          'ORDERFOLDER_NEW',      '0000-00-00 00:00:00', 0,        'OK',            '1b842e732a23255b1.91207750'),
                      ('testorder10', 1, 'testuser6', '2008-04-21 15:02:12', 9,          'example04@oxid-esales.dev',    '3userįÄк',    '3UserSurnameįÄк', '6 Street',       '1',              '7 City',     'a7c40f6320aeb2ec2.72885259', '111000',    '222222',    'Mr',        'oxuserpayments10', 'oxidinvoice',    1.5126050420168,  1.8,              14.7,              12.9,        0,          'EUR',         1,          'ORDERFOLDER_NEW',      '2008-04-21 15:08:11', 0,        'OK',            '1b842e732a23255b1.91207750'),
                      ('testorder11', 1, 'testuser2', '2008-04-21 14:50:44', 3,          'example03@oxid-esales.dev',    '2userįÄк',    '2UserSurnameįÄк', '2 Street',       '1',              '3 City',     'a7c40f6320aeb2ec2.72885259', '444000',    '555555',    'Mr',        'oxuserpayments11', 'oxidinvoice',    5.0420168067227,  6,                18.9,              12.9,        0,          'EUR',         1,          'ORDERFOLDER_FINISHED', '0000-00-00 00:00:00', 0,        'OK',            '1b842e732a23255b1.91207750'),
                      ('testorder2',  1, 'testuser6', '2008-04-21 15:07:46', 11,         'example04@oxid-esales.dev',    '3userįÄк',    '3UserSurnameįÄк', '6 Street',       '1',              '7 City',     'a7c40f6320aeb2ec2.72885259', '111000',    '222222',    'Mr',        'oxuserpayments2',  'oxidcashondel',  3.0252100840336,  3.6,              37.4,              12.9,        20.9,       'EUR',         1,          'ORDERFOLDER_NEW',      '2008-04-21 15:08:47', 0,        'OK',            '1b842e732a23255b1.91207750'),
                      ('testorder3',  1, 'testuser6', '2008-04-21 14:52:59', 5,          'example04@oxid-esales.dev',    '3userįÄк',    '3UserSurnameįÄк', '6 Street',       '1',              '7 City',     'a7c40f6320aeb2ec2.72885259', '111000',    '222222',    'Mr',        'oxuserpayments3',  'oxidcashondel',  2.5210084033613,  3,                36.8,              12.9,        20.9,       'EUR',         1,          'ORDERFOLDER_NEW',      '0000-00-00 00:00:00', 0,        'OK',            '1b842e732a23255b1.91207750'),
                      ('testorder4',  1, 'testuser1', '2008-04-21 15:00:38', 8,          'example02@oxid-esales.dev',    '1userįÄк',    '1UserSurnameįÄк', '1 Street',       '1',              '2 City',     'a7c40f631fc920687.20179984', '333000',    '444444',    'Mr',        'oxuserpayments4',  'oxidcashondel',  3.3613445378151,  4,                37.8,              12.9,        20.9,       'EUR',         1,          'ORDERFOLDER_NEW',      '0000-00-00 00:00:00', 0,        'OK',            '1b842e732a23255b1.91207750'),
                      ('testorder5',  1, 'testuser6', '2008-04-21 14:54:33', 6,          'example04@oxid-esales.dev',    '3userįÄк',    '3UserSurnameįÄк', '6 Street',       '1',              '7 City',     'a7c40f6320aeb2ec2.72885259', '111000',    '222222',    'Mr',        'oxuserpayments5',  'oxidcashondel',  4.2857142857143,  5.1,              38.9,              12.9,        20.9,       'EUR',         1,          'ORDERFOLDER_NEW',      '2008-04-21 15:08:26', 0,        'OK',            '1b842e732a23255b1.91207750'),
                      ('testorder6',  1, 'testuser6', '2008-04-21 14:51:51', 4,          'example04@oxid-esales.dev',    '3userįÄк',    '3UserSurnameįÄк', '6 Street',       '1',              '7 City',     'a7c40f6320aeb2ec2.72885259', '111000',    '222222',    'Mr',        'oxuserpayments6',  'oxidcashondel',  7.563025210084,   9,                42.8,              12.9,        20.9,       'EUR',         1,          'ORDERFOLDER_NEW',      '0000-00-00 00:00:00', 0,        'OK',            '1b842e732a23255b1.91207750'),
                      ('testorder7',  1, 'testuser1', '2008-04-21 14:35:56', 1,          'example02@oxid-esales.dev',    '1userįÄк',    '1UserSurnameįÄк', '1 Street',       '1',              '2 City',     'a7c40f631fc920687.20179984', '333000',    '444444',    'Mr',        'oxuserpayments7',  'oxidinvoice',    1.2605042016807,  1.5,              5.4,               3.9,         0,          'EUR',         1,          'ORDERFOLDER_NEW',      '0000-00-00 00:00:00', 0,        'OK',            'oxidstandard'),
                      ('testorder8',  1, 'testuser2', '2008-04-21 14:59:08', 7,          'example03@oxid-esales.dev',    '2userįÄк',    '2UserSurnameįÄк', '2 Street',       '1',              '3 City',     'a7c40f6320aeb2ec2.72885259', '444000',    '555555',    'Mr',        'oxuserpayments8',  'oxidcashondel',  1.6806722689076,  2,                35.8,              12.9,        20.9,       'EUR',         1,          'ORDERFOLDER_PROBLEMS', '0000-00-00 00:00:00', 0,        'OK',            '1b842e732a23255b1.91207750'),
                      ('testorder9',  1, 'testuser1', '2008-04-21 14:48:51', 2,          'example02@oxid-esales.dev', '1userįÄк',    '1UserSurnameįÄк', '1 Street',     '1',              '2 City',     'a7c40f631fc920687.20179984', '333000',    '444444',    'Mr',        'oxuserpayments9',  'oxidinvoice',    1.5126050420168,  1.8,              5.7,               3.9,         0,          'EUR',         1,          'ORDERFOLDER_NEW',      '2008-04-21 15:14:02', 0,        'OK',            'oxidstandard');

UPDATE `oxcounters` SET oxcount = 11 WHERE oxident = 'oxOrder';

INSERT INTO `oxorderarticles` (`OXID`,         `OXORDERID`,  `OXAMOUNT`, `OXARTID`, `OXARTNUM`, `OXTITLE`,              `OXSHORTDESC`,                  `OXNETPRICE`,    `OXBRUTPRICE`, `OXVATPRICE`,     `OXVAT`, `OXPRICE`, `OXBPRICE`, `OXNPRICE`,       `OXSTOCK`, `OXINSERT`,   `OXTIMESTAMP`,         `OXORDERSHOPID`) VALUES
                              ('testordart1',  'testorder4',  2,         '10012',   '10012',    '12 EN product šųößлы', '11 EN description šųößлы',      3.3613445378151, 4,             0.63865546218487, 19,      2,         4,          3.3613445378151,  0,        '2008-04-03', '2008-04-03 12:50:20', 1),
                              ('testordart10', 'testorder2',  2,         '10011',   '10011',    '11 EN product šųößлы', '10 EN description šųößлы',      3.0252100840336, 3.6,           0.57478991596639, 19,      1.8,       3.6,        3.0252100840336,  0,        '2008-04-03', '2008-04-03 12:50:20', 1),
                              ('testordart11', 'testorder5',  1,         '10010',   '10010',    '1 EN product šųößлы',  '[last] EN description šųößлы',  1.2605042016807, 1.5,           0.23949579831933, 19,      1.5,       1.5,        1.2605042016807,  0,        '2008-04-03', '2008-04-17 17:40:02', 1),
                              ('testordart12', 'testorder5',  2,         '10011',   '10011',    '11 EN product šųößлы', '10 EN description šųößлы',      3.0252100840336, 3.6,           0.57478991596639, 19,      1.8,       3.6,        3.0252100840336,  0,        '2008-04-03', '2008-04-03 12:50:20', 1),
                              ('testordart2',  'testorder6',  5,         '10011',   '10011',    '11 EN product šųößлы', '10 EN description šųößлы',      7.563025210084,  9,             1.436974789916,   19,      1.8,       9,          7.563025210084,   0,        '2008-04-03', '2008-04-03 12:50:20', 1),
                              ('testordart3',  'testorder7',  1,         '10010',   '10010',    '1 EN product šųößлы',  '[last] EN description šųößлы',  1.2605042016807, 1.5,           0.23949579831933, 19,      1.5,       1.5,        1.2605042016807,  1,        '2008-04-03', '2008-04-17 17:40:02', 1),
                              ('testordart4',  'testorder1',  10,        '10012',   '10012',    '12 EN product šųößлы', '11 EN description šųößлы',      16.806722689076, 20,            3.1932773109244,  19,      2,         20,         16.806722689076,  0,        '2008-04-03', '2008-04-03 12:50:20', 1),
                              ('testordart5',  'testorder9',  1,         '10011',   '10011',    '11 EN product šųößлы', '10 EN description šųößлы',      1.5126050420168, 1.8,           0.28739495798319, 19,      1.8,       1.8,        1.5126050420168,  0,        '2008-04-03', '2008-04-03 12:50:20', 1),
                              ('testordart6',  'testorder10', 1,         '10011',   '10011',    '11 EN product šųößлы', '10 EN description šųößлы',      1.5126050420168, 1.8,           0.28739495798319, 19,      1.8,       1.8,        1.5126050420168,  0,        '2008-04-03', '2008-04-03 12:50:20', 1),
                              ('testordart7',  'testorder11', 3,         '10012',   '10012',    '12 EN product šųößлы', '11 EN description šųößлы',      5.0420168067227, 6,             0.95798319327731, 19,      2,         6,          5.0420168067227,  0,        '2008-04-03', '2008-04-03 12:50:20', 1),
                              ('testordart8',  'testorder3',  2,         '10010',   '10010',    '1 EN product šųößлы',  '[last] EN description šųößлы',  2.5210084033613, 3,             0.47899159663866, 19,      1.5,       3,          2.5210084033613,  0,        '2008-04-03', '2008-04-17 17:40:02', 1),
                              ('testordart9',  'testorder8',  1,         '10012',   '10012',    '12 EN product šųößлы', '11 EN description šųößлы',      1.6806722689076, 2,             0.31932773109244, 19,      2,         2,          1.6806722689076,  0,        '2008-04-03', '2008-04-03 12:50:20', 1);

INSERT INTO `oxpayments` (`OXID`,         `OXACTIVE`, `OXDESC`,                         `OXADDSUM`, `OXADDSUMTYPE`, `OXFROMBONI`, `OXFROMAMOUNT`, `OXTOAMOUNT`, `OXVALDESC`,                 `OXCHECKED`, `OXDESC_1`,                        `OXVALDESC_1`,            `OXLONGDESC`,                            `OXLONGDESC_1`,                  `OXSORT`) VALUES
                         ('testpayment',   1,         'Test payment method [EN] šųößлы', 0.7,       'abs',           0,            55,             99999,       'payment field [EN] įÄк__@@', 0,          'Test payment method [DE] šųößлы', 'payment field [DE]__@@', 'Short payment description [EN] šųößлы', 'Short payment description [DE]', 0),
                         ('testpayment3',  0,         '2 EN test payment šųößлы',        0,         'abs',           0,            0,              0,           '',                           0,          '3 DE test payment šųößлы',        '',                       '',                                      '',                               1),
                         ('testpayment2',  0,         '3 EN test payment šųößлы',        0,         'abs',           0,            0,              0,           '',                           0,          '2 DE test payment šųößлы',        '',                       '',                                      '',                               2),
                         ('testpayment1',  0,         '[last] EN test payment šųößлы',   0,         'abs',           0,            0,              0,           '',                           0,          '1 DE test payment šųößлы',        '',                       '',                                      '',                               3),
                         ('testpayment4',  0,         '1 EN test payment šųößлы',        0,         'abs',           0,            0,              0,           '',                           0,          '[last] DE test payment šųößлы',   '',                       '',                                      '',                               4),
                         ('testpayment5',  0,         '4 EN test payment šųößлы',        0,         'abs',           0,            0,              0,           '',                           0,          '4 DE test payment šųößлы',        '',                       '',                                      '',                               5),
                         ('testpayment6',  0,         '5 EN test payment šųößлы',        0,         'abs',           0,            0,              0,           '',                           0,          '5 DE test payment šųößлы',        '',                       '',                                      '',                               6),
                         ('testpayment7',  0,         '6 EN test payment šųößлы',        0,         'abs',           0,            0,              0,           '',                           0,          '6 DE test payment šųößлы',        '',                       '',                                      '',                               7),
                         ('testpayment8',  0,         '7 EN test payment šųößлы',        0,         'abs',           0,            0,              0,           '',                           0,          '7 DE test payment šųößлы',        '',                       '',                                      '',                               8),
                         ('testpayment9',  0,         '8 EN test payment šųößлы',        0,         'abs',           0,            0,              0,           '',                           0,          '8 DE test payment šųößлы',        '',                       '',                                      '',                               9),
                         ('testpayment10', 0,         '9 EN test payment šųößлы',        0,         'abs',           0,            0,              0,           '',                           0,          '9 DE test payment šųößлы',        '',                       '',                                      '',                               10);

INSERT INTO `oxprice2article` (`OXID`,                       `OXSHOPID`,   `OXARTID`, `OXADDABS`, `OXADDPERC`, `OXAMOUNT`, `OXAMOUNTTO`) VALUES
                              ('6f047a734ea2f2cd9.42793951', 1, '1003',     75,         0,           2,          5),
                              ('6f047a73548de8581.49743013', 1, '1003',     0,          20,          6,          9999999);

INSERT INTO `oxselectlist` (`OXID`,          `OXSHOPID`,   `OXTITLE`,                         `OXIDENT`,               `OXVALDESC`,                                                                                      `OXTITLE_1`,                       `OXVALDESC_1`) VALUES
                           ('testsellist',   1, 'test selection list [EN] šųößлы', 'test sellist šųößлы',   'selvar1 [EN] įÄк!P!1__@@selvar2 [EN] įÄк__@@selvar3 [EN] įÄк!P!-2__@@selvar4 [EN] įÄк!P!2%__@@', 'test selection list [DE] šųößлы', 'selvar1 [DE]!P!1__@@selvar2 [DE]__@@selvar3 [DE]!P!-2__@@selvar4 [DE]!P!2%__@@'),
                           ('testsellist1',  1, '[last] [EN] sellist šųößлы',      '1 sellist šųößлы',      '',                                                                                               '1 [DE] sellist šųößлы',           ''),
                           ('testsellist2',  1, '7 [EN] sellist šųößлы',           '6 sellist šųößлы',      '',                                                                                               '2 [DE] sellist šųößлы',           ''),
                           ('testsellist3',  1, '6 [EN] sellist šųößлы',           '2 sellist šųößлы',      '',                                                                                               '3 [DE] sellist šųößлы',           ''),
                           ('testsellist4',  1, '2 [EN] sellist šųößлы',           '9 sellist šųößлы',      '',                                                                                               '4 [DE] sellist šųößлы',           ''),
                           ('testsellist5',  1, '3 [EN] sellist šųößлы',           '4 sellist šųößлы',      '',                                                                                               '5 [DE] sellist šųößлы',           ''),
                           ('testsellist6',  1, '5 [EN] sellist šųößлы',           '8 sellist šųößлы',      '',                                                                                               '6 [DE] sellist šųößлы',           ''),
                           ('testsellist7',  1, '4 [EN] sellist šųößлы',           '3 sellist šųößлы',      '',                                                                                               '7 [DE] sellist šųößлы',           ''),
                           ('testsellist8',  1, '8 [EN] sellist šųößлы',           '[last] sellist šųößлы', '',                                                                                               '8 [DE] sellist šųößлы',           ''),
                           ('testsellist9',  1, '1 [EN] sellist šųößлы',           '5 sellist šųößлы',      '',                                                                                               '9 [DE] sellist šųößлы',           ''),
                           ('testsellist10', 1, '9 [EN] sellist šųößлы',           '7 sellist šųößлы',      '',                                                                                               '[last] [DE] sellist šųößлы',      '');

INSERT INTO `oxuser` (`OXID`,     `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`,   `OXUSERNAME`,         `OXPASSWORD`,                       `OXPASSSALT`,        `OXCUSTNR`, `OXCOMPANY`,        `OXFNAME`,      `OXLNAME`,         `OXSTREET`,      `OXSTREETNR`, `OXADDINFO`,                 `OXCITY`,          `OXCOUNTRYID`,                `OXZIP`,  `OXFON`,       `OXFAX`,       `OXSAL`, `OXBONI`, `OXCREATE`,            `OXREGISTER`,          `OXPRIVFON`,   `OXMOBFON`,    `OXBIRTHDATE`) VALUES
                     ('testuser',  1,         'user',     1, 'example_test@oxid-esales.dev', 'c9dadd994241c9e5fa6469547009328a', '7573657275736572',   8,         'UserCompany įÄк',  'UserNameįÄк',  'UserSurnameįÄк',  'Musterstr.įÄк', '1',          'User additional info įÄк',  'Musterstadt įÄк', 'a7c40f631fc920687.20179984', '79098',  '0800 111111', '0800 111112', 'Mr',     500,     '2008-02-05 14:42:42', '2008-02-05 14:42:42', '0800 111113', '0800 111114', '1980-01-01'),
                     ('testusera', 1,         'user',     1, 'example0a@oxid-esales.dev',    'a233c8b71a465807980f4b2b18f50fec', '757365724175736572', 9,         'UserACompany įÄк', 'UserANameįÄк', 'UserASurnameįÄк', 'Musterstr.įÄк', '2',          'UserA additional info įÄк', 'Musterstadt įÄк', 'a7c40f631fc920687.20179984', '79098',  '0800 222221', '0800 222222', 'Mrs',    0,       '2008-02-05 14:49:31', '2008-02-05 14:49:31', '0800 222223', '0800 222224', '1960-02-02'),
                     ('testuserb', 1,         'user',     1, 'example0b@oxid-esales.dev',    'e5930fc80db1b3a3aaecfa62da5f85f9', '757365724275736572', 10,        'UserBCompany įÄк', 'UserBNameįÄк', 'UserBSurnameįÄк', 'Musterstr.įÄк', '3',          'UserB additional info įÄк', 'Musterstadt įÄк', 'a7c40f631fc920687.20179984', '79098',  '0800 333331', '0800 333332', 'Mr',     0,       '2008-02-05 15:19:46', '2008-02-05 15:19:46', '0800 333333', '0800 333334', '1952-03-03'),
                     ('testuserc', 1,         'user',     1, 'example0c@oxid-esales.dev',    '49a2c0585284909e97957f9b95d85b92', '757365724375736572', 11,        'UserCCompany įÄк', 'UserCNameįÄк', 'UserCSurnameįÄк', 'Musterstr.įÄк', '4',          'UserC additional info įÄк', 'Musterstadt įÄк', 'a7c40f631fc920687.20179984', '79098',  '0800 444441', '0800 444442', 'Mrs',    0,       '2008-02-05 15:26:06', '2008-02-05 15:26:06', '0800 444443', '0800 444444', '1985-04-04'),
                     ('testuser1', 0,         'user',     1, 'example02@oxid-esales.dev',    '82763a21b58be2f21e9bae3ebca59fec', '7573657274657374',   12,        '',                 '1userįÄк',     '1UserSurnameįÄк', '1 Street.įÄк',  '1',          '',                          '2 City įÄк',      'a7c40f631fc920687.20179984', '333000', '444444',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2008-02-01 00:00:00', '',            '',            '0000-00-00'),
                     ('testuser2', 0,         'user',     1, 'example03@oxid-esales.dev',    '82763a21b58be2f21e9bae3ebca59fec', '7573657274657374',   13,        '',                 '2userįÄк',     '2UserSurnameįÄк', '2 Street.įÄк',  '1',          '',                          '3 City įÄк',      'a7c40f631fc920687.20179984', '444000', '555555',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2008-01-10 00:00:00', '',            '',            '0000-00-00'),
                     ('testuser3', 0,         'user',     1, 'example07@oxid-esales.dev',    '82763a21b58be2f21e9bae3ebca59fec', '7573657274657374',   14,        '',                 '6userįÄк',     '6UserSurnameįÄк', '3 Street.įÄк',  '1',          '',                          '4 City įÄк',      'a7c40f631fc920687.20179984', '555000', '666666',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2008-01-10 11:10:00', '',            '',            '0000-00-00'),
                     ('testuser4', 0,         'user',     1, 'example05@oxid-esales.dev',    '82763a21b58be2f21e9bae3ebca59fec', '7573657274657374',   15,        '',                 '4userįÄк',     '4UserSurnameįÄк', '4 Street.įÄк',  '1',          '',                          '5 City įÄк',      'a7c40f631fc920687.20179984', '666000', '777777',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2008-01-10 00:00:01', '',            '',            '0000-00-00'),
                     ('testuser5', 0,         'user',     1, 'example08@oxid-esales.dev',    '82763a21b58be2f21e9bae3ebca59fec', '7573657274657374',   16,        '',                 '7userįÄк',     '7UserSurnameįÄк', '5 Street.įÄк',  '1',          '',                          '6 City įÄк',      'a7c40f631fc920687.20179984', '777000', '111111',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2008-01-10 00:00:02', '',            '',            '0000-00-00'),
                     ('testuser6', 0,         'user',     1, 'example04@oxid-esales.dev',    '82763a21b58be2f21e9bae3ebca59fec', '7573657274657374',   17,        '',                 '3userįÄк',     '3UserSurnameįÄк', '6 Street.įÄк',  '1',          '',                          '7 City įÄк',      'a7c40f631fc920687.20179984', '111000', '222222',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2008-01-10 00:00:03', '',            '',            '0000-00-00'),
                     ('testuser7', 0,         'user',     1, 'example06@oxid-esales.dev',    '82763a21b58be2f21e9bae3ebca59fec', '7573657274657374',   18,        '',                 '5userįÄк',     '5UserSurnameįÄк', '7 Street.įÄк',  '1',          '',                          '1 City įÄк',      'a7c40f631fc920687.20179984', '222000', '333333',      '',            'Mr',     1000,    '2008-04-15 10:34:56', '2007-06-20 00:00:00', '',            '',            '0000-00-00');

INSERT INTO `oxuserpayments` (`OXID`,             `OXUSERID`,  `OXPAYMENTSID`, `OXVALUE`) VALUES
                             ('oxuserpayments1',  'testuser6', 'oxidcashondel', ''),
                             ('oxuserpayments10', 'testuser6', 'oxidinvoice',   ''),
                             ('oxuserpayments11', 'testuser2', 'oxidinvoice',   ''),
                             ('oxuserpayments2',  'testuser6', 'oxidcashondel', ''),
                             ('oxuserpayments3',  'testuser6', 'oxidcashondel', ''),
                             ('oxuserpayments4',  'testuser1', 'oxidcashondel', ''),
                             ('oxuserpayments5',  'testuser6', 'oxidcashondel', ''),
                             ('oxuserpayments6',  'testuser6', 'oxidcashondel', ''),
                             ('oxuserpayments7',  'testuser1', 'oxidinvoice',   ''),
                             ('oxuserpayments8',  'testuser2', 'oxidcashondel', ''),
                             ('oxuserpayments9',  'testuser1', 'oxidinvoice',   '');

INSERT INTO `oxvendor` (`OXID`,             `OXSHOPID`,  `OXACTIVE`, `OXTITLE`,                      `OXSHORTDESC`,                              `OXTITLE_1`,                    `OXSHORTDESC_1`,                    `OXSHOWSUFFIX`) VALUES
                       ('testdistributor',  1, 1,         'Distributor [EN] šųößлы',      'Distributor description [EN] šųößлы',      'Distributor [DE] šųößлы',      'Distributor description [DE]',      1),
                       ('testdistributor1', 1, 0,         '[last] EN distributor šųößлы', '1 EN distributor description šųößлы',      '1 DE distributor šųößлы',      '[last] DE distributor description', 1),
                       ('testdistributor2', 1, 0,         '4 EN distributor šųößлы',      '2 EN distributor description šųößлы',      '2 DE distributor šųößлы',      '4 DE distributor description',      1),
                       ('testdistributor4', 1, 0,         '2 EN distributor šųößлы',      '4 EN distributor description šųößлы',      '4 DE distributor šųößлы',      '2 DE distributor description',      1),
                       ('testdistributor5', 1, 0,         '7 EN distributor šųößлы',      '5 EN distributor description šųößлы',      '5 DE distributor šųößлы',      '7 DE distributor description',      1),
                       ('testdistributor6', 1, 0,         '6 EN distributor šųößлы',      '6 EN distributor description šųößлы',      '6 DE distributor šųößлы',      '6 DE distributor description',      1),
                       ('testdistributor7', 1, 0,         '3 EN distributor šųößлы',      '7 EN distributor description šųößлы',      '7 DE distributor šųößлы',      '3 DE distributor description',      1),
                       ('testdistributor8', 1, 0,         '1 EN distributor šųößлы',      '[last] EN distributor description šųößлы', '[last] DE distributor šųößлы', '1 DE distributor description',      1),
                       ('testdistributor3', 1, 0,         '5 EN distributor šųößлы',      '3 EN distributor description šųößлы',      '3 DE distributor šųößлы',      '5 DE distributor description',      1);

INSERT INTO `oxvouchers` (`OXDATEUSED`, `OXORDERID`, `OXUSERID`, `OXRESERVED`, `OXVOUCHERNR`, `OXVOUCHERSERIEID`, `OXDISCOUNT`, `OXID`) VALUES
                         ( NULL,        '',          '',          0,           '123123',      'testvoucher4',      NULL,        'testcoucher011'),
                         ( NULL,        '',          '',          0,           '111111',      'testcoupon1',       NULL,        'testvoucher001'),
                         ( NULL,        '',          '',          0,           '111111',      'testcoupon1',       NULL,        'testvoucher002'),
                         ( NULL,        '',          '',          0,           '111111',      'testcoupon1',       NULL,        'testvoucher003'),
                         ( NULL,        '',          '',          0,           '111111',      'testcoupon1',       NULL,        'testvoucher004'),
                         ( NULL,        '',          '',          0,           '111111',      'testcoupon1',       NULL,        'testvoucher005'),
                         ( NULL,        '',          '',          0,           '222222',      'testcoupon2',       NULL,        'testvoucher006'),
                         ( NULL,        '',          '',          0,           '222222',      'testcoupon2',       NULL,        'testvoucher007'),
                         ( NULL,        '',          '',          0,           '222222',      'testcoupon2',       NULL,        'testvoucher008'),
                         ( NULL,        '',          '',          0,           '222222',      'testcoupon2',       NULL,        'testvoucher009'),
                         ( NULL,        '',          '',          0,           '222222',      'testcoupon2',       NULL,        'testvoucher010');

INSERT INTO `oxvoucherseries` (`OXID`,         `OXSHOPID`,   `OXSERIENR`,            `OXSERIEDESCRIPTION`,       `OXDISCOUNT`, `OXDISCOUNTTYPE`, `OXBEGINDATE`,         `OXENDDATE`,          `OXALLOWSAMESERIES`, `OXALLOWOTHERSERIES`, `OXALLOWUSEANOTHER`, `OXMINIMUMVALUE`, `OXCALCULATEONCE`) VALUES
                              ('testcoupon1',  1, 'Test coupon 1 šųößлы', 'Test coupon 1 desc šųößлы', 10.00,       'absolute',       '2008-01-01 00:00:00', '2020-01-01 00:00:00', 1,                   1,                    1,                   75.00,            0),
                              ('testcoupon2',  1, 'Test coupon 2 šųößлы', 'Test coupon 2 desc šųößлы', 5.00,        'percent',        '2008-01-01 00:00:00', '2020-01-01 00:00:00', 0,                   0,                    0,                   75.00,            0),
                              ('testvoucher1', 1, '1 Coupon šųößлы',      '1 Description šųößлы',      5.00,        'absolute',       '2007-01-01 00:00:00', '2010-12-31 00:00:00', 0,                   0,                    0,                   10.00,            0),
                              ('testvoucher2', 1, '2 Coupon šųößлы',      '2 Coupon šųößлы',           3.00,        'absolute',       '2009-01-01 00:00:00', '2009-10-10 00:00:00', 0,                   0,                    0,                   25.00,            0),
                              ('testvoucher3', 1, '3 Coupon šųößлы',      '3 Coupon šųößлы',           15.00,       'percent',        '2007-12-31 00:00:00', '2009-12-31 00:00:00', 0,                   0,                    0,                   100.00,           0),
                              ('testvoucher4', 1, '4 Coupon šųößлы',      '4 Coupon šųößлы',           50.00,       'percent',        '2008-01-01 00:00:00', '2010-01-01 00:00:00', 0,                   0,                    0,                   45.00,            0),
                              ('testvoucher5', 1, '5 Coupon šųößлы',      '5 Coupon šųößлы',           30.00,       'percent',        '2008-01-01 00:00:00', '2010-01-01 00:00:00', 0,                   0,                    0,                   300.00,           0),
                              ('testvoucher6', 1, '6 Coupon šųößлы',      '6 Coupon šųößлы',           20.00,       'percent',        '2008-01-01 00:00:00', '2010-01-01 00:00:00', 0,                   0,                    0,                   300.00,           0),
                              ('testvoucher7', 1, '7 Coupon šųößлы',      '7 Coupon šųößлы',           25.00,       'absolute',       '2008-01-01 00:00:00', '2010-01-01 00:00:00', 0,                   0,                    0,                   300.00,           0),
                              ('testvoucher8', 1, '8 Coupon šųößлы',      '8 Coupon šųößлы',           54.00,       'absolute',       '2008-01-01 00:00:00', '2010-01-01 00:00:00', 0,                   0,                    0,                   300.00,           0),
                              ('testvoucher9', 1, '[last] Coupon šųößлы', '9 Coupon šųößлы',           64.00,       'absolute',       '2008-01-01 00:00:00', '2010-01-01 00:00:00', 0,                   0,                    0,                   300.00,           0);

INSERT INTO `oxwrapping` (`OXID`,         `OXSHOPID`,  `OXACTIVE`, `OXACTIVE_1`, `OXACTIVE_2`, `OXACTIVE_3`, `OXTYPE`, `OXNAME`,                         `OXNAME_1`,                      `OXPRICE`) VALUES
                         ('testwrapping', 1, 1,          1,            1,            1,           'WRAP',   'Test wrapping [EN] šųößлы',      'Test wrapping [DE] šųößлы',      0.9),
                         ('testcard',     1, 1,          1,            1,            1,           'CARD',   'Test card [EN] šųößлы',          'Test card [DE] šųößлы',          0.2),
                         ('testwrap2',    1, 1,          0,            0,            0,           'WRAP',   '4 EN Gift Wrapping šųößлы',      '2 DE Gift Wrapping šųößлы',      2),
                         ('testwrap1',    1, 1,          0,            0,            0,           'WRAP',   '3 EN Gift Wrapping šųößлы',      '1 DE Gift Wrapping šųößлы',      1),
                         ('testwrap3',    1, 1,          0,            0,            0,           'WRAP',   '1 EN Gift Wrapping šųößлы',      '3 DE Gift Wrapping šųößлы',      3),
                         ('testwrap4',    1, 1,          0,            0,            0,           'WRAP',   '[last] EN Gift Wrapping šųößлы', '4 DE Gift Wrapping šųößлы',      4),
                         ('testwrap5',    1, 1,          0,            0,            0,           'WRAP',   '2 EN Gift Wrapping šųößлы',      '[last] DE Gift Wrapping šųößлы', 5);

#updating oxconfig settings
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'bl_perfUseSelectlistPrice';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'bl_perfShowActionCatArticleCnt';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'blOtherCountryOrder';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blCheckTemplates';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'blDisableNavBars';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'blAllowUnevenAmounts';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x07         WHERE `OXVARNAME` = 'blConfirmAGB';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x07a1       WHERE `OXVARNAME` = 'dDefaultVAT';
UPDATE `oxconfig` SET `OXVARVALUE` = 0xde         WHERE `OXVARNAME` = 'iNewBasketItemMessage';
UPDATE `oxconfig` SET `OXVARVALUE` = ''           WHERE `OXVARNAME` = 'sShopCountry';
#UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba852e75e64cf5ccd4aea5675a0d492fe42584187fbf8c9b11d3b01306b7f34f3e85a640802d63112081e17db027a004c2dc41dc0f34e296365c35ff74e3bb70693767b16bd7114149643668c6b3b9e95da86203264a2444c2881030198857baaa8be9b555b284a88db85ad196bed825c4cf572b2d3b38b50619fd949938aa1d1e21a66ac76896cbf6f9d004781e610189bf1163dc03c89fb6078b WHERE `OXVARNAME` = 'aCurrencies';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba222b70e349f0c9d1aba6133981af1e8d79724d7309a19dd3eed099418943829510e114c4f6ffcb2543f5856ec4fea325d58b96e406decb977395c57d7cc79eec7f9f8dd6e30e2f68d198bd9d079dbe8b4f WHERE `OXVARNAME` = 'aNrofCatArticles';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dbace29724a51b6af7d09aac117301142e91c3c5b7eed9a850f85c1e3d58739aa9ea92523f05320a95060d60d57fbb027bad88efdaa0b928ebcd6aacf58084d31dd6ed5e718b833f1079b3805d28203f284492955c82cea3405879ea7588ec610ccde56acede495 WHERE `OXVARNAME` = 'aInterfaceProfiles';

INSERT INTO `oxconfig` (`OXID`,                       `OXSHOPID`,   `OXVARNAME`,                     `OXVARTYPE`, `OXVARVALUE`) VALUES
                       ('fb547ac17e4a10201.92066345', 1, 'perf_LoadFullVariantsInLists',  'bool',       0x7900fdf51e),
                       ('35796d0bdbbda3bb54fcd0fb84', 1, 'iMinOrderPrice',                'str',        0xfba4),
                       ('fb547ac17e4a910c8.33968384', 1, 'blLoadSelectBoxAlways',         'bool',       0x93ea1218),
                       ('fb547ac17e4a97dd3.93417742', 1, 'blPerfNoBasketSaving',          'bool',       0x93ea1218),
                       ('33bd5512d7d7366681eb850502', 1, 'blOverrideZeroABCPrices',       'bool',       0x93ea1218),
                       ('34d266d01313cf456a4a1d2c9f', 1, 'blShowOrderButtonOnTop',        'bool',       0x93ea1218),
                       ('fb547ac17e4a5ba72.64297620', 1, 'bl_perfLoadSelectListsInAList', 'bool',       0x93ea1218),
                       ('2b456gjk7156737a6edc6814c5', 1, 'bl_rssBargain',                 'bool',       0x07),
                       ('2bpte85227eb159aedc68164c5', 1, 'bl_rssRecommLists',             'bool',       0x07),
                       ('2b7lojk77123rtdskdj68164c5', 1, 'bl_rssRecommListArts',          'bool',       0x07);

#azure theme config
DELETE FROM `oxconfig` WHERE `OXMODULE` = 'theme:azure' AND `OXVARNAME` = 'iTopNaviCatCount';
INSERT INTO `oxconfig` (`OXID`,                       `OXSHOPID`,   `OXMODULE`,    `OXVARNAME`,        `OXVARTYPE`, `OXVARVALUE`) VALUES
                       ('bd9f44ba4062387b0678d3ad7a', 1, 'theme:azure', 'iTopNaviCatCount', 'str',        0xb0);

#updating smtp and email information
UPDATE `oxshops` SET `OXPRODUCTIVE` = 0, `OXINFOEMAIL` = 'example_test@oxid-esales.dev', `OXORDEREMAIL` = 'example_test@oxid-esales.dev', `OXOWNEREMAIL` = 'example_test@oxid-esales.dev', `OXSMTP` = 'localhost', `OXDEFCAT` = '' WHERE `OXID` = 1;

UPDATE `oxcountry` SET `OXVATSTATUS` = 0 WHERE `OXTITLE` = 'Austria';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Germany';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Austria';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Switzerland';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Liechtenstein';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Italy';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Luxembourg';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'France';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Sweden';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Finland';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'United Kingdom';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Ireland';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Netherlands';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Belgium';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Portugal';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Spain';
UPDATE `oxcountry` SET `OXACTIVE` = 1 WHERE `OXTITLE` = 'Greece';

#additional for features testing
#UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'blCheckForUpdates';
#UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'blLoadDynContents';