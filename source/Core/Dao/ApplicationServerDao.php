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

namespace OxidEsales\EshopCommunity\Core\Dao;

/**
 * Application server data access manager.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ApplicationServerDao implements \OxidEsales\Eshop\Core\Dao\BaseDaoInterface
{
    /**
     * The name of config option for saving servers data information.
     */
    const CONFIG_NAME_FOR_SERVER_INFO = 'aServersData_';

    /**
     * @var \OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface Database connection
     */
    private $database;

    /**
     * @var \OxidEsales\Eshop\Core\Config Main shop configuration class.
     */
    private $config;

    /**
     * ApplicationServerDao constructor.
     *
     * @param \OxidEsales\Eshop\Core\DatabaseProvider $databaseProvider Database connection class.
     * @param \OxidEsales\Eshop\Core\Config           $config           Main shop configuration class.
     */
    public function __construct($databaseProvider, $config)
    {
        $this->database = $databaseProvider->getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $this->config = $config;
    }

    /**
     * Finds all application servers.
     *
     * @return array
     */
    public function findAll()
    {
        $appServerList = [];

        /** @var \OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface $resultList */
        $resultList = $this->selectAllData();
        if ($resultList != false && $resultList->count() > 0) {
            $result = $resultList->getFields();
            $serverId = $this->getServerIdFromConfig($result['oxvarname']);
            $information = $this->getValueFromConfig($result['oxvarvalue']);
            $appServerList[$serverId] = $this->createServer($serverId, $information);
            while ($result = $resultList->fetchRow()) {
                $serverId = $this->getServerIdFromConfig($result['oxvarname']);
                $information = $this->getValueFromConfig($result['oxvarvalue']);
                $appServerList[$serverId] = $this->createServer($serverId, $information);
            }
        }
        return $appServerList;
    }

    /**
     * Deletes the entity with the given id.
     *
     * @param string $id An id of the entity to delete.
     *
     * @return int The number of affected rows.
     */
    public function delete($id)
    {
        $query = "DELETE FROM oxconfig WHERE oxvarname = ? and oxshopid = ?";

        $parameter = array(
            self::CONFIG_NAME_FOR_SERVER_INFO.$id,
            $this->config->getBaseShopId()
        );

        return $this->database->execute($query, $parameter);
    }

    /**
     * Finds an application server by given id.
     *
     * @param string $id An id of the entity to find.
     *
     * @return \OxidEsales\Eshop\Core\DataObject\ApplicationServer
     */
    public function findById($id)
    {
        $appServerData = [];

        $serverData = $this->selectDataById($id);

        if ($serverData != false) {
            $appServerData = (array)unserialize($serverData);
        }

        return $this->createServer($id, $appServerData);
    }

    /**
     * Updates the given entity.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer
     *
     * @return int The number of affected rows.
     */
    public function update($appServer)
    {
        $query = "UPDATE oxconfig SET oxvarvalue=ENCODE( ?, ?) WHERE oxvarname = ? and oxshopid = ?";

        $parameter = array(
            $this->convertAppServerToConfigOption($appServer),
            $this->config->getConfigParam('sConfigKey'),
            self::CONFIG_NAME_FOR_SERVER_INFO.$appServer->getId(),
            $this->config->getBaseShopId()
        );

        return $this->database->execute($query, $parameter);
    }

    /**
     * Insert new application server entity.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer
     *
     * @return int The number of affected rows.
     */
    public function insert($appServer)
    {
        $query = "insert into oxconfig (oxid, oxshopid, oxmodule, oxvarname, oxvartype, oxvarvalue)
               values(?, ?, '', ?, ?, ENCODE( ?, ?) )";

        $parameter = array(
            \OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUID(),
            $this->config->getBaseShopId(),
            self::CONFIG_NAME_FOR_SERVER_INFO.$appServer->getId(),
            'arr',
            $this->convertAppServerToConfigOption($appServer),
            $this->config->getConfigParam('sConfigKey')
        );

        return $this->database->execute($query, $parameter);
    }

    /**
     * Returns all application server entities from database.
     *
     * @param string $id An id of the entity to find.
     *
     * @return string
     */
    private function selectDataById($id)
    {
        $query = "SELECT " . $this->config->getDecodeValueQuery() .
            " as oxvarvalue FROM oxconfig WHERE oxvarname = ? AND oxshopid = ?";

        $parameter = array(
            self::CONFIG_NAME_FOR_SERVER_INFO.$id,
            $this->config->getBaseShopId()
        );

        return $this->database->getOne($query, $parameter);
    }

    /**
     * Returns all application server entities from database.
     *
     * @return \OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface
     */
    private function selectAllData()
    {
        $query = "SELECT oxvarname, " . $this->config->getDecodeValueQuery() .
            " as oxvarvalue FROM oxconfig WHERE oxvarname like ? AND oxshopid = ?";

        $parameter = array(
            self::CONFIG_NAME_FOR_SERVER_INFO."%",
            $this->config->getBaseShopId()
        );

        return $this->database->select($query, $parameter);
    }

    /**
     * Parses config option name to get the server id.
     *
     * @param string $varName The name of the config option.
     *
     * @return string The id of server.
     */
    private function getServerIdFromConfig($varName)
    {
        $constNameLength = strlen(self::CONFIG_NAME_FOR_SERVER_INFO);
        $id = substr($varName, $constNameLength);
        return $id;
    }

    /**
     * Unserializes config option value.
     *
     * @param string $varValue The serialized value of the config option.
     *
     * @return string The information of server.
     */
    private function getValueFromConfig($varValue)
    {
        return (array) unserialize($varValue);
    }

    /**
     * Creates ApplicationServer from given server id and data.
     *
     * @param string $serverId The id of server.
     * @param array  $data     The array of server data.
     *
     * @return \OxidEsales\Eshop\Core\DataObject\ApplicationServer
     */
    protected function createServer($serverId, $data = [])
    {
        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer */
        $appServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);

        $appServer->setId($serverId);
        $appServer->setTimestamp($this->getServerParameter($data, 'timestamp'));
        $appServer->setIp($this->getServerParameter($data, 'ip'));
        $appServer->setLastFrontendUsage($this->getServerParameter($data, 'lastFrontendUsage'));
        $appServer->setLastAdminUsage($this->getServerParameter($data, 'lastAdminUsage'));
        $appServer->setIsValid($this->getServerParameter($data, 'isValid'));

        return $appServer;
    }

    /**
     * Gets server parameter.
     *
     * @param array  $data The array of server data.
     * @param string $name The name of searched parameter.
     *
     * @return mixed
     */
    private function getServerParameter($data, $name)
    {
        return array_key_exists($name, $data) ? $data[$name] : null;
    }

    /**
     * Convert ApplicationServer object into simple array for saving into database oxconfig table.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer An application server object.
     *
     * @return array
     */
    private function convertAppServerToConfigOption($appServer)
    {
        $serverData = array(
            'id'                => $appServer->getId(),
            'timestamp'         => $appServer->getTimestamp(),
            'ip'                => $appServer->getIp(),
            'lastFrontendUsage' => $appServer->getLastFrontendUsage(),
            'lastAdminUsage'    => $appServer->getLastAdminUsage(),
            'isValid'           => $appServer->isValid()
        );

        return serialize($serverData);
    }

}
