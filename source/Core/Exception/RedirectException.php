<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

/**
 * Class RedirectException
 * thrown if a HTTP redirect can not be executed.
 * This could happen for example happen if the systems detects a redirect cycle
 */
class RedirectException extends \RuntimeException
{

}
