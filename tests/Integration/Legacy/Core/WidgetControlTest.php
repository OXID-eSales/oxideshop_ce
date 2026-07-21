<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core;

use OxidEsales\Eshop\Application\Controller\SearchController;
use OxidEsales\Eshop\Core\Exception\ObjectException;
use OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver;
use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class WidgetControlTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        $configuration = (new ThemeConfiguration())
            ->setId('testTheme')
            ->setSource('testSourcePath')
            ->setActivated(true);
        $configuration->addThemeSetting(
            (new Setting())->setName('defaultListDisplayType')->setType('str')->setValue('infogrid')
        );
        $configuration->addThemeSetting(
            (new Setting())->setName('numberOfCategoryProducts')->setType('arr')->setValue(['10', '20', '50', '100'])
        );
        $shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, $shopId);

        $this->setParameter('oxid_esales.debug_mode', true);
        $this->replaceContainerInstance();
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    public function testIfDoesNotAllowToInitiateNonWidgetClass(): void
    {
        $nonWidgetClass = (new ControllerClassNameResolver())
            ->getIdByClassName(SearchController::class);

        $this->expectException(ObjectException::class);

        oxNew(WidgetControl::class)
            ->start($nonWidgetClass);
    }
}
