<?php

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Module\Fixtures\with_class_extenstions2\Controllers;

use OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\namespace1\Models\Content;

/**
 * Class ContentController
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Core\Module\Fixtures\with_class_extenstions2\Controllers
 */
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
        $this->_oContent->oxcontents__oxtitle->setValue($this->_oContent->oxcontents__oxtitle.$content->testContent());

        return $sTpl;
    }

    /**
     *
     */
    public function showContent()
    {
        $content = oxNew(Content::class);

        echo $content->testContent();
    }
}
