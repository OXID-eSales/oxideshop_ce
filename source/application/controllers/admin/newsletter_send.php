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
 * Newsletter sending manager.
 * Performs sending of newsletter to selected user groups.
 * @package admin
 */
class Newsletter_Send extends Newsletter_Selection
{
    /**
     * Mail sending errors array
     *
     * @var array
     */
    protected $_aMailErrors = array();

    /**
     * Executes parent method parent::render(), creates oxnewsletter object,
     * sends newsletter to users of chosen groups and returns name of template
     * file "newsletter_send.tpl"/"newsletter_done.tpl".
     *
     * @return string
     */
    public function render()
    {
        oxAdminDetails::render();

        // calculating
        $iUserCount = $this->getUserCount();

        $iStart = (int) oxConfig::getParameter( "iStart" );

        $oNewsletter = oxNew( "oxNewsLetter" );
        $oNewsletter->load( $this->getEditObjectId() );
        $oNewsletterGroups = $oNewsletter->getGroups();

        // send emails....
        $oDB = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        $sQGroups = " ( oxobject2group.oxgroupsid in ( ";
        $blSep = false;
        foreach ( $oNewsletterGroups as $sInGroup ) {
            $sSearchKey = $sInGroup->oxgroups__oxid->value;
            if ( $blSep ) {
                $sQGroups .= ",";
            }
            $sQGroups .= $oDB->quote( $sSearchKey );
            $blSep = true;
        }
        $sQGroups .= ") )";

        // no group selected
        if ( !$blSep ) {
            $sQGroups = " oxobject2group.oxobjectid is null ";
        }

        $myConfig  = $this->getConfig();

        $iSendCnt = 0;
        $iMaxCnt  = (int) $myConfig->getConfigParam( 'iCntofMails' );
        $sShopId  = $myConfig->getShopId();

        $sQ = "select oxnewssubscribed.oxuserid, oxnewssubscribed.oxemail, oxnewssubscribed.oxsal,
               oxnewssubscribed.oxfname, oxnewssubscribed.oxlname, oxnewssubscribed.oxemailfailed
               from oxnewssubscribed left join oxobject2group on
               oxobject2group.oxobjectid = oxnewssubscribed.oxuserid where
               ( oxobject2group.oxshopid = '{$sShopId}' or oxobject2group.oxshopid is null ) and
               $sQGroups and oxnewssubscribed.oxdboptin = 1 and oxnewssubscribed.oxshopid = '{$sShopId}'
               group by oxnewssubscribed.oxemail";

        $oRs = $oDB->selectLimit( $sQ, 100, $iStart );
        $blContinue = ( $oRs != false && $oRs->recordCount() > 0 );

        if ( $blContinue ) {
            $blLoadAction = $myConfig->getConfigParam( 'bl_perfLoadAktion' );
            while ( !$oRs->EOF && $iSendCnt < $iMaxCnt ) {

                if ( $oRs->fields['oxemailfailed'] != "1" ) {
                    $sUserId = $oRs->fields['oxuserid'];
                    $iSendCnt++;

                    // must check if such user is in DB
                    if ( !$oDB->getOne( "select oxid from oxuser where oxid = ".$oDB->quote( $sUserId ), false, false ) ) {
                        $sUserId = null;
                    }

                    // #559
                    if ( !isset( $sUserId ) || !$sUserId ) {
                         // there is no user object so we fake one
                        $oUser = oxNew( "oxuser" );
                        $oUser->oxuser__oxusername = new oxField( $oRs->fields['oxemail'] );
                        $oUser->oxuser__oxsal      = new oxField( $oRs->fields['oxsal'] );
                        $oUser->oxuser__oxfname    = new oxField( $oRs->fields['oxfname'] );
                        $oUser->oxuser__oxlname    = new oxField( $oRs->fields['oxlname'] );
                        $oNewsletter->prepare( $oUser, $blLoadAction );
                    } else {
                        $oNewsletter->prepare( $sUserId, $blLoadAction );
                    }

                    if ( $oNewsletter->send( $iSendCnt ) ) {
                         // add user history
                        $oRemark = oxNew( "oxremark" );
                        $oRemark->oxremark__oxtext     = new oxField( $oNewsletter->getPlainText() );
                        $oRemark->oxremark__oxparentid = new oxField( $sUserId );
                        $oRemark->oxremark__oxshopid   = new oxField( $sShopId );
                        $oRemark->oxremark__oxtype     = new oxField( "n" );
                        $oRemark->save();
                    } else {
                        $this->_aMailErrors[] = "problem sending to : ".$oRs->fields['oxemail'];
                    }
                }

                $oRs->moveNext();
                $iStart++;
            }
        }

        $iSend = $iSendCnt + ( ceil( $iStart / $iMaxCnt ) - 1 ) * $iMaxCnt;
        $iSend = $iSend > $iUserCount ? $iUserCount : $iSend;

        $this->_aViewData["iStart"] = $iStart;
        $this->_aViewData["iSend"]  = $iSend;

        // end ?
        if ( $blContinue ) {
            return "newsletter_send.tpl";
        } else {
            $this->resetUserCount();
            return "newsletter_done.tpl";
        }
    }

    /**
     * Returns count of users assigned to active newsletter receiver group
     *
     * @return int
     */
    public function getUserCount()
    {
        $iCnt = oxSession::getVar( "iUserCount" );
        if ( $iCnt === null ) {
            $iCnt = parent::getUserCount();
            oxSession::setVar( "iUserCount", $iCnt );
        }
        return $iCnt;
    }

    /**
     * Resets users count
     *
     * @return null
     */
    public function resetUserCount()
    {
        oxSession::deleteVar( "iUserCount" );
        $this->_iUserCount = null;
    }

    /**
     * Returns newsletter mailing errors
     *
     * @return array
     */
    public function getMailErrors()
    {
        return $this->_aMailErrors;
    }

    /**
     * Overrides parent method to pass referred id
     *
     * @param string $sNode referred id
     *
     * @return null
     */
    protected function _setupNavigation( $sNode )
    {
        $sNode = 'newsletter_list';

        $myAdminNavig = $this->getNavigation();

        // active tab
        $iActTab = 3;

        // tabs
        $this->_aViewData['editnavi'] = $myAdminNavig->getTabs( $sNode, $iActTab );

        // active tab
        $this->_aViewData['actlocation'] = $myAdminNavig->getActiveTab( $sNode, $iActTab );

        // default tab
        $this->_aViewData['default_edit'] = $myAdminNavig->getActiveTab( $sNode, $this->_iDefEdit );

        // passign active tab number
        $this->_aViewData['actedit'] = $iActTab;
    }

    /**
     * Does nothing, called in derived template
     *
     * @return null
     */
    public function getListSorting()
    {
    }
}
