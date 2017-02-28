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

namespace OxidEsales\EshopCommunity\Core\Autoload;

/**
 * Delivers the virtual namespace to edition class name map depending on the shop's current edition.
 * In case the edition is NOT explicitly set in config.inc.php, the edition will be set according
 * to the highest available edition files.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class VirtualNameSpaceClassMapProvider
{
    /**
     * Get array mapping virtual namespace class name as key to real edition namespace class name.
     *
     * @return array
     */
    public function getClassMap()
    {
        $virtualClassMap = $this->getVirtualClassMap();
        return $virtualClassMap->getClassMap();
    }

    /**
     * Return the corresponding virtual class map.
     * When creating the instance of VirtualNameSpaceClassMap is is assured, that no auto loader will be triggered.
     *
     * @return OxidEsales\Eshop\Core\Autoload\VirtualNameSpaceClassMap
     */
    private function getVirtualClassMap()
    {
        /** The properties defined in the config file will dynamically loaded into this class */
        include OX_BASE_PATH . DIRECTORY_SEPARATOR . 'config.inc.php';
        $editionByConfig = $this->edition;
        $editionBySource = null;
        $virtualNameSpaceClassMapFiles = [
            'CE' => OX_BASE_PATH . 'Core/Autoload/VirtualNameSpaceClassMap.php',
            'PE' => VENDOR_PATH . 'oxid-esales/oxideshop-pe/Core/Autoload/VirtualNameSpaceClassMap.php',
            'EE' => VENDOR_PATH . 'oxid-esales/oxideshop-ee/Core/Autoload/VirtualNameSpaceClassMap.php',
        ];
        $virtualNameSpaceClassMaps = [
            'CE' => \OxidEsales\EshopCommunity\Core\Autoload\VirtualNameSpaceClassMap::class,
            'PE' => \OxidEsales\EshopProfessional\Core\Autoload\VirtualNameSpaceClassMap::class,
            'EE' => \OxidEsales\EshopEnterprise\Core\Autoload\VirtualNameSpaceClassMap::class
        ];

        /** Include all classes needed for object creation in order not to trigger other autoloaders */
        foreach ($virtualNameSpaceClassMapFiles as $currentEdition => $virtualNameSpaceClassMapFile) {
            if (file_exists($virtualNameSpaceClassMapFile)) {
                include_once $virtualNameSpaceClassMapFile;
                $editionBySource = $currentEdition;
            }
        }

        $edition =  $editionByConfig ? strtoupper($editionByConfig) : $editionBySource;
        if (!$edition) {
            trigger_error('OXID eShop Edition could not be found in config file or from sources', E_USER_ERROR);
        }

        if (!file_exists($virtualNameSpaceClassMapFiles[$edition])) {
            trigger_error('The corresponding classmap for edition "' . $edition . '" was not found:  ' . $virtualNameSpaceClassMapFiles[$edition], E_USER_ERROR);
        }

        $virtualClassMap = new $virtualNameSpaceClassMaps[$edition]();
        return $virtualClassMap;
    }
}
