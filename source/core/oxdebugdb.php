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

/**
 * class for parsing and retrieving warnings from adodb saved sql table
 */
class oxDebugDb
{
    /**
     * Array of SQL queryes to skip
     *
     * @var array
     */
    private static $_aSkipSqls = array();

   /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     *
     * @return null;
     */
    public function __construct()
    {
    }

    /**
     * Removes special chars (' ', "\t", "\r", "\n") from passed string
     *
     * @param string $sStr string to cleanup
     *
     * @return string
     */
    protected static function _skipWhiteSpace( $sStr )
    {
        return str_replace( array( ' ', "\t", "\r", "\n"), '', $sStr );
    }

    /**
     * Checks if query is allready in log file
     *
     * @param string $sSql sql query to check
     *
     * @return bool
     */
    protected static function _isSkipped($sSql)
    {
        if ( !count(self::$_aSkipSqls ) ) {
            $sFile = oxRegistry::getConfig()->getLogsDir() . 'oxdebugdb_skipped.sql';
            if (is_readable($sFile)) {
                $aSkip = explode('-- -- ENTRY END', file_get_contents( $sFile ));
                foreach ( $aSkip as $sQ ) {
                    if ( ( $sQ = self::_skipWhiteSpace( $sQ ) ) ) {
                        self::$_aSkipSqls[md5($sQ)] = true;
                    }
                }
            }
        }
        $checkTpl = md5(self::_skipWhiteSpace(self::_getSqlTemplate($sSql)));
        $check = md5(self::_skipWhiteSpace($sSql));
        return self::$_aSkipSqls[$check] || self::$_aSkipSqls[$checkTpl];
    }

    /**
     * warning list generator
     *
     * @return array
     */
    public function getWarnings()
    {
        $aWarnings = array();
        $aHistory = array();
        $oDb = oxDb::getDb();
        if (method_exists($oDb, "logSQL")) {
            $iLastDbgState = $oDb->logSQL( false );
        }
        $rs = $oDb->select( "select sql0, sql1, tracer from adodb_logsql order by created limit 5000" );
        if ($rs != false && $rs->recordCount() > 0 ) {
            $aLastRecord = null;
            while ( !$rs->EOF ) {
                $sId  = $rs->fields[0];
                $sSql = $rs->fields[1];

                if (!self::_isSkipped($sSql)) {
                    if ($this->_checkMissingKeys($sSql)) {
                        $aWarnings['MissingKeys'][$sId] = true;
                        // debug: echo "<li> <pre>".self::_getSqlTemplate($sSql)." </pre><br>";
                    }
                }

                // multiple executed single statements
                if ( $aLastRecord && $this->_checkMess( $sSql, $aLastRecord[1] ) ) {
                    // sql0 matches, also, this is exactly following statement: MESS?
                    $aWarnings['MESS'][$sId] = true;
                    $aWarnings['MESS'][$aLastRecord[0]] = true;
                }

                foreach ($aHistory as $aHistItem) {
                    if ( $this->_checkMess( $sSql, $aHistItem[1] ) ) {
                        // sql0 matches, also, this is exactly following statement: MESS?
                        $aWarnings['MESS_ALL'][$sId] = true;
                        $aWarnings['MESS_ALL'][$aHistItem[0]] = true;
                    }
                }

                $aHistory[] = $aLastRecord = $rs->fields;
                /*
                if (preg_match('/select[^\*]*(?<!(from))\*.*?(?<!(from))from/im', $sSql)) {
                    $aWarnings['Select fields not strict'][$sId] = true;
                }*/
                $rs->moveNext();
            }
        }
        $aWarnings = $this->_generateWarningsResult($aWarnings);
        $this->_logToFile( $aWarnings );
        if (method_exists($oDb, "logSQL")) {
            $oDb->logSQL( $iLastDbgState );
        }
        return $aWarnings;
    }

    /**
     * returns nice formatted array
     *
     * @param array $aInput messages array
     *
     * @return array
     */
    protected function _generateWarningsResult( $aInput )
    {
        $aOutput = array();
        $oDb = oxDb::getDb();
        foreach ($aInput as $fnc => $aWarnings) {
            $ids = implode(",", oxDb::getInstance()->quoteArray(array_keys($aWarnings)));
            $rs = $oDb->select("select sql1, timer, tracer from adodb_logsql where sql0 in ($ids)");
            if ($rs != false && $rs->recordCount() > 0) {
                while (!$rs->EOF) {
                    $aOutputEntry = array();
                    $aOutputEntry['check'] = $fnc;
                    $aOutputEntry['sql'] = $rs->fields[0];
                    $aOutputEntry['time'] = $rs->fields[1];
                    $aOutputEntry['trace'] = $rs->fields[2];
                    $aOutput[] = $aOutputEntry;
                    $rs->moveNext();
                }
            }
        }
        return $aOutput;
    }

    /**
     * check missing keys - use explain
     * return true on warning
     *
     * @param string $sSql query string
     *
     * @return bool
     */
    protected function _checkMissingKeys( $sSql )
    {
        if ( strpos( strtolower( trim( $sSql ) ), 'select ' ) !== 0 ) {
            return false;
        }

        $rs = oxDb::getDb( oxDb::FETCH_MODE_ASSOC )->execute( "explain $sSql" );
        if ( $rs != false && $rs->recordCount() > 0 ) {
            while (!$rs->EOF) {
                if ( $this->_missingKeysChecker( $rs->fields ) ) {
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
     * @param array $aExplain db explain response array
     *
     * @return bool
     */
    private function _missingKeysChecker($aExplain)
    {
        if ( $aExplain['type'] == 'system' ) {
            return false;
        }

        if ( strstr($aExplain['Extra'], 'Impossible WHERE' ) !== false ) {
            return false;
        }

        if ( $aExplain['key'] === null ) {
            return true;
        }

        if ( strpos( $aExplain['type'], 'range' ) ) {
            return true;
        }

        if ( strpos($aExplain['type'], 'index' ) ) {
            return true;
        }

        if ( strpos( $aExplain['type'], 'ALL' ) ) {
            return true;
        }

        if ( strpos( $aExplain['Extra'], 'filesort' ) ) {
            if ( strpos( $aExplain['ref'], 'const' ) === false ) {
                return true;
            }
        }

        if ( strpos( $aExplain['Extra'], 'temporary' ) ) {
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
    protected function _checkMess( $s1, $s2 )
    {
        if ( strpos( strtolower( trim( $s1 ) ), 'select ' ) !== 0 ) {
            return false;
        }

        if ( strpos( strtolower( trim( $s2 ) ), 'select ' ) !== 0 ) {
            return false;
        }

        // strip from values
        $s1 = self::_getSqlTemplate( $s1 );
        $s2 = self::_getSqlTemplate( $s2 );

        if (!strcmp($s1, $s2)) {
            return true;
        }

        return false;
    }

    /**
     * strips sql down of its values
     *
     * @param string $sSql sql to process
     *
     * @return string
     */
    protected static function _getSqlTemplate( $sSql )
    {
        $sSql = preg_replace( "/'.*?(?<!\\\\)'/", "'#VALUE#'", $sSql );
        $sSql = preg_replace( '/".*?(?<!\\\\)"/', '"#VALUE#"', $sSql );
        $sSql = preg_replace( '/[0-9]/', '#NUMVALUE#', $sSql );

        return $sSql;
    }

    /**
     * logs warnings to file
     *
     * @param array $aWarnings warnings
     *
     * @return null
     */
    protected function _logToFile($aWarnings)
    {
        $oStr = getStr();
        $sLogMsg = "\n\n\n\n\n\n-- ".date("m-d  H:i:s")." --\n\n";
        foreach ( $aWarnings as $w ) {
            $sLogMsg .= "{$w['check']}: {$w['time']} - ".$oStr->htmlentities($w['sql'])."\n\n";
            $sLogMsg .= $w['trace']."\n\n\n\n";
        }
        oxRegistry::getUtils()->writeToLog( $sLogMsg, 'oxdebugdb.txt' );
    }
}
