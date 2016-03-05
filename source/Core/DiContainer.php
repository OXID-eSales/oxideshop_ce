<?php
namespace OxidEsales\Eshop\Core;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DiContainer
 */
class DiContainer implements ContainerInterface
{
    /**
     * @var DiContainer
     */
    private static $diContainer;

    /**
     * @return DiContainer
     */
    public function getInstance()
    {
        if (null === static::$diContainer) {
            static::$diContainer = new static(new ContainerBuilder());
        }
        return static::$diContainer;
    }

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @param ContainerBuilder $container
     */
    protected function __construct(ContainerBuilder $container)
    {
        $this->container = $container;

        //basic setup

        $container->register('mailer', 'Mailer');

    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $this->container->get($id);
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
        $this->container->has($id);
    }

}