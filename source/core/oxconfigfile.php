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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Wraps and provides getters for configuration constants stored in configuration file (usually config.inc.php).
 */
class OxConfigFile
{

    /**
     * Performs variable loading from configuration file by including the php file.
     * It works with current configuration file format well,
     * however in case the variable storage format is not satisfactory
     * this method is a subject to be changed.
     *
     * @param string $sFileName Configuration file name
     */
    private function _loadVars($sFileName)
    {
        include $sFileName;
    }

    /**
     * Initializes the instance. Loads config variables from the file.
     *
     * @param string $sFileName Configuration file name
     */
    public function __construct($sFileName)
    {
        $this->_loadVars($sFileName);
    }

    /**
     * Returns loaded variable value by name.
     *
     * @param string $sVarName Variable name
     *
     * @return mixed
     */
    public function getVar($sVarName)
    {
        if (isset ($this->$sVarName)) {
            return $this->$sVarName;
        }

        return null;
    }

    /**
     * Set config variable.
     *
     * @param string $sVarName Variable name
     * @param string $sValue   Variable value
     */
    public function setVar($sVarName, $sValue)
    {
        $this->$sVarName = $sValue;
    }

    /**
     * Checks by name if variable is set
     *
     * @param string $sVarName Variable name
     *
     * @return bool
     */
    public function isVarSet($sVarName)
    {
        return isset($this->$sVarName);
    }

    /**
     * Returns all loaded vars as an array
     *
     * @return array[string]mixed
     */
    public function getVars()
    {
        $aAllVars = get_object_vars($this);

        return $aAllVars;
    }

    /**
     * Sets custom config file to include
     *
     * @param string $sFileName custom configuration file name
     */
    public function setFile($sFileName)
    {
        if (is_readable($sFileName)) {
            $this->_loadVars($sFileName);
        }
    }
}
