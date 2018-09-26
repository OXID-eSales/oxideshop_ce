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
    /** @var OxgetseourlLogic $oxgetseourlLogic */
    $oxgetseourlLogic = ContainerFactory::getInstance()->getContainer()->get(OxgetseourlLogic::class);

    $sUrl = $oxgetseourlLogic->oxgetseourl($params);

    $sDynParams = isset( $params['params'] )?$params['params']:false;
    if ( $sDynParams ) {
        include_once $smarty->_get_plugin_filepath( 'modifier', 'oxaddparams' );
        $sUrl = smarty_modifier_oxaddparams( $sUrl, $sDynParams );
    }

    return $sUrl;
}
