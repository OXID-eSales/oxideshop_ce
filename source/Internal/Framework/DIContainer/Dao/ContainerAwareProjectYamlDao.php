<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Event\ProjectYamlChangedEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

class ContainerAwareProjectYamlDao extends ProjectYamlDao
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * ContainerAwareProjectYamlDao constructor.
     */
    public function __construct(
        BasicContextInterface $context,
        EventDispatcherInterface $eventDispatcher,
        Filesystem $filesystem
    ) {
        parent::__construct($context, $filesystem);
        $this->eventDispatcher = $eventDispatcher;
    }

    public function saveProjectConfigFile(DIConfigWrapper $config): void
    {
        parent::saveProjectConfigFile($config);
        $this->eventDispatcher->dispatch(
            new ProjectYamlChangedEvent(),
            ProjectYamlChangedEvent::NAME
        );
    }
}
