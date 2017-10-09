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

    /**
     * @dataProvider dataProvider
     */
    public function testGetAbsoluteSelectList($articleId, $language, $startprice, $selections, $result, $message) {

        $this->context->setCurrentLanguageAbbrevitation($language);

        $list = $this->selectListDao->getSelectListForArticle($articleId);
        $this->assertEquals($result, $list->modifyPriceForSelection($startprice, $selections), $message);
    }

    public function dataProvider() {
        return [
            // Reduce by 20 absolute
            ['articleId' => 'A1', 'language' => 'de', 'startprice' => 40, 'selections' => [1], 20,
             'message' => 'Reducing absolute is not working.'],
            // This really is a data error - the english reduction is 10 absolute, not 20
            // But that's how the code is supposed to work
            ['articleId' => 'A1', 'language' => 'en', 'startprice' => 40, 'selections' => [1], 30,
             'message' => 'Second language is not working.'],
            // Reduce by 20 absolute, then 10%
            ['articleId' => 'A2', 'language' => 'de', 'startprice' => 40, 'selections' => [1, 0], 18,
             'message' => 'Absolute / percentage combination not working.'],
            // Like A2, but with reversed sorting - first 10% , then 20 absolute
            ['articleId' => 'A3', 'language' => 'de', 'startprice' => 40, 'selections' => [0, 1], 16,
             'message' => 'Percent / absolute combination not working.'],
            // Use a child of A2 without direct selections - should be like A2
            ['articleId' => 'A2_CHILD', 'language' => 'de', 'startprice' => 40, 'selections' => [1, 0], 18,
             'message' => 'Parent select list not working.'],
            ['articleId' => 'A4', 'language' => 'de', 'startprice' => 40, 'selections' => [1], 40,
             'message' => 'Missing price tag not working.'],
            // Article without select lists
            ['articleId' => 'A5', 'language' => 'de', 'startprice' => 40, 'selections' => [], 40,
             'message' => 'Empty select list not working.']
        ];
    }

    public function getFixtureFile()
    {
        return __DIR__ . '/../Fixtures/OxSelectListFixture.xml';
    }
}