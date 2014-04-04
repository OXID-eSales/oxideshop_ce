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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

define('MAINTENANCE_SINGLEROWS', 1);
define('MAINTENANCE_WHOLETABLES', 2);

define('MAINTENANCE_MODE_ONLYRESET', 1);
define('MAINTENANCE_MODE_ONLYOUTPUT', 2);
define('MAINTENANCE_MODE_RESETANDOUTPUT', 3);

/**
 * Database maintenance class responsible complete for backuping and restoration of test database.
 */
class DbRestore
{
    /**
     * Restore database in single rows (updating, deleting or inserting single records)
     */
    const MAINTENANCE_SINGLEROWS = 1;

    /**
     * Restore database by dropping the whole table and inserting all records back
     */
    const MAINTENANCE_WHOLETABLES = 2;

    /**
     * Only reset the database, but do not create log file
     */
    const MAINTENANCE_MODE_ONLYRESET = 1;

    /**
     * Only create log files with changes, but do not restore database
     */
    const MAINTENANCE_MODE_ONLYOUTPUT = 2;

    /**
     * Create log file with changes and restore database
     */
    const MAINTENANCE_MODE_RESETANDOUTPUT = 3;

    /**
     * Temp directory, where to store database dump
     * @var string
     */
    private $_sTmpDir = '/tmp/';

    /**
     * Dump file path
     * @var string
     */
    private $_sTmpFilePath = null;

    /*
     * Dump of the original db
     *
     * @var array
     */
    private $_aDBDump = null;

    /**
     * Changes made for database with tests
     *
     * @var array
     */
    private $_aChanges = array();

    /**
     * All queries made when restoring the database
     *
     * @var array
     */
    private $_aQueries = array();

    /**
     * DB restoration mode
     *
     * @var int
     */
    private $_iResetMode = self::MAINTENANCE_SINGLEROWS;

    /**
     * Output mode
     *
     * @var int
     */
    private $_iOutputMode = self::MAINTENANCE_MODE_ONLYRESET;

    /**
     * Sets temp directory to xoCCTempDir if constant exists
     */
    public function __construct()
    {
        if (defined('oxCCTempDir')) {
            $this->_sTmpDir = oxCCTempDir;
        }
    }

    /**
     * @param $iMode
     */
    public function setResetMode($iMode)
    {
        $this->_iResetMode = $iMode;
    }

    /**
     * @return int
     */
    public function getResetMode()
    {
        return $this->_iResetMode;
    }

    /**
     * @param $iMode
     */
    public function setOutputMode($iMode)
    {
        $this->_iOutputMode = $iMode;
    }

    /**
     * @return int
     */
    public function getOutputMode()
    {
        return $this->_iOutputMode;
    }

    /**
     * Returns dump file path
     *
     * @return string
     */
    public function getDumpFilePath()
    {
        if (is_null($this->_sTmpFilePath)) {
            $sDbName = oxConfig::getInstance()->getConfigParam('dbName');
            $this->_sTmpFilePath = $this->_sTmpDir . '/tmp_db_dump_' . $sDbName;
        }

        return $this->_sTmpFilePath;
    }

    /**
     * Returns database dump data
     *
     * @return array
     */
    public function getDumpData()
    {
        if (is_null($this->_aDBDump)) {
            $this->_aDBDump = $this->_loadDumpData();
        }

        return $this->_aDBDump['data'];
    }

    /**
     * Returns database dump columns
     *
     * @return array
     */
    public function getDumpColumns()
    {
        if (is_null($this->_aDBDump)) {
            $this->_aDBDump = $this->_loadDumpData();
        }

        return $this->_aDBDump['columns'];
    }

    /**
     * Returns database dump columns
     *
     * @return array
     */
    public function getDumpChecksum()
    {
        if (is_null($this->_aDBDump)) {
            $this->_aDBDump = $this->_loadDumpData();
        }

        return $this->_aDBDump['checksum'];
    }

    /**
     * Checks which tables of the db changed and then restores these tables.
     * Uses dump file '/tmp/tmp_db_dump' for comparison and restoring.
     *
     * @param integer $iMode Maintenance mode
     * @param integer $iOutput Outout type
     * @return array changes array
     */
    public function restoreDB($iMode = self::MAINTENANCE_SINGLEROWS, $iOutput = self::MAINTENANCE_MODE_ONLYRESET)
    {
        $this->setResetMode($iMode);
        $this->setOutputMode($iOutput);

        $aDump = $this->getDumpData();
        $aTables = $this->_getDbTables();

        $aDumpChecksum = $this->getDumpChecksum();
        $aChecksum = $this->_getTableChecksum($aTables);

        $blHasChanges = false;
        foreach ($aTables as $sTable) {
            if (!isset($aDump[$sTable])) {
                $this->_dropTable($sTable);
            } else if ($aChecksum[$sTable] !== $aDumpChecksum[$sTable]) {
                $this->restoreTable($sTable, false);
                $blHasChanges = true;
            }
        }

        if ($blHasChanges) {
            $this->_aDBDump['checksum'] = $this->_getTableChecksum($aTables);
        }

        $this->_outputChanges();

        return $this->_aChanges;
    }

    /**
     * Restores table records
     *
     * @param string $sTable
     * @param bool $blCheckChecksum
     * @param bool $blRestoreColumns whether to check and restore table columns
     */
    public function restoreTable($sTable, $blCheckChecksum = true, $blRestoreColumns = false)
    {
        if ($blCheckChecksum) {
            $aDumpChecksum = $this->getDumpChecksum();
            $aChecksum = $this->_getTableChecksum($sTable);
            if ($aChecksum[$sTable] === $aDumpChecksum[$sTable]) {
                return;
            }
        }

        if ($blRestoreColumns) {
            $this->_restoreColumns($sTable);
        }

        $oDB = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $oRows = $oDB->query("Select * from " . $sTable);
        $aDump = $this->getDumpData();

        $aExistingIds = array();

        if ($oRows && $oRows->recordCount() > 0) {
            while ($aRow = $oRows->fetchRow()) {
                if ($aRow['OXID']) {
                    $blRestored = $this->restoreRecord($sTable, $aRow['OXID'], $aRow);
                } else {
                    $this->resetTable($sTable);
                    return;
                }

                if ($this->getResetMode() == self::MAINTENANCE_WHOLETABLES && $blRestored) {
                    return;
                }

                $aExistingIds[] = $aRow['OXID'];
            }

            $aOriginalIds = array_keys($aDump[$sTable]);
            $aMissingRecords = array_diff($aOriginalIds, $aExistingIds);

            $this->_insertMissingRecords($sTable, $aMissingRecords);
        } else if (!empty($aDump[$sTable])) {
            $this->resetTable($sTable);
        }
    }

    /**
     * Restores one record in a table. If no aData parameter is passed, record is selected form the database
     *
     * @param string $sTable table name
     * @param string $sId record id to restore
     * @param array $aData record data. Will be selected from database if not passed
     * @return bool whether record was restored
     */
    public function restoreRecord($sTable, $sId, $aData = null)
    {
        $aDump = $this->getDumpData();
        $blResult = false;

        if (is_null($aData)) {
            $oDB = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
            $aData = $oDB->getRow("SELECT * FROM `$sTable` WHERE oxid = '$sId'");
        }

        if (!empty($aData)) {
            if (!isset($aDump[$sTable][$sId])) {
                $this->_deleteRecord($sTable, $sId);
                $blResult = true;
            } else {
                $blResult = $this->_updateRecord($sTable, $sId, $aData, $aDump[$sTable][$sId]);
            }
        } else {
            $this->_insertMissingRecords($sTable, $sId);
        }

        return $blResult;
    }

    /**
     * Drops all table records and adds them back from dump
     *
     * @param $sTable
     * @return bool
     */
    public function resetTable($sTable)
    {
        $aDump = $this->getDumpData();

        $this->_executeSQL("TRUNCATE TABLE `$sTable`", $sTable, "data was changed");

        foreach ($aDump[$sTable] as $aVals) {
            $this->_executeSQL($aVals["_sql_"], $sTable);
        }

        return true;
    }

    /**
     * Returns database dump from file
     *
     * @return array
     */
    protected function _loadDumpData()
    {
        modConfig::getInstance()->cleanup();
        modConfig::$unitMOD = null;

        $aDBDump = file_get_contents($this->getDumpFilePath());
        $aDBDump = unserialize($aDBDump);

        return $aDBDump;
    }

    /**
     * Drops table
     *
     * @param $sTable
     */
    protected function _dropTable($sTable)
    {
        $sSQL = "DROP TABLE `$sTable`";
        $this->_executeSQL($sSQL, $sTable, "was created");
    }

    /**
     * Restores table columns (adds or removes columns)N
     *
     * @param $sTable
     */
    protected function _restoreColumns($sTable)
    {
        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);

        $aColumns = $oDb->getCol("SHOW COLUMNS FROM `$sTable`", 'Field');
        $aDumpColumns = $this->getDumpColumns();
        $aOriginalColumns = array_keys($aDumpColumns[$sTable]);

        $aExcessColumns = array_diff($aColumns, $aOriginalColumns);

        if (!empty($aExcessColumns)) {
            $sSQL = "ALTER TABLE $sTable DROP COLUMN (".implode(', ', $aExcessColumns).");";
            $this->_executeSQL($sSQL, $sTable, "has created columns (".implode(', ', $aExcessColumns).")");
        }
    }

    /**
     * Deletes record from database.
     * In MAINTENANCE_WHOLETABLES mode resets all table records.
     *
     * @param string $sTable
     * @param string $sId
     */
    protected function _deleteRecord($sTable, $sId)
    {
        if ($this->getResetMode() == self::MAINTENANCE_SINGLEROWS) {
            $sSQL = "DELETE FROM " . $sTable . " WHERE OXID = '" . $sId . "'";
            $this->_executeSQL($sSQL, $sTable, "record was added with id '$sId' ");
        } else if ($this->getResetMode() == self::MAINTENANCE_WHOLETABLES) {
            $this->resetTable($sTable);
        }
    }

    /**
     * Updates record values if changed.
     *
     * @param string $sTable
     * @param string $sId
     * @param array $aCurrentValues current record values
     * @param array $aUpdatedValues what values should be set
     * @return bool whether record was updated
     */
    protected function _updateRecord($sTable, $sId, $aCurrentValues, $aUpdatedValues)
    {
        $blResult = false;
        $aColumns = $this->_getChangedColumns($aCurrentValues, $aUpdatedValues);
        $oDb = oxDb::getDb();

        if (!empty($aColumns)) {
            if ($this->getResetMode() == self::MAINTENANCE_SINGLEROWS) {
                $sSQL = "UPDATE `$sTable` SET ";
                $aUpdates = array();
                foreach ($aColumns as $sColumn) {
                    $sValue = $oDb->quote($aUpdatedValues[$sColumn]);
                    $aUpdates[] = " `$sColumn` = $sValue ";
                }
                $sSQL = $sSQL . implode(', ', $aUpdates) . " WHERE `oxid` = ".$oDb->quote($sId);

                $this->_executeSQL($sSQL, $sTable, "record '$sId' columns '" . implode("', '", $aColumns) . "' was changed");
            } else if ($this->getResetMode() == self::MAINTENANCE_WHOLETABLES) {
                $this->resetTable($sTable);
            }
            $blResult = true;
        }

        return $blResult;
    }

    /**
     * Returns columns whom values does not match
     *
     * @param array $aRow row values
     * @param array $aExpectedRow expected row values
     * @return array
     */
    protected function _getChangedColumns($aRow, $aExpectedRow)
    {
        $aChangedColumns = array();

        foreach ($aRow as $sColumn => $sEntry) {
            if ($sColumn == 'OXTIMESTAMP') {
                continue;
            }

            if (array_key_exists($sColumn, $aExpectedRow) && strcmp($aExpectedRow[$sColumn], $sEntry) != 0) {
                $aChangedColumns[] = $sColumn;
            }
        }

        return $aChangedColumns;
    }

    /**
     * Inserts missing records
     *
     * @param string $sTable
     * @param array|string $mRecords either array of ids or one id
     */
    protected function _insertMissingRecords($sTable, $mRecords)
    {
        $aDump = $this->getDumpData();
        $aRecords = is_array($mRecords) ? $mRecords : array($mRecords);

        foreach ($aRecords as $sId) {
            $this->_executeSQL($aDump[$sTable][$sId]['_sql_'], $sTable, "record '$sId' was removed");
        }
    }

    /**
     * Depending on output mode executes given sql or just logs it
     *
     * @param string $sQuery
     * @param string $sTable
     * @param string $sMessage
     */
    protected function _executeSQL($sQuery, $sTable, $sMessage = '')
    {
        $iMode = $this->getOutputMode();

        if ($iMode == self::MAINTENANCE_MODE_ONLYRESET || $iMode == self::MAINTENANCE_MODE_RESETANDOUTPUT) {
            $oDB = oxDb::getDb();
            $oDB->Query($sQuery);
        }

        if ($sMessage) {
            $this->_aChanges[$sTable][] = "Table '$sTable', $sMessage";
        }

        $this->_aQueries[$sTable][] = $sQuery;
    }

    /**
     * Writes changes to log file
     */
    protected function _outputChanges()
    {
        $iMode = $this->getOutputMode();

        if ($iMode == self::MAINTENANCE_MODE_ONLYOUTPUT || $iMode == self::MAINTENANCE_MODE_RESETANDOUTPUT) {
            $sChanges = "DB RESET EXECUTION. start time: " . date('Y-m-d H:i:s') . "\n\n";

            $iTotalChanges = 0;
            foreach ($this->_aChanges as $sTable => $aTableChanges) {
                $iChanged = count($aTableChanges);
                $sChanges .= "In table '$sTable' are $iChanged changes:\n";
                $sChanges .= implode("\n", $aTableChanges);
                $iTotalChanges += $iChanged;
            }

            $sChanges .= "\nIn total there are $iTotalChanges changes.\n\n\n";

            $sChanges .= "Executed queries:\n\n";
            foreach ($this->_aQueries as $sTable => $aQueries) {
                $sChanges .= "Queries related to table '$sTable':\n";
                $sChanges .= implode("\n", $aQueries);
            }

            $sChanges .= "\n\n\n\nOriginal database dump:\n\n";
            $sChanges .= var_export($this->getDumpData(), 1);

            if ($iTotalChanges > 0) {
                file_put_contents('dbchanges_log.txt', $sChanges, FILE_APPEND);
            }
        }
    }

    /**
     * Creates a dump of the current database, stored in the file '/tmp/tmp_db_dump'
     * the dump includes the data and sql insert statements
     *
     * @throws Exception
     * @return null
     */
    public function dumpDB()
    {
        $iStartTime = microtime(true);

        $aTables = $this->_getDbTables();

        if (empty($aTables)) {
            $sDbName = oxConfig::getInstance()->getConfigParam('dbName');

            throw new Exception("no tables on "
                . oxConfig::getInstance()->getConfigParam('dbHost')
                . ":$sDbName, using "
                . oxConfig::getInstance()->getConfigParam('dbUser')
                . ":"
                . oxConfig::getInstance()->getConfigParam('dbPwd'));
        }

        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);

        $aData = array();
        $aColumns = array();
        $aChecksum = $this->_getTableChecksum($aTables);

        foreach ($aTables as $sTable) {
            $aData[$sTable] = array();
            $aColumns[$sTable] = $oDb->getAssoc("SHOW COLUMNS FROM `$sTable`", 'Field');

            $oResult = $oDb->Query("Select * from " . $sTable);
            if ($oResult && $oResult->RecordCount() > 0) {
                $iRow = 0;
                while ($aRow = $oResult->fetchRow()) {
                    $sId = $aRow['OXID'];
                    if (!$sId) {
                        $sId = $iRow++;
                    }
                    $aData[$sTable][$sId] = array();
                    $aData[$sTable][$sId]["_sql_"] = $this->getInsertString($aRow, $sTable);
                    foreach ($aRow as $sColumn => $sEntry) {
                        $aData[$sTable][$sId][$sColumn] = $sEntry;
                    }
                }
            }
        }

        $this->_aDBDump = array('columns' => $aColumns, 'data' => $aData, 'checksum' => $aChecksum);
        file_put_contents($this->getDumpFilePath(), serialize($this->_aDBDump));

        echo("db Dumptime: " . (microtime(true) - $iStartTime) . "\n");
    }

    /**
     * Creates a insert string to insert the given row into to given table
     *
     * @param array $aRow a array of the current row in the db
     * @param string $sTable the name of the current table
     *
     * @return string a sql insert string for the given row
     */
    private function getInsertString($aRow, $sTable)
    {
        $sSQL = 'INSERT INTO ' . $sTable . ' ';
        $sColumns = '(';
        $sValues = '(';
        foreach ($aRow as $sColumn => $sEntry) {
            $sColumns .= $sColumn . ',';
            if (is_null($sEntry)) {
                $sValues .= 'null,';
            } else {
                $sEntry = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->quote( $sEntry );
                $sValues .= $sEntry . ',';
            }
        }
        $sColumns = substr($sColumns, 0, strlen($sColumns) - 1);
        $sValues = substr($sValues, 0, strlen($sValues) - 1);
        $sColumns .= ')';
        $sValues .= ')';

        $sSQL .= $sColumns . ' VALUES ' . $sValues;

        return $sSQL;
    }

    /**
     * Converts a string to UTF format.
     *
     * @param array|string $aTables
     * @return array
     */
    protected function _getTableChecksum($aTables)
    {
        $aTables = is_array($aTables)? $aTables : array($aTables);
        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $sSelect = 'CHECKSUM TABLE ' . implode(", ", $aTables);
        $aResults = $oDb->getArray($sSelect);

        $sDbName = oxConfig::getInstance()->getConfigParam('dbName');
        $aChecksum = array();
        foreach ($aResults as $aResult) {
            $sTable = str_replace($sDbName.'.', '', $aResult['Table']);
            $aChecksum[$sTable] = $aResult['Checksum'];
        }

        return $aChecksum;
    }

    /**
     * Returns database tables, excluding views
     */
    protected function _getDbTables()
    {
        $oDB = oxDb::getDb(oxDb::FETCH_MODE_NUM);
        $aTables = $oDB->getCol("SHOW TABLES");

        foreach ($aTables as $iKey => $sTable) {
            if (strpos($sTable, 'oxv_') === 0) {
                unset($aTables[$iKey]);
            }
        }
        return $aTables;
    }
}
