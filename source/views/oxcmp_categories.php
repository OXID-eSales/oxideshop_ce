<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   views
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
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
             $myConfig->getActiveView()->getIsOrderStep() ) {
            return;
        }

        $sActCat = $this->_getActCat();

        //@deprecated in v.4.5.7, since 2012-02-15; config option removed bug #0003385
        if ( $myConfig->getConfigParam( 'bl_perfLoadVendorTree' ) ) {
            // building vendor tree
            $this->_loadVendorTree( $sActCat );
        }

        if ( $myConfig->getConfigParam( 'bl_perfLoadManufacturerTree' ) ) {
            // building Manufacturer tree
            $sActManufacturer = oxConfig::getParameter( 'mnid' );
            $this->_loadManufacturerTree( $sActManufacturer );
        }

        if ( $myConfig->getConfigParam( 'bl_perfLoadCatTree' ) ) {

            // building categorytree for all purposes (nav, search and simple category trees)
            $this->_loadCategoryTree( $sActCat );

            if ( $myConfig->getConfigParam( 'blTopNaviLayout' ) ) {
                if ( ! ( $sActCont = oxConfig::getParameter( 'oxcid' ) ) ) {
                    $sActCont = oxConfig::getParameter( 'tpl' );
                }
                $this->_oMoreCat = $this->_getMoreCategory( $sActCat, $sActCont );
            }
        }
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
        if ( ! ( $sActCont = oxConfig::getParameter( 'oxcid' ) ) ) {
            $sActCont = oxConfig::getParameter( 'tpl' );
        }
        $sActManufacturer = oxConfig::getParameter( 'mnid' );
        $sActTag = oxConfig::getParameter( 'searchtag' );
        $sActCat = $sActManufacturer ? null : oxConfig::getParameter( 'cnid' );

        // loaded article - then checking additional parameters
        $oProduct = $this->getProduct();
        if ( $oProduct ) {
            $myConfig = $this->getConfig();

            $sActManufacturer = $myConfig->getConfigParam( 'bl_perfLoadManufacturerTree' ) ? $sActManufacturer : null;

            //@deprecated in v.4.5.7, since 2012-02-15; config option removed bug #0003385
            $sActVendor = ( $myConfig->getConfigParam( 'bl_perfLoadVendorTree' ) && getStr()->preg_match( '/^v_.?/i', $sActCat ) ) ? $sActCat : null;

            $sActCat = $this->_addAdditionalParams( $oProduct, $sActCat, $sActManufacturer, $sActCont, $sActTag, $sActVendor );
        }

        // Checking for the default category
        if ( $sActCat === null && !$oProduct && !$sActCont && !$sActManufacturer && !$sActTag ) {
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
        $myConfig = $this->getConfig();
        if ( $myConfig->getConfigParam( 'bl_perfLoadCatTree' ) ) {
            $oCategoryTree = oxNew( 'oxcategorylist' );
            $oCategoryTree->buildTree( $sActCat, $myConfig->getConfigParam( 'blLoadFullTree' ), $myConfig->getConfigParam( 'bl_perfLoadTreeForSearch' ), $myConfig->getConfigParam( 'blTopNaviLayout' ) );

            $oParentView = $this->getParent();

            // setting active category tree
            $oParentView->setCategoryTree( $oCategoryTree );

            // setting active category
            $oParentView->setActCategory( $oCategoryTree->getClickCat() );
        }
    }

    /**
     * Vendor tree loader
     *
     * @param string $sActVendor active vendor id
     *
     * @deprecated in v.4.5.7, since 2012-02-15; config option removed bug #0003385
     *
     * @return null
     */
    protected function _loadVendorTree( $sActVendor )
    {
        $myConfig = $this->getConfig();
        if ( $myConfig->getConfigParam( 'bl_perfLoadVendorTree' ) ) {
            $oVendorTree = oxNew( 'oxvendorlist' );
            $oVendorTree->buildVendorTree( 'vendorlist', $sActVendor, $myConfig->getShopHomeURL() );

            $oParentView = $this->getParent();

            // setting active vendor list
            $oParentView->setVendorTree( $oVendorTree );

            // setting active vendor
            if ( ( $oVendor = $oVendorTree->getClickVendor() ) ) {
                $oParentView->setActVendor( $oVendor );
            }
        }
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
     * @return oxcategorylist
     */
    public function render()
    {
        parent::render();

        // Performance
        $myConfig = $this->getConfig();
        $oParentView = $this->getParent();

        //@deprecated in v.4.5.7, since 2012-02-15; config option removed bug #0003385
        if ( $myConfig->getConfigParam( 'bl_perfLoadVendorTree' ) &&
             ( $oVendorTree = $oParentView->getVendorTree() )) {
            $oParentView->setVendorlist( $oVendorTree );
            $oParentView->setRootVendor( $oVendorTree->getRootCat() );
        }

        if ( $myConfig->getConfigParam( 'bl_perfLoadManufacturerTree' ) &&
             ( $oManufacturerTree = $oParentView->getManufacturerTree() ) ) {
            $oParentView->setManufacturerlist( $oManufacturerTree );
            $oParentView->setRootManufacturer( $oManufacturerTree->getRootCat() );
        }

        if ( $myConfig->getConfigParam( 'bl_perfLoadCatTree' ) &&
             ( $oCategoryTree = $oParentView->getCategoryTree() ) ) {

            // we loaded full category tree ?
            if ( $myConfig->getConfigParam( 'bl_perfLoadTreeForSearch' ) ) {
                $oParentView->setSearchCatTree( $oCategoryTree );
            }

            // new navigation ?
            if ( $myConfig->getConfigParam( 'blTopNaviLayout' ) ) {
                $oParentView->setCatMore( $this->_oMoreCat );
            }

            return $oCategoryTree;
        }
    }

    /**
     * Generates fake top navigation category 'oxmore' and handles expanding
     *
     * @param string $sActCat  active category id
     * @param string $sActCont active template
     *
     * @return oxStdClass
     */
    protected function _getMoreCategory( $sActCat, $sActCont )
    {
        $myConfig = $this->getConfig();
        $blExpanded = false;

        if ( $sActCat == 'oxmore' ) {
            $blExpanded = true;
        } else {
            $iTopCount = $myConfig->getConfigParam( 'iTopNaviCatCount' );
            $oCategoryTree = $this->getParent()->getCategoryTree();
            if ( $oCategoryTree ) {
                $iCnt = 0;
                foreach ( $oCategoryTree as $oCat ) {
                    $iCnt++;

                    if ( ( $aContent = $oCat->getContentCats() ) ) {
                        foreach ( $aContent as $oContent ) {
                            if ( $sActCont == $oContent->getId() && ($iCnt > $iTopCount )) {
                                $blExpanded = true;
                                break 2;
                            }
                            $iCnt++;
                        }
                    }

                    if ( $oCat->getExpanded() && ($iCnt > $iTopCount )) {
                        $blExpanded = true;
                        break;
                    }
                }
            }
        }

        $oMoreCat = new oxStdClass();
        $oMoreCat->closelink = $oMoreCat->openlink = $myConfig->getShopHomeURL().'cnid=oxmore';
        $oMoreCat->expanded  = $blExpanded;
        return $oMoreCat;
    }

    /**
     * Adds additional parameters: active category, list type and category id
     *
     * @param oxarticle $oProduct         loaded product
     * @param string    $sActCat          active category id
     * @param string    $sActManufacturer active manufacturer id
     * @param string    $sActCont         active template
     * @param string    $sActTag          active tag
     * @param string    $sActVendor       active vendor
     *
     * @return string $sActCat
     */
    protected function _addAdditionalParams( $oProduct, $sActCat, $sActManufacturer, $sActCont, $sActTag, $sActVendor )
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
     * @param oxarticle $oProduct current product object
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
}
