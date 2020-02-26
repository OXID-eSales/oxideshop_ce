<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/tests/bootstrap.php';

class TestSetup {
    use \OxidEsales\EshopCommunity\Tests\TestUtils\Traits\SetupTrait;

    public function setup()
    {
        $this->backupConfigInc();
        $this->configureTestDatabaseInConfigInc();
        $this->initializeDatabase();
        $this->createViews();
    }

    public function restore()
    {
        $this->restoreConfigInc();
    }

}

$setup = new TestSetup();

switch ($argv[1]) {
    case 'setup': $setup->setup(); break;
    case 'restore': $setup->restore(); break;
    default: throw new Exception('Unknown command');
}
