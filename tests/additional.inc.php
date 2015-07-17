<?php

$serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();
if (OXID_VERSION_PE_CE) {
    $serviceCaller->setParameter('importSql', '@'. __DIR__ .'/Fixtures/testdata.sql');
}
if (OXID_VERSION_PE_PE) {
    $serviceCaller->setParameter('importSql', realpath('../source/Edition/Professional/Tests/Fixtures/testdata.sql'));
}
if (OXID_VERSION_EE) {
    $serviceCaller->setParameter('importSql', realpath('../source/Edition/Enterprise/Tests/Fixtures/testdata.sql'));
}
$serviceCaller->callService('ShopPreparation', 1);

define('oxADMIN_LOGIN', oxDb::getDb()->getOne("select OXUSERNAME from oxuser where oxid='oxdefaultadmin'"));
define('oxADMIN_PASSWD', getenv('oxADMIN_PASSWD') ? getenv('oxADMIN_PASSWD') : 'admin');
