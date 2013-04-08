<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.oxid-esales.com
 * @package   main
 * @copyright (C) OXID eSales AG 2003-2012
 * @version OXID eShop EE
 */

    /** @name database information */
        $this->dbType = 'mysql';
        $this->dbHost = '<dbHost_ee>'; // database host name
        $this->dbName = '<dbName_ee>'; // database name
        $this->dbUser = '<dbUser_ee>'; // database user name
        $this->dbPwd  = '<dbPwd_ee>'; // database user password
        $this->sShopURL     = '<sShopURL_ee>'; // eShop base url, required
        $this->sSSLShopURL  = null;            // eShop SSL url, optional
        $this->sAdminSSLURL = null;            // eShop Admin SSL url, optional
        $this->sShopDir     = '<sShopDir_ee>';
        $this->sCompileDir  = '<sCompileDir_ee>';

    // UTF-8 mode in shop 0 - off, 1 - on
    $this->iUtfMode  = '<iUtfMode>';

    // File type whitelist for file upload
    $this->aAllowedUploadTypes = array('jpg', 'gif', 'png', 'pdf', 'mp3', 'avi', 'mpg', 'mpeg', 'doc', 'xls', 'ppt');

    // timezone information
    date_default_timezone_set('Europe/Berlin');

    // Search engine friendly URL processor
    // After changing this value, you should rename oxid.php file as well
    // Always leave .php extension here unless you know what you are doing
    $this->sOXIDPHP = "oxid.php";

    //  enable debug mode for template development or bugfixing
    // -1 = Logger Messages internal use only
    //  0 = off
    //  1 = smarty
    //  2 = SQL
    //  3 = SQL + smarty
    //  4 = SQL + smarty + shoptemplate data
    //  5 = Delivery Cost calculation info
    //  6 = SMTP Debug Messages
    //  7 = oxDbDebug SQL parser
    //  8 = display smarty template names (requires /tmp cleanup)
    $this->iDebug = 0;

    // Log all modifications performed in Admin
    $this->blLogChangesInAdmin = false;

    // Force admin email
    $this->sAdminEmail = '';

    // in case session must be started on first user page visit (not only on
    // session required action) set this option value 1
    $this->blForceSessionStart = false;

    // Use browser cookies to store session id (no sid parameter in URL)
    $this->blSessionUseCookies = true;

    // The domain that the cookie is available: array( _SHOP_ID_ => _DOMAIN_ );
    // check setcookie() documentation for more details @php.net
    $this->aCookieDomains = null;

    // The path on the server in which the cookie will be available on: array( _SHOP_ID_ => _PATH_ );
    // check setcookie() documentation for more details @php.net
    $this->aCookiePaths = null;

    // uncomment the following line if you want to leave euro sign unchanged in output
    // by default is set to convert euro sign symbol to html entity
    // $this->blSkipEuroReplace = true;

        //Time limit in ms to be notified about slow queries
        $this->iDebugSlowQueryTime = 20;

        // enables Rights and Roles engine
        // 0 - off,
        // 1 - only in admin,
        // 2 - only in shop,
        // 3 - both
        $this->blUseRightsRoles = 3;


        //define oxarticles fields which could be edited individually in subshops
        //do not forget to add these fields to oxfield2shop table
        //note the field names are case sensitive here
        $this->aMultishopArticleFields = array("OXPRICE", "OXPRICEA", "OXPRICEB", "OXPRICEC", "OXUPDATEPRICE", "OXUPDATEPRICEA", "OXUPDATEPRICEB", "OXUPDATEPRICEC", "OXUPDATEPRICETIME");


        //Show "Update Views" button in admin
        $this->blShowUpdateViews = true;

        // If default 30 seconds is not enougth
        // @set_time_limit(3000);

    // List of all Search-Engine Robots
    $this->aRobots = array(
                        'googlebot',
                        'ultraseek',
                        'crawl',
                        'spider',
                        'fireball',
                        'robot',
                        'slurp',
                        'fast',
                        'altavista',
                        'teoma',
                        'msnbot',
                        'bingbot',
                        'yandex',
                        'gigabot',
                        'scrubby'
                        );

    // Deactivate Static URL's for these Robots
    $this->aRobotsExcept = array();

    // IP addresses for which session/cookie id match and user agent change checks are off
    $this->aTrustedIPs = array();

    /**
     * Works only if basket reservations feature is enabled in admin.
     *
     * The number specifies how many expired basket reservations are
     * cleaned per one request (to the eShop).
     * Cleaning a reservation basically means returning the reserved
     * stock to the articles.
     *
     * Keeping this number too low may cause article stock being returned too
     * slowly, while too high value may have spiking impact on the performance.
     */
    $this->iBasketReservationCleanPerRequest = 200;

    // Trusted shops buyer protection wsdl url
    $this->sTsProtectionUrl = "https://protection.trustedshops.com/ts/protectionservices/ApplicationRequestService?wsdl";
    // This is only needed for testing during integration
    $this->sTsTestProtectionUrl = "https://protection-qa.trustedshops.com/ts/protectionservices/ApplicationRequestService?wsdl";

    // Trusted Shops Ratings login info
    // Do not change credentials unless instructed otherwise by Trusted Shops!
    $this->sTsUser = "oxid_esales";
    $this->sTsPass = "V1AoGEXm";

    // Trusted Shops Ratings configuration array
    $this->aTsConfig = array( "blTestMode"   => false, // set TRUE to enable testing mode
                              "sTsUrl"       => "https://www.trustedshops.com", // Trusted Shops Rating main url
                              "sTsTestUrl"   => "https://qa.trustedshops.com",  // Trusted Shops Rating test url
                              "sTsWidgetUri" => array( "bewertung/widget/widgets/%s.gif" ), // rating widget url
                              "sTsInfoUri"   => array( "de" => "bewertung/info_%s.html",  // DE rating info url
                                                       "en" => "buyerrating/info_%s.html" // EN rating info url
                                                     ),
                              "sTsRatingUri" => array( "de" => "bewertung/bewerten_%s.html", // DE rating url
                                                       "en" => "buyerrating/rate_%s.html"    // EN rating url
                                                     )
                             );
    // Trusted Shops Ratings service wsdl
    $this->sTsServiceWsdl = "https://www.trustedshops.de/ts/services/TsRating?wsdl";

    // Trusted Shops Ratings test service wsdl
    $this->sTsServiceTestWsdl = "https://qa.trustedshops.de/ts/services/TsRating?wsdl";

    /**
     * should template blocks be highlighted in frontend ?
     * this is mainly intended for module writers in non productive environment
     */
    $this->blDebugTemplateBlocks = false;

    /**
     * should requests, coming via stdurl and not redirected to seo url be logged to seologs db table?
     * note: only active if in productive mode, as the eShop in non productive more will always log such urls
     */
    $this->blSeoLogging = false;

    /**
     * To override oxubase::_aUserComponentNames use this array option:
     * array keys are component(class) names and array values defines if component is cacheable (true/false)
     * e.g. array("user_class" => false);
     */
    $this->aUserComponentNames = null;


    /**
     * Default database conection character set
     */
    $this->sDefaultDatabaseConnection = '';

    /**
     * Additional multi language tables
     */
    $this->aMultiLangTables = null;

    /**
     * Instructs shop that price update is perfomed by cron (time based job sheduler)
     */
    $this->blUseCron = false;

    $this->aSlaveHosts = array('10.255.255.38', '10.255.255.39');
    $this->iMasterSlaveBalance = 0;
