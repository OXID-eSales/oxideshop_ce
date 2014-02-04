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
 * Class manages manufacturer assignment to articles
 */
class manufacturer_main_ajax extends ajaxListComponent
{
    /**
     * If true extended column selection will be build
     *
     * @var bool
     */
    protected $_blAllowExtColumns = true;

    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array( 'container1' => array(    // field , table,       visible, multilanguage, ident
                                        array( 'oxartnum', 'oxarticles', 1, 0, 0 ),
                                        array( 'oxtitle',  'oxarticles', 1, 1, 0 ),
                                        array( 'oxean',    'oxarticles', 1, 0, 0 ),
                                        array( 'oxmpn',    'oxarticles', 0, 0, 0 ),
                                        array( 'oxprice',  'oxarticles', 0, 0, 0 ),
                                        array( 'oxstock',  'oxarticles', 0, 0, 0 ),
                                        array( 'oxid',     'oxarticles', 0, 0, 1 )
                                        ),
                                    'container2' => array(
                                        array( 'oxartnum', 'oxarticles', 1, 0, 0 ),
                                        array( 'oxtitle',  'oxarticles', 1, 1, 0 ),
                                        array( 'oxean',    'oxarticles', 1, 0, 0 ),
                                        array( 'oxmpn',    'oxarticles', 0, 0, 0 ),
                                        array( 'oxprice',  'oxarticles', 0, 0, 0 ),
                                        array( 'oxstock',  'oxarticles', 0, 0, 0 ),
                                        array( 'oxid',     'oxarticles', 0, 0, 1 )
                                        )
                                );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $myConfig = $this->getConfig();

        // looking for table/view
        $sArtTable = $this->_getViewName('oxarticles');
        $sO2CView  = $this->_getViewName('oxobject2category');
        $oDb = oxDb::getDb();

        $sManufacturerId      = $myConfig->getRequestParameter( 'oxid' );
        $sSynchManufacturerId = $myConfig->getRequestParameter( 'synchoxid' );

        // Manufacturer selected or not ?
        if ( !$sManufacturerId ) {
            // dodger performance
            $sQAdd  = ' from '.$sArtTable.' where '.$sArtTable.'.oxshopid="'.$myConfig->getShopId().'" and 1 ';
            $sQAdd .= $myConfig->getRequestParameter( 'blVariantsSelection' ) ?'':" and $sArtTable.oxparentid = '' and $sArtTable.oxmanufacturerid != ".$oDb->quote( $sSynchManufacturerId );
        } elseif ( $sSynchManufacturerId && $sSynchManufacturerId != $sManufacturerId ) {
            // selected category ?
            $sQAdd  = " from $sO2CView left join $sArtTable on ";
            $sQAdd .= $myConfig->getRequestParameter( 'blVariantsSelection' )?" ( $sArtTable.oxid = $sO2CView.oxobjectid or $sArtTable.oxparentid = $sO2CView.oxobjectid )":" $sArtTable.oxid = $sO2CView.oxobjectid ";
            $sQAdd .= 'where '.$sArtTable.'.oxshopid="'.$myConfig->getShopId().'" and '.$sO2CView.'.oxcatnid = '.$oDb->quote( $sManufacturerId ).' and '.$sArtTable.'.oxmanufacturerid != '.$oDb->quote( $sSynchManufacturerId );
            $sQAdd .= $myConfig->getRequestParameter( 'blVariantsSelection' )?'':" and $sArtTable.oxparentid = '' ";
        } else {
            $sQAdd  = " from $sArtTable where $sArtTable.oxmanufacturerid = ".$oDb->quote( $sManufacturerId );
            $sQAdd .= $myConfig->getRequestParameter( 'blVariantsSelection' )?'':" and $sArtTable.oxparentid = '' ";
        }
        return $sQAdd;
    }

    /**
     * Adds filter SQL to current query
     *
     * @param string $sQ query to add filter condition
     *
     * @return string
     */
    protected function _addFilter( $sQ )
    {
        $sArtTable = $this->_getViewName('oxarticles');
        $sQ = parent::_addFilter( $sQ );

        // display variants or not ?
        $sQ .= $this->getConfig()->getRequestParameter( 'blVariantsSelection' ) ? ' group by '.$sArtTable.'.oxid ' : '';
        return $sQ;
    }

    /**
     * Removes article from Manufacturer config
     *
     * @return null
     */
    public function removeManufacturer()
    {
        $myConfig   = $this->getConfig();
        $aRemoveArt = $this->_getActionIds( 'oxarticles.oxid' );
        $sOxid = $myConfig->getRequestParameter( 'oxid' );

        if ( $this->getConfig()->getRequestParameter( "all" ) ) {
            $sArtTable = $this->_getViewName( 'oxarticles' );
            $aRemoveArt = $this->_getAll( $this->_addFilter( "select $sArtTable.oxid ".$this->_getQuery() ) );
        }
        
        if ( is_array(  $aRemoveArt ) && !empty( $aRemoveArt ) ) {
            $sSelect = "update oxarticles set oxmanufacturerid = null where oxid in ( ".implode(", ", oxDb::getInstance()->quoteArray( $aRemoveArt ) ).") ";
            oxDb::getDb()->Execute( $sSelect);

            $this->resetCounter( "manufacturerArticle", $sOxid );
        }
    }

    /**
     * Adds article to Manufacturer config
     *
     * @return null
     */
    public function addManufacturer()
    {
        $myConfig = $this->getConfig();

        $aAddArticle = $this->_getActionIds( 'oxarticles.oxid' );
        $sSynchOxid       = $myConfig->getRequestParameter( 'synchoxid' );

        if ( $myConfig->getRequestParameter( 'all' ) ) {
            $sArtTable = $this->_getViewName( 'oxarticles' );
            $aAddArticle = $this->_getAll( $this->_addFilter( "select $sArtTable.oxid ".$this->_getQuery() ) );
        }

        if ( $sSynchOxid && $sSynchOxid != "-1" && is_array( $aAddArticle ) ) {
            $oDb = oxDb::getDb();
            $sSelect = "update oxarticles set oxmanufacturerid = ".$oDb->quote( $sSynchOxid )." where oxid in ( ".implode(", ", oxDb::getInstance()->quoteArray( $aAddArticle ) )." )";

            $oDb->Execute( $sSelect);
            $this->resetCounter( "manufacturerArticle", $sSynchOxid );
        }
    }
}
