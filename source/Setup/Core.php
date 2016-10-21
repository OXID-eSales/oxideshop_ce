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

namespace OxidEsales\EshopCommunity\Setup;

use OxidEsales\EshopCommunity\Core\Edition\EditionPathProvider;
use OxidEsales\EshopCommunity\Core\Edition\EditionSelector;
use oxSystemComponentException;

/**
 * Core setup class, setup instance holder
 */
class Core
{
    /**
     * Keeps instance cache
     *
     * @var array
     */
    protected static $_aInstances = array();

    /**
     * Returns requested instance object
     *
     * @param string $sInstanceName instance name
     *
     * @return Core
     */
    public function getInstance($sInstanceName)
    {
        if (strpos($sInstanceName, '\\') === false) {
            $sInstanceName = $this->getClass($sInstanceName);
        }
        if (!isset(Core::$_aInstances[$sInstanceName])) {
            Core::$_aInstances[$sInstanceName] = new $sInstanceName();
        }

        return Core::$_aInstances[$sInstanceName];
    }

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     *
     * @param string $sMethod Methods name
     * @param array  $aArgs   Argument array
     *
     * @throws oxSystemComponentException Throws an exception if the called method does not exist or is not accessable in current class
     *
     * @return string
     */
    public function __call($sMethod, $aArgs)
    {
        if (defined('OXID_PHP_UNIT')) {
            if (substr($sMethod, 0, 4) == "UNIT") {
                $sMethod = str_replace("UNIT", "_", $sMethod);
            }
            if (method_exists($this, $sMethod)) {
                return call_user_func_array(array(& $this, $sMethod), $aArgs);
            }
        }

        throw new oxSystemComponentException("Function '$sMethod' does not exist or is not accessible! (" . get_class($this) . ")" . PHP_EOL);
    }

    /**
     * Methods returns class according edition.
     *
     * @param string $sInstanceName
     *
     * @return string
     */
    protected function getClass($sInstanceName)
    {
        $editionSelector = new EditionSelector();
        $class =  'OxidEsales\\EshopCommunity\\Setup\\' . $sInstanceName;

        $classEnterprise = '\\OxidEsales\\EshopEnterprise\\'.EditionPathProvider::SETUP_DIRECTORY.'\\'.$sInstanceName;
        $classProfessional = '\\OxidEsales\\EshopProfessional\\'.EditionPathProvider::SETUP_DIRECTORY.'\\'.$sInstanceName;
        if (($editionSelector->isProfessional() || $editionSelector->isEnterprise()) && class_exists($classProfessional)) {
            $class = $classProfessional;
        }
        if ($editionSelector->isEnterprise() && class_exists($classEnterprise)) {
            $class = $classEnterprise;
        }

        return $class;
    }
}
