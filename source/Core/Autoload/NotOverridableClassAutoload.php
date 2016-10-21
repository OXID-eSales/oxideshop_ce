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
namespace OxidEsales\EshopCommunity\Core\Autoload;

/**
 * Autoloader which is used to create class aliases for not overridable classes.
 */
class NotOverridableClassAutoload
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
     * @param string $sClass Class name.
     */
    public function autoload($sClass)
    {
        $sClass = strtolower($sClass);
        if (array_key_exists($sClass, $this->getClassMap())) {
            class_alias($this->map[$sClass], $sClass);
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
