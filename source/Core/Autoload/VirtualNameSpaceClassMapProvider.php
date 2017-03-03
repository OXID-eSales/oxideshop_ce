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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version       OXID eShop CE
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
     * Array with virtual name space class map class names.
     *
     * @var array
     */
    private $virtualNameSpaceClassMaps = [
        'CE' => \OxidEsales\EshopCommunity\Core\Autoload\VirtualNameSpaceClassMap::class,
        'PE' => \OxidEsales\EshopProfessional\Core\Autoload\VirtualNameSpaceClassMap::class,
        'EE' => \OxidEsales\EshopEnterprise\Core\Autoload\VirtualNameSpaceClassMap::class
    ];

    /**
     * Array with virtual namespace class map file names.
     *
     * @var array
     */
    private $virtualNameSpaceClassMapFiles = [];

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->virtualNameSpaceClassMapFiles = [
            'CE' => CORE_AUTOLOADER_PATH . 'VirtualNameSpaceClassMap.php',
            'PE' => VENDOR_PATH . 'oxid-esales/oxideshop-pe/Core/Autoload/VirtualNameSpaceClassMap.php',
            'EE' => VENDOR_PATH . 'oxid-esales/oxideshop-ee/Core/Autoload/VirtualNameSpaceClassMap.php',
        ];
    }

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
     * Get current edition. If no edition is set in config.inc.php check for available files.
     *
     * @return string
     */
    public function getEdition()
    {
        $editionByConfig = $this->getEditionByConfig();
        $editionBySource = $this->getEditionBySource();

        $edition = $editionByConfig ? $editionByConfig : $editionBySource;
        if (!$edition) {
            trigger_error('OXID eShop Edition could not be found in config file or from sources', E_USER_ERROR);
        }

        return $edition;
    }

    /**
     * Return the corresponding virtual class map.
     * When creating the instance of VirtualNameSpaceClassMap is is assured, that no auto loader will be triggered.
     *
     * @return \OxidEsales\EshopCommunity\Core\Autoload\VirtualNameSpaceClassMap
     */
    private function getVirtualClassMap()
    {
        $edition = $this->getEdition();

        if (!file_exists($this->virtualNameSpaceClassMapFiles[$edition])) {
            trigger_error('The corresponding classmap for edition "' . $edition . '" was not found:  ' . $this->virtualNameSpaceClassMapFiles[$edition], E_USER_ERROR);
        }

        $virtualClassMap = new $this->virtualNameSpaceClassMaps[$edition]();

        return $virtualClassMap;
    }

    /**
     * Get edition from config.inc.php.
     *
     * @return string
     */
    private function getEditionByConfig()
    {
        /** The properties defined in the config file will dynamically loaded into this class */
        include OX_BASE_PATH . DIRECTORY_SEPARATOR . 'config.inc.php';
        $editionByConfig = strtoupper($this->edition);

        return $editionByConfig;
    }

    /**
     * Get edition by checking which source files exist.
     * Includes available class map files in the process.
     *
     * @return string
     */
    private function getEditionBySource()
    {
        $editionBySource = null;

        /** Include all classes needed for object creation in order not to trigger other autoloaders */
        foreach ($this->virtualNameSpaceClassMapFiles as $currentEdition => $virtualNameSpaceClassMapFile) {
            if (file_exists($virtualNameSpaceClassMapFile) && is_readable($virtualNameSpaceClassMapFile)) {
                include_once $virtualNameSpaceClassMapFile;
                $editionBySource = $currentEdition;
            }
        }

        return $editionBySource;
    }
}
