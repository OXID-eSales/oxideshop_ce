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
 * Manufacturer list manager.
 * Collects list of manufacturers according to collection rules (activ, etc.).
 *
 * @package model
 */
class oxManufacturerList extends oxList
{
    /**
     * Manufacturer root.
     *
     * @var stdClass
     */
    protected $_oRoot = null;

    /**
     * Manufacturer tree path.
     *
     * @var array
     */
    protected $_aPath = array();

    /**
     * To show manufacturer article count or not
     *
     * @var bool
     */
    protected $_blShowManufacturerArticleCnt = false;

    /**
     * Active manufacturer object
     *
     * @var oxmanufacturer
     */
    protected $_oClickedManufacturer = null;

    /**
     * Class constructor, sets callback so that Shopowner is able to
     * add any information to the article.
     *
     * @param string $sObjectsInListName optional parameter, not used
     *
     * @return null
     */
    public function __construct( $sObjectsInListName = 'oxmanufacturer')
    {
        $this->setShowManufacturerArticleCnt( $this->getConfig()->getConfigParam( 'bl_perfShowActionCatArticleCnt' ) );
        parent::__construct( 'oxmanufacturer');
    }

    /**
     * Enables/disables manufacturer article count calculation
     *
     * @param bool $blShowManufacturerArticleCnt to show article count or not
     *
     * @return null
     */
    public function setShowManufacturerArticleCnt( $blShowManufacturerArticleCnt = false )
    {
        $this->_blShowManufacturerArticleCnt = $blShowManufacturerArticleCnt;
    }

    /**
     * Loads simple manufacturer list
     *
     * @return null
     */
    public function loadManufacturerList()
    {
        $oBaseObject = $this->getBaseObject();

        $sFieldList = $oBaseObject->getSelectFields();
        $sViewName  = $oBaseObject->getViewName();
        $this->getBaseObject()->setShowArticleCnt( $this->_blShowManufacturerArticleCnt );

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
     * Creates fake root for manufacturer tree, and ads category list fileds for each manufacturer item
     *
     * @param string $sLinkTarget  Name of class, responsible for category rendering
     * @param string $sActCat      Active category
     * @param string $sShopHomeUrl base shop url ($myConfig->getShopHomeURL())
     *
     * @return null
     */
    public function buildManufacturerTree( $sLinkTarget, $sActCat, $sShopHomeUrl )
    {
        //Load manufacturer list
        $this->loadManufacturerList();


        //Create fake manufacturer root category
        $this->_oRoot = oxNew( "oxManufacturer" );
        $this->_oRoot->load( "root" );

        //category fields
        $this->_addCategoryFields( $this->_oRoot );
        $this->_aPath[] = $this->_oRoot;

        foreach ( $this as $sVndId => $oManufacturer ) {

            // storing active manufacturer object
            if ( $sVndId == $sActCat ) {
                $this->setClickManufacturer( $oManufacturer );
            }

            $this->_addCategoryFields( $oManufacturer );
            if ( $sActCat == $oManufacturer->oxmanufacturers__oxid->value ) {
                $this->_aPath[] = $oManufacturer;
            }
        }

        $this->_seoSetManufacturerData();
    }

    /**
     * Root manufacturer list node (which usually is a manually prefilled object) getter
     *
     * @return oxmanufacturer
     */
    public function getRootCat()
    {
        return $this->_oRoot;
    }

    /**
     * Returns manufacturer path array
     *
     * @return array
     */
    public function getPath()
    {
        return $this->_aPath;
    }

    /**
     * Adds category specific fields to manufacturer object
     *
     * @param object $oManufacturer manufacturer object
     *
     * @return null
     */
    protected function _addCategoryFields( $oManufacturer )
    {
        $oManufacturer->oxcategories__oxid    = new oxField( $oManufacturer->oxmanufacturers__oxid->value );
        $oManufacturer->oxcategories__oxicon  = $oManufacturer->oxmanufacturers__oxicon;
        $oManufacturer->oxcategories__oxtitle = $oManufacturer->oxmanufacturers__oxtitle;
        $oManufacturer->oxcategories__oxdesc  = $oManufacturer->oxmanufacturers__oxshortdesc;

        $oManufacturer->setIsVisible( true );
        $oManufacturer->setHasVisibleSubCats( false );
    }

    /**
     * Sets active (open) manufacturer object
     *
     * @param oxmanufacturer $oManufacturer active manufacturer
     *
     * @return null
     */
    public function setClickManufacturer( $oManufacturer )
    {
        $this->_oClickedManufacturer = $oManufacturer;
    }

    /**
     * returns active (open) manufacturer object
     *
     * @return oxmanufacturer
     */
    public function getClickManufacturer()
    {
        return $this->_oClickedManufacturer;
    }

    /**
     * Processes manufacturer category URLs
     *
     * @return null
     */
    protected function _seoSetManufacturerData()
    {
        // only when SEO id on and in front end
        if ( oxRegistry::getUtils()->seoIsActive() && !$this->isAdmin()) {

            $oEncoder = oxRegistry::get("oxSeoEncoderManufacturer");

            // preparing root manufacturer category
            if ($this->_oRoot) {
                $oEncoder->getManufacturerUrl($this->_oRoot);
            }

            // encoding manufacturer category
            foreach ($this as $sVndId => $value) {
                $oEncoder->getManufacturerUrl( $this->_aArray[$sVndId] );
            }
        }
    }
}
