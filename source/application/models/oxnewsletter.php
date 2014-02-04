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
 * Newsletter manager.
 * Performs creation of newsletter text, assign newsletter to user groups,
 * deletes and etc.
 *
 * @package model
 */
class oxNewsletter extends oxBase
{
    /**
     * Newsletter HTML format text (default null).
     *
     * @var string
     */
    protected $_sHtmlText = null;

    /**
     * Newsletter plaintext format text (default null).
     *
     * @var string
     */
    protected $_sPlainText = null;

    /**
     * User groups object (default null).
     *
     * @var object
     */
    protected $_oGroups = null;

    /**
     * User session object (default null).
     *
     * @var object
     */
    protected $_oUser = null;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxnewsletter';

    /**
     * Class constructor, initiates Smarty engine object, parent constructor
     * (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxnewsletter' );
    }

    /**
     * Deletes object information from DB, returns true on success.
     *
     * @param string $sOxId object ID (default null)
     *
     * @return bool
     */
    public function delete( $sOxId = null )
    {
        if ( !$sOxId) {
            $sOxId = $this->getId();
        }
        if ( !$sOxId) {
            return false;
        }

        $blDeleted = parent::delete( $sOxId );

        if ( $blDeleted ) {
            $oDb = oxDb::getDb();
            $sDelete = "delete from oxobject2group where oxobject2group.oxshopid = '".$this->getShopId()."' and oxobject2group.oxobjectid = ".$oDb->quote( $sOxId );
            $oDb->execute( $sDelete );
        }

        return $blDeleted;
    }

    /**
     * Returns assigned user groups list object
     *
     * @return object $_oGroups
     */
    public function getGroups()
    {
        if ( isset( $this->_oGroups ) ) {
            return $this->_oGroups;
        }

        // usergroups
        $this->_oGroups = oxNew( "oxList", "oxgroups" );
        $sViewName = getViewName( "oxgroups" );

        // performance
        $sSelect = "select {$sViewName}.* from {$sViewName}, oxobject2group
                    where oxobject2group.oxobjectid='".$this->getId()."'
                    and oxobject2group.oxgroupsid={$sViewName}.oxid ";
        $this->_oGroups->selectString( $sSelect );

        return $this->_oGroups;
    }

    /**
     * Returns assigned HTML text
     *
     * @return string
     */
    public function getHtmlText()
    {
        return $this->_sHtmlText;
    }

    /**
     * Returns assigned plain text
     *
     * @return string
     */
    public function getPlainText()
    {
        return $this->_sPlainText;
    }

    /**
     * Creates oxshop object and sets base parameters (such as currency and
     * language).
     *
     * @param string $sUserid          User ID or OBJECT
     * @param bool   $blPerfLoadAktion perform option load actions
     *
     * @return null
     */
    public function prepare( $sUserid, $blPerfLoadAktion = false )
    {
        // switching off admin
        $blAdmin = $this->isAdmin();
        $this->setAdminMode( false );

        // add currency
        $this->_setUser( $sUserid );
        $this->_setParams( $blPerfLoadAktion );

        // restoring mode ..
        $this->setAdminMode( $blAdmin );
    }

    /**
     * Creates oxemail object, calls mail sending function (oxEMail::sendNewsletterMail()
     * (#2542 added subject field)),
     * returns true on success.
     *
     * @return bool
     */
    public function send()
    {
        $oxEMail = oxNew( 'oxemail' );
        $blSend = $oxEMail->sendNewsletterMail( $this, $this->_oUser, $this->oxnewsletter__oxsubject->value );

        return $blSend;
    }

    /**
     * Assigns to Smarty oxuser object, add newsletter products,
     * adds products which fit to the last order of
     * this user, generates HTML and plaintext format newsletters.
     *
     * @param bool $blPerfLoadAktion perform option load actions
     *
     * @return null
     */
    protected function _setParams( $blPerfLoadAktion = false )
    {
        $myConfig = $this->getConfig();

        $oShop = oxNew( 'oxshop' );
        $oShop->load( $myConfig->getShopId() );

        $oView = oxNew( 'oxubase' );
        $oShop = $oView->addGlobalParams( $oShop );

        $oView->addTplParam( 'myshop', $oShop );
        $oView->addTplParam( 'shop', $oShop );
        $oView->addTplParam( 'oViewConf', $oShop );
        $oView->addTplParam( 'oView', $oView );
        $oView->addTplParam( 'mycurrency', $myConfig->getActShopCurrencyObject() );
        $oView->addTplParam( 'myuser', $this->_oUser );

        $this->_assignProducts( $oView, $blPerfLoadAktion );

        $aInput[] = array( $this->getId().'html', $this->oxnewsletter__oxtemplate->value );
        $aInput[] = array( $this->getId().'plain', $this->oxnewsletter__oxplaintemplate->value );
        $aRes = oxRegistry::get("oxUtilsView")->parseThroughSmarty( $aInput, null, $oView, true );

        $this->_sHtmlText  = $aRes[0];
        $this->_sPlainText = $aRes[1];
    }

    /**
     * Creates oxuser object (user ID passed to method),
     *
     * @param string $sUserid User ID or OBJECT
     *
     * @return null
     */
    protected function _setUser( $sUserid )
    {
        if ( is_string( $sUserid )) {
            $oUser = oxNew( 'oxuser' );
            if ( $oUser->load( $sUserid ) ) {
                $this->_oUser = $oUser;
            }
        } else {
            $this->_oUser = $sUserid;   // we expect a full and valid user object
        }
    }

    /**
     * Add newsletter products (#559 only if we have user we can assign this info),
     * adds products which fit to the last order of assigned user.
     *
     * @param oxview $oView            view object to store view data
     * @param bool   $blPerfLoadAktion perform option load actions
     *
     * @return null
     */
    protected function _assignProducts( $oView, $blPerfLoadAktion = false )
    {
        if ( $blPerfLoadAktion ) {
            $oArtList = oxNew( 'oxarticlelist' );
            $oArtList->loadActionArticles( 'OXNEWSLETTER' );
            $oView->addTplParam( 'articlelist', $oArtList );
        }

        if ( $this->_oUser->getId() ) {
            $oArticle = oxNew( 'oxarticle' );
            $sArticleTable = $oArticle->getViewName();

            // add products which fit to the last order of this user
            $sSelect  = "select $sArticleTable.* from oxorder left join oxorderarticles on oxorderarticles.oxorderid = oxorder.oxid";
            $sSelect .= " left join $sArticleTable on oxorderarticles.oxartid = $sArticleTable.oxid";
            $sSelect .= " where ".$oArticle->getSqlActiveSnippet();
            $sSelect .= " and oxorder.oxuserid = '".$this->_oUser->getId()."' order by oxorder.oxorderdate desc limit 1";

            if ( $oArticle->assignRecord( $sSelect ) ) {
                $oSimList = $oArticle->getSimilarProducts();
                if ( $oSimList && $oSimList->count() ) {
                    $oView->addTplParam( 'simlist', $oSimList );
                    $iCnt = 0;
                    foreach ( $oSimList as $oArt ) {
                        $oView->addTplParam( "simarticle$iCnt", $oArt );
                        $iCnt++;
                    }
                }
            }
        }
    }

    /**
     * Sets data field value
     *
     * @param string $sFieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $sValue     value of data field
     * @param int    $iDataType  field type
     *
     * @return null
     */
    protected function _setFieldData( $sFieldName, $sValue, $iDataType = oxField::T_TEXT )
    {
        if ( 'oxtemplate' === $sFieldName || 'oxplaintemplate' === $sFieldName ) {
            $iDataType = oxField::T_RAW;
        }
        return parent::_setFieldData($sFieldName, $sValue, $iDataType);
    }
}
