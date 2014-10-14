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


DEFINE('EFIRE_WSDL_URL', 'https://soap.oxid-efire.com/');
//DEFINE('EFIRE_WSDL_URL', 'http://efire-linux:12156/');

/**
 * Efire SOAP client responsible for getting retrieving a connector over SOAP.
 *
 * @package core
 */
class oxEfiDownloader extends oxSuperCfg
{
    /**
     * Soap client
     *
     * @var object
     */
    protected $_oSoapClient = null;

    protected $_sVersion = null;

    /**
     * Retrieves connector filename, the contents and saves it to shop directory. Returns connector $sFileName.
     *
     * @param string $sUsername         eFire External Transaction username
     * @param string $sPassword         eFire External Transaction password
     * @param string $sShopVersion      eShop version
     * @param bool   $blSaveCredentials whether to save username and password to config for later use
     *
     * @return string
     */
    public function downloadConnector($sUsername, $sPassword, $sShopVersion, $blSaveCredentials)
    {

        if ($blSaveCredentials) {
            $this->getConfig()->saveShopConfVar('str', 'sEfiUsername', $sUsername);
            $this->getConfig()->saveShopConfVar('str', 'sEfiPassword', $sPassword);
        } else {
            $this->getConfig()->saveShopConfVar('str', 'sEfiUsername', null);
            $this->getConfig()->saveShopConfVar('str', 'sEfiPassword', null);
        }

        $this->_init($sUsername, $sPassword);

        $sFileName = getShopBasePath() . "core/". strtolower(basename($this->_getConnectorClassName($sShopVersion))) . ".php";
        $sFileContents = $this->_getConnectorContents($sShopVersion);

        //writing to file
        $fOut = fopen($sFileName, "w");
        if (!fputs($fOut, $sFileContents)) {
            throw new oxException('EXCEPTION_COULDNOTWRITETOFILE');
        }
        fclose($fOut);

        //remove possible old connector from the main shop dir
        if (file_exists(getShopBasePath() . "oxefi.php")) {
            unlink(getShopBasePath() . "oxefi.php");
        }

        return $sFileName;
    }

    /**
     * Initialises eFire SOAP connection
     *
     * @param string $sUsername eFire External Communication Username
     * @param string $sPassword eFire External Communication Passsword
     *
     * @return bool
     */
    protected function _init($sUsername, $sPassword)
    {
        $this->_oClient = new SoapClient( EFIRE_WSDL_URL . 'eshopconnector/?wsdl',
                                          array(
                                                 'trace'      => 1,
                                                 'style'      => SOAP_DOCUMENT,
                                                 'login'      => $sUsername,
                                                 'password'   => $sPassword,
                                                 'cache_wsdl' => WSDL_CACHE_NONE
                                               )
                                        );

    }

    /**
     * Retrieves filename for current connector.
     *
     * @param string $sShopVersion Shop edition and version (eg. 'CE 4.0.0.0')
     *
     * @return string
     */
    protected function _getConnectorClassName($sShopVersion)
    {
        $oResponse = $this->_oClient->getConnectorClassName($sShopVersion);

        if (!$oResponse->blResult) {
            throw new Exception($oResponse->sMessage);
        }

        return $oResponse->sMessage;
    }

    /**
     * Get contents of connector
     *
     * @param string $sShopVersion Shop edition and version (eg. 'CE 4.0.0.0')
     *
     * @throws Exception
     * @return string
     */
    protected function _getConnectorContents($sShopVersion)
    {
        $oResponse = $this->_oClient->getConnectorFileContents($sShopVersion);

        if (!$oResponse->blResult) {
            throw new Exception($oResponse->sMessage);
        }

        return $oResponse->sMessage;
    }

}
