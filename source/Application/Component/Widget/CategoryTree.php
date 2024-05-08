<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace  OxidEsales\EshopCommunity\Application\Component\Widget;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;

use function basename;

class CategoryTree extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * Cartegory component used in template.
     *
     * @var array
     */
    protected $_aComponentNames = ['oxcmp_categories' => 1];

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/sidebar/categorytree';

    /**
     * @return string
     */
    public function render()
    {
        parent::render();

        $widgetType = $this->getViewParameter('sWidgetType');
        if (!$widgetType) {
            return $this->_sThisTemplate;
        }
        $template = sprintf(
            'widget/%s/categorylist',
            basename($widgetType)
        );
        $templateExists = ContainerFacade::get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer()
            ->exists($template);
        if ($templateExists) {
            $this->_sThisTemplate = $template;
        }

        return $this->_sThisTemplate;
    }

    /**
     * Returns the deep level of category tree
     *
     * @return null
     */
    public function getDeepLevel()
    {
        return $this->getViewParameter("deepLevel");
    }

    /**
     * Content category getter.
     *
     * @return bool|string
     */
    public function getContentCategory()
    {
        return Registry::getRequest()->getRequestParameter('oxcid', false);
    }
}
