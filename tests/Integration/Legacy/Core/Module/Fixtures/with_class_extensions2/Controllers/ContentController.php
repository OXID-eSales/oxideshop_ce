<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core\Module\Fixtures\with_class_extenstions2\Controllers;

use OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\namespace1\Models\Content;

class ContentController extends ContentController_parent
{
    /**
     * @return mixed
     */
    public function render()
    {
        $sTpl = parent::render();

        /** @var Content $content */
        $content = oxNew(Content::class);
        $this->_oContent->oxcontents__oxtitle->setValue(
            $this->_oContent->oxcontents__oxtitle . $content->testContent()
        );

        return $sTpl;
    }

    public function showContent(): void
    {
        $content = oxNew(Content::class);

        echo $content->testContent();
    }
}
