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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Shop RSS page.
 */
class Rss extends oxUBase
{
    /**
     * current rss object
     * @var oxRssFeed
     */
    protected $_oRss = null;

    /**
     * Current rss channel
     * @var object
     */
    protected $_oChannel = null;

    /**
     * Xml start and end definition
     * @var array
     */
    protected $_aXmlDef = null;


    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'widget/rss.tpl';

    /**
     * get oxRssFeed
     *
     * @return oxRssFeed
     */
    protected function _getRssFeed()
    {
        if (!$this->_oRss) {
            $this->_oRss = oxNew('oxRssFeed');
        }
        return $this->_oRss;
    }

    /**
     * Renders requested RSS feed
     *
     * Template variables:
     * <b>rss</b>
     *
     * @return  string  $this->_sThisTemplate   current template file name
     */
    public function render()
    {
        parent::render();

        $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty();

        // #2873: In demoshop for RSS we set php_handling to SMARTY_PHP_PASSTHRU
        // as SMARTY_PHP_REMOVE removes not only php tags, but also xml
        if ($this->getConfig()->isDemoShop()) {
            $oSmarty->php_handling = SMARTY_PHP_PASSTHRU;
        }

        foreach ( array_keys( $this->_aViewData ) as $sViewName ) {
            $oSmarty->assign_by_ref( $sViewName, $this->_aViewData[$sViewName] );
        }

        // return rss xml, no further processing
        oxRegistry::getUtils()->setHeader( "Content-Type: text/xml; charset=".oxRegistry::getLang()->translateString( "charset" ) );
        oxRegistry::getUtils()->showMessageAndExit(
                    $this->_processOutput(
                            $oSmarty->fetch($this->_sThisTemplate, $this->getViewId())
                    )
                );
    }

    /**
     * Processes xml before outputting to user
     *
     * @param string $sInput input to process
     *
     * @return string
     */
    protected function _processOutput( $sInput )
    {
        return getStr()->recodeEntities( $sInput );
    }

    /**
     * getTopShop loads top shop articles to rss
     *
     * @access public
     * @return void
     */
    public function topshop()
    {
        if ($this->getConfig()->getConfigParam( 'bl_rssTopShop' )) {
            $this->_getRssFeed()->loadTopInShop();
        } else {
            error_404_handler();
        }
    }

    /**
     * loads newest shop articles
     *
     * @access public
     * @return void
     */
    public function newarts()
    {
        if ($this->getConfig()->getConfigParam( 'bl_rssNewest' )) {
            $this->_getRssFeed()->loadNewestArticles();
        } else {
            error_404_handler();
        }
    }

    /**
     * loads category articles
     *
     * @access public
     * @return void
     */
    public function catarts()
    {
        if ($this->getConfig()->getConfigParam( 'bl_rssCategories' )) {
            $oCat = oxNew('oxCategory');
            if ($oCat->load(oxConfig::getParameter('cat'))) {
                $this->_getRssFeed()->loadCategoryArticles($oCat);
            }
        } else {
            error_404_handler();
        }
    }

    /**
     * loads search articles
     *
     * @access public
     * @return void
     */
    public function searcharts()
    {
        if ($this->getConfig()->getConfigParam( 'bl_rssSearch' )) {
            $this->_getRssFeed()->loadSearchArticles( oxConfig::getParameter('searchparam', true), oxConfig::getParameter('searchcnid'), oxConfig::getParameter('searchvendor'), oxConfig::getParameter('searchmanufacturer'));
        } else {
            error_404_handler();
        }
    }

    /**
     * loads recommendation lists
     *
     * @access public
     * @return void
     */
    public function recommlists()
    {
        if ($this->getViewConfig()->getShowListmania() && $this->getConfig()->getConfigParam( 'bl_rssRecommLists' )) {
            $oArticle = oxNew('oxarticle');
            if ($oArticle->load(oxConfig::getParameter('anid'))) {
                $this->_getRssFeed()->loadRecommLists($oArticle);
                return;
            }
        }
        error_404_handler();
    }

    /**
     * loads recommendation list articles
     *
     * @access public
     * @return void
     */
    public function recommlistarts()
    {
        if ($this->getConfig()->getConfigParam( 'bl_rssRecommListArts' )) {
            $oRecommList = oxNew('oxrecommlist');
            if ($oRecommList->load(oxConfig::getParameter('recommid'))) {
                $this->_getRssFeed()->loadRecommListArticles($oRecommList);
                return;
            }
        }
        error_404_handler();
    }

    /**
     * getBargain loads top shop articles to rss
     *
     * @access public
     * @return void
     */
    public function bargain()
    {
        if ($this->getConfig()->getConfigParam( 'bl_rssBargain' )) {
            $this->_getRssFeed()->loadBargain();
        } else {
            error_404_handler();
        }
    }

    /**
     * Template variable getter. Returns rss channel
     *
     * @return object
     */
    public function getChannel()
    {
        if ( $this->_oChannel === null ) {
            $this->_oChannel = $this->_getRssFeed()->getChannel();
        }
        return $this->_oChannel;
    }

    /**
     * Returns if view should be cached
     *
     * @return bool
     */
    public function getCacheLifeTime()
    {
        return $this->_getRssFeed()->getCacheTtl();
    }

}
