<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwCategoryTree class
 */
class WidgetTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * @covers \OxidEsales\Eshop\Application\Component\Widget\WidgetController::init()
     */
    public function testInitComponentNotSet()
    {
        $languageList = oxNew(\OxidEsales\Eshop\Application\Component\Widget\LanguageList::class);
        $languageList->init();

        $components = $languageList->getComponents();
        $this->assertEquals(1, count($components));
        $this->assertEquals('oxidesales\eshop\application\component\languagecomponent', $components["oxcmp_lang"]->getThisAction());
    }

    /**
     * @covers \OxidEsales\Eshop\Application\Component\Widget\WidgetController::init()
     */
    public function testInitComponentIsSet()
    {
        $components["oxcmp_lang"] = oxNew(\OxidEsales\Eshop\Application\Component\LanguageComponent::class);
        $view = oxNew(\OxidEsales\Eshop\Application\Controller\ArticleDetailsController::class);
        $view->setComponents($components);
        $this->getConfig()->setActiveView($view);

        $languageListWidget = oxNew(\OxidEsales\Eshop\Application\Component\Widget\LanguageList::class);
        $languageListWidget->init();

        $components = $languageListWidget->getComponents();
        $this->assertEquals(1, count($components));
        $this->assertTrue(isset($components["oxcmp_lang"]));
    }
}
