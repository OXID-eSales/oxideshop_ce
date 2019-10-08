<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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
     *
     * @param string $id
     * @param string $controllerClassNameSpace
     */
    public function __construct(string $id, string $controllerClassNameSpace)
    {
        $this->id = $id;
        $this->controllerClassNameSpace = $controllerClassNameSpace;
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
