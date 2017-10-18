<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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

        $content = oxNew('Test1Content');
        $contentTitle = $contentTitle . " - Module_1_Controller " . $content->getTitle();

        return $contentTitle;
    }
}
