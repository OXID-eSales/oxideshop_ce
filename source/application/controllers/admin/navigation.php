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
 /**
 * Administrator GUI navigation manager class.
 * @package admin
 */
class Navigation extends oxAdminView
{
    /**
     * Allowed host url
     *
     * @var string
     */
    protected $_sAllowedHost = "http://admin.oxid-esales.com";

    /**
     * Executes parent method parent::render(), generates menu HTML code,
     * passes data to Smarty engine, returns name of template file "nav_frame.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $myUtilsServer = oxRegistry::get("oxUtilsServer");

        $sItem = oxConfig::getParameter("item");
        $sItem = $sItem ? basename( $sItem ) : false;
        if ( !$sItem ) {
            $sItem = "nav_frame.tpl";
            $aFavorites = oxConfig::getParameter( "favorites" );
            if ( is_array( $aFavorites ) ) {
                $myUtilsServer->setOxCookie('oxidadminfavorites', implode( '|', $aFavorites ) );
            }
        } else {
            $oNavTree = $this->getNavigation();

            // set menu structure
            $this->_aViewData["menustructure"] = $oNavTree->getDomXml()->documentElement->childNodes;

            // version patch strin
            $sVersion = str_replace( array ("EE.", "PE."), "", $this->_sShopVersion);
            $this->_aViewData["sVersion"] = trim($sVersion);

            //checking requirements if this is not nav frame reload
            if ( !oxConfig::getParameter( "navReload" ) ) {
                // #661 execute stuff we run each time when we start admin once
                if ('home.tpl' == $sItem) {
                    $this->_aViewData['aMessage'] = $this->_doStartUpChecks();
                }
            } else {
                //removing reload param to force requirements checking next time
                oxSession::deleteVar("navReload");
            }

            // favorite navigation
            $aFavorites = explode( '|', $myUtilsServer->getOxCookie( 'oxidadminfavorites' ) );

            if ( is_array( $aFavorites ) && count( $aFavorites ) ) {
                $this->_aViewData["menufavorites"] = $oNavTree->getListNodes( $aFavorites );
                $this->_aViewData["aFavorites"]    = $aFavorites;
            }

            // history navigation
            $aHistory = explode( '|', $myUtilsServer->getOxCookie( 'oxidadminhistory' ) );
            if ( is_array( $aHistory ) && count( $aHistory ) ) {
                $this->_aViewData["menuhistory"] = $oNavTree->getListNodes( $aHistory );
            }

            // open history node ?
            $this->_aViewData["blOpenHistory"] = oxConfig::getParameter( 'openHistory' );
        }

        $oShoplist = oxNew( 'oxshoplist' );
        $oBaseShop = $oShoplist->getBaseObject();

        $sWhere = '';
        $blisMallAdmin = oxSession::getVar( 'malladmin' );
        if (!$blisMallAdmin) {
            // we only allow to see our shop
            $sShopID = oxSession::getVar("actshop");
            $sWhere = "where ".$oBaseShop->getViewName().".oxid = '$sShopID'";
        }

        $oShoplist->selectString("select ".$oBaseShop->getSelectFields()." from ".$oBaseShop->getViewName()." $sWhere");
        $this->_aViewData['shoplist'] = $oShoplist;

        return $sItem;
    }

    /**
     * Changing active shop
     *
     * @return string
     */
    public function chshp()
    {
        parent::chshp();

        // informing about basefrm parameters
        $this->_aViewData['loadbasefrm'] = true;
        $sListView = oxConfig::getParameter( 'listview' );
        $sEditView = oxConfig::getParameter( 'editview' );
        $iActEdit  = oxConfig::getParameter( 'actedit' );


        $this->_aViewData['listview'] = $sListView;
        $this->_aViewData['editview'] = $sEditView;
        $this->_aViewData['actedit']  = $iActEdit;
    }

    /**
     * Destroy session, redirects to admin login and clears cache
     *
     * @return null
     */
    public function logout()
    {
        $mySession = $this->getSession();
        $myConfig = $this->getConfig();

        $oUser = oxNew("oxuser");
        $oUser->logout();

        // kill session
        $mySession->destroy();

        // delete also, this is usually not needed but for security reasons we execute still
        if ( $myConfig->getConfigParam('blAdodbSessionHandler' ) ) {
            $oDb = oxDb::getDb();
            $oDb->execute("delete from oxsessions where SessionID = ".$oDb->quote($mySession->getId()));
        }

        //reseting content cache if needed
        if ( $myConfig->getConfigParam('blClearCacheOnLogout' ) ) {
            $this->resetContentCache();
        }

        oxRegistry::getUtils()->redirect( 'index.php', true, 302 );
    }

    /**
     * Caches external url file locally, adds <base> tag with original url to load images and other links correcly
     *
     * @return null
     */
    public function exturl()
    {
        $myUtils = oxRegistry::getUtils();
        if ( $sUrl = oxConfig::getParameter( "url" ) ) {

            // Limit external url's only allowed host
            $myConfig = $this->getConfig();
            if ( $myConfig->getConfigParam('blLoadDynContents') && strpos( $sUrl, $this->_sAllowedHost ) === 0 ) {

                $sPath = $myConfig->getConfigParam('sCompileDir') . "/" . md5( $sUrl ) . '.html';
                if ( $myUtils->getRemoteCachePath( $sUrl, $sPath ) ) {

                    $oStr = getStr();
                    $sVersion = $myConfig->getVersion();
                    $sEdition = $myConfig->getFullEdition();
                    $sCurYear = date( "Y" );

                    // Get ceontent
                    $sOutput = file_get_contents( $sPath );

                    // Fix base path
                    $sOutput = $oStr->preg_replace( "/<\/head>/i", "<base href=\"".dirname( $sUrl ).'/'."\"></head>\n  <!-- OXID eShop {$sEdition}, Version {$sVersion}, Shopping Cart System (c) OXID eSales AG 2003 - {$sCurYear} - http://www.oxid-esales.com -->", $sOutput );

                    // Fix self url's
                    $myUtils->showMessageAndExit( $oStr->preg_replace( "/href=\"#\"/i", 'href="javascript::void();"', $sOutput ) );
                }
            } else {
                // Caching not allowed, redirecting
                $myUtils->redirect( $sUrl, true, 302 );
            }
        }

        $myUtils->showMessageAndExit( "" );
    }

    /**
     * Every Time Admin starts we perform these checks
     * returns some messages if there is something to display
     *
     * @return string
     */
    protected function _doStartUpChecks()
    {   // #661
        $aMessage = array ();

        // check if system reguirements are ok
        $oSysReq = new oxSysRequirements();
        if ( !$oSysReq->getSysReqStatus() ) {
            $aMessage['warning']  = oxRegistry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE');
            $aMessage['warning'] .= '<a href="?cl=sysreq&amp;stoken='.$this->getSession()->getSessionChallengeToken().'" target="basefrm">';
            $aMessage['warning'] .= oxRegistry::getLang()->translateString('NAVIGATION_SYSREQ_MESSAGE2').'</a>';
        }

        // version check
        if ( $this->getConfig()->getConfigParam('blCheckForUpdates' ) ) {
            if ( $sVersionNotice = $this->_checkVersion() ) {
                $aMessage['message'] .= $sVersionNotice;
            }
        }


        // check if setup dir is deleted
        if ( file_exists( $this->getConfig()->getConfigParam('sShopDir').'/setup/index.php' ) ) {
            $aMessage['warning'] .= ((! empty($aMessage['warning']))?"<br>":'').oxRegistry::getLang()->translateString('SETUP_DIRNOTDELETED_WARNING');
        }

        // check if updateApp dir is deleted or empty
        $sUpdateDir = $this->getConfig()->getConfigParam( 'sShopDir' ) . '/updateApp/';
        if ( file_exists( $sUpdateDir ) && !(count(glob("$sUpdateDir/*")) === 0) ) {
            $aMessage['warning'] .= ((! empty($aMessage['warning']))?"<br>":'').oxRegistry::getLang()->translateString('UPDATEAPP_DIRNOTDELETED_WARNING');
        }

        // check if config file is writable
        $sConfPath = $this->getConfig()->getConfigParam( 'sShopDir' ) . "/config.inc.php";
        if ( !is_readable( $sConfPath ) || is_writable( $sConfPath ) ) {
            $aMessage['warning'] .= ( ( ! empty($aMessage['warning'] ) )?"<br>":'' ).oxRegistry::getLang()->translateString('SETUP_CONFIGPERMISSIONS_WARNING' );
        }

        return $aMessage;
    }

    /**
     * Checks if newer shop version available. If true - returns message
     *
     * @return string
     */
    protected function _checkVersion()
    {
            $sVersion = 'CE';

        $sQuery = 'http://admin.oxid-esales.com/'.$sVersion.'/onlinecheck.php?getlatestversion';
        if ($sVersion = oxRegistry::get("oxUtilsFile")->readRemoteFileAsString($sQuery)) {
            // current version is older ..
            if (version_compare($this->getConfig()->getVersion(), $sVersion) == '-1') {
                return sprintf(oxRegistry::getLang()->translateString('NAVIGATION_NEWVERSIONAVAILABLE'), $sVersion);
            }
        }
    }
}
