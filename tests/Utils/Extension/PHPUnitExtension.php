<?php


namespace OxidEsales\EshopCommunity\Tests\Utils\Extension;

use OxidEsales\EshopCommunity\Tests\Utils\Traits\DatabaseTrait;
use PHPUnit\Runner\BeforeFirstTestHook;

final class PHPUnitExtension implements BeforeFirstTestHook
{
    use DatabaseTrait;

    public function executeBeforeFirstTest(): void
    {
        $this->setupShopDatabase();
    }
}