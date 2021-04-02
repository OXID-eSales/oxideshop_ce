<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxField;

/**
 * Remark manager.
 *
 */
class Remark extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxremark';

    /**
     * Skip update fields
     *
     * @var array
     */
    protected $_aSkipSaveFields = ['oxtimestamp'];

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxremark');
    }

    /**
     * Loads object information from DB. Returns true on success.
     *
     * @param string $oxID ID of object to load
     *
     * @return bool
     */
    public function load($oxID)
    {
        if ($blRet = parent::load($oxID)) {
            // convert date's to international format
            $this->assign([
                'oxcreate'    => \OxidEsales\Eshop\Core\Registry::getUtilsDate()->formatDBDate($this->oxremark__oxcreate->value)
            ]);
        }

        return $blRet;
    }

    /**
     * Inserts object data fields in DB. Returns true on success.
     *
     * @return bool
     * @deprecated underscore prefix violates PSR12, will be renamed to "insert" in next major
     */
    protected function _insert() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        // set oxcreate
        $sNow = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());
        $this->oxremark__oxcreate = new \OxidEsales\Eshop\Core\Field($sNow, \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->oxremark__oxheader = new \OxidEsales\Eshop\Core\Field($sNow, \OxidEsales\Eshop\Core\Field::T_RAW);

        return parent::_insert();
    }
}
