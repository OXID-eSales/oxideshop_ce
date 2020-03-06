<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\DataObject;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
use tm\oxid\SchemaExpander\DesireExpander;

/**
 * Class ParameterDatabaseSchemaExpander
 * @package OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\DataObject
 */
class ParameterDatabaseSchemaExpander implements ParameterInterface, EventSubscriberInterface
{
    /**
     * @var DesireExpander;
     */
    private $desireExpander;

    /**
     * Create new Paramerter
     *
     * @return DesireExpander
     */
    public function getParameter()
    {
        return $this->desireExpander = new DesireExpander();
    }

    /**
     * Change Database Schema
     */
    public function commitDatabaseSchema()
    {
        if ($this->desireExpander) {
            $this->desireExpander->execute();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            FinalizingModuleActivationEvent::class => ['commitDatabaseSchema', -10]
        ];
    }
}
