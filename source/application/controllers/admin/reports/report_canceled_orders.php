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

if ( !class_exists( 'report_canceled_orders' ) ) {
/**
 * Canceled orders reports class
 * @package admin
 */
class Report_canceled_orders extends report_base
{
    /**
     * Name of template to render
     *
     * @return string
     */
    protected $_sThisTemplate = "report_canceled_orders.tpl";

    /**
     * Checks if db contains data for report generation
     *
     * @return bool
     */
    public function drawReport()
    {
        $oDb = oxDb::getDb();

        $oSmarty    = $this->getSmarty();
        $sTimeFrom = $oDb->quote( date( "Y-m-d H:i:s", strtotime( $oSmarty->_tpl_vars['time_from'] ) ) );
        $sTimeTo   = $oDb->quote( date( "Y-m-d H:i:s", strtotime( $oSmarty->_tpl_vars['time_to'] ) ) );

        // collects sessions what executed 'order' function
        if ( $oDb->getOne( "select 1 from `oxlogs` where oxclass = 'order' and oxfnc = 'execute' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo" ) ) {
            return true;
        }

        // collects sessions what executed order class
        if ( $oDb->getOne( "select 1 from `oxlogs` where oxclass = 'order' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo" ) ) {
            return true;
        }

        // collects sessions what executed payment class
        if ( $oDb->getOne( "select 1 from `oxlogs` where oxclass = 'payment' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo" ) ) {
            return true;
        }

        // collects sessions what executed 'user' class
        if ( $oDb->getOne( "select 1 from `oxlogs` where oxclass = 'user' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo" ) ) {
            return true;
        }

        // collects sessions what executed 'tobasket' function
        if ( $oDb->getOne( "select 1 from `oxlogs` where oxclass = 'basket' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo" ) ) {
            return true;
        }

        // orders made
        if ( $oDb->getOne( "select 1 from oxorder where oxorderdate >= $sTimeFrom and oxorderdate <= $sTimeTo" ) ) {
            return true;
        }
    }

    /**
     * Collects sessions what executed 'order' function
     *
     * @param string $sQ data query
     *
     * @return array
     */
    protected function _collectSessions( $sQ )
    {
        $aTempOrder = array();
        $rs = oxDb::getDb()->execute( $sQ );
        if ( $rs != false && $rs->recordCount() > 0) {
            while ( !$rs->EOF ) {
                $aTempOrder[$rs->fields[1]] = $rs->fields[0];
                $rs->moveNext();
            }
        }
        return $aTempOrder;
    }

    /**
     * collects sessions what executed order class
     *
     * @param string $sQ         data query
     * @param array  $aTempOrder orders
     * @param array  &$aDataX6   data to fill
     * @param bool   $blMonth    if TRUE - for month, if FALSE - for week [true]
     *
     * @return array
     */
    protected function _collectOrderSessions( $sQ, $aTempOrder, &$aDataX6, $blMonth = true )
    {
        // collects sessions what executed order class
        $aTempExecOrdersSessions = array();
        $rs = oxDb::getDb()->execute( $sQ );
        if ($rs != false && $rs->recordCount() > 0) {
            $iFirstWeekDay = $this->getConfig()->getConfigParam( 'iFirstWeekDay' );
            while ( !$rs->EOF ) {
                if ( !isset($aTempOrder[$rs->fields[1]] ) ) {
                    $aTempExecOrdersSessions[$rs->fields[1]] = 1;
                    $sKey = strtotime( $rs->fields[0] );
                    $sKey = $blMonth ? date( "m/Y", $sKey ) : oxRegistry::get("oxUtilsDate")->getWeekNumber( $iFirstWeekDay, $sKey );
                    if ( isset( $aDataX6[$sKey] ) ) {
                        $aDataX6[$sKey]++;
                    }

                }
                $rs->moveNext();
            }
        }

        return $aTempExecOrdersSessions;
    }

    /**
     * collects sessions what executed payment class
     *
     * @param string $sQ                      data query
     * @param array  $aTempOrder              orders
     * @param array  $aTempExecOrdersSessions finished orders
     * @param array  &$aDataX2                data to fill
     * @param bool   $blMonth                 if TRUE - for month, if FALSE - for week [true]
     *
     * @return array
     */
    protected function _collectPaymentSessions( $sQ, $aTempOrder, $aTempExecOrdersSessions, &$aDataX2, $blMonth = true )
    {
        $aTempPaymentSessions = array();
        $rs = oxDb::getDb()->execute( $sQ );
        if ( $rs != false && $rs->recordCount() > 0 ) {
            $iFirstWeekDay = $this->getConfig()->getConfigParam( 'iFirstWeekDay' );
            while (!$rs->EOF) {
                if ( !isset( $aTempOrder[$rs->fields[1]]) && !isset( $aTempExecOrdersSessions[$rs->fields[1]] ) ) {
                    $aTempPaymentSessions[$rs->fields[1]] = 1;
                    $sKey = strtotime( $rs->fields[0] );
                    $sKey = $blMonth ? date( "m/Y", $sKey ) : oxRegistry::get("oxUtilsDate")->getWeekNumber( $iFirstWeekDay, $sKey);
                    if ( isset($aDataX2[$sKey]) ) {
                        $aDataX2[$sKey]++;
                    }
                }
                $rs->moveNext();
            }
        }
        return $aTempPaymentSessions;
    }

    /**
     * collects sessions what executed 'user' class
     *
     * @param string $sQ                      data query
     * @param array  $aTempOrder              orders
     * @param array  $aTempExecOrdersSessions finished orders
     * @param array  $aTempPaymentSessions    payment sessions
     * @param array  &$aDataX3                data to fill
     * @param bool   $blMonth                 if TRUE - for month, if FALSE - for week [true]
     *
     * @return array
     */
    protected function _collectUserSessionsForVisitorMonth( $sQ, $aTempOrder, $aTempExecOrdersSessions, $aTempPaymentSessions, &$aDataX3, $blMonth = true )
    {
        $aTempUserSessions = array();
        $rs = oxDb::getDb()->execute( $sQ );
        if ($rs != false && $rs->recordCount() > 0) {
            $iFirstWeekDay = $this->getConfig()->getConfigParam( 'iFirstWeekDay' );
            while (!$rs->EOF) {
                if (!isset($aTempOrder[$rs->fields[1]]) && !isset($aTempPaymentSessions[$rs->fields[1]]) && !isset($aTempExecOrdersSessions[$rs->fields[1]])) {
                    $aTempUserSessions[$rs->fields[1]] = 1;
                    $sKey = strtotime( $rs->fields[0] );
                    $sKey = $blMonth ? date( "m/Y", $sKey ) : oxRegistry::get("oxUtilsDate")->getWeekNumber( $iFirstWeekDay, $sKey);
                    if ( isset($aDataX3[$sKey]) ) {
                        $aDataX3[$sKey]++;
                    }
                }
                $rs->moveNext();
            }
        }

        return $aTempUserSessions;
    }

    /**
     * collects sessions what executed 'tobasket' function
     *
     * @param string $sSql                    data query
     * @param array  $aTempOrder              orders
     * @param array  $aTempExecOrdersSessions finished orders
     * @param array  $aTempPaymentSessions    payment sessions
     * @param array  $aTempUserSessions       user sessions
     * @param array  &$aDataX4                data to fill
     * @param bool   $blMonth                 if TRUE - for month, if FALSE - for week [true]
     *
     * @return array
     */
    protected function _collectToBasketSessions( $sSql, $aTempOrder, $aTempExecOrdersSessions, $aTempPaymentSessions, $aTempUserSessions, &$aDataX4, $blMonth = true )
    {
        $rs = oxDb::getDb()->execute( $sSql);
        if ($rs != false && $rs->recordCount() > 0) {
            $iFirstWeekDay = $this->getConfig()->getConfigParam( 'iFirstWeekDay' );
            while (!$rs->EOF) {
                if ( !$aTempOrder[$rs->fields[1]] && !isset($aTempPaymentSessions[$rs->fields[1]]) && !isset($aTempUserSessions[$rs->fields[1]]) && !isset($aTempExecOrdersSessions[$rs->fields[1]] ) ) {
                    $sKey = strtotime( $rs->fields[0] );
                    $sKey = $blMonth ? date( "m/Y", $sKey ) : oxRegistry::get("oxUtilsDate")->getWeekNumber( $iFirstWeekDay, $sKey );
                    if ( isset($aDataX4[$sKey]) ) {
                        $aDataX4[$sKey]++;
                    }
                }
                $rs->moveNext();
            }
        }
    }

    /**
     * Collects made orders
     *
     * @param string $sSql     data query
     * @param array  &$aDataX5 data to fill
     * @param bool   $blMonth  if TRUE - for month, if FALSE - for week [true]
     *
     * @return array
     */
    protected function _collectOrdersMade( $sSql, &$aDataX5, $blMonth = true )
    {
        $rs = oxDb::getDb()->execute( $sSql );
        if ( $rs != false && $rs->recordCount() > 0 ) {
            $iFirstWeekDay = $this->getConfig()->getConfigParam( 'iFirstWeekDay' );
            while (!$rs->EOF) {
                $sKey = strtotime( $rs->fields[0] );
                $sKey = $blMonth ? date( "m/Y", $sKey ) : oxRegistry::get("oxUtilsDate")->getWeekNumber( $iFirstWeekDay, $sKey );
                if ( isset($aDataX5[$sKey]) ) {
                    $aDataX5[$sKey]++;
                }
                $rs->moveNext();
            }
        }
    }

    /**
     * Collects made orders
     *
     * @param string $sQ       data query
     * @param array  &$aDataX5 data to fill
     *
     * @return array
     */
    protected function _collectOrdersMadeForVisitorWeek( $sQ, &$aDataX5 )
    {
        // orders made
        $rs = oxDb::getDb()->execute( $sQ );
        if ( $rs != false && $rs->recordCount() > 0 ) {
            while ( !$rs->EOF ) {
                $sKey = oxRegistry::get("oxUtilsDate")->getWeekNumber( oxConfig::getConfigParam( 'iFirstWeekDay' ), strtotime( $rs->fields[0] ) );
                if ( isset( $aDataX5[$sKey] ) ) {
                    $aDataX5[$sKey]++;
                }
                $rs->moveNext();
            }
        }
    }

    /**
     * Collects and renders visitor/month report data
     *
     * @return null
     */
    public function visitor_month()
    {
        $myConfig = $this->getConfig();
        $oDb = oxDb::getDb();

        $dTimeTo = strtotime( oxConfig::getParameter( "time_to" ) );
        $sTimeTo = $oDb->quote( date( "Y-m-d H:i:s", $dTimeTo ) );
        $dTimeFrom = mktime( 23, 59, 59, date( "m", $dTimeTo )-12, date( "d", $dTimeTo ), date( "Y", $dTimeTo ) );
        $sTimeFrom = $oDb->quote( date( "Y-m-d H:i:s", $dTimeFrom ) );

        $sSQL = "select oxtime, count(*) as nrof from oxlogs where oxtime >= {$sTimeFrom} and oxtime <= {$sTimeTo} group by oxsessid";

        $aTemp = array();
        for ( $i = 1; $i <= 12; $i++) {
            $aTemp[date( "m/Y", mktime( 23, 59, 59, date( "m", $dTimeFrom ) + $i, date( "d", $dTimeFrom ), date( "Y", $dTimeFrom ) ) ) ] = 0;
        }

        $rs = $oDb->execute( $sSQL );

        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
                $aTemp[date( "m/Y", strtotime( $rs->fields[0] ) )]++;
                $rs->moveNext();
            }
        }

        $aDataX  = $aTemp;
        $aDataY  = array_keys( $aTemp );
        $aDataX2 = $aDataX3 = $aDataX4 = $aDataX5 = $aDataX6 = array_fill_keys( $aDataY, 0 );

        // collects sessions what executed 'order' function
        $sQ = "select oxtime, oxsessid from `oxlogs` where oxclass = 'order' and oxfnc = 'execute' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempOrder = $this->_collectSessions( $sQ );

        // collects sessions what executed order class
        $sQ = "select oxtime, oxsessid from `oxlogs` where oxclass = 'order' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempExecOrdersSessions = $this->_collectOrderSessions( $sQ, $aTempOrder, $aDataX6 );

        // collects sessions what executed payment class
        $sQ = "select oxtime, oxsessid from `oxlogs` where oxclass = 'payment' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempPaymentSessions = $this->_collectPaymentSessions( $sQ, $aTempOrder, $aTempExecOrdersSessions, $aDataX2 );

        // collects sessions what executed 'user' class
        $sQ = "select oxtime, oxsessid from `oxlogs` where oxclass = 'user' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempUserSessions = $this->_collectUserSessionsForVisitorMonth( $sQ, $aTempOrder, $aTempExecOrdersSessions, $aTempPaymentSessions, $aDataX2 );

        // collects sessions what executed 'tobasket' function
        $sQ = "select oxtime, oxsessid from `oxlogs` where oxclass = 'basket' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $this->_collectToBasketSessions( $sQ, $aTempOrder, $aTempExecOrdersSessions, $aTempPaymentSessions, $aTempUserSessions, $aDataX4 );

        // orders made
        $sQ = "select oxorderdate from oxorder where oxorderdate >= $sTimeFrom and oxorderdate <= $sTimeTo order by oxorderdate";
        $this->_collectOrdersMade( $sQ, $aDataX5 );

        header( "Content-type: image/png" );

        // New graph with a drop shadow
        $graph = $this->getGraph( 800, 600 );

        // Description
        $graph->xaxis->setTickLabels( $aDataY );

        // Set title and subtitle
        $graph->title->set( "Monat" );

        // Create the bar plot
        $bplot2 = new BarPlot( array_values( $aDataX2 ) );
        $bplot2->setFillColor( "#9966cc" );
        $bplot2->setLegend( "Best.Abbr. in Bezahlmethoden" );

        // Create the bar plot
        $bplot3 = new BarPlot( array_values( $aDataX3 ) );
        $bplot3->setFillColor( "#ffcc00" );
        $bplot3->setLegend( "Best.Abbr. in Benutzer" );

        // Create the bar plot
        $bplot4 = new BarPlot( array_values( $aDataX4 ) );
        $bplot4->setFillColor( "#6699ff" );
        $bplot4->setLegend( "Best.Abbr. in Warenkorb" );

        // Create the bar plot
        $bplot6 = new BarPlot( array_values( $aDataX6 ) );
        $bplot6->setFillColor( "#ff0099" );
        $bplot6->setLegend( "Best.Abbr. in Bestellbestaetigung" );

        // Create the bar plot
        $bplot5 = new BarPlot( array_values( $aDataX5 ) );
        $bplot5->setFillColor( "silver" );
        $bplot5->setLegend( "Bestellungen" );

        // Create the grouped bar plot
        $gbplot = new groupBarPlot( array( $bplot4, $bplot3, $bplot2, $bplot6, $bplot5 ) );
        $graph->add( $gbplot );

        // Finally output the  image
        $graph->stroke();
    }

    /**
     * Collects and renders visitor/week report data
     *
     * @return null
     */
    public function visitor_week()
    {
        $myConfig = $this->getConfig();
        $oDb = oxDb::getDb();

        $aDataX  = array();
        $aDataX2 = array();
        $aDataX3 = array();
        $aDataX4 = array();
        $aDataX5 = array();
        $aDataX6 = array();
        $aDataY  = array();

        $sTimeTo   = $oDb->quote( date( "Y-m-d H:i:s", strtotime( oxConfig::getParameter( "time_to" ) ) ) );
        $sTimeFrom = $oDb->quote( date( "Y-m-d H:i:s", strtotime( oxConfig::getParameter( "time_from")) ) );

        $sSQL = "select oxtime, count(*) as nrof from oxlogs where oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid order by oxtime";

        $aTemp = array();
        $rs = $oDb->execute( $sSQL);

        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
                $aTemp[oxRegistry::get("oxUtilsDate")->getWeekNumber($myConfig->getConfigParam( 'iFirstWeekDay' ), strtotime( $rs->fields[0]))]++;
                $rs->moveNext();
            }
        }

        // initializing
        list( $iFrom, $iTo ) = $this->getWeekRange();
        for ( $i = $iFrom; $i < $iTo; $i++ ) {
            $aDataX[$i]  = 0;
            $aDataX2[$i] = 0;
            $aDataX3[$i] = 0;
            $aDataX4[$i] = 0;
            $aDataX5[$i] = 0;
            $aDataX6[$i] = 0;
            $aDataY[]    = "KW ".$i;
        }

        foreach ( $aTemp as $key => $value) {
            $aDataX[$key]  = $value;
            $aDataX2[$key] = 0;
            $aDataX3[$key] = 0;
            $aDataX4[$key] = 0;
            $aDataX5[$key] = 0;
            $aDataX6[$key] = 0;
            $aDataY[]      = "KW ".$key;
        }

        // collects sessions what executed 'order' function
        $sQ = "select oxtime, oxsessid FROM `oxlogs` where oxclass = 'order' and oxfnc = 'execute' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempOrder = $this->_collectSessions( $sQ );

        // collects sessions what executed order class
        $sQ = "select oxtime, oxsessid from `oxlogs` where oxclass = 'order' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempExecOrdersSessions = $this->_collectOrderSessions( $sQ, $aTempOrder, $aDataX6, false );

        // collects sessions what executed payment class
        $sQ = "select oxtime, oxsessid from `oxlogs` where oxclass = 'payment' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempPaymentSessions = $this->_collectPaymentSessions( $sQ, $aTempOrder, $aTempExecOrdersSessions, $aDataX2, false );

        // collects sessions what executed 'user' class
        $sQ = "select oxtime, oxsessid from `oxlogs` where oxclass = 'user' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $aTempUserSessions = $this->_collectUserSessionsForVisitorMonth( $sQ, $aTempOrder, $aTempExecOrdersSessions, $aTempPaymentSessions, $aDataX2, false );

        // collects sessions what executed 'tobasket' function
        $sQ = "select oxtime, oxsessid from `oxlogs` where oxclass = 'basket' and oxtime >= $sTimeFrom and oxtime <= $sTimeTo group by oxsessid";
        $this->_collectToBasketSessions( $sQ, $aTempOrder, $aTempExecOrdersSessions, $aTempPaymentSessions, $aTempUserSessions, $aDataX4, false );

        // orders made
        $sQ = "select oxorderdate from oxorder where oxorderdate >= $sTimeFrom and oxorderdate <= $sTimeTo order by oxorderdate";
        $this->_collectOrdersMade( $sQ, $aDataX5, false );

        header( "Content-type: image/png" );

        // New graph with a drop shadow
        $graph = $this->getGraph( max( 800, count( $aDataX ) * 80 ), 600 );

        // Description
        $graph->xaxis->setTickLabels( $aDataY );

        // Set title and subtitle
        $graph->title->set( "Woche" );

        // Create the bar plot
        $bplot2 = new BarPlot( array_values( $aDataX2 ) );
        $bplot2->setFillColor( "#9966cc" );
        $bplot2->setLegend( "Best.Abbr. in Bezahlmethoden" );

        // Create the bar plot
        $bplot3 = new BarPlot( array_values( $aDataX3 ) );
        $bplot3->setFillColor( "#ffcc00" );
        $bplot3->setLegend( "Best.Abbr. in Benutzer" );

        // Create the bar plot
        $bplot4 = new BarPlot( array_values( $aDataX4 ) );
        $bplot4->setFillColor( "#6699ff" );
        $bplot4->setLegend( "Best.Abbr. in Warenkorb" );

        // Create the bar plot
        $bplot6 = new BarPlot( array_values( $aDataX6 ) );
        $bplot6->setFillColor( "#ff0099" );
        $bplot6->setLegend( "Best.Abbr. in Bestellbestaetigung" );

        // Create the bar plot
        $bplot5 = new BarPlot( array_values( $aDataX5 ) );
        $bplot5->setFillColor( "silver" );
        $bplot5->setLegend( "Bestellungen" );

        // Create the grouped bar plot
        $gbplot = new groupBarPlot( array( $bplot4, $bplot3, $bplot2, $bplot6, $bplot5 ) );
        $graph->add( $gbplot );

        // Finally output the  image
        $graph->stroke();
    }
}
}