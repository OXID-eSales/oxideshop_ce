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
 * Class manages category articles order
 */
class category_order_ajax extends ajaxListComponent
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = array( 'container1' => array(    // field , table,         visible, multilanguage, ident
                                        array( 'oxartnum', 'oxarticles', 1, 0, 0 ),
                                        array( 'oxtitle',  'oxarticles', 1, 1, 0 ),
                                        array( 'oxpos',    'oxobject2category', 1, 0, 0 ),
                                        array( 'oxean',    'oxarticles', 0, 0, 0 ),
                                        array( 'oxmpn',    'oxarticles', 0, 0, 0 ),
                                        array( 'oxprice',  'oxarticles', 0, 0, 0 ),
                                        array( 'oxstock',  'oxarticles', 0, 0, 0 ),
                                        array( 'oxid',     'oxarticles', 0, 0, 1 )
                                        ),
                                'container2' => array(
                                        array( 'oxartnum', 'oxarticles', 1, 0, 0 ),
                                        array( 'oxtitle',  'oxarticles', 1, 1, 0 ),
                                        array( 'oxean',    'oxarticles', 0, 0, 0 ),
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
        $sArtTable = $this->_getViewName( 'oxarticles' );
        $sO2CView  = $this->_getViewName( 'oxobject2category' );
        $oDb = oxDb::getDb();

        // category selected or not ?
        if ( $sSynchOxid  = oxConfig::getParameter( 'synchoxid' ) ) {
            $sQAdd  = " from $sArtTable left join $sO2CView on $sArtTable.oxid=$sO2CView.oxobjectid where $sO2CView.oxcatnid = ".$oDb->quote( $sSynchOxid );
            if ( $aSkipArt = oxSession::getVar( 'neworder_sess' ) ) {
                $sQAdd .= " and $sArtTable.oxid not in ( ".implode( ", ", oxDb::getInstance()->quoteArray( $aSkipArt ) )." ) ";
            }
        } else {
            // which fields to load ?
            $sQAdd  = " from $sArtTable where ";
            if ( $aSkipArt = oxSession::getVar( 'neworder_sess') ) {
                $sQAdd .= " $sArtTable.oxid in ( ".implode( ", ", oxDb::getInstance()->quoteArray( $aSkipArt ) )." ) ";
            } else {
                $sQAdd .= " 1 = 0 ";
            }
        }

        return $sQAdd;
    }

    /**
     * Returns SQL query addon for sorting
     *
     * @return string
     */
    protected function _getSorting()
    {
        $sOrder = '';
        if ( oxConfig::getParameter( 'synchoxid' ) ) {
            $sOrder = parent::_getSorting();
        } elseif ( ( $aSkipArt = oxSession::getVar( 'neworder_sess' ) ) ) {
            $sOrderBy  = '';
            $sArtTable = $this->_getViewName( 'oxarticles' );
            $sSep = '';
            foreach ( $aSkipArt as $sId ) {
                $sOrderBy = " $sArtTable.oxid=" . oxDb::getDb()->quote( $sId ) . " ".$sSep.$sOrderBy;
                $sSep = ", ";
            }
            $sOrder = "order by ".$sOrderBy;
        }

        return $sOrder;
    }

    /**
     * Removes article from list for sorting in category
     *
     * @return string
     */
    public function removeCatOrderArticle()
    {
        $aRemoveArt = $this->_getActionIds( 'oxarticles.oxid' );
        $soxId      = oxConfig::getParameter( 'oxid' );
        $aSkipArt   = oxSession::getVar( 'neworder_sess' );

        if ( is_array( $aRemoveArt ) && is_array( $aSkipArt  ) ) {
            foreach ( $aRemoveArt as $sRem ) {
                if ( ( $iKey = array_search( $sRem, $aSkipArt ) ) !== false ) {
                    unset( $aSkipArt[$iKey] );
                }
            }
            oxSession::setVar( 'neworder_sess', $aSkipArt );

            $sArticleTable = $this->_getViewName('oxarticles');
            $sO2CView      = $this->_getViewName('oxobject2category');

            // checking if all articles were moved from one
            $sSelect  = "select 1 from $sArticleTable left join $sO2CView on $sArticleTable.oxid=$sO2CView.oxobjectid ";
            $sSelect .= "where $sO2CView.oxcatnid = '$soxId' and $sArticleTable.oxparentid = '' and $sArticleTable.oxid ";
            $sSelect .= "not in ( ".implode( ", ", oxDb::getInstance()->quoteArray( $aSkipArt ) )." ) ";

            // simply echoing "1" if some items found, and 0 if nothing was found
            echo (int) oxDb::getDb()->getOne( $sSelect, false, false );
        }
    }

    /**
     * Adds article to list for sorting in category
     *
     * @return string
     */
    public function addCatOrderArticle()
    {
        $aAddArticle = $this->_getActionIds( 'oxarticles.oxid' );
        $soxId       = oxConfig::getParameter( 'synchoxid' );

        $aOrdArt = oxSession::getVar( 'neworder_sess' );
        if ( !is_array( $aOrdArt ) )
            $aOrdArt = array();

        $blEnable    = false;

        if ( is_array( $aAddArticle ) ) {
            // storing newly ordered article seq.
            foreach ($aAddArticle as $sAdd) {
                if ( array_search( $sAdd, $aOrdArt ) === false )
                    $aOrdArt[] = $sAdd;
            }
            oxSession::setVar( 'neworder_sess', $aOrdArt );

            $sArticleTable = $this->_getViewName('oxarticles');
            $sO2CView      = $this->_getViewName('oxobject2category');

            // checking if all articles were moved from one
            $sSelect  = "select 1 from $sArticleTable left join $sO2CView on $sArticleTable.oxid=$sO2CView.oxobjectid ";
            $sSelect .= "where $sO2CView.oxcatnid = '$soxId' and $sArticleTable.oxparentid = '' and $sArticleTable.oxid ";
            $sSelect .= "not in ( ".implode( ", ", oxDb::getInstance()->quoteArray( $aOrdArt ) )." ) ";

            // simply echoing "1" if some items found, and 0 if nothing was found
            echo (int) oxDb::getDb()->getOne( $sSelect, false, false );
        }
    }

    /**
     * Saves category articles ordering.
     *
     * @return null
     */
    public function saveNewOrder()
    {
        $oCategory = oxNew( "oxcategory" );
        $sId = oxConfig::getParameter( "oxid" );
        if ( $oCategory->load( $sId ) ) {


            $aNewOrder = oxSession::getVar( "neworder_sess" );
            if ( is_array( $aNewOrder ) && count( $aNewOrder ) ) {
                $sO2CView = $this->_getViewName('oxobject2category');
                $sSelect =  "select * from $sO2CView where $sO2CView.oxcatnid='".$oCategory->getId()."' and $sO2CView.oxobjectid in (".implode( ", ", oxDb::getInstance()->quoteArray( $aNewOrder ) )." )";
                $oList = oxNew( "oxlist" );
                $oList->init( "oxbase", "oxobject2category" );
                $oList->selectString( $sSelect );

                // setting new position
                foreach ( $oList as $oObj ) {
                    if ( ( $iNewPos = array_search( $oObj->oxobject2category__oxobjectid->value, $aNewOrder ) ) !== false ) {
                        $oObj->oxobject2category__oxpos->setValue($iNewPos);
                        $oObj->save();
                    }
                }

                oxSession::setVar( 'neworder_sess', null );
            }


        }
    }

    /**
     * Removes category articles ordering set by saveneworder() method.
     *
     * @return null
     */
    public function remNewOrder()
    {
        $oCategory = oxNew( "oxcategory" );
        $sId = oxConfig::getParameter( "oxid" );
        if ( $oCategory->load( $sId ) ) {


            $oDb = oxDb::getDb();
            $sSelect = "update oxobject2category set oxpos = '0' where oxobject2category.oxcatnid=" . $oDb->quote( $oCategory->getId() );
            $oDb->execute( $sSelect );

            oxSession::setVar( 'neworder_sess', null );

        }
    }
}
