<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component\Widget;

use OxidEsales\Eshop\Core\Registry;

/**
 * Widget parent.
 * Gather functionality needed for all widgets but not for other views.
 */
class WidgetController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * Widget should rewrite and use only those which  it needs.
     *
     * @var array
     */
    protected $_aComponentNames = [];

    /**
     * If active load components
     * Widgets loads active view components
     *
     * @var array
     */
    protected $_blLoadComponents = false;

    /**
     * Sets self::$_aCollectedComponentNames to null, as views and widgets
     * controllers loads different components and calls parent::init()
     */
    public function init()
    {
        self::$_aCollectedComponentNames = null;

        $topActiveView = Registry::getConfig()->getTopActiveView();

        foreach ($this->_aComponentNames as $componentName => $compCache) {
            $this->_oaComponents[$componentName] = $topActiveView->getComponent($componentName);
            if (!isset($this->_oaComponents[$componentName])) {
                $this->_blLoadComponents = true;
                break;
            }
            $this->_oaComponents[$componentName]->setParent($this);
        }

        parent::init();
    }

    public function render()
    {
        if (Registry::getConfig()->getTopActiveView() === $this) {
            return '';
        }

        return parent::render();
    }

    /**
     * In widgets we do not need to parse seo and do any work related to that
     * Shop main control is responsible for that, and that has to be done once
     */
    protected function processRequest()
    {
    }
}
