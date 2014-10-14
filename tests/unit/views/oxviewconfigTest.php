<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
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

        $this->getConfig()->setConfigParam( "aTsLangIds", array( $sLangId => 123 ) );
        $this->getConfig()->setConfigParam( "aTsActiveLangIds", array( $sLangId => 123 ) );

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
        $this->getConfig()->setConfigParam( "blTsWidget", false );
        $this->getConfig()->setConfigParam( "blTsThankyouReview", false );
        $this->getConfig()->setConfigParam( "blTsOrderEmailReview", false );
        $this->getConfig()->setConfigParam( "blTsOrderSendEmailReview", false );

        $oViewConf = new oxViewConfig();
        $this->assertFalse( $oViewConf->showTs( "WIDGET" ) );
        $this->assertFalse( $oViewConf->showTs( "THANKYOU" ) );
        $this->assertFalse( $oViewConf->showTs( "ORDEREMAIL" ) );
        $this->assertFalse( $oViewConf->showTs( "ORDERCONFEMAIL" ) );

        $this->getConfig()->setConfigParam( "blTsWidget", true );
        $this->getConfig()->setConfigParam( "blTsThankyouReview", true );
        $this->getConfig()->setConfigParam( "blTsOrderEmailReview", true );
        $this->getConfig()->setConfigParam( "blTsOrderSendEmailReview", true );

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
        $this->getConfig()->setConfigParam( "aTsConfig", $this->_aTsConfig );
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
        $this->getConfig()->setConfigParam( "aTsConfig", $this->_aTsConfig );
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
        $this->getConfig()->setConfigParam( "aTsConfig", $this->_aTsConfig );

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
        $this->getConfig()->setConfigParam( "aTsConfig", $this->_aTsConfig );

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
        $sShopUrl = $this->getConfig()->getConfigParam( "sShopURL" );

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
        $oViewConfig = $this->getMock( "oxviewconfig", array( "getHelpLink", '_getHelpContentIdents' ) );
        $oViewConfig->expects( $this->once() )->method( "_getHelpContentIdents" )->will( $this->returnValue( array("none") ) );
        $oViewConfig->expects( $this->once() )->method( "getHelpLink" );
        $oViewConfig->getHelpPageLink();
    }

    public function testGetHomeLinkEng()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '".$this->getConfig()->getShopUrl()."'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction( "oxLang", "getBaseLanguage", "{return 1;}" );

        $oViewConfig = new oxviewconfig();
        $this->assertEquals( $this->getConfig()->getShopUrl().'en/home/', $oViewConfig->getHomeLink() );
    }

    public function testGetHomeLink_defaultLanguageEn()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '".$this->getConfig()->getShopUrl()."'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction( "oxLang", "getBaseLanguage", "{return 1;}" );
        $this->getConfig()->setConfigParam( "sDefaultLang", 1 );

        $oViewConfig = new oxviewconfig();
        $this->assertEquals( $this->getConfig()->getShopUrl(), $oViewConfig->getHomeLink() );
    }

    public function testGetHomeLinkPe()
    {

        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '".$this->getConfig()->getShopUrl()."'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $oViewConfig = new oxviewconfig();
        $this->assertEquals( $this->getConfig()->getShopURL(), $oViewConfig->getHomeLink() );
    }




    // just testing if fields are present ..
    public function testIfAllDefaultDataIsSet()
    {
        $this->setConfigParam( 'listtype', null );

        $myConfig = $this->getConfig();
        $myConfig->setActiveView(null);
        $aParams = array();
        $aParams['sid'] = $this->getSession()->getId();
        $sLang = oxLang::getInstance()->getFormLang();
        //$aParams['hiddensid']    = $this->getSession()->hiddenSid().( ( $sLang ) ? "\n{$sLang}" : "" );
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
        $aParams['anrofcatarticles']  = $myConfig->getConfigParam( 'aNrofCatArticles' );
        $aParams['blautosearchoncat'] = $myConfig->getConfigParam( 'blAutoSearchOnCat' );
        $aParams['cnid'] = $aParams['actcatid'] = null;
        $aParams['cl']   = $this->getConfig()->getActiveView()->getClassName();
        $aParams['tpl']  = null;
        $aParams['lang'] = oxLang::getInstance()->getBaseLanguage();
        $aParams['helplink']   = $myConfig->getShopCurrentURL()."cl=help&amp;page=";
        $aParams['logoutlink'] = $myConfig->getShopHomeURL()."cl=".$this->getConfig()->getActiveView()->getClassName()."&amp;fnc=logout&amp;redirect=1";
        $aParams['iartPerPage']   = '';
        $sListType = $this->getConfig()->getGlobalParameter( 'listtype' );
        $aParams['navurlparams']  = $sListType ? "&amp;listtype=$sListType" : '';
        $aParams['navformparams'] = $sListType ? "<input type=\"hidden\" name=\"listtype\" value=\"$sListType\">\n" : '';
        $aParams['blstockondefaultmessage']  = $this->getConfig()->getConfigParam( 'blStockOnDefaultMessage' );
        $aParams['blstockoffdefaultmessage'] = $this->getConfig()->getConfigParam( 'blStockOffDefaultMessage' );
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
            , array('getConfig', 'getTopActionClassName', 'getActCatId', 'getActTplName', 'getActContentLoadId'
            , 'getActArticleId', 'getActSearchParam', 'getActSearchTag', 'getActListType', 'getActRecommendationId'));

        $oVC->expects($this->any())
             ->method('getConfig')
             ->will($this->returnValue($oCfg));
        $oVC->expects($this->once())
             ->method('getTopActionClassName')
             ->will($this->returnValue('actionclass'));
        $oVC->expects($this->once())
             ->method('getActCatId')
             ->will($this->returnValue('catid'));
        $oVC->expects($this->once())
             ->method('getActTplName')
             ->will($this->returnValue('tpl'));
        $oVC->expects($this->once())
             ->method('getActContentLoadId')
             ->will($this->returnValue('oxloadid'));
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
             ->method('getActRecommendationId')
             ->will($this->returnValue('testrecomm'));
        $oVC->expects($this->once())
             ->method('getActListType')
             ->will($this->returnValue('listtype'));

        $this->assertEquals('shopHomeUrl/cl=actionclass&amp;cnid=catid&amp;anid=anid&amp;searchparam=searchparam&amp;searchtag=searchtag&amp;recommid=testrecomm&amp;listtype=listtype&amp;fnc=logout&amp;tpl=tpl&amp;oxloadid=oxloadid&amp;redirect=1', $oVC->getLogoutLink());
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
            , array('getConfig', 'getTopActionClassName', 'getActCatId', 'getActTplName', 'getActContentLoadId'
            , 'getActArticleId', 'getActSearchParam', 'getActSearchTag', 'getActListType', 'getActRecommendationId'));

        $oVC->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($oCfg));
        $oVC->expects($this->once())
            ->method('getTopActionClassName')
            ->will($this->returnValue('actionclass'));
        $oVC->expects($this->once())
            ->method('getActCatId')
            ->will($this->returnValue('catid'));
        $oVC->expects($this->once())
            ->method('getActTplName')
            ->will($this->returnValue('tpl'));
        $oVC->expects($this->once())
            ->method('getActContentLoadId')
            ->will($this->returnValue('oxloadid'));
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
            ->method('getActRecommendationId')
            ->will($this->returnValue('testrecomm'));
        $oVC->expects($this->once())
            ->method('getActListType')
            ->will($this->returnValue('listtype'));

        $this->assertEquals('sslShopHomeUrl/cl=actionclass&amp;cnid=catid&amp;anid=anid&amp;searchparam=searchparam&amp;searchtag=searchtag&amp;recommid=testrecomm&amp;listtype=listtype&amp;fnc=logout&amp;tpl=tpl&amp;oxloadid=oxloadid&amp;redirect=1', $oVC->getLogoutLink());
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

    /**
     * oxViewConfig::getTopActionClassName() test case
     *
     * @return null
     */
    public function testGetTopActionClassName()
    {
        $oView = $this->getMock( "oxView", array( "getClassName" ) );
        $oView->expects( $this->once() )->method( "getClassName" )->will( $this->returnValue( "testViewClass" ) );

        $oConfig = $this->getMock( "oxConfig", array( "getTopActiveView" ) );
        $oConfig->expects( $this->once() )->method( "getTopActiveView" )->will( $this->returnValue( $oView ) );

        $oViewConfig = $this->getMock( "oxViewConfig", array( "getConfig" ) );
        $oViewConfig->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );

        $this->assertEquals( "testViewClass", $oViewConfig->getTopActiveClassName() );
    }

    public function testGetShowBasketTimeoutWhenFunctionalityIsOnAndTimeLeft()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);

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
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);

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
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

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
        $myConfig = $this->getConfig();

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

        $myConfig = $this->getConfig();
        $myConfig->setConfigParam( "aNrofCatArticlesInGrid", $aNrofCatArticlesInGrid );
        $myConfig->setConfigParam( "aNrofCatArticles", $aNrofCatArticles );

        $oViewCfg = $this->getMock( 'oxViewConfig', array( 'getConfig' ) );
        $oViewCfg->expects( $this->any() )->method( 'getConfig')->will( $this->returnValue( $myConfig ) );

        $oSession = $this->getSession();

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

        $myConfig = $this->getConfig();
        $myConfig->setConfigParam( "sShopDir", $sMdir."/" );

        $oVC = $this->getMock( 'oxViewConfig', array( 'getConfig' ) );
        $oVC->expects( $this->any() )->method( 'getConfig')->will( $this->returnValue( $myConfig ) );

        $this->assertEquals($sMdir."/modules/test1/out", $oVC->getModulePath('test1', 'out'));
        $this->assertEquals($sMdir."/modules/test1/out/", $oVC->getModulePath('test1', '/out/'));

        $this->assertEquals($sMdir."/modules/test1/out/blocks/test2.tpl", $oVC->getModulePath('test1', 'out/blocks/test2.tpl'));
        $this->assertEquals($sMdir."/modules/test1/out/blocks/test2.tpl", $oVC->getModulePath('test1', '/out/blocks/test2.tpl'));

        $this->getConfig()->setConfigParam( "iDebug", false );
        $this->assertEquals( '', $oVC->getModulePath('test1', '/out/blocks/testWWW.tpl') );
        // check exception throwing
        try {
            $this->getConfig()->setConfigParam( "iDebug", true );
            $oVC->getModulePath('test1', '/out/blocks/test1.tpl');
            $this->fail("should have thrown");
        } catch (oxFileException $e) {
            $this->assertEquals("Requested file not found for module test1 (".$sMdir."/modules/test1/out/blocks/test1.tpl)", $e->getMessage());
        }
    }

    public function testGetModuleUrl()
    {
        $sBaseUrl  = $this->getConfig()->getCurrentShopUrl();
        $sMdir = realpath((dirname(__FILE__).'/../moduleTestBlock'));

        $myConfig = $this->getConfig();
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
            $sBaseUrl  = $this->getConfig()->getConfigParam('sShopDir');
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

        $this->getConfig()->setConfigParam('bl_showListmania', 1);
        $this->assertEquals(1, $oVC->getViewThemeParam('bl_showListmania'));

        $this->getConfig()->setConfigParam('bl_showListmania', 0);
        $this->assertEquals(0, $oVC->getViewThemeParam('bl_showListmania'));
    }

    /**
     * Test case for oxViewConfig::showSelectLists()
     *
     * @return null
     */
    public function testShowSelectLists()
    {
        $blExp = (bool) $this->getConfig()->getConfigParam( 'bl_perfLoadSelectLists' );
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
        $this->getConfig()->setConfigParam('bl_perfLoadSelectListsInAList', true);

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
        $this->getConfig()->setConfigParam('bl_perfLoadSelectListsInAList', false);

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
        $this->getConfig()->setConfigParam('sAltImageUrl', '');
        $this->getConfig()->setConfigParam('sAltImageDir', '');
        $this->getConfig()->setConfigParam('sSSLAltImageUrl', '');
        $this->getConfig()->setConfigParam('sSSLAltImageDir', '');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertFalse( $oViewConfig->isAltImageServerConfigured() );
    }

    /**
     * Testing isAltImageServerConfigured() - http url configured
     */
    public function testIsAltImageServerConfigured_httpurl()
    {
        $this->getConfig()->setConfigParam('sAltImageUrl', 'http://img.oxid-esales.com');
        $this->getConfig()->setConfigParam('sAltImageDir', '');
        $this->getConfig()->setConfigParam('sSSLAltImageUrl', '');
        $this->getConfig()->setConfigParam('sSSLAltImageDir', '');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertTrue( $oViewConfig->isAltImageServerConfigured() );
    }

    /**
     * Testing isAltImageServerConfigured() - http dir configured
     */
    public function testIsAltImageServerConfigured_httpdir()
    {
        $this->getConfig()->setConfigParam('sAltImageUrl', '');
        $this->getConfig()->setConfigParam('sAltImageDir', 'http://img.oxid-esales.com');
        $this->getConfig()->setConfigParam('sSSLAltImageUrl', '');
        $this->getConfig()->setConfigParam('sSSLAltImageDir', '');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertTrue( $oViewConfig->isAltImageServerConfigured() );
    }

    /**
     * Testing isAltImageServerConfigured() - https url configured
     */
    public function testIsAltImageServerConfigured_httpsurl()
    {
        $this->getConfig()->setConfigParam('sAltImageUrl', '');
        $this->getConfig()->setConfigParam('sAltImageDir', '');
        $this->getConfig()->setConfigParam('sSSLAltImageUrl', 'https://img.oxid-esales.com');
        $this->getConfig()->setConfigParam('sSSLAltImageDir', '');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertTrue( $oViewConfig->isAltImageServerConfigured() );
    }

    /**
     * Testing isAltImageServerConfigured() - https dir configured
     */
    public function testIsAltImageServerConfigured_httpsdir()
    {
        $this->getConfig()->setConfigParam('sAltImageUrl', '');
        $this->getConfig()->setConfigParam('sAltImageDir', '');
        $this->getConfig()->setConfigParam('sSSLAltImageUrl', '');
        $this->getConfig()->setConfigParam('sSSLAltImageDir', 'https://img.oxid-esales.com');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertTrue( $oViewConfig->isAltImageServerConfigured() );
    }

    /**
     * oxViewConfig::getTopActiveClassName() test case
     *
     * @return null
     */
    public function testGetTopActiveClassName()
    {
        $oView = $this->getMock( "oxView", array( "getClassName" ) );
        $oView->expects( $this->once() )->method( "getClassName" )->will( $this->returnValue( "testViewClass" ) );

        $oConfig = $this->getMock( "oxConfig", array( "getTopActiveView" ) );
        $oConfig->expects( $this->once() )->method( "getTopActiveView" )->will( $this->returnValue( $oView ) );

        $oViewConfig = $this->getMock( "oxViewConfig", array( "getConfig" ) );
        $oViewConfig->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );

        $this->assertEquals( "testViewClass", $oViewConfig->getTopActiveClassName() );
    }

    public function testIsFunctionalityEnabled()
    {
        $oConfig = $this->getMock( "oxConfig", array( "getConfigParam" ) );
        $oConfig->expects( $this->once() )->method( "getConfigParam" )->with( $this->equalTo( 'bl_showWishlist' ) )->will( $this->returnValue( "will" ) );

        $oVieConfig = $this->getMock( "oxViewConfig", array( "getConfig" ) );
        $oVieConfig->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );

        $this->assertTrue( $oVieConfig->isFunctionalityEnabled( 'bl_showWishlist' ) );
    }

    /**
     * oxViewconfig::getActTplName() test case
     *
     * @return null
     */
    public function testGetActTplName()
    {
        $this->setRequestParam( "tpl", 123 );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( 123, $oViewConf->getActTplName() );
    }

    /**
     * oxViewconfig::getActCurrency() test case
     *
     * @return null
     */
    public function testGetActCurrency()
    {
        $this->setRequestParam( "cur", 1 );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( 1, $oViewConf->getActCurrency() );
    }

    /**
     * oxViewconfig::getActContentLoadId() test case
     *
     * @return null
     */
    public function testGetActContentLoadId()
    {
        $this->setRequestParam( "oxloadid", 123 );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( 123, $oViewConf->getActContentLoadId() );

        $this->setRequestParam( "oxloadid", null );
        $oViewConf->setViewConfigParam( 'oxloadid', 234 );
        $this->assertNull( $oViewConf->getActContentLoadId() );
    }

    /**
     * oxViewconfig::getActContentLoadId() test case
     *
     * @return null
     */
    public function testGetActContentLoadIdFromActView()
    {
        $oView = new content();
        $oViewConf = $oView->getViewConfig();
        $oViewConf->setViewConfigParam( 'oxloadid', 234 );

        $oConfig = $this->getMock( "oxConfig", array( "getTopActiveView" ) );
        $oConfig->expects( $this->any() )->method( "getTopActiveView" )->will( $this->returnValue( $oView ) );

        $oViewConfig = $this->getMock( "oxViewConfig", array( "getConfig" ) );
        $oViewConfig->expects( $this->any() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $this->assertEquals( 234, $oViewConfig->getActContentLoadId() );
    }

    /**
     * oxViewconfig::getActRecommendationId() test case
     *
     * @return null
     */
    public function testGetActRecommendationId()
    {
        $this->setRequestParam( "recommid", 1 );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( 1, $oViewConf->getActRecommendationId() );
    }

    /**
     * oxViewconfig::getHelpLink() test case
     *
     * @return null
     */

    public function testGetHelpLinkWithTemplate()
    {
        $sTemplate = "testTemplate";
        $sClass = "testClass";

        $oViewConfig = $this->getMock( "oxViewConfig", array( "getActTplName", "getActiveClassName" ) );
        $oViewConfig->expects( $this->any() )->method( "getActTplName" )->will( $this->returnValue( $sTemplate ) );
        $oViewConfig->expects( $this->any() )->method( "getActiveClassName" )->will( $this->returnValue( $sClass ) );

        $this->assertEquals( $this->getConfig()->getShopCurrentURL()."cl=help&amp;page=$sClass&amp;tpl=$sTemplate", $oViewConfig->getHelpLink() );
    }

    /**
     * oxViewconfig::getHelpLink() test case
     *
     * @return null
     */

    public function testGetHelpLinkWithoutTemplate()
    {
        $sTemplate = null;
        $sClass = "testClass";

        $oViewConfig = $this->getMock( "oxViewConfig", array( "getActTplName", "getActiveClassName" ) );
        $oViewConfig->expects( $this->any() )->method( "getActTplName" )->will( $this->returnValue( $sTemplate ) );
        $oViewConfig->expects( $this->any() )->method( "getActiveClassName" )->will( $this->returnValue( $sClass ) );

        $this->assertEquals( $this->getConfig()->getShopCurrentURL()."cl=help&amp;page=$sClass", $oViewConfig->getHelpLink() );
    }

    /**
     * oxViewconfig::getActCatId() test case
     *
     * @return null
     */

    public function testGetActCatId()
    {
        $iCat = 12345;
        $this->setRequestParam( "cnid", $iCat );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( $iCat, $oViewConf->getActCatId() );
    }

    /**
     * oxViewconfig::getActArticleId() test case
     *
     * @return null
     */

    public function testGetActArticleId()
    {
        $sArt = "12345";
        $this->setRequestParam( "anid", $sArt );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( $sArt, $oViewConf->getActArticleId() );
    }

    /**
     * oxViewconfig::getActSearchParam() test case
     *
     * @return null
     */

    public function testGetActSearchParam()
    {
        $sParam = "test=john";
        $this->setRequestParam( "searchparam", $sParam );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( $sParam, $oViewConf->getActSearchParam() );
    }

    /**
     * oxViewconfig::getActSearchTag() test case
     *
     * @return null
     */

    public function testGetActSearchTag()
    {
        $sTag = "test=john";
        $this->setRequestParam( "searchtag", $sTag );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( $sTag, $oViewConf->getActSearchTag() );
    }

    /**
     * oxViewconfig::getActListType() test case
     *
     * @return null
     */

    public function testGetActListType()
    {
        $sType = "testType";
        $this->setRequestParam( "listtype", $sType );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( $sType, $oViewConf->getActListType() );
    }

    /**
     * oxViewconfig::getContentId() test case
     *
     * @return null
     */

    public function testGetContentId()
    {
        $sOxcid = "testCID";
        $this->setRequestParam( "oxcid", $sOxcid );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( $sOxcid, $oViewConf->getContentId() );
    }

    /**
     * oxViewconfig::getViewConfigParam() test case
     *
     * @return null
     */

    public function testGetViewConfigParamFromOShop()
    {
        $sFieldName = "nameFromObject";

        $oShop = new stdClass();
        $oShop->$sFieldName = "testShopObj";

        $oViewConf = $this->getProxyClass('oxViewConfig');
        $oViewConf->setNonPublicVar( '_oShop', $oShop );
        $this->assertEquals( $oShop->$sFieldName, $oViewConf->getViewConfigParam( $sFieldName ) );
    }

    /**
     * oxViewconfig::getViewConfigParam() test case
     *
     * @return null
     */

    public function testGetViewConfigParamFromAViewData()
    {
        $sFieldName = "nameFromArray";

        $aViewData = array();
        $aViewData[$sFieldName] = "testShopArr";

        $oViewConf = $this->getProxyClass('oxViewConfig');
        $oViewConf->setNonPublicVar( '_aViewData', $aViewData );
        $this->assertEquals( $aViewData[$sFieldName], $oViewConf->getViewConfigParam( $sFieldName ) );
    }

    /**
     * oxViewconfig::getHiddenSid() test case
     *
     * @return null
     */

    public function testGetHiddenSidFromSession()
    {
        $sSid = "newSid";

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getSession" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "hiddensid" ))->will( $this->returnValue( $sSid ) );
        $oViewConf->expects( $this->never() )->method( "getSession" );

        $this->assertEquals( $sSid, $oViewConf->getHiddenSid() );
    }

    /**
     * oxViewconfig::getHiddenSid() test case
     *
     * @return null
     */

    public function testGetHiddenSidFromSessionNull()
    {
        $sSid = "newSid";
        $sLang = "testLang";
        $sSidNew = $sSid.'
'.$sLang;
        $oSession = $this->getMock( "oxSession", array( "hiddenSid" ) );
        $oSession->expects( $this->once() )->method( "hiddenSid" )->will( $this->returnValue( $sSid ) );

        $oLang = $this->getMock( "oxLang", array( "getFormLang" ) );
        $oLang->expects( $this->once() )->method( "getFormLang" )->will( $this->returnValue( $sLang ) );
        oxRegistry::set("oxLang", $oLang);

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getSession", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "hiddensid" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getSession" )->will( $this->returnValue( $oSession ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "hiddensid" ), $this->equalTo( $sSidNew ));

        $this->assertEquals( $sSidNew, $oViewConf->getHiddenSid() );
    }



    /**
     * oxViewconfig::getBaseDir() test case
     *
     * @return null
     */

    public function testGetBaseDirForSsl()
    {
        $sSslLink = "sslsitelink";
        $oConfig = $this->getMock( "oxConfig", array( "isSsl", "getSSLShopURL" ) );
        $oConfig->expects( $this->once() )->method( "isSsl" )->will( $this->returnValue( true ) );
        $oConfig->expects( $this->once() )->method( "getSSLShopURL" )->will( $this->returnValue( $sSslLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "basedir" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->exactly(2) )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );

        $this->assertEquals( $sSslLink, $oViewConf->getBaseDir() );
    }

    /**
     * oxViewconfig::getCoreUtilsDir() test case
     *
     * @return null
     */

    public function testGetCoreUtilsDir()
    {
        $sDir= "testingDir";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'coreutilsdir', $sDir );

        $this->assertEquals( $sDir, $oViewConf->getCoreUtilsDir() );
    }

    /**
     * oxViewconfig::getCoreUtilsDir() test case
     *
     * @return null
     */

    public function testGetCoreUtilsDirWhenNull()
    {
        $sDir= "testingDir";
        $oConfig = $this->getMock( "oxConfig", array( "getCoreUtilsURL" ) );
        $oConfig->expects( $this->once() )->method( "getCoreUtilsURL" )->will( $this->returnValue( $sDir ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "coreutilsdir" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "coreutilsdir" ), $this->equalTo( $sDir ));

        $this->assertEquals( $sDir, $oViewConf->getCoreUtilsDir() );
    }

    /**
     * oxViewconfig::getSelfActionLink() test case
     *
     * @return null
     */

    public function testGetSelfActionLink()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'selfactionlink', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getSelfActionLink() );
    }

    /**
     * oxViewconfig::getSelfActionLink() test case
     *
     * @return null
     */

    public function testGetSelfActionLinkWhenNull()
    {
        $sLink= "testingLink";
        $oConfig = $this->getMock( "oxConfig", array( "getShopCurrentUrl" ) );
        $oConfig->expects( $this->once() )->method( "getShopCurrentUrl" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "selfactionlink" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "selfactionlink" ), $this->equalTo( $sLink ));

        $this->assertEquals( $sLink, $oViewConf->getSelfActionLink() );
    }

    /**
     * oxViewconfig::getCurrentHomeDir() test case
     *
     * @return null
     */

    public function testGetCurrentHomeDir()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'currenthomedir', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getCurrentHomeDir() );
    }

    /**
     * oxViewconfig::getCurrentHomeDir() test case
     *
     * @return null
     */

    public function testGetCurrentHomeDirWhenNull()
    {
        $sLink= "testingLink";
        $oConfig = $this->getMock( "oxConfig", array( "getCurrentShopUrl" ) );
        $oConfig->expects( $this->once() )->method( "getCurrentShopUrl" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "currenthomedir" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "currenthomedir" ), $this->equalTo( $sLink ));

        $this->assertEquals( $sLink, $oViewConf->getCurrentHomeDir() );
    }

    /**
     * oxViewconfig::getBasketLink() test case
     *
     * @return null
     */

    public function testGetBasketLink()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'basketlink', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getBasketLink() );
    }

    /**
     * oxViewconfig::getBasketLink() test case
     *
     * @return null
     */

    public function testGetBasketLinkWhenNull()
    {
        $sLink= "testingLink";
        $sLinkNew= "testingLink"."cl=basket";
        $oConfig = $this->getMock( "oxConfig", array( "getShopHomeURL" ) );
        $oConfig->expects( $this->once() )->method( "getShopHomeURL" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "basketlink" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "basketlink" ), $this->equalTo( $sLinkNew ));

        $this->assertEquals( $sLinkNew, $oViewConf->getBasketLink() );
    }

    /**
     * oxViewconfig::getOrderLink() test case
     *
     * @return null
     */

    public function testGetOrderLink()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'orderlink', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getOrderLink() );
    }

    /**
     * oxViewconfig::getOrderLink() test case
     *
     * @return null
     */

    public function testGetOrderLinkWhenNull()
    {
        $sLink= "testingLink";
        $sLinkNew= "testingLink"."cl=user";
        $oConfig = $this->getMock( "oxConfig", array( "getShopSecureHomeUrl" ) );
        $oConfig->expects( $this->once() )->method( "getShopSecureHomeUrl" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "orderlink" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "orderlink" ), $this->equalTo( $sLinkNew ));

        $this->assertEquals( $sLinkNew, $oViewConf->getOrderLink() );
    }

    /**
     * oxViewconfig::getPaymentLink() test case
     *
     * @return null
     */

    public function testGetPaymentLink()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'paymentlink', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getPaymentLink() );
    }

    /**
     * oxViewconfig::getPaymentLink() test case
     *
     * @return null
     */

    public function testGetPaymentLinkWhenNull()
    {
        $sLink= "testingLink";
        $sLinkNew= "testingLink"."cl=payment";
        $oConfig = $this->getMock( "oxConfig", array( "getShopSecureHomeUrl" ) );
        $oConfig->expects( $this->once() )->method( "getShopSecureHomeUrl" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "paymentlink" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "paymentlink" ), $this->equalTo( $sLinkNew ));

        $this->assertEquals( $sLinkNew, $oViewConf->getPaymentLink() );
    }

    /**
     * oxViewconfig::getExeOrderLink() test case
     *
     * @return null
     */

    public function testGetExeOrderLink()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'exeorderlink', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getExeOrderLink() );
    }

    /**
     * oxViewconfig::getExeOrderLink() test case
     *
     * @return null
     */

    public function testGetExeOrderLinkWhenNull()
    {
        $sLink= "testingLink";
        $sLinkNew= "testingLink"."cl=order&amp;fnc=execute";
        $oConfig = $this->getMock( "oxConfig", array( "getShopSecureHomeUrl" ) );
        $oConfig->expects( $this->once() )->method( "getShopSecureHomeUrl" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "exeorderlink" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "exeorderlink" ), $this->equalTo( $sLinkNew ));

        $this->assertEquals( $sLinkNew, $oViewConf->getExeOrderLink() );
    }

    /**
     * oxViewconfig::getOrderConfirmLink() test case
     *
     * @return null
     */

    public function testGetOrderConfirmLink()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'orderconfirmlink', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getOrderConfirmLink() );
    }

    /**
     * oxViewconfig::getOrderConfirmLink() test case
     *
     * @return null
     */

    public function testGetOrderConfirmLinkWhenNull()
    {
        $sLink= "testingLink";
        $sLinkNew= "testingLink"."cl=order";
        $oConfig = $this->getMock( "oxConfig", array( "getShopSecureHomeUrl" ) );
        $oConfig->expects( $this->once() )->method( "getShopSecureHomeUrl" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "orderconfirmlink" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "orderconfirmlink" ), $this->equalTo( $sLinkNew ));

        $this->assertEquals( $sLinkNew, $oViewConf->getOrderConfirmLink() );
    }

    /**
     * oxViewconfig::getResourceUrl() test case
     *
     * @return null
     */

    public function testGetResourceUrl()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'basetpldir', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getResourceUrl() );
    }

    /**
     * oxViewconfig::getResourceUrl() test case
     *
     * @return null
     */

    public function testGetResourceUrlWhenNull()
    {
        $sLink= "testingLink";
        $oConfig = $this->getMock( "oxConfig", array( "getResourceUrl" ) );
        $oConfig->expects( $this->once() )->method( "getResourceUrl" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "basetpldir" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "basetpldir" ), $this->equalTo( $sLink ));

        $this->assertEquals( $sLink, $oViewConf->getResourceUrl() );
    }

    /**
     * oxViewconfig::getResourceUrl() test case
     *
     * @return null
     */

    public function testGetResourceUrlWithFile()
    {
        $sLink= "testingLink";
        $oConfig = $this->getMock( "oxConfig", array( "getResourceUrl" ) );
        $oConfig->expects( $this->once() )->method( "getResourceUrl" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->never() )->method( "setViewConfigParam" );

        $this->assertEquals( $sLink, $oViewConf->getResourceUrl( $sLink ) );
    }

    /**
     * oxViewconfig::getTemplateDir() test case
     *
     * @return null
     */

    public function testGetTemplateDir()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'templatedir', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getTemplateDir() );
    }

    /**
     * oxViewconfig::getTemplateDir() test case
     *
     * @return null
     */

    public function testGetTemplateDirWhenNull()
    {
        $sLink= "testingLink";
        $oConfig = $this->getMock( "oxConfig", array( "getTemplateDir" ) );
        $oConfig->expects( $this->once() )->method( "getTemplateDir" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "templatedir" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "templatedir" ), $this->equalTo( $sLink ));

        $this->assertEquals( $sLink, $oViewConf->getTemplateDir() );
    }

    /**
     * oxViewconfig::getUrlTemplateDir() test case
     *
     * @return null
     */

    public function testGetUrlTemplateDir()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'urltemplatedir', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getUrlTemplateDir() );
    }

    /**
     * oxViewconfig::getTemplateDir() test case
     *
     * @return null
     */

    public function testGetUrlTemplateDirWhenNull()
    {
        $sLink= "testingLink";
        $oConfig = $this->getMock( "oxConfig", array( "getTemplateUrl" ) );
        $oConfig->expects( $this->once() )->method( "getTemplateUrl" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "urltemplatedir" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "urltemplatedir" ), $this->equalTo( $sLink ));

        $this->assertEquals( $sLink, $oViewConf->getUrlTemplateDir() );
    }

    /**
     * oxViewconfig::getNoSslImageDir() test case
     *
     * @return null
     */

    public function testGetNoSslImageDir()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'nossl_imagedir', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getNoSslImageDir() );
    }

    /**
     * oxViewconfig::getNoSslImageDir() test case
     *
     * @return null
     */

    public function testGetNoSslImageDirWhenNull()
    {
        $sLink= "testingLink";
        $oConfig = $this->getMock( "oxConfig", array( "getImageUrl" ) );
        $oConfig->expects( $this->once() )->method( "getImageUrl" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "nossl_imagedir" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "nossl_imagedir" ), $this->equalTo( $sLink ));

        $this->assertEquals( $sLink, $oViewConf->getNoSslImageDir() );
    }

    /**
     * oxViewconfig::getPictureDir() test case
     *
     * @return null
     */

    public function testGetPictureDir()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'picturedir', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getPictureDir() );
    }

    /**
     * oxViewconfig::getPictureDir() test case
     *
     * @return null
     */

    public function testGetPictureDirWhenNull()
    {
        $sLink= "testingLink";
        $oConfig = $this->getMock( "oxConfig", array( "getPictureUrl" ) );
        $oConfig->expects( $this->once() )->method( "getPictureUrl" )->will( $this->returnValue( $sLink ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "picturedir" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "picturedir" ), $this->equalTo( $sLink ));

        $this->assertEquals( $sLink, $oViewConf->getPictureDir() );
    }

    /**
     * oxViewconfig::getAdminDir() test case
     *
     * @return null
     */

    public function testGetAdminDir()
    {
        $sLink= "testingLink";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'sAdminDir', $sLink );

        $this->assertEquals( $sLink, $oViewConf->getAdminDir() );
    }

    /**
     * oxViewconfig::getAdminDir() test case
     *
     * @return null
     */

    public function testGetAdminDirWhenNull()
    {
        $sLink= "testingLink";
        $this->getConfig()->setConfigParam( "sAdminDir", $sLink );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "sAdminDir" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "sAdminDir" ), $this->equalTo( $sLink ));

        $this->assertEquals( $sLink, $oViewConf->getAdminDir() );
    }

    /**
     * oxViewconfig::getActiveShopId() test case
     *
     * @return null
     */

    public function testGetActiveShopId()
    {
        $sId = "testShopId";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'shopid', $sId );

        $this->assertEquals( $sId, $oViewConf->getActiveShopId() );
    }

    /**
     * oxViewconfig::getActiveShopId() test case
     *
     * @return null
     */

    public function testGetActiveShopIdWhenNull()
    {
        $sId = "testShopId";
        $oConfig = $this->getMock( "oxConfig", array( "getShopId" ) );
        $oConfig->expects( $this->once() )->method( "getShopId" )->will( $this->returnValue( $sId ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "shopid" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "shopid" ), $this->equalTo( $sId ));

        $this->assertEquals( $sId, $oViewConf->getActiveShopId() );
    }

    /**
     * oxViewconfig::isSsl() test case
     *
     * @return null
     */

    public function testIsSsl()
    {
        $sTest = "isSsl";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'isssl', $sTest );

        $this->assertEquals( $sTest, $oViewConf->isSsl() );
    }

    /**
     * oxViewconfig::isSsl() test case
     *
     * @return null
     */

    public function testIsSslWhenNull()
    {
        $sTest = "isSsl";
        $oConfig = $this->getMock( "oxConfig", array( "isSsl" ) );
        $oConfig->expects( $this->once() )->method( "isSsl" )->will( $this->returnValue( $sTest ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "isssl" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "isssl" ), $this->equalTo( $sTest ));

        $this->assertEquals( $sTest, $oViewConf->isSsl() );
    }


    /**
     * oxViewconfig::getRemoteAddress() test case
     *
     * @return null
     */

    public function testGetRemoteAddress()
    {
        $sTest = "testAddress";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'ip', $sTest );

        $this->assertEquals( $sTest, $oViewConf->getRemoteAddress() );
    }

    /**
     * oxViewconfig::getRemoteAddress() test case
     *
     * @return null
     */

    public function testGetRemoteAddressWhenNull()
    {
        $sTest = "testAddress";

        $oUtils = $this->getMock( "oxUtilsServer", array( "getRemoteAddress" ) );
        $oUtils->expects( $this->once() )->method( "getRemoteAddress" )->will( $this->returnValue( $sTest ) );

        oxRegistry::set("oxUtilsServer", $oUtils);

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "ip" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "ip" ), $this->equalTo( $sTest ));

        $this->assertEquals( $sTest, $oViewConf->getRemoteAddress() );
    }

    /**
     * oxViewconfig::getPopupIdent() test case
     *
     * @return null
     */

    public function testGetPopupIdent()
    {
        $sTest = "testIdent";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'popupident', $sTest );

        $this->assertEquals( $sTest, $oViewConf->getPopupIdent() );
    }

    /**
     * oxViewconfig::getPopupIdent() test case
     *
     * @return null
     */

    public function testGetPopupIdentWhenNull()
    {
        $sTest = "testIdent";
        $sTestNew = md5( $sTest );
        $oConfig = $this->getMock( "oxConfig", array( "getShopUrl" ) );
        $oConfig->expects( $this->once() )->method( "getShopUrl" )->will( $this->returnValue( $sTest ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "popupident" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "popupident" ), $this->equalTo( $sTestNew ));

        $this->assertEquals( $sTestNew, $oViewConf->getPopupIdent() );
    }

    /**
     * oxViewconfig::getPopupIdentRand() test case
     *
     * @return null
     */

    public function testGetPopupIdentRand()
    {
        $sTest = "testIdent";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'popupidentrand', $sTest );

        $this->assertEquals( $sTest, $oViewConf->getPopupIdentRand() );
    }

    /**
     * oxViewconfig::getPopupIdentRand() test case
     *
     * @return null
     */

    public function testGetPopupIdentRandWhenNull()
    {
        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "popupidentrand" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "popupidentrand" ));

        $this->assertTrue( strlen($oViewConf->getPopupIdentRand() ) == 32 );
    }

    /**
     * oxViewconfig::getArtPerPageForm() test case
     *
     * @return null
     */

    public function testGetArtPerPageForm()
    {
        $sTest = "testUrl";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'artperpageform', $sTest );

        $this->assertEquals( $sTest, $oViewConf->getArtPerPageForm() );
    }

    /**
     * oxViewconfig::getArtPerPageForm() test case
     *
     * @return null
     */

    public function testGetArtPerPageFormWhenNull()
    {
        $sTest = "testUrl";
        $oConfig = $this->getMock( "oxConfig", array( "getShopCurrentUrl" ) );
        $oConfig->expects( $this->once() )->method( "getShopCurrentUrl" )->will( $this->returnValue( $sTest ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getViewConfigParam", "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getViewConfigParam" )->with( $this->equalTo( "artperpageform" ))->will( $this->returnValue( null ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "artperpageform" ), $this->equalTo( $sTest ));

        $this->assertEquals( $sTest, $oViewConf->getArtPerPageForm() );
    }

    /**
     * oxViewconfig::isBuyableParent() test case
     *
     * @return null
     */
    public function testIsBuyableParent()
    {
        $this->getConfig()->setConfigParam( "blVariantParentBuyable", true );

        $oViewConf = new oxViewConfig();
        $this->assertTrue( $oViewConf->isBuyableParent() );

        $this->getConfig()->setConfigParam( "blVariantParentBuyable", false );
        $this->assertFalse( $oViewConf->isBuyableParent() );
    }

    /**
     * oxViewconfig::showBirthdayFields() test case
     *
     * @return null
     */
    public function testShowBirthdayFields()
    {
        $this->getConfig()->setConfigParam( "blShowBirthdayFields", true );

        $oViewConf = new oxViewConfig();
        $this->assertTrue( $oViewConf->showBirthdayFields() );

        $this->getConfig()->setConfigParam( "blShowBirthdayFields", false );
        $this->assertFalse( $oViewConf->showBirthdayFields() );
    }

    /**
     * oxViewconfig::showFinalStep() test case
     *
     * @return null
     */
    public function testShowFinalStep()
    {
        $oViewConf = new oxViewConfig();
        $this->assertTrue( $oViewConf->showFinalStep() );
    }

    /**
     * oxViewconfig::getActLanguageAbbr() test case
     *
     * @return null
     */
    public function testGetActLanguageAbbr()
    {
        $sTest = "testAbc";

        $oLang = $this->getMock( "oxLang", array( "getLanguageAbbr" ) );
        $oLang->expects( $this->once() )->method( "getLanguageAbbr" )->will( $this->returnValue( $sTest ) );

        oxRegistry::set( "oxLang", $oLang );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( $sTest, $oViewConf->getActLanguageAbbr() );
    }

    /**
     * oxViewconfig::getActiveClassName() test case
     *
     * @return null
     */
    public function testGetActiveClassName()
    {
        $sTest = "testAbc";

        $oView = $this->getMock( "oxView", array( "getClassName" ) );
        $oView->expects( $this->once() )->method( "getClassName" )->will( $this->returnValue( $sTest ) );

        $oConfig = $this->getMock( "oxConfig", array( "getActiveView" ) );
        $oConfig->expects( $this->once() )->method( "getActiveView" )->will( $this->returnValue( $oView ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getConfig" ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );

        $this->assertEquals( $sTest, $oViewConf->getActiveClassName() );
    }



    /**
     * oxViewconfig::getArtPerPageCount() test case
     *
     * @return null
     */
    public function testGetArtPerPageCount()
    {
        $sTest = "testAbc";
        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'iartPerPage', $sTest );

        $this->assertEquals( $sTest, $oViewConf->getArtPerPageCount() );
    }

    /**
     * oxViewconfig::getNavUrlParams() test case
     *
     * @return null
     */
    public function testGetNavUrlParams()
    {
        $sTest = "testAbc";
        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'navurlparams', $sTest );

        $this->assertEquals( $sTest, $oViewConf->getNavUrlParams() );
    }

    /**
     * oxViewconfig::getNavUrlParams() test case
     *
     * @return null
     */
    public function testGetNavUrlParamsEmptyNavigationParams()
    {
        $aTest = array();
        $sTest = "";

        $oView = $this->getMock( "oxView", array( "getNavigationParams" ) );
        $oView->expects( $this->once() )->method( "getNavigationParams" )->will( $this->returnValue( $aTest ) );

        $oConfig = $this->getMock( "oxConfig", array( "getActiveView" ) );
        $oConfig->expects( $this->once() )->method( "getActiveView" )->will( $this->returnValue( $oView ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "navurlparams" ), $this->equalTo( $sTest ));

        $this->assertEquals( $sTest, $oViewConf->getNavUrlParams() );
    }

    /**
     * oxViewconfig::getNavUrlParams() test case
     *
     * @return null
     */
    public function testGetNavUrlParamsOneNavigationParam()
    {
        $aTest = array( "testKey" => "testValue" );
        $sTest = "&amp;testKey=testValue";

        $oView = $this->getMock( "oxView", array( "getNavigationParams" ) );
        $oView->expects( $this->once() )->method( "getNavigationParams" )->will( $this->returnValue( $aTest ) );

        $oConfig = $this->getMock( "oxConfig", array( "getActiveView" ) );
        $oConfig->expects( $this->once() )->method( "getActiveView" )->will( $this->returnValue( $oView ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "navurlparams" ), $this->equalTo( $sTest ));

        $this->assertEquals( $sTest, $oViewConf->getNavUrlParams() );
    }

    /**
     * oxViewconfig::getNavUrlParams() test case
     *
     * @return null
     */
    public function testGetNavUrlParamsTwoNavigationParams()
    {
        $aTest = array( "testKey1" => "testValue1", "testKey2" => "testValue2" );
        $sTest = "&amp;testKey1=testValue1&amp;testKey2=testValue2";

        $oView = $this->getMock( "oxView", array( "getNavigationParams" ) );
        $oView->expects( $this->once() )->method( "getNavigationParams" )->will( $this->returnValue( $aTest ) );

        $oConfig = $this->getMock( "oxConfig", array( "getActiveView" ) );
        $oConfig->expects( $this->once() )->method( "getActiveView" )->will( $this->returnValue( $oView ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "navurlparams" ), $this->equalTo( $sTest ));

        $this->assertEquals( $sTest, $oViewConf->getNavUrlParams() );
    }

    /**
     * oxViewconfig::getNavUrlParams() test case
     *
     * @return null
     */
    public function testGetNavUrlParamsTwoNavigationParamsOneWithoutValue()
    {
        $aTest = array( "testKey1" => "testValue1", "testKey2" => null );
        $sTest = "&amp;testKey1=testValue1";

        $oView = $this->getMock( "oxView", array( "getNavigationParams" ) );
        $oView->expects( $this->once() )->method( "getNavigationParams" )->will( $this->returnValue( $aTest ) );

        $oConfig = $this->getMock( "oxConfig", array( "getActiveView" ) );
        $oConfig->expects( $this->once() )->method( "getActiveView" )->will( $this->returnValue( $oView ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "navurlparams" ), $this->equalTo( $sTest ));

        $this->assertEquals( $sTest, $oViewConf->getNavUrlParams() );
    }

    /**
     * oxViewconfig::getNavFormParams() test case
     *
     * @return null
     */
    public function getNavFormParams()
    {
        $sTest = "testAbc";
        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'navformparams', $sTest );

        $this->assertEquals( $sTest, $oViewConf->getNavFormParams() );
    }

    /**
     * oxViewconfig::getNavFormParams() test case
     *
     * @return null
     */
    public function testGetNavFormParamsEmptyNavigationParams()
    {
        $aTest = array();
        $sTest = "";

        $oView = $this->getMock( "oxView", array( "getNavigationParams" ) );
        $oView->expects( $this->once() )->method( "getNavigationParams" )->will( $this->returnValue( $aTest ) );

        $oConfig = $this->getMock( "oxConfig", array( "getActiveView" ) );
        $oConfig->expects( $this->once() )->method( "getActiveView" )->will( $this->returnValue( $oView ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "navformparams" ), $this->equalTo( $sTest ));

        $this->assertEquals( $sTest, $oViewConf->getNavFormParams() );
    }

    /**
     * oxViewconfig::getNavFormParams() test case
     *
     * @return null
     */
    public function testGetNavFormParamsOneNavigationParam()
    {
        $aTest = array( "testKey" => "testVal" );
        $sTest = '<input type="hidden" name="testKey" value="testVal" />
';

        $oView = $this->getMock( "oxView", array( "getNavigationParams" ) );
        $oView->expects( $this->once() )->method( "getNavigationParams" )->will( $this->returnValue( $aTest ) );

        $oConfig = $this->getMock( "oxConfig", array( "getActiveView" ) );
        $oConfig->expects( $this->once() )->method( "getActiveView" )->will( $this->returnValue( $oView ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "navformparams" ), $this->equalTo( $sTest ));

        $this->assertEquals( $sTest, $oViewConf->getNavFormParams() );
    }

    /**
     * oxViewconfig::getNavFormParams() test case
     *
     * @return null
     */
    public function testGetNavFormParamsTwoNavigationParams()
    {
        $aTest = array( "testKey1" => "testVal1", "testKey2" => "testVal2" );
        $sTest = '<input type="hidden" name="testKey1" value="testVal1" />
<input type="hidden" name="testKey2" value="testVal2" />
';

        $oView = $this->getMock( "oxView", array( "getNavigationParams" ) );
        $oView->expects( $this->once() )->method( "getNavigationParams" )->will( $this->returnValue( $aTest ) );

        $oConfig = $this->getMock( "oxConfig", array( "getActiveView" ) );
        $oConfig->expects( $this->once() )->method( "getActiveView" )->will( $this->returnValue( $oView ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "navformparams" ), $this->equalTo( $sTest ));

        $this->assertEquals( $sTest, $oViewConf->getNavFormParams() );
    }

    /**
     * oxViewconfig::getNavFormParams() test case
     *
     * @return null
     */
    public function testGetNavFormParamsTwoNavigationParamsOneWithoutValue()
    {
        $aTest = array( "testKey1" => "testVal1", "testKey2" => null );
        $sTest = '<input type="hidden" name="testKey1" value="testVal1" />
';

        $oView = $this->getMock( "oxView", array( "getNavigationParams" ) );
        $oView->expects( $this->once() )->method( "getNavigationParams" )->will( $this->returnValue( $aTest ) );

        $oConfig = $this->getMock( "oxConfig", array( "getActiveView" ) );
        $oConfig->expects( $this->once() )->method( "getActiveView" )->will( $this->returnValue( $oView ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getConfig", "setViewConfigParam" ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );
        $oViewConf->expects( $this->once() )->method( "setViewConfigParam" )->with( $this->equalTo( "navformparams" ), $this->equalTo( $sTest ));

        $this->assertEquals( $sTest, $oViewConf->getNavFormParams() );
    }

    /**
     * oxViewconfig::getStockOnDefaultMessage() test case
     *
     * @return null
     */
    public function testGetStockOnDefaultMessage()
    {
        $sTest = "testValue";
        $this->getConfig()->setConfigParam( "blStockOnDefaultMessage", $sTest );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( $sTest, $oViewConf->getStockOnDefaultMessage() );
    }

    /**
     * oxViewconfig::getStockOffDefaultMessage() test case
     *
     * @return null
     */
    public function testGetStockOffDefaultMessage()
    {
        $sTest = "testValue";
        $this->getConfig()->setConfigParam( "blStockOffDefaultMessage", $sTest );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( $sTest, $oViewConf->getStockOffDefaultMessage() );
    }

    /**
     * oxViewconfig::getShopVersion() test case
     *
     * @return null
     */
    public function testGetShopVersion()
    {
        $sTest = "testShopVersion";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'sShopVersion', $sTest );
        $this->assertEquals( $sTest, $oViewConf->getShopVersion() );
    }

    /**
     * oxViewconfig::getServiceUrl() test case
     *
     * @return null
     */
    public function testGetServiceUrl()
    {
        $sTest = "testServiceUrl";

        $oViewConf = new oxViewConfig();
        $oViewConf->setViewConfigParam( 'sServiceUrl', $sTest );
        $this->assertEquals( $sTest, $oViewConf->getServiceUrl() );
    }

    /**
     * oxViewconfig::isMultiShop() test case
     *
     * @return null
     */
    public function testIsMultiShop()
    {
        $sTest = "testServiceUrl";

        $oObj = new stdClass();
        $oObj->oxshops__oxismultishop = new stdClass();
        $oObj->oxshops__oxismultishop->value = $sTest;

        $oConfig = $this->getMock( "oxConfig", array( "getActiveShop" ) );
        $oConfig->expects( $this->once() )->method( "getActiveShop" )->will( $this->returnValue( $oObj ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getConfig" ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );

        $this->assertTrue( $oViewConf->isMultiShop() );
    }

    /**
     * oxViewconfig::isMultiShop() test case
     *
     * @return null
     */
    public function testIsMultiShopNotSet()
    {
        $oObj = new stdClass();
        $oObj->oxshops__oxismultishop = null;

        $oConfig = $this->getMock( "oxConfig", array( "getActiveShop" ) );
        $oConfig->expects( $this->once() )->method( "getActiveShop" )->will( $this->returnValue( $oObj ) );

        $oViewConf = $this->getMock( "oxViewConfig", array( "getConfig" ) );
        $oViewConf->expects( $this->once() )->method( "getConfig" )->will( $this->returnValue( $oConfig ) );

        $this->assertFalse( $oViewConf->isMultiShop() );
    }

    /**
     * oxViewconfig::getFbAppId() test case
     *
     * @return null
     */
    public function testGetFbAppId()
    {
        $sTest = "sFbApp";
        $this->getConfig()->setConfigParam( "sFbAppId", $sTest );

        $oViewConf = new oxViewConfig();
        $this->assertEquals( $sTest, $oViewConf->getFbAppId() );
    }

    /**
     * oxViewconfig::getShowFbConnect() test case
     *
     * @return null
     */
    public function testGetShowFbConnect()
    {
        $oViewConf = new oxViewConfig();

        $this->getConfig()->setConfigParam( "bl_showFbConnect", false );
        $this->assertFalse( $oViewConf->getShowFbConnect() );

        $this->getConfig()->setConfigParam( "bl_showFbConnect", true );
        $this->getConfig()->setConfigParam( "sFbAppId", true );
        $this->getConfig()->setConfigParam( "sFbSecretKey", true );
        $this->assertTrue( $oViewConf->getShowFbConnect() );

        $this->getConfig()->setConfigParam( "bl_showFbConnect", true );
        $this->getConfig()->setConfigParam( "sFbAppId", false );
        $this->getConfig()->setConfigParam( "sFbSecretKey", false );
        $this->assertFalse( $oViewConf->getShowFbConnect() );

        $this->getConfig()->setConfigParam( "bl_showFbConnect", true );
        $this->getConfig()->setConfigParam( "sFbAppId", false );
        $this->getConfig()->setConfigParam( "sFbSecretKey", true );
        $this->assertFalse( $oViewConf->getShowFbConnect() );

        $this->getConfig()->setConfigParam( "bl_showFbConnect", true );
        $this->getConfig()->setConfigParam( "sFbAppId", true );
        $this->getConfig()->setConfigParam( "sFbSecretKey", false );
        $this->assertFalse( $oViewConf->getShowFbConnect() );

    }

    /**
     * oxViewconfig::getPasswordLength() test case
     *
     * @return null
     */
    public function testGetPasswordLength()
    {
        $oViewConf = new oxViewConfig();
        $this->assertEquals( 6, $oViewConf->getPasswordLength() );

        $this->getConfig()->setConfigParam( "iPasswordLength", 66 );
        $this->assertEquals( 66, $oViewConf->getPasswordLength() );

    }

    /**
     * oxViewconfig::getActiveTheme() test case for main theme
     */
    public function testGetActiveTheme_mainTheme()
    {
        $oViewConf = new oxViewConfig();
        $oViewConf->getConfig()->setConfigParam( "sTheme", "testTheme" );
        $this->assertEquals( 'testTheme', $oViewConf->getActiveTheme() );
    }

    /**
     * oxViewconfig::getActiveTheme() test case for custom theme
     */
    public function testGetActiveTheme_customTheme()
    {
        $oViewConf = new oxViewConfig();
        $oViewConf->getConfig()->setConfigParam( "sCustomTheme", "testCustomTheme" );
        $oViewConf->getConfig()->setConfigParam( "sTheme", "testTheme" );
        $this->assertEquals( 'testCustomTheme', $oViewConf->getActiveTheme() );
    }

    public function testSetGetShopLogo()
    {
        $oView = new oxViewConfig();
        $oView->setShopLogo( "testlogo" );
        $this->assertEquals( "testlogo", $oView->getShopLogo() );
    }

    public function testSetGetShopLogo_FromConfig()
    {
        $oView = new oxViewConfig();
        $this->getConfig()->setConfigParam( "sShopLogo", 'logo' );
        $this->assertEquals( "logo", $oView->getShopLogo() );
    }

    public function testSetGetShopLogo_DefaultValue()
    {
        $oView = new oxViewConfig();

        $sLogo = "logo.png";

        $this->assertEquals( $sLogo, $oView->getShopLogo() );
    }

    /**
     * Data provider for test testGetSessionChallengeToken.
     *
     * @return array
     */
    public function _dpGetSessionChallengeToken()
    {
        return array(
            array(false, 0, ''),
            array(true, 1, 'session_challenge_token'),
        );
    }

    /**
     * /**
     * Tests retrieve session challenge token from session.
     *
     * @dataProvider _dpGetSessionChallengeToken
     *
     * @param boolean $blIsSessionStarted                   is session started
     * @param integer $iGetSessionChallengeTokenCalledTimes method getSessionChallengeToken expected to be called times
     * @param string  $sToken                               Security token
     */
    public function testGetSessionChallengeToken($blIsSessionStarted, $iGetSessionChallengeTokenCalledTimes, $sToken)
    {
        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('isSessionStarted', 'getSessionChallengeToken'));

        $oSession->expects($this->once())->method('isSessionStarted')
            ->will($this->returnValue($blIsSessionStarted));
        $oSession->expects($this->exactly($iGetSessionChallengeTokenCalledTimes))->method('getSessionChallengeToken')
            ->will($this->returnValue($sToken));
        oxRegistry::set('oxSession', $oSession);

        $oViewConfig = new oxViewConfig();
        $this->assertSame($sToken, $oViewConfig->getSessionChallengeToken());
    }
}
