<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup;

use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Facts\Facts;

class Core
{
    protected const SETUP_DIRECTORY = 'Setup';

    /**
     * Keeps instance cache
     *
     * @var array
     */
    protected static $_aInstances = [];

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
     * @param string $method Methods name
     * @param array  $arguments Argument array
     * @return false|mixed
     * @throws SystemComponentException
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([& $this, $method], $arguments);
        }
        throw new SystemComponentException(
            "Function '$method' does not exist or is not accessible! (" . get_class($this) . ")" . PHP_EOL
        );
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
        $facts = new Facts();
        $class =  'OxidEsales\\EshopCommunity\\Setup\\' . $sInstanceName;

        $classEnterprise = '\\OxidEsales\\EshopEnterprise\\' . self::SETUP_DIRECTORY . '\\' . $sInstanceName;
        $classProfessional = '\\OxidEsales\\EshopProfessional\\' . self::SETUP_DIRECTORY . '\\' . $sInstanceName;
        if (($facts->isProfessional() || $facts->isEnterprise()) && $this->classExists($classProfessional)) {
            $class = $classProfessional;
        }
        if ($facts->isEnterprise() && $this->classExists($classEnterprise)) {
            $class = $classEnterprise;
        }

        return $class;
    }

    /**
     * @return Setup
     */
    protected function getSetupInstance()
    {
        return $this->getInstance("Setup");
    }

    /**
     * @return Language
     */
    protected function getLanguageInstance()
    {
        return $this->getInstance("Language");
    }

    /**
     * @return Utilities
     */
    protected function getUtilitiesInstance()
    {
        return $this->getInstance("Utilities");
    }

    /**
     * @return Session
     */
    protected function getSessionInstance()
    {
        return $this->getInstance("Session");
    }

    /**
     * @return Database
     */
    protected function getDatabaseInstance()
    {
        return $this->getInstance("Database");
    }

    /**
     * Return true if user already decided to overwrite database.
     *
     * @return bool
     */
    protected function userDecidedOverwriteDB()
    {
        $userDecidedOverwriteDatabase = false;

        $overwriteCheck = $this->getUtilitiesInstance()->getRequestVar("ow", "get");
        $session = $this->getSessionInstance();

        if (isset($overwriteCheck) || $session->getSessionParam('blOverwrite')) {
            $userDecidedOverwriteDatabase = true;
        }

        return $userDecidedOverwriteDatabase;
    }

    /**
     * Check if class exists.
     * Ignore autoloader exceptions which might appear if database does not exist.
     *
     * @param string $className
     *
     * @return bool
     */
    private function classExists($className)
    {
        try {
            $classExists = class_exists($className);
        } catch (\Exception $e) {
            return false;
        }

        return $classExists;
    }
}
