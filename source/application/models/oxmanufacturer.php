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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Manufacturer manager
 *
 */
class oxManufacturer extends oxI18n implements oxIUrl
{

    protected static $_aRootManufacturer = array();

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
    protected $_aSeoUrls = array();

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        $this->setShowArticleCnt($this->getConfig()->getConfigParam('bl_perfShowActionCatArticleCnt'));
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
            $this->_iNrOfArticles = oxRegistry::get("oxUtilsCount")->getManufacturerArticleCount($this->getId());
        }

        $this->oxmanufacturers__oxnrofarticles = new oxField($this->_iNrOfArticles, oxField::T_RAW);
    }

    /**
     * Loads object data from DB (object data ID is passed to method). Returns
     * true on success.
     *
     * @param string $sOxid object id
     *
     * @return oxmanufacturer
     */
    public function load($sOxid)
    {
        if ($sOxid == 'root') {
            return $this->_setRootObjectData();
        }

        return parent::load($sOxid);
    }

    /**
     * Sets root manufacturer data. Returns true
     *
     * @return bool
     */
    protected function _setRootObjectData()
    {
        $this->setId('root');
        $this->oxmanufacturers__oxicon = new oxField('', oxField::T_RAW);
        $this->oxmanufacturers__oxtitle = new oxField(oxRegistry::getLang()->translateString('BY_MANUFACTURER', $this->getLanguage(), false), oxField::T_RAW);
        $this->oxmanufacturers__oxshortdesc = new oxField('', oxField::T_RAW);

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
        $oEncoder = oxRegistry::get("oxSeoEncoderManufacturer");
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
        if (!oxRegistry::getUtils()->seoIsActive()) {
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
            $sUrl = $this->getConfig()->getShopUrl($iLang, false);
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
    public function getStdLink($iLang = null, $aParams = array())
    {
        if ($iLang === null) {
            $iLang = $this->getLanguage();
        }

        return oxRegistry::get("oxUtilsUrl")->processUrl($this->getBaseStdLink($iLang), true, $aParams, $iLang);
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
     * @param string $sOXID Object ID(default null)
     *
     * @return bool
     */
    public function delete($sOXID = null)
    {
        if (parent::delete($sOXID)) {
            oxRegistry::get("oxSeoEncoderManufacturer")->onDeleteManufacturer($this);

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
        if (($sIcon = $this->oxmanufacturers__oxicon->value)) {
            $oConfig = $this->getConfig();
            $sSize = $oConfig->getConfigParam('sManufacturerIconsize');
            if (!$sSize) {
                $sSize = $oConfig->getConfigParam('sIconsize');
            }

            return oxRegistry::get("oxPictureHandler")->getPicUrl("manufacturer/icon/", $sIcon, $sSize);
        }
    }

    /**
     * Returns false, becouse manufacturer has not thumbnail
     *
     * @return false
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

}
