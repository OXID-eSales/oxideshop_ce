<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core\Service;

/**
 * Manages application servers information.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ApplicationServerService implements \OxidEsales\Eshop\Core\Contract\ApplicationServerServiceInterface
{
    /**
     * @var \OxidEsales\Eshop\Core\Dao\ApplicationServerDao The Dao object for application server.
     */
    private $appServerDao;

    private $activeList;

    /**
     * ApplicationServerService constructor.
     *
     * @param \OxidEsales\Eshop\Core\Dao\ApplicationServerDao $appServerDao The Dao object of application server.
     */
    public function __construct($appServerDao)
    {
        $this->appServerDao = $appServerDao;
    }

    /**
     * Returns all servers information array from configuration.
     *
     * @return array
     */
    public function loadAppServerList()
    {
        return $this->appServerDao->findAll();
    }

    /**
     * Load the application server for given id.
     *
     * @param string $id The id of the application server to load.
     *
     * @return \OxidEsales\Eshop\Core\ApplicationServer
     */
    public function loadAppServer($id)
    {
        return $this->appServerDao->findById($id);
    }

    /**
     * Removes server node information.
     *
     * @param string $serverId
     *
     * @return bool
     */
    public function deleteAppServerById($serverId)
    {
        return $this->appServerDao->delete($serverId);
    }

    /**
     * Saves application server data.
     *
     * @param \OxidEsales\Eshop\Core\ApplicationServer $appServer
     *
     * @return int
     */
    public function saveAppServer($appServer)
    {
        if ($this->appServerDao->findById($appServer->getId()) !== false) {
            $effectedRows = $this->appServerDao->update($appServer);
        } else {
            $effectedRows = $this->appServerDao->insert($appServer);
        }
        return $effectedRows;
    }

    /**
     * Returns all servers information array from configuration.
     *
     * @return array
     */
    public function loadActiveAppServerList()
    {
        if (isset($this->activeList)) {
            return $this->activeList;
        }

        $activeServerList = array();

        $allFoundServers = $this->loadAppServerList();
        /** @var \OxidEsales\Eshop\Core\ApplicationServer $server */
        foreach ($allFoundServers as $server) {
            if ($server->isValid()) {
                $activeServerList[] = $server;
            }
        }
        return $activeServerList;
    }

    public function setActiveAppServerList($actList)
    {
        $this->activeList = $actList;
    }
}
