<?php

$serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();
$testConfig = new \OxidEsales\TestingLibrary\TestConfig();

$testDirectory = $testConfig->getEditionTestsPath($testConfig->getShopEdition());
$serviceCaller->setParameter('importSql', '@' . $testDirectory . '/Fixtures/testdata.sql');
$serviceCaller->callService('ShopPreparation', 1);

define('oxADMIN_LOGIN', oxDb::getDb()->getOne("select OXUSERNAME from oxuser where oxid='oxdefaultadmin'"));
define('oxADMIN_PASSWD', getenv('oxADMIN_PASSWD') ? getenv('oxADMIN_PASSWD') : 'admin');
