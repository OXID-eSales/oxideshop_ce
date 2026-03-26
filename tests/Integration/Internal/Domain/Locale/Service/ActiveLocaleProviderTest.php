<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Locale\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Dao\LocaleDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\ActiveLocaleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use OxidEsales\Eshop\Core\Registry;
use PHPUnit\Framework\TestCase;

final class ActiveLocaleProviderTest extends TestCase
{
    use DatabaseTrait;
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginTransaction($this->get(ConnectionFactoryInterface::class)->create());
    }

    public function tearDown(): void
    {
        $this->rollBackTransaction($this->get(ConnectionFactoryInterface::class)->create());
        parent::tearDown();
    }

    public function testReturnsLocaleForCurrentLanguage(): void
    {
        $this->get(LocaleDaoInterface::class)->add(new Locale(code: 'te_ST', name: 'Test', fallbackCode: 'te_ST'));

        $config = Registry::getConfig();
        $params = $config->getConfigParam('aLanguageParams');
        $abbr = Registry::getLang()->getLanguageAbbr();
        $params[$abbr]['locale'] = 'te_ST';
        $config->saveShopConfVar('aarr', 'aLanguageParams', $params);

        $result = $this->get(ActiveLocaleProviderInterface::class)->getActiveLocale();

        $this->assertSame('te_ST', $result->getCode());
    }

    public function testReturnsFallbackWhenNoLocaleAssigned(): void
    {
        $config = Registry::getConfig();
        $params = $config->getConfigParam('aLanguageParams');
        $abbr = Registry::getLang()->getLanguageAbbr();
        unset($params[$abbr]['locale']);
        $config->saveShopConfVar('aarr', 'aLanguageParams', $params);

        $result = $this->get(ActiveLocaleProviderInterface::class)->getActiveLocale();

        $this->assertSame('de_DE', $result->getCode());
    }
}
