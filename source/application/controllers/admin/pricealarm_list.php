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
 * Admin pricealarm list manager.
 * Performs collection and managing (such as filtering or deleting) function.
 * Admin Menu: Customer News -> pricealarm.
 * @package admin
 */
class PriceAlarm_List extends oxAdminList
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'pricealarm_list.tpl';

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxpricealarm';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = "oxuserid";

    /**
     * Modifying SQL query to load additional article and customer data
     *
     * @param object $oListObject list main object
     *
     * @return string
     */
    protected function _buildSelectString( $oListObject = null )
    {
        $sViewName = getViewName( "oxarticles", (int) $this->getConfig()->getConfigParam( "sDefaultLang" ) );
        $sSql  = "select oxpricealarm.*, {$sViewName}.oxtitle AS articletitle, ";
        $sSql .= "oxuser.oxlname as userlname, oxuser.oxfname as userfname ";
        $sSql .= "from oxpricealarm left join {$sViewName} on {$sViewName}.oxid = oxpricealarm.oxartid ";
        $sSql .= "left join oxuser on oxuser.oxid = oxpricealarm.oxuserid WHERE 1 ";

        return $sSql;
    }

    /**
     * Builds and returns array of SQL WHERE conditions
     *
     * @return array
     */
    public function buildWhere()
    {
        $this->_aWhere = parent::buildWhere();
        $sViewName = getViewName( "oxpricealarm" );
        $sArtViewName = getViewName( "oxarticles" );

        // updating price fields values for correct search in DB
        if ( isset( $this->_aWhere[$sViewName.'.oxprice'] ) ) {
            $sPriceParam = (double) str_replace( array( '%', ',' ), array( '', '.' ), $this->_aWhere[$sViewName.'.oxprice'] );
            $this->_aWhere[$sViewName.'.oxprice'] = '%'. $sPriceParam. '%';
        }

        if ( isset( $this->_aWhere[$sArtViewName.'.oxprice'] ) ) {
            $sPriceParam = (double) str_replace( array( '%', ',' ), array( '', '.' ), $this->_aWhere[$sArtViewName.'.oxprice'] );
            $this->_aWhere[$sArtViewName.'.oxprice'] = '%'. $sPriceParam. '%';
        }


        return $this->_aWhere;
    }

}
