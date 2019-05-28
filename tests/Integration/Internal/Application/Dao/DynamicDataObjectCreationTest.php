<?php


namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Application\Dao;


use OxidEsales\EshopCommunity\Internal\Review\DataObject\Rating;
use OxidEsales\EshopCommunity\Internal\Review\DataObject\Review;
use PHPUnit\Framework\TestCase;

class DynamicDataObjectCreationTest extends TestCase
{

    public function testWorkingDataObject()
    {
        $dao = new TestDao();
        $dao->addObjectClass(Review::class);
        $dao->addProperty('someproperty', 'string');

        $object = $dao->create();

        $this->assertTrue($object instanceof Review);
    }

    public function testIllegalDataObject()
    {
        $this->expectExceptionMessage("Object class needs to be an instance of DynamicDataObject!");

        $dao = new TestDao();
        $dao->addObjectClass(Rating::class);

    }

}