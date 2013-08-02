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
 * Diagnostic tool configuration model
 * Stores configuration for shop diagnostics
 *
 * @package model
 */

class oxDiagnostics
{
    /**
     * Array of all files and folders in shop root folder which are to be checked
     *
     * @var array
     */
    private $_aFileCheckerPathList   = array(
                                        'bootstrap.php',
                                        'index.php',
                                        'oxid.php',
                                        'oxseo.php',
                                        'admin/',
                                        'application/',
                                        'bin/',
                                        'core/',
                                        'modules/',
                                        'views/',
                                        //we need here the specific path because we do not want to scan the custom theme folders
                                        'out/basic/',
                                        'out/admin/',
                                        'out/azure/',
                                    );

    /**
     * Array of file extensions which are to be checked
     *
     * @var array
     */
    private $_aFileCheckerExtensionList = array( 'php', 'tpl' );


    /**
     * Setter for list of files and folders to check
     *
     * @param $aPathList array
     */
    public function setFileCheckerPathList( $aPathList )
    {
        $this->_aFileCheckerPathList = $aPathList;
    }

    /**
     * getter for list of files and folders to check
     *
     * @return $this->_aFileCheckerPathList array
     */
    public function getFileCheckerPathList()
    {
        return $this->_aFileCheckerPathList;
    }

    /**
     * Setter for extensions of files to check
     *
     * @param $aExtList array
     */
    public function setFileCheckerExtensionList( $aExtList )
    {
        $this->_aFileCheckerExtensionList = $aExtList;
    }

    /**
     * getter for extensions of files to check
     *
     * @return $this->_aFileCheckerExtensionList array
     */
    public function getFileCheckerExtensionList()
    {
        return $this->_aFileCheckerExtensionList;
    }

}