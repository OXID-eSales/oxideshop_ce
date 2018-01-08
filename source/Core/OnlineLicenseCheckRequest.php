<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Online license check request class used as entity.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 */
class OnlineLicenseCheckRequest extends \OxidEsales\Eshop\Core\OnlineRequest
{
    /**
     * Web service protocol version.
     *
     * @var string
     */
    public $pVersion = '1.1';

    /**
     * Serial keys.
     *
     * @var string
     */
    public $keys;

    /**
     * Build revision number.
     *
     * @var string
     */
    public $revision;

    /**
     * Product related specific information
     * like amount of sub shops and amount of admin users.
     *
     * @var object
     */
    public $productSpecificInformation;
}
