<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Template;


use tm\oxid\SchemaExpander\DesireExpander;

/**
 * Interface ModuleEventsInterface
 *
 * @package OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Template
 */
interface ModuleEventsInterface
{
    /**
     * Called up when the module becomes active.
     * As soon as it is registered in the metadata.php under event.
     *
     * @see https://docs.oxid-esales.com/developer/en/6.1/modules/skeleton/metadataphp/version12.html#events
     * @param DesireExpander $desireExpander Database schema adjustments when activating the module
     * @return void
     */
    public static function onActivate(DesireExpander $desireExpander);

    /**
     * Called up when the module will be deactive.
     *
     * @return void
     */
    public static function onDeactivate();
}
