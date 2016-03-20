<?php

namespace OxidEsales\Eshop\Application\Model\Article;

class ListArticle implements ListArticleInterface
{
    public function getLanguage()
    {
        return $this->data[__FUNCTION__];
    }

    public function getMainLink($iLang = null)
    {
        return $this->data[__FUNCTION__];
    }

    public function getThumbnailUrl($bSsl = null)
    {
        return $this->data[__FUNCTION__];
    }

    public function getPrice($dAmount = 1)
    {
        return $this->data[__FUNCTION__];
    }

    public function isMultilang()
    {
        return $this->data[__FUNCTION__];
    }

    public function getAvailableInLangs()
    {
        return $this->data[__FUNCTION__];
    }

    public function getId()
    {
        return $this->data[__FUNCTION__];
    }

    public function getShopId()
    {
        return $this->data[__FUNCTION__];
    }

    public function getUseSkipSaveFields()
    {
        return $this->data[__FUNCTION__];
    }

    public function getSize()
    {
        return $this->data[__FUNCTION__];
    }

    public function getWeight()
    {
        return $this->data[__FUNCTION__];
    }

    public function isVisible()
    {
        return $this->data[__FUNCTION__];
    }

    public function getVariantSelections($aFilterIds = null, $sActVariantId = null, $iLimit = 0)
    {
        return $this->data[__FUNCTION__];
    }

    public function getSelections($iLimit = null, $aFilter = null)
    {
        return $this->data[__FUNCTION__];
    }

    public function getSimpleVariants()
    {
        return $this->data[__FUNCTION__];
    }

    public function getVendor($blShopCheck = true)
    {
        return $this->data[__FUNCTION__];
    }

    public function getVendorId()
    {
        return $this->data[__FUNCTION__];
    }

    public function getManufacturerId()
    {
        return $this->data[__FUNCTION__];
    }

    public function getManufacturer($blShopCheck = true)
    {
        return $this->data[__FUNCTION__];
    }

    public function getBasePrice($dAmount = 1)
    {
        return $this->data[__FUNCTION__];
    }

    public function getPictureGallery()
    {
        return $this->data[__FUNCTION__];
    }

    public function getLink($iLang = null, $blMain = false)
    {
        return $this->data[__FUNCTION__];
    }

    public function getPictureUrl($iIndex = 1)
    {
        return $this->data[__FUNCTION__];
    }

    public function getIconUrl($iIndex = 0)
    {
        return $this->data[__FUNCTION__];
    }

    public function __call($method, $params)
    {
        $method = $method;
    }

    public function __get($field)
    {
        $field = str_replace('OXARTICLES__', '', strtoupper($field));
        return new \oxField($this->origData[$field]);
    }

    public function getViewName()
    {
        return getViewName('oxarticles');
    }

    public function getSqlActiveSnippet()
    {
        return 1;
    }

    private $data;
    private $origData;

    public function load($id)
    {
        $data = \oxDb::getDb(\oxDb::FETCH_MODE_ASSOC)->getRow(
            sprintf(
                'SELECT data FROM list_article WHERE id = %s',
                \oxDb::getDb()->quote($id)
            )
        );
        $this->data = json_decode(reset($data), true);

        $origData = \oxDb::getDb(\oxDb::FETCH_MODE_ASSOC)->getRow(
            sprintf(
                'SELECT * FROM oxarticles WHERE oxid = %s',
                \oxDb::getDb(\oxDb::FETCH_MODE_ASSOC)->quote($id)
            )
        );

        $this->origData = $origData;

        $this->oxarticles__oxnid = $this->oxarticles__oxid;
    }

    public function getArticleVat()
    {
        return $this->data[__FUNCTION__];
    }

    public function getCategory()
    {

        return oxNew("oxCategory");
        return $this->data[__FUNCTION__];
    }

    public function getCustomVAT()
    {
        return $this->data[__FUNCTION__];
    }

    public function getItemKey()
    {
        return $this->data[__FUNCTION__];
    }

    public function getLinkType()
    {
        return $this->data[__FUNCTION__];
    }

    public function getParentArticle()
    {
        return $this->data[__FUNCTION__];
    }

    public function getTPrice()
    {
        return $this->data[__FUNCTION__];
    }

    public function getUnitName()
    {
        return $this->data[__FUNCTION__];
    }

    public function getUnitPrice()
    {
        return $this->data[__FUNCTION__];
    }

    public function getUnitQuantity()
    {
        return $this->data[__FUNCTION__];
    }

    public function getVariantsCount()
    {
        return $this->data[__FUNCTION__];
    }

    public function getVarMinPrice()
    {
        return $this->data[__FUNCTION__];
    }

    public function hasMdVariants()
    {
        return $this->data[__FUNCTION__];
    }

    public function isInList()
    {
        return $this->data[__FUNCTION__];
    }

    public function isNotBuyable()
    {
        return $this->data[__FUNCTION__];
    }

    public function isOnComparisonList()
    {
        return $this->data[__FUNCTION__];
    }

    public function isParentNotBuyable()
    {
        return $this->data[__FUNCTION__];
    }

    public function isRangePrice()
    {
        return $this->data[__FUNCTION__];
    }

    public function isVariant()
    {
        return $this->data[__FUNCTION__];
    }
}
