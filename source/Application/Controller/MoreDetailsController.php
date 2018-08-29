<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxRegistry;

/**
 * Article images gallery popup window.
 * If chosen article has more pictures there is ability to create
 * gallery of pictures.
 */
class MoreDetailsController extends \OxidEsales\Eshop\Application\Controller\ArticleDetailsController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'moredetails.tpl';

    /**
     * Current article id
     *
     * @var string
     */
    protected $_sProductId = null;

    /**
     * Active picture id
     *
     * @var string
     */
    protected $_sActPicId = null;

    /**
     * Article zoom pictures
     *
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
        if ($this->_sProductId === null) {
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
        if ($this->_sActPicId === null) {
            $this->_sActPicId = false;
            $aPicGallery = $this->getProduct()->getPictureGallery();

            if ($aPicGallery['ZoomPic']) {
                $sActPicId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('actpicid');
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
        if ($this->_aArtZoomPics === null) {
            $this->_aArtZoomPics = false;
            //Get picture gallery
            $aPicGallery = $this->getProduct()->getPictureGallery();
            $blArtPic = $aPicGallery['ZoomPic'];
            $aArtPics = $aPicGallery['ZoomPics'];

            if ($blArtPic) {
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
        if ($this->_oProduct === null) {
            $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $oArticle->load(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('anid'));
            $this->_oProduct = $oArticle;
        }

        return $this->_oProduct;
    }
}
