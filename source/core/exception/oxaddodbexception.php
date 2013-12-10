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
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

/**
 * exception class for all kind of connection problems to external servers, e.g.:
 * - no connection, proxy problem, wrong configuration, etc.
 * - ipayment server
 * - online vat id check
 * - db server
 */
class oxAddoDbException extends oxConnectionException
{
    var $dbms;
    var $fn;
    var $sql = '';
    var $params = '';
    var $host = '';
    var $database = '';

    function __construct($dbms, $fn, $errno, $errmsg, $p1, $p2, $thisConnection)
    {
        switch($fn) {
            case 'EXECUTE':
                $this->sql = $p1;
                $this->params = $p2;
                $s = "$dbms error: [$errno: $errmsg] in $fn(\"$p1\")\n";
                break;

            case 'PCONNECT':
            case 'CONNECT':
                $user = $thisConnection->username;
                $s = "$dbms error: [$errno: $errmsg] in $fn($p1, '$user', '****', $p2)\n";
                break;

            default:
                $s = "$dbms error: [$errno: $errmsg] in $fn($p1, $p2)\n";
                break;
        }

        $this->dbms = $dbms;
        $this->host = $thisConnection->host;
        $this->database = $thisConnection->database;
        $this->fn = $fn;
        $this->msg = $errmsg;

        if (!is_numeric($errno))
            $errno = -1;

        parent::__construct($s, $errno);
    }
}
