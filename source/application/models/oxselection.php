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
 * Variant selection container class
 *
 */
class oxSelection
{

    /**
     * Selection name
     *
     * @var string
     */
    protected $_sName = null;

    /**
     * Selection value
     *
     * @var string
     */
    protected $_sValue = null;

    /**
     * Selection state: active
     *
     * @var bool
     */
    protected $_blActive = null;

    /**
     * Selection state: disabled
     *
     * @var bool
     */
    protected $_blDisabled = null;

    /**
     * Initializes oxSelection object
     *
     * @param string $sName      selection name
     * @param string $sValue     selection value
     * @param string $blDisabled selection state - disabled/enabled
     * @param string $blActive   selection state - active/inactive
     *
     * @return null
     */
    public function __construct($sName, $sValue, $blDisabled, $blActive)
    {
        $this->_sName = $sName;
        $this->_sValue = $sValue;
        $this->_blDisabled = $blDisabled;
        $this->_blActive = $blActive;
    }

    /**
     * Returns selection value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_sValue;
    }

    /**
     * Returns selection name
     *
     * @return string
     */
    public function getName()
    {
        return getStr()->htmlspecialchars($this->_sName);
    }

    /**
     * Returns TRUE if current selection is active (chosen)
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->_blActive;
    }

    /**
     * Returns TRUE if current selection is disabled
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->_blDisabled;
    }

    /**
     * Sets selection active/inactive
     *
     * @param bool $blActive selection state TRUE/FALSE
     */
    public function setActiveState($blActive)
    {
        $this->_blActive = $blActive;
    }

    /**
     * Sets selection disabled/enables
     *
     * @param bool $blDisabled selection state TRUE/FALSE
     */
    public function setDisabled($blDisabled)
    {
        $this->_blDisabled = $blDisabled;
    }

    /**
     * Returns selection link (currently returns "#")
     *
     * @return string
     */
    public function getLink()
    {
        return "#";
    }
}
