<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Str;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use function is_string;

class Field
{
    /**
     * escaping functionality type: expected value is escaped text.
     */
    public const T_TEXT = 1;

    /**
     * escaping functionality type: expected value is not escaped (raw) text.
     */
    public const T_RAW = 2;

    public function __construct($value = null, $type = self::T_TEXT)
    {
        $this->rawValue = $value;
        if ((int)$type === self::T_RAW) {
            $this->value = $value;
        }
    }

    public function __isset($name): bool
    {
        return $this->{$name} !== null;
    }

    /**
     * @param string $name
     * @return mixed|string|null
     */
    public function __get(string $name)
    {
        if (!($name === 'value' || $name === 'rawValue')) {
            return null;
        }
        if ($name === 'value') {
            if ($this->valueNeedsEscaping()) {
                $escapedValue = Str::getStr()->htmlspecialchars($this->rawValue);
                $this->value = $escapedValue;
                if ($escapedValue === (string)$this->rawValue) {
                    unset($this->rawValue);
                }
            } else {
                $this->value = $this->rawValue;
                unset($this->rawValue);
            }
        }
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    /**
     * @param $value
     * @param $type
     * @return void
     */
    public function setValue($value = null, $type = self::T_TEXT): void
    {
        unset($this->rawValue, $this->value);
        $this->initValue($value, $type);
    }

    /**
     * @return mixed
     */
    public function getRawValue(): mixed
    {
        return $this->rawValue ?? $this->value;
    }

    /**
     * @param $value
     * @param $type
     * @return void
     */
    protected function initValue($value = null, $type = self::T_TEXT): void
    {
        if ((int)$type === self::T_TEXT) {
            $this->rawValue = $value;
        } else {
            $this->value = $value;
        }
    }

    /**
     * @return bool
     */
    private function valueNeedsEscaping(): bool
    {
        return is_string($this->rawValue)
            && !ContainerFacade::getParameter('oxid_esales.templating.engine_autoescapes_html');
    }
}
