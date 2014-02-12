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
 * Newsletter opt-in/out.
 * Arranges newsletter opt-in form, have some methods to confirm
 * user opt-in or remove user from newsletter list. OXID eShop ->
 * (Newsletter).
 */
class Newsletter extends oxUBase
{
    /**
     * Action articlelist
     * @var object
     */
    protected $_oActionArticles = null;

    /**
     * Top start article
     * @var object
     */
    protected $_oTopArticle = null;

    /**
     * Home country id
     * @var string
     */
    protected $_sHomeCountryId = null;

    /**
     * Newletter status.
     * @var integer
     */
    protected $_iNewsletterStatus = null;

    /**
     * User newsletter registration data.
     * @var object
     */
    protected $_aRegParams = null;

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'page/info/newsletter.tpl';

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Only loads newsletter subscriber data.
     *
     * Template variables:
     * <b>aRegParams</b>
     *
     * @return bool
     */
    public function fill()
    {
        // loads submited values
        $this->_aRegParams = oxConfig::getParameter( "editval" );
    }

    /**
     * Checks for newsletter subscriber data, if OK - creates new user as
     * subscriber or assigns existing user to newsletter group and sends
     * confirmation email.
     *
     * Template variables:
     * <b>success</b>, <b>error</b>, <b>aRegParams</b>
     *
     * @return bool
     */
    public function send()
    {
        $aParams  = oxConfig::getParameter("editval");

        // loads submited values
        $this->_aRegParams = $aParams;

        if ( !$aParams['oxuser__oxusername'] ) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay('ERROR_MESSAGE_COMPLETE_FIELDS_CORRECTLY');
            return;
        } elseif ( !oxRegistry::getUtils()->isValidEmail( $aParams['oxuser__oxusername'] ) ) {
            // #1052C - eMail validation added
            oxRegistry::get("oxUtilsView")->addErrorToDisplay('MESSAGE_INVALID_EMAIL');
            return;
        }

        $blSubscribe = oxConfig::getParameter("subscribeStatus");

        $oUser = oxNew( 'oxuser' );
        $oUser->oxuser__oxusername = new oxField($aParams['oxuser__oxusername'], oxField::T_RAW);

        $blUserLoaded = false;

        // if such user does not exist
        if ( !$oUser->exists() ) {

            // and subscribe is off - error, on - create
            if ( !$blSubscribe ) {

                oxRegistry::get("oxUtilsView")->addErrorToDisplay('NEWSLETTER_EMAIL_NOT_EXIST');
                return;

            } else {
                $oUser->oxuser__oxactive    = new oxField(1, oxField::T_RAW);
                $oUser->oxuser__oxrights    = new oxField('user', oxField::T_RAW);
                $oUser->oxuser__oxshopid    = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
                $oUser->oxuser__oxfname     = new oxField($aParams['oxuser__oxfname'], oxField::T_RAW);
                $oUser->oxuser__oxlname     = new oxField($aParams['oxuser__oxlname'], oxField::T_RAW);
                $oUser->oxuser__oxsal       = new oxField($aParams['oxuser__oxsal'], oxField::T_RAW);
                $oUser->oxuser__oxcountryid = new oxField($aParams['oxuser__oxcountryid'], oxField::T_RAW);
                $blUserLoaded = $oUser->save();
            }

        } else {
            $blUserLoaded = $oUser->load( $oUser->getId() );
        }



        // if user was added/loaded successfully and subscribe is on - subscribing to newsletter
        if ( $blSubscribe && $blUserLoaded ) {
            //removing user from subscribe list before adding
            $oUser->setNewsSubscription( false, false );

            $blOrderOptInEmail = $this->getConfig()->getConfigParam( 'blOrderOptInEmail' );
            if ( $oUser->setNewsSubscription( true, $blOrderOptInEmail ) ) {
                // done, confirmation required?
                if ( $blOrderOptInEmail ) {
                    $this->_iNewsletterStatus = 1;
                } else {
                    $this->_iNewsletterStatus = 2;
                }
            } else {
                oxRegistry::get("oxUtilsView")->addErrorToDisplay('MESSAGE_NOT_ABLE_TO_SEND_EMAIL');
            }
        } elseif ( !$blSubscribe && $blUserLoaded ) {
            // unsubscribing user
            $oUser->setNewsSubscription( false, false );
            $this->_iNewsletterStatus = 3;
        }
    }

    /**
     * Loads user and Adds him to newsletter group.
     *
     * Template variables:
     * <b>success</b>
     *
     * @return null
     */
    public function addme()
    {
        // user exists ?
        $oUser = oxNew( 'oxuser' );
        if ( $oUser->load( oxRegistry::getConfig()->getRequestParameter( 'uid' ) ) ) {
            $sConfirmCode = md5($oUser->oxuser__oxusername->value.$oUser->oxuser__oxpasssalt->value);
            // is confirm code ok?
            if ( oxRegistry::getConfig()->getRequestParameter( 'confirm' ) == $sConfirmCode ) {
                $oUser->getNewsSubscription()->setOptInStatus( 1 );
                $oUser->addToGroup( 'oxidnewsletter' );
                $this->_iNewsletterStatus = 2;
            }
        }
    }

    /**
     * Loads user and removes him from newsletter group.
     *
     * @return null
     */
    public function removeme()
    {
        // existing user ?
        $oUser = oxNew( 'oxuser' );
        if ( $oUser->load( oxConfig::getParameter( 'uid' ) ) ) {
            $oUser->getNewsSubscription()->setOptInStatus( 0 );

            // removing from group ..
            if ( !$this->getConfig()->getConfigParam( 'blOrderOptInEmail' ) ) {
                $oUser->removeFromGroup( 'oxidnewsletter' );
            }

            $this->_iNewsletterStatus = 3;
        }
    }

    /**
     * simlink to function removeme bug fix #0002894
     *
     * @return null
     */
    public function rmvm()
    {
        $this->removeme();
    }

    /**
     * Template variable getter. Returns action articlelist
     *
     * @return object
     */
    public function getTopStartActionArticles()
    {
        if ( $this->_oActionArticles === null ) {
            $this->_oActionArticles = false;
            if ( $this->getConfig()->getConfigParam( 'bl_perfLoadAktion' ) ) {
                $oArtList = oxNew( 'oxarticlelist' );
                $oArtList->loadActionArticles( 'OXTOPSTART' );
                if ( $oArtList->count() ) {
                    $this->_oTopArticle     = $oArtList->current();
                    $this->_oActionArticles = $oArtList;
                }
            }
        }
        return $this->_oActionArticles;
    }

    /**
     * Template variable getter. Returns top start article
     *
     * @return object
     */
    public function getTopStartArticle()
    {
        if ( $this->_oTopArticle === null ) {
            $this->_oTopArticle = false;
            if ( $this->getTopStartActionArticles() ) {
                return $this->_oTopArticle;
            }
        }
        return $this->_oTopArticle;
    }

    /**
     * Template variable getter. Returns country id
     *
     * @return string
     */
    public function getHomeCountryId()
    {
        if ( $this->_sHomeCountryId === null ) {
            $this->_sHomeCountryId = false;
            $aHomeCountry = $this->getConfig()->getConfigParam( 'aHomeCountry' );
            if ( is_array( $aHomeCountry ) ) {
                $this->_sHomeCountryId = current( $aHomeCountry );
            }
        }
        return $this->_sHomeCountryId;
    }

    /**
     * Template variable getter. Returns newsletter subscription status
     *
     * @return integer
     */
    public function getNewsletterStatus()
    {
        return $this->_iNewsletterStatus;
    }

    /**
     * Template variable getter. Returns user newsletter registration data
     *
     * @return array
     */
    public function getRegParams()
    {
        return $this->_aRegParams;
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = array();
        $aPath  = array();
        $aPath['title'] = oxRegistry::getLang()->translateString( 'STAY_INFORMED', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = $this->getLink();

        $aPaths[] = $aPath;
        return $aPaths;
    }
}
