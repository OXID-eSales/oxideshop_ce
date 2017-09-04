<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Seo;

use oxBase;
use oxDb;
use oxField;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use oxRegistry;
use oxSeoEncoder;

/**
 * Class TestFrontendController, it is a test helper.
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Seo
 */
class TestFrontendController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    protected $seoEntries = [];
    protected $categoryOxid;

    /**
     * Getter for seo entries as present directly after call to FrontendController::_processRequest().
     *
     * @return array
     */
    public function getSeoEntries()
    {
        return $this->seoEntries;
    }

    /**
     * Setter for category oxid.
     * @param string $categoryOxid
     */
    public function setCategoryOxid($categoryOxid)
    {
        $this->categoryOxid = $categoryOxid;
    }

    public function init()
    {
        $this->_processRequest();

        $query = "SELECT oxstdurl, oxseourl FROM `oxseo` WHERE `OXSTDURL` like '%" . $this->categoryOxid . "%'" .
                 " AND oxtype = 'oxcategory'";
        $this->seoEntries = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll($query);
    }
}

/**
 * Class PaginationSeoTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Seo
 */
class PaginationSeoTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /** @var string Original theme */
    private $origTheme;

    /**
     * @var string
     */
    private $seoUrl = '';

    /**
     * @var string
     */
    private $categoryOxid = '';

    /**
     * Sets up test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->origTheme = $this->getConfig()->getConfigParam('sTheme');
        $query = "UPDATE `oxconfig` SET `OXVARVALUE` = encode('azure', 'fq45QS09_fqyx09239QQ') WHERE `OXVARNAME` = 'sTheme'";
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);

        $this->getConfig()->saveShopConfVar('bool', 'blEnableSeoCache', false);

        $this->cleanRegistry();
        $this->cleanSeoTable();

        $facts = new \OxidEsales\Facts\Facts;
        $this->seoUrl = ('EE' == $facts->getEdition()) ? 'Party/Bar-Equipment/' : 'Geschenke/';
        $this->categoryOxid = ('EE' == $facts->getEdition()) ? '30e44ab8593023055.23928895' : '8a142c3e4143562a5.46426637';
    }

    /**
     * Tear down test.
     */
    protected function tearDown()
    {
        $this->cleanRegistry();
        $this->cleanSeoTable();

        parent::tearDown();
    }

    /**
     * Call with a seo url that is not yet stored in oxseo table.
     * Category etc exists.
     * We hit the 404 as no entry for this url exists in oxseo table.
     * But when shop shows the 404 page, it creates the main category seo urls.
     */
    public function testCallWithSeoUrlNoEntryInTableExists()
    {
        $seoUrl = $this->seoUrl;
        $checkResponse = '404 Not Found';

        //before
        $query = "SELECT oxstdurl, oxseourl FROM `oxseo` WHERE `OXSEOURL` like '%" . $seoUrl . "%'" .
                 " AND oxtype = 'oxcategory'";
        $res = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll($query);
        $this->assertEmpty($res);

        //check what shop does
        $response = $this->callCurl($seoUrl);
        $this->assertContains($checkResponse, $response, "Should get $checkResponse");
    }

    /**
     * Call with a standard url that is not yet stored in oxseo table.
     * Category etc exists. No pgNr is provided.
     *
     *
     */
    public function testCallWithStdUrlNoEntryExists()
    {
        $urlToCall = 'index.php?cl=alist&cnid=' . $this->categoryOxid;
        $checkResponse = 'HTTP/1.1 200 OK';

        //Check entries in oxseo table for oxtype = 'oxcategory'
        $query = "SELECT oxstdurl, oxseourl FROM `oxseo` WHERE `OXSTDURL` like '%" . $this->categoryOxid . "%'" .
                 " AND oxtype = 'oxcategory'";
        $res = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll($query);
        $this->assertEmpty($res);

        $response = $this->callCurl($urlToCall);

        $this->assertContains($checkResponse, $response, "Should get $checkResponse");
    }

    /**
     * Call shop with standard url, no matching entry in seo table exists atm.
     */
    public function testStandardUrlNoMatchingSeoUrlSavedYet()
    {
        $requestUrl = 'index.php?cl=alist&amp;cnid=' . $this->categoryOxid;

        //No match in oxseo table
        $redirectUrl = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->fetchSeoUrl($requestUrl);
        $this->assertFalse($redirectUrl);

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('redirect'));
        $utils->expects($this->never())->method('redirect');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $request = $this->getMock(\OxidEsales\Eshop\Core\Request::class, array('getRequestUrl'));
        $request->expects($this->any())->method('getRequestUrl')->will($this->returnValue($requestUrl));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Request::class, $request);

        $controller = oxNew(\OxidEsales\EshopCommunity\Tests\Integration\Seo\TestFrontendController::class);
        $controller->setCategoryOxid($this->categoryOxid);
        $this->assertEquals(VIEW_INDEXSTATE_INDEX, $controller->noIndex());

        $controller->init();

        $this->assertEquals(VIEW_INDEXSTATE_NOINDEXFOLLOW, $controller->noIndex());
        $this->assertEmpty($controller->getSeoEntries());
    }

    /**
     * Call shop with standard url, a matching entry in seo table already exists.
     */
    public function testStandardUrlMatchingSeoUrlAvailable()
    {
        $this->callCurl(''); //call shop start page to have all seo urls for main categories generated

        $requestUrl = 'index.php?cl=alist&amp;cnid=' . $this->categoryOxid;

        $shopUrl = $this->getConfig()->getCurrentShopUrl();
        $redirectUrl = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->fetchSeoUrl($requestUrl);
        $this->assertEquals($this->seoUrl, $redirectUrl);

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('redirect'));
        $utils->expects($this->once())->method('redirect')
            ->with($this->equalTo($shopUrl . $redirectUrl),
                $this->equalTo(false), $this->equalTo('301'));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $request = $this->getMock(\OxidEsales\Eshop\Core\Request::class, array('getRequestUrl'));
        $request->expects($this->any())->method('getRequestUrl')->will($this->returnValue($requestUrl));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Request::class, $request);

        $controller = oxNew(\OxidEsales\EshopCommunity\Tests\Integration\Seo\TestFrontendController::class);
        $controller->setCategoryOxid($this->categoryOxid);
        $this->assertEquals(VIEW_INDEXSTATE_INDEX, $controller->noIndex());

        $controller->init();

        $this->assertEquals(VIEW_INDEXSTATE_INDEX, $controller->noIndex());
        $this->assertEquals(1, count($controller->getSeoEntries()));
    }


    /**
     * Call shop with standard url and append pgNr request parameter.
     * Blank seo url already is stored in oxseo table.
     * Case that pgNr should exist as there are sufficient items in that category.
     */
    public function testSeoUrlWithPgNr()
    {
        $this->callCurl(''); //call shop start page to have all seo urls for main categories generated

        $requestUrl = 'index.php?cl=alist&amp;cnid=' . $this->categoryOxid . '&amp;pgNr=2';

        $redirectUrl = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->fetchSeoUrl($requestUrl);
        $this->assertFalse($redirectUrl);

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('redirect'));
        $utils->expects($this->never())->method('redirect');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $request = $this->getMock(\OxidEsales\Eshop\Core\Request::class, array('getRequestUrl'));
        $request->expects($this->any())->method('getRequestUrl')->will($this->returnValue($requestUrl));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Request::class, $request);

        $controller = oxNew(\OxidEsales\EshopCommunity\Tests\Integration\Seo\TestFrontendController::class);
        $this->assertEquals(VIEW_INDEXSTATE_INDEX, $controller->noIndex());

        $controller->init();

        $this->assertEquals(VIEW_INDEXSTATE_NOINDEXFOLLOW, $controller->noIndex());
    }

    /**
     * Test paginated entries. Call with standard url with appended pgNr parameter.
     */
    public function testExistingPaginatedSeoEntries()
    {
        $this->callCurl(''); //call shop start page to have all seo urls for main categories generated
        $this->callCurl($this->seoUrl);       //call shop seo page, this will create all paginated pages

        $this->assertGeneratedPages();

        //Call with standard url that has a pgNr parameter attached, paginated seo url will be found in this case.
        $shopUrl = $this->getConfig()->getCurrentShopUrl();
        $requestUrl = 'index.php?cl=alist&amp;cnid=' . $this->categoryOxid . '&amp;pgNr=1';
        $redirectUrl = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->fetchSeoUrl($requestUrl);
        $this->assertEquals($this->seoUrl . '2/', $redirectUrl);

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('redirect'));
        $utils->expects($this->once())->method('redirect')
            ->with($this->equalTo($shopUrl . $redirectUrl),
                $this->equalTo(false), $this->equalTo('301'));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $request = $this->getMock(\OxidEsales\Eshop\Core\Request::class, array('getRequestUrl'));
        $request->expects($this->any())->method('getRequestUrl')->will($this->returnValue($requestUrl));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Request::class, $request);

        $controller = oxNew(\OxidEsales\EshopCommunity\Tests\Integration\Seo\TestFrontendController::class);
        $this->assertEquals(VIEW_INDEXSTATE_INDEX, $controller->noIndex());

        $controller->init();
        $this->assertEquals(VIEW_INDEXSTATE_INDEX, $controller->noIndex());
    }

    /**
     * Test paginated entries. Call with standard url with appended pgNr parameter.
     * Dofference to test case before: call with ot existing page Nr.
     */
    public function testNotExistingPaginatedSeoEntries()
    {
        $this->callCurl(''); //call shop start page to have all seo urls for main categories generated
        $this->callCurl($this->seoUrl);       //call shop seo page, this will create all paginated pages

        $this->assertGeneratedPages();

        //Call with standard url that has a pgNr parameter attached, paginated seo url will be found in this case.
        $requestUrl = 'index.php?cl=alist&amp;cnid=' . $this->categoryOxid . '&amp;pgNr=20';

        $redirectUrl = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->fetchSeoUrl($requestUrl);
        $this->assertFalse($redirectUrl); //no match found, and we will end up with VIEW_INDEXSTATE_NOINDEXFOLLOW
    }
    
    public function providerTestDecodeNewSeoUrl()
    {
        $facts = new \OxidEsales\Facts\Facts;
        $this->seoUrl = ('EE' == $facts->getEdition()) ? 'Party/Bar-Equipment/' : 'Geschenke/';
        $this->categoryOxid = ('EE' == $facts->getEdition()) ? '30e44ab8593023055.23928895' : '8a142c3e4143562a5.46426637';

        $data = [];

        $data['plain_seo_url'] = ['params'   => $this->seoUrl,
                                  'expected' => ['cl'   => 'alist',
                                                 'cnid' => $this->categoryOxid,
                                                 'lang' => '0']
        ];

        $data['paginated_page'] = ['params'   => $this->seoUrl . '2/',
                                   'expected' => ['cl'   => 'alist',
                                                  'cnid' => $this->categoryOxid,
                                                  'pgNr' => '1',
                                                  'lang' => '0']
        ];

        return $data;
    }

    /**
     * Test decoding seo calls.
     *
     * @param string $params
     * @param array  $expected
     *
     * @dataProvider providerTestDecodeNewSeoUrl
     */
    public function testDecodeNewSeoUrl($params, $expected)
    {
        $this->callCurl(''); //call shop start page to have all seo urls for main categories generated
        $this->callCurl($this->seoUrl);      //call shop seo page, this will create all paginated pages

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('redirect'));
        $utils->expects($this->never())->method('redirect');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $seoDecoder = oxNew(\OxidEsales\Eshop\Core\SeoDecoder::class);

        $decoded = $seoDecoder->decodeUrl($params);  //decoded new url
        $this->assertEquals($expected, $decoded);
    }

    public function providerTestProcessingSeoCallNewSeoUrl()
    {
        $facts = new \OxidEsales\Facts\Facts;
        $this->seoUrl = ('EE' == $facts->getEdition()) ? 'Party/Bar-Equipment/' : 'Geschenke/';
        $this->categoryOxid = ('EE' == $facts->getEdition()) ? '30e44ab8593023055.23928895' : '8a142c3e4143562a5.46426637';

        $data = [];

        $data['plain_seo_url'] = ['request'  => $this->seoUrl,
                                  'get'      => [],
                                  'expected' => ['cl'   => 'alist',
                                                 'cnid' => $this->categoryOxid,
                                                 'lang' => '0']
        ];

        $data['paginated_page'] = ['request'  => $this->seoUrl . '2/',
                                   'get'      => [],
                                   'expected' => ['cl'   => 'alist',
                                                  'cnid' => $this->categoryOxid,
                                                  'pgNr' => '1',
                                                  'lang' => '0']
        ];

        $data['pgnr_as_get'] = ['params'   => $this->seoUrl . '?pgNr=2',
                                'get'      => ['pgNr' => '2'],
                                'expected' => ['cl'   => 'alist',
                                               'cnid' => $this->categoryOxid,
                                               'lang' => '0',
                                               'pgNr' => '2']
        ];

        return $data;
    }

    /**
     * Test decoding seo calls.
     * When calling with paginated pages, the paginated seo url must already be stored
     * in oxseo table.
     *
     * @param string $params
     * @param array  $get
     * @param array  $expected
     *
     * @dataProvider providerTestProcessingSeoCallNewSeoUrl
     */
    public function testProcessingSeoCallNewSeoUrl($request, $get, $expected)
    {
        $this->callCurl(''); //call shop start page to have all seo urls for main categories generated
        $this->callCurl($this->seoUrl);      //call shop seo page, this will create all paginated pages

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('redirect'));
        $utils->expects($this->never())->method('redirect');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $seoDecoder = oxNew(\OxidEsales\Eshop\Core\SeoDecoder::class);
        $_GET = $get;

        $seoDecoder->processSeoCall($request, '/');
        $this->assertEquals($expected, $_GET);
    }

    /**
     * Clean oxseo for testing.
     */
    private function cleanSeoTable()
    {
        $query = "DELETE FROM oxseo WHERE oxtype in ('oxcategory', 'oxarticle')";
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);
    }

    /**
     * Ensure that whatever mocks were added are removed from Registry.
     */
    private function cleanRegistry()
    {
        $seoEncoder = oxNew(\OxidEsales\Eshop\Core\SeoEncoder::class);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\SeoEncoder::class, $seoEncoder);

        $seoDecoder = oxNew(\OxidEsales\Eshop\Core\SeoDecoder::class);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\SeoDecoder::class, $seoDecoder);

        $utils = oxNew(\OxidEsales\Eshop\Core\Utils::class);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $request = oxNew(\OxidEsales\Eshop\Core\Request::class);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Request::class, $request);
    }

    /**
     * @param string $fileUrlPart Shop url part to call.
     *
     * @return string
     */
    private function callCurl($fileUrlPart)
    {
        $url = $this->getConfig()->getShopMainUrl() . $fileUrlPart;

        $curl = oxNew(\OxidEsales\Eshop\Core\Curl::class);
        $curl->setOption('CURLOPT_HEADER', true);
        $curl->setUrl($url);

        return $curl->execute();
    }

    /**
     * Test helper to check for paginated seo pages.
     */
    private function assertGeneratedPages()
    {
        $facts = new \OxidEsales\Facts\Facts;
        $expected = ('EE' == $facts->getEdition()) ? 2 : 4;

        $query = "SELECT oxstdurl, oxseourl FROM `oxseo` WHERE `OXSEOURL` LIKE '{$this->seoUrl}'" .
                 " OR `OXSEOURL` LIKE '{$this->seoUrl}1/'" .
                 " OR `OXSEOURL` LIKE '{$this->seoUrl}2/'" .
                 " OR `OXSEOURL` LIKE '{$this->seoUrl}3/'" .
                 " OR `OXSEOURL` LIKE '{$this->seoUrl}4/'" .
                 " OR `OXSEOURL` LIKE '{$this->seoUrl}5/'" .
                 " AND oxtype = 'oxcategory'";
        $res = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll($query);
        $this->assertEquals($expected, count($res));
    }
}