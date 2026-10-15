<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ThemeSettingServiceInterface;

abstract class AbstractInsertNewBasketItemLogic
{
    public function __construct(
        private readonly ThemeSettingServiceInterface $themeSettingService,
    ) {
    }

    /**
     * @param array  $params
     * @param object $templateEngine
     *
     * @return string
     */
    public function getNewBasketItemTemplate(array $params, $templateEngine): string
    {
        if (!$this->validateTemplateEngine($templateEngine)) {
            throw new \Exception('Please check if correct template engine is used.');
        }
        $renderedTemplate = '';

        $types = ['0' => 'none', '1' => 'message', '2' => 'popup', '3' => 'basket'];
        $newBasketItemMessage = $this->themeSettingService->getInteger('newBasketItemMessage');

        // If correct type of message is expected
        if (
            $newBasketItemMessage && $params['type']
            && (!isset($types[$newBasketItemMessage])
                || $params['type'] != $types[$newBasketItemMessage])
        ) {
            $correctMessageType = false;
        } else {
            $correctMessageType = true;
        }

        // name of template file where is stored message text
        $templateName = $params['tpl'] ? $params['tpl'] : 'inc_newbasketitem.snippet.html.twig';

        // always render for ajaxstyle popup
        $render = isset($params['ajax']) && $params['ajax'] && ($newBasketItemMessage == 2);

        // fetching article data
        $newItem = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('_newitem');

        if ($newItem && $correctMessageType) {
            $this->loadArticleObject($newItem, $templateEngine);
            $render = true;
        }

        // returning generated message content
        if ($render && $correctMessageType) {
            $renderedTemplate = $this->renderTemplate($templateName, $templateEngine);
        }

        return $renderedTemplate;
    }

    /**
     * @param object $templateEngine
     *
     * @return mixed
     */
    abstract protected function validateTemplateEngine($templateEngine);

    /**
     * @param object $newItem
     * @param object $templateEngine
     *
     * @return mixed
     */
    abstract protected function loadArticleObject($newItem, $templateEngine);

    /**
     * @param string $templateName
     * @param object $templateEngine
     *
     * @return mixed
     */
    abstract protected function renderTemplate(string $templateName, $templateEngine);

    public function getGlobals(): array
    {
        return [];
    }
}
