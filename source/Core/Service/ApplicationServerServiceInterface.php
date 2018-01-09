<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Service;

/**
 * Manages application servers information.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
interface ApplicationServerServiceInterface
{
    /**
     * Returns all servers information array from configuration.
     *
     * @return \OxidEsales\Eshop\Core\DataObject\ApplicationServer[]
     */
    public function loadAppServerList();

    /**
     * Load the application server for given id.
     *
     * @param string $id The id of the application server to load.
     *
     * @return \OxidEsales\Eshop\Core\DataObject\ApplicationServer
     */
    public function loadAppServer($id);

    /**
     * Removes server node information.
     *
     * @param string $serverId
     */
    public function deleteAppServerById($serverId);

    /**
     * Saves application server data.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer
     */
    public function saveAppServer($appServer);

    /**
     * Returns an array of all only active application servers.
     *
     * @return array
     */
    public function loadActiveAppServerList();

    /**
     * Renews application server information when it is call in admin area and
     * if it is outdated or if it does not exist.
     */
    public function updateAppServerInformationInAdmin();

    /**
     * Renews application server information when it is call in frontend and
     * if it is outdated or if it does not exist.
     */
    public function updateAppServerInformationInFrontend();
}
