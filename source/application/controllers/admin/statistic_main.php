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
 * Admin article main statistic manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Statistics -> Show -> Main.
 * @package admin
 */
class Statistic_Main extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), cretes oxstatistic object,
     * passes it's data to Smarty engine and returns name of template file
     * "statistic_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig  = $this->getConfig();
        $oLang = oxRegistry::getLang();

        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        $aReports = array();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oStat = oxNew( "oxstatistic" );
            $oStat->load( $soxId);

            $aReports = $oStat->getReports();
            $this->_aViewData["edit"] =  $oStat;
        }

        // setting all reports data: check for reports and load them
        $sPath     = getShopBasePath(). "application/controllers/admin/reports";
        $iLanguage = (int) oxConfig::getParameter("editlanguage");
        $aAllreports = array();

        $aReportFiles = glob( $sPath."/*.php" );
        foreach ( $aReportFiles as $sFile ) {
            if ( is_file( $sFile ) && !is_dir( $sFile ) ) {

                $sConst = strtoupper( str_replace( '.php', '', basename( $sFile ) ) );

                // skipping base report class
                if ( $sConst == 'REPORT_BASE') {
                    continue;
                }

                include $sFile;

                $oItem = new stdClass();
                $oItem->filename = basename( $sFile );
                $oItem->name     = $oLang->translateString( $sConst, $iLanguage );
                $aAllreports[]   = $oItem;
            }
        }

        // setting reports data
        oxSession::setVar( "allstat_reports", $aAllreports);
        oxSession::setVar( "stat_reports_$soxId", $aReports);

        // passing assigned reports count
        if ( is_array($aReports) ) {
            $this->_aViewData['ireports'] = count($aReports);
        }

        if ( oxConfig::getParameter("aoc") ) {
            $oStatisticMainAjax = oxNew( 'statistic_main_ajax' );
            $this->_aViewData['oxajax'] = $oStatisticMainAjax->getColumns();

            return "popups/statistic_main.tpl";
        }

        return "statistic_main.tpl";
    }

    /**
     * Saves statistic parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

        // shopid
        $sShopID = oxSession::getVar( "actshop");
        $oStat = oxNew( "oxstatistic" );
        if ( $soxId != "-1")
            $oStat->load( $soxId);
        else
            $aParams['oxstatistics__oxid'] = null;

        $aParams['oxstatistics__oxshopid'] = $sShopID;
        $oStat->assign($aParams);
        $oStat->save();

        // set oxid if inserted
        $this->setEditObjectId( $oStat->getId() );
    }

    /**
     * Performs report generation function (outputs Smarty generated HTML report).
     *
     * @return null
     */
    public function generate()
    {
        $myConfig  = $this->getConfig();

        $soxId = $this->getEditObjectId();

        // load object
        $oStat = oxNew( "oxstatistic" );
        $oStat->load( $soxId );

        $aAllreports = $oStat->getReports();

        $oShop = oxNew( "oxshop" );
        $oShop->load( $myConfig->getShopId());
        $oShop = $this->addGlobalParams( $oShop );

        $sTimeFrom = oxConfig::getParameter( "time_from" );
        $sTimeTo   = oxConfig::getParameter( "time_to" );
        if ( $sTimeFrom && $sTimeTo ) {
            $sTimeFrom = oxRegistry::get("oxUtilsDate")->formatDBDate( $sTimeFrom, true );
            $sTimeFrom = date( "Y-m-d", strtotime( $sTimeFrom ) );
            $sTimeTo = oxRegistry::get("oxUtilsDate")->formatDBDate( $sTimeTo, true );
            $sTimeTo = date( "Y-m-d", strtotime( $sTimeTo ) );
        } else {
            $dDays = oxConfig::getParameter( "timeframe" );
            $dNow  = time();
            $sTimeFrom = date( "Y-m-d", mktime( 0, 0, 0, date( "m", $dNow ), date( "d", $dNow ) - $dDays, date( "Y", $dNow ) ) );
            $sTimeTo   = date( "Y-m-d", time() );
        }

        $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty();
        $oSmarty->assign( "time_from", $sTimeFrom." 23:59:59" );
        $oSmarty->assign( "time_to", $sTimeTo." 23:59:59" );
        $oSmarty->assign( "oViewConf", $this->_aViewData["oViewConf"]);

        echo( $oSmarty->fetch( "report_pagehead.tpl" ) );
        foreach ( $aAllreports as $file ) {
            if ( ( $file = trim( $file ) ) ) {
                $sClassName = str_replace( ".php", "", strtolower( $file ) );

                $oReport = oxNew( $sClassName );
                $oReport->setSmarty( $oSmarty );

                $oSmarty->assign( "oView", $oReport );
                echo( $oSmarty->fetch( $oReport->render() ) );
            }
        }

        oxRegistry::getUtils()->showMessageAndExit( $oSmarty->fetch( "report_bottomitem.tpl" ) );
    }
}
