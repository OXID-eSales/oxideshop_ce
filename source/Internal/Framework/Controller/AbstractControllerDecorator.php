<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Controller;

abstract class AbstractControllerDecorator implements ControllerInterface
{
    public function __construct(
        protected readonly ControllerInterface $controller,
    ) {
    }

    public function init()
    {
        $this->controller->init();
    }

    public function render()
    {
        return $this->controller->render();
    }

    public function getFncName()
    {
        return $this->controller->getFncName();
    }

    public function executeFunction($function)
    {
        $this->controller->executeFunction($function);
    }

    public function getIsCallForCache()
    {
        return $this->controller->getIsCallForCache();
    }

    public function getClassKey()
    {
        return $this->controller->getClassKey();
    }

    public function getViewData()
    {
        return $this->controller->getViewData();
    }

    public function setViewData($viewData = null)
    {
        $this->controller->setViewData($viewData);
    }

    public function getViewId()
    {
        return $this->controller->getViewId();
    }

    public function getCharSet()
    {
        return $this->controller->getCharSet();
    }

    public function setClassKey($classKey)
    {
        $this->controller->setClassKey($classKey);
    }

    public function setFncName($sFncName)
    {
        $this->controller->setFncName($sFncName);
    }

    public function setViewParameters($aParams = null)
    {
        $this->controller->setViewParameters($aParams);
    }
}
