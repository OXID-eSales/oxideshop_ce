<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;

class SmartySettingsDataProvider implements SmartySettingsDataProviderInterface
{
    /**
     * @var SmartyContextInterface
     */
    private $context;

    /**
     * SmartySettingsDataProvider constructor.
     */
    public function __construct(SmartyContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Define and return basic smarty settings.
     */
    public function getSettings(): array
    {
        $compilePath = $this->getTemplateCompilePath();

        return [
            'caching' => false,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'compile_dir' => $compilePath,
            'cache_dir' => $compilePath,
            'template_dir' => $this->context->getTemplateDirectories(),
            'compile_id' => $this->getTemplateCompileId(),
            'default_template_handler_func' => [Registry::getUtilsView(), '_smartyDefaultTemplateHandler'],
            'debugging' => $this->context->getTemplateEngineDebugMode(),
            'compile_check' => $this->context->getTemplateCompileCheckMode(),
            'php_handling' => (int)$this->context->getTemplatePhpHandlingMode(),
            'security' => false,
        ];
    }

    /**
     * Returns a full path to Smarty compile dir.
     */
    private function getTemplateCompilePath(): string
    {
        return $this->context->getTemplateCompileDirectory();
    }

    /**
     * Get template compile id.
     */
    private function getTemplateCompileId(): string
    {
        return $this->context->getTemplateCompileId();
    }
}
