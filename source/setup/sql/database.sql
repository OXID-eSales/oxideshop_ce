SET @@session.sql_mode = '';

#
# Table structure for table `oxacceptedterms`
# for storing information user accepted terms version
# created 2010-06-10
#

DROP TABLE IF EXISTS `oxacceptedterms`;

CREATE TABLE `oxacceptedterms` (
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTERMVERSION` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXACCEPTEDTIME` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (`OXUSERID`, `OXSHOPID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxaccessoire2article`
#

DROP TABLE IF EXISTS `oxaccessoire2article`;

CREATE TABLE `oxaccessoire2article` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXARTICLENID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSORT` int(5) NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXARTICLENID` (`OXARTICLENID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxactions`
#

DROP TABLE IF EXISTS `oxactions`;

CREATE TABLE `oxactions` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTYPE` tinyint( 1 ) NOT NULL,
  `OXTITLE` char(128) NOT NULL default '',
  `OXTITLE_1` char(128) NOT NULL default '',
  `OXTITLE_2` char(128) NOT NULL default '',
  `OXTITLE_3` char(128) NOT NULL default '',
  `OXLONGDESC` text NOT NULL,
  `OXLONGDESC_1` text NOT NULL,
  `OXLONGDESC_2` text NOT NULL,
  `OXLONGDESC_3` text NOT NULL,
  `OXACTIVE` tinyint(1) NOT NULL default '1',
  `OXACTIVEFROM` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXACTIVETO` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXPIC`   VARCHAR(128) NOT NULL DEFAULT '',
  `OXPIC_1` VARCHAR(128) NOT NULL DEFAULT '',
  `OXPIC_2` VARCHAR(128) NOT NULL DEFAULT '',
  `OXPIC_3` VARCHAR(128) NOT NULL DEFAULT '',
  `OXLINK`   VARCHAR(128) NOT NULL DEFAULT '',
  `OXLINK_1` VARCHAR(128) NOT NULL DEFAULT '',
  `OXLINK_2` VARCHAR(128) NOT NULL DEFAULT '',
  `OXLINK_3` VARCHAR(128) NOT NULL DEFAULT '',
  `OXSORT` int( 5 ) NOT NULL DEFAULT '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  index(`oxsort`),
  index(`OXTYPE`, `OXACTIVE`, `OXACTIVETO`, `OXACTIVEFROM`)
) ENGINE=MyISAM;

#
# Data for table `oxactions`
#
INSERT INTO `oxactions` (`OXID`, `OXSHOPID`, `OXTYPE`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXLONGDESC`, `OXLONGDESC_1`, `OXLONGDESC_2`, `OXLONGDESC_3`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXPIC`, `OXPIC_1`, `OXPIC_2`, `OXPIC_3`, `OXLINK`, `OXLINK_1`, `OXLINK_2`, `OXLINK_3`, `OXSORT`) VALUES
('oxstart',      'oxbaseshop', 0, 'Startseite unten', 'Start page bottom', '', '', '', '', '', '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '', '', '', '', '', '', 0),
('oxtopstart',   'oxbaseshop', 0, 'Topangebot Startseite', 'Top offer start page', '', '', '', '', '', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '', '', '', '', '', '', 0),
('oxfirststart', 'oxbaseshop', 0, 'Angebot der Woche', 'Week''s Special', '', '', '', '', '', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '', '', '', '', '', '', 0),
('oxbargain',    'oxbaseshop', 0, 'Schnäppchen', 'Bargain', '', '', '', '', '', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '', '', '', '', '', '', 0),
('oxtop5',       'oxbaseshop', 0, 'Topseller', 'Top seller', '', '', '', '', '', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '', '', '', '', '', '', 0),
('oxcatoffer',   'oxbaseshop', 0, 'Kategorien-Topangebot', 'Top offer in categories', '', '', '', '', '', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '', '', '', '', '', '', 0),
('oxnewest',     'oxbaseshop', 0, 'Frisch eingetroffen', 'Just arrived', '', '', '', '', '', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '', '', '', '', '', '', 0),
('oxnewsletter', 'oxbaseshop', 0, 'Newsletter', 'Newsletter', '', '', '', '', '', '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '', '', '', '', '', '', 0);

#
# Table structure for table `oxactions2article`
#

DROP TABLE IF EXISTS `oxactions2article`;

CREATE TABLE `oxactions2article` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXACTIONID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXARTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSORT` int(11) NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXMAINIDX` (`OXSHOPID`,`OXACTIONID`,`OXSORT`),
  KEY `OXARTID` (`OXARTID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxaddress`
#

DROP TABLE IF EXISTS `oxaddress`;

CREATE TABLE `oxaddress` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXADDRESSUSERID` VARCHAR(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXCOMPANY` varchar(255) NOT NULL default '',
  `OXFNAME` varchar(255) NOT NULL default '',
  `OXLNAME` varchar(255) NOT NULL default '',
  `OXSTREET` varchar(255) NOT NULL default '',
  `OXSTREETNR` varchar(16) NOT NULL default '',
  `OXADDINFO` varchar(255) NOT NULL default '',
  `OXCITY` varchar(255) NOT NULL default '',
  `OXCOUNTRY` varchar(255) NOT NULL default '',
  `OXCOUNTRYID` VARCHAR( 32 ) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSTATEID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXZIP` varchar(50) NOT NULL default '',
  `OXFON` varchar(128) NOT NULL default '',
  `OXFAX` varchar(128) NOT NULL default '',
  `OXSAL` varchar(128) NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXUSERID` (`OXUSERID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxadminlog`
#

DROP TABLE IF EXISTS `oxadminlog`;

CREATE TABLE `oxadminlog` (
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSQL` text NOT NULL
) ENGINE=MyISAM;

#
# Table structure for table `oxarticles`
#

DROP TABLE IF EXISTS `oxarticles`;

CREATE TABLE `oxarticles` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXPARENTID` char(32) character set latin1 collate latin1_general_ci NOT NULL  default '',
  `OXACTIVE` tinyint(1) NOT NULL DEFAULT '1',
  `OXACTIVEFROM` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXACTIVETO` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXARTNUM` varchar(255) NOT NULL default '',
  `OXEAN` varchar(13)  NOT NULL default '',
  `OXDISTEAN` varchar(13)  NOT NULL default '',
  `OXMPN` varchar(100) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTITLE` varchar(255) NOT NULL default '',
  `OXSHORTDESC` varchar(255) NOT NULL default '',
  `OXPRICE` double NOT NULL default '0',
  `OXBLFIXEDPRICE` tinyint(1) NOT NULL default '0',
  `OXPRICEA` double NOT NULL default '0',
  `OXPRICEB` double NOT NULL default '0',
  `OXPRICEC` double NOT NULL default '0',
  `OXBPRICE` double NOT NULL default '0',
  `OXTPRICE` double NOT NULL default '0',
  `OXUNITNAME` varchar(32) NOT NULL default '',
  `OXUNITQUANTITY` double NOT NULL default '0',
  `OXEXTURL` varchar(255) NOT NULL default '',
  `OXURLDESC` varchar(255) NOT NULL default '',
  `OXURLIMG` varchar(128) NOT NULL default '',
  `OXVAT` float default NULL,
  `OXTHUMB` varchar(128) NOT NULL default '',
  `OXICON` varchar(128) NOT NULL default '',
  `OXPICSGENERATED` tinyint(3) NOT NULL default '0',
  `OXPIC1` varchar(128) NOT NULL default '',
  `OXPIC2` varchar(128) NOT NULL default '',
  `OXPIC3` varchar(128) NOT NULL default '',
  `OXPIC4` varchar(128) NOT NULL default '',
  `OXPIC5` varchar(128) NOT NULL default '',
  `OXPIC6` varchar(128) NOT NULL default '',
  `OXPIC7` varchar(128) NOT NULL default '',
  `OXPIC8` varchar(128) NOT NULL default '',
  `OXPIC9` varchar(128) NOT NULL default '',
  `OXPIC10` varchar(128) NOT NULL default '',
  `OXPIC11` varchar(128) NOT NULL default '',
  `OXPIC12` varchar(128) NOT NULL default '',
  `OXWEIGHT` double NOT NULL default '0',
  `OXSTOCK` double NOT NULL default '-1',
  `OXSTOCKFLAG` tinyint(1) NOT NULL default '1',
  `OXSTOCKTEXT` varchar(255) NOT NULL default '',
  `OXNOSTOCKTEXT` varchar(255) NOT NULL default '',
  `OXDELIVERY` date NOT NULL default '0000-00-00',
  `OXINSERT` date NOT NULL default '0000-00-00',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `OXLENGTH` double NOT NULL default '0',
  `OXWIDTH` double NOT NULL default '0',
  `OXHEIGHT` double NOT NULL default '0',
  `OXFILE` varchar(128) NOT NULL default '',
  `OXSEARCHKEYS` varchar(255) NOT NULL default '',
  `OXTEMPLATE` varchar(128) NOT NULL default '',
  `OXQUESTIONEMAIL` varchar(255) NOT NULL default '',
  `OXISSEARCH` tinyint(1) NOT NULL default '1',
  `OXISCONFIGURABLE` tinyint NOT NULL DEFAULT '0',
  `OXVARNAME` varchar(255) NOT NULL default '',
  `OXVARSTOCK` int(5) NOT NULL default '0',
  `OXVARCOUNT` int(1) NOT NULL default '0',
  `OXVARSELECT` varchar(255) NOT NULL default '',
  `OXVARMINPRICE` double NOT NULL default '0',
  `OXVARMAXPRICE` double NOT NULL default '0',
  `OXVARNAME_1` varchar(255) NOT NULL default '',
  `OXVARSELECT_1` varchar(255) NOT NULL default '',
  `OXVARNAME_2` varchar(255) NOT NULL default '',
  `OXVARSELECT_2` varchar(255) NOT NULL default '',
  `OXVARNAME_3` varchar(255) NOT NULL default '',
  `OXVARSELECT_3` varchar(255) NOT NULL default '',
  `OXTITLE_1` varchar(255) NOT NULL default '',
  `OXSHORTDESC_1` varchar(255) NOT NULL default '',
  `OXURLDESC_1` varchar(255) NOT NULL default '',
  `OXSEARCHKEYS_1` varchar(255) NOT NULL default '',
  `OXTITLE_2` varchar(255) NOT NULL default '',
  `OXSHORTDESC_2` varchar(255) NOT NULL default '',
  `OXURLDESC_2` varchar(255) NOT NULL default '',
  `OXSEARCHKEYS_2` varchar(255) NOT NULL default '',
  `OXTITLE_3` varchar(255) NOT NULL default '',
  `OXSHORTDESC_3` varchar(255) NOT NULL default '',
  `OXURLDESC_3` varchar(255) NOT NULL default '',
  `OXSEARCHKEYS_3` varchar(255) NOT NULL default '',
  `OXBUNDLEID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXFOLDER` varchar(32) NOT NULL default '',
  `OXSUBCLASS` varchar(32) NOT NULL default '',
  `OXSTOCKTEXT_1` varchar(255) NOT NULL default '',
  `OXSTOCKTEXT_2` varchar(255) NOT NULL default '',
  `OXSTOCKTEXT_3` varchar(255) NOT NULL default '',
  `OXNOSTOCKTEXT_1` varchar(255) NOT NULL default '',
  `OXNOSTOCKTEXT_2` varchar(255) NOT NULL default '',
  `OXNOSTOCKTEXT_3` varchar(255) NOT NULL default '',
  `OXSORT` int(5) NOT NULL default '0',
  `OXSOLDAMOUNT` double NOT NULL default '0',
  `OXNONMATERIAL` int(1) NOT NULL default '0',
  `OXFREESHIPPING` int(1) NOT NULL default '0',
  `OXREMINDACTIVE` int(1) NOT NULL default '0',
  `OXREMINDAMOUNT` double NOT NULL default '0',
  `OXAMITEMID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXAMTASKID` varchar(16) character set latin1 collate latin1_general_ci NOT NULL default '0',
  `OXVENDORID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXMANUFACTURERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSKIPDISCOUNTS` tinyint(1) NOT NULL default '0',
  `OXRATING` double NOT NULL default '0',
  `OXRATINGCNT` int(11) NOT NULL default '0',
  `OXMINDELTIME` int(11) NOT NULL default '0',
  `OXMAXDELTIME` int(11) NOT NULL default '0',
  `OXDELTIMEUNIT` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXUPDATEPRICE` DOUBLE NOT NULL default '0',
  `OXUPDATEPRICEA` DOUBLE NOT NULL default '0',
  `OXUPDATEPRICEB` DOUBLE NOT NULL default '0',
  `OXUPDATEPRICEC` DOUBLE NOT NULL default '0',
  `OXUPDATEPRICETIME` TIMESTAMP NOT NULL,
  `OXISDOWNLOADABLE` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (`OXID`),
  KEY `OXCOUNT` (`OXPARENTID`,`OXSHOPID`),
  KEY `OXSORT` (`OXSORT`),
  KEY `OXSHOPID` (`OXSHOPID`),
  KEY `OXISSEARCH` (`OXISSEARCH`),
  KEY `OXARTNUM` (`OXARTNUM`),
  KEY `OXSTOCK` (`OXSTOCK`),
  KEY `OXSTOCKFLAG` (`OXSTOCKFLAG`),
  KEY `OXINSERT` (`OXINSERT`),
  KEY `OXVARNAME` (`OXVARNAME`),
  KEY `OXACTIVE` (`OXACTIVE`),
  KEY `OXACTIVEFROM` (`OXACTIVEFROM`),
  KEY `OXACTIVETO` (`OXACTIVETO`),
  KEY `OXVENDORID` (`OXVENDORID`),
  KEY `OXMANUFACTURERID` (`OXMANUFACTURERID`),
  KEY `OXSOLDAMOUNT` ( `OXSOLDAMOUNT` ),
  KEY `parentsort` ( `OXPARENTID` , `OXSORT` ),
  KEY `OXUPDATEPRICETIME` ( `OXUPDATEPRICETIME` ),
  KEY `OXISDOWNLOADABLE` ( `OXISDOWNLOADABLE` )
)ENGINE=InnoDB;

#
# Table structure for table `oxartextends`
# created on 2008-05-23
#

DROP TABLE IF EXISTS `oxartextends`;

CREATE TABLE `oxartextends` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXLONGDESC` text NOT NULL,
  `OXLONGDESC_1` text NOT NULL,
  `OXLONGDESC_2` text NOT NULL,
  `OXLONGDESC_3` text NOT NULL,
  `OXTAGS` varchar(255) NOT NULL,
  `OXTAGS_1` varchar(255) NOT NULL,
  `OXTAGS_2` varchar(255) NOT NULL,
  `OXTAGS_3` varchar(255) NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  FULLTEXT KEY `OXTAGS`   (`OXTAGS`),
  FULLTEXT KEY `OXTAGS_1` (`OXTAGS_1`),
  FULLTEXT KEY `OXTAGS_2` (`OXTAGS_2`),
  FULLTEXT KEY `OXTAGS_3` (`OXTAGS_3`)
) ENGINE=MyISAM;

#
# Table structure for table `oxattribute`
#

DROP TABLE IF EXISTS `oxattribute`;

CREATE TABLE `oxattribute` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTITLE` char(128) NOT NULL default '',
  `OXTITLE_1` char(128) NOT NULL default '',
  `OXTITLE_2` char(128) NOT NULL default '',
  `OXTITLE_3` char(128) NOT NULL default '',
  `OXPOS` int(11) NOT NULL default '9999',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `OXDISPLAYINBASKET` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxcaptcha`
#

DROP TABLE IF EXISTS `oxcaptcha`;

CREATE TABLE IF NOT EXISTS `oxcaptcha` (
  `OXID` int(11) NOT NULL AUTO_INCREMENT,
  `OXHASH` char(32) NOT NULL default '',
  `OXTIME` int(11) NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (`OXID`),
  KEY `OXID` (`OXID`,`OXHASH`),
  KEY `OXTIME` (`OXTIME`)
) ENGINE=MEMORY AUTO_INCREMENT=1;

#
# Table structure for table `oxcategories`
#

DROP TABLE IF EXISTS `oxcategories`;

CREATE TABLE `oxcategories` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXPARENTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default 'oxrootid',
  `OXLEFT` int(11) NOT NULL default '0',
  `OXRIGHT` int(11) NOT NULL default '0',
  `OXROOTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSORT` int(11) NOT NULL default '9999',
  `OXACTIVE` tinyint(1) NOT NULL default '1',
  `OXHIDDEN` tinyint(1) NOT NULL default '0',
  `OXSHOPID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTITLE` varchar(254) NOT NULL default '',
  `OXDESC` varchar(255) NOT NULL default '',
  `OXLONGDESC` text NOT NULL,
  `OXTHUMB` varchar(128) NOT NULL default '',
  `OXTHUMB_1` VARCHAR(128) NOT NULL DEFAULT '',
  `OXTHUMB_2` VARCHAR(128) NOT NULL DEFAULT '',
  `OXTHUMB_3` VARCHAR(128) NOT NULL DEFAULT '',
  `OXEXTLINK` varchar(255) NOT NULL default '',
  `OXTEMPLATE` varchar(128) NOT NULL default '',
  `OXDEFSORT` varchar(64) NOT NULL default '',
  `OXDEFSORTMODE` tinyint(1) NOT NULL default '0',
  `OXPRICEFROM` double default NULL,
  `OXPRICETO` double default NULL,
  `OXACTIVE_1` tinyint(1) NOT NULL default '0',
  `OXTITLE_1` varchar(255) NOT NULL default '',
  `OXDESC_1` varchar(255) NOT NULL default '',
  `OXLONGDESC_1` text NOT NULL,
  `OXACTIVE_2` tinyint(1) NOT NULL default '0',
  `OXTITLE_2` varchar(255) NOT NULL default '',
  `OXDESC_2` varchar(255) NOT NULL default '',
  `OXLONGDESC_2` text NOT NULL,
  `OXACTIVE_3` tinyint(1) NOT NULL default '0',
  `OXTITLE_3` varchar(255) NOT NULL default '',
  `OXDESC_3` varchar(255) NOT NULL default '',
  `OXLONGDESC_3` text NOT NULL,
  `OXICON` varchar(128) NOT NULL default '',
  `OXPROMOICON` varchar(128) NOT NULL default '',
  `OXVAT` FLOAT NULL DEFAULT NULL,
  `OXSKIPDISCOUNTS` tinyint(1) NOT NULL default '0',
  `OXSHOWSUFFIX` tinyint(1) NOT NULL default '1',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
   PRIMARY KEY  (`OXID`),
   KEY `OXROOTID` (`OXROOTID`),
   KEY `OXPARENTID` (`OXPARENTID`),
   KEY `OXPRICEFROM` (`OXPRICEFROM`),
   KEY `OXPRICETO` (`OXPRICETO`),
   KEY `OXHIDDEN` (`OXHIDDEN`),
   KEY `OXSHOPID` (`OXSHOPID`),
   KEY `OXSORT` (`OXSORT`),
   KEY `OXVAT` (`OXVAT`)
) ENGINE=MyISAM;

#
# Table structure for table `oxcategory2attribute`
#

DROP TABLE IF EXISTS `oxcategory2attribute`;

CREATE TABLE `oxcategory2attribute` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXATTRID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSORT` INT( 11 ) NOT NULL DEFAULT '9999',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`)
) ENGINE=MyISAM;


#
# Table structure for table `oxconfig`
#

DROP TABLE IF EXISTS `oxconfig`;

CREATE TABLE `oxconfig` (
  `OXID`            char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID`        char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXMODULE`        varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXVARNAME`       char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXVARTYPE`       varchar(16) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXVARVALUE`      blob NOT NULL,
  `OXTIMESTAMP`     timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXVARNAME` (`OXVARNAME`),
  KEY `listall` (`OXSHOPID`, `OXMODULE`)
) ENGINE=MyISAM;

#
# Data for table `oxconfig`
#
INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES
('8563fba1965a11df3.34244997', 'oxbaseshop', '', 'blEnterNetPrice', 'bool', ''),
('8563fba1965a11df3.12345678', 'oxbaseshop', '', 'blWrappingVatOnTop', 'bool', ''),
('8563fba1965a1cc34.52696792', 'oxbaseshop', '', 'blCalculateDelCostIfNotLoggedIn', 'bool', ''),
('8563fba1965a1f266.82484369', 'oxbaseshop', '', 'blAllowUnevenAmounts', 'bool', ''),
('8563fba1965a219c9.51133344', 'oxbaseshop', '', 'blUseStock', 'bool', 0x07),
('8563fba1965a23786.00479842', 'oxbaseshop', '', 'blStoreCreditCardInfo', 'bool', ''),
('8563fba1965a25500.87856483', 'oxbaseshop', '', 'dDefaultVAT', 'str', 0x07a1),
('8563fba1965a27185.06428911', 'oxbaseshop', '', 'sDefaultLang', 'str', 0xde),
('8563fba1965a2b330.65668120', 'oxbaseshop', '', 'sMerchantID', 'str', ''),
('8563fba1965a2d181.97927980', 'oxbaseshop', '', 'sHost', 'str', 0x00d0e1aeebd778fac282663570d1660f41dc61385dbcd5d5d6f6),
('7fc4007ffb2639208.44268873', 'oxbaseshop', '', 'sGZSLogFile', 'str', ''),
('8563fba1965a2eee6.68137602', 'oxbaseshop', '', 'sPaymentUser', 'str', ''),
('8563fba1965a30cf7.41846088', 'oxbaseshop', '', 'sPaymentPwd', 'str', ''),
('mlae44cdad808d9b994c58540db39e7a', 'oxbaseshop', '', 'aLanguages', 'aarr', 0x4dba832f744c5786a371d9df3377ea87f0e2773dbaf685493e0b949a1c149111959424345b628f640a0d92ea6047ec118252e992),
('39893a0ef6a6e11645d4beee4fd0cd51', 'oxbaseshop', '', 'aLanguageParams', 'aarr', 0x4dba832f744c5786a371d9df33778f9525f408b6efbc82de7c3c5ae3396caa6f8afb6864afa833b43597cad1fb8f9b8970c8e9098d10aae1be4637faa40a012a04e45a8a1cdd1b2ac3da558638600e58acf70fe8c192b668995bb533dac95be7af7d343b3a9c9b8daeaf4d637f065895346773476d667de331fe40d18765d4b98faf7375e1090587d8dd4bf98ad5005eb30666410920),
('mlabefd7ebdb5946e8f3f7e7a953b323', 'oxbaseshop', '', 'aLanguageSSLURLs', 'arr', 0x4dba832f74e74df4cdd5afca153f15e216aea908af01b8),
('mla50c74dd79703312ffb8cfd82c3741', 'oxbaseshop', '', 'aLanguageURLs', 'arr', 0x4dba832f74e74df4cdd5afca153f15e216aea908af01b8),
('3c4f033dfb8fd4fe692715dda19ecd28', 'oxbaseshop', '', 'aCurrencies', 'arr', 0x4dbace2972e14bf2cbd3a9a4e655024620eb2a8b1770b083941edcbf1c09b8fc7a380fb4fb661e5241a9210d3ffa59395ce3d820d2a79c058e9c3a17c6815dfd492d51e0c01ace28af380e907a09677cedf73adab55f42f0154d9fc9bee3386b61956857d73768557428bf4d2f57e672f2bc3aaea791227a0de72d60c14259edefcf1358c703cf1d639f6a59d1cc9334bf7275d37b679cc79f178db9ca504c0e43),
('8563fba1965a43873.40898997', 'oxbaseshop', '', 'aLexwareVAT', 'aarr', 0x4dba682873e04a2acbd3a9a4113b832e198a7e75fb770da528d4e916d042856bcaa4b6795b839a7c836f43faae6ef75d3e6f91e3a0384990c0b7fae81c46aeca010521bb89b5),
('8563fba1baec4d3b7.61553539', 'oxbaseshop', '', 'iNrofSimilarArticles', 'str', 0x5d),
('8563fba1baec4f6d3.38812651', 'oxbaseshop', '', 'iNrofCustomerWhoArticles', 'str', 0x5d),
('8563fba1baec515d0.57265727', 'oxbaseshop', '', 'iNrofCrossellArticles', 'str', 0x5d),
('8563fba1baec55dc8.04115259', 'oxbaseshop', '', 'iUseGDVersion', 'str', 0xb6),
('8563fba1baec5b7d3.75515041', 'oxbaseshop', '', 'sCSVSign', 'str', 0x86),
('8563fba1baec5d615.45874801', 'oxbaseshop', '', 'iExportNrofLines', 'str', 0xb644b7),
('8563fba1baec678e2.44233324', 'oxbaseshop', '', 'iExportTickerRefresh', 'str', 0x07),
('8563fba1baec6acc3.69139343', 'oxbaseshop', '', 'iImportNrofLines', 'str', 0x07c4b1),
('8563fba1baec6cce8.28843189', 'oxbaseshop', '', 'iImportTickerRefresh', 'str', 0x07),
('8563fba1baec6eaf2.01241384', 'oxbaseshop', '', 'iCntofMails', 'str', 0xb6c7),
('8563fba1baec73b00.28734905', 'oxbaseshop', '', 'aOrderfolder', 'aarr', 0x4dba852e754d56360c19978b3f1481d799910f7f100e9ee73438ded0565e1a5edadd7c2846da44546f068ea2903bf5953068bc0cde9838848b7b31b27787c304bab9fe83bde678242f3645cb050632af58ea55b47cb51d45d03e8bd7cb984b2c2cd0fce8b09f09a2d796f5d3faa7f0ddb6b45d4554b6a7521f75503cd75b0c),
('8563fba1c39367724.92308656', 'oxbaseshop', '', 'blCheckTemplates', 'bool', 0x07),
('8563fba1c39370d88.58444180', 'oxbaseshop', '', 'blLogChangesInAdmin', 'bool', ''),
('8563fba1c393750a0.46170041', 'oxbaseshop', '', 'sUtilModule', 'str', ''),
('8563fba1c3937ee60.91079898', 'oxbaseshop', '', 'iMallMode', 'str', 0x07),
('8563fba1c39381962.39392958', 'oxbaseshop', '', 'aCacheViews', 'arr', 0x4dba852e75e64cf5ccd4ae48113baca2c2f96704c777824cbc13da72f905064aea430c1a75e7bb914d80360cf25cf5bd5ed9fcaf3d310ab4),
('8563fba1c39386cf4.18302736', 'oxbaseshop', '', 'aSkipTags', 'arr', 0x4dba85c975d460d7927733e9525403bc01ae3616da4e6cdf0a9b83cf8359894abce65f2103ad7e83270c4eb019ecf2fc0a3dcde5325b2bb08143bb43ec2c868c29d48dc7bea7f3abf16f6ebd6b97c50114bb53f0f23f59568f0fe9da452cfab264b8aa17ba9e978e892fc6cdef47b7f495e487027dcd08f12ce35d7d997b031d80044d60ba090f1d82a01b62d201d77ef25ce01e68a94948b3d48c2f6c5d612c2dcd6e8af2f00dd435f5e4a4884431560fe092e46de90ebdea5199915de557220607bfc0f7c9c945192e7640e2fda7d7f36ff1215b22ea4b3569cb47763d13e81f0a2dcf9398a5ccdd093ffa578c3c505b13a91d85f0d839543b340a4407ff6ec7d0948b0e7794bc05b993636dd6ac010b7c315f671a5c9b734402efbe207473995291e3122d474f0a86d07d643df2910af62397b4dbfb27c2bc2485498d0ff6bd0eaadc6e63a0fbb596fb50f7dc04a26f6ea8fc1b36f3ea274de76375b6dc82b3924a048a7f8a6ea741e8325b280a8d8c8c33c9d044fae750ad46b80dccfd8ae0c8471bf20c4236ecc4f3220011f7318b51e8c4276141f29a88c248a7563e89decc6561ac568f444fc75b5721947e980a280cde376532a0c7af16d6ad3a7decf89a8c3f1fd923fb11f8dd3bdea9319c71ba0be02c7f1fa10c276240727b56aafa61cc48f5b4f4852d184b3cf12e879a7d96b3b3134de64d0a9f8582632d1d18e1e7c007e2fc5dc95fd460e9d02db3fd2958ca5600d1b66f0853a6cd1488133f0299e1f20f),
('8563fba1c3938c994.40718405', 'oxbaseshop', '', 'aModules', 'aarr', 0x4dba322c774f5444a5777125d61918a96e9e65e1b8fba2e9f6f8ff4e240b745241e4b01edd9224c81f3020f58a2d),
('8563fba1c3938ebe7.95075058', 'oxbaseshop', '', 'aLogSkipTags', 'arr', 0x4dbaeb2d768d),
('79c3fbc9897c0d159.27469500', 'oxbaseshop', '', 'blLoadVariants', 'bool', 0x07),
('b2b400dd011bf6273.08965005', 'oxbaseshop', '', 'blVariantsSelection', 'bool', ''),
('43040112c71dfb0f2.40367454', 'oxbaseshop', '', 'sDefaultImageQuality', 'str', 0x7741),
('51240739e4bc61362.43751239', 'oxbaseshop', '', 'iMaxGBEntriesPerDay', 'str', 0xb0),
('4994145b9e87481c5.69580772', 'oxbaseshop', '', 'aSortCols', 'arr', 0x4dba832f74e74df4cdd5af631238e7040fc97a18cf6cb28fd522f05ae28cf374f04ceeb7bd886eb10ac36ba86043beb02e),
('4994145b9e8736eb6.03785000', 'oxbaseshop', '', 'iTop5Mode', 'str', 0x07),
('4994145b9e8678993.26056670', 'oxbaseshop', '', 'blShowSorting', 'bool', 0x07),
('a104164f96fa51c41.58873414', 'oxbaseshop', '', 'aSearchCols', 'arr', 0x4dba682873e04af3cad2a864153fe00308ce7d1fc86bb588d225f75de58b4371f549ebf5f054a8aa5d72ff4f9b5bb590240b14921d5f21962f67c7bd29417e61149f025b96cdf815d975cc85278913ee4b505bdfea13af328807c5ddd68d655b20d74de1e812236ebd97ee),
('d144175015dcd2a39.15131643', 'oxbaseshop', '', 'aHomeCountry', 'arr', 0x4dba322c77e44ef7ced6aca1f357003cad231d1d78fe80070841979cd58fd7eca88459d4cb9ce3b72a2804d5),
('ce143201f7e03e110.09792514', 'oxbaseshop', '', 'aMustFillFields', 'arr', 0x4dba322775d460d7927733e9e5fb6bf2ef688abcc84baef2405f16b906eec019f3a63b927c45a833864604543fe611a86d4a9f4027235e1a3f8572bfe00be3f1f0efee2efcc915c759d77d9270c9fef10bc707cdf5bc1a299c3795b96e0b85d032c55ff31364daf0e7a37ec5362cfdfb60e2de223e8160c91b08887f22196bfa2abae5f5d862fb1d0a7e35b2ceaf862088ab34b7029b1d598e61c436d21111682cf3442e4f9f16b936b1cdc085ed0dbda4b996a2a573c0aa47d3fb73ab13d4193b4d32c87bf9994e175f864102872ef2535d5d3df359ca2b25d26640038bbe74de0c8e2ef4b4c4e887afc4d889da38c63bf1c13c57a5c8d3f66a0615e155e4c3ec6dc279693b96e5b04004171fca59cb133027c34a309d9393736914ba027d21fa8ef1b9c79ec170ffa1a2bbf4746175c0e04b9cff68ae4f2875973518b9b1abc64f8e940d42183ed4ec6e1d285b2503869374d82727fae6f33ef4dd71c52de6bf9e460837768460a9fe62570ba2f75e83fd21be7e0c8fb78106e713d0e2e79fd19f04304989166dda296361a897ad15cc9f11db0566c70e968282da76ebb76fef0409f0),
('79e417a3916b910c8.31517473', 'oxbaseshop', '', 'bl_perfLoadAktion', 'bool', 0x07),
('79e417a4201010a12.85717286', 'oxbaseshop', '', 'bl_perfLoadReviews', 'bool', 0x07),
('79e417a420101f3e6.18536996', 'oxbaseshop', '', 'bl_perfLoadCrossselling', 'bool', 0x07),
('79e417a4201028c21.24163259', 'oxbaseshop', '', 'bl_perfLoadAccessoires', 'bool', 0x07),
('79e417a420103a598.95673089', 'oxbaseshop', '', 'bl_perfLoadCustomerWhoBoughtThis', 'bool', 0x07),
('79e417a4201044603.06076651', 'oxbaseshop', '', 'bl_perfLoadSimilar', 'bool', 0x07),
('79e417a420104dbd8.25267555', 'oxbaseshop', '', 'bl_perfLoadSelectLists', 'bool', 0x07),
('79e417a4201062a60.33852458', 'oxbaseshop', '', 'bl_perfLoadDiscounts', 'bool', 0x07),
('79e417a420106baa7.25594072', 'oxbaseshop', '', 'bl_perfLoadDelivery', 'bool', 0x07),
('79e417a420107ab46.59697382', 'oxbaseshop', '', 'bl_perfLoadPrice', 'bool', 0x07),
('79e417a442934fcb9.11733184', 'oxbaseshop', '', 'bl_perfLoadCatTree', 'bool', 0x07),
('79e417a45558d97f6.76133435', 'oxbaseshop', '', 'bl_perfLoadCurrency', 'bool', 0x07),
('79e417a45558e7851.36128674', 'oxbaseshop', '', 'bl_perfLoadLanguages', 'bool', 0x07),
('79e417a45558f1b86.05956285', 'oxbaseshop', '', 'bl_perfLoadNews', 'bool', 0x07),
('79e417a466086f390.29565974', 'oxbaseshop', '', 'bl_perfLoadNewsOnlyStart', 'bool', 0x07),
('c20424bf2f8e71271.42955545', 'oxbaseshop', '', 'bl_perfLoadTreeForSearch', 'bool', 0x07),
('36d42513de8cab671.54909813', 'oxbaseshop', '', 'bl_perfShowActionCatArticleCnt', 'bool', 0x07),
('7044252b61dcb8ac9.31672388', 'oxbaseshop', '', 'bl_perfLoadPriceForAddList', 'bool', 0x07),
('7044252b61dd44324.24181665', 'oxbaseshop', '', 'bl_perfParseLongDescinSmarty', 'bool', 0x07),
('77c425a29db68f0d9.00182375', 'oxbaseshop', '', 'bl_perfLoadManufacturerTree', 'bool', 0x07),
('79e417a4eaad1a593.54850808', 'oxbaseshop', '', 'blStoreIPs', 'bool', ''),
('79e417a4eaad9dfa6.77588633', 'oxbaseshop', '', 'aDeniedDynGroups', 'arr', 0x4dba322c77e44ef7ced6acac1f35ea091294b94a7572f88ffe92),
('33341949f476b65e8.17282442', 'oxbaseshop', '', 'iAttributesPercent', 'str', 0x77c2),
('43141b9b252874600.34636487', 'oxbaseshop', '', 'sDecimalSeparator', 'str', 0xc9),
('bf041bd98dacd9021.61732877', 'oxbaseshop', '', 'aInterfaceProfiles', 'aarr', 0x4dbace29724a51b6af7d09aac117301142e91c3c5b7eed9a850f85c1e3d58739aa9ea92523f05320a95060d60d57fbb027bad88efdaa0b928ebcd6aacf58084d31dd6ed5e718b833f1079b3805d28203f284492955c82cea3405879ea7588ec610ccde56acede495),
('e8e41bda6fa7631d8.13775806', 'oxbaseshop', '', 'iSessionTimeout', 'str', 0x17c3),
('6ec4235c5182c3620.11050422', 'oxbaseshop', '', 'iNrofNewcomerArticles', 'str', 0xfb),
('6ec4235c2aaa8eec5.99966057', 'oxbaseshop', '', 'sMidlleCustPrice', 'str', 0xfbc1),
('6ec4235c2aaa97585.69723730', 'oxbaseshop', '', 'sLargeCustPrice', 'str', 0x07c4b1),
('6ec4235c2aa997942.70260123', 'oxbaseshop', '', 'blWarnOnSameArtNums', 'bool', 0x07),
('a7a425c02819f7253.64374401', 'oxbaseshop', '', 'blAutoIcons', 'bool', 0x07),
('7e9426025ff199d75.57820200', 'oxbaseshop', '', 'sStockWarningLimit', 'str', 0x07c4),
('9a8426df9d36443e7.48701626', 'oxbaseshop', '', 'blSearchUseAND', 'bool', ''),
('a99427345bf85a602.27736147', 'oxbaseshop', '', 'blDontShowEmptyCategories', 'bool', ''),
('a99427345bf8fcff2.83464949', 'oxbaseshop', '', 'bl_perfUseSelectlistPrice', 'bool', ''),
('a99427345bf9a27a1.04791092', 'oxbaseshop', '', 'bl_perfCalcVatOnlyForBasketOrder', 'bool', ''),
('2ca4277aa49a5bd27.44511187', 'oxbaseshop', '', 'blStockOnDefaultMessage', 'bool', 0x07),
('2ca4277aa49a634f8.76432326', 'oxbaseshop', '', 'blStockOffDefaultMessage', 'bool', 0x07),
('6da42abf915b5f290.70877375', 'oxbaseshop', '', 'sCntOfNewsLoaded', 'str', 0x07),
('89e42b02704ce5589.91950338', 'oxbaseshop', '', 'iNewestArticlesMode', 'str', 0x07),
('e1142ca231becd5c4.00590616', 'oxbaseshop', '', 'blConfirmAGB', 'bool', ''),
('2e4452b5763e03c74.88240349', 'oxbaseshop', '', 'blDisableDublArtOnCopy', 'bool', 0x07),
('fde4559837789b3c7.26965372', 'oxbaseshop', '', 'aCMSfolder', 'aarr', 0x4dbace29724a5131411d93d207fd82ee70b3e465e8c18e1b60a35eb597a1f3bad1e50ee52570c9ca486b4755b08cea9d0a17892b1e7628a152af0ab842c7c310547016f7c53a9ad0d62060ca7fc75f2bf6892a6f9987d014c0418d2b1e7a6defd8e0d2f5b189c89b886c5d130a72f1dcb7b55c4455b720ce73743f3ed559cda8621a523aa1021ed09f9a1f0177fc9e7ab5920621aa55a368bfeb28ae782c3456362aee),
('6f8453f77d174e0a0.31854175', 'oxbaseshop', '', 'blOtherCountryOrder', 'bool', 0x07),
('0a5455450f97fdec9.37454802', 'oxbaseshop', '', 'blAllowNegativeStock', 'bool', ''),
('b0b4d221756c80afdad8904c0b91b877', 'oxbaseshop', '', 'iRssItemsCount', 'str', 0xb6c7),
('8b831f739c5d16cf4571b14a76006568', 'oxbaseshop', '', 'aSEOReservedWords', 'arr', 0x4dba422a71e248f1c8d0aa4c153fcde9eec56a0fcc7c8947b718d1dff30f2db6d7a60c59398fb5e1aa5999cfde45071ab225fba4d72b3ba9c23a4b0adb75314b1e7a2de97adee42d81197c0b48d4621740313f9df1ad63f693b7c47aa031ed88093c0e12eb85a75de769ede4f57823a56c6576106fb7),
('2b72d8716ab1c8a67e1f8eae931ce56f', 'oxbaseshop', '', 'bl_rssSearch', 'bool', 0x07),
('2b7aa4c6e33301b5855cc538fea4bccd', 'oxbaseshop', '', 'bl_rssTopShop', 'bool', 0x07),
('2b7f0b7ba625f0fe61993c66f21b13f3', 'oxbaseshop', '', 'bl_rssNewest', 'bool', 0x07),
('2b7eccdd7027feb7367a6edc668164c5', 'oxbaseshop', '', 'bl_rssCategories', 'bool', 0x07),
('fd770460540c32422b415a65fefb8f90', 'oxbaseshop', '', 'blLoadDynContents', 'bool', ''),
('fd7a064bbb64466f8e6ba847902b2005', 'oxbaseshop', '', 'sShopCountry', 'str', ''),
('44bcd90bd1d059.053753111', 'oxbaseshop', '', 'sTagList', 'str', 0x071d33336bce8dbe0606),
('603a1a28ff2a421b64c631ffaf97f324', 'oxbaseshop', '', 'sGiCsvFieldEncloser', 'str', 0x95),
('591e3b6358fc5c87a6cb81c3f79787bc', 'oxbaseshop', '', 'sTagSeparator', 'str', 0xc9),
('9135a582a6971656110b9a98ca5be6d2', 'oxbaseshop', '', 'blShippingCountryVat', 'bool', ''),
('7a59f9000f39e5d9549a5d1e29c076a0', 'oxbaseshop', '', 'blUseMultidimensionVariants', 'bool', 0x07),
('7a59f9000f39e5d9549a5d1e29c076a2', 'oxbaseshop', '', 'blOrderOptInEmail', 'bool', 0x07),
('bd3e73e699331eb92c557113bac02fc4', 'oxbaseshop', '', 'dPointsForInvitation', 'str', 0x07c4),
('bd320d322fa2f638086787c512329eec', 'oxbaseshop', '', 'dPointsForRegistration', 'str', 0x07c4),
('99065ff58e9d2c1b2e362e54c0bb54f3', 'oxbaseshop', '', 'blNewArtByInsert', 'bool', 0x07),
('rgk2a8c9cf8c9d23b3a7c8e9c090baf1', 'oxbaseshop', '', 'sTheme', 'str', 0x4db70f6d1a),
('8563fba1c39367724.92308656123456', 'oxbaseshop', '', 'blShowTags', 'bool', 0x07),
('8563fba1c39367724.92308656123457', 'oxbaseshop', '', 'blFacebookConfirmEnabled', 'bool', 0x07),
('8563fba1c39367724.92308656123111', 'oxbaseshop', '', 'sDownloadsDir', 'str', 0x603ee70d5409aee3c7d241d291),
('5i1c49faf83b3fe3d6bdbfa301e2704d', 'oxbaseshop', '', 'iLinkExpirationTime', 'str', 0x070de9),
('5i1d215fe1d6f0e1061ba1134e0ee4f2', 'oxbaseshop', '', 'iDownloadExpirationTime', 'str', 0xb6e2),
('l8g3e140a4bc7993d7d715df951dfe25', 'oxbaseshop', '', 'iMaxDownloadsCountUnregistered', 'str', 0x07),
('l8g957be9e7b13412960c7670f71ba3b', 'oxbaseshop', '', 'iMaxDownloadsCount', 'str', 0xde),
('mhjf24905a5b49c8d60aa31087b9797f', 'oxbaseshop', '', 'blShowRememberMe', 'bool', 0x07),
('omc4555952125c3c2.98253113', 'oxbaseshop', '', 'blDisableNavBars', 'bool', 0x07),
('mhjf24905a5b49c8d60aa31087b97971', 'oxbaseshop', '', 'blEnableSeoCache', 'bool', 0x07),
('l8g957be9e7b13412960c7670f71ba31', 'oxbaseshop', '', 'sAdditionalServVATCalcMethod', 'str', 0x55ca40ff0d0a72f5b908f2);

-- default unconfigurable values to fallback if theme does not provide alternatives
INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES
('8563fba1baec57c19.08644217', 'oxbaseshop', '', 'sThumbnailsize', 'str', 0x07c4b144c7b838),
('8563fba1baec599d5.89404456', 'oxbaseshop', '', 'sCatThumbnailsize', 'str', 0x5d43334072bf3f),
('6ec4235c2aaa45d77.87437919', 'oxbaseshop', '', 'sIconsize', 'str', 0x5d09ae6470),
('62642dfaa1d88b1b2.94593071', 'oxbaseshop', '', 'sZoomImageSize', 'str', 0xfb42b1443b3e38),
('62642dfaa1d87d064.50653921', 'oxbaseshop', '', 'aDetailImageSizes', 'aarr', 0x4dba326a73d2cdcb471b9533d7800b4b898873f7ae9dc29edf3ce8fab64f8609e31d318807f46516ea31aa94cb0b4edaaf32e7cb502403b480dd7cb1451f56975c3fd6159579cd2cab97104f17ae6a99af864bc1acb550c7e78b82f4618aeb8ba7fbec5409f277e0a2b84e66c24f96ba3fa76665f6a9294d8bf365bf7d3d0d56faf2355df799bc2892994db56203b0f5967ddbe8d403cead91988dfc82772557950eb1ba0d9468f3d8ca7170cde789d6c1282016056e51005091e7440fa453b1235c40010a71ff46f681c74515b4fda6da204abf3178561e271f8202652eabe106a93f9f4d1ed363f2f33c1e29716b95be88112373c48373148b134f2e0312bcfa2f2ba96f5cb15338dee7265d0efc66fe6526a6047b0e2bc4896143076e8dbc7dd8a7448ba2a5233814dd6abc39cb811a4d295c95cdaffde7cb8a5a3fddfe14f9a580973e9660a622f0d774bdb9);

-- data for azure theme
INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES
('1ec4235c2aee774aa45d772875437919', 'oxbaseshop', 'theme:azure', 'sIconsize', 'str', 0x8064a213b1),
('1563fba1bee774aec57c192086494217', 'oxbaseshop', 'theme:azure', 'sThumbnailsize', 'str', 0x079a3a49ca3630),
('1563fba1bee774aec599d56894094456', 'oxbaseshop', 'theme:azure', 'sCatThumbnailsize', 'str', 0x77e7ed4ecd3137),
('12642dfaa1dee77488b1b22948593071', 'oxbaseshop', 'theme:azure', 'sZoomImageSize', 'str', 0x170a3340d372be),
('12642dfaa1dee77487d0644506753921', 'oxbaseshop', 'theme:azure', 'aDetailImageSizes', 'aarr', 0x4dba326a73d2cdcb471b9533d7800b4b898873f7ae9dc29ed9e0e4f6bc678f00ea1438810efd6c1fe338a39dc20247d3a63beec4852106b7a1dd7cb1451f56975c3fd6159579cd2cab97104f17ae6c45a38a41e9a5bc59ceee828bfd6883e282aef2e55d00fb7ee9abb79b63c74cb7ba3fa76665f6a9294d8bf365bf7d3d0d56faf2355df145b02498b144bc6b0ab9fc9f74d2e1dd0ac7a4989184f58b7e2c58400bb4b92c9468f3d8ca7170cde789d6c1282016056e51005091e19803a859992a5549080378f64fff88ce4c1cbdf4afd32943b63877831b221ca302652eabe106a93f9f4d1ed363f2f33c1e29716b95b8541d2f79ec8a7a1d821a46270a1bb5f32622a06655b85a31d7ee2f52dbf963fd4426a6047b0e2bc4896143076e8dbc7dd8a7448ba2a5233ec8d166b611c288134420559cc4a6f4eec2835336d4f71df0ac899e314365a321d1d774bdb9),
('18a9473894d473f6ed28f04e80d929fc', 'oxbaseshop', 'theme:azure', 'bl_showCompareList', 'bool', 0x07),
('18acb2f595da54b5f865e54aa5cdb967', 'oxbaseshop', 'theme:azure', 'bl_showListmania', 'bool', 0x07),
('18a12329124850cd8f63cda6e8e7b4e1', 'oxbaseshop', 'theme:azure', 'bl_showWishlist', 'bool', 0x07),
('18a23429124850cd8f63cda6e8e7b4e1', 'oxbaseshop', 'theme:azure', 'bl_showVouchers', 'bool', 0x07),
('18a34529124850cd8f63cda6e8e7b4e1', 'oxbaseshop', 'theme:azure', 'bl_showGiftWrapping', 'bool', 0x07),
('15342e4cab0ee774acb3905838384984', 'oxbaseshop', 'theme:azure', 'blShowBirthdayFields', 'bool', 0x07),
('11296159b7641d31b93423972af6150b', 'oxbaseshop', 'theme:azure', 'iTopNaviCatCount', 'str', 0xfb),
('173455b29d0db9ee774b788731623955', 'oxbaseshop', 'theme:azure', 'blShowFinalStep', 'bool', 0x07),
('1ec42a395d0595ee7741091898848789', 'oxbaseshop', 'theme:azure', 'sManufacturerIconsize', 'str', 0x07c4b144c7b838),
('1ec42a395d0595ee7741091898848798', 'oxbaseshop', 'theme:azure', 'sCatIconsize', 'str', 0x070de94ac9b636),
('1ec42a395d0595ee7741091898848987', 'oxbaseshop', 'theme:azure', 'sCatPromotionsize', 'str', 0xb06fb441c2bd94),
('1ec42a395d0595ee7741091898848989', 'oxbaseshop', 'theme:azure', 'sDefaultListDisplayType', 'select', 0x83cd10b7f09064ed),
('1ec42a395d0595ee7741091898848992', 'oxbaseshop', 'theme:azure', 'sStartPageListDisplayType', 'select', 0x83cd10b7f09064ed),
('1ec42a395d0595ee7741091898848990', 'oxbaseshop', 'theme:azure', 'blShowListDisplayType', 'bool', 0x07),
('1ec42a395d0595ee7741091898848474', 'oxbaseshop', 'theme:azure', 'iNewBasketItemMessage', 'select', 0x07),
('1545423fe8ce213a0435345552230295', 'oxbaseshop', 'theme:azure', 'aNrofCatArticles', 'arr', 0x4dbace2972e14bf2cbd3a9a4113b83ad1c8f7b704f710ba39fd1ecd29b438b41809712e316c6f4fdc92741f7876cc6fca127d78994e604dcc99519),
('1ec42a395d0595ee7741091898848991', 'oxbaseshop', 'theme:azure', 'aNrofCatArticlesInGrid', 'arr', 0x4dbace2972e14bf2cbd3a9a4113b83c51e8d79724d7309a19dd3ee6153448c46879015e411c1f3fa250245f38368c2f8a523d58c91546b92cdf6);

#
# Table structure for table `oxconfigdisplay`
# Created on 2010-11-11
#

DROP TABLE IF EXISTS `oxconfigdisplay`;

CREATE TABLE `oxconfigdisplay` (
  `OXID`            char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXCFGMODULE`     varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXCFGVARNAME`    char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXGROUPING`      varchar(255) NOT NULL default '',
  `OXVARCONSTRAINT` varchar(255) NOT NULL default '',
  `OXPOS`           int NOT NULL default 0,
  `OXTIMESTAMP`     timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `list` (`OXCFGMODULE`, `OXCFGVARNAME`)
) ENGINE=MyISAM;

INSERT INTO `oxconfigdisplay` (`OXID`, `OXCFGMODULE`, `OXCFGVARNAME`, `OXGROUPING`, `OXVARCONSTRAINT`, `OXPOS`) VALUES
('1ec4235c2aee774aa45d772875437919', 'theme:azure', 'sIconsize', 'images', '', 1),
('1563fba1bee774aec57c192086494217', 'theme:azure', 'sThumbnailsize', 'images', '', 2),
('1563fba1bee774aec599d56894094456', 'theme:azure', 'sCatThumbnailsize', 'images', '', 3),
('12642dfaa1dee77488b1b22948593071', 'theme:azure', 'sZoomImageSize', 'images', '', 4),
('12642dfaa1dee77487d0644506753921', 'theme:azure', 'aDetailImageSizes', 'images', '', 5),
('18a9473894d473f6ed28f04e80d929fc', 'theme:azure', 'bl_showCompareList', 'features', '', 6),
('18acb2f595da54b5f865e54aa5cdb967', 'theme:azure', 'bl_showListmania', 'features', '', 7),
('18a12329124850cd8f63cda6e8e7b4e1', 'theme:azure', 'bl_showWishlist', 'features', '', 8),
('18a23429124850cd8f63cda6e8e7b4e1', 'theme:azure', 'bl_showVouchers', 'features', '', 9),
('18a34529124850cd8f63cda6e8e7b4e1', 'theme:azure', 'bl_showGiftWrapping', 'features', '', 10),
('15342e4cab0ee774acb3905838384984', 'theme:azure', 'blShowBirthdayFields', 'display', '', 14),
('11296159b7641d31b93423972af6150b', 'theme:azure', 'iTopNaviCatCount', 'display', '', 15),
('173455b29d0db9ee774b788731623955', 'theme:azure', 'blShowFinalStep', 'display', '', 16),
('6ec4235c2aee774aa45d772875437789', 'theme:azure', 'sManufacturerIconsize', 'images', '', 6),
('8563fba1bee774aec57c192086494897', 'theme:azure', 'sCatIconsize', 'images', '', 7),
('8563fba1bee774aec599d56894094987', 'theme:azure', 'sCatPromotionsize', 'images', '', 8),
('1ec42a395d0595ee7741091898848989', 'theme:azure', 'sDefaultListDisplayType', 'display', 'infogrid|line|grid', 21),
('1ec42a395d0595ee7741091898848992', 'theme:azure', 'sStartPageListDisplayType', 'display', 'infogrid|line|grid', 22),
('1ec42a395d0595ee7741091898848990', 'theme:azure', 'blShowListDisplayType', 'display', '', 20),
('1ec42a395d0595ee7741091898848474', 'theme:azure', 'iNewBasketItemMessage', 'display', '0|1|2|3', 17),
('1545423fe8ce213a06.20230295', 'theme:azure', 'aNrofCatArticles', 'display', '', 23),
('1ec42a395d0595ee7741091898848991', 'theme:azure', 'aNrofCatArticlesInGrid', 'display', '', 24);
# --------------------------------------------------------


#
# Table structure for table `oxcontents`
#

DROP TABLE IF EXISTS `oxcontents`;

CREATE TABLE `oxcontents` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXLOADID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSHOPID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSNIPPET` tinyint(1) NOT NULL default '1',
  `OXTYPE` tinyint(1) NOT NULL default '0',
  `OXACTIVE` tinyint(1) NOT NULL default '1',
  `OXACTIVE_1` tinyint(1) NOT NULL default '1',
  `OXPOSITION` varchar(32) NOT NULL default '',
  `OXTITLE` varchar(255) NOT NULL default '',
  `OXCONTENT` text NOT NULL,
  `OXTITLE_1` varchar(255) NOT NULL default '',
  `OXCONTENT_1` text NOT NULL,
  `OXACTIVE_2` tinyint(1) NOT NULL default '1',
  `OXTITLE_2` varchar(255) NOT NULL default '',
  `OXCONTENT_2` text NOT NULL,
  `OXACTIVE_3` tinyint(1) NOT NULL default '1',
  `OXTITLE_3` varchar(255) NOT NULL default '',
  `OXCONTENT_3` text NOT NULL,
  `OXCATID` varchar(32) character set latin1 collate latin1_general_ci default NULL,
  `OXFOLDER` varchar(32) NOT NULL default '',
  `OXTERMVERSION` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  UNIQUE KEY `OXLOADID` (`OXLOADID`),
  INDEX `cat_search` ( `OXTYPE` , `OXSHOPID` , `OXSNIPPET` , `OXCATID` )
) ENGINE=MyISAM;

#
# Table structure for table `oxcontents`
#

INSERT INTO `oxcontents` (`OXID`, `OXLOADID`, `OXSHOPID`, `OXSNIPPET`, `OXTYPE`, `OXACTIVE`, `OXACTIVE_1`, `OXPOSITION`, `OXTITLE`, `OXCONTENT`, `OXTITLE_1`, `OXCONTENT_1`, `OXACTIVE_2`, `OXTITLE_2`, `OXCONTENT_2`, `OXACTIVE_3`, `OXTITLE_3`, `OXCONTENT_3`, `OXCATID`, `OXFOLDER`, `OXTERMVERSION`) VALUES
('8709e45f31a86909e9f999222e80b1d0', 'oxstdfooter', 'oxbaseshop', 1, 0, 1, 1, '', 'Standard Footer', '<div>OXID Online Shop - Alles rund um das Thema Wassersport, Sportbekleidung und Mode </div>', 'standard footer', '<div>OXID Online Shop - All about watersports, sportswear and fashion </div>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', '', ''),
('ad542e49bff479009.64538090', 'oxadminorderemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihre Bestellung Admin', 'Folgende Artikel wurden soeben unter [{ $shop->oxshops__oxname->value }] bestellt:<br>\r\n<br>', 'your order admin', 'The following products have been ordered in [{ $shop->oxshops__oxname->value }] right now:<br>\r\n<br>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('c8d45408c4998f421.15746968', 'oxadminordernpemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihre Bestellung Admin (Fremdländer)', '<div>\r\n<p> <span style="color: #ff0000;"><strong>Hinweis:</strong></span> Derzeit ist keine Liefermethode für dieses Land bekannt. Bitte Liefermöglichkeiten suchen und den Besteller unter Angabe der <strong>Lieferkosten</strong> informieren!\r\n&nbsp;</p> </div>\r\n<div>Folgende Artikel wurden soeben unter [{ $shop->oxshops__oxname->value }] bestellt:<br>\r\n<br>\r\n</div>', 'your order admin (other country)', '<p> <span style="color: #ff0000"><strong>Information:</strong></span> Currently, there is no shipping method defined for this country. Please find a delivery option and inform the customer about the <strong>shipping costs</strong>.</p>\r\n<p>The following products have been ordered on [{ $shop->oxshops__oxname->value }]:<br />\r\n<br /></p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('c8d45408c718782f3.21298666', 'oxadminordernpplainemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihre Bestellung Admin (Fremdländer) Plain', 'Hinweis: Derzeit ist keine Liefermethode für dieses Land bekannt. Bitte Liefermöglichkeiten suchen und den Besteller informieren!\r\n\r\nFolgende Artikel wurden soeben unter [{ $shop->oxshops__oxname->getRawValue() }] bestellt:', 'your order admin plain (other country)', '<p>Information: Currently, there is no shipping method defined for this country. Please find a delivery option and inform the customer about the shipping costs.\r\n\r\nThe following products have been ordered on [{ $shop->oxshops__oxname->getRawValue() }]:</p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('ad542e49c19109ad6.04198712', 'oxadminorderplainemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihre Bestellung Admin Plain', '<p>Folgende Artikel wurden soeben unter [{ $shop->oxshops__oxname->getRawValue() }] bestellt:</p>', 'your order admin plain', 'The following products have been ordered in [{ $shop->oxshops__oxname->getRawValue() }] right now:', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('2eb4676806a3d2e87.06076523', 'oxagb', 'oxbaseshop', 1, 0, 1, 1, '', 'AGB', '<div><strong>AGB</strong></div>\r\n<div><strong>&nbsp;</strong></div>\r\n<div>Fügen Sie hier Ihre allgemeinen Geschäftsbedingungen ein:</div>\r\n<div>&nbsp;</div>\r\n<div><span style="font-weight: bold">Strukturvorschlag:</span><br>\r\n<br>\r\n<ol>\r\n<li>Geltungsbereich </li>\r\n<li>Vertragspartner </li>\r\n<li>Angebot und Vertragsschluss </li>\r\n<li>Widerrufsrecht, Widerrufsbelehrung, Widerrufsfolgen </li>\r\n<li>Preise und Versandkosten </li>\r\n<li>Lieferung </li>\r\n<li>Zahlung </li>\r\n<li>Eigentumsvorbehalt </li>\r\n<li>Gewährleistung </li>\r\n<li>Weitere Informationen</li></ol></div>', 'Terms and Conditions', 'Insert your terms and conditions here.', 1, '', '', 0, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', '1'),
('c4241316c6e7b9503.93160420', 'oxbargain', 'oxbaseshop', 1, 0, 1, 1, '', 'Schnäppchen', '<table>[{foreach from=$oView->getBargainArticleList() item=articlebargain_item}] <tbody><tr><td>\r\n<div class="product_image_s_container"><a href="[{$articlebargain_item->getLink()}]"><img border="0" alt="[{ $articlebargain_item->oxarticles__oxtitle->value }][{if $articlebargain_item->oxarticles__oxvarselect->value }] [{ $articlebargain_item->oxarticles__oxvarselect->value }][{/if}] [{$oxcmp_shop->oxshops__oxtitlesuffix->value}]" src="[{ $articlebargain_item->getDynImageDir()}]/[{$articlebargain_item->oxarticles__oxicon->value}]"></a></div> </td><td class="boxrightproduct-td"> <a href="[{$articlebargain_item->getLink()}]" class="boxrightproduct-td"><strong>[{ $articlebargain_item->oxarticles__oxtitle->value|cat:"\r\n"|cat:$articlebargain_item->oxarticles__oxvarselect->value|strip_tags|smartwordwrap:15:"<br>\r\n":2:1:"..." }]</strong></a><br>\r\n [{ if $articlebargain_item->isBuyable() }] <a href="[{$articlebargain_item->getToBasketLink()}]&amp;am=1" class="details" onclick="showBasketWnd();" rel="nofollow"><img border="0" src="[{$oViewConf->getImageUrl(''arrow_details.gif'')}]" alt=""> Jetzt bestellen! </a> [{/if}] </td></tr>[{/foreach}]\r\n</tbody></table>', 'Bargain', '<table>[{foreach from=$oView->getBargainArticleList() item=articlebargain_item}] <tbody><tr><td>\r\n<div class="product_image_s_container"><a href="[{$articlebargain_item->getLink()}]"><img border="0" src="[{ $articlebargain_item->getDynImageDir()}]/[{$articlebargain_item->oxarticles__oxicon->value}]" alt="[{ $articlebargain_item->oxarticles__oxtitle->value }][{if $articlebargain_item->oxarticles__oxvarselect->value }] [{ $articlebargain_item->oxarticles__oxvarselect->value }][{/if}] [{$oxcmp_shop->oxshops__oxtitlesuffix->value}]"></a></div> </td><td class="boxrightproduct-td"> <a class="boxrightproduct-td" href="[{$articlebargain_item->getLink()}]"><strong>[{ $articlebargain_item->oxarticles__oxtitle->value|cat:"\r\n"|cat:$articlebargain_item->oxarticles__oxvarselect->value|strip_tags|smartwordwrap:15:"<br>\r\n ":2:1:"..." }]</strong></a><br>\r\n [{ if $articlebargain_item->isBuyable()}] <a onclick="showBasketWnd();" class="details" href="[{$articlebargain_item->getToBasketLink()}]&amp;am=1" rel="nofollow"><img border="0" alt="" src="[{$oViewConf->getImageUrl(''arrow_details.gif'')}]"> Order now! </a> [{/if}] </td></tr>[{/foreach}] </tbody></table>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('1544167b4666ccdc1.28484600', 'oxblocked', 'oxbaseshop', 1, 0, 1, 1, '', 'Benutzer geblockt', '<div><span style="color: #ff0000;"><strong>\r\n<img title="" height="200" alt="" src="[{$oViewConf->getPictureDir()}]wysiwigpro/stopsign.jpg" width="200"></strong></span></div>\r\n<div><span style="color: #ff0000;"><strong>Der Zugang wurde Ihnen verweigert!</strong></span></div>\r\n<div>&nbsp;</div>\r\n<div>&nbsp;</div>', 'user blocked', '<div>\r\n   <img title="" height="200" alt="" src="[{$oViewConf->getPictureDir()}]wysiwigpro/stopsign.jpg" width="200">\r\n</div>\r\n\r\n<div>\r\n   <span style="color: #ff0000;"><strong>Permission denied!</strong></span>\r\n</div>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('f41427a07519469f1.34718981', 'oxdeliveryinfo', 'oxbaseshop', 1, 0, 1, 1, '', 'Versand und Kosten', '<p>Fügen Sie hier Ihre Versandinformationen- und kosten ein.</p>', 'Shipping and Charges', '<p>Add your shipping information and costs here.</p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('42e4667ffcf844be0.22563656', 'oxemailfooter', 'oxbaseshop', 1, 0, 1, 1, '', 'E-Mail Fußtext', '<p align="left">--</p>\r\n<p>Bitte fügen Sie hier Ihre vollständige Anbieterkennzeichnung ein.</p>', 'E-mail footer', '<p align="left">--</p>\r\n<p>Please insert your imprint here</p>', 1, '', '', 0, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('3194668fde854d711.73798992', 'oxemailfooterplain', 'oxbaseshop', 1, 0, 1, 1, '', 'E-Mail Fußtext Plain', '-- Bitte fügen Sie hier Ihre vollständige Anbieterkennzeichnung ein.', 'E-mail footer plain', '-- Please insert your imprint here.', 1, '', '', 0, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('e7a4518ce1e2c36a9.60268505', 'oxfirststart', 'oxbaseshop', 1, 0, 1, 1, '', 'UNSER SCHNÄPPCHEN!', '<div> Gültig solange Vorrat reicht. </div>', 'Our Bargain!', '<div>As long as on stock </div>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('29142e76dd32dd477.41262508', 'oxforgotpwd', 'oxbaseshop', 1, 0, 1, 1, '', 'Passwort vergessen', 'Sollten Sie innerhalb der nächsten Minuten KEINE E-Mail mit Ihren Zugangsdaten erhalten, so überprüfen Sie bitte: Haben Sie sich in unserem Shop bereits registriert? Wenn nicht, so tun Sie dies bitte einmalig im Rahmen des Bestellprozesses. Sie können dann selbst ein Passwort festlegen. Sobald Sie registriert sind, können Sie sich in Zukunft mit Ihrer E-Mail-Adresse und Ihrem Passwort einloggen.\r\n<ul>\r\n<li class="font11">Wenn Sie sich sicher sind, dass Sie sich in unserem Shop bereits registriert haben, dann überprüfen Sie bitte, ob Sie sich bei der Eingabe Ihrer E-Mail-Adresse evtl. vertippt haben.</li></ul>\r\n<p>Sollten Sie trotz korrekter E-Mail-Adresse und bereits bestehender Registrierung weiterhin Probleme mit dem Login haben und auch keine "Passwort vergessen"-E-Mail erhalten, so wenden Sie sich bitte per E-Mail an: <a href="mailto:[{ $oxcmp_shop->oxshops__oxinfoemail->value }]?subject=Passwort"><strong>[{ $oxcmp_shop->oxshops__oxinfoemail->value }]</strong></a></p>', 'Forgot password', '<p>If you don''t get an e-mail with your access data, please make sure that you have already registered with us. As soon as you are registered, you can login with your e-mail address and your password.</p>\r\n<ul>\r\n<li>\r\nIf you are sure you are already registered, please check the e-mail address you entered as user name.</li></ul>\r\n<p>\r\nIn case you still have problems logging in, please turn to us by e-mail: <a href="mailto:[{ $oxcmp_shop->oxshops__oxinfoemail->value }]?subject=Password"><strong>[{ $oxcmp_shop->oxshops__oxinfoemail->value }]</strong></a></p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('2eb46767947d21851.22681675', 'oximpressum', 'oxbaseshop', 1, 0, 1, 1, '', 'Impressum', '<p>Fügen Sie hier Ihre Anbieterkennzeichnung ein.</p>', 'About Us', '<p>Add provider identification here.</p>', 1, '', '', 0, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('ad542e49975709a72.52261121', 'oxnewsletteremail', 'oxbaseshop', 1, 0, 1, 1, '', 'Newsletter eShop', 'Hallo, [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->value }] [{ $user->oxuser__oxlname->value }],<br>\r\nvielen Dank für Ihre Anmeldung zu unserem Newsletter.<br>\r\n<br>\r\nUm den Newsletter freizuschalten klicken Sie bitte auf folgenden Link:<br>\r\n<br>\r\n<a href="[{$subscribeLink}]">[{$subscribeLink}]</a><br>\r\n<br>\r\nIhr [{ $shop->oxshops__oxname->value }] Team<br>', 'newsletter confirmation', 'Hello, [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->value }] [{ $user->oxuser__oxlname->value }],<br>\r\nthank you for your newsletter subscription.<br>\r\n<br>\r\nFor final registration, please click on this link:<br>\r\n<br>\r\n<a href="[{$subscribeLink}]">[{$subscribeLink}]</a><br>\r\n<br>\r\nYour [{ $shop->oxshops__oxname->value }] Team<br>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('ad542e4999ec01dd3.07214049', 'oxnewsletterplainemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Newsletter eShop Plain', '[{ $shop->oxshops__oxname->getRawValue() }] Newsletter Hallo, [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->getRawValue() }] [{ $user->oxuser__oxlname->getRawValue() }], vielen Dank für Ihre Anmeldung zu unserem Newsletter. Um den Newsletter freizuschalten klicken Sie bitte auf folgenden Link: [{$subscribeLink}] Ihr [{ $shop->oxshops__oxname->getRawValue() }] Team', 'newsletter confirmation plain', '[{ $shop->oxshops__oxname->getRawValue() }] Newsletter \r\n\r\nHello, [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->getRawValue() }] [{ $user->oxuser__oxlname->getRawValue() }], \r\n\r\nthank you for your newsletter subscription. For final registration, please click on this link: \r\n[{$subscribeLink}] \r\n\r\nYour [{ $shop->oxshops__oxname->getRawValue() }] Team', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('f41427a10afab8641.52768563', 'oxnewstlerinfo', 'oxbaseshop', 1, 0, 1, 1, '', 'Neuigkeiten bei uns', '<div>Mit dem [{ $oxcmp_shop->oxshops__oxname->value }]-Newsletter alle paar Wochen. <br>\r\nMit Tipps, Infos, Aktionen ... <br>\r\n<br>\r\nDas Abo kann jederzeit durch Austragen der E-Mail-Adresse beendet werden. <br>\r\nEine <span class="newsletter_title">Weitergabe Ihrer Daten an Dritte lehnen wir ab</span>. <br>\r\n<br>\r\nSie bekommen zur Bestätigung nach dem Abonnement eine E-Mail - so stellen wir sicher, dass kein Unbefugter Sie in unseren Newsletter eintragen kann (sog. "Double Opt-In").<br>\r\n<br>\r\n</div>', 'newsletter info', '<p>Stay in touch with the periodic [{ $oxcmp_shop->oxshops__oxname->value }]-newsletter every couple of weeks. We gladly inform you about recent tips, promotions and new products.</p>\r\n<p>You can unsubscribe any time from the newsletter.</p>\r\n<p>We strictly refuse <span class="newsletter_title">transferring your data to 3rd parties</span>.</p>\r\n<p>For subscription we use the so called &quot;double opt-in&quot; procedure to guarantee that no unauthorized person will register with your e-mail address.</p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('1074279e67a85f5b1.96907412', 'oxorderinfo', 'oxbaseshop', 1, 0, 1, 1, '', 'Wie bestellen?', '<div>Beispieltext:</div>\r\n<div> </div>\r\n<div>[{ $oxcmp_shop->oxshops__oxname->value }], Ihr Online-Shop für ... <br />\r\n<br />\r\nBei uns haben Sie die Wahl aus mehr als ... Artikeln von bester Qualität und namhaften Herstellern. Schauen Sie sich um, stöbern Sie in unseren Angeboten! <br />\r\n[{ $oxcmp_shop->oxshops__oxname->value }] steht Ihnen im Internet rund um die Uhr und 7 Tage die Woche offen.<br />\r\n<br />\r\nWenn Sie eine Bestellung aufgeben möchten, können Sie das:\r\n<ul>\r\n<li class="font11">direkt im Internet über unseren Shop </li>\r\n<li class="font11">per Fax unter [{ $oxcmp_shop->oxshops__oxtelefax->value }] </li>\r\n<li class="font11">per Telefon unter [{ $oxcmp_shop->oxshops__oxtelefon->value }] </li>\r\n<li class="font11">oder per E-Mail unter <a href="mailto:[{ $oxcmp_shop->oxshops__oxowneremail->value }]?subject=Bestellung"><strong>[{ $oxcmp_shop->oxshops__oxowneremail->value }]</strong></a> </li></ul>Telefonisch sind wir für Sie <br />\r\nMontag bis Freitag von 10 bis 18 Uhr erreichbar. <br />\r\nWenn Sie auf der Suche nach einem Artikel sind, der zum Sortiment von [{ $oxcmp_shop->oxshops__oxname->value }] passen könnte, ihn aber nirgends finden, lassen Sie es uns wissen. Gern bemühen wir uns um eine Lösung für Sie. <br />\r\n<br />\r\nSchreiben Sie an <a href="mailto:[{ $oxcmp_shop->oxshops__oxowneremail->value }]?subject=Produktidee"><strong>[{ $oxcmp_shop->oxshops__oxowneremail->value }]</strong></a>.</div>', 'How to order?', '<h1>Text Example</h1>\r\n<h2>[{ $oxcmp_shop->oxshops__oxname->value }], your online store for ...</h2>\r\n<p>With us, you can choose from more than ... products of high quality and reputable manufacturers. Take a look around and browse through our offers!<br />\r\nOn the internet [{ $oxcmp_shop->oxshops__oxname->value }] is open 24/7.</p>\r\n<p>If you want to place an order you can purchase</p>\r\n<ul>\r\n<li class="font11">via our online store</li>\r\n<li class="font11">via fax [{ $oxcmp_shop->oxshops__oxtelefax->value }] </li>\r\n<li class="font11">via telephone [{ $oxcmp_shop->oxshops__oxtelefon->value }] </li>\r\n<li class="font11">or via e-mail <a href="mailto:[{ $oxcmp_shop->oxshops__oxowneremail->value }]?subject=Order"><strong>[{ $oxcmp_shop->oxshops__oxowneremail->value }]</strong></a></li></ul>\r\n<p>By telephone, we are available<br />\r\nMonday to Friday 10 AM thru 6 PM. </p>\r\n<p>If you are looking for an item that did not match the range of [{ $oxcmp_shop->oxshops__oxname->value }], let''s us know. We are happy to find a solution for you.</p>\r\n<p>Write to <a href="mailto:[{ $oxcmp_shop->oxshops__oxowneremail->value }]?subject=Product idea"><strong>[{ $oxcmp_shop->oxshops__oxowneremail->value }]</strong></a></p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('67c5bcf75ee346bd9566bce6c8', 'oxcredits', 'oxbaseshop', 0, 3, 0, 1, '', 'Credits', '', 'Credits', '<h3>What is OXID eShop?</h3>\r\n<p>OXID eShop is a proven and flexible shopping cart software. Thousands of online businesses worldwide use its extensive functionality to create optimal eCommerce solutions. With its modular, state-of-the-art and standards-based architecture, customization is very easy. OXID eShop is being developed by <a href="http://www.oxid-esales.com">OXID eSales AG</a>, the trusted Open Source eCommerce company.</p>\r\n<h3>OXID eShop Community Edition</h3>\r\n<p>The Community Edition of OXID eShop is distributed under the terms and conditions of the GNU General Public License version 3 (GPLv3). Briefly summarized, the GPL gives you the right to use, modify and share this copy of OXID eShop. If you choose to share OXID eShop, you may only share it under the terms and conditions of the GPL. If you share a modified version of OXID eShop, these modifications must also be placed under the GPL. Read the complete <a href="http://www.gnu.org/licenses/gpl.txt">legal terms and conditions of the GPL</a> or see <a href="http://www.oxid-esales.com/en/products/community-edition/gpl-v3-faq">OXID''s GPLv3 FAQ</a>.</p>\r\n<h3>OXID eShop Professional and Enterprise Edition</h3>\r\n<p>These OXID eShop editions are distributed under OXID Commercial Licenses. For more information, please <a href="http://www.oxid-esales.com/en/company/about-oxid-esales/contact">contact OXID eSales</a>.</p>\r\n<h3>Third-party Software</h3>\r\n<p>This product includes certain free/open source software. A <a href="http://www.oxid-esales.com/en/company/about-oxid-esales/third-party-licenses">complete list of third-party software included in OXID eShop</a> is publicly available.</p>\r\n<h3>Copyright Notice</h3>\r\n<p>Copyright © 2003-2012 <a href="http://www.oxid-esales.com">OXID eSales AG</a>, with portions copyright by other parties.</p>\r\n\r\n\r\n<!-- added by Marco //-->\r\n\r\n\r\n<h3>List of Contributions</h3>\r\n<ul>\r\n   <li><b>Downloadable Products</b>\r\n   <br>Some business models are based on downloadable virtual products like software, PDF or MP3 files instead of the classic shipment of products purchased in an online store. Please find more information on <a href="http://wiki.oxidforge.org/Features/Downloadable_products" target="_blank">OXIDforge</a>.\r\n   <br>contributed by <a href="http://www.marmalade.de" target="_blank">marmalade.de</a>\r\n   </li>\r\n\r\n\r\n   <li><b>Rich Snippets: RDFa + GoodRelations</b>\r\n   <br>We are convinced that the RDFa + GoodRelations vocabulary will be the future of rich snippets, especially for eCommerce. Please find more information on <a href="" target="_blank">OXIDforge</a>.\r\n   <br>contributed by Daniel Bingel and Prof. Dr. Hepp <a href="http://www.heppnetz.de/projects/goodrelations/" target="_blank">(GoodRelations)</a>.\r\n   </li>\r\n\r\n\r\n</ul>', 1, '', '', 0, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('ad542e49d6de4a4f4.88594616', 'oxordersendemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihre Bestellung wurde versandt', 'Guten Tag, [{ $order->oxorder__oxbillsal->value|oxmultilangsal }] [{ $order->oxorder__oxbillfname->value }] [{ $order->oxorder__oxbilllname->value }],<br>\r\n<br>\r\nunser Vertriebszentrum hat soeben folgende Artikel versandt.<br>\r\n<br>', 'your order has been shipped', 'Hello [{ $order->oxorder__oxbillsal->value|oxmultilangsal }] [{ $order->oxorder__oxbillfname->value }] [{ $order->oxorder__oxbilllname->value }],<br />\r\n<br />\r\n<p>\r\nour distribution center just shipped this product:</p><br />', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('ad542e49d856b5b68.98220446', 'oxordersendplainemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihre Bestellung wurde versandt Plain', 'Guten Tag [{ $order->oxorder__oxbillsal->value|oxmultilangsal }] [{ $order->oxorder__oxbillfname->getRawValue() }] [{ $order->oxorder__oxbilllname->getRawValue() }],\r\n\r\nunser Vertriebszentrum hat soeben folgende Artikel versandt.', 'your order has been shipped plain', '<p>Hello [{ $order->oxorder__oxbillsal->value|oxmultilangsal }] [{ $order->oxorder__oxbillfname->getRawValue() }] [{ $order->oxorder__oxbilllname->getRawValue() }],\r\n\r\nour distribution center just shipped this product:</p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('ad542e49c585394e4.36951640', 'oxpricealarmemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Preisalarm', 'Preisalarm im [{ $shop->oxshops__oxname->value }]!<br>\r\n<br>\r\n[{ $email }] bietet für Artikel [{ $product->oxarticles__oxtitle->value }], Artnum. [{ $product->oxarticles__oxartnum->value }]<br>\r\n<br>\r\nOriginalpreis: [{ $product->getFPrice() }] [{ $currency->name}]<br>\r\nGEBOTEN: [{ $bidprice }] [{ $currency->name}]<br>\r\n<br>\r\n<br>\r\nIhr Shop.<br>', 'price alert', 'Price alert at [{ $shop->oxshops__oxname->value }]!<br>\r\n<br>\r\n[{ $email }] bids for product [{ $product->oxarticles__oxtitle->value }], product # [{ $product->oxarticles__oxartnum->value }]<br>\r\n<br>\r\nOriginal price: [{ $currency->name}][{ $product->getFPrice() }]<br>\r\nBid: [{ $currency->name}][{ $bidprice }]<br>\r\n<br>\r\n<br>\r\nYour store<br>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('ad542e49c8ec04201.39247735', 'oxregisteremail', 'oxbaseshop', 1, 0, 1, 1, '', 'Vielen Dank für Ihre Registrierung', 'Hallo, [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->value }] [{ $user->oxuser__oxlname->value }], vielen Dank für Ihre Registrierung bei [{ $shop->oxshops__oxname->value }]!<br>\r\n<br>\r\nSie können sich ab sofort auch mit Ihrer Kundennummer <strong>[{ $user->oxuser__oxcustnr->value }]</strong> einloggen.<br>\r\n<br>\r\nIhr [{ $shop->oxshops__oxname->value }] Team<br>', 'thanks for your registration', 'Hello, [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->value }] [{ $user->oxuser__oxlname->value }], <br />\r\n<br />\r\n<p>\r\nthank you for your registration at [{ $shop->oxshops__oxname->value }]!</p>\r\nFrom now on, you can log in with your customer number <strong>[{ $user->oxuser__oxcustnr->value }]</strong>.<br />\r\n<br />\r\nYour [{ $shop->oxshops__oxname->value }] team<br />', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('ad542e49ca4750015.09588134', 'oxregisterplainemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Vielen Dank für Ihre Registrierung Plain', '<p>[{ $shop->oxshops__oxregistersubject->getRawValue() }] Hallo, [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->getRawValue() }] [{ $user->oxuser__oxlname->getRawValue() }], vielen Dank für Ihre Registrierung bei [{ $shop->oxshops__oxname->getRawValue() }]! Sie können sich ab sofort auch mit Ihrer Kundennummer ([{ $user->oxuser__oxcustnr->value }]) einloggen. Ihr [{ $shop->oxshops__oxname->getRawValue() }] Team</p>', 'thanks for your registration plain', '<p>Hello, [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->getRawValue() }] [{ $user->oxuser__oxlname->getRawValue() }],\r\n\r\nthank you for your registration at [{ $shop->oxshops__oxname->getRawValue() }]!\r\nFrom now on, you can log in with your customer number [{ $user->oxuser__oxcustnr->value }].\r\n\r\nYour [{ $shop->oxshops__oxname->getRawValue() }] team</p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('1ea45574543b21636.29288751', 'oxrightofwithdrawal', 'oxbaseshop', 1, 0, 1, 1, '', 'Widerrufsrecht', 'Fügen Sie hier Ihre Widerrufsbelehrung ein.', 'Right of Withdrawal', '<div>Insert here the Right of Withdrawal policy</div>', 1, '', '', 0, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('f41427a099a603773.44301043', 'oxsecurityinfo', 'oxbaseshop', 1, 0, 1, 1, '', 'Datenschutz', 'Fügen Sie hier Ihre Datenschutzbestimmungen ein.', 'Privacy Policy', 'Enter your privacy policy here.', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('ce79015b6f6f07612270975889', 'oxstartmetadescription', 'oxbaseshop', 1, 0, 1, 1, '', 'META Description Startseite', 'Alles zum Thema Wassersport, Sportbekleidung und Mode. Umfangreiches Produktsortiment mit den neusten Trendprodukten. Blitzschneller Versand.<br />', 'META description start page', '<p>All about watersports, sportswear and fashion. Extensive product range including several trendy products. Fast shipping.</p>\r\n<p>&nbsp;</p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', '', ''),
('ce77743c334edf92b0cab924a7', 'oxstartmetakeywords', 'oxbaseshop', 1, 0, 1, 1, '', 'META Keywords Startseite', 'kite, kites, kiteboarding, kiteboards, wakeboarding, wakeboards, boards, strand, sommer, wassersport, mode, fashion, style, shirts, jeans, accessoires, angebote', 'META keywords start page', 'kite, kites, kiteboarding, kiteboards, wakeboarding, wakeboards, boards, beach, summer, watersports, funsports, fashion, style, shirts, jeans, accessories, special offers', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', '', ''),
('c4241316b2e5c1966.96997015', 'oxstartwelcome', 'oxbaseshop', 1, 0, 1, 1, '', 'start.tpl Begrüßungstext', '<h1><strong>Willkommen</strong> [{ if $oxcmp_user }]<strong>[{ $oxcmp_user->oxuser__oxfname->value }] [{ $oxcmp_user->oxuser__oxlname->value }] </strong>[{else}] [{/if}][{ if !$oxcmp_user }]<strong>im OXID <span style="color: #ff3301">e</span>Shop 4</strong>[{/if}]</h1>\r\nDies ist eine Demo-Installation des <strong>OXID eShop 4</strong>. Also keine Sorge, wenn Sie bestellen: Die Ware wird weder ausgeliefert, noch in Rechnung gestellt. Die gezeigten Produkte (und Preise) dienen nur zur Veranschaulichung der umfangreichen Funktionalität des Systems.\r\n<div><strong>&nbsp;</strong></div>\r\n<div><strong>Wir wünschen viel Spaß beim Testen!</strong></div>\r\n<div><strong>Ihr OXID eSales Team</strong></div>\r\n<p><strong>&nbsp;</strong></p>\r\n<p><strong>Hinweis für den Shop Administrator:</strong></p>\r\n<p>Dieser Text kann im Admin-Bereich unter dem Menüpunkt <em>Kundeninformation -&gt; CMS-Seiten -&gt; start.tpl Begrüßungstext</em><strong> </strong>bearbeitet werden.<strong><br />\r\n</strong></p>\r\n<p><strong>&nbsp;</strong></p>', 'start.tpl welcome text', '<h1><strong>Welcome</strong> [{ if $oxcmp_user }]<strong>[{ $oxcmp_user->oxuser__oxfname->value }] [{ $oxcmp_user->oxuser__oxlname->value }] </strong>[{else}] [{/if}][{ if !$oxcmp_user }]<strong>to OXID <span style="color: #fc6634">e</span>Shop 4</strong>[{/if}]</h1>\r\n<p>This is a demo installation of <strong>OXID eShop 4</strong>. Don''t worry when ordering: The goods will not be delivered nor charged. The displayed products and prices are solely tended to show the comprehensive functionality of the shopping cart system.</p>\r\n<div><strong>Have fun testing!</strong></div>\r\n<div><strong>Your OXID eSales team</strong></div>\r\n<p>&nbsp; </p>\r\n<p><strong>Notice for the shop administrator:</strong></p>\r\n<p>Update this text in the admin area. Choose from admin menu: <em>Customer Info -&gt; CMS Pages -&gt; start.tpl welcome text</em></p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('ad542e49ae50c60f0.64307543', 'oxuserorderemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihre Bestellung', 'Vielen Dank für Ihre Bestellung!<br>\r\n<br>\r\nNachfolgend haben wir zur Kontrolle Ihre Bestellung noch einmal aufgelistet.<br>\r\nBei Fragen sind wir jederzeit für Sie da: Schreiben Sie einfach an [{ $shop->oxshops__oxorderemail->value }]!<br>\r\n<br>', 'your order', 'Thank you for your order!<br />\r\n<br />\r\nBelow, we have listed your order.<br />\r\nIf you have any questions, don''t hesitate to drop us an e-mail [{ $shop->oxshops__oxorderemail->value }]!<br />\r\n<br />', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('84a42e66105998a86.14045828', 'oxuserorderemailend', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihre Bestellung Abschluss', '<div align="left">Fügen Sie hier Ihre Widerrufsbelehrung ein.</div>', 'your order terms', '<p>Right to Withdrawal can be inserted here.</p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('84a42e66123887821.29772527', 'oxuserorderemailendplain', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihre Bestellung Abschluss Plain', 'Fügen Sie hier Ihre Widerrufsbelehrung ein.', 'your order terms plain', '<p>Right to Withdrawal can be inserted here.</p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('c8d45408c08bbaf79.09887022', 'oxuserordernpemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihre Bestellung (Fremdländer)', '<div>Vielen Dank für Ihre Bestellung!</div>\r\n<p><strong><span style="color: #ff0000">Hinweis:</span></strong> Derzeit ist uns keine Versandmethode für dieses Land bekannt. Wir werden versuchen, Versandmethoden zu finden und Sie über das Ergebnis unter Angabe der Versandkosten informieren. </p>Bei Fragen sind wir jederzeit für Sie da: Schreiben Sie einfach an [{ $shop->oxshops__oxorderemail->value }]! <br />\r\n<br />', 'your order (other country)', '<p>Thank you for your order!</p>\r\n<p><strong><span style="color: #ff0000">Information:</span></strong> Currently, there is no shipping method defined for your country. We will find a method to deliver the goods you purchased and will inform you as soon as possible.</p>\r\n<p>If you have any requests, don''t hesitate to contact us! [{ $shop->oxshops__oxorderemail->value }]</p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('c8d45408c5c39ea22.75925645', 'oxuserordernpplainemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihre Bestellung (Fremdländer) Plain', 'Vielen Dank für Ihre Bestellung!\r\n\r\nHinweis: Derzeit ist uns keine Versandmethode für dieses Land bekannt. Wir werden versuchen, Versandmethoden zu finden und Sie über das Ergebnis unter Angabe der Versandkosten informieren.\r\n\r\nBei Fragen sind wir jederzeit für Sie da: Schreiben Sie einfach an [{ $shop->oxshops__oxorderemail->value }]!', 'your order plain (other country)', 'Thank you for your order!\r\nInformation: Currently, there is no shipping method defined for your country. We will find a method to deliver the goods you purchased and will inform you as soon as possible.\r\n\r\nIf you have any requests don''t hesitate to contact us! [{ $shop->oxshops__oxorderemail->value }]', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('ad542e49b08c65017.19848749', 'oxuserorderplainemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihre Bestellung Plain', 'Vielen Dank für Ihre Bestellung!\r\n\r\nNachfolgend haben wir zur Kontrolle Ihre Bestellung noch einmal aufgelistet.\r\nBei Fragen sind wir jederzeit für Sie da: Schreiben Sie einfach an [{ $shop->oxshops__oxorderemail->value }]!', 'your order plain', 'Thank you for your order!\r\n\r\nBelow we have listed your order.\r\nIf you have any questions, don''t hesitate to drop us an e-mail [{ $shop->oxshops__oxorderemail->value }]!', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('ad542e49541c1add', 'oxupdatepassinfoemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihr Passwort im eShop', 'Hallo [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->value }] [{ $user->oxuser__oxlname->value }],\r\n<br /><br />\r\nöffnen Sie den folgenden Link, um ein neues Passwort für [{ $shop->oxshops__oxname->value }] einzurichten:\r\n<br /><br />\r\n<a href="[{ $oViewConf->getBaseDir() }]index.php?cl=forgotpwd&amp;uid=[{ $user->getUpdateId() }]&amp;lang=[{ $oViewConf->getActLanguageId() }]&amp;shp=[{ $shop->oxshops__oxid->value }]">[{ $oViewConf->getBaseDir() }]index.php?cl=forgotpwd&amp;uid=[{ $user->getUpdateId()}]&amp;lang=[{ $oViewConf->getActLanguageId() }]&amp;shp=[{ $shop->oxshops__oxid->value }]</a>\r\n<br /><br />\r\nDiesen Link können Sie innerhalb der nächsten [{ $user->getUpdateLinkTerm()/3600 }] Stunden aufrufen.\r\n<br /><br />\r\nIhr [{ $shop->oxshops__oxname->value }] Team\r\n<br />', 'password update info', 'Hello [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->value }] [{ $user->oxuser__oxlname->value }],<br />\r\n<br />\r\nfollow this link to generate a new password for [{ $shop->oxshops__oxname->value }]:<br />\r\n<br /><a href="[{ $oViewConf->getBaseDir() }]index.php?cl=forgotpwd&amp;uid=[{ $user->getUpdateId() }]&amp;lang=[{ $oViewConf->getActLanguageId() }]&amp;shp=[{ $shop->oxshops__oxid->value }]">[{ $oViewConf->getBaseDir() }]index.php?cl=forgotpwd&amp;uid=[{ $user->getUpdateId()}]&amp;lang=[{ $oViewConf->getActLanguageId() }]&amp;shp=[{ $shop->oxshops__oxid->value }]</a><br />\r\n<br />\r\nYou can use this link within the next [{ $user->getUpdateLinkTerm()/3600 }] hours.<br />\r\n<br />\r\nYour [{ $shop->oxshops__oxname->value }] team<br />', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('ad542e495c392c6e', 'oxupdatepassinfoplainemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Ihr Passwort im eShop Plain', 'Hallo [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->getRawValue() }] [{ $user->oxuser__oxlname->getRawValue() }],\r\n\r\nöffnen Sie den folgenden Link, um ein neues Passwort für [{ $shop->oxshops__oxname->getRawValue() }] einzurichten:\r\n\r\n[{ $oViewConf->getBaseDir() }]index.php?cl=forgotpwd&amp;uid=[{ $user->getUpdateId()}]&amp;lang=[{ $oViewConf->getActLanguageId() }]&amp;shp=[{ $shop->oxshops__oxid->value }]\r\n\r\nDiesen Link können Sie innerhalb der nächsten [{ $user->getUpdateLinkTerm()/3600 }] Stunden aufrufen.\r\n\r\nIhr [{ $shop->oxshops__oxname->getRawValue() }] Team', 'password update info plain', 'Hello [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->getRawValue() }] [{ $user->oxuser__oxlname->getRawValue() }],\r\n\r\nfollow this link to generate a new password for [{ $shop->oxshops__oxname->getRawValue() }]:\r\n\r\n[{ $oViewConf->getBaseDir() }]index.php?cl=forgotpwd&amp;uid=[{ $user->getUpdateId()}]&amp;lang=[{ $oViewConf->getActLanguageId() }]&amp;shp=[{ $shop->oxshops__oxid->value }]\r\n\r\nYou can use this link within the next [{ $user->getUpdateLinkTerm()/3600 }] hours.\r\n\r\nYour [{ $shop->oxshops__oxname->getRawValue() }] team', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('460f3d25a752eeca8f8dbd66d04277c1', 'oxregisteraltemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Alternative E-Mail zur Registrierung HTML', 'Hallo [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->value }] [{ $user->oxuser__oxlname->value }], <br />\r\n<br />\r\n<p>\r\ndanke für die Registrierung im [{ $shop->oxshops__oxname->value }]!</p>\r\nVon jetzt an können Sie sich mit Ihrer Benutzernummer <strong>[{ $user->oxuser__oxcustnr->value }]</strong>.<br />\r\n<br />\r\nFolgen Sie diesem Link, um die Registrierung zu bestätigen:<br />\r\n<br /><a href="[{ $oViewConf->getBaseDir() }]index.php?cl=register&fnc=confirmRegistration&uid=[{ $user->getUpdateId() }]&amp;lang=[{ $oViewConf->getActLanguageId() }]&shp=[{ $shop->oxshops__oxid->value }]">[{ $oViewConf->getBaseDir() }]index.php?cl=register&fnc=confirmRegistration&uid=[{ $user->getUpdateId()}]&amp;lang=[{ $oViewConf->getActLanguageId() }]&shp=[{ $shop->oxshops__oxid->value }]</a><br />\r\n<br />\r\nSie können diesen Link in den nächsten [{ $user->getUpdateLinkTerm()/3600 }] Stunden verwenden.<br />\r\n<br /><br />\r\nIhr Team vom [{ $shop->oxshops__oxname->value }]', 'Alternative Registration Email HTML', 'Hello, [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->value }] [{ $user->oxuser__oxlname->value }], <br />\r\n<br />\r\nthanks for your registration at [{ $shop->oxshops__oxname->value }]!<br />\r\nFrom now on, you can log in with your customer number <strong>[{ $user->oxuser__oxcustnr->value }]</strong>.<br />\r\n<br />\r\nFollow this link to confirm your registration:<br />\r\n<br /><a href="[{ $oViewConf->getBaseDir() }]index.php?cl=register&fnc=confirmRegistration&uid=[{ $user->getUpdateId() }]&amp;lang=[{ $oViewConf->getActLanguageId() }]&shp=[{ $shop->oxshops__oxid->value }]">[{ $oViewConf->getBaseDir() }]index.php?cl=register&fnc=confirmRegistration&uid=[{ $user->getUpdateId()}]&amp;lang=[{ $oViewConf->getActLanguageId() }]&shp=[{ $shop->oxshops__oxid->value }]</a><br />\r\n<br />\r\nYou can use this link within the next [{ $user->getUpdateLinkTerm()/3600 }] hours.<br />\r\n<br />\r\n<br />\r\nYour [{ $shop->oxshops__oxname->value }] team', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('460273f2ae78b9c40c536a1c331317ee', 'oxregisterplainaltemail', 'oxbaseshop', 1, 0, 1, 1, '', 'Alternative E-Mail zur Registrierung PLAIN', 'Hallo [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->getRawValue() }] [{ $user->oxuser__oxlname->getRawValue() }],\r\n\r\ndanke für die Registrierung im [{ $shop->oxshops__oxname->getRawValue() }]!\r\nVon jetzt an können Sie sich mit Ihrer Benutzernummer [{ $user->oxuser__oxcustnr->value }].\r\n\r\nFolgen Sie diesem Link, um die Registrierung zu bestätigen:\r\n[{ $oViewConf->getBaseDir() }]index.php?cl=register&fnc=confirmRegistration&uid=[{ $user->getUpdateId() }]&amp;lang=[{ $oViewConf->getActLanguageId() }]&shp=[{ $shop->oxshops__oxid->value }]\r\n\r\nSie können diesen Link in den nächsten [{ $user->getUpdateLinkTerm()/3600 }] Stunden verwenden.</p>\r\n\r\n\r\nIhr Team vom [{ $shop->oxshops__oxname->getRawValue() }]', 'Alternative Registration Email PLAIN', 'Hello, [{ $user->oxuser__oxsal->value|oxmultilangsal }] [{ $user->oxuser__oxfname->getRawValue() }] [{ $user->oxuser__oxlname->getRawValue() }],\r\n\r\nthanks for your registration at [{ $shop->oxshops__oxname->getRawValue() }]!\r\nFrom now on, you can log in with your customer number [{ $user->oxuser__oxcustnr->value }].\r\n\r\nFollow this link to confirm your registration:\r\n[{ $oViewConf->getBaseDir() }]index.php?cl=register&fnc=confirmRegistration&uid=[{ $user->getUpdateId() }]&amp;lang=[{ $oViewConf->getActLanguageId() }]&shp=[{ $shop->oxshops__oxid->value }]\r\n\r\nYou can use this link within the next [{ $user->getUpdateLinkTerm()/3600 }] hours.<br />\r\n\r\n\r\nYour [{ $shop->oxshops__oxname->getRawValue() }] team', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_EMAILS', ''),
('220404cee0caf470e227c1c9f1ec4ae2', 'oxrighttocancellegend', 'oxbaseshop', 1, 0, 1, 1, '', 'AGB und Widerrufsrecht', '[{oxifcontent ident="oxagb" object="oCont"}]\r\n    Ich habe die <a id="test_OrderOpenAGBBottom" rel="nofollow" href="[{ $oCont->getLink() }]" onclick="window.open(''[{ $oCont->getLink()|oxaddparams:"plain=1"}]'', ''agb_popup'', ''resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400'');return false;" class="fontunderline">AGB</a> gelesen und erkläre mich mit ihnen einverstanden.&nbsp;\r\n[{/oxifcontent}]\r\n[{oxifcontent ident="oxrightofwithdrawal" object="oCont"}]\r\n    Ich wurde über mein <a id="test_OrderOpenWithdrawalBottom" rel="nofollow" href="[{ $oCont->getLink() }]" onclick="window.open(''[{ $oCont->getLink()|oxaddparams:"plain=1"}]'', ''rightofwithdrawal_popup'', ''resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400'');return false;">[{ $oCont->oxcontents__oxtitle->value }]</a> informiert.\r\n[{/oxifcontent}]', 'Terms and Conditions and Right to Withdrawal', '[{oxifcontent ident="oxagb" object="oCont"}] I agree to the <a id="test_OrderOpenAGBBottom" rel="nofollow" href="[{ $oCont->getLink() }]" onclick="window.open(''[{ $oCont->getLink()|oxaddparams:"plain=1"}]'', ''agb_popup'', ''resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400'');return false;" class="fontunderline">Terms and Conditions</a>.&nbsp;\r\n[{/oxifcontent}]\r\n[{oxifcontent ident="oxrightofwithdrawal" object="oCont"}]\r\n    I have been informed about my <a id="test_OrderOpenWithdrawalBottom" rel="nofollow" href="[{ $oCont->getLink() }]" onclick="window.open(''[{ $oCont->getLink()|oxaddparams:"plain=1"}]'', ''rightofwithdrawal_popup'', ''resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400'');return false;">[{ $oCont->oxcontents__oxtitle->value }]</a>.\r\n[{/oxifcontent}]', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('c4241316b2e5c1966.96997011', 'oxhelpalist', 'oxbaseshop', 1, 0, 1, 1, '', 'Hilfe - Die Produktliste', '<p>Hier können zusätzliche Informationen, weiterführende Links, Bedienungshinweise etc. für die Hilfe-Funktion in den <em>Produktlisten</em> eingefügt werden. </p>', 'Help - Product List', '<p>Here, you can insert additional information, further links, user manual etc. for the &quot;Help&quot;-function on <em>product pages</em>.</p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('c4241316b2e5c1966.96997012', 'oxhelpdefault', 'oxbaseshop', 1, 0, 1, 1, '', 'Hilfe - Main', '<p>Hier können zusätzliche Informationen, weiterführende Links, Bedienungshinweise etc. für die Hilfe-Funktion in der <em>Kategorieansicht</em> eingefügt werden. </p>', 'Help - Main', '<p>Here, you can insert additional information, further links, user manual etc. for the &quot;Help&quot;-function on <em>category pages</em>.</p>', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('c4241316b2e5c1966.96997013', 'oxhelpstart', 'oxbaseshop', 1, 0, 1, 1, '', 'Hilfe - Die Startseite', '<p>Hier können zusätzliche Informationen, weiterführende Links, Bedienungshinweise etc. für die Hilfe-Funktion auf der <em>Startseite</em> eingefügt werden. </p>\r\n<p>&nbsp;</p>', 'Help - Start page', '<p>Here, you can insert additional information, further links, user manual etc. for the &quot;Help&quot;-function on the <em>start page</em>.</p><br />', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', ''),
('220404cee0caf470e227c1c9f1ec4ae3', 'oxrighttocancellegend2', 'oxbaseshop', 1, 0, 1, 1, '', 'AGB und Widerrufsrecht', '[{oxifcontent ident="oxagb" object="oCont"}]\r\n    Es gelten unsere <a rel="nofollow" href="[{ $oCont->getLink() }]" onclick="window.open(''[{ $oCont->getLink()|oxaddparams:"plain=1"}]'', ''agb_popup'', ''resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400'');return false;" class="fontunderline">Allgemeinen Geschäftsbedingungen</a>.&nbsp;\r\n[{/oxifcontent}]\r\n[{oxifcontent ident="oxrightofwithdrawal" object="oCont"}]\r\n    Hier finden Sie <a id="test_OrderOpenWithdrawalBottom" rel="nofollow" href="[{ $oCont->getLink() }]" onclick="window.open(''[{ $oCont->getLink()|oxaddparams:"plain=1"}]'', ''rightofwithdrawal_popup'', ''resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400'');return false;">Einzelheiten zum Widerrufsrecht</a>.\r\n[{/oxifcontent}]', 'Terms and Conditions and Right to Withdrawal', '[{oxifcontent ident="oxagb" object="oCont"}] Our general <a rel="nofollow" href="[{ $oCont->getLink() }]" onclick="window.open(''[{ $oCont->getLink()|oxaddparams:"plain=1"}]'', ''agb_popup'', ''resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400'');return false;" class="fontunderline">terms and conditions</a> apply.&nbsp;\r\n[{/oxifcontent}]\r\n[{oxifcontent ident="oxrightofwithdrawal" object="oCont"}]\r\n    Read details about  <a id="test_OrderOpenWithdrawalBottom" rel="nofollow" href="[{ $oCont->getLink() }]" onclick="window.open(''[{ $oCont->getLink()|oxaddparams:"plain=1"}]'', ''rightofwithdrawal_popup'', ''resizable=yes,status=no,scrollbars=yes,menubar=no,width=620,height=400'');return false;">right of withdrawal</a>.\r\n[{/oxifcontent}]', 1, '', '', 1, '', '', '30e44ab83fdee7564.23264141', 'CMSFOLDER_USERINFO', '');
# --------------------------------------------------------

#
# Table structure for table `oxcounters`
#

DROP TABLE IF EXISTS `oxcounters`;

CREATE TABLE  `oxcounters` (
  `OXIDENT` CHAR( 32 ) NOT NULL ,
  `OXCOUNT` INT NOT NULL ,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (  `OXIDENT` )
) ENGINE = InnoDB;

# --------------------------------------------------------

#
# Table structure for table `oxcountry`
#

DROP TABLE IF EXISTS `oxcountry`;

CREATE TABLE `oxcountry` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXACTIVE` tinyint(1) NOT NULL default '0',
  `OXTITLE` char(128) NOT NULL default '',
  `OXISOALPHA2` char(2) NOT NULL default '',
  `OXISOALPHA3` char(3) NOT NULL default '',
  `OXUNNUM3` char(3) NOT NULL default '',
  `OXORDER` int(11) NOT NULL default '9999',
  `OXSHORTDESC` char(128) NOT NULL default '',
  `OXLONGDESC` char(255) NOT NULL default '',
  `OXTITLE_1` char(128) NOT NULL default '',
  `OXTITLE_2` char(128) NOT NULL default '',
  `OXTITLE_3` char(128) NOT NULL default '',
  `OXSHORTDESC_1` char(128) NOT NULL default '',
  `OXSHORTDESC_2` char(128) NOT NULL default '',
  `OXSHORTDESC_3` char(128) NOT NULL default '',
  `OXLONGDESC_1` char(255) NOT NULL,
  `OXLONGDESC_2` char(255) NOT NULL,
  `OXLONGDESC_3` char(255) NOT NULL,
  `OXVATSTATUS` tinyint(1) NOT NULL DEFAULT '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY (`OXACTIVE`)
) ENGINE=MyISAM;

#
# Data for table `oxcountry`
#

INSERT INTO `oxcountry` (`OXID`, `OXACTIVE`, `OXTITLE`, `OXISOALPHA2`, `OXISOALPHA3`, `OXUNNUM3`, `OXORDER`, `OXSHORTDESC`, `OXLONGDESC`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXSHORTDESC_1`, `OXSHORTDESC_2`, `OXSHORTDESC_3`, `OXLONGDESC_1`, `OXLONGDESC_2`, `OXLONGDESC_3`, `OXVATSTATUS`) VALUES
('2db455824e4a19cc7.14731328', 0, 'Anderes Land', '', '', '', 10000, '', 'Select this if you can not find your country.', 'Other country', '', '', '', '', '', 'Select this if you can not find your country.', '', '', 0),
('a7c40f631fc920687.20179984', 1, 'Deutschland', 'DE', 'DEU', '276', 9999, 'EU1', '', 'Germany', '', '', 'EU1', '', '', '', '', '', 1),
('a7c40f6320aeb2ec2.72885259', 1, 'Österreich', 'AT', 'AUT', '40', 9999, 'EU1', '', 'Austria', '', '', 'EU1', '', '', '', '', '', 1),
('a7c40f6321c6f6109.43859248', 1, 'Schweiz', 'CH', 'CHE', '756', 9999, 'EU1', '', 'Switzerland', '', '', 'EU1', '', '', '', '', '', 0),
('a7c40f6322d842ae3.83331920', 0, 'Liechtenstein', 'LI', 'LIE', '438', 9999, 'EU1', '', 'Liechtenstein', '', '', 'EU1', '', '', '', '', '', 0),
('a7c40f6323c4bfb36.59919433', 0, 'Italien', 'IT', 'ITA', '380', 9999, 'EU1', '', 'Italy', '', '', 'EU1', '', '', '', '', '', 1),
('a7c40f63264309e05.58576680', 0, 'Luxemburg', 'LU', 'LUX', '442', 9999, 'EU1', '', 'Luxembourg', '', '', 'EU1', '', '', '', '', '', 1),
('a7c40f63272a57296.32117580', 0, 'Frankreich', 'FR', 'FRA', '250', 9999, 'EU1', '', 'France', '', '', 'EU1', '', '', '', '', '', 1),
('a7c40f632848c5217.53322339', 0, 'Schweden', 'SE', 'SWE', '752', 9999, 'EU2', '', 'Sweden', '', '', 'EU2', '', '', '', '', '', 1),
('a7c40f63293c19d65.37472814', 0, 'Finnland', 'FI', 'FIN', '246', 9999, 'EU2', '', 'Finland', '', '', 'EU2', '', '', '', '', '', 1),
('a7c40f632a0804ab5.18804076', 1, 'Vereinigtes Königreich', 'GB', 'GBR', '826', 9999, 'EU2', '', 'United Kingdom', '', '', 'EU2', '', '', '', '', '', 1),
('a7c40f632be4237c2.48517912', 0, 'Irland', 'IE', 'IRL', '372', 9999, 'EU2', '', 'Ireland', '', '', 'EU2', '', '', '', '', '', 1),
('a7c40f632cdd63c52.64272623', 0, 'Niederlande', 'NL', 'NLD', '528', 9999, 'EU2', '', 'Netherlands', '', '', 'EU2', '', '', '', '', '', 1),
('a7c40f632e04633c9.47194042', 0, 'Belgien', 'BE', 'BEL', '56', 9999, 'Rest Europäische Union', '', 'Belgium', '', '', 'Rest of EU', '', '', '', '', '', 1),
('a7c40f632f65bd8e2.84963272', 0, 'Portugal', 'PT', 'PRT', '620', 9999, 'Rest Europäische Union', '', 'Portugal', '', '', 'Rest of EU', '', '', '', '', '', 1),
('a7c40f633038cd578.22975442', 0, 'Spanien', 'ES', 'ESP', '724', 9999, 'Rest Europäische Union', '', 'Spain', '', '', 'Rest of EU', '', '', '', '', '', 1),
('a7c40f633114e8fc6.25257477', 0, 'Griechenland', 'GR', 'GRC', '300', 9999, 'Rest Europäische Union', '', 'Greece', '', '', 'Rest of EU', '', '', '', '', '', 1),
('8f241f11095306451.36998225', 0, 'Afghanistan', 'AF', 'AFG', '4', 9999, 'Rest Welt', '', 'Afghanistan', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110953265a5.25286134', 0, 'Albanien', 'AL', 'ALB', '8', 9999, 'Rest Europa', '', 'Albania', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f1109533b943.50287900', 0, 'Algerien', 'DZ', 'DZA', '12', 9999, 'Rest Welt', '', 'Algeria', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109534f8c7.80349931', 0, 'Amerikanisch Samoa', 'AS', 'ASM', '16', 9999, 'Rest Welt', '', 'American Samoa', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095363464.89657222', 0, 'Andorra', 'AD', 'AND', '20', 9999, 'Europa', '', 'Andorra', '', '', 'Europe', '', '', '', '', '', 0),
('8f241f11095377d33.28678901', 0, 'Angola', 'AO', 'AGO', '24', 9999, 'Rest Welt', '', 'Angola', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095392e41.74397491', 0, 'Anguilla', 'AI', 'AIA', '660', 9999, 'Rest Welt', '', 'Anguilla', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110953a8d10.29474848', 0, 'Antarktis', 'AQ', 'ATA', '10', 9999, 'Rest Welt', '', 'Antarctica', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110953be8f2.56248134', 0, 'Antigua und Barbuda', 'AG', 'ATG', '28', 9999, 'Rest Welt', '', 'Antigua and Barbuda', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110953d2fb0.54260547', 0, 'Argentinien', 'AR', 'ARG', '32', 9999, 'Rest Welt', '', 'Argentina', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110953e7993.88180360', 0, 'Armenien', 'AM', 'ARM', '51', 9999, 'Rest Europa', '', 'Armenia', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f110953facc6.31621036', 0, 'Aruba', 'AW', 'ABW', '533', 9999, 'Rest Welt', '', 'Aruba', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095410f38.37165361', 0, 'Australien', 'AU', 'AUS', '36', 9999, 'Rest Welt', '', 'Australia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109543cf47.17877015', 0, 'Aserbaidschan', 'AZ', 'AZE', '31', 9999, 'Rest Welt', '', 'Azerbaijan', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095451379.72078871', 0, 'Bahamas', 'BS', 'BHS', '44', 9999, 'Rest Welt', '', 'Bahamas', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110954662e3.27051654', 0, 'Bahrain', 'BH', 'BHR', '48', 9999, 'Welt', '', 'Bahrain', '', '', 'World', '', '', '', '', '', 0),
('8f241f1109547ae49.60154431', 0, 'Bangladesch', 'BD', 'BGD', '50', 9999, 'Rest Welt', '', 'Bangladesh', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095497083.21181725', 0, 'Barbados', 'BB', 'BRB', '52', 9999, 'Rest Welt', '', 'Barbados', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110954ac5b9.63105203', 0, 'Weißrussland', 'BY', 'BLR', '112', 9999, 'Rest Europa', '', 'Belarus', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f110954d3621.45362515', 0, 'Belize', 'BZ', 'BLZ', '84', 9999, 'Rest Welt', '', 'Belize', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110954ea065.41455848', 0, 'Benin', 'BJ', 'BEN', '204', 9999, 'Rest Welt', '', 'Benin', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110954fee13.50011948', 0, 'Bermuda', 'BM', 'BMU', '60', 9999, 'Rest Welt', '', 'Bermuda', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095513ca0.75349731', 0, 'Bhutan', 'BT', 'BTN', '64', 9999, 'Rest Welt', '', 'Bhutan', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109552aee2.91004965', 0, 'Bolivien', 'BO', 'BOL', '68', 9999, 'Rest Welt', '', 'Bolivia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109553f902.06960438', 0, 'Bosnien und Herzegowina', 'BA', 'BIH', '70', 9999, 'Rest Europa', '', 'Bosnia and Herzegovina', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f11095554834.54199483', 0, 'Botsuana', 'BW', 'BWA', '72', 9999, 'Rest Welt', '', 'Botswana', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109556dd57.84292282', 0, 'Bouvetinsel', 'BV', 'BVT', '74', 9999, 'Rest Welt', '', 'Bouvet Island', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095592407.89986143', 0, 'Brasilien', 'BR', 'BRA', '76', 9999, 'Rest Welt', '', 'Brazil', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110955a7644.68859180', 0, 'Britisches Territorium im Indischen Ozean', 'IO', 'IOT', '86', 9999, 'Rest Welt', '', 'British Indian Ocean Territory', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110955bde61.63256042', 0, 'Brunei Darussalam', 'BN', 'BRN', '96', 9999, 'Rest Welt', '', 'Brunei Darussalam', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110955d3260.55487539', 0, 'Bulgarien', 'BG', 'BGR', '100', 9999, 'Rest Europa', '', 'Bulgaria', '', '', 'Rest Europe', '', '', '', '', '', 1),
('8f241f110955ea7c8.36762654', 0, 'Burkina Faso', 'BF', 'BFA', '854', 9999, 'Rest Welt', '', 'Burkina Faso', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110956004d5.11534182', 0, 'Burundi', 'BI', 'BDI', '108', 9999, 'Rest Welt', '', 'Burundi', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110956175f9.81682035', 0, 'Kambodscha', 'KH', 'KHM', '116', 9999, 'Rest Welt', '', 'Cambodia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095632828.20263574', 0, 'Kamerun', 'CM', 'CMR', '120', 9999, 'Rest Welt', '', 'Cameroon', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095649d18.02676059', 0, 'Kanada', 'CA', 'CAN', '124', 9999, 'Welt', '', 'Canada', '', '', 'World', '', '', '', '', '', 0),
('8f241f1109565e671.48876354', 0, 'Kap Verde', 'CV', 'CPV', '132', 9999, 'Rest Welt', '', 'Cape Verde', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095673248.50405852', 0, 'Kaimaninseln', 'KY', 'CYM', '136', 9999, 'Rest Welt', '', 'Cayman Islands', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109568a509.03566030', 0, 'Zentralafrikanische Republik', 'CF', 'CAF', '140', 9999, 'Rest Welt', '', 'Central African Republic', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109569d4c2.42800039', 0, 'Tschad', 'TD', 'TCD', '148', 9999, 'Rest Welt', '', 'Chad', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110956b3ea7.11168270', 0, 'Chile', 'CL', 'CHL', '152', 9999, 'Rest Welt', '', 'Chile', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110956c8860.37981845', 0, 'China', 'CN', 'CHN', '156', 9999, 'Rest Welt', '', 'China', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110956df6b2.52283428', 0, 'Weihnachtsinsel', 'CX', 'CXR', '162', 9999, 'Rest Welt', '', 'Christmas Island', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110956f54b4.26327849', 0, 'Kokosinseln (Keelinginseln)', 'CC', 'CCK', '166', 9999, 'Rest Welt', '', 'Cocos (Keeling) Islands', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109570a1e3.69772638', 0, 'Kolumbien', 'CO', 'COL', '170', 9999, 'Rest Welt', '', 'Colombia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109571f018.46251535', 0, 'Komoren', 'KM', 'COM', '174', 9999, 'Rest Welt', '', 'Comoros', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095732184.72771986', 0, 'Kongo', 'CG', 'COG', '178', 9999, 'Rest Welt', '', 'Congo', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095746a92.94878441', 0, 'Cookinseln', 'CK', 'COK', '184', 9999, 'Rest Welt', '', 'Cook Islands', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109575d708.20084150', 0, 'Costa Rica', 'CR', 'CRI', '188', 9999, 'Rest Welt', '', 'Costa Rica', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095771f76.87904122', 0, 'Cote d´Ivoire', 'CI', 'CIV', '384', 9999, 'Rest Welt', '', 'Cote d''Ivoire', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095789a04.65154246', 0, 'Kroatien', 'HR', 'HRV', '191', 9999, 'Rest Europa', '', 'Croatia', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f1109579ef49.91803242', 0, 'Kuba', 'CU', 'CUB', '192', 9999, 'Rest Welt', '', 'Cuba', '', '', 'World', '', '', '', '', '', 0),
('8f241f110957b6896.52725150', 0, 'Zypern', 'CY', 'CYP', '196', 9999, 'Rest Europa', '', 'Cyprus', '', '', 'Rest Europe', '', '', '', '', '', 1),
('8f241f110957cb457.97820918', 0, 'Tschechische Republik', 'CZ', 'CZE', '203', 9999, 'Europa', '', 'Czech Republic', '', '', 'Europe', '', '', '', '', '', 1),
('8f241f110957e6ef8.56458418', 0, 'Dänemark', 'DK', 'DNK', '208', 9999, 'Europa', '', 'Denmark', '', '', 'Europe', '', '', '', '', '', 1),
('8f241f110957fd356.02918645', 0, 'Dschibuti', 'DJ', 'DJI', '262', 9999, 'Rest Welt', '', 'Djibouti', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095811ea5.84717844', 0, 'Dominica', 'DM', 'DMA', '212', 9999, 'Rest Welt', '', 'Dominica', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095825bf2.61063355', 0, 'Dominikanische Republik', 'DO', 'DOM', '214', 9999, 'Rest Welt', '', 'Dominican Republic', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095839323.86755169', 0, 'Timor-Leste', 'TL', 'TLS', '626', 9999, 'Rest Welt', '', 'Timor-Leste', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109584d512.06663789', 0, 'Ecuador', 'EC', 'ECU', '218', 9999, 'Rest Welt', '', 'Ecuador', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095861fb7.55278256', 0, 'Ägypten', 'EG', 'EGY', '818', 9999, 'Welt', '', 'Egypt', '', '', 'World', '', '', '', '', '', 0),
('8f241f110958736a9.06061237', 0, 'El Salvador', 'SV', 'SLV', '222', 9999, 'Rest Welt', '', 'El Salvador', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109588d077.74284490', 0, 'Äquatorialguinea', 'GQ', 'GNQ', '226', 9999, 'Rest Welt', '', 'Equatorial Guinea', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110958a2216.38324531', 0, 'Eritrea', 'ER', 'ERI', '232', 9999, 'Rest Welt', '', 'Eritrea', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110958b69e4.93886171', 0, 'Estland', 'EE', 'EST', '233', 9999, 'Rest Europa', '', 'Estonia', '', '', 'Rest Europe', '', '', '', '', '', 1),
('8f241f110958caf67.08982313', 0, 'Äthiopien', 'ET', 'ETH', '210', 9999, 'Rest Welt', '', 'Ethiopia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110958e2cc3.90770249', 0, 'Falklandinseln (Malwinen)', 'FK', 'FLK', '238', 9999, 'Rest Welt', '', 'Falkland Islands (Malvinas)', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110958f7ba4.96908065', 0, 'Färöer', 'FO', 'FRO', '234', 9999, 'Rest Welt', '', 'Faroe Islands', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109590d226.07938729', 0, 'Fidschi', 'FJ', 'FJI', '242', 9999, 'Rest Welt', '', 'Fiji', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109594fcb1.79441780', 0, 'Französisch Guiana', 'GF', 'GUF', '254', 9999, 'Rest Welt', '', 'French Guiana', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110959636f5.71476354', 0, 'Französisch-Polynesien', 'PF', 'PYF', '258', 9999, 'Rest Welt', '', 'French Polynesia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110959784a3.34264829', 0, 'Französische Südgebiete', 'TF', 'ATF', '260', 9999, 'Rest Welt', '', 'French Southern Territories', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095994cb6.59353392', 0, 'Gabun', 'GA', 'GAB', '266', 9999, 'Rest Welt', '', 'Gabon', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110959ace77.17379319', 0, 'Gambia', 'GM', 'GMB', '270', 9999, 'Rest Welt', '', 'Gambia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110959c2341.01830199', 0, 'Georgien', 'GE', 'GEO', '268', 9999, 'Rest Europa', '', 'Georgia', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f110959e96b3.05752152', 0, 'Ghana', 'GH', 'GHA', '288', 9999, 'Rest Welt', '', 'Ghana', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110959fdde0.68919405', 0, 'Gibraltar', 'GI', 'GIB', '292', 9999, 'Rest Welt', '', 'Gibraltar', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095a29f47.04102343', 0, 'Grönland', 'GL', 'GRL', '304', 9999, 'Europa', '', 'Greenland', '', '', 'Europe', '', '', '', '', '', 0),
('8f241f11095a3f195.88886789', 0, 'Grenada', 'GD', 'GRD', '308', 9999, 'Rest Welt', '', 'Grenada', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095a52578.45413493', 0, 'Guadeloupe', 'GP', 'GLP', '312', 9999, 'Rest Welt', '', 'Guadeloupe', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095a717b3.68126681', 0, 'Guam', 'GU', 'GUM', '316', 9999, 'Rest Welt', '', 'Guam', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095a870a5.42235635', 0, 'Guatemala', 'GT', 'GTM', '320', 9999, 'Rest Welt', '', 'Guatemala', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095a9bf82.19989557', 0, 'Guinea', 'GN', 'GIN', '324', 9999, 'Rest Welt', '', 'Guinea', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095ab2b56.83049280', 0, 'Guinea-Bissau', 'GW', 'GNB', '624', 9999, 'Rest Welt', '', 'Guinea-Bissau', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095ac9d30.56640429', 0, 'Guyana', 'GY', 'GUY', '328', 9999, 'Rest Welt', '', 'Guyana', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095aebb06.34405179', 0, 'Haiti', 'HT', 'HTI', '332', 9999, 'Rest Welt', '', 'Haiti', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095aff2c3.98054755', 0, 'Heard Insel und McDonald Inseln', 'HM', 'HMD', '334', 9999, 'Rest Welt', '', 'Heard Island And Mcdonald Islands', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095b13f57.56022305', 0, 'Honduras', 'HN', 'HND', '340', 9999, 'Rest Welt', '', 'Honduras', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095b29021.49657118', 0, 'Hong Kong', 'HK', 'HKG', '344', 9999, 'Rest Welt', '', 'Hong Kong', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095b3e016.98213173', 0, 'Ungarn', 'HU', 'HUN', '348', 9999, 'Rest Europa', '', 'Hungary', '', '', 'Rest Europe', '', '', '', '', '', 1),
('8f241f11095b55846.26192602', 0, 'Island', 'IS', 'ISL', '352', 9999, 'Rest Europa', '', 'Iceland', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f11095b6bb86.01364904', 0, 'Indien', 'IN', 'IND', '356', 9999, 'Rest Welt', '', 'India', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095b80526.59927631', 0, 'Indonesien', 'ID', 'IDN', '360', 9999, 'Rest Welt', '', 'Indonesia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095b94476.05195832', 0, 'Iran', 'IR', 'IRN', '364', 9999, 'Welt', '', 'Iran', '', '', 'World', '', '', '', '', '', 0),
('8f241f11095bad5b2.42645724', 0, 'Irak', 'IQ', 'IRQ', '368', 9999, 'Welt', '', 'Iraq', '', '', 'World', '', '', '', '', '', 0),
('8f241f11095bd65e1.59459683', 0, 'Israel', 'IL', 'ISR', '376', 9999, 'Rest Europa', '', 'Israel', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f11095bfe834.63390185', 0, 'Jamaika', 'JM', 'JAM', '388', 9999, 'Rest Welt', '', 'Jamaica', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095c11d43.73419747', 0, 'Japan', 'JP', 'JPN', '392', 9999, 'Rest Welt', '', 'Japan', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095c2b304.75906962', 0, 'Jordanien', 'JO', 'JOR', '400', 9999, 'Rest Welt', '', 'Jordan', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095c3e2d1.36714463', 0, 'Kasachstan', 'KZ', 'KAZ', '398', 9999, 'Rest Europa', '', 'Kazakhstan', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f11095c5b8e8.66333679', 0, 'Kenia', 'KE', 'KEN', '404', 9999, 'Rest Welt', '', 'Kenya', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095c6e184.21450618', 0, 'Kiribati', 'KI', 'KIR', '296', 9999, 'Rest Welt', '', 'Kiribati', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095c87284.37982544', 0, 'Nordkorea', 'KP', 'PRK', '408', 9999, 'Rest Welt', '', 'North Korea', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095c9de64.01275726', 0, 'Südkorea', 'KR', 'KOR', '410', 9999, 'Rest Welt', '', 'South Korea', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095cb1546.46652174', 0, 'Kuwait', 'KW', 'KWT', '414', 9999, 'Welt', '', 'Kuwait', '', '', 'World', '', '', '', '', '', 0),
('8f241f11095cc7ef5.28043767', 0, 'Kirgisistan', 'KG', 'KGZ', '417', 9999, 'Rest Welt', '', 'Kyrgyzstan', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095cdccd5.96388808', 0, 'Laos', 'LA', 'LAO', '418', 9999, 'Rest Welt', '', 'Laos', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095cf2ea6.73925511', 0, 'Lettland', 'LV', 'LVA', '428', 9999, 'Rest Europa', '', 'Latvia', '', '', 'Rest Europe', '', '', '', '', '', 1),
('8f241f11095d07d87.58986129', 0, 'Libanon', 'LB', 'LBN', '422', 9999, 'Welt', '', 'Lebanon', '', '', 'World', '', '', '', '', '', 0),
('8f241f11095d1c9b2.21548132', 0, 'Lesotho', 'LS', 'LSO', '426', 9999, 'Rest Welt', '', 'Lesotho', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095d2fd28.91858908', 0, 'Liberia', 'LR', 'LBR', '430', 9999, 'Welt', '', 'Liberia', '', '', 'World', '', '', '', '', '', 0),
('8f241f11095d46188.64679605', 0, 'Libyen', 'LY', 'LBY', '434', 9999, 'Rest Welt', '', 'Libya', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095d6ffa8.86593236', 0, 'Litauen', 'LT', 'LTU', '440', 9999, 'Rest Europa', '', 'Lithuania', '', '', 'Rest Europe', '', '', '', '', '', 1),
('8f241f11095d9c1b2.13577033', 0, 'Macao', 'MO', 'MAC', '446', 9999, 'Rest Welt', '', 'Macao', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095db2291.58912887', 0, 'Mazedonien', 'MK', 'MKD', '807', 9999, 'Rest Europa', '', 'Macedonia', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f11095dccf17.06266806', 0, 'Madagaskar', 'MG', 'MDG', '450', 9999, 'Rest Welt', '', 'Madagascar', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095de2119.60795833', 0, 'Malawi', 'MW', 'MWI', '454', 9999, 'Rest Welt', '', 'Malawi', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095df78a8.44559506', 0, 'Malaysia', 'MY', 'MYS', '458', 9999, 'Rest Welt', '', 'Malaysia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095e0c6c9.43746477', 0, 'Malediven', 'MV', 'MDV', '462', 9999, 'Rest Welt', '', 'Maldives', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095e24006.17141715', 0, 'Mali', 'ML', 'MLI', '466', 9999, 'Rest Welt', '', 'Mali', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095e36eb3.69050509', 0, 'Malta', 'MT', 'MLT', '470', 9999, 'Rest Welt', '', 'Malta', '', '', 'Rest World', '', '', '', '', '', 1),
('8f241f11095e4e338.26817244', 0, 'Marshallinseln', 'MH', 'MHL', '584', 9999, 'Rest Welt', '', 'Marshall Islands', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095e631e1.29476484', 0, 'Martinique', 'MQ', 'MTQ', '474', 9999, 'Rest Welt', '', 'Martinique', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095e7bff9.09518271', 0, 'Mauretanien', 'MR', 'MRT', '478', 9999, 'Rest Welt', '', 'Mauritania', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095e90a81.01156393', 0, 'Mauritius', 'MU', 'MUS', '480', 9999, 'Rest Welt', '', 'Mauritius', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095ea6249.81474246', 0, 'Mayotte', 'YT', 'MYT', '175', 9999, 'Rest Welt', '', 'Mayotte', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095ebf3a6.86388577', 0, 'Mexiko', 'MX', 'MEX', '484', 9999, 'Rest Welt', '', 'Mexico', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095ed4902.49276197', 0, 'Mikronesien, Föderierte Staaten von', 'FM', 'FSM', '583', 9999, 'Rest Welt', '', 'Micronesia, Federated States Of', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095ee9923.85175653', 0, 'Moldau', 'MD', 'MDA', '498', 9999, 'Rest Europa', '', 'Moldova', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f11095f00d65.30318330', 0, 'Monaco', 'MC', 'MCO', '492', 9999, 'Europa', '', 'Monaco', '', '', 'Europe', '', '', '', '', '', 0),
('8f241f11095f160c9.41059441', 0, 'Mongolei', 'MN', 'MNG', '496', 9999, 'Rest Welt', '', 'Mongolia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11095f314f5.05830324', 0, 'Montserrat', 'MS', 'MSR', '500', 9999, 'Rest Welt', '', 'Montserrat', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096006828.49285591', 0, 'Marokko', 'MA', 'MAR', '504', 9999, 'Welt', '', 'Morocco', '', '', 'World', '', '', '', '', '', 0),
('8f241f1109601b419.55269691', 0, 'Mosambik', 'MZ', 'MOZ', '508', 9999, 'Rest Welt', '', 'Mozambique', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096030af5.65449043', 0, 'Myanmar', 'MM', 'MMR', '104', 9999, 'Rest Welt', '', 'Myanmar', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096046575.31382060', 0, 'Namibia', 'NA', 'NAM', '516', 9999, 'Rest Welt', '', 'Namibia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109605b1f4.20574895', 0, 'Nauru', 'NR', 'NRU', '520', 9999, 'Rest Welt', '', 'Nauru', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109607a9e7.03486450', 0, 'Nepal', 'NP', 'NPL', '524', 9999, 'Rest Welt', '', 'Nepal', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110960aeb64.09757010', 0, 'Niederländische Antillen', 'AN', 'ANT', '530', 9999, 'Rest Welt', '', 'Netherlands Antilles', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110960c3e97.21901471', 0, 'Neukaledonien', 'NC', 'NCL', '540', 9999, 'Rest Welt', '', 'New Caledonia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110960d8e58.96466103', 0, 'Neuseeland', 'NZ', 'NZL', '554', 9999, 'Rest Welt', '', 'New Zealand', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110960ec345.71805056', 0, 'Nicaragua', 'NI', 'NIC', '558', 9999, 'Rest Welt', '', 'Nicaragua', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096101a79.70513227', 0, 'Niger', 'NE', 'NER', '562', 9999, 'Rest Welt', '', 'Niger', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096116744.92008092', 0, 'Nigeria', 'NG', 'NGA', '566', 9999, 'Rest Welt', '', 'Nigeria', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109612dc68.63806992', 0, 'Niue', 'NU', 'NIU', '570', 9999, 'Rest Welt', '', 'Niue', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110961442c2.82573898', 0, 'Norfolkinsel', 'NF', 'NFK', '574', 9999, 'Rest Welt', '', 'Norfolk Island', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096162678.71164081', 0, 'Nördliche Marianen', 'MP', 'MNP', '580', 9999, 'Rest Welt', '', 'Northern Mariana Islands', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096176795.61257067', 0, 'Norwegen', 'NO', 'NOR', '578', 9999, 'Rest Europa', '', 'Norway', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f1109618d825.87661926', 0, 'Oman', 'OM', 'OMN', '512', 9999, 'Rest Welt', '', 'Oman', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110961a2401.59039740', 0, 'Pakistan', 'PK', 'PAK', '586', 9999, 'Rest Welt', '', 'Pakistan', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110961b7729.14290490', 0, 'Palau', 'PW', 'PLW', '585', 9999, 'Rest Welt', '', 'Palau', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110961cc384.18166560', 0, 'Panama', 'PA', 'PAN', '591', 9999, 'Rest Welt', '', 'Panama', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110961e3538.78435307', 0, 'Papua-Neuguinea', 'PG', 'PNG', '598', 9999, 'Rest Welt', '', 'Papua New Guinea', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110961f9d61.52794273', 0, 'Paraguay', 'PY', 'PRY', '600', 9999, 'Rest Welt', '', 'Paraguay', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109620b245.16261506', 0, 'Peru', 'PE', 'PER', '604', 9999, 'Rest Welt', '', 'Peru', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109621faf8.40135556', 0, 'Philippinen', 'PH', 'PHL', '608', 9999, 'Rest Welt', '', 'Philippines', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096234d62.44125992', 0, 'Pitcairn', 'PN', 'PCN', '612', 9999, 'Rest Welt', '', 'Pitcairn', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109624d3f8.50953605', 0, 'Polen', 'PL', 'POL', '616', 9999, 'Europa', '', 'Poland', '', '', 'Europe', '', '', '', '', '', 1),
('8f241f11096279a22.50582479', 0, 'Puerto Rico', 'PR', 'PRI', '630', 9999, 'Rest Welt', '', 'Puerto Rico', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109628f903.51478291', 0, 'Katar', 'QA', 'QAT', '634', 9999, 'Rest Welt', '', 'Qatar', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110962a3ec5.65857240', 0, 'Réunion', 'RE', 'REU', '638', 9999, 'Rest Welt', '', 'Réunion', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110962c3007.60363573', 0, 'Rumänien', 'RO', 'ROU', '642', 9999, 'Rest Europa', '', 'Romania', '', '', 'Rest Europe', '', '', '', '', '', 1),
('8f241f110962e40e6.75062153', 0, 'Russische Föderation', 'RU', 'RUS', '643', 9999, 'Rest Europa', '', 'Russian Federation', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f110962f8615.93666560', 0, 'Ruanda', 'RW', 'RWA', '646', 9999, 'Rest Welt', '', 'Rwanda', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110963177a7.49289900', 0, 'St. Kitts und Nevis', 'KN', 'KNA', '659', 9999, 'Rest Welt', '', 'Saint Kitts and Nevis', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109632fab4.68646740', 0, 'St. Lucia', 'LC', 'LCA', '662', 9999, 'Rest Welt', '', 'Saint Lucia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110963443c3.29598809', 0, 'St. Vincent und die Grenadinen', 'VC', 'VCT', '670', 9999, 'Rest Welt', '', 'Saint Vincent and The Grenadines', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096359986.06476221', 0, 'Samoa', 'WS', 'WSM', '882', 9999, 'Rest Welt', '', 'Samoa', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096375757.44126946', 0, 'San Marino', 'SM', 'SMR', '674', 9999, 'Europa', '', 'San Marino', '', '', 'Europe', '', '', '', '', '', 0),
('8f241f1109639b8c4.57484984', 0, 'Sao Tome und Principe', 'ST', 'STP', '678', 9999, 'Rest Welt', '', 'Sao Tome and Principe', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110963b9b20.41500709', 0, 'Saudi-Arabien', 'SA', 'SAU', '682', 9999, 'Welt', '', 'Saudi Arabia', '', '', 'World', '', '', '', '', '', 0),
('8f241f110963d9962.36307144', 0, 'Senegal', 'SN', 'SEN', '686', 9999, 'Rest Welt', '', 'Senegal', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110963f98d8.68428379', 0, 'Serbien', 'RS', 'SRB', '688', 9999, 'Rest Europa', '', 'Serbia', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f11096418496.77253079', 0, 'Seychellen', 'SC', 'SYC', '690', 9999, 'Rest Welt', '', 'Seychelles', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096436968.69551351', 0, 'Sierra Leone', 'SL', 'SLE', '694', 9999, 'Rest Welt', '', 'Sierra Leone', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096456a48.79608805', 0, 'Singapur', 'SG', 'SGP', '702', 9999, 'Rest Welt', '', 'Singapore', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109647a265.29938154', 0, 'Slowakei', 'SK', 'SVK', '703', 9999, 'Europa', '', 'Slovakia', '', '', 'Europe', '', '', '', '', '', 1),
('8f241f11096497149.85116254', 0, 'Slowenien', 'SI', 'SVN', '705', 9999, 'Rest Europa', '', 'Slovenia', '', '', 'Rest Europe', '', '', '', '', '', 1),
('8f241f110964b7bf9.49501835', 0, 'Salomonen', 'SB', 'SLB', '90', 9999, 'Rest Welt', '', 'Solomon Islands', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110964d5f29.11398308', 0, 'Somalia', 'SO', 'SOM', '706', 9999, 'Rest Welt', '', 'Somalia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110964f2623.74976876', 0, 'Südafrika', 'ZA', 'ZAF', '710', 9999, 'Rest Welt', '', 'South Africa', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096531330.03198083', 0, 'Sri Lanka', 'LK', 'LKA', '144', 9999, 'Rest Welt', '', 'Sri Lanka', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109654dca4.99466434', 0, 'Saint Helena', 'SH', 'SHN', '654', 9999, 'Rest Welt', '', 'Saint Helena', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109656cde9.10816078', 0, 'Saint Pierre und Miquelon', 'PM', 'SPM', '666', 9999, 'Rest Welt', '', 'Saint Pierre and Miquelon', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109658cbe5.08293991', 0, 'Sudan', 'SD', 'SDN', '736', 9999, 'Rest Welt', '', 'Sudan', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110965c7347.75108681', 0, 'Suriname', 'SR', 'SUR', '740', 9999, 'Rest Welt', '', 'Suriname', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110965eb7b7.26149742', 0, 'Svalbard und Jan Mayen', 'SJ', 'SJM', '744', 9999, 'Rest Welt', '', 'Svalbard and Jan Mayen', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109660c113.62780718', 0, 'Swasiland', 'SZ', 'SWZ', '748', 9999, 'Rest Welt', '', 'Swaziland', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109666b7f3.81435898', 0, 'Syrien', 'SY', 'SYR', '760', 9999, 'Rest Welt', '', 'Syria', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096687ec7.58824735', 0, 'Republik China (Taiwan)', 'TW', 'TWN', '158', 9999, 'Rest Welt', '', 'Taiwan, Province of China', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110966a54d1.43798997', 0, 'Tadschikistan', 'TJ', 'TJK', '762', 9999, 'Rest Welt', '', 'Tajikistan', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110966c3a75.68297960', 0, 'Tansania', 'TZ', 'TZA', '834', 9999, 'Rest Welt', '', 'Tanzania', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096707e08.60512709', 0, 'Thailand', 'TH', 'THA', '764', 9999, 'Rest Welt', '', 'Thailand', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110967241e1.34925220', 0, 'Togo', 'TG', 'TGO', '768', 9999, 'Rest Welt', '', 'Togo', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096742565.72138875', 0, 'Tokelau', 'TK', 'TKL', '772', 9999, 'Rest Welt', '', 'Tokelau', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096762b31.03069244', 0, 'Tonga', 'TO', 'TON', '776', 9999, 'Rest Welt', '', 'Tonga', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109677ed23.84886671', 0, 'Trinidad und Tobago', 'TT', 'TTO', '780', 9999, 'Rest Welt', '', 'Trinidad and Tobago', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109679d988.46004322', 0, 'Tunesien', 'TN', 'TUN', '788', 9999, 'Welt', '', 'Tunisia', '', '', 'World', '', '', '', '', '', 0),
('8f241f110967bba40.88233204', 0, 'Türkei', 'TR', 'TUR', '792', 9999, 'Rest Europa', '', 'Turkey', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f110967d8f65.52699796', 0, 'Turkmenistan', 'TM', 'TKM', '795', 9999, 'Rest Welt', '', 'Turkmenistan', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110967f73f8.13141492', 0, 'Turks- und Caicosinseln', 'TC', 'TCA', '796', 9999, 'Rest Welt', '', 'Turks and Caicos Islands', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109680ec30.97426963', 0, 'Tuvalu', 'TV', 'TUV', '798', 9999, 'Rest Welt', '', 'Tuvalu', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096823019.47846368', 0, 'Uganda', 'UG', 'UGA', '800', 9999, 'Rest Welt', '', 'Uganda', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110968391d2.37199812', 0, 'Ukraine', 'UA', 'UKR', '804', 9999, 'Rest Europa', '', 'Ukraine', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f1109684bf15.63071279', 0, 'Vereinigte Arabische Emirate', 'AE', 'ARE', '784', 9999, 'Rest Welt', '', 'United Arab Emirates', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096877ac0.98748826', 1, 'Vereinigte Staaten von Amerika', 'US', 'USA', '840', 9999, 'Welt', '', 'United States', '', '', 'World', '', '', '', '', '', 0),
('8f241f11096894977.41239553', 0, 'United States Minor Outlying Islands', 'UM', 'UMI', '581', 9999, 'Rest Welt', '', 'United States Minor Outlying Islands', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110968a7cc9.56710143', 0, 'Uruguay', 'UY', 'URY', '858', 9999, 'Rest Welt', '', 'Uruguay', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110968bec45.44161857', 0, 'Usbekistan', 'UZ', 'UZB', '860', 9999, 'Rest Welt', '', 'Uzbekistan', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110968d3f03.13630334', 0, 'Vanuatu', 'VU', 'VUT', '548', 9999, 'Rest Welt', '', 'Vanuatu', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110968ebc30.63792746', 0, 'Heiliger Stuhl (Vatikanstadt)', 'VA', 'VAT', '336', 9999, 'Europa', '', 'Holy See (Vatican City State)', '', '', 'Europe', '', '', '', '', '', 0),
('8f241f11096902d92.14742486', 0, 'Venezuela', 'VE', 'VEN', '862', 9999, 'Rest Welt', '', 'Venezuela', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096919d00.92534927', 0, 'Vietnam', 'VN', 'VNM', '704', 9999, 'Rest Welt', '', 'Viet Nam', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109692fc04.15216034', 0, 'Britische Jungferninseln', 'VG', 'VGB', '92', 9999, 'Rest Welt', '', 'Virgin Islands, British', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096944468.61956573', 0, 'Amerikanische Jungferninseln', 'VI', 'VIR', '850', 9999, 'Rest Welt', '', 'Virgin Islands, U.S.', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110969598c8.76966113', 0, 'Wallis und Futuna', 'WF', 'WLF', '876', 9999, 'Rest Welt', '', 'Wallis and Futuna', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109696e4e9.33006292', 0, 'Westsahara', 'EH', 'ESH', '732', 9999, 'Rest Welt', '', 'Western Sahara', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096982354.73448958', 0, 'Jemen', 'YE', 'YEM', '887', 9999, 'Rest Welt', '', 'Yemen', '', '', 'Rest World', '', '', '', '', '', 0),
('a7c40f632a0804ab5.18804099', 0, 'Åland Inseln', 'AX', 'ALA', '248', 9999, 'Rest Welt', '', 'Åland Islands', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110969c34a2.42564730', 0, 'Sambia', 'ZM', 'ZMB', '894', 9999, 'Rest Welt', '', 'Zambia', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110969da699.04185888', 0, 'Simbabwe', 'ZW', 'ZWE', '716', 9999, 'Rest Welt', '', 'Zimbabwe', '', '', 'Rest World', '', '', '', '', '', 0),
('56d308a822c18e106.3ba59048', 0, 'Montenegro', 'ME', 'MNE', '499', 9999, 'Rest Europa', '', 'Montenegro', '', '', 'Rest Europe', '', '', '', '', '', 0),
('8f241f1109575d708.20084199', 0, 'Kongo, Dem. Rep.', 'CD', 'COD', '180', 9999, 'Rest Welt', '', 'Congo, The Democratic Republic Of The', '', '', 'Rest World', '', '', '', '', '', 0),
('56d308a822c18e106.3ba59099', 0, 'Guernsey', 'GG', 'GGY', '831', 9999, 'Rest Welt', '', 'Guernsey', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096982354.73448999', 0, 'Isle of Man', 'IM', 'IMN', '833', 9999, 'Rest Welt', '', 'Isle Of Man', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f11096944468.61956599', 0, 'Jersey', 'JE', 'JEY', '832', 9999, 'Rest Welt', '', 'Jersey', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110968ebc30.63792799', 0, 'Palästinische Gebiete', 'PS', 'PSE', '275', 9999, 'Rest Welt', '', 'Palestinian Territory, Occupied', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f110968a7cc9.56710199', 0, 'Saint-Barthélemy', 'BL', 'BLM', '652', 9999, 'Rest Welt', '', 'Saint Barthélemy', '', '', 'Rest World', '', '', '', '', '', 0),
('a7c40f632f65bd8e2.84963299', 0, 'Saint-Martin', 'MF', 'MAF', '663', 9999, 'Rest Welt', '', 'Saint Martin', '', '', 'Rest World', '', '', '', '', '', 0),
('8f241f1109533b943.50287999', 0, 'Südgeorgien und die Südlichen Sandwichinseln', 'GS', 'SGS', '239', 9999, 'Rest Welt', '', 'South Georgia and The South Sandwich Islands', '', '', 'Rest World', '', '', '', '', '', 0);

#
# Table structure for table `oxdel2delset`
#

DROP TABLE IF EXISTS `oxdel2delset`;

CREATE TABLE `oxdel2delset` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXDELID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXDELSETID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXDELID` (`OXDELID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxdelivery`
#

DROP TABLE IF EXISTS `oxdelivery`;

CREATE TABLE `oxdelivery` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXACTIVE` tinyint(1) NOT NULL default '0',
  `OXACTIVEFROM` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXACTIVETO` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXTITLE` varchar(255) NOT NULL default '',
  `OXTITLE_1` varchar(255) NOT NULL default '',
  `OXTITLE_2` varchar(255) NOT NULL default '',
  `OXTITLE_3` varchar(255) NOT NULL default '',
  `OXADDSUMTYPE` enum('%','abs') NOT NULL default 'abs',
  `OXADDSUM` double NOT NULL default '0',
  `OXDELTYPE` enum('a','s','w','p') NOT NULL default 'a',
  `OXPARAM` double NOT NULL default '0',
  `OXPARAMEND` double NOT NULL default '0',
  `OXFIXED` tinyint(1) NOT NULL default '0',
  `OXSORT` int(11) NOT NULL default '9999',
  `OXFINALIZE` tinyint(1) NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXSHOPID` (`OXSHOPID`)
)  ENGINE=MyISAM;

#
# Table structure for table `oxdeliveryset`
#

DROP TABLE IF EXISTS `oxdeliveryset`;

CREATE TABLE `oxdeliveryset` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXACTIVE` tinyint(1) NOT NULL default '0',
  `OXACTIVEFROM` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXACTIVETO` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXTITLE` varchar(255) NOT NULL default '',
  `OXTITLE_1` varchar(255) NOT NULL default '',
  `OXTITLE_2` varchar(255) NOT NULL default '',
  `OXTITLE_3` varchar(255) NOT NULL default '',
  `OXPOS` int(11) NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXSHOPID` (`OXSHOPID`)
) ENGINE=MyISAM;

#
# Data for table `oxdeliveryset`
#
INSERT INTO `oxdeliveryset` (`OXID`, `OXSHOPID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXPOS`) VALUES
('oxidstandard', 'oxbaseshop', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Standard', 'Standard', '', '', 10);

#
# Table structure for table `oxdiscount`
#

DROP TABLE IF EXISTS `oxdiscount`;

CREATE TABLE `oxdiscount` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXACTIVE` tinyint(1) NOT NULL default '0',
  `OXACTIVEFROM` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXACTIVETO` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXTITLE` varchar(128) NOT NULL default '',
  `OXTITLE_1` varchar( 128 ) NOT NULL,
  `OXTITLE_2` varchar( 128 ) NOT NULL,
  `OXTITLE_3` varchar( 128 ) NOT NULL,
  `OXAMOUNT` double NOT NULL default '0',
  `OXAMOUNTTO` double NOT NULL default '999999',
  `OXPRICETO` double NOT NULL default '999999',
  `OXPRICE` double NOT NULL default '0',
  `OXADDSUMTYPE` enum('%','abs','itm') NOT NULL default '%',
  `OXADDSUM` double NOT NULL default '0',
  `OXITMARTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXITMAMOUNT` double NOT NULL default '1',
  `OXITMMULTIPLE` int(1) NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXSHOPID` (`OXSHOPID`),
  KEY `OXACTIVE` (`OXACTIVE`),
  KEY `OXACTIVEFROM` (`OXACTIVEFROM`),
  KEY `OXACTIVETO` (`OXACTIVETO`)
) ENGINE=MyISAM;

#
# Table structure for table `oxfiles`
#

DROP TABLE IF EXISTS `oxfiles`;

CREATE TABLE IF NOT EXISTS `oxfiles` (
  `OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `OXARTID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `OXFILENAME` varchar(128) NOT NULL,
  `OXSTOREHASH` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `OXPURCHASEDONLY` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `OXMAXDOWNLOADS` int(11) NOT NULL default '-1',
  `OXMAXUNREGDOWNLOADS` int(11) NOT NULL default '-1',
  `OXLINKEXPTIME` int(11) NOT NULL default '-1',
  `OXDOWNLOADEXPTIME` int(11) NOT NULL default '-1',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (`OXID`),
  KEY `OXARTID` (`OXARTID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxgbentries`
#

DROP TABLE IF EXISTS `oxgbentries`;

CREATE TABLE `oxgbentries` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXCONTENT` text NOT NULL,
  `OXCREATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXACTIVE` tinyint(1) NOT NULL default '0' ,
  `OXVIEWED` tinyint(1) NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY (`OXUSERID`)
) ENGINE=MyISAM COMMENT='Guestbook`s entries';

#
# Table structure for table `oxgroups`
#

DROP TABLE IF EXISTS `oxgroups`;

CREATE TABLE `oxgroups` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXACTIVE` tinyint(1) NOT NULL default '1',
  `OXTITLE` varchar(128) NOT NULL default '',
  `OXTITLE_1` varchar(128) NOT NULL default '',
  `OXTITLE_2` varchar(128) NOT NULL default '',
  `OXTITLE_3` varchar(128) NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXACTIVE` (`OXACTIVE`)
) ENGINE=MyISAM;

#
# Data for table `oxgroups`
#
INSERT INTO `oxgroups` (`OXID`, `OXACTIVE`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`) VALUES
('oxidblacklist', 1, 'Blacklist', 'Blacklist', '', ''),
('oxidsmallcust', 1, 'Geringer Umsatz', 'Less Turnover', '', ''),
('oxidmiddlecust', 1, 'Mittlerer Umsatz', 'Medium Turnover', '', ''),
('oxidgoodcust', 1, 'Grosser Umsatz', 'Huge Turnover', '', ''),
('oxidforeigncustomer', 1, 'Auslandskunde', 'Foreign Customer', '', ''),
('oxidnewcustomer', 1, 'Inlandskunde', 'Domestic Customer', '', ''),
('oxidpowershopper', 1, 'Powershopper', 'Powershopper', '', ''),
('oxiddealer', 1, 'Händler', 'Retailer', '', ''),
('oxidnewsletter', 1, 'Newsletter-Abonnenten', 'Newsletter Recipients', '', ''),
('oxidadmin', 1, 'Shop-Admin', 'Store Administrator', '', ''),
('oxidpriceb', 1, 'Preis B', 'Price B', '', ''),
('oxidpricea', 1, 'Preis A', 'Price A', '', ''),
('oxidpricec', 1, 'Preis C', 'Price C', '', ''),
('oxidblocked', 1, 'BLOCKED', 'BLOCKED', '', ''),
('oxidcustomer', 1, 'Kunde', 'Customer', '', ''),
('oxidnotyetordered', 1, 'Noch nicht gekauft', 'Not Yet Purchased', '', '');

#
# Table structure for table `oxinvitations`
# for storing information about invited users
# created 2010-01-06
#

DROP TABLE IF EXISTS `oxinvitations`;

CREATE TABLE IF NOT EXISTS `oxinvitations` (
   `OXUSERID` char(32) collate latin1_general_ci NOT NULL,
   `OXDATE` date NOT NULL,
   `OXEMAIL` varchar(255) collate latin1_general_ci NOT NULL,
   `OXPENDING` mediumint(9) NOT NULL,
   `OXACCEPTED` mediumint(9) NOT NULL,
   `OXTYPE` tinyint(4) NOT NULL default '1',
   `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    KEY `OXUSERID` (`OXUSERID`),
    KEY `OXDATE` (`OXDATE`)
) ENGINE=MYISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

#
# Table structure for table `oxlinks`
#

DROP TABLE IF EXISTS `oxlinks`;

CREATE TABLE `oxlinks` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXACTIVE` tinyint(1) NOT NULL default '0',
  `OXURL` varchar(255) NOT NULL default '',
  `OXURLDESC` text NOT NULL,
  `OXURLDESC_1` text NOT NULL,
  `OXURLDESC_2` text NOT NULL,
  `OXURLDESC_3` text NOT NULL,
  `OXINSERT` datetime default NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXSHOPID` (`OXSHOPID`),
  KEY `OXINSERT` (`OXINSERT`),
  KEY `OXACTIVE` (`OXACTIVE`)
) ENGINE=MyISAM;

#
# Table structure for table `oxlogs`
#

DROP TABLE IF EXISTS `oxlogs`;

CREATE TABLE `oxlogs` (
  `OXTIME` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSESSID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXCLASS` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXFNC` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXCNID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXANID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXPARAMETER` varchar(64) NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB;

#
# Table structure for table `oxmanufacturers`
#

DROP TABLE IF EXISTS `oxmanufacturers`;

CREATE TABLE `oxmanufacturers` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXACTIVE` tinyint(1) NOT NULL default '1',
  `OXICON` char(128) NOT NULL default '',
  `OXTITLE` char(255) NOT NULL default '',
  `OXSHORTDESC` char(255) NOT NULL default '',
  `OXTITLE_1` char(255) NOT NULL default '',
  `OXSHORTDESC_1` char(255) NOT NULL default '',
  `OXTITLE_2` char(255) NOT NULL default '',
  `OXSHORTDESC_2` char(255) NOT NULL default '',
  `OXTITLE_3` char(255) NOT NULL default '',
  `OXSHORTDESC_3` char(255) NOT NULL default '',
  `OXSHOWSUFFIX` tinyint(1) NOT NULL default '1',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxmediaurls`
# For storing extended file urls
# Created 2008-06-25
#

DROP TABLE IF EXISTS `oxmediaurls`;

CREATE TABLE `oxmediaurls` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXURL` varchar(255) NOT NULL,
  `OXDESC` varchar(255) NOT NULL,
  `OXDESC_1` varchar(255) NOT NULL,
  `OXDESC_2` varchar(255) NOT NULL,
  `OXDESC_3` varchar(255) NOT NULL,
  `OXISUPLOADED` int(1) NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
 PRIMARY KEY ( `OXID` ) ,
 INDEX ( `OXOBJECTID` )
) ENGINE = MYISAM ;

#
# Table structure for table `oxnews`
#

DROP TABLE IF EXISTS `oxnews`;

CREATE TABLE `oxnews` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXACTIVE` tinyint(1) NOT NULL default '1',
  `OXACTIVEFROM` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXACTIVETO` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXDATE` date NOT NULL default '0000-00-00',
  `OXSHORTDESC` varchar(255) NOT NULL default '',
  `OXLONGDESC` text NOT NULL,
  `OXACTIVE_1` tinyint(1) NOT NULL default '0',
  `OXSHORTDESC_1` varchar(255) NOT NULL default '',
  `OXLONGDESC_1` text NOT NULL,
  `OXACTIVE_2` tinyint(1) NOT NULL default '0',
  `OXSHORTDESC_2` varchar(255) NOT NULL default '',
  `OXLONGDESC_2` text NOT NULL,
  `OXACTIVE_3` tinyint(1) NOT NULL default '0',
  `OXSHORTDESC_3` varchar(255) NOT NULL default '',
  `OXLONGDESC_3` text NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXSHOPID` (`OXSHOPID`),
  KEY `OXACTIVE` (`OXACTIVE`),
  KEY `OXACTIVEFROM` (`OXACTIVEFROM`),
  KEY `OXACTIVETO` (`OXACTIVETO`)
) ENGINE=MyISAM;

#
# Table structure for table `oxnewsletter`
#

DROP TABLE IF EXISTS `oxnewsletter`;

CREATE TABLE `oxnewsletter` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTITLE` varchar(255) NOT NULL default '',
  `OXTEMPLATE` mediumtext NOT NULL,
  `OXPLAINTEMPLATE` mediumtext NOT NULL,
  `OXSUBJECT` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxnewssubscribed`
#

DROP TABLE IF EXISTS `oxnewssubscribed`;

CREATE TABLE `oxnewssubscribed` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSAL` char(64) NOT NULL default '',
  `OXFNAME` char(128) NOT NULL default '',
  `OXLNAME` char(128) NOT NULL default '',
  `OXEMAIL` char(128) NOT NULL default '',
  `OXDBOPTIN` tinyint(1) NOT NULL default '0',
  `OXEMAILFAILED` tinyint(1) NOT NULL default '0',
  `OXSUBSCRIBED` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXUNSUBSCRIBED` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  UNIQUE KEY `OXEMAIL` (`OXEMAIL`),
  KEY `OXUSERID` (`OXUSERID`)
) ENGINE=MyISAM;

#
# Data for table `oxnewssubscribed`
#
INSERT INTO `oxnewssubscribed` (`OXID`, `OXUSERID`, `OXSAL`, `OXFNAME`, `OXLNAME`, `OXEMAIL`, `OXDBOPTIN`, `OXEMAILFAILED`, `OXSUBSCRIBED`, `OXUNSUBSCRIBED`) VALUES
('0b742e66fd94c88b8.61001136', 'oxdefaultadmin', 'MR', 'John', 'Doe', 'admin', 1, 0, '2005-07-26 19:16:09', '0000-00-00 00:00:00');

#
# Table structure for table `oxobject2action`
#

DROP TABLE IF EXISTS `oxobject2action`;

CREATE TABLE IF NOT EXISTS `oxobject2action` (
  `OXID` char(32) collate latin1_general_ci NOT NULL,
  `OXACTIONID` char(32) collate latin1_general_ci NOT NULL default '',
  `OXOBJECTID` char(32) collate latin1_general_ci NOT NULL default '',
  `OXCLASS` char(32) collate latin1_general_ci NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXACTIONID` (`OXACTIONID`,`OXCLASS`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

#
# Table structure for table `oxobject2article`
#

DROP TABLE IF EXISTS `oxobject2article`;

CREATE TABLE `oxobject2article` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXARTICLENID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSORT` int(5) NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXARTICLENID` (`OXARTICLENID`),
  KEY `OXOBJECTID` (`OXOBJECTID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxobject2attribute`
#

DROP TABLE IF EXISTS `oxobject2attribute`;

CREATE TABLE `oxobject2attribute` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXATTRID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXVALUE` char(255) NOT NULL default '',
  `OXPOS` int(11) NOT NULL default '9999',
  `OXVALUE_1` char(255) NOT NULL default '',
  `OXVALUE_2` char(255) NOT NULL default '',
  `OXVALUE_3` char(255) NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXATTRID` (`OXATTRID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxobject2category`
#

DROP TABLE IF EXISTS `oxobject2category`;

CREATE TABLE `oxobject2category` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXCATNID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXPOS` int(11) NOT NULL default '0',
  `OXTIME` INT( 11 ) DEFAULT 0 NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY ( `OXOBJECTID` ),
  KEY (`OXPOS`),
  KEY `OXMAINIDX` (`OXCATNID`,`OXOBJECTID`),
  KEY `OXTIME` (`OXTIME`)
) ENGINE=MyISAM;

#
# Table structure for table `oxobject2delivery`
#

DROP TABLE IF EXISTS `oxobject2delivery`;

CREATE TABLE `oxobject2delivery` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXDELIVERYID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTYPE` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXDELIVERYID` ( `OXDELIVERYID` , `OXTYPE` )
) ENGINE=MyISAM;

#
# Table structure for table `oxobject2discount`
#

DROP TABLE IF EXISTS `oxobject2discount`;

CREATE TABLE `oxobject2discount` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXDISCOUNTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTYPE` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `oxobjectid` (`OXOBJECTID`),
  KEY `oxdiscidx` (`OXDISCOUNTID`,`OXTYPE`)
) ENGINE=MyISAM;

#
# Table structure for table `oxobject2group`
#

DROP TABLE IF EXISTS `oxobject2group`;

CREATE TABLE `oxobject2group` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXGROUPSID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXGROUPSID` (`OXGROUPSID`)
) ENGINE=MyISAM;

INSERT INTO `oxobject2group` (`OXID`, `OXSHOPID`, `OXOBJECTID`, `OXGROUPSID`) VALUES
('e913fdd8443ed43e1.51222316', 'oxbaseshop', 'oxdefaultadmin', 'oxidadmin');

#
# Table structure for table `oxobject2list`
#

DROP TABLE IF EXISTS `oxobject2list`;

CREATE TABLE `oxobject2list` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXLISTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXDESC` text NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXLISTID` (`OXLISTID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxobject2payment`
#

DROP TABLE IF EXISTS `oxobject2payment`;

CREATE TABLE `oxobject2payment` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXPAYMENTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTYPE` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxobject2selectlist`
#

DROP TABLE IF EXISTS `oxobject2selectlist`;

CREATE TABLE `oxobject2selectlist` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSELNID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSORT` int(5) NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXSELNID` (`OXSELNID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxobject2seodata`
# For storing SEO meta data
# Created 2010-05-11
#

DROP TABLE IF EXISTS `oxobject2seodata`;

CREATE TABLE `oxobject2seodata` (
  `OXOBJECTID` CHAR( 32 ) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` CHAR( 32 ) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXLANG` INT( 2 ) NOT NULL default '0',
  `OXKEYWORDS` TEXT NOT NULL ,
  `OXDESCRIPTION` TEXT NOT NULL ,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY ( `OXOBJECTID` , `OXSHOPID` , `OXLANG` )
) ENGINE = MYISAM ;

#
# Table structure for table `oxorder`
#

DROP TABLE IF EXISTS `oxorder`;

CREATE TABLE `oxorder` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXORDERDATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXORDERNR` int(11) UNSIGNED NOT NULL default '0',
  `OXBILLCOMPANY` varchar(255) NOT NULL default '',
  `OXBILLEMAIL` varchar(255) NOT NULL default '',
  `OXBILLFNAME` varchar(255) NOT NULL default '',
  `OXBILLLNAME` varchar(255) NOT NULL default '',
  `OXBILLSTREET` varchar(255) NOT NULL default '',
  `OXBILLSTREETNR` varchar(16) NOT NULL default '',
  `OXBILLADDINFO` varchar(255) NOT NULL default '',
  `OXBILLUSTID` varchar(255) NOT NULL default '',
  `OXBILLCITY` varchar(255) NOT NULL default '',
  `OXBILLCOUNTRYID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXBILLSTATEID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXBILLZIP` varchar(16) NOT NULL default '',
  `OXBILLFON` varchar(128) NOT NULL default '',
  `OXBILLFAX` varchar(128) NOT NULL default '',
  `OXBILLSAL` varchar(128) NOT NULL default '',
  `OXDELCOMPANY` varchar(255) NOT NULL default '',
  `OXDELFNAME` varchar(255) NOT NULL default '',
  `OXDELLNAME` varchar(255) NOT NULL default '',
  `OXDELSTREET` varchar(255) NOT NULL default '',
  `OXDELSTREETNR` varchar(16) NOT NULL default '',
  `OXDELADDINFO` varchar(255) NOT NULL default '',
  `OXDELCITY` varchar(255) NOT NULL default '',
  `OXDELCOUNTRYID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXDELSTATEID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXDELZIP` varchar(16) NOT NULL default '',
  `OXDELFON` varchar(128) NOT NULL default '',
  `OXDELFAX` varchar(128) NOT NULL default '',
  `OXDELSAL` varchar(128) NOT NULL default '',
  `OXPAYMENTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXPAYMENTTYPE` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTOTALNETSUM` double NOT NULL default '0',
  `OXTOTALBRUTSUM` double NOT NULL default '0',
  `OXTOTALORDERSUM` double NOT NULL default '0',
  `OXARTVAT1` double NOT NULL default '0',
  `OXARTVATPRICE1` double NOT NULL default '0',
  `OXARTVAT2` double NOT NULL default '0',
  `OXARTVATPRICE2` double NOT NULL default '0',
  `OXDELCOST` double NOT NULL default '0',
  `OXDELVAT` double NOT NULL default '0',
  `OXPAYCOST` double NOT NULL default '0',
  `OXPAYVAT` double NOT NULL default '0',
  `OXWRAPCOST` double NOT NULL default '0',
  `OXWRAPVAT` double NOT NULL default '0',
  `OXGIFTCARDCOST` double NOT NULL default '0',
  `OXGIFTCARDVAT` double NOT NULL default '0',
  `OXCARDID` varchar( 32 ) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXCARDTEXT` text NOT NULL,
  `OXDISCOUNT` double NOT NULL default '0',
  `OXEXPORT` tinyint(4) NOT NULL default '0',
  `OXBILLNR` varchar(128) NOT NULL default '',
  `OXBILLDATE` date NOT NULL default '0000-00-00',
  `OXTRACKCODE` varchar(128) NOT NULL default '',
  `OXSENDDATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXREMARK` text NOT NULL,
  `OXVOUCHERDISCOUNT` double NOT NULL default '0',
  `OXCURRENCY` varchar(32) NOT NULL default '',
  `OXCURRATE` double NOT NULL default '0',
  `OXFOLDER` varchar(32) NOT NULL default '',
  `OXTRANSID` varchar(64) NOT NULL default '',
  `OXPAYID` varchar(64) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXXID` varchar(64) NOT NULL default '',
  `OXPAID` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXSTORNO` tinyint(1) NOT NULL default '0',
  `OXIP` varchar(39) NOT NULL default '',
  `OXTRANSSTATUS` varchar(30) NOT NULL default '',
  `OXLANG` int(2) NOT NULL default '0',
  `OXINVOICENR` int(11) NOT NULL default '0',
  `OXDELTYPE` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTSPROTECTID` char(64) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTSPROTECTCOSTS` double NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `OXISNETTOMODE` tinyint(1) UNSIGNED NOT NULL DEFAULT  '0',
  PRIMARY KEY  (`OXID`),
  KEY `MAINIDX` (`OXSHOPID`,`OXSTORNO`,`OXORDERDATE`)
) ENGINE=InnoDB;

#
# Table structure for table `oxorderarticles`
#

DROP TABLE IF EXISTS `oxorderarticles`;

CREATE TABLE `oxorderarticles` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXORDERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXAMOUNT` double NOT NULL default '0',
  `OXARTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXARTNUM` varchar(255) NOT NULL default '',
  `OXTITLE` varchar(255) NOT NULL default '',
  `OXSHORTDESC` varchar(255) NOT NULL default '',
  `OXSELVARIANT` varchar(255) NOT NULL default '',
  `OXNETPRICE` double NOT NULL default '0',
  `OXBRUTPRICE` double NOT NULL default '0',
  `OXVATPRICE` double NOT NULL default '0',
  `OXVAT` double NOT NULL default '0',
  `OXPERSPARAM` text NOT NULL,
  `OXPRICE` double NOT NULL default '0',
  `OXBPRICE` double NOT NULL default '0',
  `OXNPRICE` double NOT NULL default '0',
  `OXWRAPID` varchar( 32 ) NOT NULL default '',
  `OXEXTURL` varchar(255) NOT NULL default '',
  `OXURLDESC` varchar(255) NOT NULL default '',
  `OXURLIMG` varchar(128) NOT NULL default '',
  `OXTHUMB` varchar(128) NOT NULL default '',
  `OXPIC1` varchar(128) NOT NULL default '',
  `OXPIC2` varchar(128) NOT NULL default '',
  `OXPIC3` varchar(128) NOT NULL default '',
  `OXPIC4` varchar(128) NOT NULL default '',
  `OXPIC5` varchar(128) NOT NULL default '',
  `OXWEIGHT` double NOT NULL default '0',
  `OXSTOCK` double NOT NULL default '-1',
  `OXDELIVERY` date NOT NULL default '0000-00-00',
  `OXINSERT` date NOT NULL default '0000-00-00',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `OXLENGTH` double NOT NULL default '0',
  `OXWIDTH` double NOT NULL default '0',
  `OXHEIGHT` double NOT NULL default '0',
  `OXFILE` varchar(128) NOT NULL default '',
  `OXSEARCHKEYS` varchar(255) NOT NULL default '',
  `OXTEMPLATE` varchar(128) NOT NULL default '',
  `OXQUESTIONEMAIL` varchar(255) NOT NULL default '',
  `OXISSEARCH` tinyint(1) NOT NULL default '1',
  `OXFOLDER` char(32) NOT NULL default '',
  `OXSUBCLASS` char(32) NOT NULL default '',
  `OXSTORNO` tinyint(1) NOT NULL default '0',
  `OXORDERSHOPID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXISBUNDLE` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`OXID`),
  KEY `OXORDERID` (`OXORDERID`),
  KEY `OXARTID` (`OXARTID`),
  KEY `OXARTNUM` (`OXARTNUM`)
) ENGINE=InnoDB;

#
# Table structure for table `oxorderfiles`
#

DROP TABLE IF EXISTS `oxorderfiles`;

CREATE TABLE IF NOT EXISTS `oxorderfiles` (
  `OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `OXORDERID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `OXFILENAME` varchar(128) NOT NULL,
  `OXFILEID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `OXORDERARTICLEID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `OXFIRSTDOWNLOAD` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `OXLASTDOWNLOAD` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `OXDOWNLOADCOUNT` int(10) unsigned NOT NULL,
  `OXMAXDOWNLOADCOUNT` int(10) unsigned NOT NULL,
  `OXDOWNLOADEXPIRATIONTIME` int(10) unsigned NOT NULL,
  `OXLINKEXPIRATIONTIME` int(10) unsigned NOT NULL,
  `OXRESETCOUNT` int(10) unsigned NOT NULL,
  `OXVALIDUNTIL` datetime NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (`OXID`),
  KEY `OXORDERID` (`OXORDERID`),
  KEY `OXFILEID` (`OXFILEID`),
  KEY `OXORDERARTICLEID` (`OXORDERARTICLEID`)
) ENGINE=InnoDB;

#
# Table structure for table `oxpayments`
#

DROP TABLE IF EXISTS `oxpayments`;

CREATE TABLE `oxpayments` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXACTIVE` tinyint(1) NOT NULL default '1',
  `OXDESC` varchar(128) NOT NULL default '',
  `OXADDSUM` double NOT NULL default '0',
  `OXADDSUMTYPE` enum('abs','%') NOT NULL default 'abs',
  `OXADDSUMRULES` int(11) NOT NULL default '0',
  `OXFROMBONI` int(11) NOT NULL default '0',
  `OXFROMAMOUNT` double NOT NULL default '0',
  `OXTOAMOUNT` double NOT NULL default '0',
  `OXVALDESC` text NOT NULL,
  `OXCHECKED` tinyint(1) NOT NULL default '0',
  `OXDESC_1` varchar(128) NOT NULL default '',
  `OXVALDESC_1` text NOT NULL,
  `OXDESC_2` varchar(128) NOT NULL default '',
  `OXVALDESC_2` text NOT NULL,
  `OXDESC_3` varchar(128) NOT NULL default '',
  `OXVALDESC_3` text NOT NULL,
  `OXLONGDESC` text NOT NULL default '',
  `OXLONGDESC_1` text NOT NULL default '',
  `OXLONGDESC_2` text NOT NULL default '',
  `OXLONGDESC_3` text NOT NULL default '',
  `OXSORT` int(5) NOT NULL default 0,
  `OXTSPAYMENTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXACTIVE` (`OXACTIVE`)
) ENGINE=MyISAM;

#
# Data for table `oxpayments`
#
INSERT INTO `oxpayments` (`OXID`, `OXACTIVE`, `OXDESC`, `OXADDSUM`, `OXADDSUMTYPE`, `OXADDSUMRULES`, `OXFROMBONI`, `OXFROMAMOUNT`, `OXTOAMOUNT`, `OXVALDESC`, `OXCHECKED`, `OXDESC_1`, `OXVALDESC_1`, `OXDESC_2`, `OXVALDESC_2`, `OXDESC_3`, `OXVALDESC_3`, `OXLONGDESC`, `OXLONGDESC_1`, `OXLONGDESC_2`, `OXLONGDESC_3`, `OXSORT`, `OXTSPAYMENTID`) VALUES
('oxidcashondel', 1, 'Nachnahme', 7.5, 'abs', 0, 0, 0, 1000000, '', 1, 'COD (Cash on Delivery)', '', '', '', '', '', '', '', '', '', 0, ''),
('oxidcreditcard', 1, 'Kreditkarte', 20.9, 'abs', 0, 500, 0, 1000000, 'kktype__@@kknumber__@@kkmonth__@@kkyear__@@kkname__@@kkpruef__@@', 1, 'Credit Card', 'kktype__@@kknumber__@@kkmonth__@@kkyear__@@kkname__@@kkpruef__@@', '', '', '', '', 'Die Belastung Ihrer Kreditkarte erfolgt mit dem Abschluss der Bestellung.', 'Your Credit Card will be charged when you submit the order.', '', '', 0, ''),
('oxiddebitnote', 1, 'Bankeinzug/Lastschrift', 0, 'abs', 0, 0, 0, 1000000, 'lsbankname__@@lsblz__@@lsktonr__@@lsktoinhaber__@@', 0, 'Direct Debit', 'lsbankname__@@lsblz__@@lsktonr__@@lsktoinhaber__@@', '', '', '', '', 'Die Belastung Ihres Kontos erfolgt mit dem Versand der Ware.', 'Your bank account will be charged when the order is shipped.', '', '', 0, ''),
('oxidpayadvance', 1, 'Vorauskasse', 0, 'abs', 0, 0, 0, 1000000, '', 1, 'Cash in advance', '', '', '', '', '', '', '', '', '', 0, ''),
('oxidinvoice', 1, 'Rechnung', 0, 'abs', 0, 800, 0, 1000000, '', 0, 'Invoice', '', '', '', '', '', '', '', '', '', 0, ''),
('oxempty', 1, 'Empty', 0, 'abs', 0, 0, 0, 0, '', 0, 'Empty', '', '', '', '', '', 'for other countries', 'An example. Maybe for use with other countries', '', '', 0, '');

#
# Table structure for table `oxprice2article`
#

DROP TABLE IF EXISTS `oxprice2article`;

CREATE TABLE `oxprice2article` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXARTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXADDABS` double NOT NULL default '0',
  `OXADDPERC` double NOT NULL default '0',
  `OXAMOUNT` double NOT NULL default '0',
  `OXAMOUNTTO` double NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXSHOPID` (`OXSHOPID`),
 KEY `OXARTID` (`OXARTID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxpricealarm`
#

DROP TABLE IF EXISTS `oxpricealarm`;

CREATE TABLE `oxpricealarm` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXEMAIL` varchar(128) NOT NULL default '',
  `OXARTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXPRICE` double NOT NULL default '0',
  `OXCURRENCY` varchar(32) NOT NULL default '',
  `OXLANG` INT(2) NOT NULL default 0,
  `OXINSERT` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXSENDED` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (`OXID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxratings`
#

DROP TABLE IF EXISTS `oxratings`;

CREATE TABLE `oxratings` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTYPE` enum('oxarticle','oxrecommlist') NOT NULL,
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXRATING` int(1) NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `oxobjectsearch` (`OXTYPE`,`OXOBJECTID`)
) ENGINE=MyISAM;
#
# Table structure for table `oxrecommlists`
#

DROP TABLE IF EXISTS `oxrecommlists`;

CREATE TABLE `oxrecommlists` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXAUTHOR` varchar(255) NOT NULL default '',
  `OXTITLE` varchar(255) NOT NULL default '',
  `OXDESC` text NOT NULL,
  `OXRATINGCNT` int(11) NOT NULL default '0',
  `OXRATING` double NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxremark`
#

DROP TABLE IF EXISTS `oxremark`;

CREATE TABLE `oxremark` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXPARENTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTYPE` enum('o','r','n','c') NOT NULL default 'r',
  `OXHEADER` varchar(255) NOT NULL default '',
  `OXTEXT` text NOT NULL,
  `OXCREATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXPARENTID` (`OXPARENTID`),
  KEY `OXTYPE` (`OXTYPE`)
) ENGINE=MyISAM;

#
# Table structure for table `oxreviews`
#

DROP TABLE IF EXISTS `oxreviews`;

CREATE TABLE `oxreviews` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXACTIVE` tinyint(1) NOT NULL default '0',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXTYPE` enum('oxarticle','oxrecommlist') NOT NULL,
  `OXTEXT` text NOT NULL,
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXCREATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXLANG` tinyint( 3 ) NOT NULL DEFAULT '0',
  `OXRATING` int(1) NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `oxobjectsearch` (`OXTYPE`,`OXOBJECTID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxselectlist`
#

DROP TABLE IF EXISTS `oxselectlist`;

CREATE TABLE `oxselectlist` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTITLE` varchar(254) NOT NULL default '',
  `OXIDENT` varchar(255) NOT NULL default '',
  `OXVALDESC` text NOT NULL,
  `OXTITLE_1` varchar(255) NOT NULL default '',
  `OXVALDESC_1` text NOT NULL,
  `OXTITLE_2` varchar(255) NOT NULL default '',
  `OXVALDESC_2` text NOT NULL,
  `OXTITLE_3` varchar(255) NOT NULL default '',
  `OXVALDESC_3` text NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM;

#
# Table structure for table `oxseo`
# Created 2008.04.16
#

DROP TABLE IF EXISTS `oxseo`;

CREATE TABLE `oxseo` (
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXIDENT`    char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSHOPID`   char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXLANG`     int(2) NOT NULL default 0,
  `OXSTDURL`   varchar(2048) NOT NULL,
  `OXSEOURL`   varchar(2048) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `OXTYPE`     enum('static', 'oxarticle', 'oxcategory', 'oxvendor', 'oxcontent', 'dynamic', 'oxmanufacturer') NOT NULL,
  `OXFIXED`    TINYINT(1) NOT NULL default 0,
  `OXEXPIRED` tinyint(1) NOT NULL default '0',
  `OXPARAMS` char(32) NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
   PRIMARY KEY (`OXIDENT`, `OXSHOPID`, `OXLANG`),
   UNIQUE KEY search (`OXTYPE`, `OXOBJECTID`, `OXSHOPID`, `OXLANG`,`OXPARAMS`),
   KEY `OXOBJECTID` (`OXLANG`,`OXOBJECTID`,`OXSHOPID`),
   KEY `SEARCHSTD` (OXSTDURL(100),`OXSHOPID`),
   KEY `SEARCHSEO` (OXSEOURL(100))
) ENGINE=InnoDB;

#
# Data for table `oxseo`
#
INSERT INTO `oxseo` (`OXOBJECTID`, `OXIDENT`, `OXSHOPID`, `OXLANG`, `OXSTDURL`, `OXSEOURL`, `OXTYPE`, `OXFIXED`, `OXEXPIRED`, `OXPARAMS`) VALUES
('c855234180a3b4056b496120d229ea68', '023abc17c853f9bccc201c5afd549a92', 'oxbaseshop', 1, 'index.php?cl=account_wishlist', 'en/my-gift-registry/', 'static', 0, 0, ''),
('e5340b054530ea779fb1802e93c8183e', '02b4c1e4049b1baffba090c95a7edbf7', 'oxbaseshop', 0, 'index.php?cl=invite', 'Laden-Sie-Ihre-Freunde/', 'static', 0, 0, ''),
('2e17757c0aaf8ed9ef2ba30317fa1faf', '0469752d03d80da379a679aaef4c0546', 'oxbaseshop', 1, 'index.php?cl=suggest', 'en/recommend/', 'static', 0, 0, ''),
('057ef382f23bdbb84991d049af2baba9', '063c82502d9dd528ce2cc40ceb76ade7', 'oxbaseshop', 1, 'index.php?cl=compare', 'en/my-product-comparison/', 'static', 0, 0, ''),
('67c5bcf75ee346bd9566bce6c8', '0d2f22b49c64eaa138d717ec752e3be3', 'oxbaseshop', 0, 'index.php?cl=credits&amp;oxcid=67c5bcf75ee346bd9566bce6c8', 'Credits/', 'oxcontent', 1, 0, ''),
('7bc8a506bbca225a2f95b6eac66020bf', '1368f5e45468ca1e1c7c84f174785c35', 'oxbaseshop', 1, 'index.php?cl=account_noticelist', 'en/my-wish-list/', 'static', 0, 0, ''),
('c855234180a3b4056b496120d229ea68', '1f30ae9b1c78b7dc89d3e5fe9eb0de59', 'oxbaseshop', 0, 'index.php?cl=account_wishlist', 'mein-wunschzettel/', 'static', 0, 0, ''),
('e56acdd347b0516f68a0afdd811e2382', '3a41965fb36fb45df7f4f9a4377f6364', 'oxbaseshop', 1, 'index.php?cl=newsletter', 'en/newsletter/', 'static', 0, 0, ''),
('ab459c0dc911137e9cc024d9fba5a68b', '3bdd64bd8073d044d8fd60334ed9b725', 'oxbaseshop', 1, 'index.php?cl=start', 'en/home/', 'static', 0, 0, ''),
('efaf9266cf7de3a8c84cea167285eb83', '4a70a4cd11d63fdce96fbe4ba8e714e6', 'oxbaseshop', 1, 'index.php?cnid=oxmore&amp;cl=alist', 'en/more/', 'static', 0, 0, ''),
('efaf9266cf7de3a8c84cea167285eb83', '4d3d4d70b09b5d2bd992991361374c67', 'oxbaseshop', 0, 'index.php?cnid=oxmore&amp;cl=alist', 'mehr/', 'static', 0, 0, ''),
('18f64cbbc296a32fb84b3bbe31dfed09', '510fef34e5d9b86f6a77af949d15950e', 'oxbaseshop', 1, 'index.php?cl=account', 'en/my-account/', 'static', 0, 0, ''),
('ace1e168a1e8183a2aa79c2243171a29', '5668048844927ca2767140c219e04efc', 'oxbaseshop', 1, 'index.php?cl=account_user', 'en/my-address/', 'static', 0, 0, ''),
('0563ce7f6a400737ce0e1c2b2c733e49', '5cc081514a72b0ce3d7eec4fb1e6f1dd', 'oxbaseshop', 1, 'index.php?cl=forgotpwd', 'en/forgot-password/', 'static', 0, 0, ''),
('f6bd7f77caae70afad276584caa6450a', '5d752e9e8302eeb21df24d1aee573234', 'oxbaseshop', 0, 'index.php?cl=wishlist', 'wunschzettel/', 'static', 0, 0, ''),
('6d9b5b3ee58bca1bd7be188f71e80ef2', '5eb126725ba500e6bbf1aecaa876dc48', 'oxbaseshop', 1, 'index.php?cl=review', 'en/product-review/', 'static', 0, 0, ''),
('57cb6b2fafc870810cd48b8e1d28cf91', '63794023f46c56d329e2ee6a1462e8c2', 'oxbaseshop', 0, 'index.php?cl=tags', 'stichworte/', 'static', 0, 0, ''),
('56ebd65f4a7cc25de1dc1f7cf15591fb', '637daadf1eaad2b9641333c2b1572883', 'oxbaseshop', 0, 'index.php?cl=news', 'news/', 'static', 0, 0, ''),
('f560b18b547bca23752a154b45120980', '6b30b01fe01b80894efc0f29610e5215', 'oxbaseshop', 0, 'index.php?cl=account_password', 'mein-passwort/', 'static', 0, 0, ''),
('f560b18b547bca23752a154b45120980', '6c890ac1255a99f8d1eb46f1193843d6', 'oxbaseshop', 1, 'index.php?cl=account_password', 'en/my-password/', 'static', 0, 0, ''),
('04abcb465a8d3a4441df4c480838d23d', '7685924d3f3fb7e9bda421c57f665af4', 'oxbaseshop', 1, 'index.php?cl=contact', 'en/contact/', 'static', 0, 0, ''),
('d12b7badee1037e7c1a5a7a245a14e11', '7c8aa72aa16b7d4a859b22d8b8328412', 'oxbaseshop', 0, 'index.php?cl=guestbook', 'gaestebuch/', 'static', 0, 0, ''),
('2e17757c0aaf8ed9ef2ba30317fa1faf', '82dd672d68d8f6c943f98ccdaecda691', 'oxbaseshop', 0, 'index.php?cl=suggest', 'empfehlen/', 'static', 0, 0, ''),
('18f64cbbc296a32fb84b3bbe31dfed09', '89c5e6bf1b5441599c4815281debe8df', 'oxbaseshop', 0, 'index.php?cl=account', 'mein-konto/', 'static', 0, 0, ''),
('c71c50f5443d85b37a013420857e83e7', '8afe769d3de8b5db0a872b23f959dd36', 'oxbaseshop', 1, 'index.php?cl=download', 'en/download/', 'static', 0, 0, ''),
('05c0f9a36dc4eaf3df528f0da18664d8', '8e7ebaebb0a810576453082e1f8f2fa3', 'oxbaseshop', 1, 'index.php?cl=account_recommlist', 'en/my-listmania-list/', 'static', 0, 0, ''),
('61c5d6965b480012aabb3a6701254b75', '9372b6be2d98021fb93302a6a34bfc8c', 'oxbaseshop', 1, 'index.php?cl=recommlist', 'en/Recommendation-Lists/', 'static', 0, 0, ''),
('343c043546b3d653647e75d2e246ce94', '9508bb0d70121d49e4d4554c5b1af81d', 'oxbaseshop', 0, 'index.php?cl=links', 'links/', 'static', 0, 0, ''),
('610f7fc243c7409cb5448b30029431fe', '9ff5c21cbc84dbfe815013236e132baf', 'oxbaseshop', 1, 'index.php?cl=account_order', 'en/order-history/', 'static', 0, 0, ''),
('67c5bcf75ee346bd9566bce6c8', 'a150a7b6945213daa7f138e1a042cbb4', 'oxbaseshop', 1, 'index.php?cl=credits&amp;oxcid=67c5bcf75ee346bd9566bce6c8', 'en/Credits/', 'oxcontent', 1, 0, ''),
('98964bf04c7edae2d658c5f3b3233ca1', 'a1b330b85c6f51fd9b50b1eb19707d84', 'oxbaseshop', 1, 'index.php?cl=register', 'en/open-account/', 'static', 0, 0, ''),
('7bc8a506bbca225a2f95b6eac66020bf', 'a24b03f1a3f73c325a7647e6039e2359', 'oxbaseshop', 0, 'index.php?cl=account_noticelist', 'mein-merkzettel/', 'static', 0, 0, ''),
('61c5d6965b480012aabb3a6701254b75', 'a4e5995182ade3cf039800c0ec2d512d', 'oxbaseshop', 0, 'index.php?cl=recommlist', 'Empfehlungslisten/', 'static', 0, 0, ''),
('e5340b054530ea779fb1802e93c8183e', 'a6b775aec57d06b46a958efbafdc7875', 'oxbaseshop', 1, 'index.php?cl=invite', 'en/Invite-your-friends/', 'static', 0, 0, ''),
('ace1e168a1e8183a2aa79c2243171a29', 'a7d5ab5a4e87693998c5aec066dda6e6', 'oxbaseshop', 0, 'index.php?cl=account_user', 'meine-adressen/', 'static', 0, 0, ''),
('0563ce7f6a400737ce0e1c2b2c733e49', 'a9afb500184c584fb5ab2ad9b4162e96', 'oxbaseshop', 0, 'index.php?cl=forgotpwd', 'passwort-vergessen/', 'static', 0, 0, ''),
('98964bf04c7edae2d658c5f3b3233ca1', 'acddcfef9c25bcae3b96eb00669541ff', 'oxbaseshop', 0, 'index.php?cl=register', 'konto-eroeffnen/', 'static', 0, 0, ''),
('f6bd7f77caae70afad276584caa6450a', 'b93b703d313e89662773cffaab750f1d', 'oxbaseshop', 1, 'index.php?cl=wishlist', 'en/gift-registry/', 'static', 0, 0, ''),
('05c0f9a36dc4eaf3df528f0da18664d8', 'baa3b653a618696b06c70a6dda95ebde', 'oxbaseshop', 0, 'index.php?cl=account_recommlist', 'meine-lieblingslisten/', 'static', 0, 0, ''),
('c71c50f5443d85b37a013420857e83e7', 'c552cb8718cde5cb792e181f78f5fde1', 'oxbaseshop', 0, 'index.php?cl=download', 'download/', 'static', 0, 0, ''),
('882acc7454f973844d4917515181dcba', 'c74bbaf961498de897ba7eb98fea8274', 'oxbaseshop', 1, 'index.php?cl=account_downloads', 'en/my-downloads/', 'static', 0, 0, ''),
('6d9b5b3ee58bca1bd7be188f71e80ef2', 'cc28156a4f728c1b33e7e02fd846628e', 'oxbaseshop', 0, 'index.php?cl=review', 'bewertungen/', 'static', 0, 0, ''),
('ab459c0dc911137e9cc024d9fba5a68b', 'ccca27059506a916fb64d673a5dd676b', 'oxbaseshop', 0, 'index.php?cl=start', 'startseite/', 'static', 0, 0, ''),
('57cb6b2fafc870810cd48b8e1d28cf91', 'da6c5523f7c91d108cadb0be7757c27f', 'oxbaseshop', 1, 'index.php?cl=tags', 'en/tags/', 'static', 0, 0, ''),
('d12b7badee1037e7c1a5a7a245a14e11', 'ded4f3786c6f4d6d14e3034834ebdcaf', 'oxbaseshop', 1, 'index.php?cl=guestbook', 'en/guestbook/', 'static', 0, 0, ''),
('057ef382f23bdbb84991d049af2baba9', 'e0dd29a75947539da6b1924d31c9849f', 'oxbaseshop', 0, 'index.php?cl=compare', 'mein-produktvergleich/', 'static', 0, 0, ''),
('56ebd65f4a7cc25de1dc1f7cf15591fb', 'e5a8de0e49e91c5728eadfcb233bdbd1', 'oxbaseshop', 1, 'index.php?cl=news', 'en/news/', 'static', 0, 0, ''),
('e56acdd347b0516f68a0afdd811e2382', 'e604233c5a2804d21ec0252a445e93d3', 'oxbaseshop', 0, 'index.php?cl=newsletter', 'newsletter/', 'static', 0, 0, ''),
('38efc02f0f6b6a6d54cfef1fcdb99d70', 'e6331d115f5323610c639ef2f0369f8a', 'oxbaseshop', 0, 'index.php?cl=basket', 'warenkorb/', 'static', 0, 0, ''),
('610f7fc243c7409cb5448b30029431fe', 'eb692d07ec8608d943db0a3bca29c4ce', 'oxbaseshop', 0, 'index.php?cl=account_order', 'bestellhistorie/', 'static', 0, 0, ''),
('38efc02f0f6b6a6d54cfef1fcdb99d70', 'ecaf0240f9f46bbee5ffc6dd73d0b7f0', 'oxbaseshop', 1, 'index.php?cl=basket', 'en/cart/', 'static', 0, 0, ''),
('882acc7454f973844d4917515181dcba', 'f1c7ccb53fc8d6f239b39d2fc2b76829', 'oxbaseshop', 0, 'index.php?cl=account_downloads', 'de/my-downloads/', 'static', 0, 0, ''),
('343c043546b3d653647e75d2e246ce94', 'f80ace8f87e1d7f446ab1fa58f275f4c', 'oxbaseshop', 1, 'index.php?cl=links', 'en/links/', 'static', 0, 0, ''),
('943173edecf6d6870a0f357b8ac84d32', 'f8335c84c1daa5b13657124f45c0e08b', 'oxbaseshop', 0, 'index.php?cl=alist&amp;cnid=943173edecf6d6870a0f357b8ac84d32', 'Wakeboarding/', 'oxcategory', 0, 0, ''),
('04abcb465a8d3a4441df4c480838d23d', 'f9d1a02ab749dc360c4e21f21de1beaf', 'oxbaseshop', 0, 'index.php?cl=contact', 'kontakt/', 'static', 0, 0, ''),
('0f4270b89fbef1481958381410a0dbca', 'fad614c0f4922228623ae0696b55481f', 'oxbaseshop', 1, 'index.php?cl=alist&amp;cnid=0f4270b89fbef1481958381410a0dbca', 'en/Wakeboarding/Wakeboards/', 'oxcategory', 1, 0, ''),
('965acb9a653478a186e55468c6df7406', 'd50c753d406338d92f52fe55de1e98dd', 'oxbaseshop', 1, 'index.php?cl=clearcookies', 'en/cookies/', 'static', 0, 0, ''),
('965acb9a653478a186e55468c6df7406', '295d6617dc22b6d8186667ecd6e3ae87', 'oxbaseshop', 0, 'index.php?cl=clearcookies', 'cookies/', 'static', 0, 0, '');

#
# Table structure for table `oxseohistory`
# For tracking old SEO urls
# Created 2008-05-21
#

DROP TABLE IF EXISTS `oxseohistory`;

CREATE TABLE `oxseohistory` (
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXIDENT` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXLANG` int(2) NOT NULL default '0',
  `OXHITS` bigint(20) NOT NULL default '0',
  `OXINSERT` timestamp NULL default NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXIDENT`,`OXSHOPID`,`OXLANG`),
  KEY `search` (`OXOBJECTID`,`OXSHOPID`,`OXLANG`)
) ENGINE=InnoDB;

#
# Table structure for table `oxseologs`
# For tracking untranslatable to SEO format non SEO urls
# Created 2008-10-21
#

DROP TABLE IF EXISTS `oxseologs`;

CREATE TABLE IF NOT EXISTS `oxseologs` (
  `OXSTDURL` text NOT NULL,
  `OXIDENT` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXLANG` int(11) NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXIDENT`,`OXSHOPID`,`OXLANG`)
) ENGINE=InnoDB;

#
# Table structure for table `oxshops`
#

DROP TABLE IF EXISTS `oxshops`;

CREATE TABLE `oxshops` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXACTIVE` tinyint(1) NOT NULL default '1',
  `OXPRODUCTIVE` tinyint(1) NOT NULL default '0',
  `OXDEFCURRENCY` char(32) NOT NULL default '',
  `OXDEFLANGUAGE` int(11) NOT NULL default '0',
  `OXNAME` varchar(255) NOT NULL default '',
  `OXTITLEPREFIX` varchar(255) NOT NULL default '',
  `OXTITLEPREFIX_1` varchar(255) NOT NULL default '',
  `OXTITLEPREFIX_2` varchar(255) NOT NULL default '',
  `OXTITLEPREFIX_3` varchar(255) NOT NULL default '',
  `OXTITLESUFFIX` varchar(255) NOT NULL default '',
  `OXTITLESUFFIX_1` varchar(255) NOT NULL default '',
  `OXTITLESUFFIX_2` varchar(255) NOT NULL default '',
  `OXTITLESUFFIX_3` varchar(255) NOT NULL default '',
  `OXSTARTTITLE` varchar(255) NOT NULL default '',
  `OXSTARTTITLE_1` varchar(255) NOT NULL default '',
  `OXSTARTTITLE_2` varchar(255) NOT NULL default '',
  `OXSTARTTITLE_3` varchar(255) NOT NULL default '',
  `OXINFOEMAIL` varchar(255) NOT NULL default '',
  `OXORDEREMAIL` varchar(255) NOT NULL default '',
  `OXOWNEREMAIL` varchar(255) NOT NULL default '',
  `OXORDERSUBJECT` varchar(255) NOT NULL default '',
  `OXREGISTERSUBJECT` varchar(255) NOT NULL default '',
  `OXFORGOTPWDSUBJECT` varchar(255) NOT NULL default '',
  `OXSENDEDNOWSUBJECT` varchar(255) NOT NULL default '',
  `OXORDERSUBJECT_1` varchar(255) NOT NULL default '',
  `OXREGISTERSUBJECT_1` varchar(255) NOT NULL default '',
  `OXFORGOTPWDSUBJECT_1` varchar(255) NOT NULL default '',
  `OXSENDEDNOWSUBJECT_1` varchar(255) NOT NULL default '',
  `OXORDERSUBJECT_2` varchar(255) NOT NULL default '',
  `OXREGISTERSUBJECT_2` varchar(255) NOT NULL default '',
  `OXFORGOTPWDSUBJECT_2` varchar(255) NOT NULL default '',
  `OXSENDEDNOWSUBJECT_2` varchar(255) NOT NULL default '',
  `OXORDERSUBJECT_3` varchar(255) NOT NULL default '',
  `OXREGISTERSUBJECT_3` varchar(255) NOT NULL default '',
  `OXFORGOTPWDSUBJECT_3` varchar(255) NOT NULL default '',
  `OXSENDEDNOWSUBJECT_3` varchar(255) NOT NULL default '',
  `OXSMTP` varchar(255) NOT NULL default '',
  `OXSMTPUSER` varchar(128) NOT NULL default '',
  `OXSMTPPWD` varchar(128) NOT NULL default '',
  `OXCOMPANY` varchar(128) NOT NULL default '',
  `OXSTREET` varchar(255) NOT NULL default '',
  `OXZIP` varchar(255) NOT NULL default '',
  `OXCITY` varchar(255) NOT NULL default '',
  `OXCOUNTRY` varchar(255) NOT NULL default '',
  `OXBANKNAME` varchar(255) NOT NULL default '',
  `OXBANKNUMBER` varchar(255) NOT NULL default '',
  `OXBANKCODE` varchar(255) NOT NULL default '',
  `OXVATNUMBER` varchar(255) NOT NULL default '',
  `OXBICCODE` varchar(255) NOT NULL default '',
  `OXIBANNUMBER` varchar(255) NOT NULL default '',
  `OXFNAME` varchar(255) NOT NULL default '',
  `OXLNAME` varchar(255) NOT NULL default '',
  `OXTELEFON` varchar(255) NOT NULL default '',
  `OXTELEFAX` varchar(255) NOT NULL default '',
  `OXURL` varchar(255) NOT NULL default '',
  `OXDEFCAT` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXHRBNR` varchar(64) NOT NULL default '',
  `OXCOURT` varchar(128) NOT NULL default '',
  `OXADBUTLERID` varchar(64) NOT NULL default '',
  `OXAFFILINETID` varchar(64) NOT NULL default '',
  `OXSUPERCLICKSID` varchar(64) NOT NULL default '',
  `OXAFFILIWELTID` varchar(64) NOT NULL default '',
  `OXAFFILI24ID` varchar(64) NOT NULL default '',
  `OXEDITION` CHAR( 2 ) NOT NULL,
  `OXVERSION` CHAR( 16 ) NOT NULL,
  `OXSEOACTIVE` tinyint(1) NOT NULL DEFAULT '1',
  `OXSEOACTIVE_1` tinyint(1) NOT NULL DEFAULT '1',
  `OXSEOACTIVE_2` tinyint(1) NOT NULL DEFAULT '1',
  `OXSEOACTIVE_3` tinyint(1) NOT NULL DEFAULT '1',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXACTIVE` (`OXACTIVE`)
) ENGINE=MyISAM;

#
# Data for table `oxshops`
#
INSERT INTO `oxshops` (`OXID`, `OXACTIVE`, `OXPRODUCTIVE`, `OXDEFCURRENCY`, `OXDEFLANGUAGE`, `OXNAME`, `OXTITLEPREFIX`, `OXTITLEPREFIX_1`, `OXTITLEPREFIX_2`, `OXTITLEPREFIX_3`, `OXTITLESUFFIX`, `OXTITLESUFFIX_1`, `OXTITLESUFFIX_2`, `OXTITLESUFFIX_3`, `OXSTARTTITLE`, `OXSTARTTITLE_1`, `OXSTARTTITLE_2`, `OXSTARTTITLE_3`, `OXINFOEMAIL`, `OXORDEREMAIL`, `OXOWNEREMAIL`, `OXORDERSUBJECT`, `OXREGISTERSUBJECT`, `OXFORGOTPWDSUBJECT`, `OXSENDEDNOWSUBJECT`, `OXORDERSUBJECT_1`, `OXREGISTERSUBJECT_1`, `OXFORGOTPWDSUBJECT_1`, `OXSENDEDNOWSUBJECT_1`, `OXORDERSUBJECT_2`, `OXREGISTERSUBJECT_2`, `OXFORGOTPWDSUBJECT_2`, `OXSENDEDNOWSUBJECT_2`, `OXORDERSUBJECT_3`, `OXREGISTERSUBJECT_3`, `OXFORGOTPWDSUBJECT_3`, `OXSENDEDNOWSUBJECT_3`, `OXSMTP`, `OXSMTPUSER`, `OXSMTPPWD`, `OXCOMPANY`, `OXSTREET`, `OXZIP`, `OXCITY`, `OXCOUNTRY`, `OXBANKNAME`, `OXBANKNUMBER`, `OXBANKCODE`, `OXVATNUMBER`, `OXBICCODE`, `OXIBANNUMBER`, `OXFNAME`, `OXLNAME`, `OXTELEFON`, `OXTELEFAX`, `OXURL`, `OXDEFCAT`, `OXHRBNR`, `OXCOURT`, `OXADBUTLERID`, `OXAFFILINETID`, `OXSUPERCLICKSID`, `OXAFFILIWELTID`, `OXAFFILI24ID`, `OXEDITION`, `OXVERSION`, `OXSEOACTIVE`, `OXSEOACTIVE_1`, `OXSEOACTIVE_2`, `OXSEOACTIVE_3`) VALUES
('oxbaseshop', 1, 0, '', 0, 'OXID eShop 4', 'OXID eShop 4', 'OXID eShop 4', '', '', 'online kaufen', 'purchase online', '', '', 'Der Onlineshop', 'Online Shop', '', '', 'info@myoxideshop.com', 'reply@myoxideshop.com', 'order@myoxideshop.com', 'Ihre Bestellung bei OXID eSales', 'Vielen Dank für Ihre Registrierung im OXID eShop', 'Ihr Passwort im OXID eShop', 'Ihre OXID eSales Bestellung wurde versandt', 'Your order at OXID eShop', 'Thank you for your registration at OXID eShop', 'Your OXID eShop password', 'Your OXID eSales Order has been shipped', '', '', '', '', '', '', '', '', '', '', '', 'Your Company Name', '2425 Maple Street', '9041', 'Any City, CA', 'United States', 'Bank of America', '1234567890', '900 1234567', '', '', '', 'John', 'Doe', '217-8918712', '217-8918713', 'www.myoxideshop.com', '', '', '', '', '', '', '', '', 'CE', '4.7.4', 1, 1, 0, 0);

#
# Table structure for table `oxstates`
# for storing extended file urls
# created 2010-01-06
#

DROP TABLE IF EXISTS `oxstates`;

CREATE TABLE `oxstates` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXCOUNTRYID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTITLE` char(128) NOT NULL default '',
  `OXISOALPHA2` char(2) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTITLE_1` char(128) NOT NULL default '',
  `OXTITLE_2` char(128) NOT NULL default '',
  `OXTITLE_3` char(128) NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY(`OXCOUNTRYID`)

) ENGINE = MYISAM ;

#
# Data for table `oxstates`
#
INSERT INTO `oxstates` (`OXID`, `OXCOUNTRYID`, `OXTITLE`, `OXISOALPHA2`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`) VALUES
('MB', '8f241f11095649d18.02676059', 'Manitoba', 'MB', 'Manitoba', '', ''),
('NB', '8f241f11095649d18.02676059', 'Neubraunschweig', 'NB', 'New Brunswick', '', ''),
('NF', '8f241f11095649d18.02676059', 'Neufundland und Labrador', 'NF', 'Newfoundland and Labrador', '', ''),
('NT', '8f241f11095649d18.02676059', 'Nordwest-Territorien', 'NT', 'Northwest Territories', '', ''),
('NS', '8f241f11095649d18.02676059', 'Nova Scotia', 'NS', 'Nova Scotia', '', ''),
('NU', '8f241f11095649d18.02676059', 'Nunavut', 'NU', 'Nunavut', '', ''),
('ON', '8f241f11095649d18.02676059', 'Ontario', 'ON', 'Ontario', '', ''),
('PE', '8f241f11095649d18.02676059', 'Prince Edward Island', 'PE', 'Prince Edward Island', '', ''),
('QC', '8f241f11095649d18.02676059', 'Quebec', 'QC', 'Quebec', '', ''),
('SK', '8f241f11095649d18.02676059', 'Saskatchewan', 'SK', 'Saskatchewan', '', ''),
('YK', '8f241f11095649d18.02676059', 'Yukon', 'YK', 'Yukon', '', ''),
('AL', '8f241f11096877ac0.98748826', 'Alabama', 'AL', 'Alabama', '', ''),
('AK', '8f241f11096877ac0.98748826', 'Alaska', 'AK', 'Alaska', '', ''),
('AS', '8f241f11096877ac0.98748826', 'Amerikanisch-Samoa', 'AS', 'American Samoa', '', ''),
('AZ', '8f241f11096877ac0.98748826', 'Arizona', 'AZ', 'Arizona', '', ''),
('AR', '8f241f11096877ac0.98748826', 'Arkansas', 'AR', 'Arkansas', '', ''),
('CA', '8f241f11096877ac0.98748826', 'Kalifornien', 'CA', 'California', '', ''),
('CO', '8f241f11096877ac0.98748826', 'Colorado', 'CO', 'Colorado', '', ''),
('CT', '8f241f11096877ac0.98748826', 'Connecticut', 'CT', 'Connecticut', '', ''),
('DE', '8f241f11096877ac0.98748826', 'Delaware', 'DE', 'Delaware', '', ''),
('DC', '8f241f11096877ac0.98748826', 'District of Columbia', 'DC', 'District of Columbia', '', ''),
('FM', '8f241f11096877ac0.98748826', 'Föderierten Staaten von Mikronesien', 'FM', 'Federated States of Micronesia', '', ''),
('FL', '8f241f11096877ac0.98748826', 'Florida', 'FL', 'Florida', '', ''),
('GA', '8f241f11096877ac0.98748826', 'Georgia', 'GA', 'Georgia', '', ''),
('GU', '8f241f11096877ac0.98748826', 'Guam', 'GU', 'Guam', '', ''),
('HI', '8f241f11096877ac0.98748826', 'Hawaii', 'HI', 'Hawaii', '', ''),
('ID', '8f241f11096877ac0.98748826', 'Idaho', 'ID', 'Idaho', '', ''),
('IL', '8f241f11096877ac0.98748826', 'Illinois', 'IL', 'Illinois', '', ''),
('IN', '8f241f11096877ac0.98748826', 'Indiana', 'IN', 'Indiana', '', ''),
('IA', '8f241f11096877ac0.98748826', 'Iowa', 'IA', 'Iowa', '', ''),
('KS', '8f241f11096877ac0.98748826', 'Kansas', 'KS', 'Kansas', '', ''),
('KY', '8f241f11096877ac0.98748826', 'Kentucky', 'KY', 'Kentucky', '', ''),
('LA', '8f241f11096877ac0.98748826', 'Louisiana', 'LA', 'Louisiana', '', ''),
('ME', '8f241f11096877ac0.98748826', 'Maine', 'ME', 'Maine', '', ''),
('MH', '8f241f11096877ac0.98748826', 'Marshallinseln', 'MH', 'Marshall Islands', '', ''),
('MD', '8f241f11096877ac0.98748826', 'Maryland', 'MD', 'Maryland', '', ''),
('MA', '8f241f11096877ac0.98748826', 'Massachusetts', 'MA', 'Massachusetts', '', ''),
('MI', '8f241f11096877ac0.98748826', 'Michigan', 'MI', 'Michigan', '', ''),
('MN', '8f241f11096877ac0.98748826', 'Minnesota', 'MN', 'Minnesota', '', ''),
('MS', '8f241f11096877ac0.98748826', 'Mississippi', 'MS', 'Mississippi', '', ''),
('MO', '8f241f11096877ac0.98748826', 'Missouri', 'MO', 'Missouri', '', ''),
('MT', '8f241f11096877ac0.98748826', 'Montana', 'MT', 'Montana', '', ''),
('NE', '8f241f11096877ac0.98748826', 'Nebraska', 'NE', 'Nebraska', '', ''),
('NV', '8f241f11096877ac0.98748826', 'Nevada', 'NV', 'Nevada', '', ''),
('NH', '8f241f11096877ac0.98748826', 'New Hampshire', 'NH', 'New Hampshire', '', ''),
('NJ', '8f241f11096877ac0.98748826', 'New Jersey', 'NJ', 'New Jersey', '', ''),
('NM', '8f241f11096877ac0.98748826', 'New Mexico', 'NM', 'Neumexiko', '', ''),
('NY', '8f241f11096877ac0.98748826', 'New York', 'NY', 'New York', '', ''),
('NC', '8f241f11096877ac0.98748826', 'North Carolina', 'NC', 'North Carolina', '', ''),
('ND', '8f241f11096877ac0.98748826', 'North Dakota', 'ND', 'North Dakota', '', ''),
('MP', '8f241f11096877ac0.98748826', 'Nördlichen Marianen', 'MP', 'Northern Mariana Islands', '', ''),
('OH', '8f241f11096877ac0.98748826', 'Ohio', 'OH', 'Ohio', '', ''),
('OK', '8f241f11096877ac0.98748826', 'Oklahoma', 'OK', 'Oklahoma', '', ''),
('OR', '8f241f11096877ac0.98748826', 'Oregon', 'OR', 'Oregon', '', ''),
('PW', '8f241f11096877ac0.98748826', 'Palau', 'PW', 'Palau', '', ''),
('PA', '8f241f11096877ac0.98748826', 'Pennsylvania', 'PA', 'Pennsylvania', '', ''),
('PR', '8f241f11096877ac0.98748826', 'Puerto Rico', 'PR', 'Puerto Rico', '', ''),
('RI', '8f241f11096877ac0.98748826', 'Rhode Island', 'RI', 'Rhode Island', '', ''),
('SC', '8f241f11096877ac0.98748826', 'South Carolina', 'SC', 'Südkarolina', '', ''),
('SD', '8f241f11096877ac0.98748826', 'South Dakota', 'SD', 'Süddakota', '', ''),
('TN', '8f241f11096877ac0.98748826', 'Tennessee', 'TN', 'Tennessee', '', ''),
('TX', '8f241f11096877ac0.98748826', 'Texas', 'TX', 'Texas', '', ''),
('UT', '8f241f11096877ac0.98748826', 'Utah', 'UT', 'Utah', '', ''),
('VT', '8f241f11096877ac0.98748826', 'Vermont', 'VT', 'Vermont', '', ''),
('VI', '8f241f11096877ac0.98748826', 'Jungferninseln', 'VI', 'Virgin Islands', '', ''),
('VA', '8f241f11096877ac0.98748826', 'Virginia', 'VA', 'Virginia', '', ''),
('WA', '8f241f11096877ac0.98748826', 'Washington', 'WA', 'Washington', '', ''),
('WV', '8f241f11096877ac0.98748826', 'West Virginia', 'WV', 'West Virginia', '', ''),
('WI', '8f241f11096877ac0.98748826', 'Wisconsin', 'WI', 'Wisconsin', '', ''),
('WY', '8f241f11096877ac0.98748826', 'Wyoming', 'WY', 'Wyoming', '', ''),
('AA', '8f241f11096877ac0.98748826', 'Armed Forces Americas', 'AA', 'Armed Forces Americas', '', ''),
('AE', '8f241f11096877ac0.98748826', 'Armed Forces', 'AE', 'Armed Forces', '', ''),
('AP', '8f241f11096877ac0.98748826', 'Armed Forces Pacific', 'AP', 'Armed Forces Pacific', '', '');

#
# Table structure for table `oxstatistics`
#

DROP TABLE IF EXISTS `oxstatistics`;

CREATE TABLE `oxstatistics` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTITLE` char(32) NOT NULL default '',
  `OXVALUE` text NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`)
) ENGINE=InnoDB;

#
# Table structure for table `oxtplblocks`
# for storing blocks for template parts override
# created 2010-10-12
#

DROP TABLE IF EXISTS `oxtplblocks`;

CREATE TABLE `oxtplblocks` (
  `OXID`        char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXACTIVE`    tinyint(1) NOT NULL DEFAULT '1',
  `OXSHOPID`    char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXTEMPLATE`  char(255) character set latin1 collate latin1_general_ci NOT NULL,
  `OXBLOCKNAME` char(128) character set latin1 collate latin1_general_ci NOT NULL,
  `OXPOS`       int  NOT NULL,
  `OXFILE`      char(255) character set latin1 collate latin1_general_ci NOT NULL,
  `OXMODULE`    char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (`OXID`),
  INDEX `search` (`OXACTIVE`, `OXTEMPLATE`, `OXPOS`)
) ENGINE=MyISAM;

#
# Table structure for table `oxuser`
#

DROP TABLE IF EXISTS `oxuser`;

CREATE TABLE `oxuser` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXACTIVE` tinyint(1) NOT NULL default '1',
  `OXRIGHTS` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSHOPID` char( 32 ) NOT NULL default '',
  `OXUSERNAME` varchar(255) NOT NULL default '',
  `OXPASSWORD` varchar(128) NOT NULL default '',
  `OXPASSSALT` char(128) character set latin1 collate latin1_general_ci NOT NULL,
  `OXCUSTNR` int(11) NOT NULL AUTO_INCREMENT,
  `OXUSTID` varchar(255) NOT NULL default '',
  `OXCOMPANY` varchar(255) NOT NULL default '',
  `OXFNAME` varchar(255) NOT NULL default '',
  `OXLNAME` varchar(255) NOT NULL default '',
  `OXSTREET` varchar(255) NOT NULL default '',
  `OXSTREETNR` varchar(16) NOT NULL default '',
  `OXADDINFO` varchar(255) NOT NULL default '',
  `OXCITY` varchar(255) NOT NULL default '',
  `OXCOUNTRYID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXSTATEID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXZIP` varchar(16) NOT NULL default '',
  `OXFON` varchar(128) NOT NULL default '',
  `OXFAX` varchar(128) NOT NULL default '',
  `OXSAL` varchar(128) NOT NULL default '',
  `OXBONI` int(11) NOT NULL default '0',
  `OXCREATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXREGISTER` datetime NOT NULL default '0000-00-00 00:00:00',
  `OXPRIVFON` varchar(64) NOT NULL default '',
  `OXMOBFON` varchar(64) NOT NULL default '',
  `OXBIRTHDATE` date NOT NULL default '0000-00-00',
  `OXURL` varchar(255) NOT NULL default '',
  `OXDISABLEAUTOGRP` tinyint(1) NOT NULL default '0',
  `OXUPDATEKEY` char( 32 ) NOT NULL default '',
  `OXUPDATEEXP` int(11) NOT NULL default '0',
  `OXPOINTS` double NOT NULL default '0',
  `OXFBID` bigint unsigned NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  UNIQUE `OXUSERNAME` (`OXUSERNAME`, `OXSHOPID`),
  KEY `OXPASSWORD` (`OXPASSWORD`),
  KEY `OXCUSTNR` (`OXCUSTNR`),
  KEY `OXACTIVE` (`OXACTIVE`),
  KEY `OXLNAME` (`OXLNAME`)
) ENGINE=MyISAM;

#
# Data for table `oxuser`
#
INSERT INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`, `OXCUSTNR`, `OXUSTID`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`, `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`, `OXPRIVFON`, `OXMOBFON`, `OXBIRTHDATE`, `OXURL`, `OXDISABLEAUTOGRP`, `OXUPDATEKEY`, `OXUPDATEEXP`, `OXPOINTS`, `OXFBID`) VALUES
('oxdefaultadmin', 1, 'malladmin', 'oxbaseshop', 'admin', 'f6fdffe48c908deb0f4c3bd36c032e72', '61646D696E', 1, '', 'Your Company Name', 'John', 'Doe', 'Maple Street', '2425', '', 'Any City', 'a7c40f631fc920687.20179984', '', '9041', '217-8918712', '217-8918713', 'MR', 1000, '2003-01-01 00:00:00', '2003-01-01 00:00:00', '', '', '0000-00-00', '', 0, '', 0, 0, 0);
#
# Table structure for table `oxuserbaskets`
#

DROP TABLE IF EXISTS `oxuserbaskets`;

CREATE TABLE `oxuserbaskets` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXTITLE` varchar(255) NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `OXPUBLIC` tinyint(1) DEFAULT '1' NOT NULL,
  `OXUPDATE` INT NOT NULL default 0,
  PRIMARY KEY  (`OXID`),
  KEY `OXUPDATE` (`OXUPDATE`),
  KEY `OXTITLE` (`OXTITLE`),
  KEY `OXUSERID` (`OXUSERID`)
) ENGINE=InnoDB;

#
# Table structure for table `oxuserbasketitems`
#

DROP TABLE IF EXISTS `oxuserbasketitems`;

CREATE TABLE `oxuserbasketitems` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXBASKETID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXARTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXAMOUNT` char(32) NOT NULL default '',
  `OXSELLIST` varchar(255) NOT NULL default '',
  `OXPERSPARAM` text NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXBASKETID` (`OXBASKETID`),
  KEY `OXARTID` (`OXARTID`)
) ENGINE=InnoDB;

#
# Table structure for table `oxuserpayments`
#

DROP TABLE IF EXISTS `oxuserpayments`;

CREATE TABLE `oxuserpayments` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXPAYMENTSID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXVALUE` blob NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXUSERID` (`OXUSERID`)
) ENGINE=InnoDB;

#
# Table structure for table `oxvendor`
#

DROP TABLE IF EXISTS `oxvendor`;

CREATE TABLE `oxvendor` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXACTIVE` tinyint(1) NOT NULL default '1',
  `OXICON` char(128) NOT NULL default '',
  `OXTITLE` char(255) NOT NULL default '',
  `OXSHORTDESC` char(255) NOT NULL default '',
  `OXTITLE_1` char(255) NOT NULL default '',
  `OXSHORTDESC_1` char(255) NOT NULL default '',
  `OXTITLE_2` char(255) NOT NULL default '',
  `OXSHORTDESC_2` char(255) NOT NULL default '',
  `OXTITLE_3` char(255) NOT NULL default '',
  `OXSHORTDESC_3` char(255) NOT NULL default '',
  `OXSHOWSUFFIX` tinyint(1) NOT NULL default '1',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  KEY `OXACTIVE` (`OXACTIVE`)
) ENGINE=MyISAM;

#
# Table structure for table `oxvouchers`
#

DROP TABLE IF EXISTS `oxvouchers` ;

CREATE  TABLE IF NOT EXISTS `oxvouchers` (
  `OXDATEUSED` DATE NULL DEFAULT NULL ,
  `OXORDERID` char(32) character set latin1 collate latin1_general_ci NOT NULL DEFAULT '' ,
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL DEFAULT '' ,
  `OXRESERVED` INT(11) NOT NULL DEFAULT 0 ,
  `OXVOUCHERNR` varchar(255) NOT NULL DEFAULT '',
  `OXVOUCHERSERIEID` char(32) character set latin1 collate latin1_general_ci NOT NULL DEFAULT '' ,
  `OXDISCOUNT` FLOAT(9,2) NULL DEFAULT NULL ,
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL DEFAULT '' ,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  INDEX OXVOUCHERSERIEID (`OXVOUCHERSERIEID` ASC) ,
  INDEX OXORDERID (`OXORDERID` ASC) ,
  INDEX OXUSERID (`OXUSERID` ASC) ,
  INDEX OXVOUCHERNR (`OXVOUCHERNR` ASC)
) ENGINE = InnoDB;

#
# Table structure for table `oxvoucherseries`
#

DROP TABLE IF EXISTS `oxvoucherseries` ;

CREATE  TABLE IF NOT EXISTS `oxvoucherseries` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL DEFAULT '' ,
  `OXSHOPID` CHAR(32) NOT NULL DEFAULT '' ,
  `OXSERIENR` VARCHAR(255) NOT NULL DEFAULT '' ,
  `OXSERIEDESCRIPTION` VARCHAR(255) NOT NULL DEFAULT '' ,
  `OXDISCOUNT` FLOAT(9,2) NOT NULL DEFAULT '0' ,
  `OXDISCOUNTTYPE` ENUM('percent','absolute') NOT NULL DEFAULT 'absolute' ,
  `OXSTARTDATE` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `OXRELEASEDATE` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `OXBEGINDATE` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `OXENDDATE` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `OXALLOWSAMESERIES` TINYINT(1) NOT NULL DEFAULT 0 ,
  `OXALLOWOTHERSERIES` TINYINT(1) NOT NULL DEFAULT 0 ,
  `OXALLOWUSEANOTHER` TINYINT(1) NOT NULL DEFAULT 0 ,
  `OXMINIMUMVALUE` FLOAT(9,2) NOT NULL DEFAULT '0.00' ,
  `OXCALCULATEONCE` TINYINT(1) NOT NULL DEFAULT 0,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`),
  INDEX OXSERIENR (`OXSERIENR` ASC) ,
  INDEX OXSHOPID (`OXSHOPID` ASC)
) ENGINE = InnoDB;

#
# Table structure for table `oxwrapping`
#

DROP TABLE IF EXISTS `oxwrapping`;

CREATE TABLE `oxwrapping` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL,
  `OXSHOPID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXACTIVE` tinyint(1) NOT NULL default '1',
  `OXACTIVE_1` tinyint(1) NOT NULL default '1',
  `OXACTIVE_2` tinyint(1) NOT NULL default '1',
  `OXACTIVE_3` tinyint(1) NOT NULL default '1',
  `OXTYPE` varchar(4) NOT NULL default 'WRAP',
  `OXNAME` varchar(128) NOT NULL default '',
  `OXNAME_1` varchar(128) NOT NULL default '',
  `OXNAME_2` varchar(128) NOT NULL default '',
  `OXNAME_3` varchar(128) NOT NULL default '',
  `OXPIC` varchar(128) NOT NULL default '',
  `OXPRICE` double NOT NULL default '0',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM;




CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles AS SELECT oxarticles.* FROM oxarticles;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles_de AS SELECT OXID,OXSHOPID,OXPARENTID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXARTNUM,OXEAN,OXDISTEAN,OXMPN,OXTITLE,OXSHORTDESC,OXPRICE,OXBLFIXEDPRICE,OXPRICEA,OXPRICEB,OXPRICEC,OXBPRICE,OXTPRICE,OXUNITNAME,OXUNITQUANTITY,OXEXTURL,OXURLDESC,OXURLIMG,OXVAT,OXTHUMB,OXICON,OXPICSGENERATED,OXPIC1,OXPIC2,OXPIC3,OXPIC4,OXPIC5,OXPIC6,OXPIC7,OXPIC8,OXPIC9,OXPIC10,OXPIC11,OXPIC12,OXWEIGHT,OXSTOCK,OXSTOCKFLAG,OXSTOCKTEXT,OXNOSTOCKTEXT,OXDELIVERY,OXINSERT,OXTIMESTAMP,OXLENGTH,OXWIDTH,OXHEIGHT,OXFILE,OXSEARCHKEYS,OXTEMPLATE,OXQUESTIONEMAIL,OXISSEARCH,OXISCONFIGURABLE,OXVARNAME,OXVARSTOCK,OXVARCOUNT,OXVARSELECT,OXVARMINPRICE,OXVARMAXPRICE,OXBUNDLEID,OXFOLDER,OXSUBCLASS,OXSORT,OXSOLDAMOUNT,OXNONMATERIAL,OXFREESHIPPING,OXREMINDACTIVE,OXREMINDAMOUNT,OXAMITEMID,OXAMTASKID,OXVENDORID,OXMANUFACTURERID,OXSKIPDISCOUNTS,OXRATING,OXRATINGCNT,OXMINDELTIME,OXMAXDELTIME,OXDELTIMEUNIT,OXUPDATEPRICE, OXUPDATEPRICEA, OXUPDATEPRICEB, OXUPDATEPRICEC, OXUPDATEPRICETIME, OXISDOWNLOADABLE FROM oxarticles;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles_en AS SELECT OXID,OXSHOPID,OXPARENTID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXARTNUM,OXEAN,OXDISTEAN,OXMPN,OXTITLE_1 AS OXTITLE,OXSHORTDESC_1 AS OXSHORTDESC,OXPRICE,OXBLFIXEDPRICE,OXPRICEA,OXPRICEB,OXPRICEC,OXBPRICE,OXTPRICE,OXUNITNAME,OXUNITQUANTITY,OXEXTURL,OXURLDESC_1 AS OXURLDESC,OXURLIMG,OXVAT,OXTHUMB,OXICON,OXPICSGENERATED,OXPIC1,OXPIC2,OXPIC3,OXPIC4,OXPIC5,OXPIC6,OXPIC7,OXPIC8,OXPIC9,OXPIC10,OXPIC11,OXPIC12,OXWEIGHT,OXSTOCK,OXSTOCKFLAG,OXSTOCKTEXT_1 AS OXSTOCKTEXT,OXNOSTOCKTEXT_1 AS OXNOSTOCKTEXT,OXDELIVERY,OXINSERT,OXTIMESTAMP,OXLENGTH,OXWIDTH,OXHEIGHT,OXFILE,OXSEARCHKEYS_1 AS OXSEARCHKEYS,OXTEMPLATE,OXQUESTIONEMAIL,OXISSEARCH,OXISCONFIGURABLE,OXVARNAME_1 AS OXVARNAME,OXVARSTOCK,OXVARCOUNT,OXVARSELECT_1 AS OXVARSELECT,OXVARMINPRICE,OXVARMAXPRICE,OXBUNDLEID,OXFOLDER,OXSUBCLASS,OXSORT,OXSOLDAMOUNT,OXNONMATERIAL,OXFREESHIPPING,OXREMINDACTIVE,OXREMINDAMOUNT,OXAMITEMID,OXAMTASKID,OXVENDORID,OXMANUFACTURERID,OXSKIPDISCOUNTS,OXRATING,OXRATINGCNT,OXMINDELTIME,OXMAXDELTIME,OXDELTIMEUNIT,OXUPDATEPRICE, OXUPDATEPRICEA, OXUPDATEPRICEB, OXUPDATEPRICEC, OXUPDATEPRICETIME, OXISDOWNLOADABLE FROM oxarticles;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxartextends AS SELECT oxartextends.* FROM oxartextends;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxartextends_de AS SELECT OXID,OXLONGDESC,OXTAGS,OXTIMESTAMP FROM oxartextends;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxartextends_en AS SELECT OXID,OXLONGDESC_1 AS OXLONGDESC,OXTAGS_1 AS OXTAGS,OXTIMESTAMP FROM oxartextends;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxattribute AS SELECT oxattribute.* FROM oxattribute;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxattribute_de AS SELECT OXID,OXSHOPID,OXTITLE,OXPOS,OXTIMESTAMP, OXDISPLAYINBASKET FROM oxattribute;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxattribute_en AS SELECT OXID,OXSHOPID,OXTITLE_1 AS OXTITLE,OXPOS,OXTIMESTAMP, OXDISPLAYINBASKET FROM oxattribute;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcategories AS SELECT oxcategories.* FROM oxcategories;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcategories_de AS SELECT OXID,OXPARENTID,OXLEFT,OXRIGHT,OXROOTID,OXSORT,OXACTIVE,OXHIDDEN,OXSHOPID,OXTITLE,OXDESC,OXLONGDESC,OXTHUMB,OXEXTLINK,OXTEMPLATE,OXDEFSORT,OXDEFSORTMODE,OXPRICEFROM,OXPRICETO,OXICON,OXPROMOICON,OXVAT,OXSKIPDISCOUNTS,OXSHOWSUFFIX,OXTIMESTAMP FROM oxcategories;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcategories_en AS SELECT OXID,OXPARENTID,OXLEFT,OXRIGHT,OXROOTID,OXSORT,OXACTIVE_1 AS OXACTIVE,OXHIDDEN,OXSHOPID,OXTITLE_1 AS OXTITLE,OXDESC_1 AS OXDESC,OXLONGDESC_1 AS OXLONGDESC,OXTHUMB_1 AS OXTHUMB,OXEXTLINK,OXTEMPLATE,OXDEFSORT,OXDEFSORTMODE,OXPRICEFROM,OXPRICETO,OXICON,OXPROMOICON,OXVAT,OXSKIPDISCOUNTS,OXSHOWSUFFIX,OXTIMESTAMP FROM oxcategories;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcontents AS SELECT oxcontents.* FROM oxcontents;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcontents_de AS SELECT OXID,OXLOADID,OXSHOPID,OXSNIPPET,OXTYPE,OXACTIVE,OXPOSITION,OXTITLE,OXCONTENT,OXCATID,OXFOLDER,OXTERMVERSION,OXTIMESTAMP FROM oxcontents;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcontents_en AS SELECT OXID,OXLOADID,OXSHOPID,OXSNIPPET,OXTYPE,OXACTIVE_1 AS OXACTIVE,OXPOSITION,OXTITLE_1 AS OXTITLE,OXCONTENT_1 AS OXCONTENT,OXCATID,OXFOLDER,OXTERMVERSION,OXTIMESTAMP FROM oxcontents;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcountry AS SELECT oxcountry.* FROM oxcountry;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcountry_de AS SELECT OXID,OXACTIVE,OXTITLE,OXISOALPHA2,OXISOALPHA3,OXUNNUM3,OXORDER,OXSHORTDESC,OXLONGDESC,OXVATSTATUS,OXTIMESTAMP FROM oxcountry;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcountry_en AS SELECT OXID,OXACTIVE,OXTITLE_1 AS OXTITLE,OXISOALPHA2,OXISOALPHA3,OXUNNUM3,OXORDER,OXSHORTDESC_1 AS OXSHORTDESC,OXLONGDESC_1 AS OXLONGDESC,OXVATSTATUS,OXTIMESTAMP FROM oxcountry;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdelivery AS SELECT oxdelivery.* FROM oxdelivery;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdelivery_de AS SELECT OXID,OXSHOPID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXTITLE,OXADDSUMTYPE,OXADDSUM,OXDELTYPE,OXPARAM,OXPARAMEND,OXFIXED,OXSORT,OXFINALIZE,OXTIMESTAMP FROM oxdelivery;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdelivery_en AS SELECT OXID,OXSHOPID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXTITLE_1 AS OXTITLE,OXADDSUMTYPE,OXADDSUM,OXDELTYPE,OXPARAM,OXPARAMEND,OXFIXED,OXSORT,OXFINALIZE,OXTIMESTAMP FROM oxdelivery;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdiscount AS SELECT oxdiscount.* FROM oxdiscount;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdiscount_de AS SELECT OXID,OXSHOPID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXTITLE,OXAMOUNT,OXAMOUNTTO,OXPRICETO,OXPRICE,OXADDSUMTYPE,OXADDSUM,OXITMARTID,OXITMAMOUNT,OXITMMULTIPLE,OXTIMESTAMP FROM oxdiscount;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdiscount_en AS SELECT OXID,OXSHOPID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXTITLE_1 AS OXTITLE,OXAMOUNT,OXAMOUNTTO,OXPRICETO,OXPRICE,OXADDSUMTYPE,OXADDSUM,OXITMARTID,OXITMAMOUNT,OXITMMULTIPLE,OXTIMESTAMP FROM oxdiscount;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxgroups AS SELECT oxgroups.* FROM oxgroups;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxgroups_de AS SELECT OXID,OXACTIVE,OXTITLE,OXTIMESTAMP FROM oxgroups;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxgroups_en AS SELECT OXID,OXACTIVE,OXTITLE_1 AS OXTITLE,OXTIMESTAMP FROM oxgroups;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxlinks AS SELECT oxlinks.* FROM oxlinks;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxlinks_de AS SELECT OXID,OXSHOPID,OXACTIVE,OXURL,OXURLDESC,OXINSERT,OXTIMESTAMP FROM oxlinks;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxlinks_en AS SELECT OXID,OXSHOPID,OXACTIVE,OXURL,OXURLDESC_1 AS OXURLDESC,OXINSERT,OXTIMESTAMP FROM oxlinks;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxnews AS SELECT oxnews.* FROM oxnews;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxnews_de AS SELECT OXID,OXSHOPID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXDATE,OXSHORTDESC,OXLONGDESC,OXTIMESTAMP FROM oxnews;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxnews_en AS SELECT OXID,OXSHOPID,OXACTIVE_1 AS OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXDATE,OXSHORTDESC_1 AS OXSHORTDESC,OXLONGDESC_1 AS OXLONGDESC,OXTIMESTAMP FROM oxnews;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxobject2attribute AS SELECT oxobject2attribute.* FROM oxobject2attribute;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxobject2attribute_de AS SELECT OXID,OXOBJECTID,OXATTRID,OXVALUE,OXPOS,OXTIMESTAMP FROM oxobject2attribute;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxobject2attribute_en AS SELECT OXID,OXOBJECTID,OXATTRID,OXVALUE_1 AS OXVALUE,OXPOS,OXTIMESTAMP FROM oxobject2attribute;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxpayments AS SELECT oxpayments.* FROM oxpayments;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxpayments_de AS SELECT OXID,OXACTIVE,OXDESC,OXADDSUM,OXADDSUMTYPE,OXADDSUMRULES,OXFROMBONI,OXFROMAMOUNT,OXTOAMOUNT,OXVALDESC,OXCHECKED,OXLONGDESC,OXSORT,OXTSPAYMENTID,OXTIMESTAMP FROM oxpayments;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxpayments_en AS SELECT OXID,OXACTIVE,OXDESC_1 AS OXDESC,OXADDSUM,OXADDSUMTYPE,OXADDSUMRULES,OXFROMBONI,OXFROMAMOUNT,OXTOAMOUNT,OXVALDESC_1 AS OXVALDESC,OXCHECKED,OXLONGDESC_1 AS OXLONGDESC,OXSORT,OXTSPAYMENTID,OXTIMESTAMP FROM oxpayments;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxselectlist AS SELECT oxselectlist.* FROM oxselectlist;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxselectlist_de AS SELECT OXID,OXSHOPID,OXTITLE,OXIDENT,OXVALDESC,OXTIMESTAMP FROM oxselectlist;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxselectlist_en AS SELECT OXID,OXSHOPID,OXTITLE_1 AS OXTITLE,OXIDENT,OXVALDESC_1 AS OXVALDESC,OXTIMESTAMP FROM oxselectlist;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxshops AS SELECT oxshops.* FROM oxshops;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxshops_de AS SELECT OXID,OXACTIVE,OXPRODUCTIVE,OXDEFCURRENCY,OXDEFLANGUAGE,OXNAME,OXTITLEPREFIX,OXTITLESUFFIX,OXSTARTTITLE,OXINFOEMAIL,OXORDEREMAIL,OXOWNEREMAIL,OXORDERSUBJECT,OXREGISTERSUBJECT,OXFORGOTPWDSUBJECT,OXSENDEDNOWSUBJECT,OXSMTP,OXSMTPUSER,OXSMTPPWD,OXCOMPANY,OXSTREET,OXZIP,OXCITY,OXCOUNTRY,OXBANKNAME,OXBANKNUMBER,OXBANKCODE,OXVATNUMBER,OXBICCODE,OXIBANNUMBER,OXFNAME,OXLNAME,OXTELEFON,OXTELEFAX,OXURL,OXDEFCAT,OXHRBNR,OXCOURT,OXADBUTLERID,OXAFFILINETID,OXSUPERCLICKSID,OXAFFILIWELTID,OXAFFILI24ID,OXEDITION,OXVERSION,OXSEOACTIVE,OXTIMESTAMP FROM oxshops;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxshops_en AS SELECT OXID,OXACTIVE,OXPRODUCTIVE,OXDEFCURRENCY,OXDEFLANGUAGE,OXNAME,OXTITLEPREFIX_1 AS OXTITLEPREFIX,OXTITLESUFFIX_1 AS OXTITLESUFFIX,OXSTARTTITLE_1 AS OXSTARTTITLE,OXINFOEMAIL,OXORDEREMAIL,OXOWNEREMAIL,OXORDERSUBJECT_1 AS OXORDERSUBJECT,OXREGISTERSUBJECT_1 AS OXREGISTERSUBJECT,OXFORGOTPWDSUBJECT_1 AS OXFORGOTPWDSUBJECT,OXSENDEDNOWSUBJECT_1 AS OXSENDEDNOWSUBJECT,OXSMTP,OXSMTPUSER,OXSMTPPWD,OXCOMPANY,OXSTREET,OXZIP,OXCITY,OXCOUNTRY,OXBANKNAME,OXBANKNUMBER,OXBANKCODE,OXVATNUMBER,OXBICCODE,OXIBANNUMBER,OXFNAME,OXLNAME,OXTELEFON,OXTELEFAX,OXURL,OXDEFCAT,OXHRBNR,OXCOURT,OXADBUTLERID,OXAFFILINETID,OXSUPERCLICKSID,OXAFFILIWELTID,OXAFFILI24ID,OXEDITION,OXVERSION,OXSEOACTIVE_1 AS OXSEOACTIVE,OXTIMESTAMP FROM oxshops;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxactions AS SELECT oxactions.* FROM oxactions;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxactions_de AS SELECT OXID,OXSHOPID,OXTYPE,OXTITLE,OXLONGDESC,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXPIC,OXLINK,OXSORT,OXTIMESTAMP FROM oxactions;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxactions_en AS SELECT OXID,OXSHOPID,OXTYPE,OXTITLE_1 AS OXTITLE,OXLONGDESC_1 AS OXLONGDESC,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXPIC_1 AS OXPIC,OXLINK_1 AS OXLINK,OXSORT,OXTIMESTAMP FROM oxactions;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxwrapping AS SELECT oxwrapping.* FROM oxwrapping;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxwrapping_de AS SELECT OXID,OXSHOPID,OXACTIVE,OXTYPE,OXNAME,OXPIC,OXPRICE,OXTIMESTAMP FROM oxwrapping;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxwrapping_en AS SELECT OXID,OXSHOPID,OXACTIVE_1 AS OXACTIVE,OXTYPE,OXNAME_1 AS OXNAME,OXPIC,OXPRICE,OXTIMESTAMP FROM oxwrapping;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdeliveryset AS SELECT oxdeliveryset.* FROM oxdeliveryset;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdeliveryset_de AS SELECT OXID,OXSHOPID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXTITLE,OXPOS,OXTIMESTAMP FROM oxdeliveryset;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdeliveryset_en AS SELECT OXID,OXSHOPID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXTITLE_1 AS OXTITLE,OXPOS,OXTIMESTAMP FROM oxdeliveryset;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxvendor AS SELECT oxvendor.* FROM oxvendor;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxvendor_de AS SELECT OXID,OXSHOPID,OXACTIVE,OXICON,OXTITLE,OXSHORTDESC,OXSHOWSUFFIX,OXTIMESTAMP FROM oxvendor;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxvendor_en AS SELECT OXID,OXSHOPID,OXACTIVE,OXICON,OXTITLE_1 AS OXTITLE,OXSHORTDESC_1 AS OXSHORTDESC,OXSHOWSUFFIX,OXTIMESTAMP FROM oxvendor;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxmanufacturers AS SELECT oxmanufacturers.* FROM oxmanufacturers;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxmanufacturers_de AS SELECT OXID,OXSHOPID,OXACTIVE,OXICON,OXTITLE,OXSHORTDESC,OXSHOWSUFFIX,OXTIMESTAMP FROM oxmanufacturers;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxmanufacturers_en AS SELECT OXID,OXSHOPID,OXACTIVE,OXICON,OXTITLE_1 AS OXTITLE,OXSHORTDESC_1 AS OXSHORTDESC,OXSHOWSUFFIX,OXTIMESTAMP FROM oxmanufacturers;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxmediaurls AS SELECT oxmediaurls.* FROM oxmediaurls;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxmediaurls_de AS SELECT OXID,OXOBJECTID,OXURL,OXDESC,OXISUPLOADED,OXTIMESTAMP FROM oxmediaurls;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxmediaurls_en AS SELECT OXID,OXOBJECTID,OXURL,OXDESC_1 AS OXDESC,OXISUPLOADED,OXTIMESTAMP FROM oxmediaurls;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxstates AS SELECT oxstates.* FROM oxstates;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxstates_de AS SELECT OXID,OXCOUNTRYID,OXTITLE,OXISOALPHA2,OXTIMESTAMP FROM oxstates;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxstates_en AS SELECT OXID,OXCOUNTRYID,OXTITLE_1 AS OXTITLE,OXISOALPHA2,OXTIMESTAMP FROM oxstates;
