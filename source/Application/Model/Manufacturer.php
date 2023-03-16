<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use OxidEsales\Eshop\Core\Contract\IUrl;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\MultiLanguageModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsFile;
use OxidEsales\Eshop\Core\UtilsPic;

/**
 * Manufacturer manager
 */
class Manufacturer extends MultiLanguageModel implements IUrl
{
    protected static $_aRootManufacturer = [];

    /**
     * @var string Name of current class
     */
    protected $_sClassName = 'oxmanufacturer';

    /**
     * Marker to load manufacturer article count info
     *
     * @var bool
     */
    protected $_blShowArticleCnt = false;

    /**
     * Manufacturer article count (default is -1, which means not calculated)
     *
     * @var int
     */
    protected $_iNrOfArticles = -1;

    /**
     * Marks that current object is managed by SEO
     *
     * @var bool
     */
    protected $_blIsSeoObject = true;

    /**
     * Visibility of a manufacturer
     *
     * @var int
     */
    protected $_blIsVisible;

    /**
     * has visible endors state of a category
     *
     * @var int
     */
    protected $_blHasVisibleSubCats;

    /**
     * Seo article urls for languages
     *
     * @var array
     */
    protected $_aSeoUrls = [];

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        $this->setShowArticleCnt(Registry::getConfig()->getConfigParam('bl_perfShowActionCatArticleCnt'));
        parent::__construct();
        $this->init('oxmanufacturers');
    }

    /**
     * Extra getter to guarantee compatibility with templates
     *
     * @param string $sName name of variable to return
     *
     * @return mixed
     */
    public function __get($sName)
    {
        switch ($sName) {
            case 'oxurl':
            case 'openlink':
            case 'closelink':
            case 'link':
                $sValue = $this->getLink();
                break;
            case 'iArtCnt':
                $sValue = $this->getNrOfArticles();
                break;
            case 'isVisible':
                $sValue = $this->getIsVisible();
                break;
            case 'hasVisibleSubCats':
                $sValue = $this->getHasVisibleSubCats();
                break;
            default:
                $sValue = parent::__get($sName);
                break;
        }
        return $sValue;
    }

    /**
     * Marker to load manufacturer article count info setter
     *
     * @param bool $blShowArticleCount Marker to load manufacturer article count
     */
    public function setShowArticleCnt($blShowArticleCount = false)
    {
        $this->_blShowArticleCnt = $blShowArticleCount;
    }

    /**
     * Assigns to $this object some base parameters/values.
     *
     * @param array $dbRecord parameters/values
     */
    public function assign($dbRecord)
    {
        parent::assign($dbRecord);

        // manufacturer article count is stored in cache
        if ($this->_blShowArticleCnt && !$this->isAdmin()) {
            $this->_iNrOfArticles = Registry::getUtilsCount()->getManufacturerArticleCount($this->getId());
        }

        $this->oxmanufacturers__oxnrofarticles = new Field($this->_iNrOfArticles, Field::T_RAW);
    }

    /**
     * Loads object data from DB (object data ID is passed to method). Returns
     * true on success.
     *
     * @param string $sOxid object id
     *
     * @return bool
     */
    public function load($sOxid)
    {
        if ($sOxid == 'root') {
            return $this->setRootObjectData();
        }

        return parent::load($sOxid);
    }

    /**
     * Sets root manufacturer data. Returns true
     *
     * @return bool
     */
    protected function setRootObjectData()
    {
        $this->setId('root');
        $this->oxmanufacturers__oxtitle = new Field(
            Registry::getLang()->translateString('BY_MANUFACTURER', $this->getLanguage(), false),
            Field::T_RAW
        );
        $this->oxmanufacturers__oxshortdesc = new Field('', Field::T_RAW);

        $this->oxmanufacturers__oxicon = new Field('', Field::T_RAW);
        $this->oxmanufacturers__oxicon_alt = new Field('', Field::T_RAW);
        $this->oxmanufacturers__oxpicture = new Field('', Field::T_RAW);
        $this->oxmanufacturers__oxthumbnail = new Field('', Field::T_RAW);
        $this->oxmanufacturers__oxpromotion_icon = new Field('', Field::T_RAW);

        return true;
    }

    /**
     * Returns raw manufacturer seo url
     *
     * @param int $iLang language id
     * @param int $iPage page number [optional]
     *
     * @return string
     */
    public function getBaseSeoLink($iLang, $iPage = 0)
    {
        $oEncoder = Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class);
        if (!$iPage) {
            return $oEncoder->getManufacturerUrl($this, $iLang);
        }

        return $oEncoder->getManufacturerPageUrl($this, $iPage, $iLang);
    }

    /**
     * Returns manufacturer link Url
     *
     * @param int $iLang language id [optional]
     *
     * @return string
     */
    public function getLink($iLang = null)
    {
        if (!Registry::getUtils()->seoIsActive()) {
            return $this->getStdLink($iLang);
        }

        if ($iLang === null) {
            $iLang = $this->getLanguage();
        }

        if (!isset($this->_aSeoUrls[$iLang])) {
            $this->_aSeoUrls[$iLang] = $this->getBaseSeoLink($iLang);
        }

        return $this->_aSeoUrls[$iLang];
    }

    /**
     * Returns base dynamic url: shopurl/index.php?cl=details
     *
     * @param int  $iLang   language id
     * @param bool $blAddId add current object id to url or not
     * @param bool $blFull  return full including domain name [optional]
     *
     * @return string
     */
    public function getBaseStdLink($iLang, $blAddId = true, $blFull = true)
    {
        $sUrl = '';
        if ($blFull) {
            //always returns shop url, not admin
            $sUrl = Registry::getConfig()->getShopUrl($iLang, false);
        }

        return $sUrl . "index.php?cl=manufacturerlist" . ($blAddId ? "&amp;mnid=" . $this->getId() : "");
    }

    /**
     * Returns standard URL to manufacturer
     *
     * @param int   $iLang   language
     * @param array $aParams additional params to use [optional]
     *
     * @return string
     */
    public function getStdLink($iLang = null, $aParams = [])
    {
        if ($iLang === null) {
            $iLang = $this->getLanguage();
        }

        return Registry::getUtilsUrl()->processUrl($this->getBaseStdLink($iLang), true, $aParams, $iLang);
    }

    /**
     * returns number or articles of this manufacturer
     *
     * @return integer
     */
    public function getNrOfArticles()
    {
        if (!$this->_blShowArticleCnt || $this->isAdmin()) {
            return -1;
        }

        return $this->_iNrOfArticles;
    }

    /**
     * returns the sub category array
     */
    public function getSubCats()
    {
    }

    /**
     * returns the visibility of a manufacturer
     *
     * @return bool
     */
    public function getIsVisible()
    {
        return $this->_blIsVisible;
    }

    /**
     * sets the visibilty of a category
     *
     * @param bool $blVisible manufacturers visibility status setter
     */
    public function setIsVisible($blVisible)
    {
        $this->_blIsVisible = $blVisible;
    }

    /**
     * returns if a manufacturer has visible sub categories
     *
     * @return bool
     */
    public function getHasVisibleSubCats()
    {
        if (!isset($this->_blHasVisibleSubCats)) {
            $this->_blHasVisibleSubCats = false;
        }

        return $this->_blHasVisibleSubCats;
    }

    /**
     * sets the state of has visible sub manufacturers
     *
     * @param bool $blHasVisibleSubcats marker if manufacturer has visible subcategories
     */
    public function setHasVisibleSubCats($blHasVisibleSubcats)
    {
        $this->_blHasVisibleSubCats = $blHasVisibleSubcats;
    }

    /**
     * Empty method, called in templates when manufacturer is used in same code like category
     */
    public function getContentCats()
    {
    }

    /**
     * Delete this object from the database, returns true on success.
     *
     * @param string $oxid Object ID(default null)
     *
     * @return bool
     */
    public function delete($oxid = null)
    {
        if ($oxid) {
            $this->load($oxid);
        } else {
            $oxid = $this->getId();
        }

        if (parent::delete($oxid)) {
            Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderManufacturer::class)
                ->onDeleteManufacturer($this);

            $this->deletePicture($this->oxmanufacturers__oxicon->value, 'MICO', 'oxicon');
            $this->deletePicture($this->oxmanufacturers__oxicon_alt->value, 'MICO', 'oxicon_alt');
            $this->deletePicture($this->oxmanufacturers__oxpicture->value, 'MPIC', 'oxpicture');
            $this->deletePicture($this->oxmanufacturers__oxthumbnail->value, 'MTHU', 'oxthumbnail');
            $this->deletePicture($this->oxmanufacturers__oxpromotion_icon->value, 'MPICO', 'oxpromotion_icon');

            return true;
        }

        return false;
    }

    /**
     * Returns manufacture icon
     *
     * @return string
     */
    public function getIconUrl()
    {
        $imageName = $this->oxmanufacturers__oxicon->value;

        return $this->getImageUrl($imageName, 'sManufacturerIconsize', 'icon') ?? '';
    }

    public function getIconAltUrl(): bool|string
    {
        $imageName = $this->oxmanufacturers__oxicon_alt->value;

        return $this->getImageUrl($imageName, 'sManufacturerIconsize', 'icon') ?? '';
    }

    public function getPictureUrl(): bool|string
    {
        $imageName = $this->oxmanufacturers__oxpicture->value;

        return $this->getImageUrl($imageName, 'sManufacturerPicturesize', 'picture') ?? '';
    }

    public function getThumbnailUrl(): bool|string
    {
        $imageName = $this->oxmanufacturers__oxthumbnail->value;

        return $this->getImageUrl($imageName, 'sManufacturerThumbnailsize', 'thumb') ?? '';
    }

    public function getPromotionIconUrl(): bool|string
    {
        $imageName = $this->oxmanufacturers__oxpromotion_icon->value;

        return $this->getImageUrl($imageName, 'sManufacturerPromotionsize', 'promo_icon') ?? '';
    }

    /**
     * Returns false, because manufacturer has not thumbnail
     * @return false
     * @deprecated since v7.0.0 (2023-03-16). Please use Manufacturer::getThumbnailUrl() instead.
     */
    public function getThumbUrl()
    {
        return false;
    }

    /**
     * Returns manufacturer title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->oxmanufacturers__oxtitle->value;
    }

    /**
     * Returns short description
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->oxmanufacturers__oxshortdesc->value;
    }

    public function deletePicture(string $pictureName, string $pictureType, string $pictureFieldName): void
    {
        /** @var UtilsPic $utilsPic */
        $utilsPic = Registry::getUtilsPic();
        /** @var UtilsFile $utilsFile */
        $utilsFile = Registry::getUtilsFile();
        $pictureDirectory = Registry::getConfig()->getPictureDir(false);

        $utilsPic->safePictureDelete(
            $pictureName,
            $pictureDirectory . $utilsFile->getImageDirByType($pictureType),
            'oxmanufacturers',
            $pictureFieldName
        );
    }

    private function getImageUrl(mixed $imageName, string $paramName, string $directoryName): string|bool|null
    {
        $config = Registry::getConfig();
        $size = $config->getConfigParam($paramName) ?? $config->getConfigParam('sIconsize');
        $path = 'manufacturer/' . $directoryName . '/';

        return Registry::getPictureHandler()->getPicUrl($path, $imageName, $size);
    }
}
