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
 * Class manages statistics configuration
 */
class statistic_main_ajax extends ajaxListComponent
{
    /**
     * Columns array
     * 
     * @var array 
     */
    protected $_aColumns = array( 'container1' => array(    // field , table,         visible, multilanguage, ident
                                        array( 'oxtitle', 'oxstat', 1, 0, 0 ),
                                        array( 'oxid',    'oxstat', 0, 0, 1 )
                                        ),
                                    'container2' => array(
                                        array( 'oxtitle',  'oxstat', 1, 0, 0 ),
                                        array( 'oxid',    'oxstat', 0, 0, 1 )
                                        )
                                );

    /**
     * Formats and returns statiistics configuration related data array for ajax response
     *
     * @param string $sCountQ this param currently is not used as thsi mathod overrides default function behaviour
     * @param string $sQ      this param currently is not used as thsi mathod overrides default function behaviour
     *
     * @return array
     */
    protected function _getData( $sCountQ, $sQ )
    {
        $aResponse['startIndex'] = $this->_getStartIndex();
        $aResponse['sort'] = '_' . $this->_getSortCol();
        $aResponse['dir']  = $this->_getSortDir();

        // all possible reports
        $aReports = oxSession::getVar( "allstat_reports" );
        $sSynchId = oxConfig::getParameter( "synchoxid" );
        $sOxId    = oxConfig::getParameter( "oxid" );

        $sStatId = $sSynchId?$sSynchId:$sOxId;
        $oStat = oxNew( 'oxstatistic' );
        $oStat->load( $sStatId );
        $aStatData = unserialize( $oStat->oxstatistics__oxvalue->value );

        $aData = array();
        $iCnt = 0;
        $oStr = getStr();

        // filter data
        $aFilter = oxConfig::getParameter( "aFilter" );
        $sFilter = (is_array( $aFilter ) && isset( $aFilter['_0'] ) )? $oStr->preg_replace( '/^\*/', '%', $aFilter['_0'] ) : null;

        foreach ( $aReports as $oReport ) {

            if ( $sSynchId ) {
                if ( is_array($aStatData) && in_array( $oReport->filename, $aStatData ) )
                    continue;
            } else {
                if ( !is_array( $aStatData ) ||  !in_array( $oReport->filename, $aStatData ) )
                    continue;
            }

            // checking filter
            if ( $sFilter && !$oStr->preg_match( "/^" . preg_quote( $sFilter ) . "/i", $oReport->name) ) {
                continue;
            }

            $aData[$iCnt]['_0'] = $oReport->name;
            $aData[$iCnt]['_1'] = $oReport->filename;
            $iCnt++;
        }

        // ordering ...
        if ( oxConfig::getParameter( "dir" ) ) {
            if ( 'asc' == oxConfig::getParameter( "dir" ) )
                usort( $aData, array( $this, "sortAsc" ) );
            else
                usort( $aData, array( $this, "sortDesc" ) );
        } else {
            usort( $aData, array( $this, "sortAsc" ) );
        }

        $aResponse['records'] = $aData;
        $aResponse['totalRecords'] = count( $aReports );

        return $aResponse;


    }

    /**
     * Callback function used to apply ASC sorting
     *
     * @param array $oOne first item to check sorting
     * @param array $oSec second item to check sorting
     *
     * @return int
     */
    public function sortAsc( $oOne, $oSec )
    {
        if ( $oOne['_0'] == $oSec['_0'] ) {
            return 0;
        }
        return ( $oOne['_0'] < $oSec['_0'] ) ? -1 : 1;
    }

    /**
     * Callback function used to apply ASC sorting
     *
     * @param array $oOne first item to check sorting
     * @param array $oSec second item to check sorting
     *
     * @return int
     *
     */
    public function sortDesc( $oOne, $oSec )
    {
        if ( $oOne['_0'] == $oSec['_0'] ) {
            return 0;
        }
        return ( $oOne['_0'] > $oSec['_0'] ) ? -1 : 1;
    }


    /**
     * Removes selected report(s) from generating list.
     *
     * @return null
     */
    public function removeReportFromList()
    {
        $aReports = oxSession::getVar( "allstat_reports" );
        $soxId    = oxConfig::getParameter( 'oxid');

        // assigning all items
        if ( oxConfig::getParameter( 'all' ) ) {
            $aStats = array();
            foreach ( $aReports as $oRep ) {
                $aStats[] = $oRep->filename;
            }
        } else {
            $aStats = $this->_getActionIds( 'oxstat.oxid' );
        }

        $oStat = oxNew( 'oxstatistic' );
        if ( is_array( $aStats ) && $oStat->load( $soxId ) ) {
            $aStatData = $oStat->getReports();

            // additional check
            foreach ( $aReports as $oRep ) {
                if ( in_array( $oRep->filename, $aStats ) && ($iPos = array_search( $oRep->filename, $aStatData ) ) !== false )
                    unset( $aStatData[$iPos] );
            }

            $oStat->setReports( $aStatData );
            $oStat->save();
        }
    }

    /**
     * Adds selected report(s) to generating list.
     *
     * @return null
     */
    public function addReportToList()
    {
        $aReports = oxSession::getVar( "allstat_reports" );
        $soxId    = oxConfig::getParameter( 'synchoxid' );

        // assigning all items
        if ( oxConfig::getParameter( 'all' ) ) {
            $aStats = array();
            foreach ( $aReports as $oRep ) {
                $aStats[] = $oRep->filename;
            }
        } else {
            $aStats = $this->_getActionIds( 'oxstat.oxid' );
        }

        $oStat = oxNew( 'oxstatistic' );
        if ( $oStat->load( $soxId ) ) {
            $aStatData = (array) $oStat->getReports();


            // additional check
            foreach ( $aReports as $oRep ) {
                if ( in_array( $oRep->filename, $aStats ) && !in_array( $oRep->filename, $aStatData ) )
                    $aStatData[] = $oRep->filename;
            }

            $oStat->setReports( $aStatData );
            $oStat->save();
        }
    }
}
