<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 28.05.18
 * Time: 13:36
 */

namespace OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance;

use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Class NotFoundException
 *
 * Needed to make the container wrapper PSR11 complient (the original symfony
 * exception is not implementing the right interface)
 *
 * @package OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance
 */
class NotFoundException extends \Exception implements NotFoundExceptionInterface
{

}
