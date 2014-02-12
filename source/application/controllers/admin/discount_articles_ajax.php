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
 * Class manages discount articles
 */
class discount_articles_ajax extends ajaxListComponent
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
                                        array( 'oxid',     'oxobject2discount', 0, 0, 1 )
                                        )
                                );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $oConfig = $this->getConfig();

        $sArticleTable = $this->_getViewName('oxarticles');
        $sO2CView      = $this->_getViewName('oxobject2category');

        $oDb = oxDb::getDb();
        $sOxid = $oConfig->getRequestParameter( 'oxid' );
        $sSynchOxid = $oConfig->getRequestParameter( 'synchoxid' );

        // category selected or not ?
        if ( !$sOxid && $sSynchOxid ) {
            $sQAdd  = " from $sArticleTable where 1 ";
            $sQAdd .= $oConfig->getConfigParam( 'blVariantsSelection' )?'':"and $sArticleTable.oxparentid = '' ";
        } else {
            // selected category ?
            if ( $sSynchOxid && $sOxid != $sSynchOxid ) {
                $sQAdd  = " from $sO2CView left join $sArticleTable on ";
                $sQAdd .= $oConfig->getConfigParam( 'blVariantsSelection' )?"($sArticleTable.oxid=$sO2CView.oxobjectid or $sArticleTable.oxparentid=$sO2CView.oxobjectid)":" $sArticleTable.oxid=$sO2CView.oxobjectid ";
                $sQAdd .= " where $sO2CView.oxcatnid = ".$oDb->quote( $sOxid )." and $sArticleTable.oxid is not null ";

                // resetting
                $sId = null;
            } else {
                $sQAdd  = " from oxobject2discount, $sArticleTable where $sArticleTable.oxid=oxobject2discount.oxobjectid ";
                $sQAdd .= " and oxobject2discount.oxdiscountid = ".$oDb->quote( $sOxid )." and oxobject2discount.oxtype = 'oxarticles' ";
            }
        }

        if ( $sSynchOxid && $sSynchOxid != $sOxid) {
            // dodger performance
            $sSubSelect .= " select $sArticleTable.oxid from oxobject2discount, $sArticleTable where $sArticleTable.oxid=oxobject2discount.oxobjectid ";
            $sSubSelect .= " and oxobject2discount.oxdiscountid = ".$oDb->quote( $sSynchOxid )." and oxobject2discount.oxtype = 'oxarticles' ";

            if ( stristr( $sQAdd, 'where' ) === false )
                $sQAdd .= ' where ';
            else
                $sQAdd .= ' and ';
            $sQAdd .= " $sArticleTable.oxid not in ( $sSubSelect ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes selected article (articles) from discount list
     *
     * @return null
     */
    public function removeDiscArt()
    {
        $aChosenArt = $this->_getActionIds( 'oxobject2discount.oxid' );


        if ( $this->getConfig()->getRequestParameter( 'all' ) ) {

            $sQ = parent::_addFilter( "delete oxobject2discount.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );

        } elseif ( is_array( $aChosenArt ) ) {
            $sQ = "delete from oxobject2discount where oxobject2discount.oxid in (" . implode( ", ", oxDb::getInstance()->quoteArray( $aChosenArt ) ) . ") ";
            oxDb::getDb()->Execute( $sQ );
        }
    }

    /**
     * Adds selected article (articles) to discount list
     *
     * @return null
     */
    public function addDiscArt()
    {
        $oConfig = $this->getConfig();
        $aChosenArt = $this->_getActionIds( 'oxarticles.oxid' );
        $soxId      = $oConfig->getRequestParameter('synchoxid');


        // adding
        if ( $oConfig->getRequestParameter( 'all' ) ) {
            $sArticleTable = $this->_getViewName('oxarticles');
            $aChosenArt = $this->_getAll( parent::_addFilter( "select $sArticleTable.oxid ".$this->_getQuery() ) );
        }
        if ( $soxId && $soxId != "-1" && is_array( $aChosenArt ) ) {
            foreach ( $aChosenArt as $sChosenArt) {
                $oObject2Discount = oxNew( "oxbase" );
                $oObject2Discount->init( 'oxobject2discount' );
                $oObject2Discount->oxobject2discount__oxdiscountid = new oxField($soxId);
                $oObject2Discount->oxobject2discount__oxobjectid   = new oxField($sChosenArt);
                $oObject2Discount->oxobject2discount__oxtype       = new oxField("oxarticles");
                $oObject2Discount->save();

            }
        }
    }


}
