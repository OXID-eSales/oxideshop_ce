<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\unifiednamespace_module1\Model;

/**
 * CMS - loads pages and displays it
 */
class Module1TestContent
{
    /**
     * Template variable getter. Returns tag title
     *
     * @return string
     */
    public function getTitle()
    {
        return "- Module_1_Model";
    }
}
