<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxField;

/**
 * Links manager.
 * Collects stored in DB links data (URL, description).
 */
class Links extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxlinks';

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     *
     * @return oxLinks
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxlinks');
    }

    /**
     * Sets data field value
     *
     * @param string $sFieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $sValue     value of data field
     * @param int    $iDataType  field type
     *
     * @return null
     */
    protected function _setFieldData($sFieldName, $sValue, $iDataType = \OxidEsales\Eshop\Core\Field::T_TEXT)
    {
        if ('oxurldesc' === strtolower($sFieldName) || 'oxlinks__oxurldesc' === strtolower($sFieldName)) {
            $iDataType = \OxidEsales\Eshop\Core\Field::T_RAW;
        }

        return parent::_setFieldData($sFieldName, $sValue, $iDataType);
    }
}
