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
 * Class controls article crossselling configuration
 */
class article_crossselling_ajax extends ajaxListComponent
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
                                        array( 'oxid',     'oxobject2article', 0, 0, 1 )
                                        )
                                );

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $myConfig      = $this->getConfig();
        $sArticleTable = $this->_getViewName( 'oxarticles' );
        $sO2CView      = $this->_getViewName( 'oxobject2category' );

        $sSelId      = oxConfig::getParameter( 'oxid' );
        $sSynchSelId = oxConfig::getParameter( 'synchoxid' );
        $oDb         = oxDb::getDb();

        // category selected or not ?
        if ( !$sSelId ) {
            $sQAdd  = " from $sArticleTable where 1 ";
            $sQAdd .= $myConfig->getConfigParam( 'blVariantsSelection' )? '':" and $sArticleTable.oxparentid = '' ";
        } elseif ( $sSynchSelId && $sSelId != $sSynchSelId ) {
            // selected category ?
            $sQAdd  = " from $sO2CView as oxobject2category left join $sArticleTable on ";
            $sQAdd .= $myConfig->getConfigParam( 'blVariantsSelection' )?" ($sArticleTable.oxid=oxobject2category.oxobjectid or $sArticleTable.oxparentid=oxobject2category.oxobjectid)":" $sArticleTable.oxid=oxobject2category.oxobjectid ";
            $sQAdd .= " where oxobject2category.oxcatnid = " . $oDb->quote( $sSelId ) . " ";
        } elseif ( $myConfig->getConfigParam( 'blBidirectCross' ) ) {
            $sQAdd  = " from oxobject2article ";
            $sQAdd .= " inner join $sArticleTable on ( oxobject2article.oxobjectid = $sArticleTable.oxid ";
            $sQAdd .= " or oxobject2article.oxarticlenid = $sArticleTable.oxid ) ";
            $sQAdd .= " where ( oxobject2article.oxarticlenid = " . $oDb->quote( $sSelId ) . " or oxobject2article.oxobjectid = " . $oDb->quote( $sSelId ) . " ) ";
            $sQAdd .= " and $sArticleTable.oxid != " . $oDb->quote( $sSelId ) . " ";
        } else {
            $sQAdd  = " from oxobject2article left join $sArticleTable on oxobject2article.oxobjectid=$sArticleTable.oxid ";
            $sQAdd .= " where oxobject2article.oxarticlenid = " . $oDb->quote( $sSelId ) . " ";
        }

        if ( $sSynchSelId && $sSynchSelId != $sSelId) {
            if ( $myConfig->getConfigParam( 'blBidirectCross' ) ) {
                $sSubSelect  = "select $sArticleTable.oxid from oxobject2article left join $sArticleTable on (oxobject2article.oxobjectid=$sArticleTable.oxid or oxobject2article.oxarticlenid=$sArticleTable.oxid) ";
                $sSubSelect .= "where (oxobject2article.oxarticlenid = " . $oDb->quote( $sSynchSelId ) . " or oxobject2article.oxobjectid = " . $oDb->quote( $sSynchSelId ) . " )";
            } else {
                $sSubSelect  = "select $sArticleTable.oxid from oxobject2article left join $sArticleTable on oxobject2article.oxobjectid=$sArticleTable.oxid ";
                $sSubSelect .= "where oxobject2article.oxarticlenid = " . $oDb->quote( $sSynchSelId ) . " ";
            }

            $sSubSelect .= " and $sArticleTable.oxid IS NOT NULL ";
            $sQAdd .= " and $sArticleTable.oxid not in ( $sSubSelect ) ";
        }

        // #1513C/#1826C - skip references, to not existing articles
        $sQAdd .= " and $sArticleTable.oxid IS NOT NULL ";

        // skipping self from list
        $sId = ( $sSynchSelId ) ? $sSynchSelId : $sSelId ;
        $sQAdd .= " and $sArticleTable.oxid != " . $oDb->quote( $sId ) . " ";

        return $sQAdd ;
    }

    /**
     * Removing article from corssselling list
     *
     * @return null
     */
    public function removeArticleCross()
    {
        $aChosenArt = $this->_getActionIds( 'oxobject2article.oxid' );
        // removing all
        if ( oxConfig::getParameter( 'all' ) ) {
            $sQ = $this->_addFilter( "delete oxobject2article.* ".$this->_getQuery() );
            oxDb::getDb()->Execute( $sQ );
        } elseif ( is_array( $aChosenArt ) ) {
            $sQ = "delete from oxobject2article where oxobject2article.oxid in (" . implode( ", ", oxDb::getInstance()->quoteArray( $aChosenArt ) ) . ") ";
            oxDb::getDb()->Execute( $sQ );
        }

    }

    /**
     * Adding article to corssselling list
     *
     * @return null
     */
    public function addArticleCross()
    {
        $aChosenArt = $this->_getActionIds( 'oxarticles.oxid' );
        $soxId      = oxConfig::getParameter( 'synchoxid');

        // adding
        if ( oxConfig::getParameter( 'all' ) ) {
            $sArtTable = $this->_getViewName('oxarticles');
            $aChosenArt = $this->_getAll( parent::_addFilter( "select $sArtTable.oxid ".$this->_getQuery() ) );
        }

        $oArticle = oxNew( "oxarticle" );
        if ( $oArticle->load( $soxId) && $soxId && $soxId != "-1" && is_array( $aChosenArt ) ) {
            foreach ( $aChosenArt as $sAdd ) {
                $oNewGroup = oxNew( 'oxbase' );
                $oNewGroup->init( 'oxobject2article' );
                $oNewGroup->oxobject2article__oxobjectid   = new oxField($sAdd);
                $oNewGroup->oxobject2article__oxarticlenid = new oxField($oArticle->oxarticles__oxid->value);
                $oNewGroup->oxobject2article__oxsort       = new oxField(0);
                $oNewGroup->save();
            }

        }
    }
}
