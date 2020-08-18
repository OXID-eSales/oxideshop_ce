<?php

if (getenv('TEST_DB_NAME') === false) {
    define('TEST_DB_NAME', 'oxidtest');
}
else {
    define('TEST_DB_NAME', getenv('TEST_DB_NAME'));
}


define('INSTALLATION_ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR . '..');
# Yes, adding a directory separator is stupid, but that's how the code expects it
define('VENDOR_PATH', INSTALLATION_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);
require VENDOR_PATH . DIRECTORY_SEPARATOR . 'autoload.php';

use \Webmozart\PathUtil\Path;

# Yes, adding a directory separator is stupid, but that's how the code expects it
define('OX_BASE_PATH', Path::join(INSTALLATION_ROOT_PATH, 'source') . DIRECTORY_SEPARATOR);
define('OX_LOG_FILE', Path::join(OX_BASE_PATH, 'log', 'testrun.log'));
define('OX_TESTS_PATH', __DIR__);
require Path::join(OX_BASE_PATH, 'oxfunctions.php');
require Path::join(OX_BASE_PATH, 'overridablefunctions.php');

ini_set('session.name', 'sid');
ini_set('session.use_cookies', 0);
ini_set('session.use_trans_sid', 0);
ini_set('url_rewriter.tags', '');

\OxidEsales\EshopCommunity\Tests\TestUtils\Database\TestDatabaseHandler::init();
\OxidEsales\EshopCommunity\Tests\TestUtils\Database\TestDatabaseHandler::setupTestConfigInc();
