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
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $controllerClassNameSpace;

    /**
     * ClassExtension constructor.
     */
    public function __construct(string $id, string $controllerClassNameSpace)
    {
        $this->id = $id;
        $this->controllerClassNameSpace = $controllerClassNameSpace;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getControllerClassNameSpace(): string
    {
        return $this->controllerClassNameSpace;
    }
}
