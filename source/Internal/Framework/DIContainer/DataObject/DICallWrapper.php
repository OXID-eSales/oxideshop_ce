<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject;

class DICallWrapper
{
    private const METHOD_KEY = 'method';
    private const PARAMETER_KEY = 'arguments';

    private $callArray;

    /**
     * DICallWrapper constructor.
     *
     * @param array $callArray
     */
    public function __construct(array $callArray = [])
    {
        if (!$callArray) {
            $this->callArray = [static::METHOD_KEY => '', static::PARAMETER_KEY => []];
        } else {
            $this->callArray = $callArray;
        }
    }

    /**
     * @return string
     */
    public function getMethodName(): string
    {
        return $this->callArray[static::METHOD_KEY];
    }

    /**
     * @param string $methodName
     */
    public function setMethodName(string $methodName)
    {
        $this->callArray[static::METHOD_KEY] = $methodName;
    }

    /**
     * @param int   $index
     * @param mixed $parameter
     */
    public function setParameter(int $index, $parameter)
    {
        $this->callArray[static::PARAMETER_KEY][$index] = $parameter;
    }

    /**
     * @param int $index
     *
     * @return mixed
     */
    public function getParameter(int $index)
    {
        return $this->callArray[static::PARAMETER_KEY][$index];
    }

    /**
     * @return array
     */
    public function getCallAsArray(): array
    {
        return $this->callArray;
    }
}
