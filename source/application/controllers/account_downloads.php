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
 * Account article file download page.
 *
 * @package main
 */
class Account_Downloads extends Account
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/account/downloads.tpl';

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * @var oxOrderFileList
     */
    protected $_oOrderFilesList = null;



    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = array();
        $aPath  = array();

        $aPath['title'] = oxRegistry::getLang()->translateString( 'MY_ACCOUNT', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  =  oxRegistry::get("oxSeoEncoder")->getStaticUrl( $this->getViewConfig()->getSelfLink() . "cl=account" );
        $aPaths[] = $aPath;

        $aPath['title'] = oxRegistry::getLang()->translateString( 'MY_DOWNLOADS', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Returns article list which was ordered and has downloadable files
     *
     * @return null|oxArticleList
     */
    public function getOrderFilesList()
    {
        if ( $this->_oOrderFilesList !== null ) {
            return $this->_oOrderFilesList;
        }

        $oOrderFileList = oxNew('oxOrderFileList');
        $oOrderFileList->loadUserFiles( $this->getUser()->getId() );

        $this->_oOrderFilesList = $this->_prepareForTemplate( $oOrderFileList );

        return $this->_oOrderFilesList;
    }

    /**
     * Returns prepared orders files list
     *
     * @param oxorderfilelist $oOrderFileList - list or orderfiles
     *
     * @return array
     */
    protected function _prepareForTemplate( $oOrderFileList )
    {
        $oOrderArticles = array();

        foreach ( $oOrderFileList as $oOrderFile ) {
            $sOrderArticleId = $oOrderFile->oxorderfiles__oxorderarticleid->value;
            $oOrderArticles[$sOrderArticleId]['oxordernr'] = $oOrderFile->oxorderfiles__oxordernr->value;
            $oOrderArticles[$sOrderArticleId]['oxorderdate'] = substr($oOrderFile->oxorderfiles__oxorderdate->value, 0, 16);
            $oOrderArticles[$sOrderArticleId]['oxarticletitle'] = $oOrderFile->oxorderfiles__oxarticletitle->value;
            $oOrderArticles[$sOrderArticleId]['oxorderfiles'][] = $oOrderFile;
        }

        return $oOrderArticles;
    }

     /**
     * Returns error code.
     *
     * @return int
     */
    public function getDownloadError()
    {
        return $this->getConfig()->getParameter( 'download_error' );
    }

}
