<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Contract;

/**
 * The implementation of this class determines the controllers which should be allowed to be called directly via
 * HTTP GET/POST Parameters, inside form actions or with oxid_include_widget.
 * Those controllers are specified e.g. inside a form action with a controller key which is mapped to its class.
 *
 */
interface ControllerMapProviderInterface
{
    /**
     * Get all controller keys and their assigned classes
     *
     * @return array
     */
    public function getControllerMap();
}
