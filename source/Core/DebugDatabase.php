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

namespace OxidEsales\Eshop\Core;

use oxRegistry;
use oxDb;

/**
 * class for parsing and retrieving warnings from adodb saved sql table
 */
class DebugDatabase
{

    /**
     * Array of SQL queryes to skip
     *
     * @var array
     */
    private static $_aSkipSqls = array();

    /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     */
    public function __construct()
    {
    }

    /**
     * Removes special chars (' ', "\t", "\r", "\n") from passed string
     *
     * @param string $str string to cleanup
     *
     * @return string
     */
    protected static function _skipWhiteSpace($str)
    {
        return str_replace(array(' ', "\t", "\r", "\n"), '', $str);
    }

    /**
     * Checks if query is already in log file
     *
     * @param string $sql sql query to check
     *
     * @return bool
     */
    protected static function _isSkipped($sql)
    {
        if (!count(self::$_aSkipSqls)) {
            $file = oxRegistry::getConfig()->getLogsDir() . 'oxdebugdb_skipped.sql';
            if (is_readable($file)) {
                $skip = explode('-- -- ENTRY END', file_get_contents($file));
                foreach ($skip as $q) {
                    if (($q = self::_skipWhiteSpace($q))) {
                        self::$_aSkipSqls[md5($q)] = true;
                    }
                }
            }
        }
        $checkTpl = md5(self::_skipWhiteSpace(self::_getSqlTemplate($sql)));
        $check = md5(self::_skipWhiteSpace($sql));

        return self::$_aSkipSqls[$check] || self::$_aSkipSqls[$checkTpl];
    }

    /**
     * warning list generator
     *
     * @return array
     */
    public function getWarnings()
    {
        $warnings = array();
        $history = array();
        $db = oxDb::getDb();
        if (method_exists($db, "logSQL")) {
            $lastDbgState = $db->logSQL(false);
        }
        $rs = $db->select("select sql0, sql1, tracer from adodb_logsql order by created limit 5000");
        if ($rs != false && $rs->recordCount() > 0) {
            $lastRecord = null;
            while (!$rs->EOF) {
                $id = $rs->fields[0];
                $sql = $rs->fields[1];

                if (!self::_isSkipped($sql)) {
                    if ($this->_checkMissingKeys($sql)) {
                        $warnings['MissingKeys'][$id] = true;
                        // debug: echo "<li> <pre>".self::_getSqlTemplate($sql)." </pre><br>";
                    }
                }

                // multiple executed single statements
                if ($lastRecord && $this->_checkMess($sql, $lastRecord[1])) {
                    // sql0 matches, also, this is exactly following statement: MESS?
                    $warnings['MESS'][$id] = true;
                    $warnings['MESS'][$lastRecord[0]] = true;
                }

                foreach ($history as $histItem) {
                    if ($this->_checkMess($sql, $histItem[1])) {
                        // sql0 matches, also, this is exactly following statement: MESS?
                        $warnings['MESS_ALL'][$id] = true;
                        $warnings['MESS_ALL'][$histItem[0]] = true;
                    }
                }

                $history[] = $lastRecord = $rs->fields;
                /*
                if (preg_match('/select[^\*]*(?<!(from))\*.*?(?<!(from))from/im', $sql)) {
                    $warnings['Select fields not strict'][$id] = true;
                }*/
                $rs->moveNext();
            }
        }
        $warnings = $this->_generateWarningsResult($warnings);
        $this->_logToFile($warnings);
        if (method_exists($db, "logSQL")) {
            $db->logSQL($lastDbgState);
        }

        return $warnings;
    }

    /**
     * returns nice formatted array
     *
     * @param array $input messages array
     *
     * @return array
     */
    protected function _generateWarningsResult($input)
    {
        $aOutput = array();
        $oDb = oxDb::getDb();
        foreach ($aInput as $fnc => $aWarnings) {
            $ids = implode(",", oxDb::getDb()->quoteArray(array_keys($aWarnings)));
            $rs = $oDb->select("select sql1, timer, tracer from adodb_logsql where sql0 in ($ids)");
            if ($rs != false && $rs->recordCount() > 0) {
                while (!$rs->EOF) {
                    $outputEntry = array();
                    $outputEntry['check'] = $fnc;
                    $outputEntry['sql'] = $rs->fields[0];
                    $outputEntry['time'] = $rs->fields[1];
                    $outputEntry['trace'] = $rs->fields[2];
                    $output[] = $outputEntry;
                    $rs->moveNext();
                }
            }
        }

        return $output;
    }

    /**
     * check missing keys - use explain
     * return true on warning
     *
     * @param string $sql query string
     *
     * @return bool
     */
    protected function _checkMissingKeys($sql)
    {
        if (strpos(strtolower(trim($sql)), 'select ') !== 0) {
            return false;
        }

        $rs = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->execute("explain $sql");
        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
                if ($this->_missingKeysChecker($rs->fields)) {
                    return true;
                }
                $rs->moveNext();
            }
        }

        return false;
    }

    /**
     * check if remark of explain is not using keys
     * true if not using
     *
     * @param array $explain db explain response array
     *
     * @return bool
     */
    private function _missingKeysChecker($explain)
    {
        if ($explain['type'] == 'system') {
            return false;
        }

        if (strstr($explain['Extra'], 'Impossible WHERE') !== false) {
            return false;
        }

        if ($explain['key'] === null) {
            return true;
        }

        if (strpos($explain['type'], 'range')) {
            return true;
        }

        if (strpos($explain['type'], 'index')) {
            return true;
        }

        if (strpos($explain['type'], 'ALL')) {
            return true;
        }

        if (strpos($explain['Extra'], 'filesort')) {
            if (strpos($explain['ref'], 'const') === false) {
                return true;
            }
        }

        if (strpos($explain['Extra'], 'temporary')) {
            return true;
        }

        return false;
    }

    /**
     * return true if statements are similar
     *
     * @param string $s1 statement one
     * @param string $s2 statement two
     *
     * @return boolean
     */
    protected function _checkMess($s1, $s2)
    {
        if (strpos(strtolower(trim($s1)), 'select ') !== 0) {
            return false;
        }

        if (strpos(strtolower(trim($s2)), 'select ') !== 0) {
            return false;
        }

        // strip from values
        $s1 = self::_getSqlTemplate($s1);
        $s2 = self::_getSqlTemplate($s2);

        if (!strcmp($s1, $s2)) {
            return true;
        }

        return false;
    }

    /**
     * strips sql down of its values
     *
     * @param string $sql sql to process
     *
     * @return string
     */
    protected static function _getSqlTemplate($sql)
    {
        $sql = preg_replace("/'.*?(?<!\\\\)'/", "'#VALUE#'", $sql);
        $sql = preg_replace('/".*?(?<!\\\\)"/', '"#VALUE#"', $sql);
        $sql = preg_replace('/[0-9]/', '#NUMVALUE#', $sql);

        return $sql;
    }

    /**
     * logs warnings to file
     *
     * @param array $warnings warnings
     */
    protected function _logToFile($warnings)
    {
        $str = getStr();
        $logMsg = "\n\n\n\n\n\n-- " . date("m-d  H:i:s") . " --\n\n";
        foreach ($warnings as $w) {
            $logMsg .= "{$w['check']}: {$w['time']} - " . $str->htmlentities($w['sql']) . "\n\n";
            $logMsg .= $w['trace'] . "\n\n\n\n";
        }
        oxRegistry::getUtils()->writeToLog($logMsg, 'oxdebugdb.txt');
    }
}
