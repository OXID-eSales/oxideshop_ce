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
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

/**
 * Content list manager.
 * Collects list of content
 * @package core
 */
class oxContentList extends oxList
{
    /**
     * Class constructor, initiates parent constructor (parent::oxList()).
     *
     * @param string $sObjectsInListName optional and not used
     *
     * @return null
     */
    public function __construct( $sObjectsInListName = 'oxcontent' )
    {
        parent::__construct( 'oxcontent');
    }

    /**
     * Loads main menue entries and generates list with links
     *
     * @return null
     */
    public function loadMainMenulist()
    {
        $this->_loadMenue( 1 );
    }

    /**
     * Load Array of Menue items and change keys of aList to catid
     *
     * @return null
     */
    public function loadCatMenues()
    {
        $this->_loadMenue( 2, 'and oxcatid is not null' );
        $aArray = array();

        if ( $this->count() ) {
            foreach ( $this as $oContent ) {
                // add into cattree
                if ( !isset( $aArray[$oContent->oxcontents__oxcatid->value] ) ) {
                    $aArray[$oContent->oxcontents__oxcatid->value] = array();
                }

                $aArray[$oContent->oxcontents__oxcatid->value][] = $oContent;
            }
        }

        $this->_aArray = $aArray;
    }

    /**
     * Builds and executes the sql string
     *
     * @param int    $iType   oxtype
     * @param string $sSQLAdd assitional sql
     *
     * @return null
     */
    protected function _loadMenue( $iType, $sSQLAdd = null )
    {
        // load them
        $iType = (int)$iType;
        $sViewName = $this->getBaseObject()->getViewName();
        $this->selectString( "select * from {$sViewName} where oxactive = '1' and oxtype = '$iType' and oxsnippet = '0' and oxshopid = '$this->_sShopID' $sSQLAdd order by oxloadid" );
    }
}
