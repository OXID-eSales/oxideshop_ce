<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class Controller
{
    /**
     * ClassExtension constructor.
     */
    public function __construct(private string $id, private string $controllerClassNameSpace)
    {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getControllerClassNameSpace(): string
    {
        return $this->controllerClassNameSpace;
    }
}
