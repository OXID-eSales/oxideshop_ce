<?php
namespace OxidEsales\Eshop\Core;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DiContainer
 */
class DiContainer implements ContainerInterface
{
    const CONTAINER_CORE_MAILCLIENT = 'core.mailclient';
    const CONTAINER_CORE_MAILER = 'core.mailer';
    const CONTAINER_CORE_EVENT_DISPATCHER = 'core.eventdispatcher';

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

    /**
     * @param ContainerBuilder $container
     */
    protected function __construct(ContainerBuilder $container)
    {
        $this->container = $container;

        //basic setup
        $container
            ->register(static::CONTAINER_CORE_MAILCLIENT, MailClient::class);

        $container
            ->register(static::CONTAINER_CORE_MAILER, \oxEmail::class)
            ->addArgument(new Reference(static::CONTAINER_CORE_MAILCLIENT));

        $container
            ->register(static::CONTAINER_CORE_EVENT_DISPATCHER, EventDispatcher::class);
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