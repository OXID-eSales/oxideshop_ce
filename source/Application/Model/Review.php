<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use OxidEsales\EshopCommunity\Internal\ServiceFactory\FacadeServiceFactory;

/**
 * Article review manager.
 * Performs loading, updating, inserting of article review.
 *
 */
class Review extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    /**
     * Shop control variable
     *
     * @var string
     */
    protected $_blDisableShopCheck = true;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxreview';

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxreviews');
    }

    /**
     * Calls parent::assign and assigns review writer data
     *
     * @param array $dbRecord database record
     *
     * @return bool
     */
    public function assign($dbRecord)
    {
        $blRet = parent::assign($dbRecord);

        if (isset($this->oxreviews__oxuserid) && $this->oxreviews__oxuserid->value) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $this->oxuser__oxfname = new \OxidEsales\Eshop\Core\Field($oDb->getOne("SELECT oxfname FROM oxuser WHERE oxid=" . $oDb->quote($this->oxreviews__oxuserid->value)));
        }

        return $blRet;
    }

    /**
     * Loads object review information. Returns true on success.
     *
     * @param string $oxId ID of object to load
     *
     * @return bool
     */
    public function load($oxId)
    {
        if ($blRet = parent::load($oxId)) {
            // convert date's to international format
            $this->oxreviews__oxcreate->setValue(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->formatDBDate($this->oxreviews__oxcreate->value));
        }

        return $blRet;
    }

    /**
     * Inserts object data fiels in DB. Returns true on success.
     *
     * @return bool
     */
    protected function _insert()
    {
        // set oxcreate
        $this->oxreviews__oxcreate = new \OxidEsales\Eshop\Core\Field(date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));

        return parent::_insert();
    }

    /**
     * get oxList of reviews for given object ids and type
     *
     * @param string  $sType       type of given ids
     * @param mixed   $aIds        given object ids to load, can be array or just one id, given as string
     * @param boolean $blLoadEmpty true if want to load empty text reviews
     * @param int     $iLoadInLang language to select for loading
     *
     * @return oxList
     */
    public function loadList($sType, $aIds, $blLoadEmpty = false, $iLoadInLang = null)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $oRevs = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $oRevs->init('oxreview');

        $sObjectIdWhere = '';
        if (is_array($aIds) && count($aIds)) {
            $sObjectIdWhere = "oxreviews.oxobjectid in ( " . implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aIds)) . " )";
        } elseif (is_string($aIds) && $aIds) {
            $sObjectIdWhere = "oxreviews.oxobjectid = " . $oDb->quote($aIds);
        } else {
            return $oRevs;
        }

        $iLoadInLang = is_null($iLoadInLang) ? (int) \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage() : (int) $iLoadInLang;

        $sSelect = "select oxreviews.* from oxreviews where oxreviews.oxtype = " . $oDb->quote($sType) . " and $sObjectIdWhere and oxreviews.oxlang = '$iLoadInLang'";

        if (!$blLoadEmpty) {
            $sSelect .= ' and oxreviews.oxtext != "" ';
        }

        if ($this->getConfig()->getConfigParam('blGBModerate')) {
            $sSelect .= ' and ( oxreviews.oxactive = "1" ';
            $sSelect .= ($oUser = $this->getUser()) ? 'or  oxreviews.oxuserid = ' . $oDb->quote($oUser->getId()) . ' )' : ')';
        }

        $sSelect .= ' order by oxreviews.oxcreate desc ';

        $oRevs->selectString($sSelect);

        // change date
        foreach ($oRevs as $oItem) {
            $oItem->oxreviews__oxcreate->convertToFormattedDbDate();
            $oItem->oxreviews__oxtext->convertToPseudoHtml();
        }

        return $oRevs;
    }

    /**
     * Retuns review object type
     *
     * @return string
     */
    public function getObjectType()
    {
        return $this->oxreviews__oxtype->value;
    }

    /**
     * Retuns review object id
     *
     * @return string
     */
    public function getObjectId()
    {
        return $this->oxreviews__oxobjectid->value;
    }

    /**
     * Returns ReviewAndRating list by User id.
     *
     * @param string $userId
     *
     * @return array
     */
    public function getReviewAndRatingListByUserId($userId)
    {
        return $this
            ->getFacadeServiceFactory()
            ->getUserReviewAndRatingFacade()
            ->getReviewAndRatingList($userId);
    }

    /**
     * @return FacadeServiceFactory
     */
    private function getFacadeServiceFactory()
    {
        return FacadeServiceFactory::getInstance();
    }
}
