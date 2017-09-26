<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 31.08.17
 * Time: 11:11
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Integration\Database;


use OxidEsales\EshopCommunity\Internal\Dao\SelectListDao;
use OxidEsales\EshopCommunity\Internal\Dao\UserDao;
use OxidEsales\EshopCommunity\Internal\Dao\UserDaoInterface;
use OxidEsales\EshopCommunity\Internal\DataObject\SelectListItem;
use OxidEsales\EshopCommunity\Internal\Utilities\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Internal\Unit\ContextStub;
use OxidEsales\EshopCommunity\Tests\Internal\Unit\OxidLegacyServiceStub;

class OxSelectListTest extends AbstractDaoTests
{

    /** @var  SelectListDao $selectListDao */
    private $selectListDao;

    /** @var  ContextStub $context */
    private $context;

    /** @var  OxidLegacyServiceStub $legacyService */
    private $legacyService;

    public function setUp()
    {

        parent::setUp();

        $this->context = new ContextStub();
        $this->legacyService = new OxidLegacyServiceStub();

        $this->selectListDao = new SelectListDao($this->getDoctrineConnection(), $this->context, $this->legacyService);
    }

    public function testGetAbsoluteSelectList() {

        $list = $this->selectListDao->getSelectListForArticle('A1');
        $this->assertEquals(3, sizeof($list));
        $this->assertEquals('Feld3', $list[2]->getFieldKey());
        $this->assertEquals('A1', $list[2]->getArticleId());
        $this->assertEquals(30.0, $list[2]->getPriceDelta());
        $this->assertEquals(SelectListItem::DELTA_TYPE_ABSOLUTE, $list[2]->getDeltaType());
    }

    public function testGetPercentageSelectList() {

        $list = $this->selectListDao->getSelectListForArticle('A2');
        $this->assertEquals(3, sizeof($list));
        $this->assertEquals('Feld3', $list[2]->getFieldKey());
        $this->assertEquals('A2', $list[2]->getArticleId());
        $this->assertEquals(30.0, $list[2]->getPriceDelta());
        $this->assertEquals(SelectListItem::DELTA_TYPE_PERCENT, $list[2]->getDeltaType());
    }

    public function testChangeLanguage() {

        $this->context->setCurrentLanguageAbbrevitation('en');

        $list = $this->selectListDao->getSelectListForArticle('A2');
        $this->assertEquals(3, sizeof($list));
        $this->assertEquals('Field3', $list[2]->getFieldKey());
    }

    public function getFixtureFile()
    {
        return __DIR__ . '/../Fixtures/OxSelectListFixture.xml';
    }
}