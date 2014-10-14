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
 * Transparent category manager class (executed automatically).
 * @subpackage oxcmp
 */
class oxcmp_categories extends oxView
{
    /**
     * More category object.
     * @var object
     */
    protected $_oMoreCat = null;

    /**
     * Marking object as component
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Marking object as component
     * @var bool
     */
    protected $_oCategoryTree = null;

    /**
     * Marking object as component
     * @var bool
     */
    protected $_oManufacturerTree = null;

    /**
     * Executes parent::init(), searches for active category in URL,
     * session, post variables ("cnid", "cdefnid"), active article
     * ("anid", usually article details), then loads article and
     * category if any of them available. Generates category/navigation
     * list.
     *
     * @return null
     */
    public function init()
    {
        parent::init();

        // Performance
        $myConfig = $this->getConfig();
        if ( $myConfig->getConfigParam( 'blDisableNavBars' ) &&
             $myConfig->getTopActiveView()->getIsOrderStep() ) {
            return;
        }

        $sActCat = $this->_getActCat();

        if ( $myConfig->getConfigParam( 'bl_perfLoadManufacturerTree' ) ) {
            // building Manufacturer tree
            $sActManufacturer = oxConfig::getParameter( 'mnid' );
            $this->_loadManufacturerTree( $sActManufacturer );
        }

        // building category tree for all purposes (nav, search and simple category trees)
        $this->_loadCategoryTree( $sActCat );
    }

    /**
     * get active article
     *
     * @return oxarticle
     */
    public function getProduct()
    {
        if ( ( $sActProduct = oxConfig::getParameter( 'anid' ) ) ) {
            $oParentView = $this->getParent();
            if ( ( $oProduct = $oParentView->getViewProduct() ) ) {
                return $oProduct;
            } else {
                $oProduct = oxNew( 'oxarticle' );
                if ( $oProduct->load( $sActProduct ) ) {
                    // storing for reuse
                    $oParentView->setViewProduct( $oProduct );
                    return $oProduct;
                }
            }
        }
    }

    /**
     * get active category id
     *
     * @return string
     */
    protected function _getActCat()
    {
        $sActManufacturer = oxConfig::getParameter( 'mnid' );
        $sActTag = oxConfig::getParameter( 'searchtag' );
        $sActCat = $sActManufacturer ? null : oxConfig::getParameter( 'cnid' );

        // loaded article - then checking additional parameters
        $oProduct = $this->getProduct();
        if ( $oProduct ) {
            $myConfig = $this->getConfig();

            $sActManufacturer = $myConfig->getConfigParam( 'bl_perfLoadManufacturerTree' ) ? $sActManufacturer : null;

            $sActVendor = ( getStr()->preg_match( '/^v_.?/i', $sActCat ) ) ? $sActCat : null;

            $sActCat = $this->_addAdditionalParams( $oProduct, $sActCat, $sActManufacturer, $sActTag, $sActVendor );
        }

        // Checking for the default category
        if ( $sActCat === null && !$oProduct && !$sActManufacturer && !$sActTag ) {
            // set remote cat
            $sActCat = $this->getConfig()->getActiveShop()->oxshops__oxdefcat->value;
            if ( $sActCat == 'oxrootid' ) {
                // means none selected
                $sActCat= null;
            }
        }
        return $sActCat;
    }

    /**
     * Category tree loader
     *
     * @param string $sActCat active category id
     *
     * @return null
     */
    protected function _loadCategoryTree( $sActCat )
    {
        $oCategoryTree = oxNew( 'oxCategoryList' );
        $oCategoryTree->buildTree( $sActCat );

        $oParentView = $this->getParent();

        // setting active category tree
        $oParentView->setCategoryTree( $oCategoryTree );
        $this->setCategoryTree( $oCategoryTree );

        // setting active category
        $oParentView->setActiveCategory( $oCategoryTree->getClickCat() );
    }

    /**
     * Manufacturer tree loader
     *
     * @param string $sActManufacturer active Manufacturer id
     *
     * @return null
     */
    protected function _loadManufacturerTree( $sActManufacturer )
    {
        $myConfig = $this->getConfig();
        if ( $myConfig->getConfigParam( 'bl_perfLoadManufacturerTree' ) ) {
            $oManufacturerTree = oxNew( 'oxmanufacturerlist' );
            $oManufacturerTree->buildManufacturerTree( 'manufacturerlist', $sActManufacturer, $myConfig->getShopHomeURL() );

            $oParentView = $this->getParent();

            // setting active Manufacturer list
            $oParentView->setManufacturerTree( $oManufacturerTree );
            $this->setManufacturerTree($oManufacturerTree);

            // setting active Manufacturer
            if ( ( $oManufacturer = $oManufacturerTree->getClickManufacturer() ) ) {
                $oParentView->setActManufacturer( $oManufacturer );
            }
        }
    }

    /**
     * Executes parent::render(), loads expanded/clicked category object,
     * adds parameters template engine and returns list of category tree.
     *
     * @return oxCategoryList
     */
    public function render()
    {
        parent::render();

        // Performance
        $myConfig = $this->getConfig();
        $oParentView = $this->getParent();

        if ( $myConfig->getConfigParam( 'bl_perfLoadManufacturerTree' ) && $this->_oManufacturerTree ) {
            $oParentView->setManufacturerlist( $this->_oManufacturerTree );
            $oParentView->setRootManufacturer( $this->_oManufacturerTree->getRootCat() );
        }

        if ( $this->_oCategoryTree ) {
            return $this->_oCategoryTree;
        }
    }

    /**
     * Adds additional parameters: active category, list type and category id
     *
     * @param oxArticle $oProduct         loaded product
     * @param string    $sActCat          active category id
     * @param string    $sActManufacturer active manufacturer id
     * @param string    $sActTag          active tag
     * @param string    $sActVendor       active vendor
     *
     * @return string $sActCat
     */
    protected function _addAdditionalParams( $oProduct, $sActCat, $sActManufacturer, $sActTag, $sActVendor )
    {
        $sSearchPar = oxConfig::getParameter( 'searchparam' );
        $sSearchCat = oxConfig::getParameter( 'searchcnid' );
        $sSearchVnd = oxConfig::getParameter( 'searchvendor' );
        $sSearchMan = oxConfig::getParameter( 'searchmanufacturer' );
        $sListType  = oxConfig::getParameter( 'listtype' );

        // search ?
        if ( ( !$sListType || $sListType == 'search' ) && ( $sSearchPar || $sSearchCat || $sSearchVnd || $sSearchMan ) ) {
            // setting list type directly
            $sListType = 'search';
        } else {

            // such Manufacturer is available ?
            if ( $sActManufacturer && ( $sActManufacturer == $oProduct->getManufacturerId() ) ) {
                // setting list type directly
                $sListType = 'manufacturer';
                $sActCat   = $sActManufacturer;
            } elseif ( $sActVendor && ( substr( $sActVendor, 2 ) == $oProduct->getVendorId() ) ) {
                // such vendor is available ?
                $sListType = 'vendor';
                $sActCat   = $sActVendor;
            } elseif ( $sActTag ) {
                // tag ?
                $sListType = 'tag';
            } elseif ( $sActCat && $oProduct->isAssignedToCategory( $sActCat ) ) {
                // category ?
            } else {
                list( $sListType, $sActCat ) = $this->_getDefaultParams( $oProduct );
            }
        }

        $oParentView = $this->getParent();
        //set list type and category id
        $oParentView->setListType( $sListType );
        $oParentView->setCategoryId( $sActCat );

        return $sActCat;
    }

    /**
     * Returns array containing default list type and category (or manufacturer ir vendor) id
     *
     * @param oxArticle $oProduct current product object
     *
     * @return array
     */
    protected function _getDefaultParams( $oProduct )
    {
        $sListType = null;
        $aArticleCats = $oProduct->getCategoryIds( true );
        if ( is_array( $aArticleCats ) && count( $aArticleCats ) ) {
            $sActCat = reset( $aArticleCats );
        } elseif ( ( $sActCat = $oProduct->getManufacturerId() ) ) {
            // not assigned to any category ? maybe it is assigned to Manufacturer ?
            $sListType = 'manufacturer';
        } elseif ( ( $sActCat = $oProduct->getVendorId() ) ) {
            // not assigned to any category ? maybe it is assigned to vendor ?
            $sListType = 'vendor';
        } else {
            $sActCat = null;
        }

        return array( $sListType, $sActCat );
    }

    /**
     * Setter of category tree
     *
     * @param oxCategoryList $oCategoryTree category list
     *
     * @return null
     */
    public function setCategoryTree( $oCategoryTree )
    {
        $this->_oCategoryTree = $oCategoryTree;
    }

    /**
     * Setter of manufacturer tree
     *
     * @param oxManufacturerList $oManufacturerTree manufacturer list
     *
     * @return null
     */
    public function setManufacturerTree( $oManufacturerTree )
    {
        $this->_oManufacturerTree = $oManufacturerTree;
    }
}
