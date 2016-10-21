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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core\Autoload;

/**
 * Auto loader for virtual namespace classes
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class VirtualNamespaceClassAutoload
{
    /**
     * Classes map which is used to create aliases.
     *
     * @var array
     */
    private $map;

    /**
     * Sets class map.
     *
     * @param array $map
     */
    public function __construct($map)
    {
        $this->map = $map;
    }

    /**
     * Creates class alias from class which is defined in class map.
     *
     * @param string $class Class name.
     */
    public function autoload($class)
    {
        if (array_key_exists($class, $this->getClassMap())) {
            class_alias($this->map[$class], $class);
        }
    }

    /**
     * Returns class map.
     *
     * @return array
     */
    protected function getClassMap()
    {
        return $this->map;
    }
}
