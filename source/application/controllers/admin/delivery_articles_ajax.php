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
 * Class manages delivery articles
 */
class delivery_articles_ajax extends ajaxListComponent
{
    /**
     * Columns array
     * 
     * @var array 
     */
    protected $_aColumns = array( 'container1' => array(    // field , table,         visible, multilanguage, ident
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
                                        array( 'oxid',     'oxobject2delivery', 0, 0, 1 )
                                        )
                                );

    /**
     * If true extended column selection will be build
     *
     * @var bool
     */
    protected $_blAllowExtColumns = true;

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $myConfig = $this->getConfig();
        $oDb = oxDb::getDb();

        // looking for table/view
        $sArtTable = $this->_getViewName('oxarticles');
        $sCatTable = $this->_getViewName('oxcategories');
        $sO2CView  = $this->_getViewName('oxobject2category');

        $sDelId      = $this->getConfig()->getRequestParameter( 'oxid' );
        $sSynchDelId = $this->getConfig()->getRequestParameter( 'synchoxid' );

        // category selected or not ?
        if ( !$sDelId) {
            // dodger performance
            $sQAdd  = " from $sArtTable where 1 ";
            $sQAdd .= $myConfig->getConfigParam( 'blVariantsSelection' )?'':"and $sArtTable.oxparentid = '' ";
        } else {
            // selected category ?
            if ( $sSynchDelId && $sDelId != $sSynchDelId ) {
                $sQAdd  = " from $sO2CView left join $sArtTable on ";
                $sQAdd .= $myConfig->getConfigParam( 'blVariantsSelection' )?" ( $sArtTable.oxid=$sO2CView.oxobjectid or $sArtTable.oxparentid=$sO2CView.oxobjectid)":" $sArtTable.oxid=$sO2CView.oxobjectid ";
                $sQAdd .= "where $sO2CView.oxcatnid = ". $oDb->quote( $sDelId );
            } else {
                $sQAdd  = ' from oxobject2delivery left join '.$sArtTable.' on '.$sArtTable.'.oxid=oxobject2delivery.oxobjectid ';
                $sQAdd .= 'where oxobject2delivery.oxdeliveryid = '.$oDb->quote( $sDelId ).' and oxobject2delivery.oxtype = "oxarticles" ';
            }
        }

        if ( $sSynchDelId && $sSynchDelId != $sDelId) {
            $sQAdd .= 'and '.$sArtTable.'.oxid not in ( ';
            $sQAdd .= 'select oxobject2delivery.oxobjectid from oxobject2delivery ';
            $sQAdd .= 'where oxobject2delivery.oxdeliveryid = '.$oDb->quote( $sSynchDelId ).' and oxobject2delivery.oxtype = "oxarticles" ) ';
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
    /*protected function _addFilter( $sQ )
    {
        $sArtTable = $this->_getViewName('oxarticles');
        $sQ = parent::_addFilter( $sQ );

        // display variants or not ?
        $sQ .= $this->getConfig()->getConfigParam( 'blVariantsSelection' ) ? ' group by '.$sArtTable.'.oxid ' : '';
        return $sQ;
    }*/

    /**
     * Removes article from delivery configuration
     *
     * @return null
     */
    public function removeArtFromDel()
    {
        $aChosenArt = $this->_getActionIds( 'oxobject2delivery.oxid' );
        // removing all
        if ( $this->getConfig()->getRequestParameter( 'all' ) ) {

            $sQ = parent::_addFilter( "delete oxobject2delivery.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );

        } elseif ( is_array( $aChosenArt ) ) {
            $sQ = "delete from oxobject2delivery where oxobject2delivery.oxid in (" . implode( ", ", oxDb::getInstance()->quoteArray( $aChosenArt ) ) . ") ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

    /**
     * Adds article to delivery configuration
     *
     * @return null
     */
    public function addArtToDel()
    {
        $aChosenArt = $this->_getActionIds( 'oxarticles.oxid' );
        $soxId      = $this->getConfig()->getRequestParameter( 'synchoxid');

        // adding
        if ( $this->getConfig()->getRequestParameter( 'all' ) ) {
            $sArtTable = $this->_getViewName('oxarticles');
            $aChosenArt = $this->_getAll( $this->_addFilter( "select $sArtTable.oxid ".$this->_getQuery() ) );
        }

        if ( $soxId && $soxId != "-1" && is_array( $aChosenArt ) ) {
            foreach ( $aChosenArt as $sChosenArt) {
                $oObject2Delivery = oxNew( 'oxbase' );
                $oObject2Delivery->init( 'oxobject2delivery' );
                $oObject2Delivery->oxobject2delivery__oxdeliveryid = new oxField($soxId);
                $oObject2Delivery->oxobject2delivery__oxobjectid   = new oxField($sChosenArt);
                $oObject2Delivery->oxobject2delivery__oxtype       = new oxField("oxarticles");
                $oObject2Delivery->save();
            }
        }
    }
}
