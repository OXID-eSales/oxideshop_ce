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


$_key      = $_cfg->sConfigKey;
$oDB       = mysql_connect( 'localhost', $_cfg->dbUser, $_cfg->dbPwd);

if ($_cfg->iUtfMode) {
    mysql_query("anter schema character set utf8 collate utf8_general_ci",$oDB);
    mysql_query("set names 'utf8'",$oDB);
    mysql_query("set character_set_database=utf8",$oDB);
    mysql_query("set character set utf8",$oDB);
    mysql_query("set character_set_connection = utf8",$oDB);
    mysql_query("set character_set_results = utf8",$oDB);
    mysql_query("set character_set_server = utf8",$oDB);
} else {
    mysql_query("alter schema character set latin1 collate latin1_general_ci",$oDB);
    mysql_query("set character set latin1",$oDB);
}

mysql_select_db( $_cfg->dbName , $oDB);

$sSqlDir = dirname(__FILE__)."/seleniumSql/";
$sSqlFileName = basename($_GET["filename"]);

if ( !$sSqlFileName ) {
    echo "Error: sql file name is empty.";
    exit;
}

if ( !file_exists($sSqlDir.$sSqlFileName) ) {
    echo "Error: File <b>{$sSqlFileName}</b> not found.";
    exit;
}

passthru ('mysql -u'.$_cfg->dbUser.' -p'.$_cfg->dbPwd.' '.$_cfg->dbName.' < '.$sSqlDir.$sSqlFileName, $sRes);

if ( $sRes == 0 ) {
    header("Location: ".$_cfg->sShopURL);
} else {
    echo "Error: SQL error in file <b>{$sSqlFileName}</b>.";
}

ob_end_flush();

