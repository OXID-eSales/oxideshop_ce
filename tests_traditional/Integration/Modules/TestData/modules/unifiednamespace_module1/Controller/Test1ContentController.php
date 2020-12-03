<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\unifiednamespace_module1\Controller;

use OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\unifiednamespace_module1\Model\Module1TestContent;

/**
 * CMS - loads pages and displays it
 */
class Test1ContentController extends Test1ContentController_parent
{
    /**
     * Template variable getter. Returns tag title
     *
     * @return string
     */
    public function getTitle()
    {
        $contentTitle = parent::getTitle();

        $content = oxNew(Module1TestContent::class);
        $contentTitle = $contentTitle . " - Module_1_Controller " . $content->getTitle();

        return $contentTitle;
    }
}
