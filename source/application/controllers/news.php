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
 * Shop news window.
 * Arranges news texts. OXID eShop -> (click on News box on left side).
 */
class News extends oxUBase
{
    /**
     * Newslist
     * @var object
     */
    protected $_oNewsList = null;
    /**
     * Current class login template name.
     * @var string
     */
    protected $_sThisTemplate = 'page/info/news.tpl';

    /**
     * Sign if to load and show bargain action
     * @var bool
     */
    protected $_blBargainAction = true;


    /**
     * Page navigation
     * @var object
     */
    protected $_oPageNavigation = null;

    /**
     * Number of possible pages.
     * @var integer
     */
    protected $_iCntPages = null;

    /**
     * Template variable getter. Returns newslist
     *
     * @return object
     */
    public function getNews()
    {
        if ( $this->_oNewsList === null ) {
            $this->_oNewsList = false;

            $iPerPage = (int) $this->getConfig()->getConfigParam( 'iNrofCatArticles' );
            $iPerPage = $iPerPage ? $iPerPage : 10;

            $oActNews = oxNew( 'oxnewslist' );

            if ( $iCnt = $oActNews->getCount() ) {

                 $this->_iCntPages = round( $iCnt / $iPerPage + 0.49 );

                 $oActNews->loadNews( $this->getActPage() * $iPerPage, $iPerPage );
                 $this->_oNewsList = $oActNews;
            }
        }

        return $this->_oNewsList;
    }


    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = array();
        $aPath  = array();

        $aPath['title'] = oxRegistry::getLang()->translateString( 'LATEST_NEWS_AND_UPDATES_AT', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = $this->getLink();

        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Template variable getter. Returns page navigation
     *
     * @return object
     */
    public function getPageNavigation()
    {
        if ( $this->_oPageNavigation === null ) {
            $this->_oPageNavigation = false;
            $this->_oPageNavigation = $this->generatePageNavigation();
        }

        return $this->_oPageNavigation;
    }

}
