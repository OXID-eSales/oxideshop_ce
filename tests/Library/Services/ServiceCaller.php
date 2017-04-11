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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Class ServiceCaller
 */
class ServiceCaller
{
    /**
     * Switches active shop
     *
     * @param string $sShopId
     */
    public function setActiveShop($sShopId)
    {
        $oConfig = oxRegistry::getConfig();
        if ($sShopId && $oConfig->getEdition() == 'EE') {
            $oConfig->setShopId($sShopId);
        }
    }

    /**
     * Switches active language
     *
     * @param string $sLang
     *
     * @throws Exception
     */
    public function setActiveLanguage($sLang)
    {
        if ($sLang) {
            $oLang = oxRegistry::getLang();
            $aLanguages = $oLang->getLanguageIds();
            $iLanguageId = array_search($sLang, $aLanguages);
            if ($iLanguageId === false) {
                throw new Exception("Language $sLang was not found or is not active in shop");
            }
            oxRegistry::getLang()->setBaseLanguage($iLanguageId);
        }
    }

    /**
     * Calls service
     *
     * @param string $sServiceClass
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function callService($sServiceClass)
    {
        $oService = $this->_createService($sServiceClass);

        return $oService->init();
    }

    /**
     * Creates Service object. All services must implement ShopService interface
     *
     * @param string $sServiceClass
     *
     * @throws Exception
     *
     * @return ShopServiceInterface
     */
    protected function _createService($sServiceClass)
    {
        $this->_includeServiceFile($sServiceClass);
        $oService = new $sServiceClass();

        if (!($oService instanceof ShopServiceInterface)) {
            throw new Exception("Service $sServiceClass does not implement ShopServiceInterface interface!");
        }

        return $oService;
    }

    /**
     * Includes service main class file
     *
     * @param string $sServiceClass
     *
     * @throws Exception
     */
    protected function _includeServiceFile($sServiceClass)
    {
        $sFile = realpath($sServiceClass . '/' . $sServiceClass . '.php');

        if (!file_exists($sFile)) {
            throw new Exception("Service $sServiceClass not found in path $sFile!");
        }

        include_once $sFile;
    }
}
