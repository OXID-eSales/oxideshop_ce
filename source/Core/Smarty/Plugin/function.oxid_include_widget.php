<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: set params and render widget
 * Use [{oxid_include_dynamic file="..."}] instead of include
 * -------------------------------------------------------------
 *
 * @param array  $params  Params
 * @param Smarty $oSmarty Clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxid_include_widget($params, &$smarty)
{
    $event = new OxidEsales\EshopCommunity\Internal\ShopEvents\IncludeWidgetEvent();
    $event->setParameters($params);
    $event->setSmarty($smarty);

    $container = \OxidEsales\EshopCommunity\Internal\Application\ContainerFactory::getInstance()->getContainer();
    $dispatcher = $container->get('event_dispatcher');
    $event = $dispatcher->dispatch($event::NAME, $event);

    if (!($result = $event->getResult())) {
        $class = isset($params['cl']) ? strtolower($params['cl']) : '';
        unset($params['cl']);
        $parentViews = null;
        if (!empty($params["_parent"])) {
            $parentViews = explode("|", $params["_parent"]);
            unset($params["_parent"]);
        }
        $shopControl = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\WidgetControl::class);
        $result = $shopControl->start($class, null, $params, $parentViews);
    }

    return $result;
}
