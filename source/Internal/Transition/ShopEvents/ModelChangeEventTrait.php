<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

trait ModelChangeEventTrait
{
    /**
     * Model object
     *
     * @var \OxidEsales\Eshop\Core\Model\BaseModel
     */
    private $model;

    /**
     * Constructor
     *
     * @param \OxidEsales\Eshop\Core\Model\BaseModel $model Model class object
     */
    public function __construct(\OxidEsales\Eshop\Core\Model\BaseModel $model)
    {
        $this->model = $model;
    }

    /**
     * Getter for model class name.
     *
     * @return string
     */
    public function getModel(): \OxidEsales\Eshop\Core\Model\BaseModel
    {
        return $this->model;
    }
}
