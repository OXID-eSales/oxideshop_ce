<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance;

use Psr\Container\ContainerExceptionInterface;

/**
 * Compliance class to supply exceptions in a PSR11 compatible manner.
 *
 * @internal
 */
class ContainerException extends \Exception implements ContainerExceptionInterface
{

}
