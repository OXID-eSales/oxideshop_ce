<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Core;

/**
 * Wraps and provides getters for configuration constants stored in configuration file (usually config.inc.php).
 */
class ConfigFile
{
    /**
     * Initializes the instance. Loads config variables from the file.
     *
     * @param string $fileName Configuration file name
     */
    public function __construct($fileName)
    {
        $this->_loadVars($fileName);
    }

    /**
     * Returns loaded variable value by name.
     *
     * @param string $varName Variable name
     *
     * @return mixed
     */
    public function getVar($varName)
    {
        return isset($this->$varName) ? $this->$varName : null;
    }

    /**
     * Set config variable.
     *
     * @param string $varName Variable name
     * @param string $value   Variable value
     */
    public function setVar($varName, $value)
    {
        $this->$varName = $value;
    }

    /**
     * Checks by name if variable is set
     *
     * @param string $varName Variable name
     *
     * @return bool
     */
    public function isVarSet($varName)
    {
        return isset($this->$varName);
    }

    /**
     * Returns all loaded vars as an array
     *
     * @return array[string]mixed
     */
    public function getVars()
    {
        return get_object_vars($this);
    }

    /**
     * Sets custom config file to include
     *
     * @param string $fileName custom configuration file name
     */
    public function setFile($fileName)
    {
        if (is_readable($fileName)) {
            $this->_loadVars($fileName);
        }
    }
    /**
     * Performs variable loading from configuration file by including the php file.
     * It works with current configuration file format well,
     * however in case the variable storage format is not satisfactory
     * this method is a subject to be changed.
     *
     * @param string $fileName Configuration file name
     */
    private function _loadVars($fileName)
    {
        include $fileName;
    }
}
