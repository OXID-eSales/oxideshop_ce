<?php

$serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();
$testConfig = new \OxidEsales\TestingLibrary\TestConfig();

if (OXID_VERSION_PE_CE) {
    $serviceCaller->setParameter('importSql', '@'. __DIR__ .'/Fixtures/testdata.sql');
}
if (OXID_VERSION_PE_PE) {
    $serviceCaller->setParameter('importSql', '@' . $testConfig->getShopPath() . '/Edition/Professional/Tests/Fixtures/testdata.sql');
}
if (OXID_VERSION_EE) {
    $serviceCaller->setParameter('importSql', '@' . $testConfig->getShopPath() . '/Edition/Enterprise/Tests/Fixtures/testdata.sql');
}
$serviceCaller->callService('ShopPreparation', 1);

define('oxADMIN_LOGIN', oxDb::getDb()->getOne("select OXUSERNAME from oxuser where oxid='oxdefaultadmin'"));
define('oxADMIN_PASSWD', getenv('oxADMIN_PASSWD') ? getenv('oxADMIN_PASSWD') : 'admin');
