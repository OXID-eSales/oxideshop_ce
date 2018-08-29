<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Forms real class name for edition based classes.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class BackwardsCompatibleClassNameProvider
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
        $className = $classAlias;
        if (array_key_exists($classAlias, $this->classMap)) {
            $className = $this->classMap[$classAlias];
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
        /*
         * Sanitize input: class names in namespaces should not, but may include a leading backslash
         */
        $className = ltrim($className, '\\');
        $classAlias = array_search($className, $this->classMap);

        if ($classAlias === false) {
            $classAlias = null;
        }

        return $classAlias;
    }
}
