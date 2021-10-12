<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Application\Model\Actions;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

final class UtilsViewTest extends UnitTestCase
{
    /** @var string */
    private string $smartyUnparsedContent = '[{1|cat:2|cat:3}]';
    /** @var string  */
    private string $smartyParsedContent = '123';

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
}
