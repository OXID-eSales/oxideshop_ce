<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: dbMaintenance.php, v 1.0 2007.9.3 09.46.26 mathiasf$
 */

 define('MAINTENANCE_SINGLEROWS', 1);
 define('MAINTENANCE_WHOLETABLES', 2);

 define('MAINTENANCE_MODE_ONLYRESET', 1);
 define('MAINTENANCE_MODE_ONLYOUTPUT', 2);
 define('MAINTENANCE_MODE_RESETANDOUTPUT', 3);

/**
 * Database maintenance class responsible complete for backuping and restoration of test database.
 */
class dbMaintenance
{

    /*
     * dump of the original db
     */
    private $_oDBDump = null;

    /**
     * Checks which tables of the db changed and then restores these tables.
     *
     * Uses dump file '/tmp/tmp_db_dump' for comparison and restoring.
     *
     * @param integer $iMode   Maintenance mode
     * @param integer $iOutput Outout type
     *
     * @return integer changet
     */
    public function restoreDB($iMode = MAINTENANCE_SINGLEROWS, $iOutput = MAINTENANCE_MODE_ONLYRESET)
    {
        // php 523 error
        modConfig::getInstance()->cleanup();
        modConfig::$unitMOD = null;

        $time = microtime(true);
        $myConfig = oxConfig::getInstance();
        $sDbName = oxConfig::getInstance()->getConfigParam('dbName');
        $this->_oDBDump = file_get_contents('/tmp/tmp_db_dump_'.$sDbName);
        $this->_oDBDump = unserialize($this->_oDBDump);

        $myDB = oxDb::getDb();
        $myDB->SetFetchMode(ADODB_FETCH_NUM);
        $rs = $myDB->Query("SHOW TABLES");
        $myDB->SetFetchMode(ADODB_FETCH_ASSOC);
        $sChanges = "";
        //current table
        $sTable = "";
        //list of sql commands to execute;
        $aSQL=array();
        $aInsert = array();
        $aTableInsert = array();

        while (!$rs->EOF) {
            $sTable = $rs->fields[0];
            $rs->moveNext();
            if (strpos($sTable, "oxv_")===false) {
                $aChangesCounter[$sTable]=0;

                if (!isset($this->_oDBDump[$sTable])) {
                    $sChanges.="Table ".$sTable." was added\n";
                    $aChangesCounter[$sTable]++;
                    //delete this table
                    $aSQL[] = "DROP TABLE ".$sTable."";
                    continue;
                }


                $rs2 = $myDB->Query("Select * from ".$sTable);

                if ($rs2 && $rs2->RecordCount()>0) {
                    $rowCount = 1;
                    while ($aRow = $rs2->fetchRow()) {
                    $oxid = $aRow['OXID'];
                    $blNoOXID = false;
                    if ($oxid == "") {
                        // the tables without oxid,
                        $oxid = $rowCount++;
                        $blNoOXID = true;
                    }
                     if (!isset($this->_oDBDump[$sTable][$oxid])) {
                        $aChangesCounter[$sTable]++;
                        if ($iMode == MAINTENANCE_SINGLEROWS && !$blNoOXID) {
                            $sChanges.="In table ".$sTable." a record was added\n";
                            $aSQL[] = "DELETE FROM ".$sTable." WHERE OXID = '". $oxid ."'";
                            $aInsert[$sTable][] = $oxid;
                            //mark this row as existing
                            $this->_oDBDump[$sTable][$oxid]['_EXISTS_'] = true;
                            continue; //with next row
                        } else if ($iMode == MAINTENANCE_WHOLETABLES || $blNoOXID) {
                            //in single table mode tables without oxid are handeld like in whole table mode
                            $sChanges.="In table ".$sTable." record(s)was(were) added\n";
                            $aSQL[] = "DELETE FROM ".$sTable;
                            $aTableInsert[] = $sTable;
                            //mark this table as existing
                            $this->_oDBDump[$sTable]['_EXISTS_'] = true;
                            //skip rest of table
                            continue 2;
                        }
                     } else {
                        //check only if the record already existed
                        foreach ($aRow as $sColumn => $sEntry) {
                            if ($sColumn == "OXVARVALUE") {
                                //special handeling of blob values of oxconfig
                                $sEntry= oxUtils::getInstance()->strMan($sEntry, $myConfig->getConfigParam( 'sConfigKey' ) );
                            }
                            if (!isset($this->_oDBDump[$sTable][$oxid][$sColumn])) {
                                //a new colum
                                   $aChangesCounter[$sTable]++;
                                   if ($iMode == MAINTENANCE_SINGLEROWS  && !$blNoOXID) {
                                           $sChanges.="In table ".$sTable." and record oxid: ". $oxid ." column ". $sColumn ." was added\n";
                                        $aSQL[] = "DELETE FROM ".$sTable." WHERE OXID = '". $oxid ."'";
                                        $aInsert[$sTable][] = $oxid;
                                        //mark this row as existing
                                        $this->_oDBDump[$sTable][$oxid]['_EXISTS_'] = true;
                                        //skip rest of row
                                        continue 2;
                                   } else if ($iMode == MAINTENANCE_WHOLETABLES  || $blNoOXID) {
                                        $sChanges.="In table ".$sTable." column(s) {$sColumn} was(were) added\n";
                                        $aSQL[] = "DELETE FROM ".$sTable;
                                        $aTableInsert[] = $sTable;
                                        //mark this table as existing
                                        $this->_oDBDump[$sTable]['_EXISTS_'] = true;
                                        //skip rest of table
                                        continue 3;
                                   }
                            } elseif (strcmp ($this->_oDBDump[$sTable][$oxid][$sColumn], $sEntry)!= 0) {
                                //changed value
                                $aChangesCounter[$sTable]++;
                                if ($iMode == MAINTENANCE_SINGLEROWS  && !$blNoOXID) {
                                    $sChanges.="In table ".$sTable.", record oxid: ". $oxid ." and column ". $sColumn ." the value changed from: ". $this->_oDBDump[$sTable][$oxid][$sColumn]. " to: ".$sEntry ."\n";
                                    $aSQL[] = "DELETE FROM ".$sTable." WHERE OXID = '". $oxid ."'";
                                    $aInsert[$sTable][] = $oxid;
                                    //mark this row as existing
                                    $this->_oDBDump[$sTable][$oxid]['_EXISTS_'] = true;
                                    //skip rest of row
                                    continue 2;
                                } elseif ($iMode == MAINTENANCE_WHOLETABLES || $blNoOXID) {
                                       $sChanges.="In table ".$sTable.", record values changed\n";
                                    $aSQL[] = "DELETE FROM ".$sTable;
                                    $aTableInsert[] = $sTable;
                                    //mark this table as existing
                                    $this->_oDBDump[$sTable]['_EXISTS_'] = true;
                                    //skip rest of table
                                    continue 3;
                                }
                            }
                        }
                     }
                        $this->_oDBDump[$sTable][$oxid]['_EXISTS_'] = true;
                    }
                }
                //checking for removed rows
                foreach ($this->_oDBDump[$sTable] as $oxid=>$aEntry) {
                    if ($oxid == '_EXISTS_') {
                        if ($iMode == MAINTENANCE_WHOLETABLES) {
                            //everything is fine, whole table will be inserted
                            continue;
                        }
                    } else {
                        if (!isset($aEntry['_EXISTS_'])) {
                           $sChanges.="In table ".$sTable.", record oxid ". $oxid ." was removed (". $aEntry .")\n";
                            $aChangesCounter[$sTable]++;
                            //add the record again, same for both modes
                            $aInsert[$sTable][] = $oxid;
                        }
                    }
                }
            }
        }
            //add the restore sql scripts
            foreach ($aInsert as $sTable => $aOxid) {
                foreach ($aOxid as $k => $oxid) {
                    $aSQL[] = $this->_oDBDump[$sTable][$oxid]["_sql_"];
                }
            }
            //add the whole tables (for MAINTENANCE_WHOLETABLES mode)
            foreach ($aTableInsert as $k => $sTable) {
                foreach ($this->_oDBDump[$sTable] as $oxid => $aVals) {
                    if ( $oxid == '_EXISTS_' )
                        continue;
                    $aSQL[] = $aVals["_sql_"];
                }
            }

            if ($iOutput == MAINTENANCE_MODE_ONLYOUTPUT || $iOutput == MAINTENANCE_MODE_RESETANDOUTPUT) {
                    $sChanges .="\n\n";
                    $sChanges .="DB RESET EXECUTION. start time: ". date('Y-m-d H:i:s');
                    $sChanges .="\n";
                    $i=0;
                    foreach ($aChangesCounter as $sTable => $iChanges) {
                        if ($iChanges>0) {
                            $sChanges.="In table ".$sTable." are ".$iChanges." changes!\n";
                            $i+=$iChanges;
                        }
                    }
                    $sChanges.="\nIn total there are ".$i." changes!!!\n\n\n\n";
                    if ($i>0) {
                        file_put_contents('dbchanges_log.txt', $sChanges . var_export($aSQL, 1) . var_export($this->_oDBDump, 1), FILE_APPEND);
                    }
            }
            if ($iOutput == MAINTENANCE_MODE_ONLYRESET || $iOutput == MAINTENANCE_MODE_RESETANDOUTPUT) {
                //now execute delete and drop statements and the insert statements (at the end)
                foreach ($aSQL as $k => $v) {
                    $myDB->Query($v);
                }
            }

     $myDB->SetFetchMode(ADODB_FETCH_NUM);
     //echo(" T:".(microtime(true)-$time));
     return $aChangesCounter;
    }

    private $_aData = array();

    /**
     * Creates a dump of the current database, stored in the file '/tmp/tmp_db_dump'
     * the dump includes the data and sql insert statements
     *
     * @return null
     */
    public function dumpDB()
    {
        $sDbName = oxConfig::getInstance()->getConfigParam('dbName');

        $time = microtime (true);
        $this->myDB = oxDb::getDb();
        $myConfig = oxConfig::getInstance();
        $this->myDB->SetFetchMode(ADODB_FETCH_NUM);

        if (!($rs = $this->myDB->Query("SHOW TABLES"))) {
            throw new Exception("no tables on "
                . oxConfig::getInstance()->getConfigParam('dbHost')
                . ":$sDbName, using "
                . oxConfig::getInstance()->getConfigParam('dbUser')
                . ":"
                . oxConfig::getInstance()->getConfigParam('dbPwd'));
        }

        $this->myDB->SetFetchMode(ADODB_FETCH_ASSOC);


        $aTables = array();
        while (!$rs->EOF) {
            if (strpos($rs->fields[0], "oxv_")===false) {
                $aTables[] = $rs->fields[0];
            }
            $rs->moveNext();
        }
        foreach ($aTables as $sTable) {
            $this->_aData[$sTable]=array();

            $rs = $this->myDB->Query("Select * from ".$sTable);
            if ($rs && $rs->RecordCount()>0) {
                $rowNum = 1;
                while ($aRow = $rs->fetchRow()) {
                 $oxid = $aRow['OXID'];
                 if ($oxid == "") {
                     //the tables without oxid (oxsession, oxlogs, oxadminlogs)
                    $oxid = $rowNum++;
                 }
                 $this->_aData[$sTable][$oxid]=array();
                 $this->_aData[$sTable][$oxid]["_sql_"] = $this->getInsertString($aRow, $sTable);
                    foreach ($aRow as $sColumn => $sEntry) {
                        if ($sColumn == "OXVARVALUE") {
                            $sEntry= oxUtils::getInstance()->strMan($sEntry, $myConfig->getConfigParam( 'sConfigKey' ) );
                        }
                        $this->_aData[$sTable][$oxid][$sColumn] = $sEntry;
                    }
                }
            }
        }
           $sResult = serialize ($this->_aData);
           file_put_contents('/tmp/tmp_db_dump_'.$sDbName, $sResult);

           echo("db Dumptime: ".(microtime (true)-$time)."\n");

        $this->myDB->SetFetchMode(ADODB_FETCH_NUM);
    }
    /**
     * Creates a insert string to insert the given row into to given table
     *
     * @param array  $aRow   a array of the current row in the db
     * @param string $sTable the name of the current table
     *
     * @return string a sql insert string for the given row
     */
    private function getInsertString($aRow,$sTable)
    {
        $sSQL= 'INSERT INTO '.$sTable.' ';
        $sColumns='(';
        $sValues='(';
        foreach ($aRow as $sColumn => $sEntry) {
            $sColumns.=$sColumn.',';
            if (is_null($sEntry)) {
                $sEntry = 'null';
                $sValues.='null,';
            } else {
                $sEntry = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->quote( $sEntry );
                $sValues.= $sEntry.',';
            }

        }
        $sColumns = substr($sColumns, 0, strlen($sColumns)-1);
        $sValues = substr($sValues, 0, strlen($sValues)-1);
        $sColumns.=')';
        $sValues.=')';

        $sSQL .=$sColumns.' VALUES '.$sValues;


        return $sSQL;
    }
}
