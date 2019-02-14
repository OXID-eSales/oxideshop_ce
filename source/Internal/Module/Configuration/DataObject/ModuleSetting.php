<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject;

/**
 * @internal
 */
class ModuleSetting
{
    const CLASS_EXTENSIONS          = 'classExtensions';
    const CONTROLLERS               = 'controllers';
    const TEMPLATES                 = 'templates';
    const SMARTY_PLUGIN_DIRECTORIES = 'smartyPluginDirectories';
    const TEMPLATE_BLOCKS           = 'blocks';
    const SHOP_MODULE_SETTING       = 'settings';
    const EVENTS                    = 'events';
    const CLASSES_WITHOUT_NAMESPACE = 'classesWithoutNamespace';

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * ModuleSetting constructor.
     * @param string $name
     * @param mixed  $value
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
