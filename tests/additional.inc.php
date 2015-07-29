<?php

$serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();
$serviceCaller->setParameter('importSql', '@'. __DIR__ .'/testsql/testdata' .OXID_VERSION_SUFIX . '.sql');
$serviceCaller->callService('ShopPreparation', 1);

define('oxADMIN_LOGIN', oxDb::getDb()->getOne("select OXUSERNAME from oxuser where oxid='oxdefaultadmin'"));
define('oxADMIN_PASSWD', getenv('oxADMIN_PASSWD') ? getenv('oxADMIN_PASSWD') : 'admin');
