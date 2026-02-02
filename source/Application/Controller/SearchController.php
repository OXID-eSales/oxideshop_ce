<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Application\Model\ArticleList;
use OxidEsales\Eshop\Application\Model\Search;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\Event\AfterProductSearchEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\Event\BeforeProductSearchEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchCriteria;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Search\ProductSearchServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Search\EqualsFilter;
use OxidEsales\EshopCommunity\Internal\Framework\Search\Pagination;
use OxidEsales\EshopCommunity\Internal\Framework\Search\SearchTerm;
use OxidEsales\EshopCommunity\Internal\Framework\Search\SortDirection;
use OxidEsales\EshopCommunity\Internal\Framework\Search\Sorting;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Articles searching class.
 * Performs searching through articles in database.
 */
class SearchController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Count of all found articles.
     *
     * @var integer
     */
    protected $_iAllArtCnt = 0;

    /**
     * Number of possible pages.
     *
     * @var integer
     */
    protected $_iCntPages = null;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/search/search';

    /**
     * List type
     *
     * @var string
     */
    protected $_sListType = 'search';

    /**
     * Marked which defines if current view is sortable or not
     *
     * @var bool
     */
    protected $_blShowSorting = true;

    /**
     * If search was empty
     *
     * @var bool
     */
    protected $_blEmptySearch = null;

    /**
     * Similar recommendation lists
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var object
     */
    protected $_oRecommList = null;

    /**
     * Search parameter for Html
     *
     * @var string
     */
    protected $_sSearchParamForHtml = null;

    /**
     * Search parameter
     *
     * @var string
     */
    protected $_sSearchParam = null;

    /**
     * Searched category
     *
     * @var string
     */
    protected $_sSearchCatId = null;

    /**
     * Searched vendor
     *
     * @var string
     */
    protected $_sSearchVendor = null;

    /**
     * Searched manufacturer
     *
     * @var string
     */
    protected $_sSearchManufacturer = null;

    /**
     * If called class is search
     *
     * @var bool
     */
    protected $_blSearchClass = null;

    /**
     * Page navigation
     *
     * @var object
     */
    protected $_oPageNavigation = null;

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Array of id to form recommendation list.
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var array
     */
    protected $_aSimilarRecommListIds = null;

    /**
     * Fetches search parameter from GET/POST/session, prepares search
     * SQL (search::GetWhere()), and executes it forming the list of
     * found articles. Article list is stored at search::_aArticleList
     * array.
     *
     * @return null
     */
    public function init()
    {
        parent::init();

        $searchParam = trim((string) Registry::getRequest()->getRequestParameter('searchparam'));
        $category = rawurldecode((string) Registry::getRequest()->getRequestEscapedParameter('searchcnid'));
        $vendor = rawurldecode((string) Registry::getRequest()->getRequestEscapedParameter('searchvendor'));
        $manufacturer = rawurldecode((string) Registry::getRequest()->getRequestEscapedParameter('searchmanufacturer'));

        $this->_sSearchCatId = $category;
        $this->_sSearchManufacturer = $manufacturer;
        $this->_aArticleList = null;
        $this->_blEmptySearch = false;
        if (!$searchParam && !$category && !$vendor && !$manufacturer) {
            $this->_blEmptySearch = true;

            return false;
        }

        if (!Registry::getConfig()->getConfigParam('bl_perfLoadManufacturerTree')) {
            $manufacturer = '';
        }

        $filters = [];
        if ($category) {
            $filters['oxcatnid'] = new EqualsFilter('oxcatnid', $category);
        }
        if ($vendor) {
            $filters['oxvendorid'] = new EqualsFilter('oxvendorid', $vendor);
        }
        if ($manufacturer) {
            $filters['oxmanufacturerid'] = new EqualsFilter('oxmanufacturerid', $manufacturer);
        }

        $itemsPerPage = max(1, (int) Registry::getConfig()->getConfigParam('iNrofCatArticles'));

        $this->loadSearchResults($itemsPerPage, $searchParam, $filters);

        $this->_iCntPages = ceil($this->_iAllArtCnt / $itemsPerPage);
    }

    private function loadSearchResults(int $itemsPerPage, string $searchTerm, array $filters): void
    {
        if (ContainerFacade::getParameter('oxid_esales.product_search_enabled')) {
            $logger = ContainerFacade::get(LoggerInterface::class);

            if (!ContainerFacade::has(ProductSearchServiceInterface::class)) {
                $logger->warning('Product search service is not registered, falling back to default search.');
            } else {
                try {
                    $this->loadProductsWithCustomSearch($itemsPerPage, $searchTerm, $filters);
                    return;
                } catch (Throwable $e) {
                    $logger->error('Unable to use search service, falling back to default search.', [$e]);
                }
            }
        }

        $this->loadProductsFromDatabase($searchTerm, $filters);
    }

    private function loadProductsWithCustomSearch(int $itemsPerPage, string $searchTerm, array $filters): void
    {
        $criteria = $this->buildSearchCriteria($itemsPerPage, $searchTerm, $filters);
        $beforeEvent = ContainerFacade::dispatch(new BeforeProductSearchEvent($criteria));

        $searchCriteria = $beforeEvent->getSearchCriteria();
        $context = $beforeEvent->getContext();

        $result = ContainerFacade::get(ProductSearchServiceInterface::class)->search($searchCriteria, $context);
        $afterEvent = ContainerFacade::dispatch(new AfterProductSearchEvent($searchCriteria, $context, $result));

        $articleList = oxNew(ArticleList::class);
        $articleList->loadIds(array_map('strval', $afterEvent->getSearchResult()->getProductIds()));
        $this->_aArticleList = $articleList;
        $this->_iAllArtCnt = $afterEvent->getSearchResult()->getTotal();
    }

    private function buildSearchCriteria(int $itemsPerPage, string $searchTerm, array $filters): ProductSearchCriteria
    {
        $page = max(1, (int) Registry::getRequest()->getRequestParameter('pgNr') + 1);

        return new ProductSearchCriteria(
            Pagination::fromPage($page, $itemsPerPage),
            new SearchTerm($searchTerm),
            array_values($filters),
            $this->buildSorting()
        );
    }

    private function buildSorting(): array
    {
        $sortingData = $this->getSorting($this->getSortIdent());
        $sortBy = $sortingData['sortby'] ?? null;
        $sortDirection = SortDirection::tryFrom(strtoupper((string) ($sortingData['sortdir'] ?? '')));

        if (!$sortBy || !$sortDirection) {
            return [];
        }

        return [new Sorting($sortBy, $sortDirection)];
    }

    private function loadProductsFromDatabase(string $searchTerm, array $filters): void
    {
        $category = ($filters['oxcatnid'] ?? null)?->getValue();
        $vendor = ($filters['oxvendorid'] ?? null)?->getValue();
        $manufacturer = ($filters['oxmanufacturerid'] ?? null)?->getValue();

        $searchHandler = oxNew(Search::class);
        $searchList = $searchHandler->getSearchArticles(
            $searchTerm ?: null,
            $category,
            $vendor,
            $manufacturer,
            $this->getSortingSql($this->getSortIdent())
        );

        $this->_aArticleList = $searchList;
        $this->_iAllArtCnt = 0;

        if ($searchList->count()) {
            $this->_iAllArtCnt = $searchHandler->getSearchArticleCount(
                $searchTerm ?: null,
                $category,
                $vendor,
                $manufacturer
            );
        }
    }

    /**
     * Forms search navigation URLs, executes parent::render() and
     * returns name of template to render search::_sThisTemplate.
     *
     * @return  string  current template file name
     */
    public function render()
    {
        parent::render();
        // processing list articles
        $this->processListArticles();

        return $this->_sThisTemplate;
    }

    /**
     * Iterates through list articles and performs list view specific tasks:
     *  - sets type of link which needs to be generated (Manufacturer link)
     */
    protected function processListArticles()
    {
        $sAddDynParams = $this->getAddUrlParams();
        if ($sAddDynParams && ($aArtList = $this->getArticleList())) {
            $blSeo = \OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive();
            foreach ($aArtList as $oArticle) {
                // appending std and dynamic urls
                if (!$blSeo) {
                    // only if seo is off..
                    $oArticle->appendStdLink($sAddDynParams);
                }
                $oArticle->appendLink($sAddDynParams);
            }
        }
    }

    /**
     * Returns additional URL parameters which must be added to list products urls
     *
     * @return string
     */
    public function getAddUrlParams()
    {
        $sAddParams = parent::getAddUrlParams();
        $sAddParams .= ($sAddParams ? '&amp;' : '') . "listtype={$this->_sListType}";
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        if ($sParam = Registry::getRequest()->getRequestParameter('searchparam')) {
            $sAddParams .= "&amp;searchparam=" . rawurlencode($sParam);
        }

        if ($sParam = Registry::getRequest()->getRequestEscapedParameter('searchcnid')) {
            $sAddParams .= "&amp;searchcnid=$sParam";
        }

        if ($sParam = rawurldecode(Registry::getRequest()->getRequestEscapedParameter('searchvendor'))) {
            $sAddParams .= "&amp;searchvendor=$sParam";
        }

        if ($sParam = rawurldecode(Registry::getRequest()->getRequestEscapedParameter('searchmanufacturer'))) {
            $sAddParams .= "&amp;searchmanufacturer=$sParam";
        }

        return $sAddParams;
    }

    /**
     * Template variable getter. Returns similar recommendation lists
     *
     * @return object
     */
    protected function isSearchClass()
    {
        if ($this->_blSearchClass === null) {
            $this->_blSearchClass = false;
            if ('search' == strtolower(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestControllerId())) {
                $this->_blSearchClass = true;
            }
        }

        return $this->_blSearchClass;
    }

    /**
     * Template variable getter. Returns if searched was empty
     *
     * @return bool
     */
    public function isEmptySearch()
    {
        return $this->_blEmptySearch;
    }

    /**
     * Template variable getter. Returns searched article list
     *
     * @return array
     */
    public function getArticleList()
    {
        return $this->_aArticleList;
    }

    /**
     * Return array of id to form recommend list.
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return array
     */
    public function getSimilarRecommListIds()
    {
        if ($this->_aSimilarRecommListIds === null) {
            $this->_aSimilarRecommListIds = false;

            $aList = $this->getArticleList();
            if ($aList && $aList->count() > 0) {
                $this->_aSimilarRecommListIds = $aList->arrayKeys();
            }
        }

        return $this->_aSimilarRecommListIds;
    }

    /**
     * Template variable getter. Returns search parameter for Html
     *
     * @return string
     */
    public function getSearchParamForHtml()
    {
        if ($this->_sSearchParamForHtml === null) {
            $this->_sSearchParamForHtml = false;
            if ($this->isSearchClass()) {
                $this->_sSearchParamForHtml = Registry::getRequest()->getRequestEscapedParameter('searchparam');
            }
        }

        return $this->_sSearchParamForHtml;
    }

    /**
     * Template variable getter. Returns search parameter
     *
     * @return string
     */
    public function getSearchParam()
    {
        if ($this->_sSearchParam === null) {
            $this->_sSearchParam = false;
            if ($this->isSearchClass()) {
                $this->_sSearchParam = rawurlencode(Registry::getRequest()->getRequestParameter('searchparam'));
            }
        }

        return $this->_sSearchParam;
    }

    /**
     * Template variable getter. Returns searched category id
     *
     * @return string
     */
    public function getSearchCatId()
    {
        if ($this->_sSearchCatId === null) {
            $this->_sSearchCatId = false;
            if ($this->isSearchClass()) {
                $this->_sSearchCatId = rawurldecode(Registry::getRequest()->getRequestEscapedParameter('searchcnid'));
            }
        }

        return $this->_sSearchCatId;
    }

    /**
     * Template variable getter. Returns searched vendor id
     *
     * @return string
     */
    public function getSearchVendor()
    {
        if ($this->_sSearchVendor === null) {
            $this->_sSearchVendor = false;
            if ($this->isSearchClass()) {
                // searching in vendor #671
                $this->_sSearchVendor = rawurldecode(Registry::getRequest()->getRequestEscapedParameter('searchvendor'));
            }
        }

        return $this->_sSearchVendor;
    }

    /**
     * Template variable getter. Returns searched Manufacturer id
     *
     * @return string
     */
    public function getSearchManufacturer()
    {
        if ($this->_sSearchManufacturer === null) {
            $this->_sSearchManufacturer = false;
            if ($this->isSearchClass()) {
                // searching in Manufacturer #671
                $sManufacturerParameter = Registry::getRequest()->getRequestEscapedParameter('searchmanufacturer');
                $this->_sSearchManufacturer = rawurldecode($sManufacturerParameter);
            }
        }

        return $this->_sSearchManufacturer;
    }

    /**
     * Template variable getter. Returns page navigation
     *
     * @return object
     */
    public function getPageNavigation()
    {
        if ($this->_oPageNavigation === null) {
            $this->_oPageNavigation = false;
            $this->_oPageNavigation = $this->generatePageNavigation();
        }

        return $this->_oPageNavigation;
    }


    /**
     * Template variable getter. Returns active search
     *
     * @return object
     */
    public function getActiveCategory()
    {
        return $this->getActSearch();
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = [];
        $aPath = [];

        $iBaseLanguage = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $aPath['title'] = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('SEARCH', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Returns config parameters blShowListDisplayType value
     *
     * @return boolean
     */
    public function canSelectDisplayType()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blShowListDisplayType');
    }

    /**
     * Checks if current request parameters does not block SEO redirection process
     *
     * @return bool
     */
    protected function canRedirect()
    {
        return false;
    }

    /**
     * Article count getter
     *
     * @return int
     */
    public function getArticleCount()
    {
        return $this->_iAllArtCnt;
    }

    /**
     * Return page title
     *
     * @return string
     */
    public function getTitle()
    {
        $sTitle = '';
        $sTitle .= $this->getArticleCount();
        $iBaseLanguage = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $sTitle .= ' ' . \OxidEsales\Eshop\Core\Registry::getLang()->translateString('HITS_FOR', $iBaseLanguage, false);
        $sTitle .= ' "' . $this->getSearchParamForHtml() . '"';

        return $sTitle;
    }
}
