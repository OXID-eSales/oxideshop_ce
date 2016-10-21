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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core\Edition;

use OxidEsales\EshopCommunity\Core\ConfigFile;
use OxidEsales\EshopCommunity\Core\Registry;

/**
 * Class is responsible for returning edition directory path.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class EditionRootPathProvider
{
    const EDITIONS_DIRECTORY = 'oxid-esales';

    const ENTERPRISE_DIRECTORY = 'oxideshop-ee';

    const PROFESSIONAL_DIRECTORY = 'oxideshop-pe';

    /** @var EditionSelector */
    private $editionSelector;

    /**
     * @param EditionSelector $editionSelector
     */
    public function __construct($editionSelector)
    {
        $this->editionSelector = $editionSelector;
    }

    /**
     * Returns path to edition directory. If no additional editions are found, returns base path.
     *
     * @return string
     */
    public function getDirectoryPath()
    {
        if (Registry::instanceExists('oxConfigFile')) {
            $configFile = Registry::get('oxConfigFile');
        } else {
            $configFile = new ConfigFile(getShopBasePath() . '/config.inc.php');
            Registry::set('oxConfigFile', $configFile);
        }
        $editionsPath = $configFile->getVar('vendorDirectory')  .'/'. static::EDITIONS_DIRECTORY;
        $path = getShopBasePath();
        if ($this->getEditionSelector()->isEnterprise()) {
            $path = $editionsPath  .'/'. static::ENTERPRISE_DIRECTORY;
        } elseif ($this->getEditionSelector()->isProfessional()) {
            $path = $editionsPath .'/'.  static::PROFESSIONAL_DIRECTORY;
        }

        return realpath($path) . DIRECTORY_SEPARATOR;
    }

    /**
     * @return EditionSelector
     */
    protected function getEditionSelector()
    {
        return $this->editionSelector;
    }
}
