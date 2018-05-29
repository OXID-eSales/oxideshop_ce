<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 23.05.18
 * Time: 10:51
 */

namespace OxidEsales\EshopCommunity\Internal\Application;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContainerWrapper implements ContainerInterface
{

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $symfonyContainer;

    public function __construct(\Symfony\Component\DependencyInjection\Container $symfonyContainer) {

        $this->symfonyContainer = $symfonyContainer;

    }

    public function get($id)
    {
        return $this->symfonyContainer->get($id);
    }

    public function has($id)
    {
        return $this->symfonyContainer->has($id);
    }
}