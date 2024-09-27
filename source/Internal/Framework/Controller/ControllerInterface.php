<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Controller;

interface ControllerInterface
{
    public function init();

    public function render();

    public function executeFunction($function);

    public function setClassKey($classKey);

    public function getClassKey();

    public function setFncName($fncName);

    public function getFncName();

    public function setViewParameters($params = null);

    public function getViewParameter($key);

    public function setViewData($viewData = null);

    public function getViewData();

    public function getViewId();

    public function getCharSet();

    public function getIsCallForCache();
}
