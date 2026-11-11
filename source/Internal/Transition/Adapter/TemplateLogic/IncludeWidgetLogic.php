<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use Exception;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;

class IncludeWidgetLogic
{
    /**
     * @param array $params
     *
     * @return mixed
     */
    public function renderWidget(array $params)
    {
        $class = '';
        if (isset($params['cl'])) {
            $class = strtolower($params['cl']);
            unset($params['cl']);
        }

        $parentViews = null;
        if (!empty($params["_parent"])) {
            $parentViews = explode("|", $params["_parent"]);
            unset($params["_parent"]);
        }

        /**
         * @var WidgetControl $widgetControl
         */
        $widgetControl = Registry::get(WidgetControl::class);

        try {
            return $widgetControl->buildWidgetResponse($class, '', $params, $parentViews)->getContent();
        } catch (Exception $exception) {
            if (ContainerFacade::getParameter('oxid_esales.debug_mode')) {
                throw $exception;
            }
            Registry::getLogger()->error($exception->getMessage(), [$exception]);

            return '';
        }
    }
}
