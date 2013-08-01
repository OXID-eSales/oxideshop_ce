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
 * Diagnostic tool.
 * Performs shop diagnostic
 *
 * @package model
 */

class oxDiagnostics
{
    /**
     * error tag
     *
     * @var boolean
     */
    protected $_blError          = false;

    /**
     * error message
     *
     * @var string
     */
    protected $_sErrorMessage   = null;

    /**
     * Array of all files which are to be checked
     *
     * @var array
     */
    private $_aFiles            = array();

    /**
     * Edition of THIS OXID eShop
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
     * For result output
     *
     * @var mixed
     */
    private $_aResult = array();

    /**
     * Counts number of matches for each type of result
     *
     * @var array
     */
    private $_aResultSummary = array();

    /**
     * If the variable is true, the script will show all files, even they are ok.
     *
     * @var bool
     */
    private $_blListAllFiles = false;

    /**
     * directory reader
     *
     * @var mixed
     */
    private $_oDirectoryReader = null;

    /**
     * file checker
     *
     * @var mixed
     */
    private $_oFileChecker = null;

    /**
     * base directory
     *
     * @var mixed
     */
    protected $_sBaseDirectory = '';

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
     * Setter for working directory
     *
     * @param $sDir string
     */
    public function setBaseDirectory( $sDir )
    {
        if ( !empty( $sDir ) ) {
            $this->_sBaseDirectory = $sDir;
        }
    }

    /**
     * working directory getter
     *
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->_sBaseDirectory;
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
     * Getter for result data
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->_aResult;
    }

    /**
     * Getter for result summary data
     *
     * @return mixed
     */
    public function getResultSummary()
    {
        return $this->_aResultSummary;
    }

    /**
     * Error status getter
     *
     * @return string
     */
    public function hasError()
    {
        return $this->_blError;
    }

    /**
     * Error status getter
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_sErrorMessage;
    }

    /**
     * Initializes object
     *
     * @return boolean
     */
    public function init()
    {
        if ( empty( $this->_sBaseDirectory ) ) {
            throw new Exception('Base directory is not set, please use setter setBaseDirectory!' );
            return false;
        }

        if ( empty( $this->_sVersion ) ) {
            throw new Exception('Shop version is not set, please use setter setVersion!' );
            return false;
        }

        if ( empty( $this->_sRevision ) ) {
            throw new Exception('Shop revision is not set, please use setter setRevision!' );
            return false;
        }

        if ( empty( $this->_sEdition ) ) {
            throw new Exception('Shop edition is not set, please use setter setEdition!' );
            return false;
        }

        $this->_oDirectoryReader = oxNew ( "oxDirectory" );
        $this->_oDirectoryReader->setBaseDirectory( $this->_sBaseDirectory );

        $this->_oFileChecker = oxNew ( "oxFileChecker" );
        $this->_oFileChecker->setBaseDirectory( $this->_sBaseDirectory );
        if ( !$this->_oFileChecker->init() ) {
            $this->_blError = $this->_oFileChecker->hasError();
            $this->_sErrorMessage = $this->_oFileChecker->getErrorMessage();
            return false;
        }

        return true;
    }

    /**
     * This method get the XML object for each file and checks the return values. The result will be saved in the
     * variable $aResultOutput.
     *
     * @return null
     * @throws Exception
     */
    public function checkFiles()
    {
        $this->_aResultSummary['OK'] = 0;
        $this->_aResultSummary['VERSIONMISMATCH'] = 0;
        $this->_aResultSummary['UNKNOWN'] = 0;
        $this->_aResultSummary['MODIFIED'] = 0;
        $this->_aResultSummary['FILES'] = 0;
        $this->_aResultSummary['SHOP_OK'] = true;

        $this->_getOxidFiles();

        $this->_checkOxidFiles();
    }

    /**
     * Checks version of all shop files
     *
     * @return null|void
     */
    private function _checkOxidFiles()
    {
        foreach ( $this->_aFiles as $sFile ) {
            $aCheckResult = $this->_oFileChecker->checkFile( $sFile );

            if ( empty($aCheckResult) )
                continue;

            $this->_aResultSummary['FILES']++;
            $this->_aResultSummary[$aCheckResult['result']]++;

            if ( !$aCheckResult['ok'] ) {
                $this->_aResultSummary['SHOP_OK'] = false;
            }

            $this->_aResult[] = $aCheckResult;
        }
    }

    /**
     * Selects important directors and returns files in there
     *
     * @return array
     */
    private function _getOxidFiles()
    {
        $aCheckFiles = array(
            'bootstrap.php',
            'index.php',
            'oxid.php',
            'oxseo.php',
        );

        $aCheckFolders = array(
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

        $this->_aFiles = $aCheckFiles;

        foreach ( $aCheckFolders as $sFolder) {
            $this->_aFiles = array_merge( $this->_aFiles, $this->_oDirectoryReader->getDirectoryFiles( $sFolder, array( 'php', 'tpl' ), true ) );
        }
    }

}