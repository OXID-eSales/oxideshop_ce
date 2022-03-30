<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;

class SmartySecuritySettingsDataProvider implements SmartySecuritySettingsDataProviderInterface
{
    /**
     * SmartySecuritySettingsDataProvider constructor.
     */
    public function __construct(private SmartyContextInterface $context)
    {
    }

    /**
     * Define and return smarty security settings.
     *
     * @return array
     */
    public function getSecuritySettings(): array
    {
        return [
            'php_handling' => SMARTY_PHP_REMOVE,
            'security' => true,
            'secure_dir' => $this->context->getTemplateDirectories(),
            'security_settings' => [
                'IF_FUNCS' => ['XML_ELEMENT_NODE', 'is_int'],
                'MODIFIER_FUNCS' => ['round', 'floor', 'trim', 'implode', 'is_array', 'getimagesize'],
                'ALLOW_CONSTANTS' => true,
            ],
        ];
    }
}
