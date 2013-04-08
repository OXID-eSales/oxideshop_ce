#Guestbook demodata
INSERT INTO `oxgbentries` (`OXID`,               `OXSHOPID`, `OXUSERID`,       `OXCONTENT`,                       `OXCREATE`,           `OXACTIVE`, `OXVIEWED`) VALUES
                          ('oxsubshopguestbook1', 65,         'testuser',       'Demo guestbook entry [EN] šÄßüл', '2008-01-01 00:00:01', 1,          1),
                          ('oxsubshopguestbook2', 65,         'oxdefaultadmin', 'Demo guestbook entry [DE] šÄßüл', '2008-01-02 00:00:01', 1,          1);

#updating smtp and emails data
UPDATE `oxshops` SET `OXPRODUCTIVE` = 1, `OXINFOEMAIL` = 'birute_test@nfq.lt', `OXORDEREMAIL` = 'birute_test@nfq.lt', `OXOWNEREMAIL` = 'birute_test@nfq.lt', `OXSMTP` = 'mail.nfq.lt', `OXDEFCAT` = '' WHERE `OXID` = '65';
INSERT INTO `oxfiles` (`OXID`, `OXARTID`, `OXFILENAME`, `OXPURCHASEDONLY`) VALUES ('1800l', '1002-1', 'testFile3', '1');

#config demodata
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'bl_perfLoadSelectLists'         AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'bl_perfUseSelectlistPrice'      AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'bl_perfLoadSelectListsInAList'  AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blShowVATForDelivery'           AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blCalcSkontoForDelivery'        AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blShowVATForPayCharge'          AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'bl_perfShowActionCatArticleCnt' AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'blOtherCountryOrder'            AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0xde         WHERE `OXVARNAME` = 'iNewBasketItemMessage'          AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0x7900fdf51e WHERE `OXVARNAME` = 'blCheckTemplates'               AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'blDisableNavBars'               AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0x93ea1218   WHERE `OXVARNAME` = 'blAllowUnevenAmounts'           AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0x07         WHERE `OXVARNAME` = 'blConfirmAGB'                   AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0xb0         WHERE `OXVARNAME` = 'iTopNaviCatCount'               AND `OXSHOPID` = 65 AND `OXMODULE` = 'theme:azure';
UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dbace29724a51b6af7d09aac117301142e91c3c5b7eed9a850f85c1e3d58739aa9ea92523f05320a95060d60d57fbb027bad88efdaa0b928ebcd6aacf58084d31dd6ed5e718b833f1079b3805d28203f284492955c82cea3405879ea7588ec610ccde56acede495 WHERE `OXVARNAME` = 'aInterfaceProfiles' AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = 0x4dba222b70e349f0c9d1aba6133981af1e8d79724d7309a19dd3eed099418943829510e114c4f6ffcb2543f5856ec4fea325d58b96e406decb977395c57d7cc79eec7f9f8dd6e30e2f68d198bd9d079dbe8b4f WHERE `OXVARNAME` = 'aNrofCatArticles' AND `OXSHOPID` = 65;

INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`,               `OXVARTYPE`, `OXVARVALUE`) VALUES
                       ('24402', 65,         'blAllowNegativeStock',    'bool',       0x7900fdf51e),
                       ('84432', 65,         'blBidirectCross',         'bool',       0x7900fdf51e),
                       ('65632', 65,         'blLoadDynContent',        'bool',       0x7900fdf51e),
                       ('67402', 65,         'blDisableNavBars',        'bool',       0x93ea1218),
                       ('0d0f2', 65,         'iMinOrderPrice',          'str',        0xfba4),
                       ('33be2', 65,         'blOverrideZeroABCPrices', 'bool',       0x93ea1218),
                       ('00ae2', 65,         'blUseContentCaching',     'bool',       0x93ea1218),
                       ('33po2', 65,         'blShowOrderButtonOnTop',  'bool',       0x93ea1218),
                       ('14c52', 65,         'bl_rssBargain',           'bool',       0x07),
                       ('2bck2', 65,         'bl_rssRecommLists',       'bool',       0x07),
                       ('2bcr2', 65,         'bl_rssRecommListArts',    'bool',       0x07),
                       ('a6ba2', 65,         'blOrderDisWithoutReg',    'bool',       ''),
                       ('asdf2', 65,         'blBasketExcludeEnabled',  'str',        ''),
                       ('5s8fu', 65,         'blPerfNoBasketSaving',    'bool',       0x93ea1218);

#oxroles demodata
INSERT INTO `oxroles` (`OXID`,                 `OXTITLE`,               `OXSHOPID`, `OXACTIVE`, `OXAREA`) VALUES
                      ('oxsubshopadminrole6',  '1 shop role šÄßüл',      65,          0,          1),
                      ('oxsubshopadminrole7',  '2 shop role šÄßüл',      65,          0,          1),
                      ('oxsubshopadminrole8',  '3 shop role šÄßüл',      65,          0,          1),
                      ('oxsubshopadminrole9',  '4 shop role šÄßüл',      65,          0,          1),
                      ('oxsubshopadminrole10', '10 shop role šÄßüл',     65,          0,          1),
                      ('oxsubshopadminrole11', '9 shop role šÄßüл',      65,          0,          1),
                      ('oxsubshopadminrole12', '8 shop role šÄßüл',      65,          0,          1),
                      ('oxsubshopadminrole13', '7 shop role šÄßüл',      65,          0,          1),
                      ('oxsubshopadminrole14', '6 shop role šÄßüл',      65,          0,          1),
                      ('oxsubshopadminrole15', '5 shop role šÄßüл',      65,          0,          1),
                      ('oxsubshopadminrole16', '[last] shop role šÄßüл', 65,          0,          1);
INSERT INTO `oxfield2role` (`OXFIELDID`,                        `OXTYPE`, `OXROLEID`,           `OXIDX`) VALUES
                           ('42b44bc9934bdb406.85935627',       '',       'oxsubshopadminrole6', 1),
                           ('42b44bc9941a46fd3.13180499',       '',       'oxsubshopadminrole6', 1),
                           ('42b44bc99488c66b1.94059993',       '',       'oxsubshopadminrole6', 1),
                           ('42b44bc9950334951.12393781',       '',       'oxsubshopadminrole6', 1),
                           ('3a6a13b4820fff81c09131cf4c5afcee', '',       'oxsubshopadminrole6', 1);
INSERT INTO `oxobjectrights` (`OXID`,                             `OXOBJECTID`,         `OXGROUPIDX`, `OXOFFSET`, `OXACTION`) VALUES
                             ('sub1b34435f387b40025bb518d950380', 'oxsubshopadminrole6', 512,          0,          1);

#CMS pages demodata
INSERT INTO `oxcontents` (`OXID`,              `OXLOADID`,         `OXSHOPID`, `OXTYPE`, `OXSNIPPET`, `OXACTIVE`, `OXTITLE`,                   `OXCONTENT`,               `OXACTIVE_1`, `OXTITLE_1`,                 `OXCONTENT_1`,             `OXCATID`,                    `OXFOLDER`,           `OXTIMESTAMP`) VALUES
                         ('oxsubshopcontent1', '[last]testcontent', 65,          2,        0,           0,         '1 [DE] content šÄßüл',      'content [DE] 1 šÄßüл',     0,           '[last] [EN] content šÄßüл', 'content [EN] 1  šÄßüл',   'testcategory0',              'CMSFOLDER_USERINFO', '2008-01-01 00:00:01'),
                         ('oxsubshopcontent2', '1testcontent',      65,          2,        0,           0,         '[last] [DE] content šÄßüл', 'content [DE] last šÄßüл',  0,           '3 [EN] content šÄßüл',      'content [EN] last šÄßüл', 'testcategory1',              'CMSFOLDER_USERINFO', '2008-01-01 00:00:01'),
                         ('oxsubshopcontent3', 't3testcontent',     65,          3,        0,           0,         'T2 [DE] content šÄßüл',     '',                         0,           'T4 [EN] content šÄßüл',     '',                        '30e44ab834ea42417.86131097', 'CMSFOLDER_USERINFO', '2008-01-01 00:00:01'),
                         ('oxsubshopcontent4', 't4testcontent',     65,          3,        0,           0,         'T4 [DE] content šÄßüл',     '',                         0,           'T1 [EN] content šÄßüл',     '',                        '30e44ab834ea42417.86131097', 'CMSFOLDER_USERINFO', '2008-01-01 00:00:01'),
                         ('oxsubshopcontent6', 't5testcontent',     65,          3,        0,           0,         'T6 [DE] content šÄßüл',     '',                         0,           'T5 [EN] content šÄßüл',     '',                        '30e44ab834ea42417.86131097', 'CMSFOLDER_USERINFO', '2008-01-01 00:00:01');

#updating smtp and emails data
UPDATE `oxshops` SET `OXPRODUCTIVE` = 1, `OXINFOEMAIL` = 'birute_test@nfq.lt', `OXORDEREMAIL` = 'birute_test@nfq.lt', `OXOWNEREMAIL` = 'birute_test@nfq.lt', `OXSMTP` = 'mail.nfq.lt', `OXDEFCAT` = '' WHERE `OXID` = '65';

#additional for features testing
UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'blCheckForUpdates' AND `OXSHOPID` = 65;
UPDATE `oxconfig` SET `OXVARVALUE` = '' WHERE `OXVARNAME` = 'blLoadDynContents' AND `OXSHOPID` = 65;