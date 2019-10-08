<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use Smarty;

class InsertNewBasketItemLogicSmarty extends AbstractInsertNewBasketItemLogic
{
    /**
     * @param Smarty $templateEngine
     *
     * @return bool
     */
    protected function validateTemplateEngine($templateEngine)
    {
        return ($templateEngine instanceof Smarty);
    }

    /**
     * @param object $newItem
     * @param Smarty $templateEngine
     */
    protected function loadArticleObject($newItem, $templateEngine)
    {
        // loading article object here because on some system passing article by session causes problems
        $newItem->oArticle = oxNew('oxarticle');
        $newItem->oArticle->Load($newItem->sId);

        // passing variable to template with unique name
        $templateEngine->assign('_newitem', $newItem);

        // deleting article object data
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable('_newitem');
    }

    /**
     * @param string $templateName
     * @param Smarty $templateEngine
     *
     * @return string
     */
    protected function renderTemplate(string $templateName, $templateEngine)
    {
        $renderedTemplate = $templateEngine->fetch($templateName);

        return $renderedTemplate;
    }
}
