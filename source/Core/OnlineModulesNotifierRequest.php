<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Online module notifier request class and used as entity.
 *
 * @internal Do not make a module extension for this class.
 *
 * @ignore   This class will not be included in documentation.
 */
class OnlineModulesNotifierRequest extends \OxidEsales\Eshop\Core\OnlineRequest
{
    /**
     * Web service protocol version.
     *
     * @var string
     */
    public $pVersion = '1.1';

    /**
     * Modules array.
     *
     * @var array
     */
    public $modules;
}
