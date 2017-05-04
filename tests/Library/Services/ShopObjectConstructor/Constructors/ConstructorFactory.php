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

require_once 'ObjectConstructor.php';

/**
 * Class CallerFactory
 */
class ConstructorFactory
{
    /**
     * @param string $sClassName
     * @return ObjectConstructor
     */
    public function getConstructor($sClassName)
    {
        $sConstructorClass = $this->_getConstructorClass($sClassName)?: 'ObjectConstructor';

        return new $sConstructorClass($sClassName);
    }

    /**
     * @param $sClassName
     * @return bool|string
     */
    protected function _getConstructorClass($sClassName)
    {
        $sConstructorClass = $sClassName . "Constructor";
        $sFile = realpath(__DIR__.'/'.$sConstructorClass.".php");

        if (file_exists($sFile)) {
            include_once($sFile);
            return $sConstructorClass;
        }

        return false;
    }
}
