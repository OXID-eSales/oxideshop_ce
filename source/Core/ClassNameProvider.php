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

namespace OxidEsales\EshopCommunity\Core;

/**
 * Forms real class name for edition based classes.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ClassNameProvider
{
    /** @var array */
    private $classMap;

    /**
     * @param array $classMap
     */
    public function __construct($classMap)
    {
        $this->classMap = $classMap;
    }

    /**
     * Returns real class name from given alias. If class alias is not found,
     * given class alias is thought to be a real class and is returned.
     *
     * @param string $classAlias
     *
     * @return mixed
     */
    public function getClassName($classAlias)
    {
        $classMap = $this->getExtendedClassMap();

        $className = $classAlias;
        if (array_key_exists($classAlias, $classMap)) {
            $className = $classMap[$classAlias];
        }

        return $className;
    }

    /**
     * Method returns class alias by given class name.
     *
     * @param string $className with namespace.
     *
     * @return string|null
     */
    public function getClassAliasName($className)
    {
        if (substr($className, 0, 1) !== '\\') {
            $className = '\\' . $className;
        }

        $classAlias = array_search($className, $this->getExtendedClassMap());
        if ($classAlias === false) {
            $classAlias = null;
        }

        return $classAlias;
    }

    /**
     * Returns extended classes map
     *
     * @return array
     */
    protected function getExtendedClassMap()
    {
        return $this->classMap;
    }
}
