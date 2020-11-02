<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Online license check response class.
 *
 * @internal do not make a module extension for this class
 *
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
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
