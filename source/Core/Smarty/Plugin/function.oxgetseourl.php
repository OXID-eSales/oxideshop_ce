<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\OxgetseourlLogic;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;


/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: output SEO style url
 * add [{oxgetseourl ident="..."}] where you want to display content
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxgetseourl( $params, &$smarty )
{
    $container = ContainerFactory::getInstance()->getContainer();
    /** @var OxgetseourlLogic $oxgetseourlLogic */
    $oxgetseourlLogic = $container->get(OxgetseourlLogic::class);

    return $oxgetseourlLogic->oxgetseourl($params);
}
