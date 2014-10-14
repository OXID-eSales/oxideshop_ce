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
 * Article images gallery popup window.
 * If chosen article has more pictures there is ability to create
 * gallery of pictures.
 */
class MoreDetails extends Details
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'moredetails.tpl';

    /**
     * Current article id
     * @var string
     */
    protected $_sProductId = null;

    /**
     * Active picture id
     * @var string
     */
    protected $_sActPicId = null;

    /**
     * Article zoom pictures
     * @var array
     */
    protected $_aArtZoomPics = null;

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Template variable getter. Returns current product id
     *
     * @return string
     */
    public function getProductId()
    {
        if ( $this->_sProductId === null ) {
            $this->_sProductId = $this->getProduct()->getId();
        }
        return $this->_sProductId;
    }

    /**
     * Template variable getter. Returns active picture id
     *
     * @return string
     */
    public function getActPictureId()
    {
        if ( $this->_sActPicId === null ) {
            $this->_sActPicId = false;
            $aPicGallery = $this->getProduct()->getPictureGallery();

            if ( $aPicGallery['ZoomPic'] ) {
                $sActPicId = oxConfig::getParameter( 'actpicid' );
                $this->_sActPicId = $sActPicId ? $sActPicId : 1;
            }
        }
        return $this->_sActPicId;
    }

    /**
     * Template variable getter. Returns article zoom pictures
     *
     * @return array
     */
    public function getArtZoomPics()
    {
        if ( $this->_aArtZoomPics === null ) {
            $this->_aArtZoomPics = false;
            //Get picture gallery
            $aPicGallery = $this->getProduct()->getPictureGallery();
            $blArtPic = $aPicGallery['ZoomPic'];
            $aArtPics = $aPicGallery['ZoomPics'];

            if ( $blArtPic ) {
                $this->_aArtZoomPics = $aArtPics;
            }
        }
        return $this->_aArtZoomPics;
    }

    /**
     * Template variable getter. Returns active product
     *
     * @return oxArticle
     */
    public function getProduct()
    {
        if ( $this->_oProduct === null ) {
            $oArticle = oxNew( 'oxArticle' );
            $oArticle->load( oxConfig::getParameter( 'anid' ) );
            $this->_oProduct = $oArticle;
        }
        return $this->_oProduct;
    }
}
