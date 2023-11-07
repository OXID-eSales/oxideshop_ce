<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\UserReviewAndRatingBridgeInterface;

class Review extends BaseModel
{
    /**
     * @var string
     */
    protected $_blDisableShopCheck = true;

    /**
     * @var string
     */
    protected $_sClassName = 'oxreview';

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
            $params = [
                ':oxid' => $this->oxreviews__oxuserid->value
            ];

            $firstName = $oDb->getOne("SELECT oxfname FROM oxuser 
                WHERE oxid = :oxid", $params);

            $this->oxuser__oxfname = new \OxidEsales\Eshop\Core\Field($firstName);
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
            $this->oxreviews__oxcreate->setValue(Registry::getUtilsDate()->formatDBDate($this->oxreviews__oxcreate->value));
        }

        return $blRet;
    }

    /**
     * Inserts object data fiels in DB. Returns true on success.
     *
     * @return bool
     */
    protected function insert()
    {
        // set oxcreate
        $this->oxreviews__oxcreate = new \OxidEsales\Eshop\Core\Field(date('Y-m-d H:i:s', Registry::getUtilsDate()->getTime()));

        return parent::insert();
    }

    /**
     * get oxList of reviews for given object ids and type
     *
     * @param string  $sType       type of given ids
     * @param mixed   $aIds        given object ids to load, can be array or just one id, given as string
     * @param boolean $blLoadEmpty true if want to load empty text reviews
     * @param int     $iLoadInLang language to select for loading
     *
     * @return \OxidEsales\Eshop\Core\Model\ListModel
     */
    public function loadList($sType, $aIds, $blLoadEmpty = false, $iLoadInLang = null)
    {
        $reviews = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $reviews->init('oxreview');

        $params = [
            ':oxtype' => $sType,
            ':oxlang' => is_null($iLoadInLang) ? (int) Registry::getLang()->getBaseLanguage() : (int) $iLoadInLang
        ];

        if (is_array($aIds) && count($aIds)) {
            $sObjectIdWhere = "oxreviews.oxobjectid in ( " . implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aIds)) . " )";
        } elseif (is_string($aIds) && $aIds) {
            $sObjectIdWhere = "oxreviews.oxobjectid = :oxobjectid";
            $params[':oxobjectid'] = $aIds;
        } else {
            return $reviews;
        }

        $sSelect = "select oxreviews.* from oxreviews where oxreviews.oxtype = :oxtype and $sObjectIdWhere and oxreviews.oxlang = :oxlang";

        if (!$blLoadEmpty) {
            $sSelect .= ' and oxreviews.oxtext != "" ';
        }

        if (Registry::getConfig()->getConfigParam('blGBModerate')) {
            $sSelect .= ' and ( oxreviews.oxactive = "1" ';

            if ($oUser = $this->getUser()) {
                $sSelect .= 'or  oxreviews.oxuserid = :oxuserid ';
                $params[':oxuserid'] = $oUser->getId();
            }

            $sSelect .= ')';
        }

        $sSelect .= ' order by oxreviews.oxcreate desc ';

        $reviews->selectString($sSelect, $params);

        foreach ($reviews as $review) {
            $reviewCreationDate = $review->oxreviews__oxcreate->getRawValue();
            $review->oxreviews__oxcreate->setValue(
                Registry::getUtilsDate()->formatDBDate($reviewCreationDate),
                Field::T_RAW
            );

            $reviewText = (string)$review->oxreviews__oxtext->value;
            $review->oxreviews__oxtext->setValue(
                $reviewText,
                Field::T_RAW
            );
        }

        return $reviews;
    }

    /**
     * Retuns review object type
     *
     * @return string
     */
    public function getObjectType()
    {
        return is_object($this->oxreviews__oxtype) ? $this->oxreviews__oxtype->value : $this->oxreviews__oxtype;
    }

    /**
     * Retuns review object id
     *
     * @return string
     */
    public function getObjectId()
    {
        return is_object($this->oxreviews__oxobjectid) ? $this->oxreviews__oxobjectid->value : $this->oxreviews__oxobjectid;
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
        return ContainerFacade::get(UserReviewAndRatingBridgeInterface::class)
            ->getReviewAndRatingList($userId);
    }
}
