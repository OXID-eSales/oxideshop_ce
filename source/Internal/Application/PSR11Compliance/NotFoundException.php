<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Class NotFoundException
 *
 * Needed to make the container wrapper PSR11 complient (the original symfony
 * exception is not implementing the right interface).
 */
class NotFoundException extends \Exception implements NotFoundExceptionInterface
{

}
