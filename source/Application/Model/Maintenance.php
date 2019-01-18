<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Maintenance task handler. Maintenance tasks are called periodically, by cronTab (configure on your needs)
 *
 */
class Maintenance
{
    /**
     * Executes maintenance tasks. Currently calls oxArticleList::updateUpcomingPrices()
     */
    public function execute()
    {
        // updating upcoming prices
        oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class)->updateUpcomingPrices(true);
    }
}
