<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * CMS - loads pages and displays it
 */
class Test3ContentController extends Test3ContentController_parent
{
    /**
     * Template variable getter. Returns tag title
     *
     * @return string
     */
    public function getTitle()
    {
        $contentTitle = parent::getTitle();

        // The commented things are not working atm or will not be implemented.
        // $content = new Test1Content();
        $contentTitle = $contentTitle . "Mod3";

        return $contentTitle;
    }
}
