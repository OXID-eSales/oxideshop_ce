<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Application\Controller\ContentController;
use OxidEsales\Eshop\Application\Model\Content;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

final class ContentControllerTest extends UnitTestCase
{
    private string $smartyTagsContent = '[{1|cat:2|cat:3}]';
    private string $smartyParsedContent = '123';
    private string $testSmartyContentId = 'test-smarty-content';
    private ContentController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareTestData();
    }

    public function testGetParsedContent(): void
    {
        $parsedContent = $this->controller->getParsedContent();

        $this->assertSame($this->smartyParsedContent, $parsedContent);
    }

    public function testGetParsedContentWithConfigurationOff(): void
    {
        Registry::getConfig()->setConfigParam('deactivateSmartyForCmsContent', true);

        $parsedContent = $this->controller->getParsedContent();

        $this->assertSame($this->smartyTagsContent, $parsedContent);
    }

    private function prepareTestData(): void
    {
        $content = oxNew(Content::class);
        $content->oxcontents__oxcontent = new Field($this->smartyTagsContent);
        $content->setId($this->testSmartyContentId);
        $content->oxcontents__oxloadid = new Field($this->testSmartyContentId);
        $content->oxcontents__oxactive = new Field(1);
        $content->save();

        $_GET['oxloadid'] = $this->testSmartyContentId;
        $this->controller = oxNew(ContentController::class);
    }
}
