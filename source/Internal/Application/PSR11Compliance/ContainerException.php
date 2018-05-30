<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class ContainerException
 *
 * Compliance class to supply exceptions in a PSR11 compatible manner
 */
class ContainerException extends \Exception implements ContainerExceptionInterface
{

}
