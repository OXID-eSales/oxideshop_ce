<?php


namespace OxidEsales\EshopCommunity\Tests;

use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Runner\BeforeFirstTestHook;

final class PHPUnitExtension implements BeforeFirstTestHook
{
    use DatabaseTrait;

    public function executeBeforeFirstTest(): void
    {
        $this->setupShopDatabase();
    }
}