<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

/**
 * Template preparation class.
 * Used only in some specific cases (usually when you need to outpt just template
 * having text information).
 */
class TemplateController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /** @inheritdoc */
    public function render()
    {
        parent::render();

        // security fix so that you cant access files from outside template dir
        $sTplName = basename((string) Registry::getRequest()->getRequestEscapedParameter("tpl"));
        if ($sTplName) {
            $sTplName = 'custom/' . $sTplName;
        }

        return $sTplName;
    }
}
