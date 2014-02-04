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
 * Admin order list manager.
 * Performs collection and managing (such as filtering or deleting) function.
 * Admin Menu: Orders -> Display Orders.
 * @package admin
 */
class Order_List extends oxAdminList
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxorder';

    /**
     * Enable/disable sorting by DESC (SQL) (defaultfalse - disable).
     *
     * @var bool
     */
    protected $_blDesc = true;

        /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = "oxorderdate";

    /**
     * Executes parent method parent::render() and returns name of template
     * file "order_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $aFolders = $this->getConfig()->getConfigParam( 'aOrderfolder' );
        $sFolder  = oxConfig::getParameter( "folder" );
        // first display new orders
        if ( !$sFolder && is_array( $aFolders )) {
            $aNames = array_keys( $aFolders );
            $sFolder = $aNames[0];
        }

        $aSearch    = array( 'oxorderarticles' => 'ARTID', 'oxpayments' => 'PAYMENT');
        $sSearch    = oxConfig::getParameter( "addsearch" );
        $sSearchfld = oxConfig::getParameter( "addsearchfld" );

        $this->_aViewData["folder"]       = $sFolder ? $sFolder : -1;
        $this->_aViewData["addsearchfld"] = $sSearchfld ? $sSearchfld : -1;
        $this->_aViewData["asearch"]      = $aSearch;
        $this->_aViewData["addsearch"]    = $sSearch;
        $this->_aViewData["afolder"]      = $aFolders;

        return "order_list.tpl";
    }

    /**
     * Adding folder check
     *
     * @param array  $aWhere  SQL condition array
     * @param string $sqlFull SQL query string
     *
     * @return $sQ
     */
    protected function _prepareWhereQuery( $aWhere, $sqlFull )
    {
        $oDb = oxDb::getDb();
        $sQ = parent::_prepareWhereQuery( $aWhere, $sqlFull );
        $myConfig = $this->getConfig();
        $aFolders = $myConfig->getConfigParam( 'aOrderfolder' );
        $sFolder = oxConfig::getParameter( 'folder' );
        //searchong for empty oxfolder fields
        if ( $sFolder && $sFolder != '-1' ) {
            $sQ .= " and ( oxorder.oxfolder = ".$oDb->quote( $sFolder )." )";
        } elseif ( !$sFolder && is_array( $aFolders ) ) {
            $aFolderNames = array_keys( $aFolders );
            $sQ .= " and ( oxorder.oxfolder = ".$oDb->quote( $aFolderNames[0] )." )";
        }

        return $sQ;
    }

    /**
     * Builds and returns SQL query string. Adds additional order check.
     *
     * @param object $oListObject list main object
     *
     * @return string
     */
    protected function _buildSelectString( $oListObject = null )
    {
        $sSql = parent::_buildSelectString( $oListObject );
        $oDb = oxDb::getDb();

        $sSearch      = oxConfig::getParameter( 'addsearch' );
        $sSearch      = trim( $sSearch );
        $sSearchField = oxConfig::getParameter( 'addsearchfld' );

        if ( $sSearch ) {
            switch ( $sSearchField ) {
            case 'oxorderarticles':
                $sQ = "oxorder left join oxorderarticles on oxorderarticles.oxorderid=oxorder.oxid where ( oxorderarticles.oxartnum like ".$oDb->quote( "%{$sSearch}%" ) ." or oxorderarticles.oxtitle like ".$oDb->quote( "%{$sSearch}%" )." ) and ";
                break;
            case 'oxpayments':
                $sQ = "oxorder left join oxpayments on oxpayments.oxid=oxorder.oxpaymenttype where oxpayments.oxdesc like ".$oDb->quote( "%{$sSearch}%" ) ." and ";
                break;
            default:
                $sQ = "oxorder where oxorder.oxpaid like ".$oDb->quote( "%{$sSearch}%" )." and ";
                break;
            }
            $sSql = str_replace( 'oxorder where', $sQ, $sSql);
        }

        return $sSql;
    }

    /**
     * Cancels order and its order articles
     *
     * @return null
     */
    public function storno()
    {
        $oOrder = oxNew( "oxorder" );
        if ( $oOrder->load( $this->getEditObjectId() ) ) {
            $oOrder->cancelOrder();
        }


        //we call init() here to load list items after sorno()
        $this->init();
    }

    /**
     * Returns sorting fields array
     *
     * @return array
     */
    public function getListSorting()
    {
        $aSorting = parent::getListSorting();
        if ( isset( $aSorting["oxorder"]["oxbilllname"] )) {
            $this->_blDesc = false;
        }
        return $aSorting;
    }
}
