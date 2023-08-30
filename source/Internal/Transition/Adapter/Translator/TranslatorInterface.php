<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\Translator;

interface TranslatorInterface
{
    /**
     * @param string $string
     *
     * @return string
     */
    public function translate(string $string): string;
}
