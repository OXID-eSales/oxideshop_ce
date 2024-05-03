<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\unifiednamespace_module1\Model;

/**
 * CMS - loads pages and displays it
 */
class Module1TestContent
{
    /**
     * Template variable getter. Returns tag title
     */
    public function getTitle(): string
    {
        return '- Module_1_Model';
    }
}
