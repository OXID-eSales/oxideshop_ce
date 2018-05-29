<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 23.05.18
 * Time: 10:51
 */

namespace OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Class ContainerWrapper
 *
 * Wrapper class around the Symfony 3.1 DI container to implement
 * the ContainerInterface (PSR11). This did not yet exist when
 * Symfony 3.1 was released. This wrapper may be removed when
 * we are able to switch to a newer version of the Symfony container.
 *
 * @package OxidEsales\EshopCommunity\Internal\Application
 */
class ContainerWrapper implements ContainerInterface
{

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $symfonyContainer;

    /**
     * ContainerWrapper constructor.
     *
     * Just wraps the symfony 3.1 container.
     *
     * @param \Symfony\Component\DependencyInjection\Container $symfonyContainer
     */
    public function __construct(\Symfony\Component\DependencyInjection\Container $symfonyContainer)
    {

        $this->symfonyContainer = $symfonyContainer;
    }

    /**
     * @param string $id
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return mixed Entry
     */
    public function get($id)
    {
        try {
            $service = $this->symfonyContainer->get($id);
        } catch (ServiceNotFoundException $e) {
            throw new NotFoundException($e->getMessage());
        } catch (\Exception $e) {
            throw new ContainerException();
        }
        return $service;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return $this->symfonyContainer->has($id);
    }
}
