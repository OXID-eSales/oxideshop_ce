<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Application\Model\Actions;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\News;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

final class UtilsViewTest extends UnitTestCase
{
    /** @var string */
    private $smartyUnparsedContent = '[{1|cat:2|cat:3}]';
    /** @var string  */
    private $smartyParsedContent = '123';

    public function testDisableSmartyForCmsContentWithProduct(): void
    {
        $model = oxNew(Article::class);
        $model->setArticleLongDesc($this->smartyUnparsedContent);

        $this->assertSame($this->smartyParsedContent, $model->getLongDesc());
        Registry::getConfig()->setConfigParam('deactivateSmartyForCmsContent', true);
        $this->assertSame($this->smartyUnparsedContent, $model->getLongDesc());
    }

    public function testDisableSmartyForCmsContentWithCategory(): void
    {
        $model = oxNew(Category::class);
        $model->oxcategories__oxlongdesc = new Field($this->smartyUnparsedContent);

        $this->assertSame($this->smartyParsedContent, $model->getLongDesc());
        Registry::getConfig()->setConfigParam('deactivateSmartyForCmsContent', true);
        $this->assertSame($this->smartyUnparsedContent, $model->getLongDesc());
    }

    public function testDisableSmartyForCmsContentWithAction(): void
    {
        $model = oxNew(Actions::class);
        $model->oxactions__oxlongdesc = new Field($this->smartyUnparsedContent);

        $this->assertSame($this->smartyParsedContent, $model->getLongDesc());
        Registry::getConfig()->setConfigParam('deactivateSmartyForCmsContent', true);
        $this->assertSame($this->smartyUnparsedContent, $model->getLongDesc());
    }

    public function testDisableSmartyForCmsContentWithNews(): void
    {
        $model = oxNew(News::class);
        $model->oxnews__oxlongdesc = new Field($this->smartyUnparsedContent);

        $this->assertSame($this->smartyParsedContent, $model->getLongDesc());
        Registry::getConfig()->setConfigParam('deactivateSmartyForCmsContent', true);
        $this->assertSame($this->smartyUnparsedContent, $model->getLongDesc());
    }
}
