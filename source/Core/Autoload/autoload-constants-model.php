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
 */
namespace OxidEsales\Eshop\Core\Field
{
    use OxidEsales\Eshop\Core\Database\TABLE;

    /**
     * Action id
     */
    const ACTIONS_ID = TABLE\OXACTIONS. '__' . TABLE\OXACTIONS\OXID;

    /**
     * Shop id (oxshops)
     */
    const ACTIONS_SHOPID = TABLE\OXACTIONS. '__' . TABLE\OXACTIONS\OXSHOPID;

    /**
     * Action type: 0 or 1 - action, 2 - promotion, 3 - banner
     */
    const ACTIONS_TYPE = TABLE\OXACTIONS. '__' . TABLE\OXACTIONS\OXTYPE;

    /**
     * Title (multilanguage)
     */
    const ACTIONS_TITLE = TABLE\OXACTIONS. '__' . TABLE\OXACTIONS\OXTITLE;

    /**
     * Long description, used for promotion (multilanguage)
     */
    const ACTIONS_LONGDESC = TABLE\OXACTIONS. '__' . TABLE\OXACTIONS\OXLONGDESC;

    /**
     * Active
     */
    const ACTIONS_ACTIVE = TABLE\OXACTIONS. '__' . TABLE\OXACTIONS\OXACTIVE;

    /**
     * Active from specified date
     */
    const ACTIONS_ACTIVEFROM = TABLE\OXACTIONS. '__' . TABLE\OXACTIONS\OXACTIVEFROM;

    /**
     * Active to specified date
     */
    const ACTIONS_ACTIVETO = TABLE\OXACTIONS. '__' . TABLE\OXACTIONS\OXACTIVETO;

    /**
     * Picture filename, used for banner (multilanguage)
     */
    const ACTIONS_PIC = TABLE\OXACTIONS. '__' . TABLE\OXACTIONS\OXPIC;

    /**
     * Link, used on banner (multilanguage)
     */
    const ACTIONS_LINK = TABLE\OXACTIONS. '__' . TABLE\OXACTIONS\OXLINK;

    /**
     * Sorting
     */
    const ACTIONS_SORT = TABLE\OXACTIONS. '__' . TABLE\OXACTIONS\OXSORT;

    /**
     * Timestamp
     */
    const ACTIONS_TIMESTAMP = TABLE\OXACTIONS. '__' . TABLE\OXACTIONS\OXTIMESTAMP;

    /**
     * Action id
     */
    const ADDRESS_ID = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXID;

    /**
     * User id (oxuser)
     */
    const ADDRESS_USERID = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXUSERID;

    /**
     * User id (oxuser)
     */
    const ADDRESS_ADDRESSUSERID = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXADDRESSUSERID;

    /**
     * Company name
     */
    const ADDRESS_COMPANY = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXCOMPANY;

    /**
     * First name
     */
    const ADDRESS_FNAME = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXFNAME;

    /**
     * Last name
     */
    const ADDRESS_LNAME = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXLNAME;

    /**
     * Street
     */
    const ADDRESS_STREET = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXSTREET;

    /**
     * House number
     */
    const ADDRESS_STREETNR = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXSTREETNR;

    /**
     * Additional info
     */
    const ADDRESS_ADDINFO = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXADDINFO;

    /**
     * City
     */
    const ADDRESS_CITY = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXCITY;

    /**
     * Country name
     */
    const ADDRESS_COUNTRY = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXCOUNTRY;

    /**
     * Country id (oxcountry)
     */
    const ADDRESS_COUNTRYID = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXCOUNTRYID;

    /**
     * State id (oxstate)
     */
    const ADDRESS_STATEID = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXSTATEID;

    /**
     * Zip code
     */
    const ADDRESS_ZIP = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXZIP;

    /**
     * Phone number
     */
    const ADDRESS_FON = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXFON;

    /**
     * Fax number
     */
    const ADDRESS_FAX = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXFAX;

    /**
     * User title prefix (Mr/Mrs)
     */
    const ADDRESS_SAL = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXSAL;

    /**
     * Timestamp
     */
    const ADDRESS_TIMESTAMP = TABLE\OXADDRESS. '__' . TABLE\OXADDRESS\OXTIMESTAMP;

    /**
     * Action id
     */
    const ARTICLE_ID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXID;

    /**
     * Shop id (oxshops)
     */
    const ARTICLE_SHOPID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSHOPID;

    /**
     * Parent article id
     */
    const ARTICLE_PARENTID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPARENTID;

    /**
     * Active
     */
    const ARTICLE_ACTIVE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXACTIVE;

    /**
     * Hidden
     */
    const ARTICLE_HIDDEN = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXHIDDEN;

    /**
     * Active from specified date
     */
    const ARTICLE_ACTIVEFROM = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXACTIVEFROM;

    /**
     * Active to specified date
     */
    const ARTICLE_ACTIVETO = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXACTIVETO;

    /**
     * Article number
     */
    const ARTICLE_ARTNUM = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXARTNUM;

    /**
     * International Article Number (EAN)
     */
    const ARTICLE_EAN = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXEAN;

    /**
     * Manufacture International Article Number (Man. EAN)
     */
    const ARTICLE_DISTEAN = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXDISTEAN;

    /**
     * Manufacture Part Number (MPN)
     */
    const ARTICLE_MPN = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXMPN;

    /**
     * Title (multilanguage)
     */
    const ARTICLE_TITLE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXTITLE;

    /**
     * Short description (multilanguage)
     */
    const ARTICLE_SHORTDESC = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSHORTDESC;

    /**
     * Article Price
     */
    const ARTICLE_PRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPRICE;

    /**
     * No Promotions (Price Alert)
     */
    const ARTICLE_BLFIXEDPRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXBLFIXEDPRICE;

    /**
     * Price A
     */
    const ARTICLE_PRICEA = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPRICEA;

    /**
     * Price B
     */
    const ARTICLE_PRICEB = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPRICEB;

    /**
     * Price C
     */
    const ARTICLE_PRICEC = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPRICEC;

    /**
     * Purchase Price
     */
    const ARTICLE_BPRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXBPRICE;

    /**
     * Recommended Retail Price (RRP)
     */
    const ARTICLE_TPRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXTPRICE;

    /**
     * Unit name (kg,g,l,cm etc), used in setting price per quantity unit calculation
     */
    const ARTICLE_UNITNAME = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUNITNAME;

    /**
     * Article quantity, used in setting price per quantity unit calculation
     */
    const ARTICLE_UNITQUANTITY = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUNITQUANTITY;

    /**
     * External URL to other information about the article
     */
    const ARTICLE_EXTURL = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXEXTURL;

    /**
     * Text for external URL (multilanguage)
     */
    const ARTICLE_URLDESC = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXURLDESC;

    /**
     * External URL image
     */
    const ARTICLE_URLIMG = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXURLIMG;

    /**
     * Value added tax. If specified, used in all calculations instead of global vat
     */
    const ARTICLE_VAT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVAT;

    /**
     * Thumbnail filename
     */
    const ARTICLE_THUMB = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXTHUMB;

    /**
     * Icon filename
     */
    const ARTICLE_ICON = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXICON;

    /**
     * 1# Picture filename
     */
    const ARTICLE_PIC1 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC1;

    /**
     * 2# Picture filename
     */
    const ARTICLE_PIC2 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC2;

    /**
     * 3# Picture filename
     */
    const ARTICLE_PIC3 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC3;

    /**
     * 4# Picture filename
     */
    const ARTICLE_PIC4 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC4;

    /**
     * 5# Picture filename
     */
    const ARTICLE_PIC5 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC5;

    /**
     * 6# Picture filename
     */
    const ARTICLE_PIC6 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC6;

    /**
     * 7# Picture filename
     */
    const ARTICLE_PIC7 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC7;

    /**
     * 8# Picture filename
     */
    const ARTICLE_PIC8 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC8;

    /**
     * 9# Picture filename
     */
    const ARTICLE_PIC9 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC9;

    /**
     * 10# Picture filename
     */
    const ARTICLE_PIC10 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC10;

    /**
     * 11# Picture filename
     */
    const ARTICLE_PIC11 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC11;

    /**
     * 12# Picture filename
     */
    const ARTICLE_PIC12 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC12;

    /**
     * Weight (kg)
     */
    const ARTICLE_WEIGHT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXWEIGHT;

    /**
     * Article quantity in stock
     */
    const ARTICLE_STOCK = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSTOCK;

    /**
     * Delivery Status: 1 - Standard, 2 - If out of Stock, offline, 3 - If out of Stock, not orderable, 4 - External Storehouse
     */
    const ARTICLE_STOCKFLAG = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSTOCKFLAG;

    /**
     * Message, which is shown if the article is in stock (multilanguage)
     */
    const ARTICLE_STOCKTEXT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSTOCKTEXT;

    /**
     * Message, which is shown if the article is off stock (multilanguage)
     */
    const ARTICLE_NOSTOCKTEXT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXNOSTOCKTEXT;

    /**
     * Date, when the product will be available again if it is sold out
     */
    const ARTICLE_DELIVERY = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXDELIVERY;

    /**
     * Creation time
     */
    const ARTICLE_INSERT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXINSERT;

    /**
     * Timestamp
     */
    const ARTICLE_TIMESTAMP = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXTIMESTAMP;

    /**
     * Article dimensions: Length
     */
    const ARTICLE_LENGTH = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXLENGTH;

    /**
     * Article dimensions: Width
     */
    const ARTICLE_WIDTH = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXWIDTH;

    /**
     * Article dimensions: Height
     */
    const ARTICLE_HEIGHT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXHEIGHT;

    /**
     * File, shown in article media list
     */
    const ARTICLE_FILE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXFILE;

    /**
     * Search terms (multilanguage)
     */
    const ARTICLE_SEARCHKEYS = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSEARCHKEYS;

    /**
     * Alternative template filename (if empty, default is used)
     */
    const ARTICLE_TEMPLATE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXTEMPLATE;

    /**
     * E-mail for question
     */
    const ARTICLE_QUESTIONEMAIL = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXQUESTIONEMAIL;

    /**
     * Should article be shown in search
     */
    const ARTICLE_ISSEARCH = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXISSEARCH;

    /**
     * Can article be customized
     */
    const ARTICLE_ISCONFIGURABLE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXISCONFIGURABLE;

    /**
     * Name of variants selection lists (different lists are separated by | ) (multilanguage)
     */
    const ARTICLE_VARNAME = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVARNAME;

    /**
     * Sum of active article variants stock quantity
     */
    const ARTICLE_VARSTOCK = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVARSTOCK;

    /**
     * Total number of variants that article has (active and inactive)
     */
    const ARTICLE_VARCOUNT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVARCOUNT;

    /**
     * Variant article selections (separated by | ) (multilanguage)
     */
    const ARTICLE_VARSELECT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVARSELECT;

    /**
     * Lowest price in active article variants
     */
    const ARTICLE_VARMINPRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVARMINPRICE;

    /**
     * Highest price in active article variants
     */
    const ARTICLE_VARMAXPRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVARMAXPRICE;

    /**
     * Bundled article id
     */
    const ARTICLE_BUNDLEID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXBUNDLEID;

    /**
     * Folder
     */
    const ARTICLE_FOLDER = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXFOLDER;

    /**
     * Subclass
     */
    const ARTICLE_SUBCLASS = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSUBCLASS;

    /**
     * Sorting
     */
    const ARTICLE_SORT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSORT;

    /**
     * Amount of sold articles including variants (used only for parent articles)
     */
    const ARTICLE_SOLDAMOUNT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSOLDAMOUNT;

    /**
     * Intangible article, free shipping is used (variants inherits parent setting)
     */
    const ARTICLE_NONMATERIAL = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXNONMATERIAL;

    /**
     * Free shipping (variants inherits parent setting)
     */
    const ARTICLE_FREESHIPPING = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXFREESHIPPING;

    /**
     * Enables sending of notification email when oxstock field value falls below oxremindamount value
     */
    const ARTICLE_REMINDACTIVE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXREMINDACTIVE;

    /**
     * Defines the amount, below which notification email will be sent if oxremindactive is set to 1
     */
    const ARTICLE_REMINDAMOUNT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXREMINDAMOUNT;

    /**
     *
     */
    const ARTICLE_AMITEMID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXAMITEMID;

    /**
     *
     */
    const ARTICLE_AMTASKID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXAMTASKID;

    /**
     * Vendor id (oxvendor)
     */
    const ARTICLE_VENDORID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVENDORID;

    /**
     * Manufacturer id (oxmanufacturers)
     */
    const ARTICLE_MANUFACTURERID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXMANUFACTURERID;

    /**
     * Skips all negative Discounts (Discounts, Vouchers, Delivery ...)
     */
    const ARTICLE_SKIPDISCOUNTS = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSKIPDISCOUNTS;

    /**
     * Article rating
     */
    const ARTICLE_RATING = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXRATING;

    /**
     * Rating votes count
     */
    const ARTICLE_RATINGCNT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXRATINGCNT;

    /**
     * Minimal delivery time (unit is set in oxdeltimeunit)
     */
    const ARTICLE_MINDELTIME = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXMINDELTIME;

    /**
     * Maximum delivery time (unit is set in oxdeltimeunit)
     */
    const ARTICLE_MAXDELTIME = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXMAXDELTIME;

    /**
     * Delivery time unit: DAY, WEEK, MONTH
     */
    const ARTICLE_DELTIMEUNIT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXDELTIMEUNIT;

    /**
     * If not 0, oxprice will be updated to this value on oxupdatepricetime date
     */
    const ARTICLE_UPDATEPRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUPDATEPRICE;

    /**
     * If not 0, oxpricea will be updated to this value on oxupdatepricetime date
     */
    const ARTICLE_UPDATEPRICEA = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUPDATEPRICEA;

    /**
     * If not 0, oxpriceb will be updated to this value on oxupdatepricetime date
     */
    const ARTICLE_UPDATEPRICEB = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUPDATEPRICEB;

    /**
     * If not 0, oxpricec will be updated to this value on oxupdatepricetime date
     */
    const ARTICLE_UPDATEPRICEC = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUPDATEPRICEC;

    /**
     * Date, when oxprice[a,b,c] should be updated to oxupdateprice[a,b,c] values
     */
    const ARTICLE_UPDATEPRICETIME = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUPDATEPRICETIME;

    /**
     * Enable download of files for this product
     */
    const ARTICLE_ISDOWNLOADABLE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXISDOWNLOADABLE;

    /**
     * Show custom agreement check in checkout
     */
    const ARTICLE_SHOWCUSTOMAGREEMENT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSHOWCUSTOMAGREEMENT;

    /**
     * Action id
     */
    const ATTRIBUTE_ID = TABLE\OXATTRIBUTE. '__' . TABLE\OXATTRIBUTE\OXID;

    /**
     * Shop id (oxshops)
     */
    const ATTRIBUTE_SHOPID = TABLE\OXATTRIBUTE. '__' . TABLE\OXATTRIBUTE\OXSHOPID;

    /**
     * Title (multilanguage)
     */
    const ATTRIBUTE_TITLE = TABLE\OXATTRIBUTE. '__' . TABLE\OXATTRIBUTE\OXTITLE;

    /**
     * Sorting
     */
    const ATTRIBUTE_POS = TABLE\OXATTRIBUTE. '__' . TABLE\OXATTRIBUTE\OXPOS;

    /**
     * Timestamp
     */
    const ATTRIBUTE_TIMESTAMP = TABLE\OXATTRIBUTE. '__' . TABLE\OXATTRIBUTE\OXTIMESTAMP;

    /**
     * Display attribute`s value for articles in checkout
     */
    const ATTRIBUTE_DISPLAYINBASKET = TABLE\OXATTRIBUTE. '__' . TABLE\OXATTRIBUTE\OXDISPLAYINBASKET;

    /**
     * Action id
     */
    const CATEGORY_ID = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXID;

    /**
     * Parent article id
     */
    const CATEGORY_PARENTID = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXPARENTID;

    /**
     * Used for building category tree
     */
    const CATEGORY_LEFT = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXLEFT;

    /**
     * Used for building category tree
     */
    const CATEGORY_RIGHT = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXRIGHT;

    /**
     * Root category id
     */
    const CATEGORY_ROOTID = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXROOTID;

    /**
     * Sorting
     */
    const CATEGORY_SORT = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXSORT;

    /**
     * Active
     */
    const CATEGORY_ACTIVE = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXACTIVE;

    /**
     * Hidden
     */
    const CATEGORY_HIDDEN = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXHIDDEN;

    /**
     * Shop id (oxshops)
     */
    const CATEGORY_SHOPID = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXSHOPID;

    /**
     * Title (multilanguage)
     */
    const CATEGORY_TITLE = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXTITLE;

    /**
     * Description (multilanguage)
     */
    const CATEGORY_DESC = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXDESC;

    /**
     * Long description, used for promotion (multilanguage)
     */
    const CATEGORY_LONGDESC = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXLONGDESC;

    /**
     * Thumbnail filename
     */
    const CATEGORY_THUMB = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXTHUMB;

    /**
     * External link, that if specified is opened instead of category content
     */
    const CATEGORY_EXTLINK = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXEXTLINK;

    /**
     * Alternative template filename (if empty, default is used)
     */
    const CATEGORY_TEMPLATE = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXTEMPLATE;

    /**
     * Default field for sorting of articles in this category (most of oxarticles fields)
     */
    const CATEGORY_DEFSORT = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXDEFSORT;

    /**
     * Default mode of sorting of articles in this category (0 - asc, 1 - desc)
     */
    const CATEGORY_DEFSORTMODE = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXDEFSORTMODE;

    /**
     * If specified, all articles, with price higher than specified, will be shown in this category
     */
    const CATEGORY_PRICEFROM = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXPRICEFROM;

    /**
     * If specified, all articles, with price lower than specified, will be shown in this category
     */
    const CATEGORY_PRICETO = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXPRICETO;

    /**
     * Icon filename
     */
    const CATEGORY_ICON = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXICON;

    /**
     * Promotion icon filename
     */
    const CATEGORY_PROMOICON = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXPROMOICON;

    /**
     * Value added tax. If specified, used in all calculations instead of global vat
     */
    const CATEGORY_VAT = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXVAT;

    /**
     * Skips all negative Discounts (Discounts, Vouchers, Delivery ...)
     */
    const CATEGORY_SKIPDISCOUNTS = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXSKIPDISCOUNTS;

    /**
     * Show SEO Suffix in Category
     */
    const CATEGORY_SHOWSUFFIX = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXSHOWSUFFIX;

    /**
     * Timestamp
     */
    const CATEGORY_TIMESTAMP = TABLE\OXCATEGORIES. '__' . TABLE\OXCATEGORIES\OXTIMESTAMP;

    /**
     * Action id
     */
    const CONTENT_ID = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXID;

    /**
     * Id, specified by admin and can be used instead of oxid
     */
    const CONTENT_LOADID = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXLOADID;

    /**
     * Shop id (oxshops)
     */
    const CONTENT_SHOPID = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXSHOPID;

    /**
     * Snippet (can be included to other oxcontents records)
     */
    const CONTENT_SNIPPET = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXSNIPPET;

    /**
     * Action type: 0 or 1 - action, 2 - promotion, 3 - banner
     */
    const CONTENT_TYPE = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXTYPE;

    /**
     * Active
     */
    const CONTENT_ACTIVE = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXACTIVE;

    /**
     * Position
     */
    const CONTENT_POSITION = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXPOSITION;

    /**
     * Title (multilanguage)
     */
    const CONTENT_TITLE = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXTITLE;

    /**
     * Content (multilanguage)
     */
    const CONTENT_CONTENT = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXCONTENT;

    /**
     * Category id (oxcategories), used only when type = 2
     */
    const CONTENT_CATID = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXCATID;

    /**
     * Folder
     */
    const CONTENT_FOLDER = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXFOLDER;

    /**
     * Term and Conditions version (used only when OXLOADID = oxagb)
     */
    const CONTENT_TERMVERSION = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXTERMVERSION;

    /**
     * Timestamp
     */
    const CONTENT_TIMESTAMP = TABLE\OXCONTENTS. '__' . TABLE\OXCONTENTS\OXTIMESTAMP;

    /**
     * Action id
     */
    const COUNTRY_ID = TABLE\OXCOUNTRY. '__' . TABLE\OXCOUNTRY\OXID;

    /**
     * Active
     */
    const COUNTRY_ACTIVE = TABLE\OXCOUNTRY. '__' . TABLE\OXCOUNTRY\OXACTIVE;

    /**
     * Title (multilanguage)
     */
    const COUNTRY_TITLE = TABLE\OXCOUNTRY. '__' . TABLE\OXCOUNTRY\OXTITLE;

    /**
     * ISO 3166-1 alpha-2
     */
    const COUNTRY_ISOALPHA2 = TABLE\OXCOUNTRY. '__' . TABLE\OXCOUNTRY\OXISOALPHA2;

    /**
     * ISO 3166-1 alpha-3
     */
    const COUNTRY_ISOALPHA3 = TABLE\OXCOUNTRY. '__' . TABLE\OXCOUNTRY\OXISOALPHA3;

    /**
     * ISO 3166-1 numeric
     */
    const COUNTRY_UNNUM3 = TABLE\OXCOUNTRY. '__' . TABLE\OXCOUNTRY\OXUNNUM3;

    /**
     * VAT identification number prefix
     */
    const COUNTRY_VATINPREFIX = TABLE\OXCOUNTRY. '__' . TABLE\OXCOUNTRY\OXVATINPREFIX;

    /**
     * Sorting
     */
    const COUNTRY_ORDER = TABLE\OXCOUNTRY. '__' . TABLE\OXCOUNTRY\OXORDER;

    /**
     * Short description (multilanguage)
     */
    const COUNTRY_SHORTDESC = TABLE\OXCOUNTRY. '__' . TABLE\OXCOUNTRY\OXSHORTDESC;

    /**
     * Long description, used for promotion (multilanguage)
     */
    const COUNTRY_LONGDESC = TABLE\OXCOUNTRY. '__' . TABLE\OXCOUNTRY\OXLONGDESC;

    /**
     * Vat status: 0 - Do not bill VAT, 1 - Do not bill VAT only if provided valid VAT ID
     */
    const COUNTRY_VATSTATUS = TABLE\OXCOUNTRY. '__' . TABLE\OXCOUNTRY\OXVATSTATUS;

    /**
     * Timestamp
     */
    const COUNTRY_TIMESTAMP = TABLE\OXCOUNTRY. '__' . TABLE\OXCOUNTRY\OXTIMESTAMP;

    /**
     * Action id
     */
    const DELIVERY_ID = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXID;

    /**
     * Shop id (oxshops)
     */
    const DELIVERY_SHOPID = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXSHOPID;

    /**
     * Active
     */
    const DELIVERY_ACTIVE = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXACTIVE;

    /**
     * Active from specified date
     */
    const DELIVERY_ACTIVEFROM = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXACTIVEFROM;

    /**
     * Active to specified date
     */
    const DELIVERY_ACTIVETO = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXACTIVETO;

    /**
     * Title (multilanguage)
     */
    const DELIVERY_TITLE = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXTITLE;

    /**
     * Price Surcharge/Reduction type (abs|%)
     */
    const DELIVERY_ADDSUMTYPE = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXADDSUMTYPE;

    /**
     * Price Surcharge/Reduction amount
     */
    const DELIVERY_ADDSUM = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXADDSUM;

    /**
     * Condition type: a - Amount, s - Size, w - Weight, p - Price
     */
    const DELIVERY_DELTYPE = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXDELTYPE;

    /**
     * Condition param from (e.g. amount from 1)
     */
    const DELIVERY_PARAM = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXPARAM;

    /**
     * Condition param to (e.g. amount to 10)
     */
    const DELIVERY_PARAMEND = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXPARAMEND;

    /**
     * Calculation Rules: 0 - Once per Cart, 1 - Once for each different product, 2 - For each product
     */
    const DELIVERY_FIXED = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXFIXED;

    /**
     * Sorting
     */
    const DELIVERY_SORT = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXSORT;

    /**
     * Do not run further rules if this rule is valid and is being run
     */
    const DELIVERY_FINALIZE = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXFINALIZE;

    /**
     * Timestamp
     */
    const DELIVERY_TIMESTAMP = TABLE\OXDELIVERY. '__' . TABLE\OXDELIVERY\OXTIMESTAMP;

    /**
     * Action id
     */
    const DELIVERYSET_ID = TABLE\OXDELIVERYSET. '__' . TABLE\OXDELIVERYSET\OXID;

    /**
     * Shop id (oxshops)
     */
    const DELIVERYSET_SHOPID = TABLE\OXDELIVERYSET. '__' . TABLE\OXDELIVERYSET\OXSHOPID;

    /**
     * Active
     */
    const DELIVERYSET_ACTIVE = TABLE\OXDELIVERYSET. '__' . TABLE\OXDELIVERYSET\OXACTIVE;

    /**
     * Active from specified date
     */
    const DELIVERYSET_ACTIVEFROM = TABLE\OXDELIVERYSET. '__' . TABLE\OXDELIVERYSET\OXACTIVEFROM;

    /**
     * Active to specified date
     */
    const DELIVERYSET_ACTIVETO = TABLE\OXDELIVERYSET. '__' . TABLE\OXDELIVERYSET\OXACTIVETO;

    /**
     * Title (multilanguage)
     */
    const DELIVERYSET_TITLE = TABLE\OXDELIVERYSET. '__' . TABLE\OXDELIVERYSET\OXTITLE;

    /**
     * Sorting
     */
    const DELIVERYSET_POS = TABLE\OXDELIVERYSET. '__' . TABLE\OXDELIVERYSET\OXPOS;

    /**
     * Timestamp
     */
    const DELIVERYSET_TIMESTAMP = TABLE\OXDELIVERYSET. '__' . TABLE\OXDELIVERYSET\OXTIMESTAMP;

    /**
     * Action id
     */
    const DISCOUNT_ID = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXID;

    /**
     * Shop id (oxshops)
     */
    const DISCOUNT_SHOPID = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXSHOPID;

    /**
     * Active
     */
    const DISCOUNT_ACTIVE = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXACTIVE;

    /**
     * Active from specified date
     */
    const DISCOUNT_ACTIVEFROM = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXACTIVEFROM;

    /**
     * Active to specified date
     */
    const DISCOUNT_ACTIVETO = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXACTIVETO;

    /**
     * Title (multilanguage)
     */
    const DISCOUNT_TITLE = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXTITLE;

    /**
     * Valid from specified amount of articles
     */
    const DISCOUNT_AMOUNT = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXAMOUNT;

    /**
     * Valid to specified amount of articles
     */
    const DISCOUNT_AMOUNTTO = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXAMOUNTTO;

    /**
     * If specified, all articles, with price lower than specified, will be shown in this category
     */
    const DISCOUNT_PRICETO = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXPRICETO;

    /**
     * Article Price
     */
    const DISCOUNT_PRICE = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXPRICE;

    /**
     * Price Surcharge/Reduction type (abs|%)
     */
    const DISCOUNT_ADDSUMTYPE = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXADDSUMTYPE;

    /**
     * Price Surcharge/Reduction amount
     */
    const DISCOUNT_ADDSUM = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXADDSUM;

    /**
     * Free article id, that will be added as a discount
     */
    const DISCOUNT_ITMARTID = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXITMARTID;

    /**
     * The quantity of free article that will be added to basket with discounted article
     */
    const DISCOUNT_ITMAMOUNT = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXITMAMOUNT;

    /**
     * Should free article amount be multiplied by discounted item quantity in basket
     */
    const DISCOUNT_ITMMULTIPLE = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXITMMULTIPLE;

    /**
     * Sorting
     */
    const DISCOUNT_SORT = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXSORT;

    /**
     * Timestamp
     */
    const DISCOUNT_TIMESTAMP = TABLE\OXDISCOUNT. '__' . TABLE\OXDISCOUNT\OXTIMESTAMP;

    /**
     * Action id
     */
    const FILE_ID = TABLE\OXFILES. '__' . TABLE\OXFILES\OXID;

    /**
     * Article id (oxarticles)
     */
    const FILE_ARTID = TABLE\OXFILES. '__' . TABLE\OXFILES\OXARTID;

    /**
     * Filename
     */
    const FILE_FILENAME = TABLE\OXFILES. '__' . TABLE\OXFILES\OXFILENAME;

    /**
     * Hashed filename, used for file directory path creation
     */
    const FILE_STOREHASH = TABLE\OXFILES. '__' . TABLE\OXFILES\OXSTOREHASH;

    /**
     * Download is available only after purchase
     */
    const FILE_PURCHASEDONLY = TABLE\OXFILES. '__' . TABLE\OXFILES\OXPURCHASEDONLY;

    /**
     * Maximum count of downloads after order
     */
    const FILE_MAXDOWNLOADS = TABLE\OXFILES. '__' . TABLE\OXFILES\OXMAXDOWNLOADS;

    /**
     * Maximum count of downloads for not registered users after order
     */
    const FILE_MAXUNREGDOWNLOADS = TABLE\OXFILES. '__' . TABLE\OXFILES\OXMAXUNREGDOWNLOADS;

    /**
     * Expiration time of download link in hours
     */
    const FILE_LINKEXPTIME = TABLE\OXFILES. '__' . TABLE\OXFILES\OXLINKEXPTIME;

    /**
     * Expiration time of download link after the first download in hours
     */
    const FILE_DOWNLOADEXPTIME = TABLE\OXFILES. '__' . TABLE\OXFILES\OXDOWNLOADEXPTIME;

    /**
     * Timestamp
     */
    const FILE_TIMESTAMP = TABLE\OXFILES. '__' . TABLE\OXFILES\OXTIMESTAMP;

    /**
     * Action id
     */
    const GROUPS_ID = TABLE\OXGROUPS. '__' . TABLE\OXGROUPS\OXID;

    /**
     * Active
     */
    const GROUPS_ACTIVE = TABLE\OXGROUPS. '__' . TABLE\OXGROUPS\OXACTIVE;

    /**
     * Title (multilanguage)
     */
    const GROUPS_TITLE = TABLE\OXGROUPS. '__' . TABLE\OXGROUPS\OXTITLE;

    /**
     * Timestamp
     */
    const GROUPS_TIMESTAMP = TABLE\OXGROUPS. '__' . TABLE\OXGROUPS\OXTIMESTAMP;

    /**
     * Action id
     */
    const LINKS_ID = TABLE\OXLINKS. '__' . TABLE\OXLINKS\OXID;

    /**
     * Shop id (oxshops)
     */
    const LINKS_SHOPID = TABLE\OXLINKS. '__' . TABLE\OXLINKS\OXSHOPID;

    /**
     * Active
     */
    const LINKS_ACTIVE = TABLE\OXLINKS. '__' . TABLE\OXLINKS\OXACTIVE;

    /**
     * Link url
     */
    const LINKS_URL = TABLE\OXLINKS. '__' . TABLE\OXLINKS\OXURL;

    /**
     * Text for external URL (multilanguage)
     */
    const LINKS_URLDESC = TABLE\OXLINKS. '__' . TABLE\OXLINKS\OXURLDESC;

    /**
     * Creation time
     */
    const LINKS_INSERT = TABLE\OXLINKS. '__' . TABLE\OXLINKS\OXINSERT;

    /**
     * Timestamp
     */
    const LINKS_TIMESTAMP = TABLE\OXLINKS. '__' . TABLE\OXLINKS\OXTIMESTAMP;

    /**
     * Action id
     */
    const MANUFACTURER_ID = TABLE\OXMANUFACTURERS. '__' . TABLE\OXMANUFACTURERS\OXID;

    /**
     * Shop id (oxshops)
     */
    const MANUFACTURER_SHOPID = TABLE\OXMANUFACTURERS. '__' . TABLE\OXMANUFACTURERS\OXSHOPID;

    /**
     * Active
     */
    const MANUFACTURER_ACTIVE = TABLE\OXMANUFACTURERS. '__' . TABLE\OXMANUFACTURERS\OXACTIVE;

    /**
     * Icon filename
     */
    const MANUFACTURER_ICON = TABLE\OXMANUFACTURERS. '__' . TABLE\OXMANUFACTURERS\OXICON;

    /**
     * Title (multilanguage)
     */
    const MANUFACTURER_TITLE = TABLE\OXMANUFACTURERS. '__' . TABLE\OXMANUFACTURERS\OXTITLE;

    /**
     * Short description (multilanguage)
     */
    const MANUFACTURER_SHORTDESC = TABLE\OXMANUFACTURERS. '__' . TABLE\OXMANUFACTURERS\OXSHORTDESC;

    /**
     * Show SEO Suffix in Category
     */
    const MANUFACTURER_SHOWSUFFIX = TABLE\OXMANUFACTURERS. '__' . TABLE\OXMANUFACTURERS\OXSHOWSUFFIX;

    /**
     * Timestamp
     */
    const MANUFACTURER_TIMESTAMP = TABLE\OXMANUFACTURERS. '__' . TABLE\OXMANUFACTURERS\OXTIMESTAMP;

    /**
     * Action id
     */
    const MEDIAURL_ID = TABLE\OXMEDIAURLS. '__' . TABLE\OXMEDIAURLS\OXID;

    /**
     * Article id (oxarticles)
     */
    const MEDIAURL_OBJECTID = TABLE\OXMEDIAURLS. '__' . TABLE\OXMEDIAURLS\OXOBJECTID;

    /**
     * Link url
     */
    const MEDIAURL_URL = TABLE\OXMEDIAURLS. '__' . TABLE\OXMEDIAURLS\OXURL;

    /**
     * Description (multilanguage)
     */
    const MEDIAURL_DESC = TABLE\OXMEDIAURLS. '__' . TABLE\OXMEDIAURLS\OXDESC;

    /**
     * Is oxurl field used for filename or url
     */
    const MEDIAURL_ISUPLOADED = TABLE\OXMEDIAURLS. '__' . TABLE\OXMEDIAURLS\OXISUPLOADED;

    /**
     * Timestamp
     */
    const MEDIAURL_TIMESTAMP = TABLE\OXMEDIAURLS. '__' . TABLE\OXMEDIAURLS\OXTIMESTAMP;

    /**
     * Action id
     */
    const NEWS_ID = TABLE\OXNEWS. '__' . TABLE\OXNEWS\OXID;

    /**
     * Shop id (oxshops)
     */
    const NEWS_SHOPID = TABLE\OXNEWS. '__' . TABLE\OXNEWS\OXSHOPID;

    /**
     * Active
     */
    const NEWS_ACTIVE = TABLE\OXNEWS. '__' . TABLE\OXNEWS\OXACTIVE;

    /**
     * Active from specified date
     */
    const NEWS_ACTIVEFROM = TABLE\OXNEWS. '__' . TABLE\OXNEWS\OXACTIVEFROM;

    /**
     * Active to specified date
     */
    const NEWS_ACTIVETO = TABLE\OXNEWS. '__' . TABLE\OXNEWS\OXACTIVETO;

    /**
     * Creation date (entered by user)
     */
    const NEWS_DATE = TABLE\OXNEWS. '__' . TABLE\OXNEWS\OXDATE;

    /**
     * Short description (multilanguage)
     */
    const NEWS_SHORTDESC = TABLE\OXNEWS. '__' . TABLE\OXNEWS\OXSHORTDESC;

    /**
     * Long description, used for promotion (multilanguage)
     */
    const NEWS_LONGDESC = TABLE\OXNEWS. '__' . TABLE\OXNEWS\OXLONGDESC;

    /**
     * Timestamp
     */
    const NEWS_TIMESTAMP = TABLE\OXNEWS. '__' . TABLE\OXNEWS\OXTIMESTAMP;

    /**
     * Action id
     */
    const NEWSSUBSCRIBED_ID = TABLE\OXNEWSSUBSCRIBED. '__' . TABLE\OXNEWSSUBSCRIBED\OXID;

    /**
     * User id (oxuser)
     */
    const NEWSSUBSCRIBED_USERID = TABLE\OXNEWSSUBSCRIBED. '__' . TABLE\OXNEWSSUBSCRIBED\OXUSERID;

    /**
     * User title prefix (Mr/Mrs)
     */
    const NEWSSUBSCRIBED_SAL = TABLE\OXNEWSSUBSCRIBED. '__' . TABLE\OXNEWSSUBSCRIBED\OXSAL;

    /**
     * First name
     */
    const NEWSSUBSCRIBED_FNAME = TABLE\OXNEWSSUBSCRIBED. '__' . TABLE\OXNEWSSUBSCRIBED\OXFNAME;

    /**
     * Last name
     */
    const NEWSSUBSCRIBED_LNAME = TABLE\OXNEWSSUBSCRIBED. '__' . TABLE\OXNEWSSUBSCRIBED\OXLNAME;

    /**
     * Email
     */
    const NEWSSUBSCRIBED_EMAIL = TABLE\OXNEWSSUBSCRIBED. '__' . TABLE\OXNEWSSUBSCRIBED\OXEMAIL;

    /**
     * Subscription status: 0 - not subscribed, 1 - subscribed, 2 - not confirmed
     */
    const NEWSSUBSCRIBED_DBOPTIN = TABLE\OXNEWSSUBSCRIBED. '__' . TABLE\OXNEWSSUBSCRIBED\OXDBOPTIN;

    /**
     * Subscription email sending status
     */
    const NEWSSUBSCRIBED_EMAILFAILED = TABLE\OXNEWSSUBSCRIBED. '__' . TABLE\OXNEWSSUBSCRIBED\OXEMAILFAILED;

    /**
     * Subscription date
     */
    const NEWSSUBSCRIBED_SUBSCRIBED = TABLE\OXNEWSSUBSCRIBED. '__' . TABLE\OXNEWSSUBSCRIBED\OXSUBSCRIBED;

    /**
     * Unsubscription date
     */
    const NEWSSUBSCRIBED_UNSUBSCRIBED = TABLE\OXNEWSSUBSCRIBED. '__' . TABLE\OXNEWSSUBSCRIBED\OXUNSUBSCRIBED;

    /**
     * Timestamp
     */
    const NEWSSUBSCRIBED_TIMESTAMP = TABLE\OXNEWSSUBSCRIBED. '__' . TABLE\OXNEWSSUBSCRIBED\OXTIMESTAMP;

    /**
     * Shop id (oxshops)
     */
    const NEWSSUBSCRIBED_SHOPID = TABLE\OXNEWSSUBSCRIBED. '__' . TABLE\OXNEWSSUBSCRIBED\OXSHOPID;

    /**
     * Action id
     */
    const NEWSLETTER_ID = TABLE\OXNEWSLETTER. '__' . TABLE\OXNEWSLETTER\OXID;

    /**
     * Shop id (oxshops)
     */
    const NEWSLETTER_SHOPID = TABLE\OXNEWSLETTER. '__' . TABLE\OXNEWSLETTER\OXSHOPID;

    /**
     * Title (multilanguage)
     */
    const NEWSLETTER_TITLE = TABLE\OXNEWSLETTER. '__' . TABLE\OXNEWSLETTER\OXTITLE;

    /**
     * Alternative template filename (if empty, default is used)
     */
    const NEWSLETTER_TEMPLATE = TABLE\OXNEWSLETTER. '__' . TABLE\OXNEWSLETTER\OXTEMPLATE;

    /**
     * Plain template
     */
    const NEWSLETTER_PLAINTEMPLATE = TABLE\OXNEWSLETTER. '__' . TABLE\OXNEWSLETTER\OXPLAINTEMPLATE;

    /**
     * Subject
     */
    const NEWSLETTER_SUBJECT = TABLE\OXNEWSLETTER. '__' . TABLE\OXNEWSLETTER\OXSUBJECT;

    /**
     * Timestamp
     */
    const NEWSLETTER_TIMESTAMP = TABLE\OXNEWSLETTER. '__' . TABLE\OXNEWSLETTER\OXTIMESTAMP;

    /**
     * Action id
     */
    const OBJECT2CATEGORY_ID = TABLE\OXOBJECT2CATEGORY. '__' . TABLE\OXOBJECT2CATEGORY\OXID;

    /**
     * Article id (oxarticles)
     */
    const OBJECT2CATEGORY_OBJECTID = TABLE\OXOBJECT2CATEGORY. '__' . TABLE\OXOBJECT2CATEGORY\OXOBJECTID;

    /**
     * Category id (oxcategory)
     */
    const OBJECT2CATEGORY_CATNID = TABLE\OXOBJECT2CATEGORY. '__' . TABLE\OXOBJECT2CATEGORY\OXCATNID;

    /**
     * Sorting
     */
    const OBJECT2CATEGORY_POS = TABLE\OXOBJECT2CATEGORY. '__' . TABLE\OXOBJECT2CATEGORY\OXPOS;

    /**
     * Creation time
     */
    const OBJECT2CATEGORY_TIME = TABLE\OXOBJECT2CATEGORY. '__' . TABLE\OXOBJECT2CATEGORY\OXTIME;

    /**
     * Timestamp
     */
    const OBJECT2CATEGORY_TIMESTAMP = TABLE\OXOBJECT2CATEGORY. '__' . TABLE\OXOBJECT2CATEGORY\OXTIMESTAMP;

    /**
     * Action id
     */
    const OBJECT2GROUP_ID = TABLE\OXOBJECT2GROUP. '__' . TABLE\OXOBJECT2GROUP\OXID;

    /**
     * Shop id (oxshops)
     */
    const OBJECT2GROUP_SHOPID = TABLE\OXOBJECT2GROUP. '__' . TABLE\OXOBJECT2GROUP\OXSHOPID;

    /**
     * Article id (oxarticles)
     */
    const OBJECT2GROUP_OBJECTID = TABLE\OXOBJECT2GROUP. '__' . TABLE\OXOBJECT2GROUP\OXOBJECTID;

    /**
     * Group id
     */
    const OBJECT2GROUP_GROUPSID = TABLE\OXOBJECT2GROUP. '__' . TABLE\OXOBJECT2GROUP\OXGROUPSID;

    /**
     * Timestamp
     */
    const OBJECT2GROUP_TIMESTAMP = TABLE\OXOBJECT2GROUP. '__' . TABLE\OXOBJECT2GROUP\OXTIMESTAMP;

    /**
     * Action id
     */
    const ORDER_ID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXID;

    /**
     * Shop id (oxshops)
     */
    const ORDER_SHOPID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXSHOPID;

    /**
     * User id (oxuser)
     */
    const ORDER_USERID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXUSERID;

    /**
     * Order date
     */
    const ORDER_ORDERDATE = TABLE\OXORDER. '__' . TABLE\OXORDER\OXORDERDATE;

    /**
     * Order number
     */
    const ORDER_ORDERNR = TABLE\OXORDER. '__' . TABLE\OXORDER\OXORDERNR;

    /**
     * Billing info: Company name
     */
    const ORDER_BILLCOMPANY = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLCOMPANY;

    /**
     * Billing info: Email
     */
    const ORDER_BILLEMAIL = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLEMAIL;

    /**
     * Billing info: First name
     */
    const ORDER_BILLFNAME = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLFNAME;

    /**
     * Billing info: Last name
     */
    const ORDER_BILLLNAME = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLLNAME;

    /**
     * Billing info: Street name
     */
    const ORDER_BILLSTREET = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLSTREET;

    /**
     * Billing info: House number
     */
    const ORDER_BILLSTREETNR = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLSTREETNR;

    /**
     * Billing info: Additional info
     */
    const ORDER_BILLADDINFO = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLADDINFO;

    /**
     * Billing info: VAT ID No.
     */
    const ORDER_BILLUSTID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLUSTID;

    /**
     * Billing info: City
     */
    const ORDER_BILLCITY = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLCITY;

    /**
     * Billing info: Country id (oxcountry)
     */
    const ORDER_BILLCOUNTRYID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLCOUNTRYID;

    /**
     * Billing info: US State id (oxstates)
     */
    const ORDER_BILLSTATEID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLSTATEID;

    /**
     * Billing info: Zip code
     */
    const ORDER_BILLZIP = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLZIP;

    /**
     * Billing info: Phone number
     */
    const ORDER_BILLFON = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLFON;

    /**
     * Billing info: Fax number
     */
    const ORDER_BILLFAX = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLFAX;

    /**
     * Billing info: User title prefix (Mr/Mrs)
     */
    const ORDER_BILLSAL = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLSAL;

    /**
     * Shipping info: Company name
     */
    const ORDER_DELCOMPANY = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELCOMPANY;

    /**
     * Shipping info: First name
     */
    const ORDER_DELFNAME = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELFNAME;

    /**
     * Shipping info: Last name
     */
    const ORDER_DELLNAME = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELLNAME;

    /**
     * Shipping info: Street name
     */
    const ORDER_DELSTREET = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELSTREET;

    /**
     * Shipping info: House number
     */
    const ORDER_DELSTREETNR = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELSTREETNR;

    /**
     * Shipping info: Additional info
     */
    const ORDER_DELADDINFO = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELADDINFO;

    /**
     * Shipping info: City
     */
    const ORDER_DELCITY = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELCITY;

    /**
     * Shipping info: Country id (oxcountry)
     */
    const ORDER_DELCOUNTRYID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELCOUNTRYID;

    /**
     * Shipping info: US State id (oxstates)
     */
    const ORDER_DELSTATEID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELSTATEID;

    /**
     * Shipping info: Zip code
     */
    const ORDER_DELZIP = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELZIP;

    /**
     * Shipping info: Phone number
     */
    const ORDER_DELFON = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELFON;

    /**
     * Shipping info: Fax number
     */
    const ORDER_DELFAX = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELFAX;

    /**
     * Shipping info: User title prefix (Mr/Mrs)
     */
    const ORDER_DELSAL = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELSAL;

    /**
     * User payment id (oxuserpayments)
     */
    const ORDER_PAYMENTID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXPAYMENTID;

    /**
     * Payment id (oxpayments)
     */
    const ORDER_PAYMENTTYPE = TABLE\OXORDER. '__' . TABLE\OXORDER\OXPAYMENTTYPE;

    /**
     * Total net sum
     */
    const ORDER_TOTALNETSUM = TABLE\OXORDER. '__' . TABLE\OXORDER\OXTOTALNETSUM;

    /**
     * Total brut sum
     */
    const ORDER_TOTALBRUTSUM = TABLE\OXORDER. '__' . TABLE\OXORDER\OXTOTALBRUTSUM;

    /**
     * Total order sum
     */
    const ORDER_TOTALORDERSUM = TABLE\OXORDER. '__' . TABLE\OXORDER\OXTOTALORDERSUM;

    /**
     * First VAT
     */
    const ORDER_ARTVAT1 = TABLE\OXORDER. '__' . TABLE\OXORDER\OXARTVAT1;

    /**
     * First calculated VAT price
     */
    const ORDER_ARTVATPRICE1 = TABLE\OXORDER. '__' . TABLE\OXORDER\OXARTVATPRICE1;

    /**
     * Second VAT
     */
    const ORDER_ARTVAT2 = TABLE\OXORDER. '__' . TABLE\OXORDER\OXARTVAT2;

    /**
     * Second calculated VAT price
     */
    const ORDER_ARTVATPRICE2 = TABLE\OXORDER. '__' . TABLE\OXORDER\OXARTVATPRICE2;

    /**
     * Delivery price
     */
    const ORDER_DELCOST = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELCOST;

    /**
     * Delivery VAT
     */
    const ORDER_DELVAT = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELVAT;

    /**
     * Payment cost
     */
    const ORDER_PAYCOST = TABLE\OXORDER. '__' . TABLE\OXORDER\OXPAYCOST;

    /**
     * Payment VAT
     */
    const ORDER_PAYVAT = TABLE\OXORDER. '__' . TABLE\OXORDER\OXPAYVAT;

    /**
     * Wrapping cost
     */
    const ORDER_WRAPCOST = TABLE\OXORDER. '__' . TABLE\OXORDER\OXWRAPCOST;

    /**
     * Wrapping VAT
     */
    const ORDER_WRAPVAT = TABLE\OXORDER. '__' . TABLE\OXORDER\OXWRAPVAT;

    /**
     * Giftcard cost
     */
    const ORDER_GIFTCARDCOST = TABLE\OXORDER. '__' . TABLE\OXORDER\OXGIFTCARDCOST;

    /**
     * Giftcard VAT
     */
    const ORDER_GIFTCARDVAT = TABLE\OXORDER. '__' . TABLE\OXORDER\OXGIFTCARDVAT;

    /**
     * Gift card id (oxwrapping)
     */
    const ORDER_CARDID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXCARDID;

    /**
     * Gift card text
     */
    const ORDER_CARDTEXT = TABLE\OXORDER. '__' . TABLE\OXORDER\OXCARDTEXT;

    /**
     * Additional discount for order (abs)
     */
    const ORDER_DISCOUNT = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDISCOUNT;

    /**
     * Is exported
     */
    const ORDER_EXPORT = TABLE\OXORDER. '__' . TABLE\OXORDER\OXEXPORT;

    /**
     * Invoice No.
     */
    const ORDER_BILLNR = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLNR;

    /**
     * Invoice sent date
     */
    const ORDER_BILLDATE = TABLE\OXORDER. '__' . TABLE\OXORDER\OXBILLDATE;

    /**
     * Tracking code
     */
    const ORDER_TRACKCODE = TABLE\OXORDER. '__' . TABLE\OXORDER\OXTRACKCODE;

    /**
     * Order shipping date
     */
    const ORDER_SENDDATE = TABLE\OXORDER. '__' . TABLE\OXORDER\OXSENDDATE;

    /**
     * User remarks
     */
    const ORDER_REMARK = TABLE\OXORDER. '__' . TABLE\OXORDER\OXREMARK;

    /**
     * Coupon (voucher) discount price
     */
    const ORDER_VOUCHERDISCOUNT = TABLE\OXORDER. '__' . TABLE\OXORDER\OXVOUCHERDISCOUNT;

    /**
     * Currency
     */
    const ORDER_CURRENCY = TABLE\OXORDER. '__' . TABLE\OXORDER\OXCURRENCY;

    /**
     * Currency rate
     */
    const ORDER_CURRATE = TABLE\OXORDER. '__' . TABLE\OXORDER\OXCURRATE;

    /**
     * Folder
     */
    const ORDER_FOLDER = TABLE\OXORDER. '__' . TABLE\OXORDER\OXFOLDER;

    /**
     * Paypal: Transaction id
     */
    const ORDER_TRANSID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXTRANSID;

    /**
     *
     */
    const ORDER_PAYID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXPAYID;

    /**
     *
     */
    const ORDER_XID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXXID;

    /**
     * Time, when order was paid
     */
    const ORDER_PAID = TABLE\OXORDER. '__' . TABLE\OXORDER\OXPAID;

    /**
     * Order cancelled
     */
    const ORDER_STORNO = TABLE\OXORDER. '__' . TABLE\OXORDER\OXSTORNO;

    /**
     * User ip address
     */
    const ORDER_IP = TABLE\OXORDER. '__' . TABLE\OXORDER\OXIP;

    /**
     * Order status: NOT_FINISHED, OK, ERROR
     */
    const ORDER_TRANSSTATUS = TABLE\OXORDER. '__' . TABLE\OXORDER\OXTRANSSTATUS;

    /**
     * Language id
     */
    const ORDER_LANG = TABLE\OXORDER. '__' . TABLE\OXORDER\OXLANG;

    /**
     * Invoice number
     */
    const ORDER_INVOICENR = TABLE\OXORDER. '__' . TABLE\OXORDER\OXINVOICENR;

    /**
     * Condition type: a - Amount, s - Size, w - Weight, p - Price
     */
    const ORDER_DELTYPE = TABLE\OXORDER. '__' . TABLE\OXORDER\OXDELTYPE;

    /**
     * Timestamp
     */
    const ORDER_TIMESTAMP = TABLE\OXORDER. '__' . TABLE\OXORDER\OXTIMESTAMP;

    /**
     * Order created in netto mode
     */
    const ORDER_ISNETTOMODE = TABLE\OXORDER. '__' . TABLE\OXORDER\OXISNETTOMODE;

    /**
     * Action id
     */
    const ORDERARTICLE_ID = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXID;

    /**
     * Order id (oxorder)
     */
    const ORDERARTICLE_ORDERID = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXORDERID;

    /**
     * Valid from specified amount of articles
     */
    const ORDERARTICLE_AMOUNT = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXAMOUNT;

    /**
     * Article id (oxarticles)
     */
    const ORDERARTICLE_ARTID = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXARTID;

    /**
     * Article number
     */
    const ORDERARTICLE_ARTNUM = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXARTNUM;

    /**
     * Title (multilanguage)
     */
    const ORDERARTICLE_TITLE = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXTITLE;

    /**
     * Short description (multilanguage)
     */
    const ORDERARTICLE_SHORTDESC = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXSHORTDESC;

    /**
     * Selected variant
     */
    const ORDERARTICLE_SELVARIANT = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXSELVARIANT;

    /**
     * Full netto price (oxnprice * oxamount)
     */
    const ORDERARTICLE_NETPRICE = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXNETPRICE;

    /**
     * Full brutto price (oxbprice * oxamount)
     */
    const ORDERARTICLE_BRUTPRICE = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXBRUTPRICE;

    /**
     * Calculated VAT price
     */
    const ORDERARTICLE_VATPRICE = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXVATPRICE;

    /**
     * Value added tax. If specified, used in all calculations instead of global vat
     */
    const ORDERARTICLE_VAT = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXVAT;

    /**
     * Serialized persistent parameters
     */
    const ORDERARTICLE_PERSPARAM = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXPERSPARAM;

    /**
     * Article Price
     */
    const ORDERARTICLE_PRICE = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXPRICE;

    /**
     * Purchase Price
     */
    const ORDERARTICLE_BPRICE = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXBPRICE;

    /**
     * Netto price for one item
     */
    const ORDERARTICLE_NPRICE = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXNPRICE;

    /**
     * Wrapping id (oxwrapping)
     */
    const ORDERARTICLE_WRAPID = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXWRAPID;

    /**
     * External URL to other information about the article
     */
    const ORDERARTICLE_EXTURL = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXEXTURL;

    /**
     * Text for external URL (multilanguage)
     */
    const ORDERARTICLE_URLDESC = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXURLDESC;

    /**
     * External URL image
     */
    const ORDERARTICLE_URLIMG = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXURLIMG;

    /**
     * Thumbnail filename
     */
    const ORDERARTICLE_THUMB = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXTHUMB;

    /**
     * 1# Picture filename
     */
    const ORDERARTICLE_PIC1 = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXPIC1;

    /**
     * 2# Picture filename
     */
    const ORDERARTICLE_PIC2 = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXPIC2;

    /**
     * 3# Picture filename
     */
    const ORDERARTICLE_PIC3 = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXPIC3;

    /**
     * 4# Picture filename
     */
    const ORDERARTICLE_PIC4 = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXPIC4;

    /**
     * 5# Picture filename
     */
    const ORDERARTICLE_PIC5 = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXPIC5;

    /**
     * Weight (kg)
     */
    const ORDERARTICLE_WEIGHT = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXWEIGHT;

    /**
     * Article quantity in stock
     */
    const ORDERARTICLE_STOCK = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXSTOCK;

    /**
     * Date, when the product will be available again if it is sold out
     */
    const ORDERARTICLE_DELIVERY = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXDELIVERY;

    /**
     * Creation time
     */
    const ORDERARTICLE_INSERT = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXINSERT;

    /**
     * Timestamp
     */
    const ORDERARTICLE_TIMESTAMP = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXTIMESTAMP;

    /**
     * Article dimensions: Length
     */
    const ORDERARTICLE_LENGTH = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXLENGTH;

    /**
     * Article dimensions: Width
     */
    const ORDERARTICLE_WIDTH = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXWIDTH;

    /**
     * Article dimensions: Height
     */
    const ORDERARTICLE_HEIGHT = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXHEIGHT;

    /**
     * File, shown in article media list
     */
    const ORDERARTICLE_FILE = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXFILE;

    /**
     * Search terms (multilanguage)
     */
    const ORDERARTICLE_SEARCHKEYS = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXSEARCHKEYS;

    /**
     * Alternative template filename (if empty, default is used)
     */
    const ORDERARTICLE_TEMPLATE = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXTEMPLATE;

    /**
     * E-mail for question
     */
    const ORDERARTICLE_QUESTIONEMAIL = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXQUESTIONEMAIL;

    /**
     * Should article be shown in search
     */
    const ORDERARTICLE_ISSEARCH = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXISSEARCH;

    /**
     * Folder
     */
    const ORDERARTICLE_FOLDER = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXFOLDER;

    /**
     * Subclass
     */
    const ORDERARTICLE_SUBCLASS = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXSUBCLASS;

    /**
     * Order cancelled
     */
    const ORDERARTICLE_STORNO = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXSTORNO;

    /**
     * Shop id (oxshops), in which order was done
     */
    const ORDERARTICLE_ORDERSHOPID = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXORDERSHOPID;

    /**
     * Bundled article
     */
    const ORDERARTICLE_ISBUNDLE = TABLE\OXORDERARTICLES. '__' . TABLE\OXORDERARTICLES\OXISBUNDLE;

    /**
     * Action id
     */
    const ORDERFILE_ID = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXID;

    /**
     * Order id (oxorder)
     */
    const ORDERFILE_ORDERID = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXORDERID;

    /**
     * Filename
     */
    const ORDERFILE_FILENAME = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXFILENAME;

    /**
     * File id (oxfiles)
     */
    const ORDERFILE_FILEID = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXFILEID;

    /**
     * Shop id (oxshops)
     */
    const ORDERFILE_SHOPID = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXSHOPID;

    /**
     * Ordered article id (oxorderarticles)
     */
    const ORDERFILE_ORDERARTICLEID = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXORDERARTICLEID;

    /**
     * First time downloaded time
     */
    const ORDERFILE_FIRSTDOWNLOAD = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXFIRSTDOWNLOAD;

    /**
     * Last time downloaded time
     */
    const ORDERFILE_LASTDOWNLOAD = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXLASTDOWNLOAD;

    /**
     * Downloads count
     */
    const ORDERFILE_DOWNLOADCOUNT = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXDOWNLOADCOUNT;

    /**
     * Maximum count of downloads
     */
    const ORDERFILE_MAXDOWNLOADCOUNT = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXMAXDOWNLOADCOUNT;

    /**
     * Download expiration time in hours
     */
    const ORDERFILE_DOWNLOADEXPIRATIONTIME = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXDOWNLOADEXPIRATIONTIME;

    /**
     * Link expiration time in hours
     */
    const ORDERFILE_LINKEXPIRATIONTIME = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXLINKEXPIRATIONTIME;

    /**
     * Count of resets
     */
    const ORDERFILE_RESETCOUNT = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXRESETCOUNT;

    /**
     * Download is valid until time specified
     */
    const ORDERFILE_VALIDUNTIL = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXVALIDUNTIL;

    /**
     * Timestamp
     */
    const ORDERFILE_TIMESTAMP = TABLE\OXORDERFILES. '__' . TABLE\OXORDERFILES\OXTIMESTAMP;

    /**
     * Action id
     */
    const PAYMENT_ID = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXID;

    /**
     * Active
     */
    const PAYMENT_ACTIVE = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXACTIVE;

    /**
     * Description (multilanguage)
     */
    const PAYMENT_DESC = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXDESC;

    /**
     * Price Surcharge/Reduction amount
     */
    const PAYMENT_ADDSUM = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXADDSUM;

    /**
     * Price Surcharge/Reduction type (abs|%)
     */
    const PAYMENT_ADDSUMTYPE = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXADDSUMTYPE;

    /**
     * Base of price surcharge/reduction: 1 - Value of all goods in cart, 2 - Discounts, 4 - Vouchers, 8 - Shipping costs, 16 - Gift Wrapping/Greeting Card
     */
    const PAYMENT_ADDSUMRULES = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXADDSUMRULES;

    /**
     * Minimal Credit Rating
     */
    const PAYMENT_FROMBONI = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXFROMBONI;

    /**
     * Purchase Price: From
     */
    const PAYMENT_FROMAMOUNT = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXFROMAMOUNT;

    /**
     * Purchase Price: To
     */
    const PAYMENT_TOAMOUNT = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXTOAMOUNT;

    /**
     * Payment additional fields, separated by "field1__@@field2" (multilanguage)
     */
    const PAYMENT_VALDESC = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXVALDESC;

    /**
     * Selected as the default method
     */
    const PAYMENT_CHECKED = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXCHECKED;

    /**
     * Long description, used for promotion (multilanguage)
     */
    const PAYMENT_LONGDESC = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXLONGDESC;

    /**
     * Sorting
     */
    const PAYMENT_SORT = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXSORT;

    /**
     * Timestamp
     */
    const PAYMENT_TIMESTAMP = TABLE\OXPAYMENTS. '__' . TABLE\OXPAYMENTS\OXTIMESTAMP;

    /**
     * Action id
     */
    const PRICEALARM_ID = TABLE\OXPRICEALARM. '__' . TABLE\OXPRICEALARM\OXID;

    /**
     * Shop id (oxshops)
     */
    const PRICEALARM_SHOPID = TABLE\OXPRICEALARM. '__' . TABLE\OXPRICEALARM\OXSHOPID;

    /**
     * User id (oxuser)
     */
    const PRICEALARM_USERID = TABLE\OXPRICEALARM. '__' . TABLE\OXPRICEALARM\OXUSERID;

    /**
     * Email
     */
    const PRICEALARM_EMAIL = TABLE\OXPRICEALARM. '__' . TABLE\OXPRICEALARM\OXEMAIL;

    /**
     * Article id (oxarticles)
     */
    const PRICEALARM_ARTID = TABLE\OXPRICEALARM. '__' . TABLE\OXPRICEALARM\OXARTID;

    /**
     * Article Price
     */
    const PRICEALARM_PRICE = TABLE\OXPRICEALARM. '__' . TABLE\OXPRICEALARM\OXPRICE;

    /**
     * Currency
     */
    const PRICEALARM_CURRENCY = TABLE\OXPRICEALARM. '__' . TABLE\OXPRICEALARM\OXCURRENCY;

    /**
     * Language id
     */
    const PRICEALARM_LANG = TABLE\OXPRICEALARM. '__' . TABLE\OXPRICEALARM\OXLANG;

    /**
     * Creation time
     */
    const PRICEALARM_INSERT = TABLE\OXPRICEALARM. '__' . TABLE\OXPRICEALARM\OXINSERT;

    /**
     * Time, when notification was sent
     */
    const PRICEALARM_SENDED = TABLE\OXPRICEALARM. '__' . TABLE\OXPRICEALARM\OXSENDED;

    /**
     * Timestamp
     */
    const PRICEALARM_TIMESTAMP = TABLE\OXPRICEALARM. '__' . TABLE\OXPRICEALARM\OXTIMESTAMP;

    /**
     * Action id
     */
    const RATING_ID = TABLE\OXRATINGS. '__' . TABLE\OXRATINGS\OXID;

    /**
     * Shop id (oxshops)
     */
    const RATING_SHOPID = TABLE\OXRATINGS. '__' . TABLE\OXRATINGS\OXSHOPID;

    /**
     * User id (oxuser)
     */
    const RATING_USERID = TABLE\OXRATINGS. '__' . TABLE\OXRATINGS\OXUSERID;

    /**
     * Action type: 0 or 1 - action, 2 - promotion, 3 - banner
     */
    const RATING_TYPE = TABLE\OXRATINGS. '__' . TABLE\OXRATINGS\OXTYPE;

    /**
     * Article id (oxarticles)
     */
    const RATING_OBJECTID = TABLE\OXRATINGS. '__' . TABLE\OXRATINGS\OXOBJECTID;

    /**
     * Article rating
     */
    const RATING_RATING = TABLE\OXRATINGS. '__' . TABLE\OXRATINGS\OXRATING;

    /**
     * Timestamp
     */
    const RATING_TIMESTAMP = TABLE\OXRATINGS. '__' . TABLE\OXRATINGS\OXTIMESTAMP;

    /**
     * Action id
     */
    const RECOMMENDATIONLIST_ID = TABLE\OXRECOMMLISTS. '__' . TABLE\OXRECOMMLISTS\OXID;

    /**
     * Shop id (oxshops)
     */
    const RECOMMENDATIONLIST_SHOPID = TABLE\OXRECOMMLISTS. '__' . TABLE\OXRECOMMLISTS\OXSHOPID;

    /**
     * User id (oxuser)
     */
    const RECOMMENDATIONLIST_USERID = TABLE\OXRECOMMLISTS. '__' . TABLE\OXRECOMMLISTS\OXUSERID;

    /**
     * Author first and last name
     */
    const RECOMMENDATIONLIST_AUTHOR = TABLE\OXRECOMMLISTS. '__' . TABLE\OXRECOMMLISTS\OXAUTHOR;

    /**
     * Title (multilanguage)
     */
    const RECOMMENDATIONLIST_TITLE = TABLE\OXRECOMMLISTS. '__' . TABLE\OXRECOMMLISTS\OXTITLE;

    /**
     * Description (multilanguage)
     */
    const RECOMMENDATIONLIST_DESC = TABLE\OXRECOMMLISTS. '__' . TABLE\OXRECOMMLISTS\OXDESC;

    /**
     * Rating votes count
     */
    const RECOMMENDATIONLIST_RATINGCNT = TABLE\OXRECOMMLISTS. '__' . TABLE\OXRECOMMLISTS\OXRATINGCNT;

    /**
     * Article rating
     */
    const RECOMMENDATIONLIST_RATING = TABLE\OXRECOMMLISTS. '__' . TABLE\OXRECOMMLISTS\OXRATING;

    /**
     * Timestamp
     */
    const RECOMMENDATIONLIST_TIMESTAMP = TABLE\OXRECOMMLISTS. '__' . TABLE\OXRECOMMLISTS\OXTIMESTAMP;

    /**
     * Action id
     */
    const REMARK_ID = TABLE\OXREMARK. '__' . TABLE\OXREMARK\OXID;

    /**
     * Parent article id
     */
    const REMARK_PARENTID = TABLE\OXREMARK. '__' . TABLE\OXREMARK\OXPARENTID;

    /**
     * Action type: 0 or 1 - action, 2 - promotion, 3 - banner
     */
    const REMARK_TYPE = TABLE\OXREMARK. '__' . TABLE\OXREMARK\OXTYPE;

    /**
     * Header (default: Creation time)
     */
    const REMARK_HEADER = TABLE\OXREMARK. '__' . TABLE\OXREMARK\OXHEADER;

    /**
     * Remark text
     */
    const REMARK_TEXT = TABLE\OXREMARK. '__' . TABLE\OXREMARK\OXTEXT;

    /**
     * Creation time
     */
    const REMARK_CREATE = TABLE\OXREMARK. '__' . TABLE\OXREMARK\OXCREATE;

    /**
     * Timestamp
     */
    const REMARK_TIMESTAMP = TABLE\OXREMARK. '__' . TABLE\OXREMARK\OXTIMESTAMP;

    /**
     * Action id
     */
    const REVIEW_ID = TABLE\OXREVIEWS. '__' . TABLE\OXREVIEWS\OXID;

    /**
     * Active
     */
    const REVIEW_ACTIVE = TABLE\OXREVIEWS. '__' . TABLE\OXREVIEWS\OXACTIVE;

    /**
     * Article id (oxarticles)
     */
    const REVIEW_OBJECTID = TABLE\OXREVIEWS. '__' . TABLE\OXREVIEWS\OXOBJECTID;

    /**
     * Action type: 0 or 1 - action, 2 - promotion, 3 - banner
     */
    const REVIEW_TYPE = TABLE\OXREVIEWS. '__' . TABLE\OXREVIEWS\OXTYPE;

    /**
     * Remark text
     */
    const REVIEW_TEXT = TABLE\OXREVIEWS. '__' . TABLE\OXREVIEWS\OXTEXT;

    /**
     * User id (oxuser)
     */
    const REVIEW_USERID = TABLE\OXREVIEWS. '__' . TABLE\OXREVIEWS\OXUSERID;

    /**
     * Creation time
     */
    const REVIEW_CREATE = TABLE\OXREVIEWS. '__' . TABLE\OXREVIEWS\OXCREATE;

    /**
     * Language id
     */
    const REVIEW_LANG = TABLE\OXREVIEWS. '__' . TABLE\OXREVIEWS\OXLANG;

    /**
     * Article rating
     */
    const REVIEW_RATING = TABLE\OXREVIEWS. '__' . TABLE\OXREVIEWS\OXRATING;

    /**
     * Timestamp
     */
    const REVIEW_TIMESTAMP = TABLE\OXREVIEWS. '__' . TABLE\OXREVIEWS\OXTIMESTAMP;

    /**
     * Action id
     */
    const SELECTLIST_ID = TABLE\OXSELECTLIST. '__' . TABLE\OXSELECTLIST\OXID;

    /**
     * Shop id (oxshops)
     */
    const SELECTLIST_SHOPID = TABLE\OXSELECTLIST. '__' . TABLE\OXSELECTLIST\OXSHOPID;

    /**
     * Title (multilanguage)
     */
    const SELECTLIST_TITLE = TABLE\OXSELECTLIST. '__' . TABLE\OXSELECTLIST\OXTITLE;

    /**
     * Working Title
     */
    const SELECTLIST_IDENT = TABLE\OXSELECTLIST. '__' . TABLE\OXSELECTLIST\OXIDENT;

    /**
     * Payment additional fields, separated by "field1__@@field2" (multilanguage)
     */
    const SELECTLIST_VALDESC = TABLE\OXSELECTLIST. '__' . TABLE\OXSELECTLIST\OXVALDESC;

    /**
     * Timestamp
     */
    const SELECTLIST_TIMESTAMP = TABLE\OXSELECTLIST. '__' . TABLE\OXSELECTLIST\OXTIMESTAMP;

    /**
     * Action id
     */
    const SHOP_ID = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXID;

    /**
     * Active
     */
    const SHOP_ACTIVE = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXACTIVE;

    /**
     * Productive Mode (if 0, debug info displayed)
     */
    const SHOP_PRODUCTIVE = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXPRODUCTIVE;

    /**
     * Default currency
     */
    const SHOP_DEFCURRENCY = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXDEFCURRENCY;

    /**
     * Default language id
     */
    const SHOP_DEFLANGUAGE = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXDEFLANGUAGE;

    /**
     * Shop name
     */
    const SHOP_NAME = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXNAME;

    /**
     * Seo title prefix (multilanguage)
     */
    const SHOP_TITLEPREFIX = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXTITLEPREFIX;

    /**
     * Seo title suffix (multilanguage)
     */
    const SHOP_TITLESUFFIX = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXTITLESUFFIX;

    /**
     * Start page title (multilanguage)
     */
    const SHOP_STARTTITLE = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXSTARTTITLE;

    /**
     * Informational email address
     */
    const SHOP_INFOEMAIL = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXINFOEMAIL;

    /**
     * Order email address
     */
    const SHOP_ORDEREMAIL = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXORDEREMAIL;

    /**
     * Owner email address
     */
    const SHOP_OWNEREMAIL = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXOWNEREMAIL;

    /**
     * Order email subject (multilanguage)
     */
    const SHOP_ORDERSUBJECT = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXORDERSUBJECT;

    /**
     * Registration email subject (multilanguage)
     */
    const SHOP_REGISTERSUBJECT = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXREGISTERSUBJECT;

    /**
     * Forgot password email subject (multilanguage)
     */
    const SHOP_FORGOTPWDSUBJECT = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXFORGOTPWDSUBJECT;

    /**
     * Order sent email subject (multilanguage)
     */
    const SHOP_SENDEDNOWSUBJECT = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXSENDEDNOWSUBJECT;

    /**
     * SMTP server
     */
    const SHOP_SMTP = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXSMTP;

    /**
     * SMTP user
     */
    const SHOP_SMTPUSER = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXSMTPUSER;

    /**
     * SMTP password
     */
    const SHOP_SMTPPWD = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXSMTPPWD;

    /**
     * Company name
     */
    const SHOP_COMPANY = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXCOMPANY;

    /**
     * Street
     */
    const SHOP_STREET = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXSTREET;

    /**
     * Zip code
     */
    const SHOP_ZIP = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXZIP;

    /**
     * City
     */
    const SHOP_CITY = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXCITY;

    /**
     * Country name
     */
    const SHOP_COUNTRY = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXCOUNTRY;

    /**
     * Bank name
     */
    const SHOP_BANKNAME = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXBANKNAME;

    /**
     * Account Number
     */
    const SHOP_BANKNUMBER = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXBANKNUMBER;

    /**
     * Routing Number
     */
    const SHOP_BANKCODE = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXBANKCODE;

    /**
     * Sales Tax ID
     */
    const SHOP_VATNUMBER = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXVATNUMBER;

    /**
     * Tax ID
     */
    const SHOP_TAXNUMBER = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXTAXNUMBER;

    /**
     * Bank BIC
     */
    const SHOP_BICCODE = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXBICCODE;

    /**
     * Bank IBAN
     */
    const SHOP_IBANNUMBER = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXIBANNUMBER;

    /**
     * First name
     */
    const SHOP_FNAME = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXFNAME;

    /**
     * Last name
     */
    const SHOP_LNAME = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXLNAME;

    /**
     * Phone number
     */
    const SHOP_TELEFON = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXTELEFON;

    /**
     * Fax number
     */
    const SHOP_TELEFAX = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXTELEFAX;

    /**
     * Link url
     */
    const SHOP_URL = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXURL;

    /**
     * Default category id
     */
    const SHOP_DEFCAT = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXDEFCAT;

    /**
     * CBR
     */
    const SHOP_HRBNR = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXHRBNR;

    /**
     * District Court
     */
    const SHOP_COURT = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXCOURT;

    /**
     * Adbutler code (belboon.de) - deprecated
     */
    const SHOP_ADBUTLERID = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXADBUTLERID;

    /**
     * Affilinet code (webmasterplan.com) - deprecated
     */
    const SHOP_AFFILINETID = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXAFFILINETID;

    /**
     * Superclix code (superclix.de) - deprecated
     */
    const SHOP_SUPERCLICKSID = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXSUPERCLICKSID;

    /**
     * Affiliwelt code (affiliwelt.net) - deprecated
     */
    const SHOP_AFFILIWELTID = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXAFFILIWELTID;

    /**
     * Affili24 code (affili24.com) - deprecated
     */
    const SHOP_AFFILI24ID = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXAFFILI24ID;

    /**
     * Shop Edition (CE,PE,EE (@deprecated since v6.0.0-RC.2 (2017-08-24))
     */
    const SHOP_EDITION = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXEDITION;

    /**
     * Shop Version (@deprecated since v6.0.0-RC.2 (2017-08-22))
     */
    const SHOP_VERSION = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXVERSION;

    /**
     * Seo active (multilanguage)
     */
    const SHOP_SEOACTIVE = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXSEOACTIVE;

    /**
     * Timestamp
     */
    const SHOP_TIMESTAMP = TABLE\OXSHOPS. '__' . TABLE\OXSHOPS\OXTIMESTAMP;

    /**
     * Action id
     */
    const SIMPLEVARIANT_ID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXID;

    /**
     * Shop id (oxshops)
     */
    const SIMPLEVARIANT_SHOPID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSHOPID;

    /**
     * Parent article id
     */
    const SIMPLEVARIANT_PARENTID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPARENTID;

    /**
     * Active
     */
    const SIMPLEVARIANT_ACTIVE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXACTIVE;

    /**
     * Hidden
     */
    const SIMPLEVARIANT_HIDDEN = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXHIDDEN;

    /**
     * Active from specified date
     */
    const SIMPLEVARIANT_ACTIVEFROM = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXACTIVEFROM;

    /**
     * Active to specified date
     */
    const SIMPLEVARIANT_ACTIVETO = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXACTIVETO;

    /**
     * Article number
     */
    const SIMPLEVARIANT_ARTNUM = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXARTNUM;

    /**
     * International Article Number (EAN)
     */
    const SIMPLEVARIANT_EAN = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXEAN;

    /**
     * Manufacture International Article Number (Man. EAN)
     */
    const SIMPLEVARIANT_DISTEAN = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXDISTEAN;

    /**
     * Manufacture Part Number (MPN)
     */
    const SIMPLEVARIANT_MPN = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXMPN;

    /**
     * Title (multilanguage)
     */
    const SIMPLEVARIANT_TITLE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXTITLE;

    /**
     * Short description (multilanguage)
     */
    const SIMPLEVARIANT_SHORTDESC = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSHORTDESC;

    /**
     * Article Price
     */
    const SIMPLEVARIANT_PRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPRICE;

    /**
     * No Promotions (Price Alert)
     */
    const SIMPLEVARIANT_BLFIXEDPRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXBLFIXEDPRICE;

    /**
     * Price A
     */
    const SIMPLEVARIANT_PRICEA = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPRICEA;

    /**
     * Price B
     */
    const SIMPLEVARIANT_PRICEB = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPRICEB;

    /**
     * Price C
     */
    const SIMPLEVARIANT_PRICEC = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPRICEC;

    /**
     * Purchase Price
     */
    const SIMPLEVARIANT_BPRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXBPRICE;

    /**
     * Recommended Retail Price (RRP)
     */
    const SIMPLEVARIANT_TPRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXTPRICE;

    /**
     * Unit name (kg,g,l,cm etc), used in setting price per quantity unit calculation
     */
    const SIMPLEVARIANT_UNITNAME = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUNITNAME;

    /**
     * Article quantity, used in setting price per quantity unit calculation
     */
    const SIMPLEVARIANT_UNITQUANTITY = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUNITQUANTITY;

    /**
     * External URL to other information about the article
     */
    const SIMPLEVARIANT_EXTURL = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXEXTURL;

    /**
     * Text for external URL (multilanguage)
     */
    const SIMPLEVARIANT_URLDESC = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXURLDESC;

    /**
     * External URL image
     */
    const SIMPLEVARIANT_URLIMG = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXURLIMG;

    /**
     * Value added tax. If specified, used in all calculations instead of global vat
     */
    const SIMPLEVARIANT_VAT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVAT;

    /**
     * Thumbnail filename
     */
    const SIMPLEVARIANT_THUMB = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXTHUMB;

    /**
     * Icon filename
     */
    const SIMPLEVARIANT_ICON = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXICON;

    /**
     * 1# Picture filename
     */
    const SIMPLEVARIANT_PIC1 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC1;

    /**
     * 2# Picture filename
     */
    const SIMPLEVARIANT_PIC2 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC2;

    /**
     * 3# Picture filename
     */
    const SIMPLEVARIANT_PIC3 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC3;

    /**
     * 4# Picture filename
     */
    const SIMPLEVARIANT_PIC4 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC4;

    /**
     * 5# Picture filename
     */
    const SIMPLEVARIANT_PIC5 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC5;

    /**
     * 6# Picture filename
     */
    const SIMPLEVARIANT_PIC6 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC6;

    /**
     * 7# Picture filename
     */
    const SIMPLEVARIANT_PIC7 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC7;

    /**
     * 8# Picture filename
     */
    const SIMPLEVARIANT_PIC8 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC8;

    /**
     * 9# Picture filename
     */
    const SIMPLEVARIANT_PIC9 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC9;

    /**
     * 10# Picture filename
     */
    const SIMPLEVARIANT_PIC10 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC10;

    /**
     * 11# Picture filename
     */
    const SIMPLEVARIANT_PIC11 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC11;

    /**
     * 12# Picture filename
     */
    const SIMPLEVARIANT_PIC12 = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXPIC12;

    /**
     * Weight (kg)
     */
    const SIMPLEVARIANT_WEIGHT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXWEIGHT;

    /**
     * Article quantity in stock
     */
    const SIMPLEVARIANT_STOCK = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSTOCK;

    /**
     * Delivery Status: 1 - Standard, 2 - If out of Stock, offline, 3 - If out of Stock, not orderable, 4 - External Storehouse
     */
    const SIMPLEVARIANT_STOCKFLAG = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSTOCKFLAG;

    /**
     * Message, which is shown if the article is in stock (multilanguage)
     */
    const SIMPLEVARIANT_STOCKTEXT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSTOCKTEXT;

    /**
     * Message, which is shown if the article is off stock (multilanguage)
     */
    const SIMPLEVARIANT_NOSTOCKTEXT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXNOSTOCKTEXT;

    /**
     * Date, when the product will be available again if it is sold out
     */
    const SIMPLEVARIANT_DELIVERY = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXDELIVERY;

    /**
     * Creation time
     */
    const SIMPLEVARIANT_INSERT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXINSERT;

    /**
     * Timestamp
     */
    const SIMPLEVARIANT_TIMESTAMP = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXTIMESTAMP;

    /**
     * Article dimensions: Length
     */
    const SIMPLEVARIANT_LENGTH = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXLENGTH;

    /**
     * Article dimensions: Width
     */
    const SIMPLEVARIANT_WIDTH = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXWIDTH;

    /**
     * Article dimensions: Height
     */
    const SIMPLEVARIANT_HEIGHT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXHEIGHT;

    /**
     * File, shown in article media list
     */
    const SIMPLEVARIANT_FILE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXFILE;

    /**
     * Search terms (multilanguage)
     */
    const SIMPLEVARIANT_SEARCHKEYS = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSEARCHKEYS;

    /**
     * Alternative template filename (if empty, default is used)
     */
    const SIMPLEVARIANT_TEMPLATE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXTEMPLATE;

    /**
     * E-mail for question
     */
    const SIMPLEVARIANT_QUESTIONEMAIL = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXQUESTIONEMAIL;

    /**
     * Should article be shown in search
     */
    const SIMPLEVARIANT_ISSEARCH = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXISSEARCH;

    /**
     * Can article be customized
     */
    const SIMPLEVARIANT_ISCONFIGURABLE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXISCONFIGURABLE;

    /**
     * Name of variants selection lists (different lists are separated by | ) (multilanguage)
     */
    const SIMPLEVARIANT_VARNAME = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVARNAME;

    /**
     * Sum of active article variants stock quantity
     */
    const SIMPLEVARIANT_VARSTOCK = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVARSTOCK;

    /**
     * Total number of variants that article has (active and inactive)
     */
    const SIMPLEVARIANT_VARCOUNT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVARCOUNT;

    /**
     * Variant article selections (separated by | ) (multilanguage)
     */
    const SIMPLEVARIANT_VARSELECT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVARSELECT;

    /**
     * Lowest price in active article variants
     */
    const SIMPLEVARIANT_VARMINPRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVARMINPRICE;

    /**
     * Highest price in active article variants
     */
    const SIMPLEVARIANT_VARMAXPRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVARMAXPRICE;

    /**
     * Bundled article id
     */
    const SIMPLEVARIANT_BUNDLEID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXBUNDLEID;

    /**
     * Folder
     */
    const SIMPLEVARIANT_FOLDER = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXFOLDER;

    /**
     * Subclass
     */
    const SIMPLEVARIANT_SUBCLASS = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSUBCLASS;

    /**
     * Sorting
     */
    const SIMPLEVARIANT_SORT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSORT;

    /**
     * Amount of sold articles including variants (used only for parent articles)
     */
    const SIMPLEVARIANT_SOLDAMOUNT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSOLDAMOUNT;

    /**
     * Intangible article, free shipping is used (variants inherits parent setting)
     */
    const SIMPLEVARIANT_NONMATERIAL = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXNONMATERIAL;

    /**
     * Free shipping (variants inherits parent setting)
     */
    const SIMPLEVARIANT_FREESHIPPING = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXFREESHIPPING;

    /**
     * Enables sending of notification email when oxstock field value falls below oxremindamount value
     */
    const SIMPLEVARIANT_REMINDACTIVE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXREMINDACTIVE;

    /**
     * Defines the amount, below which notification email will be sent if oxremindactive is set to 1
     */
    const SIMPLEVARIANT_REMINDAMOUNT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXREMINDAMOUNT;

    /**
     *
     */
    const SIMPLEVARIANT_AMITEMID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXAMITEMID;

    /**
     *
     */
    const SIMPLEVARIANT_AMTASKID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXAMTASKID;

    /**
     * Vendor id (oxvendor)
     */
    const SIMPLEVARIANT_VENDORID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXVENDORID;

    /**
     * Manufacturer id (oxmanufacturers)
     */
    const SIMPLEVARIANT_MANUFACTURERID = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXMANUFACTURERID;

    /**
     * Skips all negative Discounts (Discounts, Vouchers, Delivery ...)
     */
    const SIMPLEVARIANT_SKIPDISCOUNTS = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSKIPDISCOUNTS;

    /**
     * Article rating
     */
    const SIMPLEVARIANT_RATING = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXRATING;

    /**
     * Rating votes count
     */
    const SIMPLEVARIANT_RATINGCNT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXRATINGCNT;

    /**
     * Minimal delivery time (unit is set in oxdeltimeunit)
     */
    const SIMPLEVARIANT_MINDELTIME = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXMINDELTIME;

    /**
     * Maximum delivery time (unit is set in oxdeltimeunit)
     */
    const SIMPLEVARIANT_MAXDELTIME = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXMAXDELTIME;

    /**
     * Delivery time unit: DAY, WEEK, MONTH
     */
    const SIMPLEVARIANT_DELTIMEUNIT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXDELTIMEUNIT;

    /**
     * If not 0, oxprice will be updated to this value on oxupdatepricetime date
     */
    const SIMPLEVARIANT_UPDATEPRICE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUPDATEPRICE;

    /**
     * If not 0, oxpricea will be updated to this value on oxupdatepricetime date
     */
    const SIMPLEVARIANT_UPDATEPRICEA = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUPDATEPRICEA;

    /**
     * If not 0, oxpriceb will be updated to this value on oxupdatepricetime date
     */
    const SIMPLEVARIANT_UPDATEPRICEB = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUPDATEPRICEB;

    /**
     * If not 0, oxpricec will be updated to this value on oxupdatepricetime date
     */
    const SIMPLEVARIANT_UPDATEPRICEC = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUPDATEPRICEC;

    /**
     * Date, when oxprice[a,b,c] should be updated to oxupdateprice[a,b,c] values
     */
    const SIMPLEVARIANT_UPDATEPRICETIME = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXUPDATEPRICETIME;

    /**
     * Enable download of files for this product
     */
    const SIMPLEVARIANT_ISDOWNLOADABLE = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXISDOWNLOADABLE;

    /**
     * Show custom agreement check in checkout
     */
    const SIMPLEVARIANT_SHOWCUSTOMAGREEMENT = TABLE\OXARTICLES. '__' . TABLE\OXARTICLES\OXSHOWCUSTOMAGREEMENT;

    /**
     * Action id
     */
    const STATE_ID = TABLE\OXSTATES. '__' . TABLE\OXSTATES\OXID;

    /**
     * Country id (oxcountry)
     */
    const STATE_COUNTRYID = TABLE\OXSTATES. '__' . TABLE\OXSTATES\OXCOUNTRYID;

    /**
     * Title (multilanguage)
     */
    const STATE_TITLE = TABLE\OXSTATES. '__' . TABLE\OXSTATES\OXTITLE;

    /**
     * ISO 3166-1 alpha-2
     */
    const STATE_ISOALPHA2 = TABLE\OXSTATES. '__' . TABLE\OXSTATES\OXISOALPHA2;

    /**
     * Timestamp
     */
    const STATE_TIMESTAMP = TABLE\OXSTATES. '__' . TABLE\OXSTATES\OXTIMESTAMP;

    /**
     * Action id
     */
    const USER_ID = TABLE\OXUSER. '__' . TABLE\OXUSER\OXID;

    /**
     * Active
     */
    const USER_ACTIVE = TABLE\OXUSER. '__' . TABLE\OXUSER\OXACTIVE;

    /**
     * User rights: user, malladmin
     */
    const USER_RIGHTS = TABLE\OXUSER. '__' . TABLE\OXUSER\OXRIGHTS;

    /**
     * Shop id (oxshops)
     */
    const USER_SHOPID = TABLE\OXUSER. '__' . TABLE\OXUSER\OXSHOPID;

    /**
     * Username
     */
    const USER_USERNAME = TABLE\OXUSER. '__' . TABLE\OXUSER\OXUSERNAME;

    /**
     * Hashed password
     */
    const USER_PASSWORD = TABLE\OXUSER. '__' . TABLE\OXUSER\OXPASSWORD;

    /**
     * Password salt
     */
    const USER_PASSSALT = TABLE\OXUSER. '__' . TABLE\OXUSER\OXPASSSALT;

    /**
     * Customer number
     */
    const USER_CUSTNR = TABLE\OXUSER. '__' . TABLE\OXUSER\OXCUSTNR;

    /**
     * VAT ID No.
     */
    const USER_USTID = TABLE\OXUSER. '__' . TABLE\OXUSER\OXUSTID;

    /**
     * Company name
     */
    const USER_COMPANY = TABLE\OXUSER. '__' . TABLE\OXUSER\OXCOMPANY;

    /**
     * First name
     */
    const USER_FNAME = TABLE\OXUSER. '__' . TABLE\OXUSER\OXFNAME;

    /**
     * Last name
     */
    const USER_LNAME = TABLE\OXUSER. '__' . TABLE\OXUSER\OXLNAME;

    /**
     * Street
     */
    const USER_STREET = TABLE\OXUSER. '__' . TABLE\OXUSER\OXSTREET;

    /**
     * House number
     */
    const USER_STREETNR = TABLE\OXUSER. '__' . TABLE\OXUSER\OXSTREETNR;

    /**
     * Additional info
     */
    const USER_ADDINFO = TABLE\OXUSER. '__' . TABLE\OXUSER\OXADDINFO;

    /**
     * City
     */
    const USER_CITY = TABLE\OXUSER. '__' . TABLE\OXUSER\OXCITY;

    /**
     * Country id (oxcountry)
     */
    const USER_COUNTRYID = TABLE\OXUSER. '__' . TABLE\OXUSER\OXCOUNTRYID;

    /**
     * State id (oxstate)
     */
    const USER_STATEID = TABLE\OXUSER. '__' . TABLE\OXUSER\OXSTATEID;

    /**
     * Zip code
     */
    const USER_ZIP = TABLE\OXUSER. '__' . TABLE\OXUSER\OXZIP;

    /**
     * Phone number
     */
    const USER_FON = TABLE\OXUSER. '__' . TABLE\OXUSER\OXFON;

    /**
     * Fax number
     */
    const USER_FAX = TABLE\OXUSER. '__' . TABLE\OXUSER\OXFAX;

    /**
     * User title prefix (Mr/Mrs)
     */
    const USER_SAL = TABLE\OXUSER. '__' . TABLE\OXUSER\OXSAL;

    /**
     * Credit points
     */
    const USER_BONI = TABLE\OXUSER. '__' . TABLE\OXUSER\OXBONI;

    /**
     * Creation time
     */
    const USER_CREATE = TABLE\OXUSER. '__' . TABLE\OXUSER\OXCREATE;

    /**
     * Registration time
     */
    const USER_REGISTER = TABLE\OXUSER. '__' . TABLE\OXUSER\OXREGISTER;

    /**
     * Personal phone number
     */
    const USER_PRIVFON = TABLE\OXUSER. '__' . TABLE\OXUSER\OXPRIVFON;

    /**
     * Mobile phone number
     */
    const USER_MOBFON = TABLE\OXUSER. '__' . TABLE\OXUSER\OXMOBFON;

    /**
     * Birthday date
     */
    const USER_BIRTHDATE = TABLE\OXUSER. '__' . TABLE\OXUSER\OXBIRTHDATE;

    /**
     * Link url
     */
    const USER_URL = TABLE\OXUSER. '__' . TABLE\OXUSER\OXURL;

    /**
     * Update key
     */
    const USER_UPDATEKEY = TABLE\OXUSER. '__' . TABLE\OXUSER\OXUPDATEKEY;

    /**
     * Update key expiration time
     */
    const USER_UPDATEEXP = TABLE\OXUSER. '__' . TABLE\OXUSER\OXUPDATEEXP;

    /**
     * User points (for registration, invitation, etc)
     */
    const USER_POINTS = TABLE\OXUSER. '__' . TABLE\OXUSER\OXPOINTS;

    /**
     * Timestamp
     */
    const USER_TIMESTAMP = TABLE\OXUSER. '__' . TABLE\OXUSER\OXTIMESTAMP;

    /**
     * Action id
     */
    const USERBASKET_ID = TABLE\OXUSERBASKETS. '__' . TABLE\OXUSERBASKETS\OXID;

    /**
     * User id (oxuser)
     */
    const USERBASKET_USERID = TABLE\OXUSERBASKETS. '__' . TABLE\OXUSERBASKETS\OXUSERID;

    /**
     * Title (multilanguage)
     */
    const USERBASKET_TITLE = TABLE\OXUSERBASKETS. '__' . TABLE\OXUSERBASKETS\OXTITLE;

    /**
     * Timestamp
     */
    const USERBASKET_TIMESTAMP = TABLE\OXUSERBASKETS. '__' . TABLE\OXUSERBASKETS\OXTIMESTAMP;

    /**
     * Is public
     */
    const USERBASKET_PUBLIC = TABLE\OXUSERBASKETS. '__' . TABLE\OXUSERBASKETS\OXPUBLIC;

    /**
     * Update timestamp
     */
    const USERBASKET_UPDATE = TABLE\OXUSERBASKETS. '__' . TABLE\OXUSERBASKETS\OXUPDATE;

    /**
     * Action id
     */
    const USERBASKETITEM_ID = TABLE\OXUSERBASKETITEMS. '__' . TABLE\OXUSERBASKETITEMS\OXID;

    /**
     * Basket id (oxuserbaskets)
     */
    const USERBASKETITEM_BASKETID = TABLE\OXUSERBASKETITEMS. '__' . TABLE\OXUSERBASKETITEMS\OXBASKETID;

    /**
     * Article id (oxarticles)
     */
    const USERBASKETITEM_ARTID = TABLE\OXUSERBASKETITEMS. '__' . TABLE\OXUSERBASKETITEMS\OXARTID;

    /**
     * Valid from specified amount of articles
     */
    const USERBASKETITEM_AMOUNT = TABLE\OXUSERBASKETITEMS. '__' . TABLE\OXUSERBASKETITEMS\OXAMOUNT;

    /**
     * Selection list
     */
    const USERBASKETITEM_SELLIST = TABLE\OXUSERBASKETITEMS. '__' . TABLE\OXUSERBASKETITEMS\OXSELLIST;

    /**
     * Serialized persistent parameters
     */
    const USERBASKETITEM_PERSPARAM = TABLE\OXUSERBASKETITEMS. '__' . TABLE\OXUSERBASKETITEMS\OXPERSPARAM;

    /**
     * Timestamp
     */
    const USERBASKETITEM_TIMESTAMP = TABLE\OXUSERBASKETITEMS. '__' . TABLE\OXUSERBASKETITEMS\OXTIMESTAMP;

    /**
     * Action id
     */
    const USERPAYMENT_ID = TABLE\OXUSERPAYMENTS. '__' . TABLE\OXUSERPAYMENTS\OXID;

    /**
     * User id (oxuser)
     */
    const USERPAYMENT_USERID = TABLE\OXUSERPAYMENTS. '__' . TABLE\OXUSERPAYMENTS\OXUSERID;

    /**
     * Payment id (oxpayments)
     */
    const USERPAYMENT_PAYMENTSID = TABLE\OXUSERPAYMENTS. '__' . TABLE\OXUSERPAYMENTS\OXPAYMENTSID;

    /**
     * DYN payment values array as string
     */
    const USERPAYMENT_VALUE = TABLE\OXUSERPAYMENTS. '__' . TABLE\OXUSERPAYMENTS\OXVALUE;

    /**
     * Timestamp
     */
    const USERPAYMENT_TIMESTAMP = TABLE\OXUSERPAYMENTS. '__' . TABLE\OXUSERPAYMENTS\OXTIMESTAMP;

    /**
     * Action id
     */
    const VENDOR_ID = TABLE\OXVENDOR. '__' . TABLE\OXVENDOR\OXID;

    /**
     * Shop id (oxshops)
     */
    const VENDOR_SHOPID = TABLE\OXVENDOR. '__' . TABLE\OXVENDOR\OXSHOPID;

    /**
     * Active
     */
    const VENDOR_ACTIVE = TABLE\OXVENDOR. '__' . TABLE\OXVENDOR\OXACTIVE;

    /**
     * Icon filename
     */
    const VENDOR_ICON = TABLE\OXVENDOR. '__' . TABLE\OXVENDOR\OXICON;

    /**
     * Title (multilanguage)
     */
    const VENDOR_TITLE = TABLE\OXVENDOR. '__' . TABLE\OXVENDOR\OXTITLE;

    /**
     * Short description (multilanguage)
     */
    const VENDOR_SHORTDESC = TABLE\OXVENDOR. '__' . TABLE\OXVENDOR\OXSHORTDESC;

    /**
     * Show SEO Suffix in Category
     */
    const VENDOR_SHOWSUFFIX = TABLE\OXVENDOR. '__' . TABLE\OXVENDOR\OXSHOWSUFFIX;

    /**
     * Timestamp
     */
    const VENDOR_TIMESTAMP = TABLE\OXVENDOR. '__' . TABLE\OXVENDOR\OXTIMESTAMP;

    /**
     * Action id
     */
    const VOUCHER_ID = TABLE\OXVOUCHERS. '__' . TABLE\OXVOUCHERS\OXID;

    /**
     * Date, when coupon was used (set on order complete)
     */
    const VOUCHER_DATEUSED = TABLE\OXVOUCHERS. '__' . TABLE\OXVOUCHERS\OXDATEUSED;

    /**
     * Order id (oxorder)
     */
    const VOUCHER_ORDERID = TABLE\OXVOUCHERS. '__' . TABLE\OXVOUCHERS\OXORDERID;

    /**
     * User id (oxuser)
     */
    const VOUCHER_USERID = TABLE\OXVOUCHERS. '__' . TABLE\OXVOUCHERS\OXUSERID;

    /**
     * Time, when coupon is added to basket
     */
    const VOUCHER_RESERVED = TABLE\OXVOUCHERS. '__' . TABLE\OXVOUCHERS\OXRESERVED;

    /**
     * Coupon number
     */
    const VOUCHER_VOUCHERNR = TABLE\OXVOUCHERS. '__' . TABLE\OXVOUCHERS\OXVOUCHERNR;

    /**
     * Coupon Series id (oxvoucherseries)
     */
    const VOUCHER_VOUCHERSERIEID = TABLE\OXVOUCHERS. '__' . TABLE\OXVOUCHERS\OXVOUCHERSERIEID;

    /**
     * Additional discount for order (abs)
     */
    const VOUCHER_DISCOUNT = TABLE\OXVOUCHERS. '__' . TABLE\OXVOUCHERS\OXDISCOUNT;

    /**
     * Timestamp
     */
    const VOUCHER_TIMESTAMP = TABLE\OXVOUCHERS. '__' . TABLE\OXVOUCHERS\OXTIMESTAMP;

    /**
     * Action id
     */
    const VOUCHERSERIE_ID = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXID;

    /**
     * Shop id (oxshops)
     */
    const VOUCHERSERIE_SHOPID = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXSHOPID;

    /**
     * Series name
     */
    const VOUCHERSERIE_SERIENR = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXSERIENR;

    /**
     * Description
     */
    const VOUCHERSERIE_SERIEDESCRIPTION = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXSERIEDESCRIPTION;

    /**
     * Additional discount for order (abs)
     */
    const VOUCHERSERIE_DISCOUNT = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXDISCOUNT;

    /**
     * Discount type (percent, absolute)
     */
    const VOUCHERSERIE_DISCOUNTTYPE = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXDISCOUNTTYPE;

    /**
     * Valid from
     */
    const VOUCHERSERIE_BEGINDATE = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXBEGINDATE;

    /**
     * Valid to
     */
    const VOUCHERSERIE_ENDDATE = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXENDDATE;

    /**
     * Coupons of this series can be used with single order
     */
    const VOUCHERSERIE_ALLOWSAMESERIES = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXALLOWSAMESERIES;

    /**
     * Coupons of different series can be used with single order
     */
    const VOUCHERSERIE_ALLOWOTHERSERIES = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXALLOWOTHERSERIES;

    /**
     * Coupons of this series can be used in multiple orders
     */
    const VOUCHERSERIE_ALLOWUSEANOTHER = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXALLOWUSEANOTHER;

    /**
     * Minimum Order Sum
     */
    const VOUCHERSERIE_MINIMUMVALUE = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXMINIMUMVALUE;

    /**
     * Calculate only once (valid only for product or category vouchers)
     */
    const VOUCHERSERIE_CALCULATEONCE = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXCALCULATEONCE;

    /**
     * Timestamp
     */
    const VOUCHERSERIE_TIMESTAMP = TABLE\OXVOUCHERSERIES. '__' . TABLE\OXVOUCHERSERIES\OXTIMESTAMP;

    /**
     * Action id
     */
    const WRAPPING_ID = TABLE\OXWRAPPING. '__' . TABLE\OXWRAPPING\OXID;

    /**
     * Shop id (oxshops)
     */
    const WRAPPING_SHOPID = TABLE\OXWRAPPING. '__' . TABLE\OXWRAPPING\OXSHOPID;

    /**
     * Active
     */
    const WRAPPING_ACTIVE = TABLE\OXWRAPPING. '__' . TABLE\OXWRAPPING\OXACTIVE;

    /**
     * Action type: 0 or 1 - action, 2 - promotion, 3 - banner
     */
    const WRAPPING_TYPE = TABLE\OXWRAPPING. '__' . TABLE\OXWRAPPING\OXTYPE;

    /**
     * Shop name
     */
    const WRAPPING_NAME = TABLE\OXWRAPPING. '__' . TABLE\OXWRAPPING\OXNAME;

    /**
     * Picture filename, used for banner (multilanguage)
     */
    const WRAPPING_PIC = TABLE\OXWRAPPING. '__' . TABLE\OXWRAPPING\OXPIC;

    /**
     * Article Price
     */
    const WRAPPING_PRICE = TABLE\OXWRAPPING. '__' . TABLE\OXWRAPPING\OXPRICE;

    /**
     * Timestamp
     */
    const WRAPPING_TIMESTAMP = TABLE\OXWRAPPING. '__' . TABLE\OXWRAPPING\OXTIMESTAMP;
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


