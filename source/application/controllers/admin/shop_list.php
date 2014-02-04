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
 * Admin shop list manager.
 * Performs collection and managing (such as filtering or deleting) function.
 * Admin Menu: Main Menu -> Core Settings.
 * @package admin
 */
class Shop_List extends oxAdminList
{
    /**
     * Forces main frame update is set TRUE
     *
     * @var bool
     */
    protected $_blUpdateMain = false;

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = 'oxname';

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxshop';

    /**
     * Navigation frame reload marker
     *
     * @var bool
     */
    protected $_blUpdateNav = null;

    /**
     * Sets SQL query parameters (such as sorting),
     * executes parent method parent::Init().
     *
     * @return null
     */
    public function init()
    {
        parent::Init();

    }

    /**
     * Executes parent method parent::render() and returns name of template
     * file "shop_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != '-1' && isset( $soxId ) ) {
            // load object
            $oShop = oxNew( 'oxshop' );
            if ( !$oShop->load( $soxId ) ) {
                $soxId = $myConfig->getBaseShopId();
                $oShop->load( $soxId );
            }
            $this->_aViewData['editshop'] = $oShop;
        }

        // default page number 1
        $this->_aViewData['default_edit'] = 'shop_main';
        $this->_aViewData['updatemain']   = $this->_blUpdateMain;

        if ( $this->_aViewData['updatenav'] ) {
            //skipping requirements checking when reloading nav frame
            oxSession::setVar( "navReload", true );
        }

        //making sure we really change shops on low level
        if ( $soxId && $soxId != '-1' ) {
            $myConfig->setShopId( $soxId );
            oxSession::setVar( 'currentadminshop', $soxId );
        }

        return 'shop_list.tpl';
    }

    /**
     * Sets SQL WHERE condition. Returns array of conditions.
     *
     * @return array
     */
    public function buildWhere()
    {
        // we override this to add our shop if we are not malladmin
        $this->_aWhere = parent::buildWhere();
        if ( !oxSession::getVar( 'malladmin' ) ) {
            // we only allow to see our shop
            $this->_aWhere[ getViewName( "oxshops" ) . ".oxid" ] = oxSession::getVar( "actshop" );
        }

        return $this->_aWhere;
    }

}
