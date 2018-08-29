<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxField;

/**
 * Manages product assignment to category.
 */
class Object2Category extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxobject2category';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()) and sets table name.
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxobject2category');
    }

    /**
     * Returns assigned product id
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->oxobject2category__oxobjectid->value;
    }

    /**
     * Sets assigned product id
     *
     * @param string $sId assigned product id
     */
    public function setProductId($sId)
    {
        $this->oxobject2category__oxobjectid = new \OxidEsales\Eshop\Core\Field($sId);
    }

    /**
     * Returns assigned category id
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->oxobject2category__oxcatnid->value;
    }

    /**
     * Sets assigned category id
     *
     * @param string $sId assigned category id
     */
    public function setCategoryId($sId)
    {
        $this->oxobject2category__oxcatnid = new \OxidEsales\Eshop\Core\Field($sId);
    }
}
