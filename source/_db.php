<?php
/**
 * Full reinstall
 */

echo "<h1>Full reinstall of OXID eShop</h1>";




class _config {
    function __construct(){
        include "_version_define.php";
        include "config.inc.php";
        include "core/oxconfk.php";
    }
}
$_cfg      = new _config();




echo "<h2>Delete cookies</h2>";
echo "<ol>";
{
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $aCookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($aCookies as $sCookie) {
            $sRawCookie = explode('=', $sCookie);
            setcookie(trim( $sRawCookie[0] ), '', time() - 10000, '/');
            echo "<li>".$sRawCookie[0]."</li>";
        }
    }
}
echo "</ol>";

echo "<hr>";

function rrmdir($dir, $blKeepTargetDir = false)
{
    $iTotalCleaned = 0;
    foreach (glob($dir . '/*') as $file) {
        if (is_dir($file)) {
            $iTotalCleaned += rrmdir($file);
        } else {
            unlink($file);
            $iTotalCleaned++;
        }
    }
    if (!$blKeepTargetDir) {
        rmdir($dir);
    }
    return $iTotalCleaned;
}

echo "<h2>Cleanup tmp directory</h2>";
echo "<ul>";
$iTotalCleaned = rrmdir( $_cfg->sCompileDir, true );
echo "<li>Total files cleaned: $iTotalCleaned</li>";
echo "</ul>";


echo "<hr>";


echo "<h2>Install and configure database</h2>";
echo "<ol>";
{
    $_key      = $_cfg->sConfigKey;
    $oDB       = mysql_connect( 'localhost', $_cfg->dbUser, $_cfg->dbPwd);

    if ($_cfg->iUtfMode) {
        mysql_query("alter schema character set utf8 collate utf8_general_ci",$oDB);
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

    echo "<li>drop database '".$_cfg->dbName."'</li>";
    mysql_query( 'drop   database '.$_cfg->dbName, $oDB);

    echo "<li>create database '".$_cfg->dbName."'</li>";
    mysql_query( 'create database '.$_cfg->dbName, $oDB);

    echo "<li>select database '".$_cfg->dbName."'</li>";
    mysql_select_db( $_cfg->dbName , $oDB);

    echo "<li>insert 'database.sql'</li>";
    passthru ('mysql -u'.$_cfg->dbUser.' -p'.$_cfg->dbPwd.' '.$_cfg->dbName.' < '.dirname(__FILE__).'/setup/sql'.OXID_VERSION_SUFIX.'/'.'database.sql');

    if ( isset($_GET["test"]) ) {
        echo "<li>insert <b>TEST DATA</b></li>";
        passthru ('mysql -u'.$_cfg->dbUser.' -p'.$_cfg->dbPwd.' '.$_cfg->dbName.' < '.dirname(__FILE__).'/../tests/testsql/testdata'.OXID_VERSION_SUFIX.'.sql');
    } elseif ( isset($_GET["demo"]) ) {
        echo "<li>insert 'demodata.sql'</li>";
        passthru ('mysql -u'.$_cfg->dbUser.' -p'.$_cfg->dbPwd.' '.$_cfg->dbName.' < '.dirname(__FILE__).'/setup/sql'.OXID_VERSION_SUFIX.'/'.'demodata.sql');
    }



    echo "<li>set configuration parameters</li>";
    mysql_query( "delete from oxconfig where oxvarname in ('iSetUtfMode','blLoadDynContents','sShopCountry')", $oDB);
    mysql_query( "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values ".
                 "('config1', '{$sShopId}', 'iSetUtfMode',       'str',  ENCODE('0', '{$_key}') ),".
                 "('config2', '{$sShopId}', 'blLoadDynContents', 'bool', ENCODE('1', '{$_key}') ),".
                 "('config3', '{$sShopId}', 'sShopCountry',      'str',  ENCODE('de','{$_key}') )" , $oDB);

	if (isset($_GET['test'])) {
		if ($_GET['test'] == 1) {
			$sFile = dirname(__FILE__).'/../tests/testsql/testdata'.OXID_VERSION_SUFIX.'.sql';
		} else {
			$sFile = dirname(__FILE__).'/setup/sql'.OXID_VERSION_SUFIX.'/'.'demodata.sql';
		}
		}

    if( !empty($sSerial) ) {

        require_once "core/oxserial.php";

        $oSerial = new oxSerial();
        $oSerial->setEd($iEdition);
        $oSerial->isValidSerial($sSerial);

        echo "<li>add demo serial '{$sSerial}'</li>";

        mysql_query( "update oxshops set oxserial = '{$sSerial}'", $oDB);
        mysql_query( "delete from oxconfig where oxvarname in ('aSerials','sTagList','IMD','IMA','IMS')", $oDB);
        mysql_query( "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values ".
                     "('serial1', '{$sShopId}', 'aSerials', 'arr', ENCODE('". serialize(array($sSerial))         ."','{$_key}') ),".
                     "('serial2', '{$sShopId}', 'sTagList', 'str', ENCODE('". time()                             ."','{$_key}') ),".
                     "('serial3', '{$sShopId}', 'IMD',      'str', ENCODE('". $oSerial->getMaxDays($sSerial)     ."','{$_key}') ),".
                     "('serial4', '{$sShopId}', 'IMA',      'str', ENCODE('". $oSerial->getMaxArticles($sSerial) ."','{$_key}') ),".
                     "('serial5', '{$sShopId}', 'IMS',      'str', ENCODE('". $oSerial->getMaxShops($sSerial)    ."','{$_key}') )" , $oDB);
    }

    if ($_cfg->iUtfMode) {
        echo "<li>convert shop config to utf8</li>";

        $rs = mysql_query("select oxvarname, oxvartype, oxmodule, DECODE( oxvarvalue, '{$_key}') as oxvarvalue from oxconfig where oxvartype in ('str', 'arr', 'aarr') ", $oDB);

        $aCnv =array();
        while ( $aRow = mysql_fetch_assoc($rs) ) {

            if ( $aRow['oxvartype'] == 'arr' || $aRow['oxvartype'] == 'aarr' ) {
                $aRow['oxvarvalue'] = unserialize( $aRow['oxvarvalue'] );
            }
            $aRow['oxvarvalue'] = to_utf8($aRow['oxvarvalue']);
            $aCnv[] = $aRow;
        }

       foreach ( $aCnv as $oCnf ) {
           $_vnm = $oCnf['oxvarname'];
           $_val = $oCnf['oxvarvalue'];
           $_module = $oCnf['oxmodule'];
           if ( is_array($_val) ) {
               $_val = mysql_real_escape_string(serialize($_val),$oDB);
           } elseif(is_string($_val)) {
               $_val = mysql_real_escape_string($_val,$oDB);
           }

           mysql_query("update oxconfig set oxvarvalue = ENCODE( '{$_val}','{$_key}') where oxvarname = '{$_vnm}' and oxmodule = '{$_module}';",$oDB);
       }
    }

}
echo "</ol>";

echo "<hr>",
     "<h3><a target='shp' href='".$_cfg->sShopURL."'>to Shop &raquo; </a></h3>",
     "<h3><a target='adm' href='".$_cfg->sShopURL."/admin/'>to Admin &raquo; </a></h3>";





    function to_utf8($in)
    {
        $out = array();
        if (is_array($in)) {
            foreach ($in as $key => $value) {
                $out[to_utf8($key)] = to_utf8($value);
            }
        } elseif(is_string($in)) {
            return iconv( 'iso-8859-15', 'utf-8', $in );
        } else {
            return $in;
        }
        return $out;
    }
