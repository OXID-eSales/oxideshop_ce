SET @@session.sql_mode = '';

#
# Table structure for table `oxacceptedterms`
# for storing information user accepted terms version
# created 2010-06-10
#

DROP TABLE IF EXISTS `oxacceptedterms`;

CREATE TABLE `oxacceptedterms` (
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'User id (oxuser)',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXTERMVERSION` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Terms version',
  `OXACCEPTEDTIME` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Time, when terms were accepted',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY (`OXUSERID`, `OXSHOPID`)
) ENGINE=MyISAM COMMENT='Shows which users has accepted shop terms';

#
# Table structure for table `oxaccessoire2article`
#

DROP TABLE IF EXISTS `oxaccessoire2article`;

CREATE TABLE `oxaccessoire2article` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Accessory Article id (oxarticles)',
  `OXARTICLENID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Article id (oxarticles)',
  `OXSORT` int(5) NOT NULL default '0' COMMENT 'Sorting',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXARTICLENID` (`OXARTICLENID`)
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between article and its accessory articles';

#
# Table structure for table `oxactions`
#

DROP TABLE IF EXISTS `oxactions`;

CREATE TABLE `oxactions` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Action id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXTYPE` tinyint( 1 ) NOT NULL COMMENT 'Action type: 0 or 1 - action, 2 - promotion, 3 - banner',
  `OXTITLE` char(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXTITLE_1` char(128) NOT NULL default '' COMMENT '',
  `OXTITLE_2` char(128) NOT NULL default '' COMMENT '',
  `OXTITLE_3` char(128) NOT NULL default '' COMMENT '',
  `OXLONGDESC` text NOT NULL COMMENT 'Long description, used for promotion (multilanguage)',
  `OXLONGDESC_1` text NOT NULL,
  `OXLONGDESC_2` text NOT NULL,
  `OXLONGDESC_3` text NOT NULL,
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Active',
  `OXACTIVEFROM` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Active from specified date',
  `OXACTIVETO` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Active to specified date',
  `OXPIC`   VARCHAR(128) NOT NULL DEFAULT '' COMMENT 'Picture filename, used for banner (multilanguage)',
  `OXPIC_1` VARCHAR(128) NOT NULL DEFAULT '',
  `OXPIC_2` VARCHAR(128) NOT NULL DEFAULT '',
  `OXPIC_3` VARCHAR(128) NOT NULL DEFAULT '',
  `OXLINK`   VARCHAR(128) NOT NULL DEFAULT '' COMMENT 'Link, used on banner (multilanguage)',
  `OXLINK_1` VARCHAR(128) NOT NULL DEFAULT '',
  `OXLINK_2` VARCHAR(128) NOT NULL DEFAULT '',
  `OXLINK_3` VARCHAR(128) NOT NULL DEFAULT '',
  `OXSORT` int( 5 ) NOT NULL DEFAULT '0' COMMENT 'Sorting',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  index(`oxsort`),
  index(`OXTYPE`, `OXACTIVE`, `OXACTIVETO`, `OXACTIVEFROM`)
) ENGINE=MyISAM COMMENT 'Stores information about actions, promotions and banners';

#
# Table structure for table `oxactions2article`
#

DROP TABLE IF EXISTS `oxactions2article`;

CREATE TABLE `oxactions2article` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXACTIONID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Action id (oxactions)',
  `OXARTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Article id (oxarticles)',
  `OXSORT` int(11) NOT NULL default '0' COMMENT 'Sorting',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXMAINIDX` (`OXSHOPID`,`OXACTIONID`,`OXSORT`),
  KEY `OXARTID` (`OXARTID`)
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between actions and articles';

#
# Table structure for table `oxaddress`
#

DROP TABLE IF EXISTS `oxaddress`;

CREATE TABLE `oxaddress` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Address id',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id (oxuser)',
  `OXADDRESSUSERID` VARCHAR(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'User id (oxuser)',
  `OXCOMPANY` varchar(255) NOT NULL default '' COMMENT 'Company name',
  `OXFNAME` varchar(255) NOT NULL default '' COMMENT 'First name',
  `OXLNAME` varchar(255) NOT NULL default '' COMMENT 'Last name',
  `OXSTREET` varchar(255) NOT NULL default '' COMMENT 'Street',
  `OXSTREETNR` varchar(16) NOT NULL default '' COMMENT 'House number',
  `OXADDINFO` varchar(255) NOT NULL default '' COMMENT 'Additional info',
  `OXCITY` varchar(255) NOT NULL default '' COMMENT 'City',
  `OXCOUNTRY` varchar(255) NOT NULL default '' COMMENT 'Country name',
  `OXCOUNTRYID` char( 32 ) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Country id (oxcountry)',
  `OXSTATEID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'State id (oxstate)',
  `OXZIP` varchar(50) NOT NULL default '' COMMENT 'Zip code',
  `OXFON` varchar(128) NOT NULL default '' COMMENT 'Phone number',
  `OXFAX` varchar(128) NOT NULL default '' COMMENT 'Fax number',
  `OXSAL` varchar(128) NOT NULL default '' COMMENT 'User title prefix (Mr/Mrs)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXUSERID` (`OXUSERID`)
) ENGINE=MyISAM COMMENT 'Stores user shipping addresses';

#
# Table structure for table `oxadminlog`
#

DROP TABLE IF EXISTS `oxadminlog`;

CREATE TABLE `oxadminlog` (
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id (oxuser)',
  `OXSQL` text NOT NULL COMMENT 'Logged sql'
) ENGINE=MyISAM COMMENT 'Logs admin actions';

#
# Table structure for table `oxarticles`
#

DROP TABLE IF EXISTS `oxarticles`;

CREATE TABLE `oxarticles` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Article id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXPARENTID` char(32) character set latin1 collate latin1_general_ci NOT NULL  default '' COMMENT 'Parent article id',
  `OXACTIVE` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Active',
  `OXHIDDEN` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Hidden',
  `OXACTIVEFROM` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Active from specified date',
  `OXACTIVETO` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT  'Active to specified date',
  `OXARTNUM` varchar(255) NOT NULL default '' COMMENT 'Article number',
  `OXEAN` varchar(128)  NOT NULL default '' COMMENT 'International Article Number (EAN)',
  `OXDISTEAN` varchar(128)  NOT NULL default '' COMMENT 'Manufacture International Article Number (Man. EAN)',
  `OXMPN` varchar(100) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Manufacture Part Number (MPN)',
  `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXSHORTDESC` varchar(255) NOT NULL default '' COMMENT 'Short description (multilanguage)',
  `OXPRICE` double NOT NULL default '0' COMMENT 'Article Price',
  `OXBLFIXEDPRICE` tinyint(1) NOT NULL default '0' COMMENT 'No Promotions (Price Alert) ',
  `OXPRICEA` double NOT NULL default '0' COMMENT 'Price A',
  `OXPRICEB` double NOT NULL default '0' COMMENT 'Price B',
  `OXPRICEC` double NOT NULL default '0' COMMENT 'Price C',
  `OXBPRICE` double NOT NULL default '0' COMMENT 'Purchase Price',
  `OXTPRICE` double NOT NULL default '0' COMMENT 'Recommended Retail Price (RRP)',
  `OXUNITNAME` varchar(32) NOT NULL default '' COMMENT 'Unit name (kg,g,l,cm etc), used in setting price per quantity unit calculation',
  `OXUNITQUANTITY` double NOT NULL default '0' COMMENT 'Article quantity, used in setting price per quantity unit calculation',
  `OXEXTURL` varchar(255) NOT NULL default '' COMMENT 'External URL to other information about the article',
  `OXURLDESC` varchar(255) NOT NULL default '' COMMENT 'Text for external URL (multilanguage)',
  `OXURLIMG` varchar(128) NOT NULL default '' COMMENT 'External URL image',
  `OXVAT` float default NULL COMMENT 'Value added tax. If specified, used in all calculations instead of global vat',
  `OXTHUMB` varchar(128) NOT NULL default '' COMMENT 'Thumbnail filename',
  `OXICON` varchar(128) NOT NULL default '' COMMENT 'Icon filename',
  `OXPIC1` varchar(128) NOT NULL default '' COMMENT '1# Picture filename',
  `OXPIC2` varchar(128) NOT NULL default '' COMMENT '2# Picture filename',
  `OXPIC3` varchar(128) NOT NULL default '' COMMENT '3# Picture filename',
  `OXPIC4` varchar(128) NOT NULL default '' COMMENT '4# Picture filename',
  `OXPIC5` varchar(128) NOT NULL default '' COMMENT '5# Picture filename',
  `OXPIC6` varchar(128) NOT NULL default '' COMMENT '6# Picture filename',
  `OXPIC7` varchar(128) NOT NULL default '' COMMENT '7# Picture filename',
  `OXPIC8` varchar(128) NOT NULL default '' COMMENT '8# Picture filename',
  `OXPIC9` varchar(128) NOT NULL default '' COMMENT '9# Picture filename',
  `OXPIC10` varchar(128) NOT NULL default '' COMMENT '10# Picture filename',
  `OXPIC11` varchar(128) NOT NULL default '' COMMENT '11# Picture filename',
  `OXPIC12` varchar(128) NOT NULL default '' COMMENT '12# Picture filename',
  `OXWEIGHT` double NOT NULL default '0' COMMENT 'Weight (kg)',
  `OXSTOCK` double NOT NULL default '0' COMMENT 'Article quantity in stock',
  `OXSTOCKFLAG` tinyint(1) NOT NULL default '1' COMMENT 'Delivery Status: 1 - Standard, 2 - If out of Stock, offline, 3 - If out of Stock, not orderable, 4 - External Storehouse',
  `OXSTOCKTEXT` varchar(255) NOT NULL default '' COMMENT 'Message, which is shown if the article is in stock (multilanguage)',
  `OXNOSTOCKTEXT` varchar(255) NOT NULL default '' COMMENT 'Message, which is shown if the article is off stock (multilanguage)',
  `OXDELIVERY` date NOT NULL default '0000-00-00' COMMENT 'Date, when the product will be available again if it is sold out',
  `OXINSERT` date NOT NULL default '0000-00-00' COMMENT 'Creation time',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `OXLENGTH` double NOT NULL default '0' COMMENT 'Article dimensions: Length',
  `OXWIDTH` double NOT NULL default '0' COMMENT 'Article dimensions: Width',
  `OXHEIGHT` double NOT NULL default '0' COMMENT 'Article dimensions: Height',
  `OXFILE` varchar(128) NOT NULL default '' COMMENT 'File, shown in article media list',
  `OXSEARCHKEYS` varchar(255) NOT NULL default '' COMMENT 'Search terms (multilanguage)',
  `OXTEMPLATE` varchar(128) NOT NULL default '' COMMENT 'Alternative template filename (if empty, default is used)',
  `OXQUESTIONEMAIL` varchar(255) NOT NULL default '' COMMENT 'E-mail for question',
  `OXISSEARCH` tinyint(1) NOT NULL default '1' COMMENT 'Should article be shown in search',
  `OXISCONFIGURABLE` tinyint NOT NULL DEFAULT '0' COMMENT 'Can article be customized',
  `OXVARNAME` varchar(255) NOT NULL default '' COMMENT 'Name of variants selection lists (different lists are separated by | ) (multilanguage)',
  `OXVARSTOCK` int(5) NOT NULL default '0' COMMENT 'Sum of active article variants stock quantity',
  `OXVARCOUNT` int(1) NOT NULL default '0' COMMENT 'Total number of variants that article has (active and inactive)',
  `OXVARSELECT` varchar(255) NOT NULL default '' COMMENT 'Variant article selections (separated by | ) (multilanguage)',
  `OXVARMINPRICE` double NOT NULL default '0' COMMENT 'Lowest price in active article variants',
  `OXVARMAXPRICE` double NOT NULL default '0' COMMENT 'Highest price in active article variants',
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
  `OXBUNDLEID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Bundled article id',
  `OXFOLDER` varchar(32) NOT NULL default '' COMMENT 'Folder',
  `OXSUBCLASS` varchar(32) NOT NULL default '' COMMENT 'Subclass',
  `OXSTOCKTEXT_1` varchar(255) NOT NULL default '',
  `OXSTOCKTEXT_2` varchar(255) NOT NULL default '',
  `OXSTOCKTEXT_3` varchar(255) NOT NULL default '',
  `OXNOSTOCKTEXT_1` varchar(255) NOT NULL default '',
  `OXNOSTOCKTEXT_2` varchar(255) NOT NULL default '',
  `OXNOSTOCKTEXT_3` varchar(255) NOT NULL default '',
  `OXSORT` int(5) NOT NULL default '0' COMMENT 'Sorting',
  `OXSOLDAMOUNT` double NOT NULL default '0' COMMENT 'Amount of sold articles including variants (used only for parent articles)',
  `OXNONMATERIAL` int(1) NOT NULL default '0' COMMENT 'Intangible article, free shipping is used (variants inherits parent setting)',
  `OXFREESHIPPING` int(1) NOT NULL default '0' COMMENT 'Free shipping (variants inherits parent setting)',
  `OXREMINDACTIVE` int(1) NOT NULL default '0' COMMENT 'Enables sending of notification email when oxstock field value falls below oxremindamount value',
  `OXREMINDAMOUNT` double NOT NULL default '0' COMMENT 'Defines the amount, below which notification email will be sent if oxremindactive is set to 1',
  `OXAMITEMID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXAMTASKID` varchar(16) character set latin1 collate latin1_general_ci NOT NULL default '0',
  `OXVENDORID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Vendor id (oxvendor)',
  `OXMANUFACTURERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Manufacturer id (oxmanufacturers)',
  `OXSKIPDISCOUNTS` tinyint(1) NOT NULL default '0' COMMENT 'Skips all negative Discounts (Discounts, Vouchers, Delivery ...)',
  `OXRATING` double NOT NULL default '0' COMMENT 'Article rating',
  `OXRATINGCNT` int(11) NOT NULL default '0' COMMENT 'Rating votes count',
  `OXMINDELTIME` int(11) NOT NULL default '0' COMMENT 'Minimal delivery time (unit is set in oxdeltimeunit)',
  `OXMAXDELTIME` int(11) NOT NULL default '0' COMMENT 'Maximum delivery time (unit is set in oxdeltimeunit)',
  `OXDELTIMEUNIT` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Delivery time unit: DAY, WEEK, MONTH',
  `OXUPDATEPRICE` DOUBLE NOT NULL default '0' COMMENT 'If not 0, oxprice will be updated to this value on oxupdatepricetime date',
  `OXUPDATEPRICEA` DOUBLE NOT NULL default '0' COMMENT 'If not 0, oxpricea will be updated to this value on oxupdatepricetime date',
  `OXUPDATEPRICEB` DOUBLE NOT NULL default '0' COMMENT 'If not 0, oxpriceb will be updated to this value on oxupdatepricetime date',
  `OXUPDATEPRICEC` DOUBLE NOT NULL default '0' COMMENT 'If not 0, oxpricec will be updated to this value on oxupdatepricetime date',
  `OXUPDATEPRICETIME` TIMESTAMP NOT NULL COMMENT 'Date, when oxprice[a,b,c] should be updated to oxupdateprice[a,b,c] values',
  `OXISDOWNLOADABLE` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Enable download of files for this product',
  `OXSHOWCUSTOMAGREEMENT` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Show custom agreement check in checkout',
  PRIMARY KEY  (`OXID`),
  KEY `OXSORT` (`OXSORT`),
  KEY `OXISSEARCH` (`OXISSEARCH`),
  KEY `OXSTOCKFLAG` (`OXSTOCKFLAG`),
  KEY `OXACTIVE` (`OXACTIVE`),
  KEY `OXACTIVEFROM` (`OXACTIVEFROM`),
  KEY `OXACTIVETO` (`OXACTIVETO`),
  KEY `OXVENDORID` (`OXVENDORID`),
  KEY `OXMANUFACTURERID` (`OXMANUFACTURERID`),
  KEY `OXSOLDAMOUNT` ( `OXSOLDAMOUNT` ),
  KEY `parentsort` ( `OXPARENTID` , `OXSORT` ),
  KEY `OXUPDATEPRICETIME` ( `OXUPDATEPRICETIME` ),
  KEY `OXISDOWNLOADABLE` ( `OXISDOWNLOADABLE` ),
  KEY `OXPRICE` ( `OXPRICE` )
)ENGINE=InnoDB COMMENT 'Articles information';

#
# Table structure for table `oxartextends`
# created on 2008-05-23
#

DROP TABLE IF EXISTS `oxartextends`;

CREATE TABLE `oxartextends` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Article id (extends oxarticles article with this id)',
  `OXLONGDESC` text NOT NULL COMMENT 'Long description (multilanguage)',
  `OXLONGDESC_1` text NOT NULL,
  `OXLONGDESC_2` text NOT NULL,
  `OXLONGDESC_3` text NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM COMMENT 'Additional information for articles';

#
# Table structure for table `oxattribute`
#

DROP TABLE IF EXISTS `oxattribute`;

CREATE TABLE `oxattribute` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Attribute id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXTITLE` char(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXTITLE_1` char(128) NOT NULL default '',
  `OXTITLE_2` char(128) NOT NULL default '',
  `OXTITLE_3` char(128) NOT NULL default '',
  `OXPOS` int(11) NOT NULL default '9999' COMMENT 'Sorting',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `OXDISPLAYINBASKET` tinyint(1) NOT NULL default '0' COMMENT 'Display attribute`s value for articles in checkout',
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM COMMENT 'Article attributes';


#
# Table structure for table `oxcategories`
#

DROP TABLE IF EXISTS `oxcategories`;

CREATE TABLE `oxcategories` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Category id',
  `OXPARENTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default 'oxrootid' COMMENT 'Parent category id',
  `OXLEFT` int(11) NOT NULL default '0' COMMENT 'Used for building category tree',
  `OXRIGHT` int(11) NOT NULL default '0' COMMENT 'Used for building category tree',
  `OXROOTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Root category id',
  `OXSORT` int(11) NOT NULL default '9999' COMMENT 'Sorting',
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Active (multilanguage)',
  `OXHIDDEN` tinyint(1) NOT NULL default '0' COMMENT 'Hidden (Can be accessed by direct link, but is not visible in lists and menu)',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXTITLE` varchar(254) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXDESC` varchar(255) NOT NULL default '' COMMENT 'Description (multilanguage)',
  `OXLONGDESC` text NOT NULL COMMENT 'Long description (multilanguage)',
  `OXTHUMB` varchar(128) NOT NULL default '' COMMENT 'Thumbnail filename (multilanguage)',
  `OXTHUMB_1` VARCHAR(128) NOT NULL DEFAULT '',
  `OXTHUMB_2` VARCHAR(128) NOT NULL DEFAULT '',
  `OXTHUMB_3` VARCHAR(128) NOT NULL DEFAULT '',
  `OXEXTLINK` varchar(255) NOT NULL default '' COMMENT 'External link, that if specified is opened instead of category content',
  `OXTEMPLATE` varchar(128) NOT NULL default '' COMMENT 'Alternative template filename (if empty, default is used)',
  `OXDEFSORT` varchar(64) NOT NULL default '' COMMENT 'Default field for sorting of articles in this category (most of oxarticles fields)',
  `OXDEFSORTMODE` tinyint(1) NOT NULL default '0' COMMENT 'Default mode of sorting of articles in this category (0 - asc, 1 - desc)',
  `OXPRICEFROM` double NOT NULL default '0' COMMENT 'If specified, all articles, with price higher than specified, will be shown in this category',
  `OXPRICETO` double NOT NULL default '0' COMMENT 'If specified, all articles, with price lower than specified, will be shown in this category',
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
  `OXICON` varchar(128) NOT NULL default '' COMMENT 'Icon filename',
  `OXPROMOICON` varchar(128) NOT NULL default '' COMMENT 'Promotion icon filename',
  `OXVAT` FLOAT NULL DEFAULT NULL COMMENT 'VAT, used for articles in this category (only if oxarticles.oxvat is not set)',
  `OXSKIPDISCOUNTS` tinyint(1) NOT NULL default '0' COMMENT 'Skip all negative Discounts for articles in this category (Discounts, Vouchers, Delivery ...) ',
  `OXSHOWSUFFIX` tinyint(1) NOT NULL default '1' COMMENT 'Show SEO Suffix in Category',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
   PRIMARY KEY  (`OXID`),
   KEY `OXROOTID` (`OXROOTID`),
   KEY `OXPARENTID` (`OXPARENTID`),
   KEY `OXPRICEFROM` (`OXPRICEFROM`),
   KEY `OXPRICETO` (`OXPRICETO`),
   KEY `OXHIDDEN` (`OXHIDDEN`),
   KEY `OXSHOPID` (`OXSHOPID`),
   KEY `OXSORT` (`OXSORT`),
   KEY `OXVAT` (`OXVAT`)
) ENGINE=MyISAM COMMENT 'Article categories';

#
# Table structure for table `oxcategory2attribute`
#

DROP TABLE IF EXISTS `oxcategory2attribute`;

CREATE TABLE `oxcategory2attribute` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Category id (oxcategories)',
  `OXATTRID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Attribute id (oxattributes)',
  `OXSORT` INT( 11 ) NOT NULL DEFAULT '9999' COMMENT 'Sorting',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Creation time',
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`)
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between categories and attributes';


#
# Table structure for table `oxconfig`
#

DROP TABLE IF EXISTS `oxconfig`;

CREATE TABLE `oxconfig` (
  `OXID`            char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Config id',
  `OXSHOPID`        int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXMODULE`        varchar(100) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Module or theme specific config (theme:themename, module:modulename)',
  `OXVARNAME`       varchar(100) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Variable name',
  `OXVARTYPE`       varchar(16) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Variable type',
  `OXVARVALUE`      blob NOT NULL COMMENT 'Variable value',
  `OXTIMESTAMP`     timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXVARNAME` (`OXVARNAME`),
  KEY `listall` (`OXSHOPID`, `OXMODULE`)
) ENGINE=MyISAM COMMENT 'Shop configuration values';

#
# Table structure for table `oxconfigdisplay`
# Created on 2010-11-11
#

DROP TABLE IF EXISTS `oxconfigdisplay`;

CREATE TABLE `oxconfigdisplay` (
  `OXID`            char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Config id (extends oxconfig record with this id)',
  `OXCFGMODULE`     varchar(100) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Module or theme specific config (theme:themename, module:modulename)',
  `OXCFGVARNAME`    varchar(100) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Variable name',
  `OXGROUPING`      varchar(255) NOT NULL default '' COMMENT 'Grouping (groups config fields to array with specified value as key)',
  `OXVARCONSTRAINT` varchar(255) NOT NULL default '' COMMENT 'Serialized constraints',
  `OXPOS`           int NOT NULL default 0 COMMENT 'Sorting',
  `OXTIMESTAMP`     timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `list` (`OXCFGMODULE`, `OXCFGVARNAME`)
) ENGINE=MyISAM COMMENT 'Additional configuraion fields';

#
# Table structure for table `oxcontents`
#

DROP TABLE IF EXISTS `oxcontents`;

CREATE TABLE `oxcontents` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Content id',
  `OXLOADID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Id, specified by admin and can be used instead of oxid',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXSNIPPET` tinyint(1) NOT NULL default '1' COMMENT 'Snippet (can be included to other oxcontents records)',
  `OXTYPE` tinyint(1) NOT NULL default '0' COMMENT 'Type: 0 - Snippet, 1 - Upper Menu, 2 - Category, 3 - Manual',
  `OXACTIVE` tinyint(1) NOT NULL default '0' COMMENT 'Active (multilanguage)',
  `OXACTIVE_1` tinyint(1) NOT NULL default '0' COMMENT '',
  `OXPOSITION` varchar(32) NOT NULL default '' COMMENT 'Position',
  `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXCONTENT` text NOT NULL COMMENT 'Content (multilanguage)',
  `OXTITLE_1` varchar(255) NOT NULL default '' COMMENT '',
  `OXCONTENT_1` text NOT NULL,
  `OXACTIVE_2` tinyint(1) NOT NULL default '0' ,
  `OXTITLE_2` varchar(255) NOT NULL default '' ,
  `OXCONTENT_2` text NOT NULL ,
  `OXACTIVE_3` tinyint(1) NOT NULL default '0' ,
  `OXTITLE_3` varchar(255) NOT NULL default '' ,
  `OXCONTENT_3` text NOT NULL,
  `OXCATID` varchar(32) character set latin1 collate latin1_general_ci default NULL COMMENT 'Category id (oxcategories), used only when type = 2',
  `OXFOLDER` varchar(32) NOT NULL default '' COMMENT 'Content Folder (available options at oxconfig.OXVARNAME = aCMSfolder)',
  `OXTERMVERSION` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Term and Conditions version (used only when OXLOADID = oxagb)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  UNIQUE KEY `OXLOADID` (`OXLOADID`),
  INDEX `cat_search` ( `OXTYPE` , `OXSHOPID` , `OXSNIPPET` , `OXCATID` )
) ENGINE=MyISAM COMMENT 'Content pages (Snippets, Menu, Categories, Manual)';

#
# Table structure for table `oxcounters`
#

DROP TABLE IF EXISTS `oxcounters`;

CREATE TABLE  `oxcounters` (
  `OXIDENT` CHAR( 32 ) NOT NULL COMMENT 'Counter id',
  `OXCOUNT` INT NOT NULL COMMENT 'Counted number',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY (  `OXIDENT` )
) ENGINE = InnoDB COMMENT 'Shop counters';

#
# Table structure for table `oxcountry`
#

DROP TABLE IF EXISTS `oxcountry`;

CREATE TABLE `oxcountry` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Country id',
  `OXACTIVE` tinyint(1) NOT NULL default '0' COMMENT 'Active',
  `OXTITLE` char(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXISOALPHA2` char(2) NOT NULL default '' COMMENT 'ISO 3166-1 alpha-2',
  `OXISOALPHA3` char(3) NOT NULL default '' COMMENT 'ISO 3166-1 alpha-3',
  `OXUNNUM3` char(3) NOT NULL default '' COMMENT 'ISO 3166-1 numeric',
  `OXVATINPREFIX` char(2) NOT NULL default '' COMMENT 'VAT identification number prefix',
  `OXORDER` int(11) NOT NULL default '9999' COMMENT 'Sorting',
  `OXSHORTDESC` char(128) NOT NULL default '' COMMENT 'Short description (multilanguage)',
  `OXLONGDESC` char(255) NOT NULL default '' COMMENT 'Long description (multilanguage)',
  `OXTITLE_1` char(128) NOT NULL default '',
  `OXTITLE_2` char(128) NOT NULL default '',
  `OXTITLE_3` char(128) NOT NULL default '',
  `OXSHORTDESC_1` char(128) NOT NULL default '',
  `OXSHORTDESC_2` char(128) NOT NULL default '',
  `OXSHORTDESC_3` char(128) NOT NULL default '',
  `OXLONGDESC_1` char(255) NOT NULL,
  `OXLONGDESC_2` char(255) NOT NULL,
  `OXLONGDESC_3` char(255) NOT NULL,
  `OXVATSTATUS` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Vat status: 0 - Do not bill VAT, 1 - Do not bill VAT only if provided valid VAT ID',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY (`OXACTIVE`)
) ENGINE=MyISAM COMMENT 'Countries list';

#
# Table structure for table `oxdel2delset`
#

DROP TABLE IF EXISTS `oxdel2delset`;

CREATE TABLE `oxdel2delset` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXDELID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Shipping cost rule id (oxdelivery)',
  `OXDELSETID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Delivery method id (oxdeliveryset)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXDELID` (`OXDELID`)
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between Shipping cost rules (oxdelivery) and delivery methods (oxdeliveryset)';

#
# Table structure for table `oxdelivery`
#

DROP TABLE IF EXISTS `oxdelivery`;

CREATE TABLE `oxdelivery` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Delivery shipping cost rule id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXACTIVE` tinyint(1) NOT NULL default '0' COMMENT 'Active',
  `OXACTIVEFROM` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Active from specified date',
  `OXACTIVETO` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Active to specified date',
  `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXTITLE_1` varchar(255) NOT NULL default '',
  `OXTITLE_2` varchar(255) NOT NULL default '',
  `OXTITLE_3` varchar(255) NOT NULL default '',
  `OXADDSUMTYPE` enum('%','abs') NOT NULL default 'abs' COMMENT 'Price Surcharge/Reduction type (abs|%)',
  `OXADDSUM` double NOT NULL default '0' COMMENT 'Price Surcharge/Reduction amount',
  `OXDELTYPE` enum('a','s','w','p') NOT NULL default 'a' COMMENT 'Condition type: a - Amount, s - Size, w - Weight, p - Price',
  `OXPARAM` double NOT NULL default '0' COMMENT 'Condition param from (e.g. amount from 1)',
  `OXPARAMEND` double NOT NULL default '0' COMMENT 'Condition param to (e.g. amount to 10)',
  `OXFIXED` tinyint(1) NOT NULL default '0' COMMENT 'Calculation Rules: 0 - Once per Cart, 1 - Once for each different product, 2 - For each product',
  `OXSORT` int(11) NOT NULL default '9999' COMMENT 'Order of Rules Processing',
  `OXFINALIZE` tinyint(1) NOT NULL default '0' COMMENT 'Do not run further rules if this rule is valid and is being run',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXSHOPID` (`OXSHOPID`)
)  ENGINE=MyISAM COMMENT 'Delivery shipping cost rules';

#
# Table structure for table `oxdeliveryset`
#

DROP TABLE IF EXISTS `oxdeliveryset`;

CREATE TABLE `oxdeliveryset` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Delivery method id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXACTIVE` tinyint(1) NOT NULL default '0' COMMENT 'Active',
  `OXACTIVEFROM` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Active from specified date',
  `OXACTIVETO` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Active to specified date',
  `OXTITLE` char(255) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXTITLE_1` char(255) NOT NULL default '',
  `OXTITLE_2` char(255) NOT NULL default '',
  `OXTITLE_3` char(255) NOT NULL default '',
  `OXPOS` int(11) NOT NULL default '0' COMMENT 'Sorting',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Creation time',
  PRIMARY KEY  (`OXID`),
  KEY `OXSHOPID` (`OXSHOPID`)
) ENGINE=MyISAM COMMENT 'Delivery (shipping) methods';

#
# Table structure for table `oxdiscount`
#

DROP TABLE IF EXISTS `oxdiscount`;

CREATE TABLE `oxdiscount` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Discount id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXACTIVE` tinyint(1) NOT NULL default '0' COMMENT 'Active',
  `OXACTIVEFROM` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Active from specified date',
  `OXACTIVETO` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Active to specified date',
  `OXTITLE` varchar(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXTITLE_1` varchar( 128 ) NOT NULL,
  `OXTITLE_2` varchar( 128 ) NOT NULL,
  `OXTITLE_3` varchar( 128 ) NOT NULL,
  `OXAMOUNT` double NOT NULL default '0' COMMENT 'Valid from specified amount of articles',
  `OXAMOUNTTO` double NOT NULL default '999999' COMMENT 'Valid to specified amount of articles',
  `OXPRICETO` double NOT NULL default '999999' COMMENT 'Valid to specified purchase price',
  `OXPRICE` double NOT NULL default '0' COMMENT 'Valid from specified purchase price',
  `OXADDSUMTYPE` enum('%','abs','itm') NOT NULL default '%' COMMENT 'Discount type (%,abs,itm)',
  `OXADDSUM` double NOT NULL default '0' COMMENT 'Magnitude of the discount',
  `OXITMARTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Free article id, that will be added as a discount',
  `OXITMAMOUNT` double NOT NULL default '1' COMMENT 'The quantity of free article that will be added to basket with discounted article',
  `OXITMMULTIPLE` int(1) NOT NULL default '0' COMMENT 'Should free article amount be multiplied by discounted item quantity in basket',
  `OXSORT` int(5) NOT NULL default '0' COMMENT 'Sorting',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXSHOPID` (`OXSHOPID`),
  KEY `OXACTIVE` (`OXACTIVE`),
  KEY `OXACTIVEFROM` (`OXACTIVEFROM`),
  KEY `OXACTIVETO` (`OXACTIVETO`)
) ENGINE=MyISAM COMMENT 'Article discounts';

#
# Table structure for table `oxfiles`
#

DROP TABLE IF EXISTS `oxfiles`;

CREATE TABLE IF NOT EXISTS `oxfiles` (
  `OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'File id',
  `OXARTID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Article id (oxarticles)',
  `OXFILENAME` varchar(128) NOT NULL COMMENT 'Filename',
  `OXSTOREHASH` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Hashed filename, used for file directory path creation',
  `OXPURCHASEDONLY` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Download is available only after purchase',
  `OXMAXDOWNLOADS` int(11) NOT NULL default '-1' COMMENT 'Maximum count of downloads after order',
  `OXMAXUNREGDOWNLOADS` int(11) NOT NULL default '-1' COMMENT 'Maximum count of downloads for not registered users after order',
  `OXLINKEXPTIME` int(11) NOT NULL default '-1' COMMENT 'Expiration time of download link in hours',
  `OXDOWNLOADEXPTIME` int(11) NOT NULL default '-1' COMMENT 'Expiration time of download link after the first download in hours',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Creation time',
  PRIMARY KEY (`OXID`),
  KEY `OXARTID` (`OXARTID`)
) ENGINE=MyISAM COMMENT 'Files available for users to download';

#
# Table structure for table `oxgroups`
#

DROP TABLE IF EXISTS `oxgroups`;

CREATE TABLE `oxgroups` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Group id',
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Active',
  `OXTITLE` varchar(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXTITLE_1` varchar(128) NOT NULL default '',
  `OXTITLE_2` varchar(128) NOT NULL default '',
  `OXTITLE_3` varchar(128) NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXACTIVE` (`OXACTIVE`)
) ENGINE=MyISAM COMMENT 'User groups';

#
# Table structure for table `oxinvitations`
# for storing information about invited users
# created 2010-01-06
#

DROP TABLE IF EXISTS `oxinvitations`;

CREATE TABLE IF NOT EXISTS `oxinvitations` (
   `OXUSERID` char(32) collate latin1_general_ci NOT NULL COMMENT 'User id (oxuser), who sent invitation',
   `OXDATE` date NOT NULL COMMENT 'Creation time',
   `OXEMAIL` varchar(255) collate latin1_general_ci NOT NULL COMMENT 'Recipient email',
   `OXPENDING` mediumint(9) NOT NULL COMMENT 'Has recipient user registered',
   `OXACCEPTED` mediumint(9) NOT NULL COMMENT 'Is recipient user accepted',
   `OXTYPE` tinyint(4) NOT NULL default '1' COMMENT 'Invitation type',
   `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
    KEY `OXUSERID` (`OXUSERID`),
    KEY `OXDATE` (`OXDATE`)
) ENGINE=MYISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT 'User sent invitations';

#
# Table structure for table `oxlinks`
#

DROP TABLE IF EXISTS `oxlinks`;

CREATE TABLE `oxlinks` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Link id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXACTIVE` tinyint(1) NOT NULL default '0' COMMENT 'Active',
  `OXURL` varchar(255) NOT NULL default '' COMMENT 'Link url',
  `OXURLDESC` text NOT NULL COMMENT 'Description (multilanguage)',
  `OXURLDESC_1` text NOT NULL,
  `OXURLDESC_2` text NOT NULL,
  `OXURLDESC_3` text NOT NULL,
  `OXINSERT` datetime default NULL COMMENT 'Creation time (set by user)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXSHOPID` (`OXSHOPID`),
  KEY `OXINSERT` (`OXINSERT`),
  KEY `OXACTIVE` (`OXACTIVE`)
) ENGINE=MyISAM COMMENT 'Links';

#
# Table structure for table `oxmanufacturers`
#

DROP TABLE IF EXISTS `oxmanufacturers`;

CREATE TABLE `oxmanufacturers` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Manufacturer id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Is active',
  `OXICON` char(128) NOT NULL default '' COMMENT 'Icon filename',
  `OXTITLE` char(255) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXSHORTDESC` char(255) NOT NULL default '' COMMENT 'Short description (multilanguage)',
  `OXTITLE_1` char(255) NOT NULL default '',
  `OXSHORTDESC_1` char(255) NOT NULL default '',
  `OXTITLE_2` char(255) NOT NULL default '',
  `OXSHORTDESC_2` char(255) NOT NULL default '',
  `OXTITLE_3` char(255) NOT NULL default '',
  `OXSHORTDESC_3` char(255) NOT NULL default '',
  `OXSHOWSUFFIX` tinyint(1) NOT NULL default '1' COMMENT 'Show SEO Suffix in Category',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM COMMENT 'Shop manufacturers';

#
# Table structure for table `oxmediaurls`
# For storing extended file urls
# Created 2008-06-25
#

DROP TABLE IF EXISTS `oxmediaurls`;

CREATE TABLE `oxmediaurls` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Media id',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Article id (oxarticles)',
  `OXURL` varchar(255) NOT NULL COMMENT 'Media url or filename',
  `OXDESC` varchar(255) NOT NULL COMMENT 'Description (multilanguage)',
  `OXDESC_1` varchar(255) NOT NULL,
  `OXDESC_2` varchar(255) NOT NULL,
  `OXDESC_3` varchar(255) NOT NULL,
  `OXISUPLOADED` int(1) NOT NULL default '0' COMMENT 'Is oxurl field used for filename or url',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
 PRIMARY KEY ( `OXID` ) ,
 INDEX ( `OXOBJECTID` )
) ENGINE = MYISAM COMMENT 'Stores objects media';

#
# Table structure for table `oxnews`
#

DROP TABLE IF EXISTS `oxnews`;

CREATE TABLE `oxnews` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'News id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Is active',
  `OXACTIVEFROM` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Active from specified date',
  `OXACTIVETO` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Active to specified date',
  `OXDATE` date NOT NULL default '0000-00-00' COMMENT 'Creation date (entered by user)',
  `OXSHORTDESC` varchar(255) NOT NULL default '' COMMENT 'Short description (multilanguage)',
  `OXLONGDESC` text NOT NULL COMMENT 'Long description (multilanguage)',
  `OXACTIVE_1` tinyint(1) NOT NULL default '0',
  `OXSHORTDESC_1` varchar(255) NOT NULL default '',
  `OXLONGDESC_1` text NOT NULL,
  `OXACTIVE_2` tinyint(1) NOT NULL default '0',
  `OXSHORTDESC_2` varchar(255) NOT NULL default '',
  `OXLONGDESC_2` text NOT NULL,
  `OXACTIVE_3` tinyint(1) NOT NULL default '0',
  `OXSHORTDESC_3` varchar(255) NOT NULL default '',
  `OXLONGDESC_3` text NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXSHOPID` (`OXSHOPID`),
  KEY `OXACTIVE` (`OXACTIVE`),
  KEY `OXACTIVEFROM` (`OXACTIVEFROM`),
  KEY `OXACTIVETO` (`OXACTIVETO`)
) ENGINE=MyISAM COMMENT 'Shop news';

#
# Table structure for table `oxnewsletter`
#

DROP TABLE IF EXISTS `oxnewsletter`;

CREATE TABLE `oxnewsletter` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Newsletter id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Title',
  `OXTEMPLATE` mediumtext NOT NULL COMMENT 'HTML template',
  `OXPLAINTEMPLATE` mediumtext NOT NULL COMMENT 'Plain template',
  `OXSUBJECT` varchar(255) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Subject',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM COMMENT 'Templates for sending newsletters';

#
# Table structure for table `oxnewssubscribed`
#

DROP TABLE IF EXISTS `oxnewssubscribed`;

CREATE TABLE `oxnewssubscribed` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Subscription id',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id (oxuser)',
  `OXSAL` char(64) NOT NULL default '' COMMENT 'User title prefix (Mr/Mrs)',
  `OXFNAME` char(128) NOT NULL default '' COMMENT 'First name',
  `OXLNAME` char(128) NOT NULL default '' COMMENT 'Last name',
  `OXEMAIL` char(128) NOT NULL default '' COMMENT 'Email',
  `OXDBOPTIN` tinyint(1) NOT NULL default '0' COMMENT 'Subscription status: 0 - not subscribed, 1 - subscribed, 2 - not confirmed',
  `OXEMAILFAILED` tinyint(1) NOT NULL default '0' COMMENT 'Subscription email sending status',
  `OXSUBSCRIBED` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Subscription date',
  `OXUNSUBSCRIBED` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Unsubscription date',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  PRIMARY KEY (`OXID`),
  UNIQUE KEY `OXEMAIL` (`OXEMAIL`),
  KEY `OXUSERID` (`OXUSERID`)
) ENGINE=MyISAM COMMENT 'User subscriptions';

#
# Table structure for table `oxobject2action`
#

DROP TABLE IF EXISTS `oxobject2action`;

CREATE TABLE IF NOT EXISTS `oxobject2action` (
  `OXID` char(32) collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXACTIONID` char(32) collate latin1_general_ci NOT NULL default '' COMMENT 'Action id (oxactions)',
  `OXOBJECTID` char(32) collate latin1_general_ci NOT NULL default '' COMMENT 'Object id (table set by oxclass)',
  `OXCLASS` char(32) collate latin1_general_ci NOT NULL default '' COMMENT 'Object table name',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXACTIONID` (`OXACTIONID`,`OXCLASS`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT 'Shows many-to-many relationship between actions (oxactions) and objects (table set by oxclass)';

#
# Table structure for table `oxobject2article`
#

DROP TABLE IF EXISTS `oxobject2article`;

CREATE TABLE `oxobject2article` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Cross-selling Article id (oxarticles)',
  `OXARTICLENID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Main Article id (oxarticles)',
  `OXSORT` int(5) NOT NULL default '0' COMMENT 'Sorting',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXARTICLENID` (`OXARTICLENID`),
  KEY `OXOBJECTID` (`OXOBJECTID`)
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between cross-selling articles';

#
# Table structure for table `oxobject2attribute`
#

DROP TABLE IF EXISTS `oxobject2attribute`;

CREATE TABLE `oxobject2attribute` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Article id (oxarticles)',
  `OXATTRID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Attribute id (oxattributes)',
  `OXVALUE` char(255) NOT NULL default '' COMMENT 'Attribute value (multilanguage)',
  `OXPOS` int(11) NOT NULL default '9999' COMMENT 'Sorting',
  `OXVALUE_1` char(255) NOT NULL default '',
  `OXVALUE_2` char(255) NOT NULL default '',
  `OXVALUE_3` char(255) NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXATTRID` (`OXATTRID`)
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between articles and attributes';

#
# Table structure for table `oxobject2category`
#

DROP TABLE IF EXISTS `oxobject2category`;

CREATE TABLE `oxobject2category` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Article id (oxarticles)',
  `OXCATNID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Category id (oxcategory)',
  `OXPOS` int(11) NOT NULL default '0' COMMENT 'Sorting',
  `OXTIME` INT( 11 ) DEFAULT 0 NOT NULL COMMENT 'Creation time',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  UNIQUE KEY `OXMAINIDX` (`OXCATNID`,`OXOBJECTID`),
  KEY ( `OXOBJECTID` ),
  KEY (`OXPOS`),
  KEY `OXTIME` (`OXTIME`)
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between articles and categories';

#
# Table structure for table `oxobject2delivery`
#

DROP TABLE IF EXISTS `oxobject2delivery`;

CREATE TABLE `oxobject2delivery` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXDELIVERYID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Delivery id (oxdelivery)',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Object id (table determined by oxtype)',
  `OXTYPE` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Record type',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXDELIVERYID` ( `OXDELIVERYID` , `OXTYPE` )
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between delivery cost rules and objects (table determined by oxtype)';

#
# Table structure for table `oxobject2discount`
#

DROP TABLE IF EXISTS `oxobject2discount`;

CREATE TABLE `oxobject2discount` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXDISCOUNTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Discount id (oxdiscount)',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Object id (table determined by oxtype)',
  `OXTYPE` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Record type',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `oxobjectid` (`OXOBJECTID`),
  KEY `oxdiscidx` (`OXDISCOUNTID`,`OXTYPE`)
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between discounts and objects (table determined by oxtype)';

#
# Table structure for table `oxobject2group`
#

DROP TABLE IF EXISTS `oxobject2group`;

CREATE TABLE `oxobject2group` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id',
  `OXGROUPSID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Group id',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXGROUPSID` (`OXGROUPSID`)
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between users and groups';

#
# Table structure for table `oxobject2list`
#

DROP TABLE IF EXISTS `oxobject2list`;

CREATE TABLE `oxobject2list` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Article id (oxarticles)',
  `OXLISTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Listmania id (oxrecommlists)',
  `OXDESC` text NOT NULL default '' COMMENT 'Description',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXLISTID` (`OXLISTID`)
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between articles and listmania lists';

#
# Table structure for table `oxobject2payment`
#

DROP TABLE IF EXISTS `oxobject2payment`;

CREATE TABLE `oxobject2payment` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXPAYMENTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Payment id (oxpayments)',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Object id (table determined by oxtype)',
  `OXTYPE` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Record type',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY ( `OXOBJECTID` ),
  KEY ( `OXPAYMENTID` )
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between payments and objects (table determined by oxtype)';

#
# Table structure for table `oxobject2selectlist`
#

DROP TABLE IF EXISTS `oxobject2selectlist`;

CREATE TABLE `oxobject2selectlist` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Article id (oxarticles)',
  `OXSELNID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Selection list id (oxselectlist)',
  `OXSORT` int(5) NOT NULL default '0' COMMENT 'Sorting',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  KEY `OXSELNID` (`OXSELNID`)
) ENGINE=MyISAM COMMENT 'Shows many-to-many relationship between articles and selection lists';

#
# Table structure for table `oxobject2seodata`
# For storing SEO meta data
# Created 2010-05-11
#

DROP TABLE IF EXISTS `oxobject2seodata`;

CREATE TABLE `oxobject2seodata` (
  `OXOBJECTID` CHAR( 32 ) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Objects id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXLANG` INT( 2 ) NOT NULL default '0' COMMENT 'Language id',
  `OXKEYWORDS` TEXT NOT NULL COMMENT 'Keywords',
  `OXDESCRIPTION` TEXT NOT NULL COMMENT 'Description',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY ( `OXOBJECTID` , `OXSHOPID` , `OXLANG` )
) ENGINE = MYISAM  COMMENT 'Seo entries';

#
# Table structure for table `oxorder`
#

DROP TABLE IF EXISTS `oxorder`;

CREATE TABLE `oxorder` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Order id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id (oxuser)',
  `OXORDERDATE` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Order date',
  `OXORDERNR` int(11) UNSIGNED NOT NULL default '0' COMMENT 'Order number',
  `OXBILLCOMPANY` varchar(255) NOT NULL default '' COMMENT 'Billing info: Company name',
  `OXBILLEMAIL` varchar(255) NOT NULL default '' COMMENT 'Billing info: Email',
  `OXBILLFNAME` varchar(255) NOT NULL default '' COMMENT 'Billing info: First name',
  `OXBILLLNAME` varchar(255) NOT NULL default '' COMMENT 'Billing info: Last name',
  `OXBILLSTREET` varchar(255) NOT NULL default '' COMMENT 'Billing info: Street name',
  `OXBILLSTREETNR` varchar(16) NOT NULL default '' COMMENT 'Billing info: House number',
  `OXBILLADDINFO` varchar(255) NOT NULL default '' COMMENT 'Billing info: Additional info',
  `OXBILLUSTID` varchar(255) NOT NULL default '' COMMENT 'Billing info: VAT ID No.',
  `OXBILLCITY` varchar(255) NOT NULL default '' COMMENT 'Billing info: City',
  `OXBILLCOUNTRYID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Billing info: Country id (oxcountry)',
  `OXBILLSTATEID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Billing info: US State id (oxstates)',
  `OXBILLZIP` varchar(16) NOT NULL default '' COMMENT 'Billing info: Zip code',
  `OXBILLFON` varchar(128) NOT NULL default '' COMMENT 'Billing info: Phone number',
  `OXBILLFAX` varchar(128) NOT NULL default '' COMMENT 'Billing info: Fax number',
  `OXBILLSAL` varchar(128) NOT NULL default '' COMMENT 'Billing info: User title prefix (Mr/Mrs)',
  `OXDELCOMPANY` varchar(255) NOT NULL default '' COMMENT 'Shipping info: Company name',
  `OXDELFNAME` varchar(255) NOT NULL default '' COMMENT 'Shipping info: First name',
  `OXDELLNAME` varchar(255) NOT NULL default '' COMMENT 'Shipping info: Last name',
  `OXDELSTREET` varchar(255) NOT NULL default '' COMMENT 'Shipping info: Street name',
  `OXDELSTREETNR` varchar(16) NOT NULL default '' COMMENT 'Shipping info: House number',
  `OXDELADDINFO` varchar(255) NOT NULL default '' COMMENT 'Shipping info: Additional info',
  `OXDELCITY` varchar(255) NOT NULL default '' COMMENT 'Shipping info: City',
  `OXDELCOUNTRYID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Shipping info: Country id (oxcountry)',
  `OXDELSTATEID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Shipping info: US State id (oxstates)',
  `OXDELZIP` varchar(16) NOT NULL default '' COMMENT 'Shipping info: Zip code',
  `OXDELFON` varchar(128) NOT NULL default '' COMMENT 'Shipping info: Phone number',
  `OXDELFAX` varchar(128) NOT NULL default '' COMMENT 'Shipping info: Fax number',
  `OXDELSAL` varchar(128) NOT NULL default '' COMMENT 'Shipping info: User title prefix (Mr/Mrs)',
  `OXPAYMENTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User payment id (oxuserpayments)',
  `OXPAYMENTTYPE` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Payment id (oxpayments)',
  `OXTOTALNETSUM` double NOT NULL default '0' COMMENT 'Total net sum',
  `OXTOTALBRUTSUM` double NOT NULL default '0' COMMENT 'Total brut sum',
  `OXTOTALORDERSUM` double NOT NULL default '0' COMMENT 'Total order sum',
  `OXARTVAT1` double NOT NULL default '0' COMMENT 'First VAT',
  `OXARTVATPRICE1` double NOT NULL default '0' COMMENT 'First calculated VAT price',
  `OXARTVAT2` double NOT NULL default '0' COMMENT 'Second VAT',
  `OXARTVATPRICE2` double NOT NULL default '0' COMMENT 'Second calculated VAT price',
  `OXDELCOST` double NOT NULL default '0' COMMENT 'Delivery price',
  `OXDELVAT` double NOT NULL default '0' COMMENT 'Delivery VAT',
  `OXPAYCOST` double NOT NULL default '0' COMMENT 'Payment cost',
  `OXPAYVAT` double NOT NULL default '0' COMMENT 'Payment VAT',
  `OXWRAPCOST` double NOT NULL default '0' COMMENT 'Wrapping cost',
  `OXWRAPVAT` double NOT NULL default '0' COMMENT 'Wrapping VAT',
  `OXGIFTCARDCOST` double NOT NULL default '0' COMMENT 'Giftcard cost',
  `OXGIFTCARDVAT` double NOT NULL default '0' COMMENT 'Giftcard VAT',
  `OXCARDID` varchar( 32 ) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Gift card id (oxwrapping)',
  `OXCARDTEXT` text NOT NULL COMMENT 'Gift card text',
  `OXDISCOUNT` double NOT NULL default '0' COMMENT 'Additional discount for order (abs)',
  `OXEXPORT` tinyint(4) NOT NULL default '0' COMMENT 'Is exported',
  `OXBILLNR` varchar(128) NOT NULL default '' COMMENT 'Invoice No.',
  `OXBILLDATE` date NOT NULL default '0000-00-00' COMMENT 'Invoice sent date',
  `OXTRACKCODE` varchar(128) NOT NULL default '' COMMENT 'Tracking code',
  `OXSENDDATE` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Order shipping date',
  `OXREMARK` text NOT NULL COMMENT 'User remarks',
  `OXVOUCHERDISCOUNT` double NOT NULL default '0' COMMENT 'Coupon (voucher) discount price',
  `OXCURRENCY` varchar(32) NOT NULL default '' COMMENT 'Currency',
  `OXCURRATE` double NOT NULL default '0' COMMENT 'Currency rate',
  `OXFOLDER` varchar(32) NOT NULL default '' COMMENT 'Folder: ORDERFOLDER_FINISHED, ORDERFOLDER_NEW, ORDERFOLDER_PROBLEMS',
  `OXTRANSID` varchar(64) NOT NULL default '' COMMENT 'Paypal: Transaction id',
  `OXPAYID` varchar(64) character set latin1 collate latin1_general_ci NOT NULL default '',
  `OXXID` varchar(64) NOT NULL default '',
  `OXPAID` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Time, when order was paid',
  `OXSTORNO` tinyint(1) NOT NULL default '0' COMMENT 'Order cancelled',
  `OXIP` varchar(39) NOT NULL default '' COMMENT 'User ip address',
  `OXTRANSSTATUS` varchar(30) NOT NULL default '' COMMENT 'Order status: NOT_FINISHED, OK, ERROR',
  `OXLANG` int(2) NOT NULL default '0' COMMENT 'Language id',
  `OXINVOICENR` int(11) NOT NULL default '0' COMMENT 'Invoice number',
  `OXDELTYPE` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Delivery id (oxdeliveryset)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `OXISNETTOMODE` tinyint(1) UNSIGNED NOT NULL DEFAULT  '0' COMMENT 'Order created in netto mode',
  PRIMARY KEY  (`OXID`),
  KEY `MAINIDX` (`OXSHOPID`,`OXSTORNO`,`OXORDERDATE`)
) ENGINE=InnoDB COMMENT 'Shop orders information';

#
# Table structure for table `oxorderarticles`
#

DROP TABLE IF EXISTS `oxorderarticles`;

CREATE TABLE `oxorderarticles` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Order article id',
  `OXORDERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Order id (oxorder)',
  `OXAMOUNT` double NOT NULL default '0' COMMENT 'Amount',
  `OXARTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Article id (oxarticles)',
  `OXARTNUM` varchar(255) NOT NULL default '' COMMENT 'Article number',
  `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Title',
  `OXSHORTDESC` varchar(255) NOT NULL default '' COMMENT 'Short description',
  `OXSELVARIANT` varchar(255) NOT NULL default '' COMMENT 'Selected variant',
  `OXNETPRICE` double NOT NULL default '0' COMMENT 'Full netto price (oxnprice * oxamount)',
  `OXBRUTPRICE` double NOT NULL default '0' COMMENT 'Full brutto price (oxbprice * oxamount)',
  `OXVATPRICE` double NOT NULL default '0' COMMENT 'Calculated VAT price',
  `OXVAT` double NOT NULL default '0' COMMENT 'VAT',
  `OXPERSPARAM` text NOT NULL COMMENT 'Serialized persistent parameters',
  `OXPRICE` double NOT NULL default '0' COMMENT 'Base price',
  `OXBPRICE` double NOT NULL default '0' COMMENT 'Brutto price for one item',
  `OXNPRICE` double NOT NULL default '0' COMMENT 'Netto price for one item',
  `OXWRAPID` varchar( 32 ) NOT NULL default '' COMMENT 'Wrapping id (oxwrapping)',
  `OXEXTURL` varchar(255) NOT NULL default '' COMMENT 'External URL to other information about the article',
  `OXURLDESC` varchar(255) NOT NULL default '' COMMENT 'Text for external URL',
  `OXURLIMG` varchar(128) NOT NULL default '' COMMENT 'External URL image',
  `OXTHUMB` varchar(128) NOT NULL default '' COMMENT 'Thumbnail filename',
  `OXPIC1` varchar(128) NOT NULL default '' COMMENT '1# Picture filename',
  `OXPIC2` varchar(128) NOT NULL default '' COMMENT '2# Picture filename',
  `OXPIC3` varchar(128) NOT NULL default '' COMMENT '3# Picture filename',
  `OXPIC4` varchar(128) NOT NULL default '' COMMENT '4# Picture filename',
  `OXPIC5` varchar(128) NOT NULL default '' COMMENT '5# Picture filename',
  `OXWEIGHT` double NOT NULL default '0' COMMENT 'Weight (kg)',
  `OXSTOCK` double NOT NULL default '-1' COMMENT 'Articles quantity in stock',
  `OXDELIVERY` date NOT NULL default '0000-00-00' COMMENT 'Date, when the product will be available again if it is sold out',
  `OXINSERT` date NOT NULL default '0000-00-00' COMMENT 'Creation time',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `OXLENGTH` double NOT NULL default '0' COMMENT 'Article dimensions: Length',
  `OXWIDTH` double NOT NULL default '0' COMMENT 'Article dimensions: Width',
  `OXHEIGHT` double NOT NULL default '0' COMMENT 'Article dimensions: Height',
  `OXFILE` varchar(128) NOT NULL default '' COMMENT 'File, shown in article media list',
  `OXSEARCHKEYS` varchar(255) NOT NULL default '' COMMENT 'Search terms',
  `OXTEMPLATE` varchar(128) NOT NULL default '' COMMENT 'Alternative template filename (use default, if empty)',
  `OXQUESTIONEMAIL` varchar(255) NOT NULL default '' COMMENT 'E-mail for question',
  `OXISSEARCH` tinyint(1) NOT NULL default '1' COMMENT 'Is article shown in search',
  `OXFOLDER` char(32) NOT NULL default '' COMMENT 'Folder: ORDERFOLDER_FINISHED, ORDERFOLDER_NEW, ORDERFOLDER_PROBLEMS',
  `OXSUBCLASS` char(32) NOT NULL default '' COMMENT 'Subclass',
  `OXSTORNO` tinyint(1) NOT NULL default '0' COMMENT 'Order cancelled',
  `OXORDERSHOPID` int(11) NOT NULL default 1 COMMENT 'Shop id (oxshops), in which order was done',
  `OXISBUNDLE` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Bundled article',
  PRIMARY KEY  (`OXID`),
  KEY `OXORDERID` (`OXORDERID`),
  KEY `OXARTID` (`OXARTID`),
  KEY `OXARTNUM` (`OXARTNUM`)
) ENGINE=InnoDB COMMENT 'Ordered articles information';

#
# Table structure for table `oxorderfiles`
#

DROP TABLE IF EXISTS `oxorderfiles`;

CREATE TABLE IF NOT EXISTS `oxorderfiles` (
  `OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Order file id',
  `OXORDERID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Order id (oxorder)',
  `OXFILENAME` varchar(128) NOT NULL COMMENT 'Filename',
  `OXFILEID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'File id (oxfiles)',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXORDERARTICLEID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Ordered article id (oxorderarticles)',
  `OXFIRSTDOWNLOAD` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'First time downloaded time',
  `OXLASTDOWNLOAD` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last time downloaded time',
  `OXDOWNLOADCOUNT` int(10) unsigned NOT NULL COMMENT 'Downloads count',
  `OXMAXDOWNLOADCOUNT` int(10) unsigned NOT NULL COMMENT 'Maximum count of downloads',
  `OXDOWNLOADEXPIRATIONTIME` int(10) unsigned NOT NULL COMMENT 'Download expiration time in hours',
  `OXLINKEXPIRATIONTIME` int(10) unsigned NOT NULL COMMENT 'Link expiration time in hours',
  `OXRESETCOUNT` int(10) unsigned NOT NULL COMMENT 'Count of resets',
  `OXVALIDUNTIL` datetime NOT NULL COMMENT 'Download is valid until time specified',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY (`OXID`),
  KEY `OXORDERID` (`OXORDERID`),
  KEY `OXFILEID` (`OXFILEID`),
  KEY `OXORDERARTICLEID` (`OXORDERARTICLEID`)
) ENGINE=InnoDB COMMENT 'Files, given to users to download after order';

#
# Table structure for table `oxpayments`
#

DROP TABLE IF EXISTS `oxpayments`;

CREATE TABLE `oxpayments` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Payment id',
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Active',
  `OXDESC` varchar(128) NOT NULL default '' COMMENT 'Description (multilanguage)',
  `OXADDSUM` double NOT NULL default '0' COMMENT 'Price Surcharge/Reduction amount',
  `OXADDSUMTYPE` enum('abs','%') NOT NULL default 'abs' COMMENT 'Price Surcharge/Reduction type (abs|%)',
  `OXADDSUMRULES` int(11) NOT NULL default '0' COMMENT 'Base of price surcharge/reduction: 1 - Value of all goods in cart, 2 - Discounts, 4 - Vouchers, 8 - Shipping costs, 16 - Gift Wrapping/Greeting Card',
  `OXFROMBONI` int(11) NOT NULL default '0' COMMENT 'Minimal Credit Rating ',
  `OXFROMAMOUNT` double NOT NULL default '0' COMMENT 'Purchase Price: From',
  `OXTOAMOUNT` double NOT NULL default '0' COMMENT 'Purchase Price: To',
  `OXVALDESC` text NOT NULL COMMENT 'Payment additional fields, separated by "field1__@@field2" (multilanguage)',
  `OXCHECKED` tinyint(1) NOT NULL default '0' COMMENT 'Selected as the default method',
  `OXDESC_1` varchar(128) NOT NULL default '',
  `OXVALDESC_1` text NOT NULL,
  `OXDESC_2` varchar(128) NOT NULL default '',
  `OXVALDESC_2` text NOT NULL,
  `OXDESC_3` varchar(128) NOT NULL default '',
  `OXVALDESC_3` text NOT NULL,
  `OXLONGDESC` text NOT NULL default '' COMMENT 'Long description (multilanguage)',
  `OXLONGDESC_1` text NOT NULL default '',
  `OXLONGDESC_2` text NOT NULL default '',
  `OXLONGDESC_3` text NOT NULL default '',
  `OXSORT` int(5) NOT NULL default 0 COMMENT 'Sorting',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXACTIVE` (`OXACTIVE`)
) ENGINE=MyISAM COMMENT 'Payment methods';

#
# Table structure for table `oxprice2article`
#

DROP TABLE IF EXISTS `oxprice2article`;

CREATE TABLE `oxprice2article` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXARTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Article id (oxarticles)',
  `OXADDABS` double NOT NULL default '0' COMMENT 'Price, that will be used for specified article if basket amount is between oxamount and oxamountto',
  `OXADDPERC` double NOT NULL default '0' COMMENT 'Discount, that will be used for specified article if basket amount is between oxamount and oxamountto',
  `OXAMOUNT` double NOT NULL default '0' COMMENT 'Quantity: From',
  `OXAMOUNTTO` double NOT NULL default '0' COMMENT 'Quantity: To',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXSHOPID` (`OXSHOPID`),
 KEY `OXARTID` (`OXARTID`)
) ENGINE=MyISAM COMMENT 'Article scale prices';

#
# Table structure for table `oxpricealarm`
#

DROP TABLE IF EXISTS `oxpricealarm`;

CREATE TABLE `oxpricealarm` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Price alarm id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id (oxuser)',
  `OXEMAIL` varchar(128) NOT NULL default '' COMMENT 'Recipient email',
  `OXARTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Article id (oxarticles)',
  `OXPRICE` double NOT NULL default '0' COMMENT 'Expected (user) price, when notification email should be sent',
  `OXCURRENCY` varchar(32) NOT NULL default '' COMMENT 'Currency',
  `OXLANG` INT(2) NOT NULL default 0 COMMENT 'Language id',
  `OXINSERT` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Creation time',
  `OXSENDED` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Time, when notification was sent',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY (`OXID`)
) ENGINE=MyISAM COMMENT 'Price fall alarm requests';

#
# Table structure for table `oxratings`
#

DROP TABLE IF EXISTS `oxratings`;

CREATE TABLE `oxratings` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Rating id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id (oxuser)',
  `OXTYPE` enum('oxarticle','oxrecommlist') NOT NULL COMMENT 'Rating type (oxarticle, oxrecommlist)',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Article or Listmania id (oxarticles or oxrecommlists)',
  `OXRATING` int(1) NOT NULL default '0' COMMENT 'Rating',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `oxobjectsearch` (`OXTYPE`,`OXOBJECTID`)
) ENGINE=MyISAM COMMENT 'Articles and Listmania ratings';
#
# Table structure for table `oxrecommlists`
#

DROP TABLE IF EXISTS `oxrecommlists`;

CREATE TABLE `oxrecommlists` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Listmania id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id (oxuser)',
  `OXAUTHOR` varchar(255) NOT NULL default '' COMMENT 'Author first and last name',
  `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Title',
  `OXDESC` text NOT NULL COMMENT 'Description',
  `OXRATINGCNT` int(11) NOT NULL default '0' COMMENT 'Rating votes count',
  `OXRATING` double NOT NULL default '0' COMMENT 'Rating',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM COMMENT 'Listmania';

#
# Table structure for table `oxremark`
#

DROP TABLE IF EXISTS `oxremark`;

CREATE TABLE `oxremark` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXPARENTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id (oxuser)',
  `OXTYPE` enum('o','r','n','c') NOT NULL default 'r' COMMENT 'Record type: o - order, r - remark, n - nesletter, c - registration',
  `OXHEADER` varchar(255) NOT NULL default '' COMMENT 'Header (default: Creation time)',
  `OXTEXT` text NOT NULL COMMENT 'Remark text',
  `OXCREATE` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Creation time',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXPARENTID` (`OXPARENTID`),
  KEY `OXTYPE` (`OXTYPE`)
) ENGINE=MyISAM COMMENT 'User History';

#
# Table structure for table `oxreviews`
#

DROP TABLE IF EXISTS `oxreviews`;

CREATE TABLE `oxreviews` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Review id',
  `OXACTIVE` tinyint(1) NOT NULL default '0' COMMENT 'Active',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Article or Listmania id (oxarticles or oxrecommlist)',
  `OXTYPE` enum('oxarticle','oxrecommlist') NOT NULL COMMENT 'Review type (oxarticle, oxrecommlist)',
  `OXTEXT` text NOT NULL COMMENT 'Review text',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id (oxuser)',
  `OXCREATE` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Creation time',
  `OXLANG` tinyint( 3 ) NOT NULL DEFAULT '0' COMMENT 'Language id',
  `OXRATING` int(1) NOT NULL default '0' COMMENT 'Rating',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `oxobjectsearch` (`OXTYPE`,`OXOBJECTID`)
) ENGINE=MyISAM COMMENT 'Articles and Listmania reviews';

#
# Table structure for table `oxselectlist`
#

DROP TABLE IF EXISTS `oxselectlist`;

CREATE TABLE `oxselectlist` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Selection list id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXTITLE` varchar(254) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXIDENT` varchar(255) NOT NULL default '' COMMENT 'Working Title',
  `OXVALDESC` text NOT NULL COMMENT 'List fields, separated by "[field_name]!P![price]__@@[field_name]__@@" (multilanguage)',
  `OXTITLE_1` varchar(255) NOT NULL default '',
  `OXVALDESC_1` text NOT NULL,
  `OXTITLE_2` varchar(255) NOT NULL default '',
  `OXVALDESC_2` text NOT NULL,
  `OXTITLE_3` varchar(255) NOT NULL default '',
  `OXVALDESC_3` text NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM COMMENT 'Selection lists';

#
# Table structure for table `oxseo`
# Created 2008.04.16
#

DROP TABLE IF EXISTS `oxseo`;

CREATE TABLE `oxseo` (
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Object id',
  `OXIDENT`    char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Hashed seo url (md5)',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXLANG`     int(2) NOT NULL default 0 COMMENT 'Language id',
  `OXSTDURL`   varchar(2048) NOT NULL COMMENT 'Primary url, not seo encoded',
  `OXSEOURL`   varchar(2048) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL COMMENT 'Old seo url',
  `OXTYPE`     enum('static', 'oxarticle', 'oxcategory', 'oxvendor', 'oxcontent', 'dynamic', 'oxmanufacturer') NOT NULL COMMENT 'Record type',
  `OXFIXED`    TINYINT(1) NOT NULL default 0 COMMENT 'Fixed',
  `OXEXPIRED` tinyint(1) NOT NULL default '0' COMMENT 'Expired',
  `OXPARAMS` char(32) NOT NULL default '' COMMENT 'Params',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
   PRIMARY KEY (`OXIDENT`, `OXSHOPID`, `OXLANG`),
   UNIQUE KEY search (`OXTYPE`, `OXOBJECTID`, `OXSHOPID`, `OXLANG`,`OXPARAMS`),
   KEY `OXOBJECTID` (`OXLANG`,`OXOBJECTID`,`OXSHOPID`),
   KEY `SEARCHSTD` (OXSTDURL(100),`OXSHOPID`),
   KEY `SEARCHSEO` (OXSEOURL(100))
) ENGINE=InnoDB COMMENT 'Seo urls information';

#
# Table structure for table `oxseohistory`
# For tracking old SEO urls
# Created 2008-05-21
#

DROP TABLE IF EXISTS `oxseohistory`;

CREATE TABLE `oxseohistory` (
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Object id',
  `OXIDENT` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Hashed url (md5)',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXLANG` int(2) NOT NULL default '0' COMMENT 'Language id',
  `OXHITS` bigint(20) NOT NULL default '0' COMMENT 'Hits',
  `OXINSERT` timestamp NULL default NULL COMMENT 'Creation time',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXIDENT`,`OXSHOPID`,`OXLANG`),
  KEY `search` (`OXOBJECTID`,`OXSHOPID`,`OXLANG`)
) ENGINE=InnoDB COMMENT 'Seo urls history. If url does not exists in oxseo, then checks here and redirects';

#
# Table structure for table `oxseologs`
# For tracking untranslatable to SEO format non SEO urls
# Created 2008-10-21
#

DROP TABLE IF EXISTS `oxseologs`;

CREATE TABLE IF NOT EXISTS `oxseologs` (
  `OXSTDURL` text NOT NULL COMMENT 'Primary url, not seo encoded',
  `OXIDENT` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Hashed seo url',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXLANG` int(11) NOT NULL COMMENT 'Language id',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXIDENT`,`OXSHOPID`,`OXLANG`)
) ENGINE=InnoDB COMMENT 'Seo logging. Logs bad requests';

#
# Table structure for table `oxshops`
#

DROP TABLE IF EXISTS `oxshops`;

CREATE TABLE `oxshops` (
  `OXID` int(11) NOT NULL default 1 COMMENT 'Shop id',
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Active',
  `OXPRODUCTIVE` tinyint(1) NOT NULL default '0' COMMENT 'Productive Mode (if 0, debug info displayed)',
  `OXDEFCURRENCY` char(32) NOT NULL default '' COMMENT 'Default currency',
  `OXDEFLANGUAGE` int(11) NOT NULL default '0' COMMENT 'Default language id',
  `OXNAME` varchar(255) NOT NULL default '' COMMENT 'Shop name',
  `OXTITLEPREFIX` varchar(255) NOT NULL default '' COMMENT 'Seo title prefix (multilanguage)',
  `OXTITLEPREFIX_1` varchar(255) NOT NULL default '',
  `OXTITLEPREFIX_2` varchar(255) NOT NULL default '',
  `OXTITLEPREFIX_3` varchar(255) NOT NULL default '',
  `OXTITLESUFFIX` varchar(255) NOT NULL default '' COMMENT 'Seo title suffix (multilanguage)',
  `OXTITLESUFFIX_1` varchar(255) NOT NULL default '',
  `OXTITLESUFFIX_2` varchar(255) NOT NULL default '',
  `OXTITLESUFFIX_3` varchar(255) NOT NULL default '',
  `OXSTARTTITLE` varchar(255) NOT NULL default '' COMMENT 'Start page title (multilanguage)',
  `OXSTARTTITLE_1` varchar(255) NOT NULL default '',
  `OXSTARTTITLE_2` varchar(255) NOT NULL default '',
  `OXSTARTTITLE_3` varchar(255) NOT NULL default '',
  `OXINFOEMAIL` varchar(255) NOT NULL default '' COMMENT 'Informational email address',
  `OXORDEREMAIL` varchar(255) NOT NULL default '' COMMENT 'Order email address',
  `OXOWNEREMAIL` varchar(255) NOT NULL default '' COMMENT 'Owner email address',
  `OXORDERSUBJECT` varchar(255) NOT NULL default '' COMMENT 'Order email subject (multilanguage)',
  `OXREGISTERSUBJECT` varchar(255) NOT NULL default '' COMMENT 'Registration email subject (multilanguage)',
  `OXFORGOTPWDSUBJECT` varchar(255) NOT NULL default '' COMMENT 'Forgot password email subject (multilanguage)',
  `OXSENDEDNOWSUBJECT` varchar(255) NOT NULL default '' COMMENT 'Order sent email subject (multilanguage)',
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
  `OXSMTP` varchar(255) NOT NULL default '' COMMENT 'SMTP server',
  `OXSMTPUSER` varchar(128) NOT NULL default '' COMMENT 'SMTP user',
  `OXSMTPPWD` varchar(128) NOT NULL default '' COMMENT 'SMTP password',
  `OXCOMPANY` varchar(128) NOT NULL default '' COMMENT 'Your company',
  `OXSTREET` varchar(255) NOT NULL default '' COMMENT 'Street',
  `OXZIP` varchar(255) NOT NULL default '' COMMENT 'ZIP code',
  `OXCITY` varchar(255) NOT NULL default '' COMMENT 'City',
  `OXCOUNTRY` varchar(255) NOT NULL default '' COMMENT 'Country',
  `OXBANKNAME` varchar(255) NOT NULL default '' COMMENT 'Bank name',
  `OXBANKNUMBER` varchar(255) NOT NULL default '' COMMENT 'Account Number',
  `OXBANKCODE` varchar(255) NOT NULL default '' COMMENT 'Routing Number',
  `OXVATNUMBER` varchar(255) NOT NULL default '' COMMENT 'Sales Tax ID',
  `OXTAXNUMBER` varchar(255) NOT NULL default '' COMMENT 'Tax ID',
  `OXBICCODE` varchar(255) NOT NULL default '' COMMENT 'Bank BIC',
  `OXIBANNUMBER` varchar(255) NOT NULL default '' COMMENT 'Bank IBAN',
  `OXFNAME` varchar(255) NOT NULL default '' COMMENT 'First name',
  `OXLNAME` varchar(255) NOT NULL default '' COMMENT 'Last name',
  `OXTELEFON` varchar(255) NOT NULL default '' COMMENT 'Phone number',
  `OXTELEFAX` varchar(255) NOT NULL default '' COMMENT 'Fax number',
  `OXURL` varchar(255) NOT NULL default '' COMMENT 'Shop url',
  `OXDEFCAT` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Default category id',
  `OXHRBNR` varchar(64) NOT NULL default '' COMMENT 'CBR',
  `OXCOURT` varchar(128) NOT NULL default '' COMMENT 'District Court',
  `OXADBUTLERID` varchar(64) NOT NULL default '' COMMENT 'Adbutler code (belboon.de) - deprecated',
  `OXAFFILINETID` varchar(64) NOT NULL default '' COMMENT 'Affilinet code (webmasterplan.com) - deprecated',
  `OXSUPERCLICKSID` varchar(64) NOT NULL default '' COMMENT 'Superclix code (superclix.de) - deprecated',
  `OXAFFILIWELTID` varchar(64) NOT NULL default '' COMMENT 'Affiliwelt code (affiliwelt.net) - deprecated',
  `OXAFFILI24ID` varchar(64) NOT NULL default '' COMMENT 'Affili24 code (affili24.com) - deprecated',
  `OXEDITION` CHAR( 2 ) NOT NULL COMMENT 'Shop Edition (CE,PE,EE)',
  `OXVERSION` CHAR( 16 ) NOT NULL COMMENT 'Shop Version',
  `OXSEOACTIVE` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Seo active (multilanguage)',
  `OXSEOACTIVE_1` tinyint(1) NOT NULL DEFAULT '1',
  `OXSEOACTIVE_2` tinyint(1) NOT NULL DEFAULT '1',
  `OXSEOACTIVE_3` tinyint(1) NOT NULL DEFAULT '1',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXACTIVE` (`OXACTIVE`)
) ENGINE=MyISAM COMMENT 'Shop config';

#
# Table structure for table `oxstates`
# for storing extended file urls
# created 2010-01-06
#

DROP TABLE IF EXISTS `oxstates`;

CREATE TABLE `oxstates` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'State id',
  `OXCOUNTRYID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Country id (oxcountry)',
  `OXTITLE` char(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXISOALPHA2` char(2) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'SEO short name',
  `OXTITLE_1` char(128) NOT NULL default '',
  `OXTITLE_2` char(128) NOT NULL default '',
  `OXTITLE_3` char(128) NOT NULL default '',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY(`OXCOUNTRYID`)
) ENGINE = MYISAM COMMENT 'US States list';

#
# Table structure for table `oxtplblocks`
# for storing blocks for template parts override
# created 2010-10-12
#

DROP TABLE IF EXISTS `oxtplblocks`;

CREATE TABLE `oxtplblocks` (
  `OXID`        char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Block id',
  `OXACTIVE`    tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Is active',
  `OXSHOPID`    int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXTHEME`     char(128) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Shop theme id',
  `OXTEMPLATE`  char(255) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Template filename (with rel. path), where block is located',
  `OXBLOCKNAME` char(128) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Block name',
  `OXPOS`       int  NOT NULL COMMENT 'Sorting',
  `OXFILE`      char(255) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Module template filename, where block replacement is located',
  `OXMODULE`    char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Module, which uses this template',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY (`OXID`),
  INDEX `search` (`OXACTIVE`, `OXTEMPLATE`, `OXPOS`),
  INDEX `oxtheme` (`OXTHEME`)
) ENGINE=MyISAM COMMENT 'Module template blocks';

#
# Table structure for table `oxuser`
#

DROP TABLE IF EXISTS `oxuser`;

CREATE TABLE `oxuser` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'User id',
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Is active',
  `OXRIGHTS` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User rights: user, malladmin',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXUSERNAME` varchar(255) NOT NULL default '' COMMENT 'Username',
  `OXPASSWORD` varchar(128) NOT NULL default '' COMMENT 'Hashed password',
  `OXPASSSALT` char(128) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Password salt',
  `OXCUSTNR` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Customer number',
  `OXUSTID` varchar(255) NOT NULL default '' COMMENT 'VAT ID No.',
  `OXCOMPANY` varchar(255) NOT NULL default '' COMMENT 'Company',
  `OXFNAME` varchar(255) NOT NULL default '' COMMENT 'First name',
  `OXLNAME` varchar(255) NOT NULL default '' COMMENT 'Last name',
  `OXSTREET` varchar(255) NOT NULL default '' COMMENT 'Street',
  `OXSTREETNR` varchar(16) NOT NULL default '' COMMENT 'House number',
  `OXADDINFO` varchar(255) NOT NULL default '' COMMENT 'Additional info',
  `OXCITY` varchar(255) NOT NULL default '' COMMENT 'City',
  `OXCOUNTRYID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Country id (oxcountry)',
  `OXSTATEID` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'State id (oxstates)',
  `OXZIP` varchar(16) NOT NULL default '' COMMENT 'ZIP code',
  `OXFON` varchar(128) NOT NULL default '' COMMENT 'Phone number',
  `OXFAX` varchar(128) NOT NULL default '' COMMENT 'Fax number',
  `OXSAL` varchar(128) NOT NULL default '' COMMENT 'User title (Mr/Mrs)',
  `OXBONI` int(11) NOT NULL default '0' COMMENT 'Credit points',
  `OXCREATE` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Creation time',
  `OXREGISTER` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Registration time',
  `OXPRIVFON` varchar(64) NOT NULL default '' COMMENT 'Personal phone number',
  `OXMOBFON` varchar(64) NOT NULL default '' COMMENT 'Mobile phone number',
  `OXBIRTHDATE` date NOT NULL default '0000-00-00' COMMENT 'Birthday date',
  `OXURL` varchar(255) NOT NULL default '' COMMENT 'Url',
  `OXUPDATEKEY` char( 32 ) NOT NULL default '' COMMENT 'Update key',
  `OXUPDATEEXP` int(11) NOT NULL default '0' COMMENT 'Update key expiration time',
  `OXPOINTS` double NOT NULL default '0' COMMENT 'User points (for registration, invitation, etc)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  UNIQUE `OXUSERNAME` (`OXUSERNAME`, `OXSHOPID`),
  KEY `OXPASSWORD` (`OXPASSWORD`),
  KEY `OXCUSTNR` (`OXCUSTNR`),
  KEY `OXACTIVE` (`OXACTIVE`),
  KEY `OXLNAME` (`OXLNAME`),
  KEY `OXUPDATEEXP` (`OXUPDATEEXP`)
) ENGINE=MyISAM COMMENT 'Shop administrators and users';

#
# Table structure for table `oxuserbaskets`
#

DROP TABLE IF EXISTS `oxuserbaskets`;

CREATE TABLE `oxuserbaskets` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Basket id',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id (oxuser)',
  `OXTITLE` varchar(255) NOT NULL default '' COMMENT 'Basket title',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `OXPUBLIC` tinyint(1) DEFAULT '1' NOT NULL COMMENT 'Is public',
  `OXUPDATE` INT NOT NULL default 0 COMMENT 'Update timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXUPDATE` (`OXUPDATE`),
  KEY `OXTITLE` (`OXTITLE`),
  KEY `OXUSERID` (`OXUSERID`)
) ENGINE=InnoDB COMMENT 'Active User baskets';

#
# Table structure for table `oxuserbasketitems`
#

DROP TABLE IF EXISTS `oxuserbasketitems`;

CREATE TABLE `oxuserbasketitems` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Item id',
  `OXBASKETID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Basket id (oxuserbaskets)',
  `OXARTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Article id (oxarticles)',
  `OXAMOUNT` char(32) NOT NULL default '' COMMENT 'Amount',
  `OXSELLIST` varchar(255) NOT NULL default '' COMMENT 'Selection list',
  `OXPERSPARAM` text NOT NULL COMMENT 'Serialized persistent parameters',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXBASKETID` (`OXBASKETID`),
  KEY `OXARTID` (`OXARTID`)
) ENGINE=InnoDB COMMENT 'User basket items';

#
# Table structure for table `oxuserpayments`
#

DROP TABLE IF EXISTS `oxuserpayments`;

CREATE TABLE `oxuserpayments` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Payment id',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id (oxusers)',
  `OXPAYMENTSID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Payment id (oxpayments)',
  `OXVALUE` blob NOT NULL COMMENT 'DYN payment values array as string',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXUSERID` (`OXUSERID`)
) ENGINE=InnoDB COMMENT 'User payments';

#
# Table structure for table `oxvendor`
#

DROP TABLE IF EXISTS `oxvendor`;

CREATE TABLE `oxvendor` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Vendor id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Active',
  `OXICON` char(128) NOT NULL default '' COMMENT 'Icon filename',
  `OXTITLE` char(255) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXSHORTDESC` char(255) NOT NULL default '' COMMENT 'Short description (multilanguage)',
  `OXTITLE_1` char(255) NOT NULL default '',
  `OXSHORTDESC_1` char(255) NOT NULL default '',
  `OXTITLE_2` char(255) NOT NULL default '',
  `OXSHORTDESC_2` char(255) NOT NULL default '',
  `OXTITLE_3` char(255) NOT NULL default '',
  `OXSHORTDESC_3` char(255) NOT NULL default '',
  `OXSHOWSUFFIX` tinyint(1) NOT NULL default '1' COMMENT 'Show SEO Suffix in Category',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXACTIVE` (`OXACTIVE`)
) ENGINE=MyISAM COMMENT 'Distributors list';

#
# Table structure for table `oxvouchers`
#

DROP TABLE IF EXISTS `oxvouchers` ;

CREATE  TABLE IF NOT EXISTS `oxvouchers` (
  `OXDATEUSED` DATE NULL DEFAULT NULL COMMENT 'Date, when coupon was used (set on order complete)',
  `OXORDERID` char(32) character set latin1 collate latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Order id (oxorder)',
  `OXUSERID` char(32) character set latin1 collate latin1_general_ci NOT NULL DEFAULT '' COMMENT 'User id (oxuser)',
  `OXRESERVED` INT(11) NOT NULL DEFAULT 0 COMMENT 'Time, when coupon is added to basket',
  `OXVOUCHERNR` varchar(255) NOT NULL DEFAULT '' COMMENT 'Coupon number',
  `OXVOUCHERSERIEID` char(32) character set latin1 collate latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Coupon Series id (oxvoucherseries)',
  `OXDISCOUNT` FLOAT(9,2) NULL DEFAULT NULL COMMENT 'Discounted amount (if discount was used)',
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Coupon id',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  INDEX OXVOUCHERSERIEID (`OXVOUCHERSERIEID` ASC) ,
  INDEX OXORDERID (`OXORDERID` ASC) ,
  INDEX OXUSERID (`OXUSERID` ASC) ,
  INDEX OXVOUCHERNR (`OXVOUCHERNR` ASC)
) ENGINE = InnoDB COMMENT 'Generated coupons';

#
# Table structure for table `oxvoucherseries`
#

DROP TABLE IF EXISTS `oxvoucherseries` ;

CREATE  TABLE IF NOT EXISTS `oxvoucherseries` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Series id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXSERIENR` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Series name',
  `OXSERIEDESCRIPTION` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Description',
  `OXDISCOUNT` FLOAT(9,2) NOT NULL DEFAULT '0' COMMENT 'Discount amount',
  `OXDISCOUNTTYPE` ENUM('percent','absolute') NOT NULL DEFAULT 'absolute' COMMENT 'Discount type (percent, absolute)',
  `OXBEGINDATE` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Valid from',
  `OXENDDATE` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Valid to',
  `OXALLOWSAMESERIES` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Coupons of this series can be used with single order',
  `OXALLOWOTHERSERIES` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Coupons of different series can be used with single order',
  `OXALLOWUSEANOTHER` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Coupons of this series can be used in multiple orders',
  `OXMINIMUMVALUE` FLOAT(9,2) NOT NULL DEFAULT '0.00' COMMENT 'Minimum Order Sum ',
  `OXCALCULATEONCE` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Calculate only once (valid only for product or category vouchers)',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  INDEX OXSERIENR (`OXSERIENR` ASC) ,
  INDEX OXSHOPID (`OXSHOPID` ASC)
) ENGINE = InnoDB COMMENT 'Coupon series';

#
# Table structure for table `oxwrapping`
#

DROP TABLE IF EXISTS `oxwrapping`;

CREATE TABLE `oxwrapping` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Wrapping id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXACTIVE` tinyint(1) NOT NULL default '1' COMMENT 'Active (multilanguage)',
  `OXACTIVE_1` tinyint(1) NOT NULL default '1',
  `OXACTIVE_2` tinyint(1) NOT NULL default '1',
  `OXACTIVE_3` tinyint(1) NOT NULL default '1',
  `OXTYPE` varchar(4) NOT NULL default 'WRAP' COMMENT 'Wrapping type: WRAP,CARD',
  `OXNAME` varchar(128) NOT NULL default '' COMMENT 'Name (multilanguage)',
  `OXNAME_1` varchar(128) NOT NULL default '',
  `OXNAME_2` varchar(128) NOT NULL default '',
  `OXNAME_3` varchar(128) NOT NULL default '',
  `OXPIC` varchar(128) NOT NULL default '' COMMENT 'Image filename',
  `OXPRICE` double NOT NULL default '0' COMMENT 'Price',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`)
) ENGINE=MyISAM COMMENT 'Wrappings';




CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles AS SELECT oxarticles.* FROM oxarticles;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles_de AS SELECT OXID,OXSHOPID,OXPARENTID,OXACTIVE,OXHIDDEN,OXACTIVEFROM,OXACTIVETO,OXARTNUM,OXEAN,OXDISTEAN,OXMPN,OXTITLE,OXSHORTDESC,OXPRICE,OXBLFIXEDPRICE,OXPRICEA,OXPRICEB,OXPRICEC,OXBPRICE,OXTPRICE,OXUNITNAME,OXUNITQUANTITY,OXEXTURL,OXURLDESC,OXURLIMG,OXVAT,OXTHUMB,OXICON,OXPIC1,OXPIC2,OXPIC3,OXPIC4,OXPIC5,OXPIC6,OXPIC7,OXPIC8,OXPIC9,OXPIC10,OXPIC11,OXPIC12,OXWEIGHT,OXSTOCK,OXSTOCKFLAG,OXSTOCKTEXT,OXNOSTOCKTEXT,OXDELIVERY,OXINSERT,OXTIMESTAMP,OXLENGTH,OXWIDTH,OXHEIGHT,OXFILE,OXSEARCHKEYS,OXTEMPLATE,OXQUESTIONEMAIL,OXISSEARCH,OXISCONFIGURABLE,OXVARNAME,OXVARSTOCK,OXVARCOUNT,OXVARSELECT,OXVARMINPRICE,OXVARMAXPRICE,OXBUNDLEID,OXFOLDER,OXSUBCLASS,OXSORT,OXSOLDAMOUNT,OXNONMATERIAL,OXFREESHIPPING,OXREMINDACTIVE,OXREMINDAMOUNT,OXAMITEMID,OXAMTASKID,OXVENDORID,OXMANUFACTURERID,OXSKIPDISCOUNTS,OXRATING,OXRATINGCNT,OXMINDELTIME,OXMAXDELTIME,OXDELTIMEUNIT,OXUPDATEPRICE, OXUPDATEPRICEA, OXUPDATEPRICEB, OXUPDATEPRICEC, OXUPDATEPRICETIME, OXISDOWNLOADABLE, OXSHOWCUSTOMAGREEMENT FROM oxarticles;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxarticles_en AS SELECT OXID,OXSHOPID,OXPARENTID,OXACTIVE,OXHIDDEN,OXACTIVEFROM,OXACTIVETO,OXARTNUM,OXEAN,OXDISTEAN,OXMPN,OXTITLE_1 AS OXTITLE,OXSHORTDESC_1 AS OXSHORTDESC,OXPRICE,OXBLFIXEDPRICE,OXPRICEA,OXPRICEB,OXPRICEC,OXBPRICE,OXTPRICE,OXUNITNAME,OXUNITQUANTITY,OXEXTURL,OXURLDESC_1 AS OXURLDESC,OXURLIMG,OXVAT,OXTHUMB,OXICON,OXPIC1,OXPIC2,OXPIC3,OXPIC4,OXPIC5,OXPIC6,OXPIC7,OXPIC8,OXPIC9,OXPIC10,OXPIC11,OXPIC12,OXWEIGHT,OXSTOCK,OXSTOCKFLAG,OXSTOCKTEXT_1 AS OXSTOCKTEXT,OXNOSTOCKTEXT_1 AS OXNOSTOCKTEXT,OXDELIVERY,OXINSERT,OXTIMESTAMP,OXLENGTH,OXWIDTH,OXHEIGHT,OXFILE,OXSEARCHKEYS_1 AS OXSEARCHKEYS,OXTEMPLATE,OXQUESTIONEMAIL,OXISSEARCH,OXISCONFIGURABLE,OXVARNAME_1 AS OXVARNAME,OXVARSTOCK,OXVARCOUNT,OXVARSELECT_1 AS OXVARSELECT,OXVARMINPRICE,OXVARMAXPRICE,OXBUNDLEID,OXFOLDER,OXSUBCLASS,OXSORT,OXSOLDAMOUNT,OXNONMATERIAL,OXFREESHIPPING,OXREMINDACTIVE,OXREMINDAMOUNT,OXAMITEMID,OXAMTASKID,OXVENDORID,OXMANUFACTURERID,OXSKIPDISCOUNTS,OXRATING,OXRATINGCNT,OXMINDELTIME,OXMAXDELTIME,OXDELTIMEUNIT,OXUPDATEPRICE, OXUPDATEPRICEA, OXUPDATEPRICEB, OXUPDATEPRICEC, OXUPDATEPRICETIME, OXISDOWNLOADABLE, OXSHOWCUSTOMAGREEMENT FROM oxarticles;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxartextends AS SELECT oxartextends.* FROM oxartextends;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxartextends_de AS SELECT OXID,OXLONGDESC,OXTIMESTAMP FROM oxartextends;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxartextends_en AS SELECT OXID,OXLONGDESC_1 AS OXLONGDESC,OXTIMESTAMP FROM oxartextends;

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
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcountry_de AS SELECT OXID,OXACTIVE,OXTITLE,OXISOALPHA2,OXISOALPHA3,OXUNNUM3,OXVATINPREFIX,OXORDER,OXSHORTDESC,OXLONGDESC,OXVATSTATUS,OXTIMESTAMP FROM oxcountry;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxcountry_en AS SELECT OXID,OXACTIVE,OXTITLE_1 AS OXTITLE,OXISOALPHA2,OXISOALPHA3,OXUNNUM3,OXVATINPREFIX,OXORDER,OXSHORTDESC_1 AS OXSHORTDESC,OXLONGDESC_1 AS OXLONGDESC,OXVATSTATUS,OXTIMESTAMP FROM oxcountry;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdelivery AS SELECT oxdelivery.* FROM oxdelivery;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdelivery_de AS SELECT OXID,OXSHOPID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXTITLE,OXADDSUMTYPE,OXADDSUM,OXDELTYPE,OXPARAM,OXPARAMEND,OXFIXED,OXSORT,OXFINALIZE,OXTIMESTAMP FROM oxdelivery;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdelivery_en AS SELECT OXID,OXSHOPID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXTITLE_1 AS OXTITLE,OXADDSUMTYPE,OXADDSUM,OXDELTYPE,OXPARAM,OXPARAMEND,OXFIXED,OXSORT,OXFINALIZE,OXTIMESTAMP FROM oxdelivery;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdiscount AS SELECT oxdiscount.* FROM oxdiscount;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdiscount_de AS SELECT OXID,OXSHOPID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXTITLE,OXAMOUNT,OXAMOUNTTO,OXPRICETO,OXPRICE,OXADDSUMTYPE,OXADDSUM,OXITMARTID,OXITMAMOUNT,OXITMMULTIPLE,OXSORT,OXTIMESTAMP FROM oxdiscount;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxdiscount_en AS SELECT OXID,OXSHOPID,OXACTIVE,OXACTIVEFROM,OXACTIVETO,OXTITLE_1 AS OXTITLE,OXAMOUNT,OXAMOUNTTO,OXPRICETO,OXPRICE,OXADDSUMTYPE,OXADDSUM,OXITMARTID,OXITMAMOUNT,OXITMMULTIPLE,OXSORT,OXTIMESTAMP FROM oxdiscount;

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
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxpayments_de AS SELECT OXID,OXACTIVE,OXDESC,OXADDSUM,OXADDSUMTYPE,OXADDSUMRULES,OXFROMBONI,OXFROMAMOUNT,OXTOAMOUNT,OXVALDESC,OXCHECKED,OXLONGDESC,OXSORT,OXTIMESTAMP FROM oxpayments;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxpayments_en AS SELECT OXID,OXACTIVE,OXDESC_1 AS OXDESC,OXADDSUM,OXADDSUMTYPE,OXADDSUMRULES,OXFROMBONI,OXFROMAMOUNT,OXTOAMOUNT,OXVALDESC_1 AS OXVALDESC,OXCHECKED,OXLONGDESC_1 AS OXLONGDESC,OXSORT,OXTIMESTAMP FROM oxpayments;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxselectlist AS SELECT oxselectlist.* FROM oxselectlist;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxselectlist_de AS SELECT OXID,OXSHOPID,OXTITLE,OXIDENT,OXVALDESC,OXTIMESTAMP FROM oxselectlist;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxselectlist_en AS SELECT OXID,OXSHOPID,OXTITLE_1 AS OXTITLE,OXIDENT,OXVALDESC_1 AS OXVALDESC,OXTIMESTAMP FROM oxselectlist;

CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxshops AS SELECT oxshops.* FROM oxshops;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxshops_de AS SELECT OXID,OXACTIVE,OXPRODUCTIVE,OXDEFCURRENCY,OXDEFLANGUAGE,OXNAME,OXTITLEPREFIX,OXTITLESUFFIX,OXSTARTTITLE,OXINFOEMAIL,OXORDEREMAIL,OXOWNEREMAIL,OXORDERSUBJECT,OXREGISTERSUBJECT,OXFORGOTPWDSUBJECT,OXSENDEDNOWSUBJECT,OXSMTP,OXSMTPUSER,OXSMTPPWD,OXCOMPANY,OXSTREET,OXZIP,OXCITY,OXCOUNTRY,OXBANKNAME,OXBANKNUMBER,OXBANKCODE,OXVATNUMBER,OXTAXNUMBER,OXBICCODE,OXIBANNUMBER,OXFNAME,OXLNAME,OXTELEFON,OXTELEFAX,OXURL,OXDEFCAT,OXHRBNR,OXCOURT,OXADBUTLERID,OXAFFILINETID,OXSUPERCLICKSID,OXAFFILIWELTID,OXAFFILI24ID,OXEDITION,OXVERSION,OXSEOACTIVE,OXTIMESTAMP FROM oxshops;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW oxv_oxshops_en AS SELECT OXID,OXACTIVE,OXPRODUCTIVE,OXDEFCURRENCY,OXDEFLANGUAGE,OXNAME,OXTITLEPREFIX_1 AS OXTITLEPREFIX,OXTITLESUFFIX_1 AS OXTITLESUFFIX,OXSTARTTITLE_1 AS OXSTARTTITLE,OXINFOEMAIL,OXORDEREMAIL,OXOWNEREMAIL,OXORDERSUBJECT_1 AS OXORDERSUBJECT,OXREGISTERSUBJECT_1 AS OXREGISTERSUBJECT,OXFORGOTPWDSUBJECT_1 AS OXFORGOTPWDSUBJECT,OXSENDEDNOWSUBJECT_1 AS OXSENDEDNOWSUBJECT,OXSMTP,OXSMTPUSER,OXSMTPPWD,OXCOMPANY,OXSTREET,OXZIP,OXCITY,OXCOUNTRY,OXBANKNAME,OXBANKNUMBER,OXBANKCODE,OXVATNUMBER,OXTAXNUMBER,OXBICCODE,OXIBANNUMBER,OXFNAME,OXLNAME,OXTELEFON,OXTELEFAX,OXURL,OXDEFCAT,OXHRBNR,OXCOURT,OXADBUTLERID,OXAFFILINETID,OXSUPERCLICKSID,OXAFFILIWELTID,OXAFFILI24ID,OXEDITION,OXVERSION,OXSEOACTIVE_1 AS OXSEOACTIVE,OXTIMESTAMP FROM oxshops;

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
