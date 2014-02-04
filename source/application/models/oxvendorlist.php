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
 * Vendor list manager.
 * Collects list of vendors according to collection rules (activ, etc.).
 *
 * @package model
 */
class oxVendorList extends oxList
{
    /**
     * Vendor root.
     *
     * @var stdClass
     */
    protected $_oRoot = null;

    /**
     * Vendor tree path.
     *
     * @var array
     */
    protected $_aPath = array();

    /**
     * To show vendor article count or not
     *
     * @var bool
     */
    protected $_blShowVendorArticleCnt = false;

    /**
     * Active vendor object
     *
     * @var oxvendor
     */
    protected $_oClickedVendor = null;

    /**
     * Class constructor, sets callback so that Shopowner is able to
     * add any information to the article.
     *
     * @param string $sObjectsInListName optional parameter, not used
     *
     * @return null
     */
    public function __construct( $sObjectsInListName = 'oxvendor')
    {
        $this->setShowVendorArticleCnt( $this->getConfig()->getConfigParam( 'bl_perfShowActionCatArticleCnt' ) );
        parent::__construct( 'oxvendor');
    }

    /**
     * Enables/disables vendor article count calculation
     *
     * @param bool $blShowVendorArticleCnt to show article count or not
     *
     * @return null
     */
    public function setShowVendorArticleCnt( $blShowVendorArticleCnt = false )
    {
        $this->_blShowVendorArticleCnt = $blShowVendorArticleCnt;
    }

    /**
     * Loads simple vendor list
     *
     * @return null
     */
    public function loadVendorList()
    {
        $oBaseObject = $this->getBaseObject();
        $sFieldList = $oBaseObject->getSelectFields();
        $sViewName  = $oBaseObject->getViewName();
        $this->getBaseObject()->setShowArticleCnt( $this->_blShowVendorArticleCnt );

        $sWhere = '';
        if ( !$this->isAdmin() ) {
            $sWhere  = $oBaseObject->getSqlActiveSnippet();
            $sWhere  = $sWhere?" where $sWhere and ":' where ';
            $sWhere .= "{$sViewName}.oxtitle != '' ";
        }

        $sSelect = "select {$sFieldList} from {$sViewName} {$sWhere} order by {$sViewName}.oxtitle";
        $this->selectString( $sSelect );
    }

    /**
     * Creates fake root for vendor tree, and ads category list fileds for each vendor item
     *
     * @param string $sLinkTarget  Name of class, responsible for category rendering
     * @param string $sActCat      Active category
     * @param string $sShopHomeUrl base shop url ($myConfig->getShopHomeURL())
     *
     * @return null
     */
    public function buildVendorTree( $sLinkTarget, $sActCat, $sShopHomeUrl )
    {
        $sActCat = str_replace( 'v_', '', $sActCat );

        //Load vendor list
        $this->loadVendorList();


        //Create fake vendor root category
        $this->_oRoot = oxNew( "oxVendor" );
        $this->_oRoot->load( 'root' );

        //category fields
        $this->_addCategoryFields( $this->_oRoot );
        $this->_aPath[] = $this->_oRoot;

        foreach ( $this as $sVndId => $oVendor ) {

            // storing active vendor object
            if ( $sVndId == $sActCat ) {
                $this->setClickVendor( $oVendor );
            }

            $this->_addCategoryFields( $oVendor );
            if ( $sActCat == $oVendor->oxvendor__oxid->value ) {
                $this->_aPath[] = $oVendor;
            }
        }

        $this->_seoSetVendorData();
    }

    /**
     * Root vendor list node (which usually is a manually prefilled object) getter
     *
     * @return oxvendor
     */
    public function getRootCat()
    {
        return $this->_oRoot;
    }

    /**
     * Returns vendor path array
     *
     * @return array
     */
    public function getPath()
    {
        return $this->_aPath;
    }

    /**
     * Adds category specific fields to vendor object
     *
     * @param object $oVendor vendor object
     *
     * @return null
     */
    protected function _addCategoryFields( $oVendor )
    {
        $oVendor->oxcategories__oxid    = new oxField("v_".$oVendor->oxvendor__oxid->value);
        $oVendor->oxcategories__oxicon  = $oVendor->oxvendor__oxicon;
        $oVendor->oxcategories__oxtitle = $oVendor->oxvendor__oxtitle;
        $oVendor->oxcategories__oxdesc  = $oVendor->oxvendor__oxshortdesc;

        $oVendor->setIsVisible( true );
        $oVendor->setHasVisibleSubCats( false );
    }

    /**
     * Sets active (open) vendor object
     *
     * @param oxvendor $oVendor active vendor
     *
     * @return null
     */
    public function setClickVendor( $oVendor )
    {
        $this->_oClickedVendor = $oVendor;
    }

    /**
     * returns active (open) vendor object
     *
     * @return oxvendor
     */
    public function getClickVendor()
    {
        return $this->_oClickedVendor;
    }

    /**
     * Processes vendor category URLs
     *
     * @return null
     */
    protected function _seoSetVendorData()
    {
        // only when SEO id on and in front end
        if ( oxRegistry::getUtils()->seoIsActive() && !$this->isAdmin()) {

            $oEncoder = oxRegistry::get("oxSeoEncoderVendor");

            // preparing root vendor category
            if ($this->_oRoot) {
                $oEncoder->getVendorUrl($this->_oRoot);
            }

            // encoding vendor category
            foreach ($this as $sVndId => $value) {
                $oEncoder->getVendorUrl( $this->_aArray[$sVndId] );
            }
        }
    }
}
