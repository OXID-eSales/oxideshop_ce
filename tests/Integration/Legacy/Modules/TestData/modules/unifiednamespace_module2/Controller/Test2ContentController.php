<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\unifiednamespace_module2\Controller;

/**
 * CMS - loads pages and displays it
 */
class Test2ContentController extends Test2ContentController_parent
{
    /**
     * Template variable getter. Returns tag title
     *
     * @return string
     */
    public function getTitle()
    {
        $contentTitle = parent::getTitle();
        $contentTitle = $contentTitle . " - Module_2_Controller";// . $content->getTitle();

        return $contentTitle;
    }
}
