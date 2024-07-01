<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ViewConfig;
use Twig\Environment;

class InsertNewBasketItemLogicTwig extends AbstractInsertNewBasketItemLogic
{
    /**
     * @param Environment $templateEngine
     *
     * @return bool
     */
    protected function validateTemplateEngine($templateEngine): bool
    {
        return $templateEngine instanceof Environment;
    }

    /**
     * @param object      $newItem
     * @param Environment $templateEngine
     */
    protected function loadArticleObject($newItem, $templateEngine)
    {
        // loading article object here because on some system passing article by session causes problems
        $newItem->oArticle = oxNew(Article::class);
        $newItem->oArticle->load($newItem->sId);

        // passing variable to template with unique name
        $templateEngine->addGlobal('_newitem', clone $newItem);

        // deleting article object data
        Registry::getSession()->deleteVariable('_newitem');
    }

    /**
     * @param string      $templateName
     * @param Environment $templateEngine
     *
     * @return string
     */
    protected function renderTemplate(string $templateName, $templateEngine): string
    {
        $template = $templateEngine->load($templateName);

        return $template->render();
    }

    public function getGlobals(): array
    {
        return [
            '_newitem' => null,
            'oViewConf' => oxNew(ViewConfig::class),
            'oxcmp_basket' => Registry::getSession()->getBasket(),
        ];
    }
}
