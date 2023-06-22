<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container;

trait ContainerTrait
{
    /**
     * @param string $service
     * @return object|mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function get(string $service): mixed
    {
        return $this->getContainer()->get($service);
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    protected function getContainer(): \Psr\Container\ContainerInterface
    {
        return ContainerFactory::getInstance()->getContainer();
    }
}
