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
)ENGINE=MEMORY COMMENT 'Articles information';

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
) ENGINE=MEMORY COMMENT 'Article scale prices';

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
  `OXLONGDESC` varchar(1024) NOT NULL default '' COMMENT 'Long description (multilanguage)',
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
  `OXLONGDESC_1` varchar(1024) NOT NULL default '',
  `OXACTIVE_2` tinyint(1) NOT NULL default '0',
  `OXTITLE_2` varchar(255) NOT NULL default '',
  `OXDESC_2` varchar(255) NOT NULL default '',
  `OXLONGDESC_2` varchar(1024) NOT NULL default '',
  `OXACTIVE_3` tinyint(1) NOT NULL default '0',
  `OXTITLE_3` varchar(255) NOT NULL default '',
  `OXDESC_3` varchar(255) NOT NULL default '',
  `OXLONGDESC_3` varchar(1024) NOT NULL default '',
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
) ENGINE=MEMORY COMMENT 'Article categories';

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
  `OXUPDATEKEY` varchar( 32 ) NOT NULL default '' COMMENT 'Update key',
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
) ENGINE=MEMORY COMMENT 'Shop administrators and users';

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
) ENGINE=MEMORY COMMENT 'Stores user shipping addresses';

DROP TABLE IF EXISTS `oxcountry`;

CREATE TABLE `oxcountry` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Country id',
  `OXACTIVE` tinyint(1) NOT NULL default '0' COMMENT 'Active',
  `OXTITLE` varchar(128) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXISOALPHA2` char(2) NOT NULL default '' COMMENT 'ISO 3166-1 alpha-2',
  `OXISOALPHA3` char(3) NOT NULL default '' COMMENT 'ISO 3166-1 alpha-3',
  `OXUNNUM3` char(3) NOT NULL default '' COMMENT 'ISO 3166-1 numeric',
  `OXVATINPREFIX` char(2) NOT NULL default '' COMMENT 'VAT identification number prefix',
  `OXORDER` int(11) NOT NULL default '9999' COMMENT 'Sorting',
  `OXSHORTDESC` varchar(255) NOT NULL default '' COMMENT 'Short description (multilanguage)',
  `OXLONGDESC` varchar(255) NOT NULL default '' COMMENT 'Long description (multilanguage)',
  `OXTITLE_1` varchar(128) NOT NULL default '',
  `OXTITLE_2` varchar(128) NOT NULL default '',
  `OXTITLE_3` varchar(128) NOT NULL default '',
  `OXSHORTDESC_1` varchar(255) NOT NULL default '',
  `OXSHORTDESC_2` varchar(255) NOT NULL default '',
  `OXSHORTDESC_3` varchar(255) NOT NULL default '',
  `OXLONGDESC_1` varchar(255) NOT NULL,
  `OXLONGDESC_2` varchar(255) NOT NULL,
  `OXLONGDESC_3` varchar(255) NOT NULL,
  `OXVATSTATUS` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Vat status: 0 - Do not bill VAT, 1 - Do not bill VAT only if provided valid VAT ID',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY (`OXACTIVE`)
) ENGINE=MEMORY COMMENT 'Countries list';

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
  `OXSORT` int(5) NOT NULL default '0' COMMENT 'Defines the order discounts are applied to basket or product',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  UNIQUE INDEX `UNIQ_OXSORT` (`OXSHOPID`, `OXSORT`),
  KEY `OXSHOPID` (`OXSHOPID`),
  KEY `OXACTIVE` (`OXACTIVE`),
  KEY `OXACTIVEFROM` (`OXACTIVEFROM`),
  KEY `OXACTIVETO` (`OXACTIVETO`)
) ENGINE=MEMORY COMMENT 'Article discounts';

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
) ENGINE=MEMORY COMMENT 'Shows many-to-many relationship between discounts and objects (table determined by oxtype)';

DROP TABLE IF EXISTS `oxobject2group`;

CREATE TABLE `oxobject2group` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Record id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXOBJECTID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'User id',
  `OXGROUPSID` char(32) character set latin1 collate latin1_general_ci NOT NULL default '' COMMENT 'Group id',
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`),
  KEY `OXOBJECTID` (`OXOBJECTID`),
  UNIQUE INDEX `UNIQ_OBJECTGROUP` (`OXGROUPSID`, `OXOBJECTID`, `OXSHOPID`)
) ENGINE=MEMORY COMMENT 'Shows many-to-many relationship between users and groups';

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
) ENGINE=MEMORY COMMENT 'Shows many-to-many relationship between articles and categories';

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
) ENGINE=MEMORY COMMENT 'Shows many-to-many relationship between articles and selection lists';

DROP TABLE IF EXISTS `oxselectlist`;

CREATE TABLE `oxselectlist` (
  `OXID` char(32) character set latin1 collate latin1_general_ci NOT NULL COMMENT 'Selection list id',
  `OXSHOPID` int(11) NOT NULL default '1' COMMENT 'Shop id (oxshops)',
  `OXTITLE` varchar(254) NOT NULL default '' COMMENT 'Title (multilanguage)',
  `OXIDENT` varchar(255) NOT NULL default '' COMMENT 'Working Title',
  `OXVALDESC` varchar(1024) NOT NULL COMMENT 'List fields, separated by "[field_name]!P![price]__@@[field_name]__@@" (multilanguage)',
  `OXTITLE_1` varchar(255) NOT NULL default '',
  `OXVALDESC_1` varchar(1024) NOT NULL,
  `OXTITLE_2` varchar(255) NOT NULL default '',
  `OXVALDESC_2` varchar(1024) NOT NULL,
  `OXTITLE_3` varchar(255) NOT NULL default '',
  `OXVALDESC_3` varchar(1024) NOT NULL,
  `OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp',
  PRIMARY KEY  (`OXID`)
) ENGINE=MEMORY COMMENT 'Selection lists';

DROP VIEW IF EXISTS `oxv_oxselectlist_de`;

CREATE VIEW oxv_oxselectlist_de AS select `oxselectlist`.`OXID` AS `OXID`,
  `oxselectlist`.`OXSHOPID` AS `OXSHOPID`,
  `oxselectlist`.`OXTITLE` AS `OXTITLE`,
  `oxselectlist`.`OXIDENT` AS `OXIDENT`,
  `oxselectlist`.`OXVALDESC` AS `OXVALDESC`,
  `oxselectlist`.`OXTIMESTAMP` AS `OXTIMESTAMP` from `oxselectlist`;

DROP VIEW IF EXISTS `oxv_oxselectlist_en`;

CREATE VIEW oxv_oxselectlist_en AS select `oxselectlist`.`OXID` AS `OXID`,
  `oxselectlist`.`OXSHOPID` AS `OXSHOPID`,
  `oxselectlist`.`OXTITLE_1` AS `OXTITLE`,
  `oxselectlist`.`OXIDENT` AS `OXIDENT`,
  `oxselectlist`.`OXVALDESC_1` AS `OXVALDESC`,
  `oxselectlist`.`OXTIMESTAMP` AS `OXTIMESTAMP` from `oxselectlist`;
