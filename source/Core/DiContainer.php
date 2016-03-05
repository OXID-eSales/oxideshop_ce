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
    public static function getInstance()
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

    private $services;

    /**
     * @param ContainerBuilder $container
     */
    protected function __construct(ContainerBuilder $container)
    {
        $this->container = $container;

        //basic setup
        $container->register('core.mailclient', \OxidEsales\Eshop\Core\MailClient::class);
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->container->get($id);
        }
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

}