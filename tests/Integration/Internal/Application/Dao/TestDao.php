<?php


namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Application\Dao;


use OxidEsales\EshopCommunity\Internal\Common\Dao\DynamicDataObjectDao;

class TestDao extends DynamicDataObjectDao
{
    protected $mapper;

    public function __construct()
    {
        $this->mapper = new TestMapper();
    }
}