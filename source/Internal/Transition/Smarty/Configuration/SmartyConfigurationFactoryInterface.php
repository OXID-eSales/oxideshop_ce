<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Smarty\Configuration;

/**
 * Class SmartyConfigurationFactoryInterface
 *
 * @package OxidEsales\EshopCommunity\Internal\Smarty\Configuration
 */
interface SmartyConfigurationFactoryInterface
{
    /**
     * @return SmartyConfigurationInterface
     */
    public function getConfiguration(): SmartyConfigurationInterface;
}
