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
 * Class manages vendor assignment to articles
 */
class vendor_main_ajax extends ajaxListComponent
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
        // looking for table/view
        $sArtTable = $this->_getViewName('oxarticles');
        $sO2CView  = $this->_getViewName('oxobject2category');
        $oDb = oxDb::getDb();
        $oConfig = oxRegistry::getConfig();
        $sVendorId      = $oConfig->getRequestParameter( 'oxid' );
        $sSynchVendorId = $oConfig->getRequestParameter( 'synchoxid' );

        // vendor selected or not ?
        if ( !$sVendorId ) {
            $sQAdd  = ' from '.$sArtTable.' where '.$sArtTable.'.oxshopid="'.$oConfig->getShopId().'" and 1 ';
            $sQAdd .= $oConfig->getConfigParam( 'blVariantsSelection' ) ?'':" and $sArtTable.oxparentid = '' and $sArtTable.oxvendorid != ".$oDb->quote( $sSynchVendorId );
        } else {
            // selected category ?
            if ( $sSynchVendorId && $sSynchVendorId != $sVendorId ) {
                $sQAdd  = " from $sO2CView left join $sArtTable on ";
                $sQAdd .= $oConfig->getConfigParam( 'blVariantsSelection' )?" ( $sArtTable.oxid = $sO2CView.oxobjectid or $sArtTable.oxparentid = oxobject2category.oxobjectid )":" $sArtTable.oxid = $sO2CView.oxobjectid ";
                $sQAdd .= 'where '.$sArtTable.'.oxshopid="'.$oConfig->getShopId().'" and '.$sO2CView.'.oxcatnid = '.$oDb->quote( $sVendorId ).' and '.$sArtTable.'.oxvendorid != '. $oDb->quote( $sSynchVendorId );
            } else {
                $sQAdd  = " from $sArtTable where $sArtTable.oxvendorid = ".$oDb->quote( $sVendorId );
            }

            $sQAdd .= $oConfig->getConfigParam( 'blVariantsSelection' )?'':" and $sArtTable.oxparentid = '' ";
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
        $sQ .= $this->getConfig()->getConfigParam( 'blVariantsSelection' ) ? ' group by '.$sArtTable.'.oxid ' : '';
        return $sQ;
    }

    /**
     * Removes article from Vendor
     *
     * @return null
     */
    public function removeVendor()
    {
        $oConfig    = $this->getConfig();
        $aRemoveArt = $this->_getActionIds( 'oxarticles.oxid' );

        if ( $oConfig->getRequestParameter( 'all' ) ) {
            $sArtTable = $this->_getViewName( 'oxarticles' );
            $aRemoveArt = $this->_getAll( $this->_addFilter( "select $sArtTable.oxid ".$this->_getQuery() ) );
        }

        if ( is_array(  $aRemoveArt ) ) {
            $sSelect = "update oxarticles set oxvendorid = null where oxid in ( ".implode(", ", oxDb::getInstance()->quoteArray( $aRemoveArt ) ) . ") ";
            oxDb::getDb()->Execute( $sSelect);
            $this->resetCounter( "vendorArticle", $oConfig->getRequestParameter( 'oxid' ) );
        }
    }

    /**
     * Adds article to Vendor config
     *
     * @return null
     */
    public function addVendor()
    {
        $oConfig = $this->getConfig();

        $aAddArticle = $this->_getActionIds( 'oxarticles.oxid' );
        $soxId       = $oConfig->getRequestParameter( 'synchoxid' );

        if ( $oConfig->getRequestParameter( 'all' ) ) {
            $sArtTable = $this->_getViewName( 'oxarticles' );
            $aAddArticle = $this->_getAll( $this->_addFilter( "select $sArtTable.oxid ".$this->_getQuery() ) );
        }

        if ( $soxId && $soxId != "-1" && is_array( $aAddArticle ) ) {
            $oDb = oxDb::getDb();
            $sSelect = "update oxarticles set oxvendorid = ".$oDb->quote( $soxId )." where oxid in ( ".implode(", ", oxDb::getInstance()->quoteArray( $aAddArticle ) )." )";

            $oDb->Execute( $sSelect);
            $this->resetCounter( "vendorArticle", $soxId );
        }
    }
}
