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
 * Class oxConfigCaller
 */
class oxConfigConstructor extends ObjectConstructor
{

    /**
     * Skip loading of config object, as it is already loaded
     *
     * @param $sOxId
     */
    public function load($sOxId) {}

    /**
     * Sets class parameters
     *
     * @param array $aClassParams
     * @return array
     */
    public function setClassParameters($aClassParams)
    {
        $aValues = array();
        foreach ($aClassParams as $sConfKey => $aConfParams) {
            if (is_int($sConfKey)) {
                $aValues[$aConfParams] = $this->getObject()->getConfigParam($aConfParams);
            } else {
                $aFormedParams = $this->_formSaveConfigParameters($sConfKey, $aConfParams);
                if ($aFormedParams) {
                    $this->callFunction("saveShopConfVar", $aFormedParams);
                }
            }
        }

        return $aValues;
    }

    /**
     * Returns created object to work with
     *
     * @param $sClassName
     * @return oxConfig
     */
    protected function _createObject($sClassName)
    {
        return oxRegistry::getConfig();
    }

    /**
     * Forms parameters for saveShopConfVar function from given parameters
     *
     * @param $sConfKey
     * @param $aConfParams
     * @return array|bool
     */
    private function _formSaveConfigParameters($sConfKey, $aConfParams)
    {
        $sType = $aConfParams['type'] ? $aConfParams['type'] : null;
        $sValue = $aConfParams['value'] ? $aConfParams['value'] : null;
        $sModule = $aConfParams['module'] ? $aConfParams['module'] : null;

        if (($sType == "arr" || $sType == 'aarr') && !is_array($sValue)) {
            $sValue = unserialize(htmlspecialchars_decode($sValue));
        }
        return !empty($sType) ? array($sType, $sConfKey, $sValue, null, $sModule) : false;
    }
}
