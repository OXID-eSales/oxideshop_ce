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
 * Content list manager.
 * Collects list of content
 *
 * @package model
 */
class oxContentList extends oxList
{
    /**
     * Main menu list type
     *
     * @var int
     */
    const TYPE_MAIN_MENU_LIST = 1;

    /**
     * Main menu list type
     *
     * @var int
     */
    const TYPE_CATEGORY_MENU = 2;

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
        $this->_load( self::TYPE_MAIN_MENU_LIST );
    }

    /**
     * Load Array of Menue items and change keys of aList to catid
     *
     * @return null
     */
    public function loadCatMenues()
    {
        $this->_load( self::TYPE_CATEGORY_MENU );
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
     * Get data from db
     *
     * @param integer $iType - type of content
     *
     * @return array
     */
    protected function _loadFromDb( $iType )
    {
        $sSQLAdd = '';
        if ( $iType == self::TYPE_CATEGORY_MENU ) {
            $sSQLAdd = ' AND `oxcatid` IS NOT NULL';
        }

        $sViewName = $this->getBaseObject()->getViewName();
        $sSql = "SELECT * FROM {$sViewName} WHERE `oxactive` = '1' AND `oxtype` = '$iType' AND `oxsnippet` = '0' AND `oxshopid` = '$this->_sShopID' $sSQLAdd ORDER BY `oxloadid`";
        $aData = oxDb::getDb( oxDb::FETCH_MODE_ASSOC )->getAll( $sSql );

        return $aData;
    }

    /**
     * Load category list data
     *
     * @param integer $iType - type of content
     *
     * @return null
     */
    protected function _load( $iType )
    {

           $aData = $this->_loadFromDb( $iType );

        $this->assignArray( $aData );
    }


}
