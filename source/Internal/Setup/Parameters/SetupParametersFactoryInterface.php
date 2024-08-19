<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Parameters;

use OxidEsales\EshopCommunity\Internal\Setup\Language\DefaultLanguage;

interface SetupParametersFactoryInterface
{
    public function create(DefaultLanguage $language): SetupParameters;
}
