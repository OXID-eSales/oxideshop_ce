<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Variant selection container class
 *
 */
class Selection
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
