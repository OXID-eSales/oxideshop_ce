<?php
//do not use the default logger because we do not want to create an mock in each test case
//and default logging would produce noise on STDERR and in log files
\OxidEsales\Eshop\Core\Registry::set('Logger',new \Psr\Log\NullLogger());

$serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();
$testConfig = new \OxidEsales\TestingLibrary\TestConfig();

if ($testConfig->getShopEdition() === 'CE') {
    $serviceCaller->setParameter('importSql', '@'. __DIR__ .'/Fixtures/testdata.sql');
}
if ($testConfig->getShopEdition() === 'PE') {
    $serviceCaller->setParameter('importSql', '@' . $testConfig->getVendorDirectory() . '/oxid-esales/oxideshop-pe/Tests/Fixtures/testdata.sql');
}
if ($testConfig->getShopEdition() === 'EE') {
    $serviceCaller->setParameter('importSql', '@' . $testConfig->getVendorDirectory() . '/oxid-esales/oxideshop-ee/Tests/Fixtures/testdata.sql');
}
$serviceCaller->callService('ShopPreparation', 1);

define('oxADMIN_LOGIN', oxDb::getDb()->getOne("select OXUSERNAME from oxuser where oxid='oxdefaultadmin'"));
define('oxADMIN_PASSWD', getenv('oxADMIN_PASSWD') ? getenv('oxADMIN_PASSWD') : 'admin');
