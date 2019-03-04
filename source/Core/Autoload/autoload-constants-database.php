<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/** @noinspection SpellCheckingInspection */

/**
 */
namespace OxidEsales\Eshop\Core\Database\TABLE

{
    /**
     * Stores information about actions, promotions and banners [InnoDB]
     *
     * @see OXACTIONS\*
     * @see \OxidEsales\Eshop\Application\Model\Actions::__construct
     */
    const OXACTIONS = 'oxactions';

    /**
     * Stores user shipping addresses [InnoDB]
     *
     * @see OXADDRESS\*
     * @see \OxidEsales\Eshop\Application\Model\Address::__construct
     */
    const OXADDRESS = 'oxaddress';

    /**
     * Articles information [InnoDB]
     *
     * @see OXARTICLES\*
     * @see \OxidEsales\Eshop\Application\Model\Article::__construct
     * @see \OxidEsales\Eshop\Application\Model\SimpleVariant::__construct
     */
    const OXARTICLES = 'oxarticles';

    /**
     * Article attributes [InnoDB]
     *
     * @see OXATTRIBUTE\*
     * @see \OxidEsales\Eshop\Application\Model\Attribute::__construct
     */
    const OXATTRIBUTE = 'oxattribute';

    /**
     * Article categories [InnoDB]
     *
     * @see OXCATEGORIES\*
     * @see \OxidEsales\Eshop\Application\Model\Category::__construct
     */
    const OXCATEGORIES = 'oxcategories';

    /**
     * Content pages (Snippets, Menu, Categories, Manual) [InnoDB]
     *
     * @see OXCONTENTS\*
     * @see \OxidEsales\Eshop\Application\Model\Content::__construct
     */
    const OXCONTENTS = 'oxcontents';

    /**
     * Countries list [InnoDB]
     *
     * @see OXCOUNTRY\*
     * @see \OxidEsales\Eshop\Application\Model\Country::__construct
     */
    const OXCOUNTRY = 'oxcountry';

    /**
     * Delivery shipping cost rules [InnoDB]
     *
     * @see OXDELIVERY\*
     * @see \OxidEsales\Eshop\Application\Model\Delivery::__construct
     */
    const OXDELIVERY = 'oxdelivery';

    /**
     * Delivery (shipping) methods [InnoDB]
     *
     * @see OXDELIVERYSET\*
     * @see \OxidEsales\Eshop\Application\Model\DeliverySet::__construct
     */
    const OXDELIVERYSET = 'oxdeliveryset';

    /**
     * Article discounts [InnoDB]
     *
     * @see OXDISCOUNT\*
     * @see \OxidEsales\Eshop\Application\Model\Discount::__construct
     */
    const OXDISCOUNT = 'oxdiscount';

    /**
     * Files available for users to download [InnoDB]
     *
     * @see OXFILES\*
     * @see \OxidEsales\Eshop\Application\Model\File::__construct
     */
    const OXFILES = 'oxfiles';

    /**
     * User groups [InnoDB]
     *
     * @see OXGROUPS\*
     * @see \OxidEsales\Eshop\Application\Model\Groups::__construct
     */
    const OXGROUPS = 'oxgroups';

    /**
     * Links [InnoDB]
     *
     * @see OXLINKS\*
     * @see \OxidEsales\Eshop\Application\Model\Links::__construct
     */
    const OXLINKS = 'oxlinks';

    /**
     * Shop manufacturers [InnoDB]
     *
     * @see OXMANUFACTURERS\*
     * @see \OxidEsales\Eshop\Application\Model\Manufacturer::__construct
     */
    const OXMANUFACTURERS = 'oxmanufacturers';

    /**
     * Stores objects media [InnoDB]
     *
     * @see OXMEDIAURLS\*
     * @see \OxidEsales\Eshop\Application\Model\MediaUrl::__construct
     */
    const OXMEDIAURLS = 'oxmediaurls';

    /**
     * Shop news [InnoDB]
     *
     * @see OXNEWS\*
     * @see \OxidEsales\Eshop\Application\Model\News::__construct
     */
    const OXNEWS = 'oxnews';

    /**
     * User subscriptions [InnoDB]
     *
     * @see OXNEWSSUBSCRIBED\*
     * @see \OxidEsales\Eshop\Application\Model\NewsSubscribed::__construct
     */
    const OXNEWSSUBSCRIBED = 'oxnewssubscribed';

    /**
     * Templates for sending newsletters [InnoDB]
     *
     * @see OXNEWSLETTER\*
     * @see \OxidEsales\Eshop\Application\Model\Newsletter::__construct
     */
    const OXNEWSLETTER = 'oxnewsletter';

    /**
     * Shows many-to-many relationship between articles and categories [InnoDB]
     *
     * @see OXOBJECT2CATEGORY\*
     * @see \OxidEsales\Eshop\Application\Model\Object2Category::__construct
     */
    const OXOBJECT2CATEGORY = 'oxobject2category';

    /**
     * Shows many-to-many relationship between users and groups [InnoDB]
     *
     * @see OXOBJECT2GROUP\*
     * @see \OxidEsales\Eshop\Application\Model\Object2Group::__construct
     */
    const OXOBJECT2GROUP = 'oxobject2group';

    /**
     * Shop orders information [InnoDB]
     *
     * @see OXORDER\*
     * @see \OxidEsales\Eshop\Application\Model\Order::__construct
     */
    const OXORDER = 'oxorder';

    /**
     * Ordered articles information [InnoDB]
     *
     * @see OXORDERARTICLES\*
     * @see \OxidEsales\Eshop\Application\Model\OrderArticle::__construct
     */
    const OXORDERARTICLES = 'oxorderarticles';

    /**
     * Files, given to users to download after order [InnoDB]
     *
     * @see OXORDERFILES\*
     * @see \OxidEsales\Eshop\Application\Model\OrderFile::__construct
     */
    const OXORDERFILES = 'oxorderfiles';

    /**
     * Payment methods [InnoDB]
     *
     * @see OXPAYMENTS\*
     * @see \OxidEsales\Eshop\Application\Model\Payment::__construct
     */
    const OXPAYMENTS = 'oxpayments';

    /**
     * Price fall alarm requests [InnoDB]
     *
     * @see OXPRICEALARM\*
     * @see \OxidEsales\Eshop\Application\Model\PriceAlarm::__construct
     */
    const OXPRICEALARM = 'oxpricealarm';

    /**
     * Articles and Listmania ratings [InnoDB]
     *
     * @see OXRATINGS\*
     * @see \OxidEsales\Eshop\Application\Model\Rating::__construct
     */
    const OXRATINGS = 'oxratings';

    /**
     * Listmania [InnoDB]
     *
     * @see OXRECOMMLISTS\*
     * @see \OxidEsales\Eshop\Application\Model\RecommendationList::__construct
     */
    const OXRECOMMLISTS = 'oxrecommlists';

    /**
     * User History [InnoDB]
     *
     * @see OXREMARK\*
     * @see \OxidEsales\Eshop\Application\Model\Remark::__construct
     */
    const OXREMARK = 'oxremark';

    /**
     * Articles and Listmania reviews [InnoDB]
     *
     * @see OXREVIEWS\*
     * @see \OxidEsales\Eshop\Application\Model\Review::__construct
     */
    const OXREVIEWS = 'oxreviews';

    /**
     * Selection lists [InnoDB]
     *
     * @see OXSELECTLIST\*
     * @see \OxidEsales\Eshop\Application\Model\SelectList::__construct
     */
    const OXSELECTLIST = 'oxselectlist';

    /**
     * Shop config [InnoDB]
     *
     * @see OXSHOPS\*
     * @see \OxidEsales\Eshop\Application\Model\Shop::__construct
     */
    const OXSHOPS = 'oxshops';

    /**
     * US States list [InnoDB]
     *
     * @see OXSTATES\*
     * @see \OxidEsales\Eshop\Application\Model\State::__construct
     */
    const OXSTATES = 'oxstates';

    /**
     * Shop administrators and users [InnoDB]
     *
     * @see OXUSER\*
     * @see \OxidEsales\Eshop\Application\Model\User::__construct
     */
    const OXUSER = 'oxuser';

    /**
     * Active User baskets [InnoDB]
     *
     * @see OXUSERBASKETS\*
     * @see \OxidEsales\Eshop\Application\Model\UserBasket::__construct
     */
    const OXUSERBASKETS = 'oxuserbaskets';

    /**
     * User basket items [InnoDB]
     *
     * @see OXUSERBASKETITEMS\*
     * @see \OxidEsales\Eshop\Application\Model\UserBasketItem::__construct
     */
    const OXUSERBASKETITEMS = 'oxuserbasketitems';

    /**
     * User payments [InnoDB]
     *
     * @see OXUSERPAYMENTS\*
     * @see \OxidEsales\Eshop\Application\Model\UserPayment::__construct
     */
    const OXUSERPAYMENTS = 'oxuserpayments';

    /**
     * Distributors list [InnoDB]
     *
     * @see OXVENDOR\*
     * @see \OxidEsales\Eshop\Application\Model\Vendor::__construct
     */
    const OXVENDOR = 'oxvendor';

    /**
     * Generated coupons [InnoDB]
     *
     * @see OXVOUCHERS\*
     * @see \OxidEsales\Eshop\Application\Model\Voucher::__construct
     */
    const OXVOUCHERS = 'oxvouchers';

    /**
     * Coupon series [InnoDB]
     *
     * @see OXVOUCHERSERIES\*
     * @see \OxidEsales\Eshop\Application\Model\VoucherSerie::__construct
     */
    const OXVOUCHERSERIES = 'oxvoucherseries';

    /**
     * Wrappings [InnoDB]
     *
     * @see OXWRAPPING\*
     * @see \OxidEsales\Eshop\Application\Model\Wrapping::__construct
     */
    const OXWRAPPING = 'oxwrapping';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXACTIONS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXACTIONS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Action type: 0 or 1 - action, 2 - promotion, 3 - banner
     *
     * tinyint(1)
     */
    const OXTYPE = 'oxtype';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Long description, used for promotion (multilanguage)
     *
     * text-i18n
     */
    const OXLONGDESC = 'oxlongdesc';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Active from specified date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXACTIVEFROM = 'oxactivefrom';

    /**
     * Active to specified date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXACTIVETO = 'oxactiveto';

    /**
     * Picture filename, used for banner (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXPIC = 'oxpic';

    /**
     * Link, used on banner (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXLINK = 'oxlink';

    /**
     * Sorting
     *
     * int(5) = 0
     */
    const OXSORT = 'oxsort';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXADDRESS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXADDRESS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * User id (oxuser)
     *
     * char(32)
     */
    const OXUSERID = 'oxuserid';

    /**
     * User id (oxuser)
     *
     * varchar(32)
     */
    const OXADDRESSUSERID = 'oxaddressuserid';

    /**
     * Company name
     *
     * varchar(255)
     */
    const OXCOMPANY = 'oxcompany';

    /**
     * First name
     *
     * varchar(255)
     */
    const OXFNAME = 'oxfname';

    /**
     * Last name
     *
     * varchar(255)
     */
    const OXLNAME = 'oxlname';

    /**
     * Street
     *
     * varchar(255)
     */
    const OXSTREET = 'oxstreet';

    /**
     * House number
     *
     * varchar(16)
     */
    const OXSTREETNR = 'oxstreetnr';

    /**
     * Additional info
     *
     * varchar(255)
     */
    const OXADDINFO = 'oxaddinfo';

    /**
     * City
     *
     * varchar(255)
     */
    const OXCITY = 'oxcity';

    /**
     * Country name
     *
     * varchar(255)
     */
    const OXCOUNTRY = 'oxcountry';

    /**
     * Country id (oxcountry)
     *
     * char(32)
     */
    const OXCOUNTRYID = 'oxcountryid';

    /**
     * State id (oxstate)
     *
     * varchar(32)
     */
    const OXSTATEID = 'oxstateid';

    /**
     * Zip code
     *
     * varchar(50)
     */
    const OXZIP = 'oxzip';

    /**
     * Phone number
     *
     * varchar(128)
     */
    const OXFON = 'oxfon';

    /**
     * Fax number
     *
     * varchar(128)
     */
    const OXFAX = 'oxfax';

    /**
     * User title prefix (Mr/Mrs)
     *
     * varchar(128)
     */
    const OXSAL = 'oxsal';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXARTICLES
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXARTICLES

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Parent article id
     *
     * char(32)
     */
    const OXPARENTID = 'oxparentid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Hidden
     *
     * tinyint(1) = 0
     */
    const OXHIDDEN = 'oxhidden';

    /**
     * Active from specified date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXACTIVEFROM = 'oxactivefrom';

    /**
     * Active to specified date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXACTIVETO = 'oxactiveto';

    /**
     * Article number
     *
     * varchar(255)
     */
    const OXARTNUM = 'oxartnum';

    /**
     * International Article Number (EAN)
     *
     * varchar(128)
     */
    const OXEAN = 'oxean';

    /**
     * Manufacture International Article Number (Man. EAN)
     *
     * varchar(128)
     */
    const OXDISTEAN = 'oxdistean';

    /**
     * Manufacture Part Number (MPN)
     *
     * varchar(100)
     */
    const OXMPN = 'oxmpn';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Short description (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXSHORTDESC = 'oxshortdesc';

    /**
     * Article Price
     *
     * double = 0
     */
    const OXPRICE = 'oxprice';

    /**
     * No Promotions (Price Alert)
     *
     * tinyint(1) = 0
     */
    const OXBLFIXEDPRICE = 'oxblfixedprice';

    /**
     * Price A
     *
     * double = 0
     */
    const OXPRICEA = 'oxpricea';

    /**
     * Price B
     *
     * double = 0
     */
    const OXPRICEB = 'oxpriceb';

    /**
     * Price C
     *
     * double = 0
     */
    const OXPRICEC = 'oxpricec';

    /**
     * Purchase Price
     *
     * double = 0
     */
    const OXBPRICE = 'oxbprice';

    /**
     * Recommended Retail Price (RRP)
     *
     * double = 0
     */
    const OXTPRICE = 'oxtprice';

    /**
     * Unit name (kg,g,l,cm etc), used in setting price per quantity unit calculation
     *
     * varchar(32)
     */
    const OXUNITNAME = 'oxunitname';

    /**
     * Article quantity, used in setting price per quantity unit calculation
     *
     * double = 0
     */
    const OXUNITQUANTITY = 'oxunitquantity';

    /**
     * External URL to other information about the article
     *
     * varchar(255)
     */
    const OXEXTURL = 'oxexturl';

    /**
     * Text for external URL (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXURLDESC = 'oxurldesc';

    /**
     * External URL image
     *
     * varchar(128)
     */
    const OXURLIMG = 'oxurlimg';

    /**
     * Value added tax. If specified, used in all calculations instead of global vat
     *
     * float
     */
    const OXVAT = 'oxvat';

    /**
     * Thumbnail filename
     *
     * varchar(128)
     */
    const OXTHUMB = 'oxthumb';

    /**
     * Icon filename
     *
     * varchar(128)
     */
    const OXICON = 'oxicon';

    /**
     * 1# Picture filename
     *
     * varchar(128)
     */
    const OXPIC1 = 'oxpic1';

    /**
     * 2# Picture filename
     *
     * varchar(128)
     */
    const OXPIC2 = 'oxpic2';

    /**
     * 3# Picture filename
     *
     * varchar(128)
     */
    const OXPIC3 = 'oxpic3';

    /**
     * 4# Picture filename
     *
     * varchar(128)
     */
    const OXPIC4 = 'oxpic4';

    /**
     * 5# Picture filename
     *
     * varchar(128)
     */
    const OXPIC5 = 'oxpic5';

    /**
     * 6# Picture filename
     *
     * varchar(128)
     */
    const OXPIC6 = 'oxpic6';

    /**
     * 7# Picture filename
     *
     * varchar(128)
     */
    const OXPIC7 = 'oxpic7';

    /**
     * 8# Picture filename
     *
     * varchar(128)
     */
    const OXPIC8 = 'oxpic8';

    /**
     * 9# Picture filename
     *
     * varchar(128)
     */
    const OXPIC9 = 'oxpic9';

    /**
     * 10# Picture filename
     *
     * varchar(128)
     */
    const OXPIC10 = 'oxpic10';

    /**
     * 11# Picture filename
     *
     * varchar(128)
     */
    const OXPIC11 = 'oxpic11';

    /**
     * 12# Picture filename
     *
     * varchar(128)
     */
    const OXPIC12 = 'oxpic12';

    /**
     * Weight (kg)
     *
     * double = 0
     */
    const OXWEIGHT = 'oxweight';

    /**
     * Article quantity in stock
     *
     * double = 0
     */
    const OXSTOCK = 'oxstock';

    /**
     * Delivery Status: 1 - Standard, 2 - If out of Stock, offline, 3 - If out of Stock, not orderable, 4 - External Storehouse
     *
     * tinyint(1) = 1
     */
    const OXSTOCKFLAG = 'oxstockflag';

    /**
     * Message, which is shown if the article is in stock (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXSTOCKTEXT = 'oxstocktext';

    /**
     * Message, which is shown if the article is off stock (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXNOSTOCKTEXT = 'oxnostocktext';

    /**
     * Date, when the product will be available again if it is sold out
     *
     * date = 0000-00-00
     */
    const OXDELIVERY = 'oxdelivery';

    /**
     * Creation time
     *
     * date = 0000-00-00
     */
    const OXINSERT = 'oxinsert';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';

    /**
     * Article dimensions: Length
     *
     * double = 0
     */
    const OXLENGTH = 'oxlength';

    /**
     * Article dimensions: Width
     *
     * double = 0
     */
    const OXWIDTH = 'oxwidth';

    /**
     * Article dimensions: Height
     *
     * double = 0
     */
    const OXHEIGHT = 'oxheight';

    /**
     * File, shown in article media list
     *
     * varchar(128)
     */
    const OXFILE = 'oxfile';

    /**
     * Search terms (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXSEARCHKEYS = 'oxsearchkeys';

    /**
     * Alternative template filename (if empty, default is used)
     *
     * varchar(128)
     */
    const OXTEMPLATE = 'oxtemplate';

    /**
     * E-mail for question
     *
     * varchar(255)
     */
    const OXQUESTIONEMAIL = 'oxquestionemail';

    /**
     * Should article be shown in search
     *
     * tinyint(1) = 1
     */
    const OXISSEARCH = 'oxissearch';

    /**
     * Can article be customized
     *
     * tinyint(4) = 0
     */
    const OXISCONFIGURABLE = 'oxisconfigurable';

    /**
     * Name of variants selection lists (different lists are separated by | ) (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXVARNAME = 'oxvarname';

    /**
     * Sum of active article variants stock quantity
     *
     * int(5) = 0
     */
    const OXVARSTOCK = 'oxvarstock';

    /**
     * Total number of variants that article has (active and inactive)
     *
     * int(1) = 0
     */
    const OXVARCOUNT = 'oxvarcount';

    /**
     * Variant article selections (separated by | ) (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXVARSELECT = 'oxvarselect';

    /**
     * Lowest price in active article variants
     *
     * double = 0
     */
    const OXVARMINPRICE = 'oxvarminprice';

    /**
     * Highest price in active article variants
     *
     * double = 0
     */
    const OXVARMAXPRICE = 'oxvarmaxprice';

    /**
     * Bundled article id
     *
     * varchar(32)
     */
    const OXBUNDLEID = 'oxbundleid';

    /**
     * Folder
     *
     * varchar(32)
     */
    const OXFOLDER = 'oxfolder';

    /**
     * Subclass
     *
     * varchar(32)
     */
    const OXSUBCLASS = 'oxsubclass';

    /**
     * Sorting
     *
     * int(5) = 0
     */
    const OXSORT = 'oxsort';

    /**
     * Amount of sold articles including variants (used only for parent articles)
     *
     * double = 0
     */
    const OXSOLDAMOUNT = 'oxsoldamount';

    /**
     * Intangible article, free shipping is used (variants inherits parent setting)
     *
     * int(1) = 0
     */
    const OXNONMATERIAL = 'oxnonmaterial';

    /**
     * Free shipping (variants inherits parent setting)
     *
     * int(1) = 0
     */
    const OXFREESHIPPING = 'oxfreeshipping';

    /**
     * Enables sending of notification email when oxstock field value falls below oxremindamount value
     *
     * int(1) = 0
     */
    const OXREMINDACTIVE = 'oxremindactive';

    /**
     * Defines the amount, below which notification email will be sent if oxremindactive is set to 1
     *
     * double = 0
     */
    const OXREMINDAMOUNT = 'oxremindamount';

    /**
     *
     *
     * varchar(32)
     */
    const OXAMITEMID = 'oxamitemid';

    /**
     *
     *
     * varchar(16) = 0
     */
    const OXAMTASKID = 'oxamtaskid';

    /**
     * Vendor id (oxvendor)
     *
     * char(32)
     */
    const OXVENDORID = 'oxvendorid';

    /**
     * Manufacturer id (oxmanufacturers)
     *
     * char(32)
     */
    const OXMANUFACTURERID = 'oxmanufacturerid';

    /**
     * Skips all negative Discounts (Discounts, Vouchers, Delivery ...)
     *
     * tinyint(1) = 0
     */
    const OXSKIPDISCOUNTS = 'oxskipdiscounts';

    /**
     * Article rating
     *
     * double = 0
     */
    const OXRATING = 'oxrating';

    /**
     * Rating votes count
     *
     * int(11) = 0
     */
    const OXRATINGCNT = 'oxratingcnt';

    /**
     * Minimal delivery time (unit is set in oxdeltimeunit)
     *
     * int(11) = 0
     */
    const OXMINDELTIME = 'oxmindeltime';

    /**
     * Maximum delivery time (unit is set in oxdeltimeunit)
     *
     * int(11) = 0
     */
    const OXMAXDELTIME = 'oxmaxdeltime';

    /**
     * Delivery time unit: DAY, WEEK, MONTH
     *
     * varchar(255)
     */
    const OXDELTIMEUNIT = 'oxdeltimeunit';

    /**
     * If not 0, oxprice will be updated to this value on oxupdatepricetime date
     *
     * double = 0
     */
    const OXUPDATEPRICE = 'oxupdateprice';

    /**
     * If not 0, oxpricea will be updated to this value on oxupdatepricetime date
     *
     * double = 0
     */
    const OXUPDATEPRICEA = 'oxupdatepricea';

    /**
     * If not 0, oxpriceb will be updated to this value on oxupdatepricetime date
     *
     * double = 0
     */
    const OXUPDATEPRICEB = 'oxupdatepriceb';

    /**
     * If not 0, oxpricec will be updated to this value on oxupdatepricetime date
     *
     * double = 0
     */
    const OXUPDATEPRICEC = 'oxupdatepricec';

    /**
     * Date, when oxprice[a,b,c] should be updated to oxupdateprice[a,b,c] values
     *
     * timestamp = 0000-00-00 00:00:00
     */
    const OXUPDATEPRICETIME = 'oxupdatepricetime';

    /**
     * Enable download of files for this product
     *
     * tinyint(1) = 0
     */
    const OXISDOWNLOADABLE = 'oxisdownloadable';

    /**
     * Show custom agreement check in checkout
     *
     * tinyint(1) = 1
     */
    const OXSHOWCUSTOMAGREEMENT = 'oxshowcustomagreement';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXATTRIBUTE
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXATTRIBUTE

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Sorting
     *
     * int(11) = 9999
     */
    const OXPOS = 'oxpos';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';

    /**
     * Display attribute`s value for articles in checkout
     *
     * tinyint(1) = 0
     */
    const OXDISPLAYINBASKET = 'oxdisplayinbasket';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXCATEGORIES
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXCATEGORIES

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Parent article id
     *
     * char(32)
     */
    const OXPARENTID = 'oxparentid';

    /**
     * Used for building category tree
     *
     * int(11) = 0
     */
    const OXLEFT = 'oxleft';

    /**
     * Used for building category tree
     *
     * int(11) = 0
     */
    const OXRIGHT = 'oxright';

    /**
     * Root category id
     *
     * char(32)
     */
    const OXROOTID = 'oxrootid';

    /**
     * Sorting
     *
     * int(5) = 0
     */
    const OXSORT = 'oxsort';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Hidden
     *
     * tinyint(1) = 0
     */
    const OXHIDDEN = 'oxhidden';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Description (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXDESC = 'oxdesc';

    /**
     * Long description, used for promotion (multilanguage)
     *
     * text-i18n
     */
    const OXLONGDESC = 'oxlongdesc';

    /**
     * Thumbnail filename
     *
     * varchar(128)
     */
    const OXTHUMB = 'oxthumb';

    /**
     * External link, that if specified is opened instead of category content
     *
     * varchar(255)
     */
    const OXEXTLINK = 'oxextlink';

    /**
     * Alternative template filename (if empty, default is used)
     *
     * varchar(128)
     */
    const OXTEMPLATE = 'oxtemplate';

    /**
     * Default field for sorting of articles in this category (most of oxarticles fields)
     *
     * varchar(64)
     */
    const OXDEFSORT = 'oxdefsort';

    /**
     * Default mode of sorting of articles in this category (0 - asc, 1 - desc)
     *
     * tinyint(1) = 0
     */
    const OXDEFSORTMODE = 'oxdefsortmode';

    /**
     * If specified, all articles, with price higher than specified, will be shown in this category
     *
     * double = 0
     */
    const OXPRICEFROM = 'oxpricefrom';

    /**
     * If specified, all articles, with price lower than specified, will be shown in this category
     *
     * double = 0
     */
    const OXPRICETO = 'oxpriceto';

    /**
     * Icon filename
     *
     * varchar(128)
     */
    const OXICON = 'oxicon';

    /**
     * Promotion icon filename
     *
     * varchar(128)
     */
    const OXPROMOICON = 'oxpromoicon';

    /**
     * Value added tax. If specified, used in all calculations instead of global vat
     *
     * float
     */
    const OXVAT = 'oxvat';

    /**
     * Skips all negative Discounts (Discounts, Vouchers, Delivery ...)
     *
     * tinyint(1) = 0
     */
    const OXSKIPDISCOUNTS = 'oxskipdiscounts';

    /**
     * Show SEO Suffix in Category
     *
     * tinyint(1) = 1
     */
    const OXSHOWSUFFIX = 'oxshowsuffix';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXCONTENTS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXCONTENTS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Id, specified by admin and can be used instead of oxid
     *
     * char(32)
     */
    const OXLOADID = 'oxloadid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Snippet (can be included to other oxcontents records)
     *
     * tinyint(1) = 1
     */
    const OXSNIPPET = 'oxsnippet';

    /**
     * Action type: 0 or 1 - action, 2 - promotion, 3 - banner
     *
     * tinyint(1)
     */
    const OXTYPE = 'oxtype';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Position
     *
     * varchar(32)
     */
    const OXPOSITION = 'oxposition';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Content (multilanguage)
     *
     * text-i18n
     */
    const OXCONTENT = 'oxcontent';

    /**
     * Category id (oxcategories), used only when type = 2
     *
     * varchar(32)
     */
    const OXCATID = 'oxcatid';

    /**
     * Folder
     *
     * varchar(32)
     */
    const OXFOLDER = 'oxfolder';

    /**
     * Term and Conditions version (used only when OXLOADID = oxagb)
     *
     * char(32)
     */
    const OXTERMVERSION = 'oxtermversion';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXCOUNTRY
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXCOUNTRY

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * ISO 3166-1 alpha-2
     *
     * char(2)
     */
    const OXISOALPHA2 = 'oxisoalpha2';

    /**
     * ISO 3166-1 alpha-3
     *
     * char(3)
     */
    const OXISOALPHA3 = 'oxisoalpha3';

    /**
     * ISO 3166-1 numeric
     *
     * char(3)
     */
    const OXUNNUM3 = 'oxunnum3';

    /**
     * VAT identification number prefix
     *
     * char(2)
     */
    const OXVATINPREFIX = 'oxvatinprefix';

    /**
     * Sorting
     *
     * int(11) = 9999
     */
    const OXORDER = 'oxorder';

    /**
     * Short description (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXSHORTDESC = 'oxshortdesc';

    /**
     * Long description, used for promotion (multilanguage)
     *
     * text-i18n
     */
    const OXLONGDESC = 'oxlongdesc';

    /**
     * Vat status: 0 - Do not bill VAT, 1 - Do not bill VAT only if provided valid VAT ID
     *
     * tinyint(1) = 0
     */
    const OXVATSTATUS = 'oxvatstatus';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXDELIVERY
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXDELIVERY

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Active from specified date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXACTIVEFROM = 'oxactivefrom';

    /**
     * Active to specified date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXACTIVETO = 'oxactiveto';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Price Surcharge/Reduction type (abs|%)
     *
     * enum(3) = abs
     */
    const OXADDSUMTYPE = 'oxaddsumtype';

    /**
     * Price Surcharge/Reduction amount
     *
     * double = 0
     */
    const OXADDSUM = 'oxaddsum';

    /**
     * Condition type: a - Amount, s - Size, w - Weight, p - Price
     *
     * enum(1) = a
     */
    const OXDELTYPE = 'oxdeltype';

    /**
     * Condition param from (e.g. amount from 1)
     *
     * double = 0
     */
    const OXPARAM = 'oxparam';

    /**
     * Condition param to (e.g. amount to 10)
     *
     * double = 0
     */
    const OXPARAMEND = 'oxparamend';

    /**
     * Calculation Rules: 0 - Once per Cart, 1 - Once for each different product, 2 - For each product
     *
     * tinyint(1) = 0
     */
    const OXFIXED = 'oxfixed';

    /**
     * Sorting
     *
     * int(5) = 0
     */
    const OXSORT = 'oxsort';

    /**
     * Do not run further rules if this rule is valid and is being run
     *
     * tinyint(1) = 0
     */
    const OXFINALIZE = 'oxfinalize';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXDELIVERYSET
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXDELIVERYSET

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Active from specified date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXACTIVEFROM = 'oxactivefrom';

    /**
     * Active to specified date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXACTIVETO = 'oxactiveto';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Sorting
     *
     * int(11) = 9999
     */
    const OXPOS = 'oxpos';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXDISCOUNT
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXDISCOUNT

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Active from specified date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXACTIVEFROM = 'oxactivefrom';

    /**
     * Active to specified date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXACTIVETO = 'oxactiveto';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Valid from specified amount of articles
     *
     * double = 0
     */
    const OXAMOUNT = 'oxamount';

    /**
     * Valid to specified amount of articles
     *
     * double = 999999
     */
    const OXAMOUNTTO = 'oxamountto';

    /**
     * If specified, all articles, with price lower than specified, will be shown in this category
     *
     * double = 0
     */
    const OXPRICETO = 'oxpriceto';

    /**
     * Article Price
     *
     * double = 0
     */
    const OXPRICE = 'oxprice';

    /**
     * Price Surcharge/Reduction type (abs|%)
     *
     * enum(3) = abs
     */
    const OXADDSUMTYPE = 'oxaddsumtype';

    /**
     * Price Surcharge/Reduction amount
     *
     * double = 0
     */
    const OXADDSUM = 'oxaddsum';

    /**
     * Free article id, that will be added as a discount
     *
     * char(32)
     */
    const OXITMARTID = 'oxitmartid';

    /**
     * The quantity of free article that will be added to basket with discounted article
     *
     * double = 1
     */
    const OXITMAMOUNT = 'oxitmamount';

    /**
     * Should free article amount be multiplied by discounted item quantity in basket
     *
     * int(1) = 0
     */
    const OXITMMULTIPLE = 'oxitmmultiple';

    /**
     * Sorting
     *
     * int(5) = 0
     */
    const OXSORT = 'oxsort';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXFILES
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXFILES

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Article id (oxarticles)
     *
     * char(32)
     */
    const OXARTID = 'oxartid';

    /**
     * Filename
     *
     * varchar(128)
     */
    const OXFILENAME = 'oxfilename';

    /**
     * Hashed filename, used for file directory path creation
     *
     * char(32)
     */
    const OXSTOREHASH = 'oxstorehash';

    /**
     * Download is available only after purchase
     *
     * tinyint(1) = 1
     */
    const OXPURCHASEDONLY = 'oxpurchasedonly';

    /**
     * Maximum count of downloads after order
     *
     * int(11) = -1
     */
    const OXMAXDOWNLOADS = 'oxmaxdownloads';

    /**
     * Maximum count of downloads for not registered users after order
     *
     * int(11) = -1
     */
    const OXMAXUNREGDOWNLOADS = 'oxmaxunregdownloads';

    /**
     * Expiration time of download link in hours
     *
     * int(11) = -1
     */
    const OXLINKEXPTIME = 'oxlinkexptime';

    /**
     * Expiration time of download link after the first download in hours
     *
     * int(11) = -1
     */
    const OXDOWNLOADEXPTIME = 'oxdownloadexptime';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXGROUPS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXGROUPS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXLINKS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXLINKS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Link url
     *
     * varchar(255)
     */
    const OXURL = 'oxurl';

    /**
     * Text for external URL (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXURLDESC = 'oxurldesc';

    /**
     * Creation time
     *
     * date = 0000-00-00
     */
    const OXINSERT = 'oxinsert';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXMANUFACTURERS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXMANUFACTURERS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Icon filename
     *
     * varchar(128)
     */
    const OXICON = 'oxicon';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Short description (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXSHORTDESC = 'oxshortdesc';

    /**
     * Show SEO Suffix in Category
     *
     * tinyint(1) = 1
     */
    const OXSHOWSUFFIX = 'oxshowsuffix';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXMEDIAURLS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXMEDIAURLS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Article id (oxarticles)
     *
     * char(32)
     */
    const OXOBJECTID = 'oxobjectid';

    /**
     * Link url
     *
     * varchar(255)
     */
    const OXURL = 'oxurl';

    /**
     * Description (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXDESC = 'oxdesc';

    /**
     * Is oxurl field used for filename or url
     *
     * int(1) = 0
     */
    const OXISUPLOADED = 'oxisuploaded';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXNEWS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXNEWS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Active from specified date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXACTIVEFROM = 'oxactivefrom';

    /**
     * Active to specified date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXACTIVETO = 'oxactiveto';

    /**
     * Creation date (entered by user)
     *
     * date = 0000-00-00
     */
    const OXDATE = 'oxdate';

    /**
     * Short description (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXSHORTDESC = 'oxshortdesc';

    /**
     * Long description, used for promotion (multilanguage)
     *
     * text-i18n
     */
    const OXLONGDESC = 'oxlongdesc';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXNEWSSUBSCRIBED
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXNEWSSUBSCRIBED

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * User id (oxuser)
     *
     * char(32)
     */
    const OXUSERID = 'oxuserid';

    /**
     * User title prefix (Mr/Mrs)
     *
     * varchar(128)
     */
    const OXSAL = 'oxsal';

    /**
     * First name
     *
     * varchar(255)
     */
    const OXFNAME = 'oxfname';

    /**
     * Last name
     *
     * varchar(255)
     */
    const OXLNAME = 'oxlname';

    /**
     * Email
     *
     * char(128)
     */
    const OXEMAIL = 'oxemail';

    /**
     * Subscription status: 0 - not subscribed, 1 - subscribed, 2 - not confirmed
     *
     * tinyint(1) = 0
     */
    const OXDBOPTIN = 'oxdboptin';

    /**
     * Subscription email sending status
     *
     * tinyint(1) = 0
     */
    const OXEMAILFAILED = 'oxemailfailed';

    /**
     * Subscription date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXSUBSCRIBED = 'oxsubscribed';

    /**
     * Unsubscription date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXUNSUBSCRIBED = 'oxunsubscribed';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXNEWSLETTER
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXNEWSLETTER

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Alternative template filename (if empty, default is used)
     *
     * varchar(128)
     */
    const OXTEMPLATE = 'oxtemplate';

    /**
     * Plain template
     *
     * mediumtext
     */
    const OXPLAINTEMPLATE = 'oxplaintemplate';

    /**
     * Subject
     *
     * varchar(255)
     */
    const OXSUBJECT = 'oxsubject';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXOBJECT2CATEGORY
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXOBJECT2CATEGORY

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Article id (oxarticles)
     *
     * char(32)
     */
    const OXOBJECTID = 'oxobjectid';

    /**
     * Category id (oxcategory)
     *
     * char(32)
     */
    const OXCATNID = 'oxcatnid';

    /**
     * Sorting
     *
     * int(11) = 9999
     */
    const OXPOS = 'oxpos';

    /**
     * Creation time
     *
     * int(11) = 0
     */
    const OXTIME = 'oxtime';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXOBJECT2GROUP
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXOBJECT2GROUP

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Article id (oxarticles)
     *
     * char(32)
     */
    const OXOBJECTID = 'oxobjectid';

    /**
     * Group id
     *
     * char(32)
     */
    const OXGROUPSID = 'oxgroupsid';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXORDER
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXORDER

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * User id (oxuser)
     *
     * char(32)
     */
    const OXUSERID = 'oxuserid';

    /**
     * Order date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXORDERDATE = 'oxorderdate';

    /**
     * Order number
     *
     * int(11) = 0
     */
    const OXORDERNR = 'oxordernr';

    /**
     * Billing info: Company name
     *
     * varchar(255)
     */
    const OXBILLCOMPANY = 'oxbillcompany';

    /**
     * Billing info: Email
     *
     * varchar(255)
     */
    const OXBILLEMAIL = 'oxbillemail';

    /**
     * Billing info: First name
     *
     * varchar(255)
     */
    const OXBILLFNAME = 'oxbillfname';

    /**
     * Billing info: Last name
     *
     * varchar(255)
     */
    const OXBILLLNAME = 'oxbilllname';

    /**
     * Billing info: Street name
     *
     * varchar(255)
     */
    const OXBILLSTREET = 'oxbillstreet';

    /**
     * Billing info: House number
     *
     * varchar(16)
     */
    const OXBILLSTREETNR = 'oxbillstreetnr';

    /**
     * Billing info: Additional info
     *
     * varchar(255)
     */
    const OXBILLADDINFO = 'oxbilladdinfo';

    /**
     * Billing info: VAT ID No.
     *
     * varchar(255)
     */
    const OXBILLUSTID = 'oxbillustid';

    /**
     * Billing info: City
     *
     * varchar(255)
     */
    const OXBILLCITY = 'oxbillcity';

    /**
     * Billing info: Country id (oxcountry)
     *
     * varchar(32)
     */
    const OXBILLCOUNTRYID = 'oxbillcountryid';

    /**
     * Billing info: US State id (oxstates)
     *
     * varchar(32)
     */
    const OXBILLSTATEID = 'oxbillstateid';

    /**
     * Billing info: Zip code
     *
     * varchar(16)
     */
    const OXBILLZIP = 'oxbillzip';

    /**
     * Billing info: Phone number
     *
     * varchar(128)
     */
    const OXBILLFON = 'oxbillfon';

    /**
     * Billing info: Fax number
     *
     * varchar(128)
     */
    const OXBILLFAX = 'oxbillfax';

    /**
     * Billing info: User title prefix (Mr/Mrs)
     *
     * varchar(128)
     */
    const OXBILLSAL = 'oxbillsal';

    /**
     * Shipping info: Company name
     *
     * varchar(255)
     */
    const OXDELCOMPANY = 'oxdelcompany';

    /**
     * Shipping info: First name
     *
     * varchar(255)
     */
    const OXDELFNAME = 'oxdelfname';

    /**
     * Shipping info: Last name
     *
     * varchar(255)
     */
    const OXDELLNAME = 'oxdellname';

    /**
     * Shipping info: Street name
     *
     * varchar(255)
     */
    const OXDELSTREET = 'oxdelstreet';

    /**
     * Shipping info: House number
     *
     * varchar(16)
     */
    const OXDELSTREETNR = 'oxdelstreetnr';

    /**
     * Shipping info: Additional info
     *
     * varchar(255)
     */
    const OXDELADDINFO = 'oxdeladdinfo';

    /**
     * Shipping info: City
     *
     * varchar(255)
     */
    const OXDELCITY = 'oxdelcity';

    /**
     * Shipping info: Country id (oxcountry)
     *
     * varchar(32)
     */
    const OXDELCOUNTRYID = 'oxdelcountryid';

    /**
     * Shipping info: US State id (oxstates)
     *
     * varchar(32)
     */
    const OXDELSTATEID = 'oxdelstateid';

    /**
     * Shipping info: Zip code
     *
     * varchar(16)
     */
    const OXDELZIP = 'oxdelzip';

    /**
     * Shipping info: Phone number
     *
     * varchar(128)
     */
    const OXDELFON = 'oxdelfon';

    /**
     * Shipping info: Fax number
     *
     * varchar(128)
     */
    const OXDELFAX = 'oxdelfax';

    /**
     * Shipping info: User title prefix (Mr/Mrs)
     *
     * varchar(128)
     */
    const OXDELSAL = 'oxdelsal';

    /**
     * User payment id (oxuserpayments)
     *
     * char(32)
     */
    const OXPAYMENTID = 'oxpaymentid';

    /**
     * Payment id (oxpayments)
     *
     * char(32)
     */
    const OXPAYMENTTYPE = 'oxpaymenttype';

    /**
     * Total net sum
     *
     * double = 0
     */
    const OXTOTALNETSUM = 'oxtotalnetsum';

    /**
     * Total brut sum
     *
     * double = 0
     */
    const OXTOTALBRUTSUM = 'oxtotalbrutsum';

    /**
     * Total order sum
     *
     * double = 0
     */
    const OXTOTALORDERSUM = 'oxtotalordersum';

    /**
     * First VAT
     *
     * double = 0
     */
    const OXARTVAT1 = 'oxartvat1';

    /**
     * First calculated VAT price
     *
     * double = 0
     */
    const OXARTVATPRICE1 = 'oxartvatprice1';

    /**
     * Second VAT
     *
     * double = 0
     */
    const OXARTVAT2 = 'oxartvat2';

    /**
     * Second calculated VAT price
     *
     * double = 0
     */
    const OXARTVATPRICE2 = 'oxartvatprice2';

    /**
     * Delivery price
     *
     * double = 0
     */
    const OXDELCOST = 'oxdelcost';

    /**
     * Delivery VAT
     *
     * double = 0
     */
    const OXDELVAT = 'oxdelvat';

    /**
     * Payment cost
     *
     * double = 0
     */
    const OXPAYCOST = 'oxpaycost';

    /**
     * Payment VAT
     *
     * double = 0
     */
    const OXPAYVAT = 'oxpayvat';

    /**
     * Wrapping cost
     *
     * double = 0
     */
    const OXWRAPCOST = 'oxwrapcost';

    /**
     * Wrapping VAT
     *
     * double = 0
     */
    const OXWRAPVAT = 'oxwrapvat';

    /**
     * Giftcard cost
     *
     * double = 0
     */
    const OXGIFTCARDCOST = 'oxgiftcardcost';

    /**
     * Giftcard VAT
     *
     * double = 0
     */
    const OXGIFTCARDVAT = 'oxgiftcardvat';

    /**
     * Gift card id (oxwrapping)
     *
     * varchar(32)
     */
    const OXCARDID = 'oxcardid';

    /**
     * Gift card text
     *
     * text
     */
    const OXCARDTEXT = 'oxcardtext';

    /**
     * Additional discount for order (abs)
     *
     * double = 0
     */
    const OXDISCOUNT = 'oxdiscount';

    /**
     * Is exported
     *
     * tinyint(4) = 0
     */
    const OXEXPORT = 'oxexport';

    /**
     * Invoice No.
     *
     * varchar(128)
     */
    const OXBILLNR = 'oxbillnr';

    /**
     * Invoice sent date
     *
     * date = 0000-00-00
     */
    const OXBILLDATE = 'oxbilldate';

    /**
     * Tracking code
     *
     * varchar(128)
     */
    const OXTRACKCODE = 'oxtrackcode';

    /**
     * Order shipping date
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXSENDDATE = 'oxsenddate';

    /**
     * User remarks
     *
     * text
     */
    const OXREMARK = 'oxremark';

    /**
     * Coupon (voucher) discount price
     *
     * double = 0
     */
    const OXVOUCHERDISCOUNT = 'oxvoucherdiscount';

    /**
     * Currency
     *
     * varchar(32)
     */
    const OXCURRENCY = 'oxcurrency';

    /**
     * Currency rate
     *
     * double = 0
     */
    const OXCURRATE = 'oxcurrate';

    /**
     * Folder
     *
     * varchar(32)
     */
    const OXFOLDER = 'oxfolder';

    /**
     * Paypal: Transaction id
     *
     * varchar(64)
     */
    const OXTRANSID = 'oxtransid';

    /**
     *
     *
     * varchar(64)
     */
    const OXPAYID = 'oxpayid';

    /**
     *
     *
     * varchar(64)
     */
    const OXXID = 'oxxid';

    /**
     * Time, when order was paid
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXPAID = 'oxpaid';

    /**
     * Order cancelled
     *
     * tinyint(1) = 0
     */
    const OXSTORNO = 'oxstorno';

    /**
     * User ip address
     *
     * varchar(39)
     */
    const OXIP = 'oxip';

    /**
     * Order status: NOT_FINISHED, OK, ERROR
     *
     * varchar(30)
     */
    const OXTRANSSTATUS = 'oxtransstatus';

    /**
     * Language id
     *
     * int(2) = 0
     */
    const OXLANG = 'oxlang';

    /**
     * Invoice number
     *
     * int(11) = 0
     */
    const OXINVOICENR = 'oxinvoicenr';

    /**
     * Condition type: a - Amount, s - Size, w - Weight, p - Price
     *
     * enum(1) = a
     */
    const OXDELTYPE = 'oxdeltype';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';

    /**
     * Order created in netto mode
     *
     * tinyint(1) = 0
     */
    const OXISNETTOMODE = 'oxisnettomode';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXORDERARTICLES
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXORDERARTICLES

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Order id (oxorder)
     *
     * char(32)
     */
    const OXORDERID = 'oxorderid';

    /**
     * Valid from specified amount of articles
     *
     * double = 0
     */
    const OXAMOUNT = 'oxamount';

    /**
     * Article id (oxarticles)
     *
     * char(32)
     */
    const OXARTID = 'oxartid';

    /**
     * Article number
     *
     * varchar(255)
     */
    const OXARTNUM = 'oxartnum';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Short description (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXSHORTDESC = 'oxshortdesc';

    /**
     * Selected variant
     *
     * varchar(255)
     */
    const OXSELVARIANT = 'oxselvariant';

    /**
     * Full netto price (oxnprice * oxamount)
     *
     * double = 0
     */
    const OXNETPRICE = 'oxnetprice';

    /**
     * Full brutto price (oxbprice * oxamount)
     *
     * double = 0
     */
    const OXBRUTPRICE = 'oxbrutprice';

    /**
     * Calculated VAT price
     *
     * double = 0
     */
    const OXVATPRICE = 'oxvatprice';

    /**
     * Value added tax. If specified, used in all calculations instead of global vat
     *
     * float
     */
    const OXVAT = 'oxvat';

    /**
     * Serialized persistent parameters
     *
     * text
     */
    const OXPERSPARAM = 'oxpersparam';

    /**
     * Article Price
     *
     * double = 0
     */
    const OXPRICE = 'oxprice';

    /**
     * Purchase Price
     *
     * double = 0
     */
    const OXBPRICE = 'oxbprice';

    /**
     * Netto price for one item
     *
     * double = 0
     */
    const OXNPRICE = 'oxnprice';

    /**
     * Wrapping id (oxwrapping)
     *
     * varchar(32)
     */
    const OXWRAPID = 'oxwrapid';

    /**
     * External URL to other information about the article
     *
     * varchar(255)
     */
    const OXEXTURL = 'oxexturl';

    /**
     * Text for external URL (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXURLDESC = 'oxurldesc';

    /**
     * External URL image
     *
     * varchar(128)
     */
    const OXURLIMG = 'oxurlimg';

    /**
     * Thumbnail filename
     *
     * varchar(128)
     */
    const OXTHUMB = 'oxthumb';

    /**
     * 1# Picture filename
     *
     * varchar(128)
     */
    const OXPIC1 = 'oxpic1';

    /**
     * 2# Picture filename
     *
     * varchar(128)
     */
    const OXPIC2 = 'oxpic2';

    /**
     * 3# Picture filename
     *
     * varchar(128)
     */
    const OXPIC3 = 'oxpic3';

    /**
     * 4# Picture filename
     *
     * varchar(128)
     */
    const OXPIC4 = 'oxpic4';

    /**
     * 5# Picture filename
     *
     * varchar(128)
     */
    const OXPIC5 = 'oxpic5';

    /**
     * Weight (kg)
     *
     * double = 0
     */
    const OXWEIGHT = 'oxweight';

    /**
     * Article quantity in stock
     *
     * double = 0
     */
    const OXSTOCK = 'oxstock';

    /**
     * Date, when the product will be available again if it is sold out
     *
     * date = 0000-00-00
     */
    const OXDELIVERY = 'oxdelivery';

    /**
     * Creation time
     *
     * date = 0000-00-00
     */
    const OXINSERT = 'oxinsert';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';

    /**
     * Article dimensions: Length
     *
     * double = 0
     */
    const OXLENGTH = 'oxlength';

    /**
     * Article dimensions: Width
     *
     * double = 0
     */
    const OXWIDTH = 'oxwidth';

    /**
     * Article dimensions: Height
     *
     * double = 0
     */
    const OXHEIGHT = 'oxheight';

    /**
     * File, shown in article media list
     *
     * varchar(128)
     */
    const OXFILE = 'oxfile';

    /**
     * Search terms (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXSEARCHKEYS = 'oxsearchkeys';

    /**
     * Alternative template filename (if empty, default is used)
     *
     * varchar(128)
     */
    const OXTEMPLATE = 'oxtemplate';

    /**
     * E-mail for question
     *
     * varchar(255)
     */
    const OXQUESTIONEMAIL = 'oxquestionemail';

    /**
     * Should article be shown in search
     *
     * tinyint(1) = 1
     */
    const OXISSEARCH = 'oxissearch';

    /**
     * Folder
     *
     * varchar(32)
     */
    const OXFOLDER = 'oxfolder';

    /**
     * Subclass
     *
     * varchar(32)
     */
    const OXSUBCLASS = 'oxsubclass';

    /**
     * Order cancelled
     *
     * tinyint(1) = 0
     */
    const OXSTORNO = 'oxstorno';

    /**
     * Shop id (oxshops), in which order was done
     *
     * int(11) = 1
     */
    const OXORDERSHOPID = 'oxordershopid';

    /**
     * Bundled article
     *
     * tinyint(1) = 0
     */
    const OXISBUNDLE = 'oxisbundle';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXORDERFILES
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXORDERFILES

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Order id (oxorder)
     *
     * char(32)
     */
    const OXORDERID = 'oxorderid';

    /**
     * Filename
     *
     * varchar(128)
     */
    const OXFILENAME = 'oxfilename';

    /**
     * File id (oxfiles)
     *
     * char(32)
     */
    const OXFILEID = 'oxfileid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Ordered article id (oxorderarticles)
     *
     * char(32)
     */
    const OXORDERARTICLEID = 'oxorderarticleid';

    /**
     * First time downloaded time
     *
     * timestamp = 0000-00-00 00:00:00
     */
    const OXFIRSTDOWNLOAD = 'oxfirstdownload';

    /**
     * Last time downloaded time
     *
     * timestamp = 0000-00-00 00:00:00
     */
    const OXLASTDOWNLOAD = 'oxlastdownload';

    /**
     * Downloads count
     *
     * int(10)
     */
    const OXDOWNLOADCOUNT = 'oxdownloadcount';

    /**
     * Maximum count of downloads
     *
     * int(10)
     */
    const OXMAXDOWNLOADCOUNT = 'oxmaxdownloadcount';

    /**
     * Download expiration time in hours
     *
     * int(10)
     */
    const OXDOWNLOADEXPIRATIONTIME = 'oxdownloadexpirationtime';

    /**
     * Link expiration time in hours
     *
     * int(10)
     */
    const OXLINKEXPIRATIONTIME = 'oxlinkexpirationtime';

    /**
     * Count of resets
     *
     * int(10)
     */
    const OXRESETCOUNT = 'oxresetcount';

    /**
     * Download is valid until time specified
     *
     * datetime
     */
    const OXVALIDUNTIL = 'oxvaliduntil';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXPAYMENTS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXPAYMENTS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Description (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXDESC = 'oxdesc';

    /**
     * Price Surcharge/Reduction amount
     *
     * double = 0
     */
    const OXADDSUM = 'oxaddsum';

    /**
     * Price Surcharge/Reduction type (abs|%)
     *
     * enum(3) = abs
     */
    const OXADDSUMTYPE = 'oxaddsumtype';

    /**
     * Base of price surcharge/reduction: 1 - Value of all goods in cart, 2 - Discounts, 4 - Vouchers, 8 - Shipping costs, 16 - Gift Wrapping/Greeting Card
     *
     * int(11) = 0
     */
    const OXADDSUMRULES = 'oxaddsumrules';

    /**
     * Minimal Credit Rating
     *
     * int(11) = 0
     */
    const OXFROMBONI = 'oxfromboni';

    /**
     * Purchase Price: From
     *
     * double = 0
     */
    const OXFROMAMOUNT = 'oxfromamount';

    /**
     * Purchase Price: To
     *
     * double = 0
     */
    const OXTOAMOUNT = 'oxtoamount';

    /**
     * Payment additional fields, separated by "field1__@@field2" (multilanguage)
     *
     * text-i18n
     */
    const OXVALDESC = 'oxvaldesc';

    /**
     * Selected as the default method
     *
     * tinyint(1) = 0
     */
    const OXCHECKED = 'oxchecked';

    /**
     * Long description, used for promotion (multilanguage)
     *
     * text-i18n
     */
    const OXLONGDESC = 'oxlongdesc';

    /**
     * Sorting
     *
     * int(5) = 0
     */
    const OXSORT = 'oxsort';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXPRICEALARM
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXPRICEALARM

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * User id (oxuser)
     *
     * char(32)
     */
    const OXUSERID = 'oxuserid';

    /**
     * Email
     *
     * char(128)
     */
    const OXEMAIL = 'oxemail';

    /**
     * Article id (oxarticles)
     *
     * char(32)
     */
    const OXARTID = 'oxartid';

    /**
     * Article Price
     *
     * double = 0
     */
    const OXPRICE = 'oxprice';

    /**
     * Currency
     *
     * varchar(32)
     */
    const OXCURRENCY = 'oxcurrency';

    /**
     * Language id
     *
     * int(2) = 0
     */
    const OXLANG = 'oxlang';

    /**
     * Creation time
     *
     * date = 0000-00-00
     */
    const OXINSERT = 'oxinsert';

    /**
     * Time, when notification was sent
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXSENDED = 'oxsended';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXRATINGS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXRATINGS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * User id (oxuser)
     *
     * char(32)
     */
    const OXUSERID = 'oxuserid';

    /**
     * Action type: 0 or 1 - action, 2 - promotion, 3 - banner
     *
     * tinyint(1)
     */
    const OXTYPE = 'oxtype';

    /**
     * Article id (oxarticles)
     *
     * char(32)
     */
    const OXOBJECTID = 'oxobjectid';

    /**
     * Article rating
     *
     * double = 0
     */
    const OXRATING = 'oxrating';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXRECOMMLISTS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXRECOMMLISTS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * User id (oxuser)
     *
     * char(32)
     */
    const OXUSERID = 'oxuserid';

    /**
     * Author first and last name
     *
     * varchar(255)
     */
    const OXAUTHOR = 'oxauthor';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Description (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXDESC = 'oxdesc';

    /**
     * Rating votes count
     *
     * int(11) = 0
     */
    const OXRATINGCNT = 'oxratingcnt';

    /**
     * Article rating
     *
     * double = 0
     */
    const OXRATING = 'oxrating';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXREMARK
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXREMARK

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Parent article id
     *
     * char(32)
     */
    const OXPARENTID = 'oxparentid';

    /**
     * Action type: 0 or 1 - action, 2 - promotion, 3 - banner
     *
     * tinyint(1)
     */
    const OXTYPE = 'oxtype';

    /**
     * Header (default: Creation time)
     *
     * varchar(255)
     */
    const OXHEADER = 'oxheader';

    /**
     * Remark text
     *
     * text
     */
    const OXTEXT = 'oxtext';

    /**
     * Creation time
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXCREATE = 'oxcreate';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXREVIEWS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXREVIEWS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Article id (oxarticles)
     *
     * char(32)
     */
    const OXOBJECTID = 'oxobjectid';

    /**
     * Action type: 0 or 1 - action, 2 - promotion, 3 - banner
     *
     * tinyint(1)
     */
    const OXTYPE = 'oxtype';

    /**
     * Remark text
     *
     * text
     */
    const OXTEXT = 'oxtext';

    /**
     * User id (oxuser)
     *
     * char(32)
     */
    const OXUSERID = 'oxuserid';

    /**
     * Creation time
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXCREATE = 'oxcreate';

    /**
     * Language id
     *
     * int(2) = 0
     */
    const OXLANG = 'oxlang';

    /**
     * Article rating
     *
     * double = 0
     */
    const OXRATING = 'oxrating';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXSELECTLIST
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXSELECTLIST

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Working Title
     *
     * varchar(255)
     */
    const OXIDENT = 'oxident';

    /**
     * Payment additional fields, separated by "field1__@@field2" (multilanguage)
     *
     * text-i18n
     */
    const OXVALDESC = 'oxvaldesc';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXSHOPS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXSHOPS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Productive Mode (if 0, debug info displayed)
     *
     * tinyint(1) = 0
     */
    const OXPRODUCTIVE = 'oxproductive';

    /**
     * Default currency
     *
     * varchar(32)
     */
    const OXDEFCURRENCY = 'oxdefcurrency';

    /**
     * Default language id
     *
     * int(11) = 0
     */
    const OXDEFLANGUAGE = 'oxdeflanguage';

    /**
     * Shop name
     *
     * varchar(255)
     */
    const OXNAME = 'oxname';

    /**
     * Seo title prefix (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXTITLEPREFIX = 'oxtitleprefix';

    /**
     * Seo title suffix (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXTITLESUFFIX = 'oxtitlesuffix';

    /**
     * Start page title (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXSTARTTITLE = 'oxstarttitle';

    /**
     * Informational email address
     *
     * varchar(255)
     */
    const OXINFOEMAIL = 'oxinfoemail';

    /**
     * Order email address
     *
     * varchar(255)
     */
    const OXORDEREMAIL = 'oxorderemail';

    /**
     * Owner email address
     *
     * varchar(255)
     */
    const OXOWNEREMAIL = 'oxowneremail';

    /**
     * Order email subject (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXORDERSUBJECT = 'oxordersubject';

    /**
     * Registration email subject (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXREGISTERSUBJECT = 'oxregistersubject';

    /**
     * Forgot password email subject (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXFORGOTPWDSUBJECT = 'oxforgotpwdsubject';

    /**
     * Order sent email subject (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXSENDEDNOWSUBJECT = 'oxsendednowsubject';

    /**
     * SMTP server
     *
     * varchar(255)
     */
    const OXSMTP = 'oxsmtp';

    /**
     * SMTP user
     *
     * varchar(128)
     */
    const OXSMTPUSER = 'oxsmtpuser';

    /**
     * SMTP password
     *
     * varchar(128)
     */
    const OXSMTPPWD = 'oxsmtppwd';

    /**
     * Company name
     *
     * varchar(255)
     */
    const OXCOMPANY = 'oxcompany';

    /**
     * Street
     *
     * varchar(255)
     */
    const OXSTREET = 'oxstreet';

    /**
     * Zip code
     *
     * varchar(50)
     */
    const OXZIP = 'oxzip';

    /**
     * City
     *
     * varchar(255)
     */
    const OXCITY = 'oxcity';

    /**
     * Country name
     *
     * varchar(255)
     */
    const OXCOUNTRY = 'oxcountry';

    /**
     * Bank name
     *
     * varchar(255)
     */
    const OXBANKNAME = 'oxbankname';

    /**
     * Account Number
     *
     * varchar(255)
     */
    const OXBANKNUMBER = 'oxbanknumber';

    /**
     * Routing Number
     *
     * varchar(255)
     */
    const OXBANKCODE = 'oxbankcode';

    /**
     * Sales Tax ID
     *
     * varchar(255)
     */
    const OXVATNUMBER = 'oxvatnumber';

    /**
     * Tax ID
     *
     * varchar(255)
     */
    const OXTAXNUMBER = 'oxtaxnumber';

    /**
     * Bank BIC
     *
     * varchar(255)
     */
    const OXBICCODE = 'oxbiccode';

    /**
     * Bank IBAN
     *
     * varchar(255)
     */
    const OXIBANNUMBER = 'oxibannumber';

    /**
     * First name
     *
     * varchar(255)
     */
    const OXFNAME = 'oxfname';

    /**
     * Last name
     *
     * varchar(255)
     */
    const OXLNAME = 'oxlname';

    /**
     * Phone number
     *
     * varchar(255)
     */
    const OXTELEFON = 'oxtelefon';

    /**
     * Fax number
     *
     * varchar(255)
     */
    const OXTELEFAX = 'oxtelefax';

    /**
     * Link url
     *
     * varchar(255)
     */
    const OXURL = 'oxurl';

    /**
     * Default category id
     *
     * char(32)
     */
    const OXDEFCAT = 'oxdefcat';

    /**
     * CBR
     *
     * varchar(64)
     */
    const OXHRBNR = 'oxhrbnr';

    /**
     * District Court
     *
     * varchar(128)
     */
    const OXCOURT = 'oxcourt';

    /**
     * Adbutler code (belboon.de) - deprecated
     *
     * varchar(64)
     */
    const OXADBUTLERID = 'oxadbutlerid';

    /**
     * Affilinet code (webmasterplan.com) - deprecated
     *
     * varchar(64)
     */
    const OXAFFILINETID = 'oxaffilinetid';

    /**
     * Superclix code (superclix.de) - deprecated
     *
     * varchar(64)
     */
    const OXSUPERCLICKSID = 'oxsuperclicksid';

    /**
     * Affiliwelt code (affiliwelt.net) - deprecated
     *
     * varchar(64)
     */
    const OXAFFILIWELTID = 'oxaffiliweltid';

    /**
     * Affili24 code (affili24.com) - deprecated
     *
     * varchar(64)
     */
    const OXAFFILI24ID = 'oxaffili24id';

    /**
     * Shop Edition (CE,PE,EE (@deprecated since v6.0.0-RC.2 (2017-08-24))
     *
     * char(2)
     */
    const OXEDITION = 'oxedition';

    /**
     * Shop Version (@deprecated since v6.0.0-RC.2 (2017-08-22))
     *
     * char(16)
     */
    const OXVERSION = 'oxversion';

    /**
     * Seo active (multilanguage)
     *
     * tinyint-i18n(1) = 1
     */
    const OXSEOACTIVE = 'oxseoactive';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXSTATES
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXSTATES

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Country id (oxcountry)
     *
     * char(32)
     */
    const OXCOUNTRYID = 'oxcountryid';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * ISO 3166-1 alpha-2
     *
     * char(2)
     */
    const OXISOALPHA2 = 'oxisoalpha2';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXUSER
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXUSER

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * User rights: user, malladmin
     *
     * char(32)
     */
    const OXRIGHTS = 'oxrights';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Username
     *
     * varchar(255)
     */
    const OXUSERNAME = 'oxusername';

    /**
     * Hashed password
     *
     * varchar(128)
     */
    const OXPASSWORD = 'oxpassword';

    /**
     * Password salt
     *
     * char(128)
     */
    const OXPASSSALT = 'oxpasssalt';

    /**
     * Customer number
     *
     * int(11)
     */
    const OXCUSTNR = 'oxcustnr';

    /**
     * VAT ID No.
     *
     * varchar(255)
     */
    const OXUSTID = 'oxustid';

    /**
     * Company name
     *
     * varchar(255)
     */
    const OXCOMPANY = 'oxcompany';

    /**
     * First name
     *
     * varchar(255)
     */
    const OXFNAME = 'oxfname';

    /**
     * Last name
     *
     * varchar(255)
     */
    const OXLNAME = 'oxlname';

    /**
     * Street
     *
     * varchar(255)
     */
    const OXSTREET = 'oxstreet';

    /**
     * House number
     *
     * varchar(16)
     */
    const OXSTREETNR = 'oxstreetnr';

    /**
     * Additional info
     *
     * varchar(255)
     */
    const OXADDINFO = 'oxaddinfo';

    /**
     * City
     *
     * varchar(255)
     */
    const OXCITY = 'oxcity';

    /**
     * Country id (oxcountry)
     *
     * char(32)
     */
    const OXCOUNTRYID = 'oxcountryid';

    /**
     * State id (oxstate)
     *
     * varchar(32)
     */
    const OXSTATEID = 'oxstateid';

    /**
     * Zip code
     *
     * varchar(50)
     */
    const OXZIP = 'oxzip';

    /**
     * Phone number
     *
     * varchar(128)
     */
    const OXFON = 'oxfon';

    /**
     * Fax number
     *
     * varchar(128)
     */
    const OXFAX = 'oxfax';

    /**
     * User title prefix (Mr/Mrs)
     *
     * varchar(128)
     */
    const OXSAL = 'oxsal';

    /**
     * Credit points
     *
     * int(11) = 0
     */
    const OXBONI = 'oxboni';

    /**
     * Creation time
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXCREATE = 'oxcreate';

    /**
     * Registration time
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXREGISTER = 'oxregister';

    /**
     * Personal phone number
     *
     * varchar(64)
     */
    const OXPRIVFON = 'oxprivfon';

    /**
     * Mobile phone number
     *
     * varchar(64)
     */
    const OXMOBFON = 'oxmobfon';

    /**
     * Birthday date
     *
     * date = 0000-00-00
     */
    const OXBIRTHDATE = 'oxbirthdate';

    /**
     * Link url
     *
     * varchar(255)
     */
    const OXURL = 'oxurl';

    /**
     * Update key
     *
     * varchar(32)
     */
    const OXUPDATEKEY = 'oxupdatekey';

    /**
     * Update key expiration time
     *
     * int(11) = 0
     */
    const OXUPDATEEXP = 'oxupdateexp';

    /**
     * User points (for registration, invitation, etc)
     *
     * double = 0
     */
    const OXPOINTS = 'oxpoints';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXUSERBASKETS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXUSERBASKETS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * User id (oxuser)
     *
     * char(32)
     */
    const OXUSERID = 'oxuserid';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';

    /**
     * Is public
     *
     * tinyint(1) = 1
     */
    const OXPUBLIC = 'oxpublic';

    /**
     * Update timestamp
     *
     * int(11) = 0
     */
    const OXUPDATE = 'oxupdate';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXUSERBASKETITEMS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXUSERBASKETITEMS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Basket id (oxuserbaskets)
     *
     * char(32)
     */
    const OXBASKETID = 'oxbasketid';

    /**
     * Article id (oxarticles)
     *
     * char(32)
     */
    const OXARTID = 'oxartid';

    /**
     * Valid from specified amount of articles
     *
     * double = 0
     */
    const OXAMOUNT = 'oxamount';

    /**
     * Selection list
     *
     * varchar(255)
     */
    const OXSELLIST = 'oxsellist';

    /**
     * Serialized persistent parameters
     *
     * text
     */
    const OXPERSPARAM = 'oxpersparam';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXUSERPAYMENTS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXUSERPAYMENTS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * User id (oxuser)
     *
     * char(32)
     */
    const OXUSERID = 'oxuserid';

    /**
     * Payment id (oxpayments)
     *
     * char(32)
     */
    const OXPAYMENTSID = 'oxpaymentsid';

    /**
     * DYN payment values array as string
     *
     * blob
     */
    const OXVALUE = 'oxvalue';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXVENDOR
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXVENDOR

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Icon filename
     *
     * varchar(128)
     */
    const OXICON = 'oxicon';

    /**
     * Title (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXTITLE = 'oxtitle';

    /**
     * Short description (multilanguage)
     *
     * varchar-i18n(255)
     */
    const OXSHORTDESC = 'oxshortdesc';

    /**
     * Show SEO Suffix in Category
     *
     * tinyint(1) = 1
     */
    const OXSHOWSUFFIX = 'oxshowsuffix';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXVOUCHERS
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXVOUCHERS

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Date, when coupon was used (set on order complete)
     *
     * date
     */
    const OXDATEUSED = 'oxdateused';

    /**
     * Order id (oxorder)
     *
     * char(32)
     */
    const OXORDERID = 'oxorderid';

    /**
     * User id (oxuser)
     *
     * char(32)
     */
    const OXUSERID = 'oxuserid';

    /**
     * Time, when coupon is added to basket
     *
     * int(11) = 0
     */
    const OXRESERVED = 'oxreserved';

    /**
     * Coupon number
     *
     * varchar(255)
     */
    const OXVOUCHERNR = 'oxvouchernr';

    /**
     * Coupon Series id (oxvoucherseries)
     *
     * char(32)
     */
    const OXVOUCHERSERIEID = 'oxvoucherserieid';

    /**
     * Additional discount for order (abs)
     *
     * double = 0
     */
    const OXDISCOUNT = 'oxdiscount';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXVOUCHERSERIES
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXVOUCHERSERIES

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Series name
     *
     * varchar(255)
     */
    const OXSERIENR = 'oxserienr';

    /**
     * Description
     *
     * varchar(255)
     */
    const OXSERIEDESCRIPTION = 'oxseriedescription';

    /**
     * Additional discount for order (abs)
     *
     * double = 0
     */
    const OXDISCOUNT = 'oxdiscount';

    /**
     * Discount type (percent, absolute)
     *
     * enum(8) = absolute
     */
    const OXDISCOUNTTYPE = 'oxdiscounttype';

    /**
     * Valid from
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXBEGINDATE = 'oxbegindate';

    /**
     * Valid to
     *
     * datetime = 0000-00-00 00:00:00
     */
    const OXENDDATE = 'oxenddate';

    /**
     * Coupons of this series can be used with single order
     *
     * tinyint(1) = 0
     */
    const OXALLOWSAMESERIES = 'oxallowsameseries';

    /**
     * Coupons of different series can be used with single order
     *
     * tinyint(1) = 0
     */
    const OXALLOWOTHERSERIES = 'oxallowotherseries';

    /**
     * Coupons of this series can be used in multiple orders
     *
     * tinyint(1) = 0
     */
    const OXALLOWUSEANOTHER = 'oxallowuseanother';

    /**
     * Minimum Order Sum
     *
     * float(9) = 0.00
     */
    const OXMINIMUMVALUE = 'oxminimumvalue';

    /**
     * Calculate only once (valid only for product or category vouchers)
     *
     * tinyint(1) = 0
     */
    const OXCALCULATEONCE = 'oxcalculateonce';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}

/**
 * @see \OxidEsales\Eshop\Core\Database\TABLE\OXWRAPPING
 */
namespace OxidEsales\Eshop\Core\Database\TABLE\OXWRAPPING

{

    /**
     * Action id
     *
     * char(32)
     */
    const OXID = 'oxid';

    /**
     * Shop id (oxshops)
     *
     * int(11) = 1
     */
    const OXSHOPID = 'oxshopid';

    /**
     * Active
     *
     * tinyint(1) = 1
     */
    const OXACTIVE = 'oxactive';

    /**
     * Action type: 0 or 1 - action, 2 - promotion, 3 - banner
     *
     * tinyint(1)
     */
    const OXTYPE = 'oxtype';

    /**
     * Shop name
     *
     * varchar(255)
     */
    const OXNAME = 'oxname';

    /**
     * Picture filename, used for banner (multilanguage)
     *
     * varchar-i18n(128)
     */
    const OXPIC = 'oxpic';

    /**
     * Article Price
     *
     * double = 0
     */
    const OXPRICE = 'oxprice';

    /**
     * Timestamp
     *
     * timestamp = CURRENT_TIMESTAMP
     */
    const OXTIMESTAMP = 'oxtimestamp';
}
