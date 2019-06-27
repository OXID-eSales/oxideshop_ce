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

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\Object2Category;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Field;

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
        $this->activateTheme('azure');

        $this->getConfig()->saveShopConfVar('bool', 'blEnableSeoCache', false);

        $this->cleanRegistry();
        $this->cleanSeoTable();

        $facts = new \OxidEsales\Facts\Facts;
        $this->seoUrl = ('EE' == $facts->getEdition()) ? 'Party/Bar-Equipment/' : 'Geschenke/';
        $this->categoryOxid = ('EE' == $facts->getEdition()) ? '30e44ab8593023055.23928895' : '8a142c3e4143562a5.46426637';
        oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class)->renewPriceUpdateTime();
        oxNew(\OxidEsales\Eshop\Core\SystemEventHandler::class)->onShopEnd();
    }

    /**
     * Tear down test.
     */
    protected function tearDown()
    {
        //restore theme, do it directly in database as it might be dummy 'basic' theme
        $query = "UPDATE `oxconfig` SET `OXVARVALUE` = encode('" . $this->origTheme . "', 'fq45QS09_fqyx09239QQ') WHERE `OXVARNAME` = 'sTheme'";
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);

        $this->cleanRegistry();
        $this->cleanSeoTable();
        $_GET = [];

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
        $this->callCurl('');
        $this->callCurl($this->seoUrl);

        $this->cleanRegistry();
        $this->cleanSeoTable();
        $this->clearProxyCache();

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

        $controller = oxNew(\OxidEsales\Eshop\Application\Controller\FrontendController::class);
        $this->assertEquals(VIEW_INDEXSTATE_INDEX, $controller->noIndex());

        $controller->init();

        $this->assertEquals(VIEW_INDEXSTATE_NOINDEXFOLLOW, $controller->noIndex());
        $this->assertEmpty($this->getCategorySeoEntries());
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
            ->with(
                $this->equalTo($shopUrl . $redirectUrl),
                $this->equalTo(false),
                $this->equalTo('301')
            );
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $request = $this->getMock(\OxidEsales\Eshop\Core\Request::class, array('getRequestUrl'));
        $request->expects($this->any())->method('getRequestUrl')->will($this->returnValue($requestUrl));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Request::class, $request);

        $controller = oxNew(\OxidEsales\Eshop\Application\Controller\FrontendController::class);
        $this->assertEquals(VIEW_INDEXSTATE_INDEX, $controller->noIndex());

        $controller->init();

        $this->assertEquals(VIEW_INDEXSTATE_INDEX, $controller->noIndex());
        $this->assertEquals(1, count($this->getCategorySeoEntries()));
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

        //base seo url is found in database and page part appended
        $shopUrl = $this->getConfig()->getCurrentShopUrl();
        $redirectUrl = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->fetchSeoUrl($requestUrl);
        $this->assertEquals($this->seoUrl . '?pgNr=2', $redirectUrl);

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('redirect'));
        $utils->expects($this->once())->method('redirect')
            ->with(
                $this->equalTo($shopUrl . $redirectUrl),
                $this->equalTo(false),
                $this->equalTo('301')
            );
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $request = $this->getMock(\OxidEsales\Eshop\Core\Request::class, array('getRequestUrl'));
        $request->expects($this->any())->method('getRequestUrl')->will($this->returnValue($requestUrl));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Request::class, $request);

        $controller = oxNew(\OxidEsales\Eshop\Application\Controller\FrontendController::class);
        $this->assertEquals(VIEW_INDEXSTATE_INDEX, $controller->noIndex());

        $controller->init();

        $this->assertEquals(VIEW_INDEXSTATE_INDEX, $controller->noIndex());
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
        $this->assertEquals($this->seoUrl . '?pgNr=1', $redirectUrl);

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('redirect'));
        $utils->expects($this->once())->method('redirect')
            ->with(
                $this->equalTo($shopUrl . $redirectUrl),
                $this->equalTo(false),
                $this->equalTo('301')
            );
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $request = $this->getMock(\OxidEsales\Eshop\Core\Request::class, array('getRequestUrl'));
        $request->expects($this->any())->method('getRequestUrl')->will($this->returnValue($requestUrl));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Request::class, $request);

        $controller = oxNew(\OxidEsales\Eshop\Application\Controller\FrontendController::class);
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
        $this->callCurl('');  //call shop start page to have all seo urls for main categories generated
        $this->callCurl($this->seoUrl); //call shop seo page, this will create all paginated pages

        $this->assertGeneratedPages();

        //Call with standard url that has a pgNr parameter attached, paginated seo url will be found in this case.
        $requestUrl = 'index.php?cl=alist&amp;cnid=' . $this->categoryOxid . '&amp;pgNr=20';

        //The paginated page url is created on the fly. This seo page would not contain any data.
        $redirectUrl = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->fetchSeoUrl($requestUrl);
        $this->assertEquals($this->seoUrl . '?pgNr=20', $redirectUrl);
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

        $data['old_style_paginated_page'] = ['params'   => $this->seoUrl . '2/',
                                             'expected' => ['cl'   => 'alist',
                                                            'cnid' => $this->categoryOxid,
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
        $this->callCurl('');   //call shop start page to have all seo urls for main categories generated
        $this->callCurl($this->seoUrl);  //call shop seo page, this will create all paginated pages

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
                                   'get'      => ['pgNr' => '1'],
                                   'expected' => ['cl'   => 'alist',
                                                  'cnid' => $this->categoryOxid,
                                                  'lang' => '0',
                                                  'pgNr' => '1']
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
     * Test decoding seo calls. Call shop with seo main page plus pgNr parameter.
     * No additional paginated pages are stored in oxseo table. pgNr parameter
     * come in via GET and all other needed parameters for processing the call
     * are extracted via decodeUrl.
     * To be able to correctly decode an url like 'Geschenke/2/' without having
     * a matching entry stored in oxseo table, we need to parse this url into
     * 'Geschenke/' plus pgNr parameter.
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

        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('redirect'));
        $utils->expects($this->never())->method('redirect');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $seoDecoder = oxNew(\OxidEsales\Eshop\Core\SeoDecoder::class);
        $_GET = $get;

        $seoDecoder->processSeoCall($request, '/');

        $this->assertEquals($expected, $_GET);
        $this->assertGeneratedPages();
    }

    /**
     * Test SeoEncoderCategory::getCategoryPageUrl()
     */
    public function testGetCategoryPageUrl()
    {
        $shopUrl = $this->getConfig()->getCurrentShopUrl();
        $category = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        $category->load($this->categoryOxid);

        $seoEncoderCategory = oxNew(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class);

        $result = $seoEncoderCategory->getCategoryPageUrl($category, 2);

        $this->assertEquals($shopUrl . $this->seoUrl . '?pgNr=2', $result);

        //unpaginated seo url is now stored in database and should not be saved again.
        $seoEncoderCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class, ['_saveToDb']);
        $seoEncoderCategory->expects($this->never())->method('_saveToDb');

        $seoEncoderCategory->getCategoryPageUrl($category, 0);
        $seoEncoderCategory->getCategoryPageUrl($category, 1);
        $seoEncoderCategory->getCategoryPageUrl($category, 2);
    }

    /**
     * Test SeoEncoderVendor::getVendorPageUrl()
     */
    public function testGetVendorPageUrl()
    {
        $facts = new \OxidEsales\Facts\Facts;
        if ('EE' != $facts->getEdition()) {
            $this->markTestSkipped('missing testdata');
        }

        $shopUrl = $this->getConfig()->getCurrentShopUrl();
        $vendorOxid = 'd2e44d9b31fcce448.08890330';
        $seoUrl = 'Nach-Lieferant/Hersteller-1/';

        $vendor = oxNew(\OxidEsales\Eshop\Application\Model\Vendor::class);
        $vendor->load($vendorOxid);

        $seoEncoderVendor = oxNew(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class);
        $result = $seoEncoderVendor->getVendorPageUrl($vendor, 2);

        $this->assertEquals($shopUrl . $seoUrl . '?pgNr=2', $result);

        //unpaginated seo url is now stored in database and should not be saved again.
        $seoEncoderVendor = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderVendor::class, ['_saveToDb']);
        $seoEncoderVendor->expects($this->never())->method('_saveToDb');

        $seoEncoderVendor->getVendorPageUrl($vendor, 0);
        $seoEncoderVendor->getVendorPageUrl($vendor, 1);
        $seoEncoderVendor->getVendorPageUrl($vendor, 2);
    }

    /**
     * Test SeoEncoderManufacturer::getManufacturerPageUrl()
     */
    public function testGetManufacturerPageUrl()
    {
        $facts = new \OxidEsales\Facts\Facts;
        if ('EE' != $facts->getEdition()) {
            $this->markTestSkipped('missing testdata');
        }

        $languageId = 1; //en
        $shopUrl = $this->getConfig()->getCurrentShopUrl();
        $manufacturerOxid = '2536d76675ebe5cb777411914a2fc8fb';
        $seoUrl = 'en/By-manufacturer/Manufacturer-2/';

        $manufacturer = oxNew(\OxidEsales\Eshop\Application\Model\Manufacturer::class);
        $manufacturer->load($manufacturerOxid);

        $seoEncoderManufacturer = oxNew(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class);
        $result = $seoEncoderManufacturer->getManufacturerPageUrl($manufacturer, 2, $languageId);

        $this->assertEquals($shopUrl . $seoUrl . '?pgNr=2', $result);
    }

    /**
     * Test SeoEncoderRecomm::getRecommPageUrl()
     */
    public function testGetRecommPageUrl()
    {
        $shopUrl = $this->getConfig()->getCurrentShopUrl();
        $seoUrl = 'testTitle/';

        $recomm = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, ['getId', 'getBaseStdLink']);
        $recomm->expects($this->any())->method('getId')->will($this->returnValue('testRecommId'));
        $recomm->expects($this->any())->method('getBaseStdLink')->will($this->returnValue('testStdLink'));
        $recomm->oxrecommlists__oxtitle = new \OxidEsales\Eshop\Core\Field('testTitle');

        $seoEncoderRecomm = oxNew(\OxidEsales\Eshop\Application\Model\SeoEncoderRecomm::class);
        $result = $seoEncoderRecomm->getRecommPageUrl($recomm, 2);

        $this->assertEquals($shopUrl . $seoUrl . '?pgNr=2', $result);

        //unpaginated seo url is now stored in database and should not be saved again.
        $seoEncoderRecomm = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderRecomm::class, ['_saveToDb']);
        $seoEncoderRecomm->expects($this->never())->method('_saveToDb');

        $seoEncoderRecomm->getRecommPageUrl($recomm, 0);
        $seoEncoderRecomm->getRecommPageUrl($recomm, 1);
        $seoEncoderRecomm->getRecommPageUrl($recomm, 2);
    }

    public function providerCheckSeoUrl()
    {
        $facts = new \OxidEsales\Facts\Facts;
        $oxidLiving = ('EE' != $facts->getEdition()) ? '8a142c3e44ea4e714.31136811' : '30e44ab83b6e585c9.63147165';

        $data = [
            ['Eco-Fashion/', ['HTTP/1.1 200 OK'], ['ROBOTS'], []],
            ['Eco-Fashion/3/', ['404 Not Found'], [], ['Eco-Fashion/']],
            ['Eco-Fashion/?pgNr=0', ['HTTP/1.1 200 OK'], ['ROBOTS', 'Location'], []],
            ['Eco-Fashion/?pgNr=34', ['404 Not Found'],[], []],
            ['index.php?cl=alist&cnid=oxmore', ['HTTP/1.1 200 OK'], ['Location'], []],
            ['index.php?cl=alist&cnid=oxmore&pgNr=0', ['HTTP/1.1 200 OK'], ['Location'], []],
            ['index.php?cl=alist&cnid=oxmore&pgNr=10', ['HTTP/1.1 200 OK'], ['Location'], []],
            ['index.php?cl=alist&cnid=oxmore&pgNr=20', ['HTTP/1.1 200 OK'], ['Location'], []],
            ['index.php?cl=alist&cnid=' . $oxidLiving, ['HTTP/1.1 200 OK'], ['ROBOTS'], []],
            ['index.php?cl=alist&cnid=' . $oxidLiving . '&pgNr=0', ['HTTP/1.1 200 OK'], ['ROBOTS'], []],
            ['index.php?cl=alist&cnid=' . $oxidLiving . '&pgNr=100', ['HTTP/1.1 302 Found'], [], ['index.php?cl=alist&cnid=' . $oxidLiving]],
            ['index.php?cl=alist&cnid=' . $oxidLiving . '&pgNr=200', ['HTTP/1.1 302 Found'], [], ['index.php?cl=alist&cnid=' . $oxidLiving]]
        ];

        if (('EE' == $facts->getEdition())) {
            $data[] = ['Fuer-Sie/', ['HTTP/1.1 200 OK'], ['ROBOTS'], []];
            $data[] = ['Fuer-Sie/45/', ['HTTP/1.1 302 Found', 'Location'], ['ROBOTS'], ['Fuer-Sie/']];
            $data[] = ['Fuer-Sie/?pgNr=0', ['HTTP/1.1 200 OK'], [ 'Location', 'ROBOTS'], []];
            $data[] = ['Fuer-Sie/?pgNr=34', ['HTTP/1.1 302 Found', 'Location'], ['ROBOTS'], ['Fuer-Sie/']];
        } else {
            $data[] = ['Geschenke/', ['HTTP/1.1 200 OK'], ['ROBOTS', 'Location'], ['index.php?cl=alist&cnid=' . $oxidLiving]];
            $data[] = ['Geschenke/?pgNr=0', ['HTTP/1.1 200 OK'], ['ROBOTS', 'Location'], ['index.php?cl=alist&cnid=' . $oxidLiving, 'Geschenke/']];
            $data[] = ['Geschenke/?pgNr=100', ['HTTP/1.1 302 Found', 'Location'], [], ['index.php?cl=alist&cnid=' . $oxidLiving, 'Geschenke/']];
            $data[] = ['Geschenke/30/', ['HTTP/1.1 302 Found', 'Location'], [], ['index.php?cl=alist&cnid=' . $oxidLiving, 'Geschenke/']];
            $data[] = ['Geschenke/?pgNr=1', ['HTTP/1.1 200 OK', 'ROBOTS', 'NOINDEX'], ['Location'], ['index.php?cl=alist&cnid=' . $oxidLiving, 'Geschenke/']];
            $data[] = ['Geschenke/?pgNr=3', ['HTTP/1.1 200 OK', 'ROBOTS', 'NOINDEX'], ['Location'], ['index.php?cl=alist&cnid=' . $oxidLiving, 'Geschenke/']];
            $data[] = ['Geschenke/?pgNr=4', ['HTTP/1.1 302 Found', 'Location'], [], ['index.php?cl=alist&cnid=' . $oxidLiving, 'Geschenke/']];
            $data[] = ['Geschenke/4/', ['HTTP/1.1 200 OK', 'ROBOTS', 'NOINDEX'], ['Location'], ['index.php?cl=alist&cnid=' . $oxidLiving, 'Geschenke/', 'Geschenke/?pgNr=0', 'Geschenke/?pgNr=1',]];
            $data[] = ['Geschenke/10/', ['HTTP/1.1 302 Found', 'Location'], [], ['index.php?cl=alist&cnid=' . $oxidLiving, 'Geschenke/', 'Geschenke/?pgNr=0', 'Geschenke/?pgNr=1', 'Geschenke/4/']];
        }

        return $data;
    }

    /**
     * Calling not existing pagenumbers must not result in additional entries in oxseo table.
     *
     * @dataProvider providerCheckSeoUrl
     *
     * @param string $urlToCall           Url to call
     * @param array  $responseContains    Curl call response must contain.
     * @param array  $responseNotContains Curl call response must not contain.
     * @param array  $prepareUrls         To make test cases independent, call this url first.
     */
    public function testCheckSeoUrl(
        $urlToCall,
        $responseContains,
        $responseNotContains,
        $prepareUrls
    ) {
        $this->initSeoUrlGeneration();

        foreach ($prepareUrls as $url) {
            $this->callCurl($url);
        }
        $response = $this->callCurl($urlToCall);

        foreach ($responseContains as $checkFor) {
            $this->assertContains($checkFor, $response, "Should get $checkFor");
        }
        foreach ($responseNotContains as $checkFor) {
            $this->assertNotContains($checkFor, $response, "Should not get $checkFor");
        }
    }

    public function testCreateProductSeoUrlsOnProductListPageRequest()
    {
        $this->prepareSeoUrlTestData();
        $this->initSeoUrlGeneration();

        $seoUrl = 'testSeoUrl/';

        $productSeoUrlsCountBeforeRequest = $this->getProductSeoUrlsCount($seoUrl);

        $this->callCurl($seoUrl . '?pgNr=0');
        $this->clearProxyCache();
        $this->callCurl($seoUrl . '?pgNr=0');

        $productSeoUrlsCountAfterRequest = $this->getProductSeoUrlsCount($seoUrl);

        $productsPerPage = 10;

        $this->assertEquals(
            $productSeoUrlsCountBeforeRequest + $productsPerPage,
            $productSeoUrlsCountAfterRequest
        );
    }

    public function testDoNotCreateAnotherCategorySeoUrlsOnProductListPageRequest()
    {
        $this->prepareSeoUrlTestData();

        $seoUrl = 'testSeoUrl/';

        $this->callCurl($seoUrl);

        $this->assertCount(
            1,
            $this->getCategorySeoUrls($seoUrl)
        );

        $this->callCurl($seoUrl . '?pgNr=0');
        $this->callCurl($seoUrl . '1');

        $this->assertCount(
            1,
            $this->getCategorySeoUrls($seoUrl)
        );
    }

    private function initSeoUrlGeneration()
    {
        $this->clearProxyCache();
        $this->callCurl(''); //call shop startpage
        $this->clearProxyCache();
    }

    private function getProductSeoUrlsCount($url)
    {
        $query = "
          SELECT 
              count(*)
          FROM 
              `oxseo`
          WHERE 
              oxseourl LIKE '%" . $url . "%'
              AND oxtype = 'oxarticle'
        ";

        return DatabaseProvider::getDb()->getOne($query);
    }

    private function getCategorySeoUrls($url)
    {
        $query = "
          SELECT 
              oxseourl
          FROM 
              `oxseo`
          WHERE 
              oxseourl LIKE '%" . $url . "%'
              AND oxtype = 'oxcategory'
        ";

        return DatabaseProvider::getDb()->getAll($query);
    }

    private function prepareSeoUrlTestData()
    {
        $seoUrl = 'testSeoUrl';
        $shopId = $this->getConfig()->getBaseShopId();

        $category = oxNew(Category::class);
        $category->oxcategories__oxactive       = new Field(1, Field::T_RAW);
        $category->oxcategories__oxparentid     = new Field('oxrootid', Field::T_RAW);
        $category->oxcategories__oxshopid       = new Field($shopId, Field::T_RAW);
        $category->oxcategories__oxtitle        = new Field($seoUrl, Field::T_RAW);
        $category->save();

        for ($i = 1; $i <= 20; $i++) {
            $product = oxNew(Article::class);
            $product->oxarticles__oxtitle = new Field($seoUrl, Field::T_RAW);
            $product->save();

            $relation = oxNew(Object2Category::class);
            $relation->setCategoryId($category->getId());
            $relation->setProductId($product->getId());
            $relation->save();
        }
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
        $curl->setOption('CURLOPT_RETURNTRANSFER', true);
        $curl->setUrl($url);
        $return = $curl->execute();

        sleep(0.5); // for master slave: please wait before checking the results.

        return $return;
    }

    /**
     * Test helper to check for paginated seo pages.
     */
    private function assertGeneratedPages()
    {
        $query = "SELECT oxstdurl, oxseourl FROM `oxseo` WHERE `OXSEOURL` LIKE '{$this->seoUrl}'" .
                 " OR `OXSEOURL` LIKE '{$this->seoUrl}%pgNr%'" .
                 " OR `OXSEOURL` LIKE '{$this->seoUrl}1/'" .
                 " OR `OXSEOURL` LIKE '{$this->seoUrl}2/'" .
                 " OR `OXSEOURL` LIKE '{$this->seoUrl}3/'" .
                 " OR `OXSEOURL` LIKE '{$this->seoUrl}4/'" .
                 " OR `OXSEOURL` LIKE '{$this->seoUrl}5/'" .
                 " AND oxtype = 'oxcategory'";
        $res = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll($query);
        $this->assertEquals(1, count($res));
    }

    /**
     * Test helper.
     *
     * @return array
     */
    private function getCategorySeoEntries()
    {
        $query = "SELECT oxstdurl, oxseourl FROM `oxseo` WHERE `OXSTDURL` like '%" . $this->categoryOxid . "%'" .
                 " AND oxtype = 'oxcategory'";
        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll($query);
    }

    /**
     * Test helper.
     */
    private function clearProxyCache()
    {
        $cacheService = oxNew(\OxidEsales\TestingLibrary\Services\Library\Cache::class);
        $cacheService->clearReverseProxyCache();
    }
}
