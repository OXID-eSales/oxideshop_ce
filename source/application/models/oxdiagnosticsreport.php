<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

/**
 * Diagnostic tool result report generator
 * Performs version Checker of shop files and generates report file.
 *
 * @package model
 */
class oxDiagnosticsReport {

    /**
     * Edition of THIS OXID eShop - detected automatically
     *
     * @var string
     */
    private $_sEdition = "";

    /**
     * Version of THIS OXID eShop
     *
     * @var string
     */
    private $_sVersion = "";

    /**
     * Revision of THIS OXID eShop
     *
     * @var string
     */
    private $_sRevision = "";

    /**
     * Link to Checker page
     *
     * @var string
     */
    private $_sHomeLink = "";

    /**
     * Setter for home link
     *
     * @param $sLink string
     */
    public function setHomeLink( $sLink )
    {
        if ( !empty( $sLink ) )
        {
            $this->_sHomeLink = $sLink;
        }
    }

    /**
     * home link getter
     *
     * @return string
     */
    public function getHomeLink()
    {
        return $this->_sHomeLink;
    }

    /**
     * Version setter
     *
     * @param $sVersion string
     */
    public function setVersion( $sVersion )
    {
        if ( !empty( $sVersion ) ) {
            $this->_sVersion = $sVersion;
        }
    }

    /**
     * Version getter
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_sVersion;
    }

    /**
     * Edition setter
     *
     * @param $sEdition string
     */
    public function setEdition( $sEdition )
    {
        if ( !empty( $sEdition ) ) {
            $this->_sEdition = $sEdition;
        }
    }

    /**
     * Edition getter
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->_sEdition;
    }

    /**
     * Revision setter
     *
     * @param $sRevision string
     */
    public function setRevision( $sRevision )
    {
        if ( !empty( $sRevision ) ) {
            $this->_sRevision = $sRevision;
        }
    }

    /**
     * Revision getter
     *
     * @return string
     */
    public function getRevision()
    {
        return $this->_sRevision;
    }

    /**
     * File Checker result report generator
     *
     * @return string
     */
    public function getFileCheckerReport()
    {
        $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty();

        $oSmarty->assign( "sVersion", $this->getVersion() );
        $oSmarty->assign( "sEdition", $this->getEdition() );
        $oSmarty->assign( "sRevision", $this->getRevision() );
        $oSmarty->assign( "sVersionTag",  $this->getEdition() ."_". $this->getVersion() ."_". $this->getRevision() );
        $oSmarty->assign( "aResultSummary", $this->_oFileCheckerResult->getResultSummary() );
        $oSmarty->assign( "aResultOutput", $this->_oFileCheckerResult->getResult() );
        $oSmarty->assign( "sDateTime", date( oxRegistry::getLang()->translateString( 'fullDateFormat' ), time() ) );
        $oSmarty->assign( "sSelfLink", $this->getHomeLink() );

        $sBody = $oSmarty->fetch( "version_checker_result.tpl" );

        return $sBody;
    }
}