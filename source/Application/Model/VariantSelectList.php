<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Variant selection lists manager class
 *
 */
class VariantSelectList implements \OxidEsales\Eshop\Core\Contract\ISelectList
{
    /**
     * Variant selection list label
     *
     * @var string
     */
    protected $_sLabel = null;

    /**
     * Selection list index
     *
     * @var int
     */
    protected $_iIndex = 0;

    /**
     * List with selections
     *
     * @var array
     */
    protected $_aList = [];

    /**
     * Active variant selection object
     *
     * @var oxSelection
     */
    protected $_oActiveSelection = null;

    /**
     * Builds current selection list
     *
     * @param string $sLabel list label
     * @param int    $iIndex list index
     */
    public function __construct($sLabel, $iIndex)
    {
        $this->_sLabel = trim($sLabel);
        $this->_iIndex = $iIndex;
    }

    /**
     * Returns variant selection list label
     *
     * @return string
     */
    public function getLabel()
    {
        return getStr()->htmlspecialchars($this->_sLabel);
    }

    /**
     * Adds given variant info to current variant selection list
     *
     * @param string $sName      selection name
     * @param string $sValue     selection value
     * @param string $blDisabled selection state - disabled/enabled
     * @param string $blActive   selection state - active/inactive
     */
    public function addVariant($sName, $sValue, $blDisabled, $blActive)
    {
        $sName = trim($sName);
        //#6053 Allow "0" as a valid value.
        if (!empty($sName) || $sName === '0') {
            $sKey = $sValue;

            // creating new
            if (!isset($this->_aList[$sKey])) {
                $this->_aList[$sKey] = oxNew(\OxidEsales\Eshop\Application\Model\Selection::class, $sName, $sValue, $blDisabled, $blActive);
            } else {
                // overriding states
                if ($this->_aList[$sKey]->isDisabled() && !$blDisabled) {
                    $this->_aList[$sKey]->setDisabled($blDisabled);
                }

                if (!$this->_aList[$sKey]->isActive() && $blActive) {
                    $this->_aList[$sKey]->setActiveState($blActive);
                }
            }

            // storing active selection
            if ($this->_aList[$sKey]->isActive()) {
                $this->_oActiveSelection = $this->_aList[$sKey];
            }
        }
    }

    /**
     * Returns active selection object
     *
     * @return oxSelection
     */
    public function getActiveSelection()
    {
        return $this->_oActiveSelection;
    }

    /**
     * Returns array of oxSelection's
     *
     * @return array
     */
    public function getSelections()
    {
        return $this->_aList;
    }
}
