<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxField;
use oxDb;

/**
 * Content manager.
 * Base object for content pages
 *
 */
class Content extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel implements \OxidEsales\Eshop\Core\Contract\IUrl
{
    /**
     * Current class name.
     *
     * @var string
     */
    protected $_sClassName = 'oxcontent';

    /**
     * Seo article urls for languages.
     *
     * @var array
     */
    protected $_aSeoUrls = [];

    /**
     * Content parent category id
     *
     * @var string
     */
    protected $_sParentCatId = null;

    /**
     * Expanded state of a content category.
     *
     * @var bool
     */
    protected $_blExpanded = null;

    /**
     * Marks that current object is managed by SEO.
     *
     * @var bool
     */
    protected $_blIsSeoObject = true;

    /**
     * Category id.
     *
     * @var string
     */
    protected $_sCategoryId;

    /**
     * Extra getter to guarantee compatibility with templates.
     *
     * @param string $sName parameter name
     *
     * @return mixed
     */
    public function __get($sName)
    {
        switch ($sName) {
            case 'expanded':
                return $this->getExpanded();
                break;
        }
        return parent::__get($sName);
    }

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxcontents');
    }

    /**
     * Returns the expanded state of the content category.
     *
     * @return bool
     */
    public function getExpanded()
    {
        if (!isset($this->_blExpanded)) {
            $this->_blExpanded = ($this->getId() == \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxcid'));
        }

        return $this->_blExpanded;
    }

    /**
     * Sets category id.
     *
     * @param string $sCategoryId
     */
    public function setCategoryId($sCategoryId)
    {
        $this->oxcontents__oxcatid = new \OxidEsales\Eshop\Core\Field($sCategoryId);
    }

    /**
     * Returns category id.
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->oxcontents__oxcatid->value;
    }

    /**
     * Get data from db.
     *
     * @param string $sLoadId id
     *
     * @return array
     */
    protected function _loadFromDb($sLoadId)
    {
        $sTable = $this->getViewName();
        $sShopId = $this->getShopId();
        $aParams = [$sTable . '.oxloadid' => $sLoadId, $sTable . '.oxshopid' => $sShopId];

        $sSelect = $this->buildSelectString($aParams);

        //Loads "credits" content object and its text (first available)
        if ($sLoadId == 'oxcredits') {
            // fetching column names
            $sColQ = "SHOW COLUMNS FROM oxcontents WHERE field LIKE  'oxcontent%'";
            $aCols = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getAll($sColQ);

            // building subquery
            $sPattern = "IF ( %s != '', %s, %s ) ";
            $iCount = count($aCols) - 1;

            $sContQ = "SELECT {$sPattern}";
            foreach ($aCols as $iKey => $aCol) {
                $sContQ = sprintf($sContQ, $aCol[0], $aCol[0], $iCount != $iKey ? $sPattern : "''");
            }
            $sContQ .= " FROM oxcontents WHERE oxloadid = '{$sLoadId}' AND oxshopid = '{$sShopId}'";

            $sSelect = $this->buildSelectString($aParams);
            $sSelect = str_replace("`{$sTable}`.`oxcontent`", "( $sContQ ) as oxcontent", $sSelect);
        }

        $aData = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC)->getRow($sSelect);

        return $aData;
    }

    /**
     * Loads Content by using field oxloadid instead of oxid.
     *
     * @param string $loadId     content load ID
     * @param string $onlyActive selection state - active/inactive
     *
     * @return bool
     */
    public function loadByIdent($loadId, $onlyActive = false)
    {
        return $this->assignContentData($this->_loadFromDb($loadId), $onlyActive);
    }

    /**
     * Assign content data, filter inactive if needed.
     *
     * @param array $fetchedContent Item data to assign
     * @param bool  $onlyActive     Only assign if item is active
     *
     * @return bool
     */
    protected function assignContentData($fetchedContent, $onlyActive = false)
    {
        $filteredContent = $this->filterInactive($fetchedContent, $onlyActive);

        if (!is_null($filteredContent)) {
            $this->assign($filteredContent);
            return true;
        }

        return false;
    }

    /**
     * Decide if content item can be loaded by checking item activity if needed
     *
     * @param array $data
     * @param bool  $checkIfActive
     *
     * @return array | null
     */
    protected function filterInactive($data, $checkIfActive = false)
    {
        return $data && (!$checkIfActive || ($checkIfActive && $data['OXACTIVE']) == '1') ? $data : null;
    }

    /**
     * Returns unique object id.
     *
     * @return string
     */
    public function getLoadId()
    {
        return $this->oxcontents__oxloadid->value;
    }

    /**
     * Returns unique object id.
     *
     * @return string
     */
    public function isActive()
    {
        return $this->oxcontents__oxactive->value;
    }

    /**
     * Replace the "&amp;" into "&" and call base class.
     *
     * @param array $dbRecord database record
     */
    public function assign($dbRecord)
    {
        parent::assign($dbRecord);
        // workaround for firefox showing &lang= as &9001;= entity, mantis#0001272

        if ($this->oxcontents__oxcontent) {
            $this->oxcontents__oxcontent->setValue(str_replace('&lang=', '&amp;lang=', $this->oxcontents__oxcontent->value), \OxidEsales\Eshop\Core\Field::T_RAW);
        }
    }

    /**
     * Returns raw content seo url
     *
     * @param int $iLang language id
     *
     * @return string
     */
    public function getBaseSeoLink($iLang)
    {
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderContent::class)->getContentUrl($this, $iLang);
    }

    /**
     * getLink returns link for this content in the frontend.
     *
     * @param int $iLang language id [optional]
     *
     * @return string
     */
    public function getLink($iLang = null)
    {
        if (!\OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive()) {
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

        if ($this->oxcontents__oxloadid->value === 'oxcredits') {
            $sUrl .= "index.php?cl=credits";
        } else {
            $sUrl .= "index.php?cl=content";
        }
        $sUrl .= '&amp;oxloadid=' . $this->getLoadId();

        if ($blAddId) {
            $sUrl .= "&amp;oxcid=" . $this->getId();
            // adding parent category if if available
            if ($this->_sParentCatId !== false && $this->oxcontents__oxcatid->value && $this->oxcontents__oxcatid->value != 'oxrootid') {
                if ($this->_sParentCatId === null) {
                    $this->_sParentCatId = false;
                    $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
                    $sParentId = $oDb->getOne("select oxparentid from oxcategories where oxid = :oxid", [
                        ':oxid' => $this->oxcontents__oxcatid->value
                    ]);
                    if ($sParentId && 'oxrootid' != $sParentId) {
                        $this->_sParentCatId = $sParentId;
                    }
                }

                if ($this->_sParentCatId) {
                    $sUrl .= "&amp;cnid=" . $this->_sParentCatId;
                }
            }
        }

        //always returns shop url, not admin
        return $sUrl;
    }

    /**
     * Returns standard URL to product.
     *
     * @param integer $iLang   language
     * @param array   $aParams additional params to use [optional]
     *
     * @return string
     */
    public function getStdLink($iLang = null, $aParams = [])
    {
        if ($iLang === null) {
            $iLang = $this->getLanguage();
        }

        return \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl($this->getBaseStdLink($iLang), true, $aParams, $iLang);
    }

    /**
     * Sets data field value.
     *
     * @param string $sFieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $sValue     value of data field
     * @param int    $iDataType  field type
     *
     * @return null
     */
    protected function _setFieldData($sFieldName, $sValue, $iDataType = \OxidEsales\Eshop\Core\Field::T_TEXT)
    {
        $sLoweredFieldName = strtolower($sFieldName);
        if ('oxcontent' === $sLoweredFieldName || 'oxcontents__oxcontent' === $sLoweredFieldName) {
            $iDataType = \OxidEsales\Eshop\Core\Field::T_RAW;
        }

        return parent::_setFieldData($sFieldName, $sValue, $iDataType);
    }

    /**
     * Get field data
     *
     * @param string $sFieldName name of the field which value to get
     *
     * @return mixed
     */
    protected function _getFieldData($sFieldName)
    {
        return $this->{$sFieldName}->value;
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
        if (!$sOXID) {
            $sOXID = $this->getId();
        }

        if (parent::delete($sOXID)) {
            \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderContent::class)->onDeleteContent($sOXID);

            return true;
        }

        return false;
    }

    /**
     * Save this Object to database, insert or update as needed.
     *
     * @return mixed
     */
    public function save()
    {
        $blSaved = parent::save();
        if ($blSaved && $this->oxcontents__oxloadid->value === 'oxagb') {
            $sShopId = $this->getConfig()->getShopId();
            $sVersion = $this->oxcontents__oxtermversion->value;

            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            // dropping expired..
            $oDb->execute("delete from oxacceptedterms where oxshopid = :oxshopid and oxtermversion != :notoxtermversion", [
                ':oxshopid' => $sShopId,
                ':notoxtermversion' => $sVersion
            ]);
        }

        return $blSaved;
    }

    /**
     * Returns latest terms version id.
     *
     * @return string
     */
    public function getTermsVersion()
    {
        if ($this->loadByIdent('oxagb')) {
            return $this->oxcontents__oxtermversion->value;
        }
    }

    /**
     * Set type of content.
     *
     * @param string $sValue type value
     */
    public function setType($sValue)
    {
        $this->_setFieldData('oxcontents__oxtype', $sValue);
    }

    /**
     * Return type of content
     *
     * @return integer
     */
    public function getType()
    {
        return (int) $this->_getFieldData('oxcontents__oxtype');
    }

    /**
     * Set title of content
     *
     * @param string $sValue title value
     */
    public function setTitle($sValue)
    {
        $this->_setFieldData('oxcontents__oxtitle', $sValue);
    }

    /**
     * Return title of content
     *
     * @return string
     */
    public function getTitle()
    {
        return (string) $this->_getFieldData('oxcontents__oxtitle');
    }
}
