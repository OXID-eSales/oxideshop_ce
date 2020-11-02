<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Dao;

/**
 * Application server data access manager.
 *
 * @internal do not make a module extension for this class
 *
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ApplicationServerDao implements \OxidEsales\Eshop\Core\Dao\ApplicationServerDaoInterface
{
    /**
     * The name of config option for saving servers data information.
     */
    public const CONFIG_NAME_FOR_SERVER_INFO = 'aServersData_';

    /**
     * @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer[]
     */
    private $appServer = [];

    /**
     * @var \OxidEsales\Eshop\Core\Config main shop configuration class
     */
    private $config;

    /**
     * @var \OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface
     */
    protected $database;

    /**
     * ApplicationServerDao constructor.
     *
     * @param \OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface $database database connection class
     * @param \OxidEsales\Eshop\Core\Config                             $config   main shop configuration class
     */
    public function __construct($database, $config)
    {
        $this->database = $database;
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
        if (false !== $resultList && $resultList->count() > 0) {
            $result = $resultList->getFields();
            $serverId = $this->getServerIdFromConfig($result['oxvarname']);
            $information = $this->getValueFromConfig($result['oxvarvalue']);
            $appServerList[$serverId] = $this->createServer($information);
            while ($result = $resultList->fetchRow()) {
                $serverId = $this->getServerIdFromConfig($result['oxvarname']);
                $information = $this->getValueFromConfig($result['oxvarvalue']);
                $appServerList[$serverId] = $this->createServer($information);
            }
        }

        return $appServerList;
    }

    /**
     * Deletes the entity with the given id.
     *
     * @param string $id an id of the entity to delete
     */
    public function delete($id): void
    {
        unset($this->appServer[$id]);

        $query = 'DELETE FROM oxconfig WHERE oxvarname = :oxvarname and oxshopid = :oxshopid';
        $this->database->execute($query, [
            ':oxvarname' => self::CONFIG_NAME_FOR_SERVER_INFO . $id,
            ':oxshopid' => $this->config->getBaseShopId(),
        ]);
    }

    /**
     * Finds an application server by given id, null if none is found.
     *
     * @param string $id an id of the entity to find
     *
     * @return \OxidEsales\Eshop\Core\DataObject\ApplicationServer|null
     */
    public function findAppServer($id)
    {
        if (!isset($this->appServer[$id])) {
            $serverData = $this->selectDataById($id);

            if (false !== $serverData) {
                $appServerProperties = (array)unserialize($serverData);
            } else {
                return null;
            }

            $this->appServer[$id] = $this->createServer($appServerProperties);
        }

        return $this->appServer[$id];
    }

    /**
     * Updates or insert the given entity.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer
     */
    public function save($appServer): void
    {
        $id = $appServer->getId();
        if ($this->findAppServer($id)) {
            $this->update($appServer);
            unset($this->appServer[$id]);
        } else {
            $this->insert($appServer);
        }
    }

    /**
     * Start a database transaction.
     */
    public function startTransaction(): void
    {
        $this->database->startTransaction();
    }

    /**
     * Commit a database transaction.
     */
    public function commitTransaction(): void
    {
        $this->database->commitTransaction();
    }

    /**
     * RollBack a database transaction.
     */
    public function rollbackTransaction(): void
    {
        $this->database->rollbackTransaction();
    }

    /**
     * Updates the given entity.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer
     */
    protected function update($appServer): void
    {
        $query = 'UPDATE oxconfig SET oxvarvalue = :value
                  WHERE oxvarname = :oxvarname and oxshopid = :oxshopid';

        $parameter = [
            ':value' => $this->convertAppServerToConfigOption($appServer),
            ':oxvarname' => self::CONFIG_NAME_FOR_SERVER_INFO . $appServer->getId(),
            ':oxshopid' => $this->config->getBaseShopId(),
        ];

        $this->database->execute($query, $parameter);
    }

    /**
     * Insert new application server entity.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer
     */
    protected function insert($appServer): void
    {
        $query = "insert into oxconfig (oxid, oxshopid, oxmodule, oxvarname, oxvartype, oxvarvalue)
                  values (:oxid, :oxshopid, '', :oxvarname, :oxvartype, :value)";

        $parameter = [
            ':oxid' => \OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUID(),
            ':oxshopid' => $this->config->getBaseShopId(),
            ':oxvarname' => self::CONFIG_NAME_FOR_SERVER_INFO . $appServer->getId(),
            ':oxvartype' => 'arr',
            ':value' => $this->convertAppServerToConfigOption($appServer),
        ];

        $this->database->execute($query, $parameter);
    }

    /**
     * Returns all application server entities from database.
     *
     * @param string $id an id of the entity to find
     *
     * @return string
     */
    private function selectDataById($id)
    {
        $query = 'SELECT oxvarvalue FROM oxconfig 
            WHERE oxvarname = :oxvarname 
              AND oxshopid = :oxshopid FOR UPDATE';

        $parameter = [
            ':oxvarname' => self::CONFIG_NAME_FOR_SERVER_INFO . $id,
            ':oxshopid' => $this->config->getBaseShopId(),
        ];

        $this->database->setFetchMode(\OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface::FETCH_MODE_ASSOC);

        return $this->database->getOne($query, $parameter);
    }

    /**
     * Returns all application server entities from database.
     *
     * @return \OxidEsales\Eshop\Core\Database\Adapter\ResultSetInterface
     */
    private function selectAllData()
    {
        $query = 'SELECT oxvarname, oxvarvalue
                    FROM oxconfig
                    WHERE oxvarname like :oxvarname AND oxshopid = :oxshopid';

        $parameter = [
            ':oxvarname' => self::CONFIG_NAME_FOR_SERVER_INFO . '%',
            ':oxshopid' => $this->config->getBaseShopId(),
        ];

        $this->database->setFetchMode(\OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface::FETCH_MODE_ASSOC);

        return $this->database->select($query, $parameter);
    }

    /**
     * Parses config option name to get the server id.
     *
     * @param string $varName the name of the config option
     *
     * @return string the id of server
     */
    private function getServerIdFromConfig($varName)
    {
        $constNameLength = \strlen(self::CONFIG_NAME_FOR_SERVER_INFO);

        return substr($varName, $constNameLength);
    }

    /**
     * Unserializes config option value.
     *
     * @param string $varValue the serialized value of the config option
     *
     * @return array the information of server
     */
    private function getValueFromConfig($varValue)
    {
        return (array)unserialize($varValue);
    }

    /**
     * Creates ApplicationServer from given server id and data.
     *
     * @param array $data the array of server data
     *
     * @return \OxidEsales\Eshop\Core\DataObject\ApplicationServer
     */
    protected function createServer($data)
    {
        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer */
        $appServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);

        $appServer->setId($this->getServerParameter($data, 'id'));
        $appServer->setTimestamp($this->getServerParameter($data, 'timestamp'));
        $appServer->setIp($this->getServerParameter($data, 'ip'));
        $appServer->setLastFrontendUsage($this->getServerParameter($data, 'lastFrontendUsage'));
        $appServer->setLastAdminUsage($this->getServerParameter($data, 'lastAdminUsage'));

        return $appServer;
    }

    /**
     * Gets server parameter.
     *
     * @param array  $data the array of server data
     * @param string $name the name of searched parameter
     *
     * @return mixed
     */
    private function getServerParameter($data, $name)
    {
        return \array_key_exists($name, $data) ? $data[$name] : null;
    }

    /**
     * Convert ApplicationServer object into simple array for saving into database oxconfig table.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer an application server object
     *
     * @return array
     */
    private function convertAppServerToConfigOption($appServer)
    {
        $serverData = [
            'id' => $appServer->getId(),
            'timestamp' => $appServer->getTimestamp(),
            'ip' => $appServer->getIp(),
            'lastFrontendUsage' => $appServer->getLastFrontendUsage(),
            'lastAdminUsage' => $appServer->getLastAdminUsage(),
        ];

        return serialize($serverData);
    }
}
