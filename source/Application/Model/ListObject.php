<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxField;

/**
 * Simple list object
 */
class ListObject
{
    /**
     * @var string
     */
    private $_sTableName = '';

    /**
     * Class constructor
     *
     * @param string $sTableName Table name
     */
    public function __construct($sTableName)
    {
        $this->_sTableName = $sTableName;
    }

    /**
     * Assigns database record to object
     *
     * @param object $aData Database record
     *
     * @return null
     */
    public function assign($aData)
    {
        if (!is_array($aData)) {
            return;
        }
        foreach ($aData as $sKey => $sValue) {
            $sFieldName = strtolower($this->_sTableName . '__' . $sKey);
            $this->$sFieldName = new \OxidEsales\Eshop\Core\Field($sValue);
        }
    }

    /**
     * Returns object id
     *
     * @return int
     */
    public function getId()
    {
        $sFieldName = strtolower($this->_sTableName . '__oxid');
        return $this->$sFieldName->value;
    }
}
