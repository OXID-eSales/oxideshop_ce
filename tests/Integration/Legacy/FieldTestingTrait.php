<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use OxidEsales\Eshop\Core\Str;

/**
 * Mocks differences in behaviour of Field::value when Twig/Smarty is used as a templating engine.
 * Used for tests that need to be run with both engines.
 */
trait FieldTestingTrait
{
    public function encode(string $string): string
    {
        return !$this->isSmarty() ? $string : Str::getStr()->htmlspecialchars($string);
    }

    public function insertLineBreaks(string $string): string
    {
        return !$this->isSmarty() ? $string : \str_replace("\r", '', \nl2br($string));
    }

    private function isSmarty(): bool
    {
        return \in_array(\ACTIVE_THEME, ['flow', 'wave', 'azure']);
    }
}
