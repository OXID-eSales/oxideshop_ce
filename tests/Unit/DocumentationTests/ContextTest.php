<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 19.07.17
 * Time: 15:12
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Refactoring;

use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Core\Registry;

class ContextTests extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /** @var \OxidEsales\EshopCommunity\Internal\Utilities\ContextInterface $context */
    private $context;

    public function setUp()
    {

        /** @var Connection $connection */
        $connection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $this->context = new \OxidEsales\EshopCommunity\Internal\Utilities\Context(Registry::getConfig(), Registry::getLang(), $connection);
    }

    public function testGetShopId()
    {

        $this->assertEquals(1, $this->context->getShopId());
    }

    public function testGetCurrentLanguageId()
    {

        $this->assertEquals(0, $this->context->getCurrentLanguageId());
    }

    public function testGetCurrentLanguageAbbrevitation()
    {

        $this->assertEquals("de", $this->context->getCurrentLanguageAbbrevitation());
    }

    public function testUseTimeCheck()
    {

        $this->setConfigParam('blUseTimeCheck', true);

        $this->assertTrue($this->context->useTimeCheck());

        $this->setConfigParam('blUseTimeCheck', false);

        $this->assertFalse($this->context->useTimeCheck());
    }

    public function testUseStock()
    {

        $this->setConfigParam('blUseStock', true);

        $this->assertTrue($this->context->useStock());

        $this->setConfigParam('blUseStock', false);

        $this->assertFalse($this->context->useStock());
    }

    public function testIsVariantParentBuyable()
    {

        $this->setConfigParam('blVariantParentBuyable', true);

        $this->assertTrue($this->context->isVariantParentBuyable());

        $this->setConfigParam('blVariantParentBuyable', false);

        $this->assertFalse($this->context->isVariantParentBuyable());
    }
}
