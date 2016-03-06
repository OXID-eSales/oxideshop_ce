<?php
namespace OxidEsales\Eshop\Core;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;

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

    private $hacks = [];

    /**
     * @param ContainerBuilder $container
     */
    protected function __construct(ContainerBuilder $container)
    {
        $this->container = $container;

        //basic setup
        $container
            ->register(static::CONTAINER_CORE_MAILCLIENT, MailClient::class);

        // hack!
        $container
            ->register('symfony.eventdispatcher', SymfonyEventDispatcher::class);

        $container
            ->register(static::CONTAINER_CORE_MAILER, \oxEmail::class)
            ->addArgument(new Reference(static::CONTAINER_CORE_MAILCLIENT));

        $container
            ->register(static::CONTAINER_CORE_EVENT_DISPATCHER, EventDispatcher::class)
            ->addArgument(new Reference('symfony.eventdispatcher'));
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        // hacks!
        if (isset($this->hacks[$id])) {
            return $this->hacks[$id];
        }

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

    // Dirty...
    public function set($id, $object)
    {
        //basic setup
        $this->hacks[$id] = $object;
    }

}