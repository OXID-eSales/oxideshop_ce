<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ApplicationModelChangedEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class ApplicationModelChangedEvent extends Event
{
    const NAME = self::class;

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
