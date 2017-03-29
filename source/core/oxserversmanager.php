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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Manages application servers information.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class oxServersManager
{

    /**
     * Time in seconds, server node information life time.
     */
    const NODE_AVAILABILITY_CHECK_PERIOD = 86400;

    /**
     * Time in seconds, server node information life time.
     */
    const INACTIVE_NODE_STORAGE_PERIOD = 259200;

    /**
     * The name of config option for saving servers data information.
     */
    const CONFIG_NAME_FOR_SERVER_INFO = 'aServersData_';

    /**
     * Returns server based on server id.
     *
     * @param string $sServerId
     *
     * @return oxApplicationServer
     */
    public function getServer($sServerId)
    {
        $aServerData = $this->getServerDataFromDb($sServerId);

        return $this->createServer($sServerId, $aServerData);
    }

    /**
     * Saves given server information to config.
     *
     * @param oxApplicationServer $oServer
     */
    public function saveServer($oServer)
    {
        $aServerData = array(
            'id'                => $oServer->getId(),
            'timestamp'         => $oServer->getTimestamp(),
            'ip'                => $oServer->getIp(),
            'lastFrontendUsage' => $oServer->getLastFrontendUsage(),
            'lastAdminUsage'    => $oServer->getLastAdminUsage(),
            'isValid'           => $oServer->isValid()
        );
        $this->saveToDb($oServer->getId(), $aServerData);
    }

    /**
     * Creates oxApplicationServer from given server id and data.
     *
     * @param string $sServerId
     * @param array  $aData
     *
     * @return oxApplicationServer
     */
    protected function createServer($sServerId, $aData = array())
    {
        /** @var oxApplicationServer $oAppServer */
        $oAppServer = oxNew('oxApplicationServer');

        $oAppServer->setId($sServerId);
        $oAppServer->setTimestamp($this->getServerParameter($aData, 'timestamp'));
        $oAppServer->setIp($this->getServerParameter($aData, 'serverIp'));
        $oAppServer->setLastFrontendUsage($this->getServerParameter($aData, 'lastFrontendUsage'));
        $oAppServer->setLastAdminUsage($this->getServerParameter($aData, 'lastAdminUsage'));
        $oAppServer->setIsValid($this->getServerParameter($aData, 'isValid'));

        return $oAppServer;
    }

    /**
     * Gets server parameter.
     *
     * @param array  $aData Data
     * @param string $sName Name
     *
     * @return mixed
     */
    protected function getServerParameter($aData, $sName)
    {
        return array_key_exists($sName, $aData) ? $aData[$sName] : null;
    }

    /**
     * Return active server nodes.
     *
     * @return array
     */
    public function getServers()
    {
        $aServersData = $this->getServersData();
        $aServersData = $this->markInActiveServers($aServersData);
        $aServersData = $this->deleteInActiveServers($aServersData);

        $aValidServers = array();

        foreach ($aServersData as $aServer) {
            if ($aServer['isValid']) {
                unset($aServer['isValid']);
                unset($aServer['timestamp']);
                $aValidServers[] = $aServer;
            }
        }

        return $aValidServers;
    }

    /**
     * Removes server node information.
     *
     * @param string $sServerId Server id
     */
    public function deleteServer($sServerId)
    {
        $oConfig = oxRegistry::getConfig();
        $sShopId = $oConfig->getBaseShopId();
        $sVarName = self::CONFIG_NAME_FOR_SERVER_INFO.$sServerId;
        $oDb = oxDb::getDb();
        $sQ = "DELETE FROM oxconfig WHERE oxvarname = ? and oxshopid = ?";
        $oDb->execute($sQ, array($sVarName, $sShopId));
    }

    /**
     * Mark servers as inactive if they are not used anymore.
     *
     * @param array $aServersData Information of all servers data
     *
     * @return array $aServersData Information of all servers data
     */
    public function markInActiveServers($aServersData = null)
    {
        foreach ($aServersData as $sServerId => $aServerData) {
            if ($aServerData['timestamp'] < oxRegistry::get("oxUtilsDate")->getTime() - self::NODE_AVAILABILITY_CHECK_PERIOD) {
                $oServer = $this->getServer($sServerId);
                $oServer->setIsValid(false);
                $this->saveServer($oServer);
                $aServersData[$sServerId]['isValid'] = false;
            }
        }
        return $aServersData;
    }

    /**
     * Removes information about old and not used servers.
     *
     * @param array $aServersData Information of all servers data
     *
     * @return array $aServersData Information of all servers data
     */
    public function deleteInActiveServers($aServersData)
    {
        foreach ($aServersData as $sServerId => $aServerData) {
            if ($aServerData['timestamp'] < oxRegistry::get("oxUtilsDate")->getTime() - self::INACTIVE_NODE_STORAGE_PERIOD) {
                $this->deleteServer($sServerId);
                unset($aServersData[$sServerId]);
            }
        }
        return $aServersData;
    }

    /**
     * Returns all servers information array from configuration.
     *
     * @return array
     */
    public function getServersData()
    {
        $aServersData = array();
        $result = $this->getAllServersDataConfigsFromDb();
        if ($result != false && $result->recordCount() > 0) {
            while (!$result->EOF) {
                $sServerId = $this->parseServerIdFromConfig($result->fields['oxvarname']);
                $aServersData[$sServerId] = (array)unserialize($result->fields['oxvarvalue']);
                $result->moveNext();
            }
        }
        return $aServersData;
    }

    /**
     * Parses config option name to get the server id.
     *
     * @param string $sVarName The name of the config option.
     *
     * @return string The id of server.
     */
    private function parseServerIdFromConfig($sVarName)
    {
        $iConstNameLength = strlen(self::CONFIG_NAME_FOR_SERVER_INFO);
        $sId = substr($sVarName, $iConstNameLength);
        return $sId;
    }

    /**
     * Returns all servers information array from database.
     *
     * @return object ResultSetInterface
     */
    protected function getAllServersDataConfigsFromDb()
    {
        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $oConfig = oxRegistry::getConfig();

        $sConfigsQuery = "SELECT oxvarname, " . $oConfig->getDecodeValueQuery() .
            " as oxvarvalue FROM oxconfig WHERE oxvarname like ? AND oxshopid = ?";

        return $oDb->select($sConfigsQuery, array(self::CONFIG_NAME_FOR_SERVER_INFO."%", $oConfig->getBaseShopId()));
    }

    /**
     * Returns server information from configuration.
     *
     * @param string $sServerId
     *
     * @return array
     */
    protected function getServerDataFromDb($sServerId)
    {
        $aServerData = array();
        $sData = $this->getConfigValueFromDB(self::CONFIG_NAME_FOR_SERVER_INFO.$sServerId);

        if ($sData != false ) {
            $aServerData = (array)unserialize($sData);
        }
        return $aServerData;
    }

    /**
     * Returns configuration value from database.
     *
     * @param string $sVarName Variable name
     *
     * @return string
     */
    private function getConfigValueFromDB($sVarName)
    {
        $oConfig = oxRegistry::getConfig();
        $oDb = oxDb::getDb();

        $sConfigsQuery = "SELECT " . $oConfig->getDecodeValueQuery() .
            " as oxvarvalue FROM oxconfig WHERE oxvarname = ? AND oxshopid = ?";

        $sResult = $oDb->getOne($sConfigsQuery, array($sVarName, $oConfig->getBaseShopId()), false);

        return $sResult;
    }

    /**
     * Saves servers data to database.
     *
     * @param string $sServerId Server id
     * @param array $aServerData Server data
     */
    protected function saveToDb($sServerId, $aServerData)
    {
        $oConfig = oxRegistry::getConfig();
        $sVarName = self::CONFIG_NAME_FOR_SERVER_INFO.$sServerId;
        $sConfigKey = $oConfig->getConfigParam('sConfigKey');
        $sValue = serialize($aServerData);
        $sVarType = 'arr';
        $sShopId = $oConfig->getBaseShopId();
        $oDb = oxDb::getDb();
        if ($this->getConfigValueFromDB($sVarName) !== false) {
            $sQ = "UPDATE oxconfig SET oxvarvalue=ENCODE( ?, ?) WHERE oxvarname = ? and oxshopid = ?";
            $oDb->execute($sQ, array($sValue, $sConfigKey, $sVarName, $sShopId));
        } else {
            $sOxid = oxUtilsObject::getInstance()->generateUID();

            $sQ = "insert into oxconfig (oxid, oxshopid, oxmodule, oxvarname, oxvartype, oxvarvalue)
               values(?, ?, '', ?, ?, ENCODE( ?, ?) )";
            $oDb->execute($sQ, array($sOxid, $sShopId, $sVarName, $sVarType, $sValue, $sConfigKey));
        }
    }
}
