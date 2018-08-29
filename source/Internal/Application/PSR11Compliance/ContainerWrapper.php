<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\PSR11Compliance;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Container;

/**
 * Wrapper class around the Symfony 3.1 DI container to implement
 * the ContainerInterface (PSR11). This did not yet exist when
 * Symfony 3.1 was released. This wrapper may be removed when
 * we are able to switch to a newer version of the Symfony container.
 *
 * @internal
 */
class ContainerWrapper implements ContainerInterface
{
    /**
     * @var Container
     */
    private $symfonyContainer;

    /**
     * ContainerWrapper constructor.
     *
     * Just wraps the symfony 3.1 container.
     *
     * @param Container $symfonyContainer
     */
    public function __construct(Container $symfonyContainer)
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
            throw new ContainerException($e->getMessage());
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
