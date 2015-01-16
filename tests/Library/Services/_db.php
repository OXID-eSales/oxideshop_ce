<?php
/**
 * File for shop installation during test suite run.
 */

error_reporting((E_ALL ^ E_NOTICE) | E_STRICT);
ini_set('display_errors', true);

require_once 'Library/ShopInstaller.php';

$oShopInstaller = new ShopInstaller();

$sShopTestingSerial = array_key_exists('serial', $_REQUEST)? $_REQUEST['serial'] : false;
$blAddDemoData = array_key_exists('addDemoData', $_REQUEST) ? $_REQUEST['addDemoData'] : true;
$blInternationalShop = array_key_exists('international', $_REQUEST) ? $_REQUEST['international'] : false;
$blTurnOnVarnish = (bool)$oShopInstaller->turnOnVarnish || $_REQUEST['RP'] || $_REQUEST['turnOnVarnish'];
$sTestSqlLocalFile = array_key_exists('importSql', $_REQUEST) ? $_REQUEST['importSql'] : false;
$sTestSqlRemoteFile = array_key_exists('importSql', $_FILES) ? $_FILES['importSql'] : false;
$sSetupPath = array_key_exists('setupPath', $_REQUEST) ? $_REQUEST['setupPath'] : null;

if ($sSetupPath) {
    $oShopInstaller->setSetupDirectory($sSetupPath);
}

if ($sTestSqlRemoteFile) {
    include_once 'Library/FileUploader.php';
    $oFileUploader = new FileUploader();
    $sTestSqlLocalFile = 'temp/import.sql';
    $oFileUploader->uploadFile('importSql', $sTestSqlLocalFile);
}
if (!$sTestSqlLocalFile && $_REQUEST['test']) {
    $blAddDemoData = false;
    $sTestSqlLocalFile = '../../tests/testsql/testdata'.OXID_VERSION_SUFIX.'.sql';
}


?>

<h1>Full reinstall of OXID eShop</h1>

<ol>
    <li>drop and recreate database: <?=$oShopInstaller->dbName?> <?php $oShopInstaller->setupDatabase(); ?></li>
    <?php if ($blAddDemoData) : ?>
        <li>Insert demo data <?php $oShopInstaller->insertDemoData()?></li>
    <?php endif; ?>
    <?php if ($blInternationalShop) : ?>
        <li>Convert shop to International <?php $oShopInstaller->convertToInternational();?></li>
    <?php endif; ?>
    <?php if ($sTestSqlLocalFile) : ?>
        <li>Insert test data <?php $oShopInstaller->importFileToDatabase($sTestSqlLocalFile)?></li>
    <?php endif; ?>
    <li>Add configuration options <?php $oShopInstaller->setConfigurationParameters();?></li>
    <?php if ($sShopTestingSerial) : ?>
        <li>Set serial number to: <?=$sShopTestingSerial?><?php $oShopInstaller->setSerialNumber($sShopTestingSerial);?></li>
    <?php endif; ?>
    <?php if ($oShopInstaller->iUtfMode) : ?>
        <li>Convert shop to UTF8 <?php $oShopInstaller->convertToUtf();?></li>
    <?php endif; ?>
    <?php if ($blTurnOnVarnish) : ?>
        <li>Turn on varnish <?php $oShopInstaller->turnVarnishOn();?></li>
    <?php endif; ?>
    <li>Delete cookies: <?php implode(', ', $oShopInstaller->deleteCookies()); ?></li>
    <li>Clear temp directory: <?=$oShopInstaller->sCompileDir?> <?php $oShopInstaller->clearTemp(); ?></li>
</ol>

<h3><a target='shp' href='<?=$oShopInstaller->sShopURL?>'>to Shop &raquo; </a></h3>
<h3><a target='adm' href='<?=$oShopInstaller->sShopURL?>/admin/'>to Admin &raquo; </a></h3>