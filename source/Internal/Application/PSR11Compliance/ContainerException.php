<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 28.05.18
 * Time: 14:46
 */

namespace OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance;

use Psr\Container\ContainerExceptionInterface;

/**
 * Class ContainerException
 *
 * Compliance class to supply exceptions in a PSR11 compatible manner
 *
 * @package OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance
 */
class ContainerException extends \Exception implements ContainerExceptionInterface
{

}
