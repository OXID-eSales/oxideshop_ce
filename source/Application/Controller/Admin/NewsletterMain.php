<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin Menu: Customer Info -> Newsletter -> Main.
 *
 * @deprecated Will be removed in next major
 */
class NewsletterMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    public function render()
    {
        parent::render();
        return "newsletter_main.tpl";
    }
}
