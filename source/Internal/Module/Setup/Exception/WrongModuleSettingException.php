<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Exception;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;

/**
 * @internal
 */
class WrongModuleSettingException extends \Exception
{
    /**
     * @param ModuleSetting $moduleSetting
     * @param string        $handlerName
     */
    public function __construct(ModuleSetting $moduleSetting, string $handlerName)
    {
        $message = 'The setting ' . $moduleSetting->getName() . ' can not be handled by the ' . $handlerName . '.';
        parent::__construct($message);
    }
}
