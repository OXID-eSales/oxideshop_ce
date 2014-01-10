<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Views_oxviewConfigTest extends OxidTestCase
{
    protected $_aTsConfig = array( "blTestMode"   => false, // set TRUE to enable testing mode
                                   "sTsUrl"       => "https://www.trustedshops.com",
                                   "sTsTestUrl"   => "https://qa.trustedshops.com",
                                   "sTsWidgetUri" => array( "bewertung/widget/widgets/%s.gif" ),
                                   "sTsInfoUri"   => array( "de" => "bewertung/info_%s.html",
                                                            "en" => "buyerrating/info_%s.html"
                                             ),
                                   "sTsRatingUri" => array( "de" => "bewertung/bewerten_%s.html",
                                                            "en" => "buyerrating/rate_%s.html"
                                                          )
                                 );

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        overrideGetShopBasePath(null);


        parent::tearDown();
    }

    /**
     * oxViewconfig::getTsId() test case
     *
     * @return null
     */
    public function testGetTsId()
    {
        $sLangId = oxLang::getInstance()->getLanguageAbbr();

        modConfig::getInstance()->setConfigParam( "aTsLangIds", array( $sLangId => 123 ) );
        modConfig::getInstance()->setConfigParam( "aTsActiveLangIds", array( $sLangId => 123 ) );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( 123, $oViewConf->getTsId() );
    }

    /**
     * oxViewconfig::showTs() test case
     *
     * @return null
     */
    public function testShowTs()
    {
        modConfig::getInstance()->setConfigParam( "blTsWidget", false );
        modConfig::getInstance()->setConfigParam( "blTsThankyouReview", false );
        modConfig::getInstance()->setConfigParam( "blTsOrderEmailReview", false );
        modConfig::getInstance()->setConfigParam( "blTsOrderSendEmailReview", false );

        $oViewConf = new oxViewConfig();
        $this->assertFalse( $oViewConf->showTs( "WIDGET" ) );
        $this->assertFalse( $oViewConf->showTs( "THANKYOU" ) );
        $this->assertFalse( $oViewConf->showTs( "ORDEREMAIL" ) );
        $this->assertFalse( $oViewConf->showTs( "ORDERCONFEMAIL" ) );

        modConfig::getInstance()->setConfigParam( "blTsWidget", true );
        modConfig::getInstance()->setConfigParam( "blTsThankyouReview", true );
        modConfig::getInstance()->setConfigParam( "blTsOrderEmailReview", true );
        modConfig::getInstance()->setConfigParam( "blTsOrderSendEmailReview", true );

        $this->assertTrue( $oViewConf->showTs( "WIDGET" ) );
        $this->assertTrue( $oViewConf->showTs( "THANKYOU" ) );
        $this->assertTrue( $oViewConf->showTs( "ORDEREMAIL" ) );
        $this->assertTrue( $oViewConf->showTs( "ORDERCONFEMAIL" ) );
    }

    /**
     * oxViewconfig::getTsRatingUrl() test case
     *
     * @return null
     */
    public function testGetTsRatingUrl()
    {
        modConfig::getInstance()->setConfigParam( "aTsConfig", $this->_aTsConfig );
        $sLangId = oxLang::getInstance()->getLanguageAbbr();
        $sTsInfoUri = ( isset( $this->_aTsConfig["sTsRatingUri"] ) && isset( $this->_aTsConfig["sTsRatingUri"][$sLangId] ) ) ? $this->_aTsConfig["sTsRatingUri"][$sLangId] : false;

        $oViewConf = $this->getMock( "oxViewConfig", array( "getTsId" ) );
        $oViewConf->expects( $this->once() )->method( "getTsId" )->will( $this->returnValue( "xyz" ) );
        $this->assertEquals( "https://www.trustedshops.com/".sprintf( $sTsInfoUri, "xyz" ), $oViewConf->getTsRatingUrl() );
    }

    /**
     * oxViewconfig::getTsInfoUrl() test case
     *
     * @return null
     */
    public function testGetTsInfoUrl()
    {
        modConfig::getInstance()->setConfigParam( "aTsConfig", $this->_aTsConfig );
        $sLangId = oxLang::getInstance()->getLanguageAbbr();
        $sTsInfoUri = ( isset( $this->_aTsConfig["sTsInfoUri"] ) && isset( $this->_aTsConfig["sTsInfoUri"][$sLangId] ) ) ? $this->_aTsConfig["sTsInfoUri"][$sLangId] : false;

        $oViewConf = $this->getMock( "oxViewConfig", array( "getTsId" ) );
        $oViewConf->expects( $this->once() )->method( "getTsId" )->will( $this->returnValue( "xyz" ) );
        $this->assertEquals( "https://www.trustedshops.com/".sprintf( $sTsInfoUri, "xyz" ), $oViewConf->getTsInfoUrl() );
    }

    /**
     * oxViewconfig::getTsWidgetUrl() test case
     *
     * @return null
     */
    public function testGetTsWidgetUrl()
    {
        modConfig::getInstance()->setConfigParam( "aTsConfig", $this->_aTsConfig );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getTsId" ) );
        $oViewConf->expects( $this->once() )->method( "getTsId" )->will( $this->returnValue( "xyz" ) );
        $this->assertEquals( "https://www.trustedshops.com/bewertung/widget/widgets/xyz.gif", $oViewConf->getTsWidgetUrl() );
    }

    /**
     * oxViewconfig::etTsDomain() test case
     *
     * @return null
     */
    public function testGetTsDomain()
    {
        modConfig::getInstance()->setConfigParam( "aTsConfig", $this->_aTsConfig );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( "https://www.trustedshops.com", $oViewConf->getTsDomain() );
    }

    /**
     * oxViewConfig::getHelpPageLink() test case
     *
     * @return null
     */
    public function testGetHelpPageLink()
    {
        $sShopUrl = oxConfig::getInstance()->getConfigParam( "sShopURL" );

        $oViewConfig = $this->getMock( "oxviewconfig", array( "getActiveClassName" ) );
        $oViewConfig->expects( $this->once() )->method( "getActiveClassName" )->will( $this->returnValue( "start" ) );
        $this->assertEquals( $sShopUrl . "Hilfe-Die-Startseite/", $oViewConfig->getHelpPageLink() );

        $oViewConfig = $this->getMock( "oxviewconfig", array( "getActiveClassName" ) );
        $oViewConfig->expects( $this->once() )->method( "getActiveClassName" )->will( $this->returnValue( "alist" ) );
        $this->assertEquals( $sShopUrl . "Hilfe-Die-Produktliste/", $oViewConfig->getHelpPageLink() );

        $oViewConfig = $this->getMock( "oxviewconfig", array( "getActiveClassName" ) );
        $oViewConfig->expects( $this->once() )->method( "getActiveClassName" )->will( $this->returnValue( "details" ) );
        $this->assertEquals( $sShopUrl . "Hilfe-Main/", $oViewConfig->getHelpPageLink() );
    }

    /**
     * oxViewConfig::getHelpPageLink() test case
     *
     * @return null
     */
    public function testGetHelpPageLinkInactiveContents()
    {
        modDB::getInstance()->addClassFunction( 'getOne', create_function('$x', 'return false;' ) );

        $oViewConfig = $this->getMock( "oxviewconfig", array( "getActiveClassName", "getHelpLink" ) );
        $oViewConfig->expects( $this->once() )->method( "getActiveClassName" )->will( $this->returnValue( "start" ) );
        $oViewConfig->expects( $this->once() )->method( "getHelpLink" );
        $oViewConfig->getHelpPageLink();
    }

    public function testGetHomeLinkEng()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '".oxConfig::getInstance()->getShopUrl()."'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction( "oxLang", "getBaseLanguage", "{return 1;}" );

        $oViewConfig = new oxviewconfig();
        $this->assertEquals( oxConfig::getInstance()->getShopUrl().'en/home/', $oViewConfig->getHomeLink() );
    }

    public function testGetHomeLink_defaultLanguageEn()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '".oxConfig::getInstance()->getShopUrl()."'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction( "oxLang", "getBaseLanguage", "{return 1;}" );
        modConfig::getInstance()->setConfigParam( "sDefaultLang", 1 );

        $oViewConfig = new oxviewconfig();
        $this->assertEquals( oxConfig::getInstance()->getShopUrl(), $oViewConfig->getHomeLink() );
    }

    public function testGetHomeLinkPe()
    {

        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '".oxConfig::getInstance()->getShopUrl()."'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $oViewConfig = new oxviewconfig();
        $this->assertEquals( oxConfig::getInstance()->getShopURL(), $oViewConfig->getHomeLink() );
    }



    // just testing if fields are present ..
    public function testIfAllDefaultDataIsSet()
    {
        modConfig::setParameter( 'listtype', null );

        $myConfig = oxConfig::getInstance();
        $myConfig->setActiveView(null);
        $aParams = array();
        $aParams['sid'] = oxSession::getInstance()->getId();
        $sLang = oxLang::getInstance()->getFormLang();
        //$aParams['hiddensid']    = oxSession::getInstance()->hiddenSid().( ( $sLang ) ? "\n{$sLang}" : "" );
        //$aParams['selflink']     = $myConfig->getShopHomeURL();
        //$aParams['sslselflink']  = $myConfig->getShopSecureHomeURL();
        //$aParams['basedir']      = $myConfig->getShopURL();
        //$aParams['coreutilsdir'] = $myConfig->getCoreUtilsURL();
        /*
        $aParams['selfactionlink'] = $myConfig->getShopCurrentURL();
        $aParams['currenthomedir'] = $myConfig->getCurrentShopURL();
        $aParams['basketlink']     = $myConfig->getShopHomeURL() . 'cl=basket';
        $aParams['orderlink']      = $myConfig->getShopSecureHomeUrl() . 'cl=user';
        $aParams['paymentlink']    = $myConfig->getShopSecureHomeUrl() . 'cl=payment';
        $aParams['exeorderlink']   = $myConfig->getShopSecureHomeUrl() . 'cl=order&amp;fnc=execute';
        $aParams['orderconfirmlink'] = $myConfig->getShopSecureHomeUrl() . 'cl=order';
        $aParams['basetpldir']       = $myConfig->getBaseTemplateDir( false );
        $aParams['templatedir']      = $myConfig->getTemplateDir( false );
        $aParams['urltemplatedir'] = $myConfig->getTemplateUrl( false );
        $aParams['imagedir']       = $myConfig->getImageUrl();
        $aParams['nossl_imagedir'] = $aParams['nosslimagedir'] = $myConfig->getNoSSLImageDir( false );
        $aParams['dimagedir']      = $myConfig->getDynImageDir();
        $aParams['admindir']       = $myConfig->getConfigParam( 'sAdminDir' );
        $aParams['id']             = $myConfig->getShopId();
        $aParams['isssl']          = $myConfig->isSsl();
        $aParams['ip']             = oxUtilsServer::getInstance()->getRemoteAddress();
        $aParams['popupident']     = md5( $myConfig->getShopURL() );
        $aParams['artperpageform'] = $myConfig->getShopCurrentURL();
        $aParams['buyableparent']  = $aParams['isbuyableparent'] = $myConfig->getConfigParam( 'blVariantParentBuyable' );
        $aParams['blshowbirthdayfields'] = $myConfig->getConfigParam( 'blShowBirthdayFields' );
        $aParams['blshowfinalstep']   = $myConfig->getConfigParam( 'blShowFinalStep' );
        $aParams['anrofcatarticles']  = $myConfig->getConfigParam( 'aNrofCatArticles' );
        $aParams['blautosearchoncat'] = $myConfig->getConfigParam( 'blAutoSearchOnCat' );
        $aParams['cnid'] = $aParams['actcatid'] = null;
        $aParams['cl']   = oxConfig::getInstance()->getActiveView()->getClassName();
        $aParams['tpl']  = null;
        $aParams['lang'] = oxLang::getInstance()->getBaseLanguage();
        $aParams['helplink']   = $myConfig->getShopCurrentURL()."cl=help&amp;page=";
        $aParams['logoutlink'] = $myConfig->getShopHomeURL()."cl=".oxConfig::getInstance()->getActiveView()->getClassName()."&amp;fnc=logout&amp;redirect=1";
        $aParams['iartPerPage']   = '';
        $sListType = oxConfig::getInstance()->getGlobalParameter( 'listtype' );
        $aParams['navurlparams']  = $sListType ? "&amp;listtype=$sListType" : '';
        $aParams['navformparams'] = $sListType ? "<input type=\"hidden\" name=\"listtype\" value=\"$sListType\">\n" : '';
        $aParams['blstockondefaultmessage']  = oxConfig::getInstance()->getConfigParam( 'blStockOnDefaultMessage' );
        $aParams['blstockoffdefaultmessage'] = oxConfig::getInstance()->getConfigParam( 'blStockOffDefaultMessage' );
        $aParams['sShopVersion'] = '';
        $aParams['ajaxlink']     = '';
        $aParams['ismultishop']  = false;
        $aParams['sServiceUrl']  = '';


        $oViewConf = new oxViewConfig();
        foreach ( $aParams as $sVarName => $sVarValue ) {
            $sFncName = "get$sVarName";
            $sResult  = $oViewConf->$sFncName();
            $this->assertEquals( $sVarValue, $sResult, "'$sVarName' does not match ($sVarValue != $sResult)" );
        }
        */
    }

    /**
     * check config params getter
     */
    public function testGetShowWishlist()
    {
        $oCfg = $this->getMock('oxconfig', array('getConfigParam'));
        $oCfg->expects($this->once())
             ->method('getConfigParam')
             ->with($this->equalTo('bl_showWishlist'))
             ->will($this->returnValue('lalala'));
        $oVC = $this->getMock('oxviewconfig', array('getConfig'));
        $oVC->expects($this->once())
             ->method('getConfig')
             ->will($this->returnValue($oCfg));
        $this->assertEquals('lalala', $oVC->getShowWishlist());
    }

    /**
     * check config params getter
     */
    public function testGetShowCompareList()
    {
        $oView = $this->getMock( 'oxview', array( 'getIsOrderStep' ) );
        $oView->expects( $this->once() )->method( 'getIsOrderStep' )->will( $this->returnValue( true ) );

        $oCfg = $this->getMock( 'oxconfig', array( 'getConfigParam', 'getActiveView' ) );
        $oCfg->expects( $this->at( 0 ) )->method( 'getConfigParam' )->with( $this->equalTo( 'bl_showCompareList' ) )->will( $this->returnValue( true ) );
        $oCfg->expects( $this->at( 1 ) )->method( 'getConfigParam' )->with( $this->equalTo( 'blDisableNavBars' ) )->will( $this->returnValue( true ) );
        $oCfg->expects( $this->at( 2 ) )->method( 'getActiveView' )->will( $this->returnValue( $oView ) );

        $oVC = $this->getMock('oxviewconfig', array('getConfig'));
        $oVC->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $this->assertFalse( $oVC->getShowCompareList() );
    }

    /**
     * check config params getter
     */
    public function testGetShowListmania()
    {
        $oCfg = $this->getMock('oxconfig', array('getConfigParam'));
        $oCfg->expects($this->once())
             ->method('getConfigParam')
             ->with($this->equalTo('bl_showListmania'))
             ->will($this->returnValue('lalala'));
        $oVC = $this->getMock('oxviewconfig', array('getConfig'));
        $oVC->expects($this->once())
             ->method('getConfig')
             ->will($this->returnValue($oCfg));
        $this->assertEquals('lalala', $oVC->getShowListmania());
    }
    /**
     * check config params getter
     */
    public function testGetShowVouchers()
    {
        $oCfg = $this->getMock('oxconfig', array('getConfigParam'));
        $oCfg->expects($this->once())
             ->method('getConfigParam')
             ->with($this->equalTo('bl_showVouchers'))
             ->will($this->returnValue('lalala'));
        $oVC = $this->getMock('oxviewconfig', array('getConfig'));
        $oVC->expects($this->once())
             ->method('getConfig')
             ->will($this->returnValue($oCfg));
        $this->assertEquals('lalala', $oVC->getShowVouchers());
    }

    /**
     * check config params getter
     */
    public function testGetShowGiftWrapping()
    {
        $oCfg = $this->getMock('oxconfig', array('getConfigParam'));
        $oCfg->expects($this->once())
             ->method('getConfigParam')
             ->with($this->equalTo('bl_showGiftWrapping'))
             ->will($this->returnValue('lalala'));
        $oVC = $this->getMock('oxviewconfig', array('getConfig'));
        $oVC->expects($this->once())
             ->method('getConfig')
             ->will($this->returnValue($oCfg));
        $this->assertEquals('lalala', $oVC->getShowGiftWrapping());
    }

    public function testGetRemoteAccessToken()
    {
        $oSubj = new oxViewConfig();
        $sTestToken1 = $oSubj->getRemoteAccessToken();
        $sTestToken2 = $oSubj->getRemoteAccessToken();

        $this->assertEquals($sTestToken1, $sTestToken2);
        $this->assertEquals(8, strlen($sTestToken1));
    }

    public function testGetLogoutLink()
    {
        $oCfg = $this->getMock('oxconfig', array('getShopHomeURL', 'isSsl'));
        $oCfg->expects($this->once())
             ->method('getShopHomeURL')
             ->will($this->returnValue('shopHomeUrl/'));
        $oCfg->expects($this->once())
             ->method('isSsl')
             ->will($this->returnValue(false));

        $oVC = $this->getMock('oxviewconfig'
            , array('getConfig', 'getActionClassName', 'getActCatId', 'getActTplName'
            , 'getActArticleId', 'getActSearchParam', 'getActSearchTag', 'getActListType'
            , 'getActRecommendationId' ));

        $oVC->expects($this->once())
             ->method('getConfig')
             ->will($this->returnValue($oCfg));
        $oVC->expects($this->once())
             ->method('getActionClassName')
             ->will($this->returnValue('actionclass'));
        $oVC->expects($this->once())
             ->method('getActCatId')
             ->will($this->returnValue('catid'));
        $oVC->expects($this->once())
             ->method('getActTplName')
             ->will($this->returnValue('tpl'));
        $oVC->expects($this->once())
             ->method('getActArticleId')
             ->will($this->returnValue('anid'));
        $oVC->expects($this->once())
             ->method('getActSearchParam')
             ->will($this->returnValue('searchparam'));
        $oVC->expects($this->once())
             ->method('getActSearchTag')
             ->will($this->returnValue('searchtag'));
        $oVC->expects($this->once())
             ->method('getActListType')
             ->will($this->returnValue('listtype'));
        $oVC->expects($this->once())
        ->method('getActRecommendationId')
        ->will($this->returnValue('recommid'));

        $this->assertEquals('shopHomeUrl/cl=actionclass&amp;cnid=catid&amp;anid=anid&amp;searchparam=searchparam&amp;searchtag=searchtag&amp;recommid=recommid&amp;listtype=listtype&amp;fnc=logout&amp;tpl=tpl&amp;redirect=1', $oVC->getLogoutLink());
    }

    /**
     * Tests forming of logout link when in ssl page
     *
     * @return null
     */
    public function testGetLogoutLinkSsl()
    {
        $oCfg = $this->getMock('oxconfig', array('getShopSecureHomeUrl', 'isSsl'));
        $oCfg->expects($this->once())
             ->method('getShopSecureHomeUrl')
             ->will($this->returnValue('sslShopHomeUrl/'));
        $oCfg->expects($this->once())
             ->method('isSsl')
             ->will($this->returnValue(true));

        $oVC = $this->getMock('oxviewconfig'
            , array('getConfig', 'getActionClassName', 'getActCatId', 'getActTplName'
            , 'getActArticleId', 'getActSearchParam', 'getActSearchTag', 'getActListType'
            , 'getActRecommendationId' ));

        $oVC->expects($this->once())
             ->method('getConfig')
             ->will($this->returnValue($oCfg));
        $oVC->expects($this->once())
             ->method('getActionClassName')
             ->will($this->returnValue('actionclass'));
        $oVC->expects($this->once())
             ->method('getActCatId')
             ->will($this->returnValue('catid'));
        $oVC->expects($this->once())
             ->method('getActTplName')
             ->will($this->returnValue('tpl'));
        $oVC->expects($this->once())
             ->method('getActArticleId')
             ->will($this->returnValue('anid'));
        $oVC->expects($this->once())
             ->method('getActSearchParam')
             ->will($this->returnValue('searchparam'));
        $oVC->expects($this->once())
             ->method('getActSearchTag')
             ->will($this->returnValue('searchtag'));
        $oVC->expects($this->once())
             ->method('getActListType')
             ->will($this->returnValue('listtype'));
        $oVC->expects($this->once())
        ->method('getActRecommendationId')
        ->will($this->returnValue('recommid'));

        $this->assertEquals('sslShopHomeUrl/cl=actionclass&amp;cnid=catid&amp;anid=anid&amp;searchparam=searchparam&amp;searchtag=searchtag&amp;recommid=recommid&amp;listtype=listtype&amp;fnc=logout&amp;tpl=tpl&amp;redirect=1', $oVC->getLogoutLink());
    }

    /**
     * check config params getter
     */
    public function testGetActionClassName()
    {
        $oV = $this->getMock('oxview', array('getActionClassName'));
        $oV->expects($this->once())
             ->method('getActionClassName')
             ->will($this->returnValue('lalala'));
        $oCfg = $this->getMock('oxconfig', array('getActiveView'));
        $oCfg->expects($this->once())
             ->method('getActiveView')
             ->will($this->returnValue($oV));
        $oVC = $this->getMock('oxviewconfig', array('getConfig'));
        $oVC->expects($this->once())
             ->method('getConfig')
             ->will($this->returnValue($oCfg));
        $this->assertEquals('lalala', $oVC->getActionClassName());
    }

    public function testGetShowBasketTimeoutWhenFunctionalityIsOnAndTimeLeft()
    {
        modConfig::getInstance()->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', array('getTimeLeft'));
        $oR->expects($this->once())->method('getTimeLeft')->will($this->returnValue(5));

        $oS = $this->getMock('oxsession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));

        $oVC = $this->getMock('oxViewConfig', array('getSession'));
        $oVC->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $this->assertEquals(true, $oVC->getShowBasketTimeout());
    }
    public function testGetShowBasketTimeoutWhenFunctionalityIsOnAndTimeExpired()
    {
        modConfig::getInstance()->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', array('getTimeLeft'));
        $oR->expects($this->once())->method('getTimeLeft')->will($this->returnValue(0));

        $oS = $this->getMock('oxsession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));

        $oVC = $this->getMock('oxViewConfig', array('getSession'));
        $oVC->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $this->assertEquals(false, $oVC->getShowBasketTimeout());
    }
    public function testGetShowBasketTimeoutWhenFunctionalityIsOff()
    {
        modConfig::getInstance()->setConfigParam('blPsBasketReservationEnabled', false);

        $oVC = $this->getMock('oxViewConfig', array('getSession'));
        $oVC->expects($this->never())->method('getSession');

        $this->assertEquals(false, $oVC->getShowBasketTimeout());
    }

    public function testGetBasketTimeLeft()
    {
        $oR = $this->getMock('stdclass', array('getTimeLeft'));
        $oR->expects($this->once())->method('getTimeLeft')->will($this->returnValue(954));

        $oS = $this->getMock('oxsession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));

        $oVC = $this->getMock('oxViewConfig', array('getSession'));
        $oVC->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $this->assertEquals(954, $oVC->getBasketTimeLeft());
        // return cached
        $this->assertEquals(954, $oVC->getBasketTimeLeft());
    }

    /**
     * test method
     *
     * return null
     */
    public function testIsTplBlocksDebugMode()
    {
        $myConfig = modConfig::getInstance();

        $oViewCfg = $this->getMock( 'oxViewConfig', array( 'getConfig' ) );
        $oViewCfg->expects( $this->any() )->method( 'getConfig')->will( $this->returnValue( $myConfig ) );

        $myConfig->setConfigParam( "blDebugTemplateBlocks", false );
        $this->assertFalse( $oViewCfg->isTplBlocksDebugMode() );
        $myConfig->setConfigParam( "blDebugTemplateBlocks", true );
        $this->assertTrue ( $oViewCfg->isTplBlocksDebugMode() );
    }

    /**
     * test method "getNrOfCatArticles()"
     *
     * return null
     */
    public function testGetNrOfCatArticles()
    {
        $aNrofCatArticlesInGrid = array(1,2,3);
        $aNrofCatArticles = array(4,5,6);

        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam( "aNrofCatArticlesInGrid", $aNrofCatArticlesInGrid );
        $myConfig->setConfigParam( "aNrofCatArticles", $aNrofCatArticles );

        $oViewCfg = $this->getMock( 'oxViewConfig', array( 'getConfig' ) );
        $oViewCfg->expects( $this->any() )->method( 'getConfig')->will( $this->returnValue( $myConfig ) );

        $oSession = modSession::getInstance();

        $oSession->setVar( "ldtype", "grid" );
        $this->assertEquals( $aNrofCatArticlesInGrid, $oViewCfg->getNrOfCatArticles() );

        $oSession->setVar( "ldtype", "line" );
        $this->assertEquals( $aNrofCatArticles, $oViewCfg->getNrOfCatArticles() );

        $oSession->setVar( "ldtype", "infogrid" );
        $this->assertEquals( $aNrofCatArticles, $oViewCfg->getNrOfCatArticles() );
    }

    /**
     * Testing oxViewConfig::getCountryList()
     *
     * @return null
     */
    public function testGetCountryList()
    {
        $oView = new oxViewConfig();
        $this->assertTrue( $oView->getCountryList() instanceof oxcountrylist );
    }

    public function testGetModulePath()
    {
        $sMdir = realpath((dirname(__FILE__).'/../moduleTestBlock'));

        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam( "sShopDir", $sMdir."/" );

        $oVC = $this->getMock( 'oxViewConfig', array( 'getConfig' ) );
        $oVC->expects( $this->any() )->method( 'getConfig')->will( $this->returnValue( $myConfig ) );

        $this->assertEquals($sMdir."/modules/test1/out", $oVC->getModulePath('test1', 'out'));
        $this->assertEquals($sMdir."/modules/test1/out/", $oVC->getModulePath('test1', '/out/'));

        $this->assertEquals($sMdir."/modules/test1/out/blocks/test2.tpl", $oVC->getModulePath('test1', 'out/blocks/test2.tpl'));
        $this->assertEquals($sMdir."/modules/test1/out/blocks/test2.tpl", $oVC->getModulePath('test1', '/out/blocks/test2.tpl'));

        // check exception throwing
        try {
            $oVC->getModulePath('test1', '/out/blocks/test1.tpl');
            $this->fail("should have thrown");
        } catch (oxFileException $e) {
            $this->assertEquals("Requested file not found for module test1 (".$sMdir."/modules/test1/out/blocks/test1.tpl)", $e->getMessage());
        }
    }

    public function testGetModuleUrl()
    {
        $sBaseUrl  = oxConfig::getInstance()->getCurrentShopUrl();
        $sMdir = realpath((dirname(__FILE__).'/../moduleTestBlock'));

        $myConfig = modConfig::getInstance();
        $myConfig->setConfigParam( "sShopDir", $sMdir."/" );

        $oVC = $this->getMock( 'oxViewConfig', array( 'getConfig' ) );
        $oVC->expects( $this->any() )->method( 'getConfig')->will( $this->returnValue( $myConfig ) );

        $this->assertEquals("{$sBaseUrl}modules/test1/out", $oVC->getModuleUrl('test1', 'out'));
        $this->assertEquals("{$sBaseUrl}modules/test1/out/", $oVC->getModuleUrl('test1', '/out/'));

        $this->assertEquals("{$sBaseUrl}modules/test1/out/blocks/test2.tpl", $oVC->getModuleUrl('test1', 'out/blocks/test2.tpl'));
        $this->assertEquals("{$sBaseUrl}modules/test1/out/blocks/test2.tpl", $oVC->getModuleUrl('test1', '/out/blocks/test2.tpl'));

        // check exception throwing
        try {
            $oVC->getModuleUrl('test1', '/out/blocks/test1.tpl');
            $this->fail("should have thrown");
        } catch (oxFileException $e) {
            $sBaseUrl  = oxConfig::getInstance()->getConfigParam('sShopDir');
            $this->assertEquals("Requested file not found for module test1 (".$sMdir."/modules/test1/out/blocks/test1.tpl)", $e->getMessage());
        }
    }

    public function testViewThemeParam()
    {
        $oVC = new oxViewConfig();

        $oV = $this->getMock('oxConfig', array('isThemeOption'));
        $oV->expects($this->any())->method('getSession')->will($this->returnValue(false));

        $this->assertEquals(false, $oVC->getViewThemeParam('aaa'));

        $oV = $this->getMock('oxConfig', array('isThemeOption'));
        $oV->expects($this->any())->method('getSession')->will($this->returnValue(true));

        modConfig::getInstance()->setConfigParam('bl_showListmania', 1);
        $this->assertEquals(1, $oVC->getViewThemeParam('bl_showListmania'));

        modConfig::getInstance()->setConfigParam('bl_showListmania', 0);
        $this->assertEquals(0, $oVC->getViewThemeParam('bl_showListmania'));
    }

    /**
     * Test case for oxViewConfig::showSelectLists()
     *
     * @return null
     */
    public function testShowSelectLists()
    {
        $blExp = (bool) oxConfig::getINstance()->getConfigParam( 'bl_perfLoadSelectLists' );
        $oVC = new oxViewConfig();
        $this->assertEquals( $blExp, $oVC->showSelectLists() );
    }

    /**
     * Test case for oxViewConfig::showSelectListsInList()
     *
     * @return null
     */
    public function testShowSelectListsInList()
    {   
        modConfig::getInstance()->setConfigParam('bl_perfLoadSelectListsInAList', true);
        
        $oVC = $this->getMock('oxviewconfig', array( 'showSelectLists' ));
        $oVC->expects( $this->once() )->method( 'showSelectLists' )->will( $this->returnValue( true ) );
        $this->assertTrue( $oVC->showSelectListsInList() );
    }
    
    /**
     * Test case for oxViewConfig::showSelectListsInList()
     *
     * @return null
     */
    public function testShowSelectListsInListFalse()
    {   
        $oCfg = new oxConfig();
        $oVC = $this->getMock('oxviewconfig', array( 'showSelectLists' ));
        $oVC->expects( $this->once() )->method( 'showSelectLists' )->will( $this->returnValue( false ) );
        $this->assertFalse( $oVC->showSelectListsInList() );
    }    
    
    /**
     * Test case for oxViewConfig::showSelectListsInList()
     *
     * @return null
     */
    public function testShowSelectListsInListDifferent()
    {
        modConfig::getInstance()->setConfigParam('bl_perfLoadSelectListsInAList', false);
        
        $oVC = $this->getMock('oxviewconfig', array( 'showSelectLists' ));
        $oVC->expects( $this->once() )->method( 'showSelectLists' )->will( $this->returnValue( true ) );
        $this->assertFalse( $oVC->showSelectListsInList() );
    }

    /**
     * oxViewconfig::getImageUrl() test case
     *
     * @return null
     */
    public function testGetImageUrl()
    {
        $oViewConf = $this->getMock( "oxConfig", array( "getImageUrl" ) );
        $oViewConf->expects( $this->once() )->method( "getImageUrl" )->will( $this->returnValue( "shopUrl/out/theme/img/imgFile" ) );
        $this->assertEquals( "shopUrl/out/theme/img/imgFile", $oViewConf->getImageUrl('imgFile') );

        $oViewConf = $this->getMock( "oxConfig", array( "getImageUrl" ) );
        $oViewConf->expects( $this->once() )->method( "getImageUrl" )->will( $this->returnValue( "shopUrl/out/theme/img/" ) );
        $this->assertEquals( "shopUrl/out/theme/img/", $oViewConf->getImageUrl() );
    }

    /**
     * Checks if shop licenze is in staging mode
     */
    public function testHasDemoKey()
    {
            return;

        $oConfig = $this->getMock( "oxConfig", array( "hasDemoKey" ) );
        $oConfig->expects( $this->once() )->method( "hasDemoKey" )->will( $this->returnValue( true ) );

        $oViewConfig = $this->getMock( 'oxViewConfig', array('getConfig') );
        $oViewConfig->expects($this->any())->method('getConfig')->will( $this->returnValue( $oConfig ) );

        $this->assertTrue( $oViewConfig->hasDemoKey() );
    }


    /**
     * Testing getSelfLink()
     */
    public function testGetSelfLink()
    {
        $oConfig = $this->getMock( "oxConfig", array( "getShopHomeURL" ) );
        $oConfig->expects( $this->once() )->method( "getShopHomeURL" )->will( $this->returnValue( "testShopUrl" ) );

        $oViewConfig = $this->getMock( 'oxViewConfig', array('getConfig') );
        $oViewConfig->expects($this->any())->method('getConfig')->will( $this->returnValue( $oConfig ) );

        $this->assertEquals( "testShopUrl", $oViewConfig->getSelfLink() );
    }

    /**
     * Testing getSslSelfLink()
     */
    public function testGetSslSelfLink()
    {
        $oConfig = $this->getMock( "oxConfig", array( "getShopSecureHomeURL" ) );
        $oConfig->expects( $this->once() )->method( "getShopSecureHomeURL" )->will( $this->returnValue( "testSecureShopUrl" ) );

        $oViewConfig = $this->getMock( 'oxViewConfig', array('getConfig') );
        $oViewConfig->expects($this->any())->method('getConfig')->will( $this->returnValue( $oConfig ) );

        $this->assertEquals( "testSecureShopUrl", $oViewConfig->getSslSelfLink() );
    }

    /**
     * Testing getSslSelfLink() - admin mode
     */
    public function testGetSslSelfLink_adminMode()
    {
        $oConfig = $this->getMock( "oxConfig", array( "getShopSecureHomeURL" ) );
        $oConfig->expects( $this->never() )->method( "getShopSecureHomeURL" );

        $oViewConfig = $this->getMock( 'oxViewConfig', array('getConfig', 'isAdmin', 'getSelfLink') );
        $oViewConfig->expects( $this->any() )->method( 'getConfig' )->will( $this->returnValue( $oConfig ) );
        $oViewConfig->expects( $this->any() )->method( 'isAdmin' )->will( $this->returnValue( true ) );
        $oViewConfig->expects( $this->once() )->method( "getSelfLink" )->will( $this->returnValue("testShopUrl") );

        $this->assertEquals( "testShopUrl", $oViewConfig->getSslSelfLink() );
    }

    /**
     * Testing isAltImageServerConfigured() - nothing configured
     */
    public function testIsAltImageServerConfigured_none()
    {
        modConfig::getInstance()->setConfigParam('sAltImageUrl', '');
        modConfig::getInstance()->setConfigParam('sAltImageDir', '');
        modConfig::getInstance()->setConfigParam('sSSLAltImageUrl', '');
        modConfig::getInstance()->setConfigParam('sSSLAltImageDir', '');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertFalse( $oViewConfig->isAltImageServerConfigured() );
    }

    /**
     * Testing isAltImageServerConfigured() - http url configured
     */
    public function testIsAltImageServerConfigured_httpurl()
    {
        modConfig::getInstance()->setConfigParam('sAltImageUrl', 'http://img.oxid-esales.com');
        modConfig::getInstance()->setConfigParam('sAltImageDir', '');
        modConfig::getInstance()->setConfigParam('sSSLAltImageUrl', '');
        modConfig::getInstance()->setConfigParam('sSSLAltImageDir', '');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertTrue( $oViewConfig->isAltImageServerConfigured() );
    }

    /**
     * Testing isAltImageServerConfigured() - http dir configured
     */
    public function testIsAltImageServerConfigured_httpdir()
    {
        modConfig::getInstance()->setConfigParam('sAltImageUrl', '');
        modConfig::getInstance()->setConfigParam('sAltImageDir', 'http://img.oxid-esales.com');
        modConfig::getInstance()->setConfigParam('sSSLAltImageUrl', '');
        modConfig::getInstance()->setConfigParam('sSSLAltImageDir', '');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertTrue( $oViewConfig->isAltImageServerConfigured() );
    }

    /**
     * Testing isAltImageServerConfigured() - https url configured
     */
    public function testIsAltImageServerConfigured_httpsurl()
    {
        modConfig::getInstance()->setConfigParam('sAltImageUrl', '');
        modConfig::getInstance()->setConfigParam('sAltImageDir', '');
        modConfig::getInstance()->setConfigParam('sSSLAltImageUrl', 'https://img.oxid-esales.com');
        modConfig::getInstance()->setConfigParam('sSSLAltImageDir', '');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertTrue( $oViewConfig->isAltImageServerConfigured() );
    }

    /**
     * Testing isAltImageServerConfigured() - https dir configured
     */
    public function testIsAltImageServerConfigured_httpsdir()
    {
        modConfig::getInstance()->setConfigParam('sAltImageUrl', '');
        modConfig::getInstance()->setConfigParam('sAltImageDir', '');
        modConfig::getInstance()->setConfigParam('sSSLAltImageUrl', '');
        modConfig::getInstance()->setConfigParam('sSSLAltImageDir', 'https://img.oxid-esales.com');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertTrue( $oViewConfig->isAltImageServerConfigured() );
    }
}
