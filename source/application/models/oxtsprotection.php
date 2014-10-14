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
 * TRusted shops protection product manager.
 *
 * @package model
 */
class oxTsProtection extends oxSuperCfg
{
    /**
     * TS protection product Ids
     *
     * @var array
     */
    protected $_aAllProducts = null;


    /**
     * TS protection Vat
     *
     * @var float
     */
    protected $_dVat = null;

    /**
     * Buyer protection products
     *
     * @var array
     */
    protected $_sTsCurrencyProducts = array(
                                       "TS080501_500_30_EUR"   => array( "GBP" => "TS100629_500_30_GBP", "CHF" => "TS100629_500_30_CHF", "USD" => "TS080501_500_30_USD" ),
                                       "TS080501_1500_30_EUR"  => array( "GBP" => "TS100629_1500_30_GBP", "CHF" => "TS100629_1500_30_CHF", "USD" => "TS100629_1500_30_USD" ),
                                       "TS080501_2500_30_EUR"  => array( "GBP" => "TS100629_2500_30_GBP", "CHF" => "TS100629_2500_30_CHF", "USD" => "TS100629_2500_30_USD" ),
                                       "TS080501_5000_30_EUR"  => array( "GBP" => "TS100629_5000_30_GBP", "CHF" => "TS100629_5000_30_CHF", "USD" => "TS100629_5000_30_USD" ),
                                       "TS080501_10000_30_EUR" => array( "GBP" => "TS100629_1000_30_GBP", "CHF" => "TS100629_10000_30_CHF", "USD" => "TS100629_10000_30_USD" ),
                                       "TS080501_20000_30_EUR" => array( "GBP" => "TS100629_2000_30_GBP", "CHF" => "TS100629_20000_30_CHF", "USD" => "TS100629_20000_30_USD" )
                                );

    /**
     * Return VAT
     *
     * @return float
     */
    public function getVat()
    {
        return $this->_dVat;
    }

    /**
     * Set VAT
     *
     * @param float $dVat - vat
     *
     * @return null
     */
    public function setVat( $dVat )
    {
        $this->_dVat = $dVat;
    }

    /**
     * Returns array of TS protection products according to order price
     *
     * @param float $dPrice order price
     *
     * @return array
     */
    public function getTsProducts( $dPrice )
    {
        $aProducts = array();
        if ( $aTsProducts = $this->_getTsAllProducts()) {
            foreach ( $aTsProducts as $oProduct ) {
                $aProducts[] = $oProduct;
                if ( $oProduct->getAmount() > $dPrice ) {
                    break;
                }
            }
        }
        return $aProducts;
    }

    /**
     * Returns TS protection product by id
     *
     * @param string $sTsId TS protection product id
     *
     * @return oxTsProduct
     */
    public function getTsProduct( $sTsId )
    {
        $oProduct = oxNew("oxTsProduct");
        $oProduct->setTsId($sTsId);
        return $oProduct;
    }

    /**
     * Executes TS protection
     *
     * @param array  $aValues    Order values
     * @param string $sPaymentId Order payment id
     *
     * @return bool
     */
    public function requestForTsProtection( $aValues, $sPaymentId )
    {
        $oConfig = $this->getConfig();
        $iLangId = (int) oxRegistry::getLang()->getBaseLanguage();
        $blTsTestMode = $oConfig->getConfigParam( 'tsTestMode' );
        $aTsUser = $oConfig->getConfigParam( 'aTsUser' );
        $aTsPassword = $oConfig->getConfigParam( 'aTsPassword' );
        $aTrustedShopIds = $oConfig->getConfigParam( 'iShopID_TrustedShops' );
        if ( $aTrustedShopIds && $aTrustedShopIds[$iLangId] ) {
            try {
                if ( $blTsTestMode ) {
                    $sSoapUrl = $oConfig->getConfigParam( 'sTsTestProtectionUrl' );
                } else {
                    $sSoapUrl = $oConfig->getConfigParam( 'sTsProtectionUrl' );
                }
                $sFunction = 'requestForProtectionV2';
                $sVersion = $this->getConfig()->getVersion();
                $sEdition = $this->getConfig()->getFullEdition();
                $sTsPaymentId = $this->_getTsPaymentId($sPaymentId);
                $tsProductId = $this->_getTsProductCurrId($aValues['tsProductId'], $aValues['currency']);
                $aValues['tsId']    = $aTrustedShopIds[$iLangId];
                $aValues['paymentType'] = $sTsPaymentId;
                $aValues['shopSystemVersion'] = $sEdition . " " . $sVersion;
                $aValues['wsUser'] = $aTsUser[$iLangId];
                $aValues['wsPassword'] = $aTsPassword[$iLangId];
                $aValues['orderDate'] = str_replace(" ", "T", $aValues['orderDate']);
                $oSoap = new SoapClient($sSoapUrl);
                $aResults = $oSoap->{$sFunction}($aValues['tsId'],$tsProductId,$aValues['amount'],$aValues['currency'],$aValues['paymentType'],
                $aValues['buyerEmail'],$aValues['shopCustomerID'],$aValues['shopOrderID'],$aValues['orderDate'],$aValues['shopSystemVersion'],
                $aValues['wsUser'],$aValues['wsPassword']);

                if ( isset($aResults) && "" != $aResults ) {
                    if ( $aResults == "-10001" ) {
                        oxRegistry::getUtils()->logger( "NO_VALID_SHOP" );
                        return false;
                    }
                    if ( $aResults == "-11111" ) {
                        oxRegistry::getUtils()->logger( "SYSTEM_ERROR" );
                        return false;
                    }
                    return $aResults;
                }
            } catch( Exception $eException ) {
                oxRegistry::getUtils()->logger( "Soap-Error: " . $eException->faultstring );
                return false;
            }
        }
        return null;

    }

    /**
     * Executes TS certificate check
     *
     * @param integer $iTrustedShopId Trusted shop Id
     * @param bool    $blTsTestMode   if test mode is on
     *
     * @return object
     */
    public function checkCertificate( $iTrustedShopId, $blTsTestMode )
    {
        if ( $iTrustedShopId ) {
            if ( $blTsTestMode == "true" ) {
                $sSoapUrl = 'https://qa.trustedshops.de/ts/services/TsProtection?wsdl';
            } else {
                $sSoapUrl = 'https://www.trustedshops.de/ts/services/TsProtection?wsdl';
            }
            $sFunction = 'checkCertificate';
            $aValues['tsId'] = $iTrustedShopId;
            $aResults = $this->executeSoap( $sSoapUrl, $sFunction, $aValues['tsId']);
            return $aResults;
        }
        return null;

    }

    /**
     * Executes SOAP call
     *
     * @param string $sSoapUrl  soap url
     * @param string $sFunction soap function
     * @param string $sValues   values sent per soap
     *
     * @return object
     */
    public function executeSoap( $sSoapUrl, $sFunction, $sValues )
    {
        try {
            $oSoap = new SoapClient($sSoapUrl);
            $aResults = $oSoap->{$sFunction}($sValues);
            if ( isset($aResults) ) {
                return $aResults;
            }
        } catch( Exception $eException ) {
            oxRegistry::getUtils()->logger( "Soap-Error: " . $eException->faultstring );
            return false;
        }
        return null;

    }

    /**
     * Returns TS payment id by shop payment id
     *
     * @param string $sPaymentId payment id
     *
     * @return string
     */
    protected function _getTsPaymentId( $sPaymentId )
    {
        $sTsPaymentId = '';

        $aPayment = oxNew("oxPayment");
        if ( $aPayment->load($sPaymentId) ) {
            $sTsPaymentId = $aPayment->oxpayments__oxtspaymentid->value;
        }
        return $sTsPaymentId;
    }

    /**
     * Returns TS protection product Ids
     *
     * @return array
     */
    protected function _getTsAllProducts()
    {
        if ($this->_aAllProducts == null) {
            $this->_aAllProducts = false;
            $oTsProduct = oxNew("oxTsProduct");
            if ( $aTsProducts = $oTsProduct->getAllTsProducts()) {
                foreach ( $aTsProducts as $sId => $aTsProduct ) {
                    $oProduct = oxNew("oxTsProduct");
                    $oProduct->setTsId($sId);
                    $oProduct->setVat( $this->getVat() );
                    $this->_aAllProducts[] = $oProduct;
                }
            }
        }
        return $this->_aAllProducts;
    }

    /**
     * Returns TS protection product id by currency
     *
     * @param string $sTsId product id
     * @param string $sCurr active currency
     *
     * @return array
     */
    protected function _getTsProductCurrId( $sTsId, $sCurr )
    {
        $sTsCurrId = $sTsId;
        if ($sCurr != 'EUR') {
            $aTsCurrId = $this->_sTsCurrencyProducts[$sTsId];
            $sTsCurrId = $aTsCurrId[$sCurr];
        }
        return $sTsCurrId;
    }

}
