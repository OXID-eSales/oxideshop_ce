<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Controller;

interface ControllerInterface
{
    public function setClassKey($classKey);

    public function setFncName($sFncName);

    public function setViewParameters($aParams = null);

    public function init();

    public function getFncName();

    public function executeFunction($function);

    public function getIsCallForCache();

    public function render();

    public function getClassKey();

    public function getViewData();

    public function setViewData($viewData = null);

    public function getViewId();

    public function getCharSet();
}
