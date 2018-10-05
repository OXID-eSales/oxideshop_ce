<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\AddUrlParametersLogic;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: add additional parameters to SEO url
 * add |oxaddparams:"...." to link
 * -------------------------------------------------------------
 *
 * @param string $sUrl       Url
 * @param string $sDynParams Dynamic URL parameters
 *
 * @return string
 */
function smarty_modifier_oxaddparams( $sUrl, $sDynParams )
{
    /** @var AddUrlParametersLogic $addUrlParametersLogic */
    $addUrlParametersLogic = ContainerFactory::getInstance()->getContainer()->get(AddUrlParametersLogic::class);

    return $addUrlParametersLogic->addUrlParameters($sUrl, $sDynParams);
}
