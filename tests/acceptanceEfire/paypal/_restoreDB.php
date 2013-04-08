<?php
/**
 * Full reinstall
 */


ob_start();

class _config {
    function __construct(){
        include "config.inc.php";
        include "core/oxconfk.php";
    }
}

$_cfg      = new _config();

if ( isset($_POST["dumpDb"]) ) {
    dumpDB( $_cfg );
}

if ( isset($_POST["restoreDb"]) ) {
    restoreDB( $_cfg );
}

ob_end_flush();

/**
 * Checks which tables of the db changed and then restores these tables.
 *
 * Uses dump file '/tmp/tmp_db_dump' for comparison and restoring.
 *
 * @return null
 */
function restoreDB( $_cfg )
{
    $time = microtime(true);
    //var_Dump("Restore: ".number_format(memory_get_usage(), 0, '.', ','));

    $sUser    = $_cfg->dbUser;
    $sPass    = $_cfg->dbPwd;
    $sDbName  = $_cfg->dbName;
    $sHost    = 'localhost';
    $demo     = '/tmp/tmp_db_dump_'.$sDbName;

    if (file_exists($demo)) {
        $sCmd = 'mysql -h'.escapeshellarg($sHost).' -u'.escapeshellarg($sUser).' -p'.escapeshellarg($sPass).' --default-character-set=utf8 '.escapeshellarg($sDbName).'  < '.escapeshellarg($demo).' 2>&1';
        exec($sCmd, $sOut, $ret);
        $sOut = implode("\n",$sOut);
    } else {
        echo "File $demo - not found!\n";
    }

}

/**
 * Creates a dump of the current database, stored in the file '/tmp/tmp_db_dump'
 * the dump includes the data and sql insert statements
 *
 * @return null
 */
function dumpDB( $_cfg )
{
    $time = microtime (true);
    // echo("Dump: ".number_format(memory_get_usage(), 0, '.', ','));

    $sUser    = $_cfg->dbUser;
    $sPass    = $_cfg->dbPwd;
    $sDbName  = $_cfg->dbName;
    $sHost    = 'localhost';
    $demo     = '/tmp/tmp_db_dump_'.$sDbName;

    $sCmd = 'mysqldump -h'.escapeshellarg($sHost).' -u'.escapeshellarg($sUser).' -p'.escapeshellarg($sPass).' --add-drop-table '.escapeshellarg($sDbName).'  > '.escapeshellarg($demo);
    exec($sCmd, $sOut, $ret);
    $sOut = implode("\n",$sOut);
    if ( $ret > 0 ) {
        throw new Exception( $sOut );
    }
    //echo("Dump end: ".number_format(memory_get_usage(), 0, '.', ','));
    if (file_exists($demo)) {
        echo("db Dumptime: ".(microtime (true)-$time)."\n");
    }

}

