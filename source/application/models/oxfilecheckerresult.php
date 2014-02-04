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
 * File checker result class
 * Structures and keeps the result of shop file check diagnostics
 *
 * @package model
 */

class oxFileCheckerResult {

    /**
     * For result output
     *
     * @var mixed
     */
    protected $_aResult = array();

    /**
     * Counts number of matches for each type of result
     *
     * @var array
     */
    protected $_aResultSummary = array();

    /**
     * If the variable is true, the script will show all files, even they are ok.
     *
     * @var bool
     */
    protected $_blListAllFiles = false;

    /**
     * Object constructor
     */
    public function __construct()
    {
        $this->_aResultSummary['OK'] = 0;
        $this->_aResultSummary['VERSIONMISMATCH'] = 0;
        $this->_aResultSummary['UNKNOWN'] = 0;
        $this->_aResultSummary['MODIFIED'] = 0;
        $this->_aResultSummary['FILES'] = 0;
        $this->_aResultSummary['SHOP_OK'] = true;
    }

    /**
     * Setter for working directory
     *
     * @param $blListAllFiles boolean
     */
    public function setListAllFiles( $blListAllFiles )
    {
        $this->_blListAllFiles = $blListAllFiles;
    }

    /**
     * working directory getter
     *
     * @return boolean
     */
    public function getListAllFiles()
    {
        return $this->_blListAllFiles;
    }

    /**
     * Getter for file checker result
     *
     * @return array
     */
    public function getResult()
    {
        return $this->_aResult;
    }

    /**
     * Getter for file checker result summary
     *
     * @return array
     */
    public function getResultSummary()
    {
        return $this->_aResultSummary;
    }

    /**
     * Methods saves result of one file check and returns updated summary array
     *
     * @param $aResult
     * @return array
     */
    public function addResult( $aResult )
    {
        $this->_aResultSummary['FILES']++;
        $this->_aResultSummary[$aResult['result']]++;

        if ( !$aResult['ok'] ) {
            $this->_aResultSummary['SHOP_OK'] = false;
        }

        if ( ( $aResult['ok'] && $this->getListAllFiles() ) || !$aResult['ok'] ) {
            $this->_aResult[] = $aResult;
        }

        return $this->_aResultSummary;
    }
}