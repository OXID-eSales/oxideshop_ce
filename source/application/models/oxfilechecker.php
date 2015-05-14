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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Shop file checker
 * Performs version check of shop file
 *
 */

class oxFileChecker
{

    /**
     * error tag
     *
     * @var boolean
     */
    protected $_blError = false;

    /**
     * error message
     *
     * @var string
     */
    protected $_sErrorMessage = null;

    /**
     * Web service script
     *
     * @var string
     */
    public $_sWebServiceUrl = 'http://oxchkversion.oxid-esales.com/webService.php';

    /**
     * CURL handler
     *
     * @var oxCurl
     */
    protected $_oCurlHandler = null;

    /**
     * Edition of THIS OXID eShop
     *
     * @var string
     */
    protected $_sEdition = "";

    /**
     * Version of THIS OXID eShop
     *
     * @var string
     */
    protected $_sVersion = "";

    /**
     * Revision of THIS OXID eShop
     *
     * @var string
     */
    protected $_sRevision = "";

    /**
     * base directory
     *
     * @var mixed
     */
    protected $_sBaseDirectory = '';


    /**
     * If the variable is true, the script will show all files, even they are ok.
     *
     * @var bool
     */
    protected $_blListAllFiles = false;

    /**
     * Setter for working directory
     *
     * @param string $sDir Directory
     */
    public function setBaseDirectory($sDir)
    {
        if (!empty($sDir)) {
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
     * @param string $sVersion Version
     */
    public function setVersion($sVersion)
    {
        if (!empty($sVersion)) {
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
     * @param string $sEdition Edition
     */
    public function setEdition($sEdition)
    {
        if (!empty($sEdition)) {
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
     * @param string $sRevision Revision
     */
    public function setRevision($sRevision)
    {
        if (!empty($sRevision)) {
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
     * Web service URL setter
     *
     * @param string $sUrl Web service url.
     */
    public function setWebServiceUrl($sUrl)
    {
        if (!empty($sUrl)) {
            $this->_sWebServiceUrl = $sUrl;
        }
    }

    /**
     * Web service URL getter
     *
     * @return string
     */
    public function getWebServiceUrl()
    {
        return $this->_sWebServiceUrl;
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
     * Initializes object and checks web service availability
     *
     * @return boolean
     */
    public function init()
    {
        $this->_oCurlHandler = oxNew("oxCurl");

        if (!$this->checkSystemRequirements()) {
            $this->_blError = true;
            $this->_sErrorMessage .= "Error: requirements are not met.";

            return false;
        }

        return true;
    }


    /**
     * Checks system requirements and builds error messages if there are some
     *
     * @return boolean
     */
    public function checkSystemRequirements()
    {
        return $this->_isWebServiceOnline() && $this->_isShopVersionIsKnown();
    }

    /**
     * in case if a general error is thrown by webservice
     *
     * @return string error
     */
    protected function _isWebServiceOnline()
    {
        $oXML = null;
        $aParams = array(
            'job' => 'ping',
        );

        $this->_oCurlHandler->setUrl($this->_sWebServiceUrl);
        $this->_oCurlHandler->setMethod("GET");
        $this->_oCurlHandler->setOption("CURLOPT_CONNECTTIMEOUT", 30);
        $this->_oCurlHandler->setParameters($aParams);
        $sXML = $this->_oCurlHandler->execute();

        if (empty($sXML)) {
            $this->_blError = true;
            $this->_sErrorMessage = oxRegistry::getLang()->translateString('OXDIAG_ERRORMESSAGEWEBSERVICEISNOTREACHABLE');
        }

        try {
            $oXML = new SimpleXMLElement($sXML);
        } catch (Exception $ex) {
            $this->_blError = true;
            $this->_sErrorMessage .= oxRegistry::getLang()->translateString('OXDIAG_ERRORMESSAGEWEBSERVICERETURNEDNOXML');
        }

        if (!is_object($oXML)) {
            $this->_blError = true;
            $this->_sErrorMessage .= oxRegistry::getLang()->translateString('OXDIAG_ERRORMESSAGEVERSIONDOESNOTEXIST');
        }

        return !$this->_blError;
    }


    /**
     * asks the webservice, if the shop version is known.
     *
     * @return boolean
     */
    protected function _isShopVersionIsKnown()
    {
        $aParams = array(
            'job' => 'existsversion',
            'ver' => $this->getVersion(),
            'rev' => $this->getRevision(),
            'edi' => $this->getEdition(),
        );

        $sURL = $this->_sWebServiceUrl . "?" . http_build_query($aParams);

        if ($sXML = @file_get_contents($sURL)) {
            $oXML = new SimpleXMLElement($sXML);
            if (is_object($oXML)) {
                if ($oXML->exists == 1) {
                    return true;
                }
            }
        }

        $this->_blError = true;
        $sError = sprintf(
            oxRegistry::getLang()->translateString('OXDIAG_ERRORMESSAGEVERSIONDOESNOTEXIST'),
            $this->getEdition(), $this->getVersion(), $this->getRevision()
        );

        $this->_sErrorMessage .= $sError;

        return false;
    }

    /**
     * This method gets the XML object for each file and checks the return values. The result will be saved in the
     * variable $sResultOutput.
     *
     * @param string $sFile File
     *
     * @return mixed
     */
    public function checkFile($sFile)
    {
        $aResult = array();

        if ($this->_oCurlHandler == null) {
            return $aResult;
        }

        if (!file_exists($this->_sBaseDirectory . $sFile)) {
            return $aResult;
        }

        $sMD5 = md5_file($this->_sBaseDirectory . $sFile);

        usleep(10);
        $oXML = $this->_getFileVersion($sMD5, $sFile);
        $sColor = "blue";
        $blOk = true;
        $sMessage = oxRegistry::getLang()->translateString('OXDIAG_ERRORVERSIONCOMPARE');

        if (is_object($oXML)) {

            if ($oXML->res == 'OK') {
                // If recognized, still can be source or snapshot
                $aMatch = array();

                if (preg_match('/(SOURCE|SNAPSHOT)/', $oXML->pkg, $aMatch)) {
                    $blOk = false;
                    $sMessage = 'SOURCE|SNAPSHOT';
                    $sColor = 'red';
                } else {
                    $sMessage = oxRegistry::getLang()->translateString('OXDIAG_OK');
                    $sColor = "green";
                }
            } elseif ($oXML->res == 'VERSIONMISMATCH') {
                $sMessage = oxRegistry::getLang()->translateString('OXDIAG_VERSION_MISMATCH');
                $sColor = 'red';
                $blOk = false;
            } elseif ($oXML->res == 'MODIFIED') {
                $sMessage = oxRegistry::getLang()->translateString('OXDIAG_MODIFIED');
                $sColor = 'red';
                $blOk = false;
            } elseif ($oXML->res == 'OBSOLETE') {
                $sMessage = oxRegistry::getLang()->translateString('OXDIAG_OBSOLETE');
                $sColor = 'red';
                $blOk = false;
            } elseif ($oXML->res == 'UNKNOWN') {
                $sMessage = oxRegistry::getLang()->translateString('OXDIAG_UNKNOWN');
                $sColor = "green";
            }
        }

        if ($sMessage) {
            $aResult = array(
                "result"  => strval($oXML->res),
                "ok"      => $blOk,
                "file"    => $sFile,
                "color"   => $sColor,
                "message" => $sMessage
            );
        }

        return $aResult;
    }

    /**
     * Queries checksum-webservice according to md5, version, revision, edition and filename
     *
     * @param string $sMD5  MD5 to check
     * @param string $sFile File to check
     *
     * @return SimpleXMLElement
     */
    protected function _getFileVersion($sMD5, $sFile)
    {
        $aParams = array(
            'job' => 'md5check',
            'ver' => $this->getVersion(),
            'rev' => $this->getRevision(),
            'edi' => $this->getEdition(),
            'fil' => $sFile,
            'md5' => $sMD5,
        );

        $this->_oCurlHandler->setUrl($this->_sWebServiceUrl);
        $this->_oCurlHandler->setMethod("GET");
        $this->_oCurlHandler->setOption("CURLOPT_CONNECTTIMEOUT", 30);
        $this->_oCurlHandler->setParameters($aParams);
        $sXML = $this->_oCurlHandler->execute();
        $oXML = null;
        try {
            $oXML = new SimpleXMLElement($sXML);
        } catch (Exception $ex) {
            $oXML = null;
        }

        return $oXML;
    }
}
