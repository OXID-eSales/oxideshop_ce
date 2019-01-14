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
     * ProjectYamlDao constructor.
     *
     * @param BasicContextInterface    $context
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(BasicContextInterface $context, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($context);
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
