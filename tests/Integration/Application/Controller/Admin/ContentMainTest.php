<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ContentMain;
use OxidEsales\Eshop\Application\Model\Content;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ContentMainTest extends IntegrationTestCase
{
    use ContainerTrait;

    private string $contentId = 'testContentId';
    private string $safeHtml = '<div><a>Click</a><img src="x" /></div>';
    private string $unsafeHtml = '<div onclick="alert(1)"><script>evil()</script>'
        . '<a href="javascript:alert(1)">Click</a><img src="x" onerror="evil()"><iframe src="evil.html"></div>';

    private ContentMain $contentMain;

    public function setUp(): void
    {
        parent::setUp();
        $this->contentMain = $this->createContentMain();
        $this->createContainer();
    }

    public function tearDown(): void
    {
        ContainerFactory::resetContainer();
        parent::tearDown();
    }

    public function testSanitizerShouldRemoveUnsafeHtmlContent(): void
    {
        $this->setParameter('oxid_esales.html_sanitizer_enabled', true);
        $this->mockContentRequest($this->unsafeHtml);

        $this->contentMain->save();

        $content = $this->loadContent($this->contentId);
        $this->assertEquals($this->safeHtml, $content->oxcontents__oxcontent->value);
    }

    public function testSaveShouldPreserveEmptyContent(): void
    {
        $this->setParameter('oxid_esales.html_sanitizer_enabled', true);
        $this->mockContentRequest('');

        $this->contentMain->save();

        $content = $this->loadContent($this->contentId);
        $this->assertEmpty($content->oxcontents__oxcontent->value);
    }

    public function testDisabledSanitizerShouldPreserveAllElements(): void
    {
        $this->setParameter('oxid_esales.html_sanitizer_enabled', false);
        $this->mockContentRequest($this->unsafeHtml);

        $this->contentMain->save();

        $content = $this->loadContent($this->contentId);
        $this->assertEquals($this->unsafeHtml, $content->oxcontents__oxcontent->value);
    }

    public function testSanitizerShouldHandleMalformedHtml(): void
    {
        $this->setParameter('oxid_esales.html_sanitizer_enabled', true);
        $malformedHtml = '<div><span>missing closing div';
        $this->mockContentRequest($malformedHtml);

        $this->contentMain->save();

        $content = $this->loadContent($this->contentId);
        $this->assertStringContainsString($malformedHtml, $content->oxcontents__oxcontent->value);
    }

    public function testIdentShouldBePreparedAndPersisted(): void
    {
        $this->setParameter('oxid_esales.html_sanitizer_enabled', true);

        $this->mockPostRequest([
            'oxid' => $this->contentId,
            'editval' => $this->createEditValArray('a-b_c.d!', 'Test content')
        ]);

        $this->contentMain->save();

        $content = $this->loadContent($this->contentId);
        $this->assertEquals('ab_cd', $content->oxcontents__oxloadid->value);
    }

    public function testDuplicateIdentShouldTriggerError(): void
    {
        $this->setParameter('oxid_esales.html_sanitizer_enabled', true);

        $ident = 'abc';
        $this->mockPostRequest([
            'oxid' => $this->contentId,
            'editval' => $this->createEditValArray($ident, 'Test content 1')
        ]);
        $this->contentMain->setEditObjectId($this->contentId);
        $this->contentMain->save();

        $this->mockPostRequest([
            'oxid' => 'other-content-id',
            'editval' => $this->createEditValArray($ident, 'Test content 2')
        ]);
        $this->contentMain->setEditObjectId('other-content-id');
        $this->contentMain->save();

        $this->assertTrue($this->contentMain->getViewData()['blLoadError']);
    }

    public function testSaveinnlangShouldRemoveUnsafeHtmlWhenSanitizerEnabled(): void
    {
        $this->setParameter('oxid_esales.html_sanitizer_enabled', true);

        $newLang = 1;
        $this->mockPostRequest([
            'oxid' => $this->contentId,
            'new_lang' => $newLang,
            'editval' => $this->createEditValArray(uniqid(), $this->unsafeHtml)
        ]);

        $this->contentMain->saveinnlang();

        $content = $this->loadContent($this->contentId);
        $content->loadInLang($newLang, $this->contentId);
        $this->assertEquals($this->safeHtml, $content->oxcontents__oxcontent->value);
    }

    public function testSaveinnlangShouldPreserveUnsafeHtmlWhenSanitizerDisabled(): void
    {
        $this->setParameter('oxid_esales.html_sanitizer_enabled', false);

        $newLang = 1;
        $this->mockPostRequest([
            'oxid' => $this->contentId,
            'new_lang' => $newLang,
            'editval' => $this->createEditValArray(uniqid(), $this->unsafeHtml)
        ]);

        $this->contentMain->saveinnlang();

        $content = $this->loadContent($this->contentId);
        $content->loadInLang($newLang, $this->contentId);
        $this->assertEquals($this->unsafeHtml, $content->oxcontents__oxcontent->value);
    }

    private function createContentMain(): ContentMain
    {
        $contentMain = new ContentMain();
        Registry::getConfig()->setAdminMode(true);
        Registry::getSession()->setVariable('blIsAdmin', true);

        return $contentMain;
    }

    private function mockContentRequest(string $content): void
    {
        $this->mockPostRequest([
            'oxid' => $this->contentId,
            'editval' => $this->createEditValArray(uniqid(), $content)
        ]);
    }

    private function createEditValArray(string $loadId, string $content): array
    {
        return [
            'oxcontents__oxid' => $this->contentId,
            'oxcontents__oxloadid' => $loadId,
            'oxcontents__oxcontent' => $content,
            'oxcontents__oxtype' => '0',
            'oxcontents__oxfolder' => 'CMSFOLDER_NONE',
        ];
    }

    private function mockPostRequest(array $data): void
    {
        $_POST = $data;
    }

    private function loadContent(string $id): Content
    {
        $content = new Content();
        $content->load($id);
        return $content;
    }
}
