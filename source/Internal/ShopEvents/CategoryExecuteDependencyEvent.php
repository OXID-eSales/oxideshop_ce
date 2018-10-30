<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class CategoryExecuteDependencyEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class CategoryExecuteDependencyEvent extends Event
{
    const NAME = 'oxidesales.category.executedependencyevent';

    /**
     * Category ids
     *
     * @var array
     */
    protected $categoryIds = [];

    /**
     * Flush articles flag.
     *
     * @var bool
     */
    protected $flushArticles = true;

    /**
     * Check if articles need to be flushed.
     *
     * @return bool
     */
    public function isFlushArticles()
    {
        return $this->flushArticles;
    }

    /**
     * Setter for flush articles flag.
     *
     * @param bool $flushArticles Flush articles yes/no
     */
    public function setFlushArticles($flushArticles)
    {
        $this->flushArticles = $flushArticles;
    }

    /**
     * Getter for category ids
     *
     * @return array
     */
    public function getCategoryIds()
    {
        return $this->categoryIds;
    }

    /**
     * Setter for category ids.
     *
     * @param array $categoryIds
     */
    public function setCategoryIds($categoryIds)
    {
        $this->categoryIds = $categoryIds;
    }
}