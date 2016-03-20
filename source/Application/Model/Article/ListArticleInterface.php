<?php
namespace OxidEsales\Eshop\Application\Model\Article;

interface ListArticleInterface
{
    public function getArticleVat();
    public function getCategory();
    public function getCustomVAT();
    public function getItemKey();
    public function getLinkType();
    public function getParentArticle();
    public function getTPrice();
    public function getUnitName();
    public function getUnitPrice();
    public function getSize();
    public function getWeight();
    public function isVisible();
    public function getUnitQuantity();
    //public function getVariantSelections($aFilterIds = null, $sActVariantId = null, $iLimit = 0);
    public function getSelections($iLimit = null, $aFilter = null);
    //public function getSimpleVariants();
    public function getVendor($blShopCheck = true);
    public function getVendorId();
    public function getManufacturerId();
    public function getManufacturer($blShopCheck = true);
    public function getBasePrice($dAmount = 1);
    public function getVariantsCount();
    public function getPictureGallery();
    public function getLink($iLang = null, $blMain = false);
    public function getPictureUrl($iIndex = 1);
    public function getIconUrl($iIndex = 0);
    public function getVarMinPrice();
    public function hasMdVariants();
    public function isInList();
    public function isNotBuyable();
    public function isOnComparisonList();
    public function isParentNotBuyable();
    public function isRangePrice();
    public function isVariant();
    public function getLanguage();
    public function isMultilang();
    public function getAvailableInLangs();
    public function getId();
    public function getShopId();
    public function getMainLink($iLang = null);
    public function getThumbnailUrl($bSsl = null);
    public function getPrice($dAmount = 1);
    //
    public function getUseSkipSaveFields();
}