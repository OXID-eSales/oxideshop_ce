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
 * Encapsulates methods for application initialization.
 */
class oxStart extends oxUBase
{
    /**
     * Initializes globals and environment vars
     *
     * @return null
     */
    public function appInit()
    {
        $this->pageStart();

        if ( 'oxstart' == oxConfig::getParameter( 'cl' )  || $this->isAdmin() )
            return;

    }

    /**
     * Renders error screen
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $sErrorNo = oxConfig::getParameter( 'execerror' );

        $sTemplate = '';



        if ( $sErrorNo == 'unknown' ) {
            $sTemplate = 'message/err_unknown.tpl';
        }

        if ( $sTemplate ) {
            return $sTemplate;
        } else {
            return 'message/err_unknown.tpl';
        }
    }

    /**
     * Creates and starts session object, sets default currency.
     *
     * @return null
     */
    public function pageStart()
    {
        $myConfig  = $this->getConfig();


        $myConfig->setConfigParam( 'iMaxMandates', $myConfig->getConfigParam( 'IMS' ) );
        $myConfig->setConfigParam( 'iMaxArticles', $myConfig->getConfigParam( 'IMA' ) );
    }

    /**
     * Finalizes the script.
     *
     * @return null
     */
    public function pageClose()
    {
        $mySession = $this->getSession();

        if ( isset( $mySession ) ) {
            $mySession->freeze();
        }

        //commit file cache
        oxRegistry::getUtils()->commitFileCache();
    }

    /**
     * Return error number
     *
     * @return integer
     */
    public function getErrorNumber()
    {
        return oxConfig::getParameter( 'errornr' );
    }

}
