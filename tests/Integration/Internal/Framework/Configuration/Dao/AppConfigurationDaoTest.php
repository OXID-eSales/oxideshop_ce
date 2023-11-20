<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Configuration\Dao;

use OxidEsales\EshopCommunity\Application\Model\ArticleList;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\Dao\AppConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class AppConfigurationDaoTest extends TestCase
{
    use ContainerTrait;

    public function testGetWillWork(): void
    {
        $appConfig = ContainerFacade::get(AppConfigurationDaoInterface::class)->get();

        $oxClass = oxNew(ArticleList::class);
        $oxClass->loadActionArticles('oxtopstart');
        $this->assertCount(6, $oxClass);
    }
}
