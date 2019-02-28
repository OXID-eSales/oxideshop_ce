<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Dao;

use OxidEsales\EshopCommunity\Internal\Application\Events\ProjectYamlChangedEvent;
use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class ContainerAwareProjectYamlDao extends ProjectYamlDao
{
    /**
     * @var EventDispatcherInterface $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * ContainerAwareProjectYamlDao constructor.
     * @param BasicContextInterface    $context
     * @param EventDispatcherInterface $eventDispatcher
     * @param Filesystem               $filesystem
     */
    public function __construct(BasicContextInterface $context, EventDispatcherInterface $eventDispatcher, Filesystem $filesystem)
    {
        parent::__construct($context, $filesystem);
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param DIConfigWrapper $config
     */
    public function saveProjectConfigFile(DIConfigWrapper $config)
    {
        parent::saveProjectConfigFile($config);
        $this->eventDispatcher->dispatch(ProjectYamlChangedEvent::NAME, new ProjectYamlChangedEvent());
    }
}
