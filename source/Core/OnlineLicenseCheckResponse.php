<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Online license check response class.
 *
 * @internal Do not make a module extension for this class.
 *
 * @ignore   This class will not be included in documentation.
 */
class OnlineLicenseCheckResponse
{
    /**
     * Serial keys.
     *
     * @var string
     */
    public $code;

    /**
     * Build revision number.
     *
     * @var string
     */
    public $message;
}
