<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;
use oxField;
use oxRegistry;
use oxUtilsUrl;
use oxUtilsView;
use oxUtilsFile;

/**
 * Article actions manager. Collects and keeps actions of chosen article.
 *
 */
class Actions extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = "oxactions";

    /**
     * Class constructor. Executes oxActions::init(), initiates parent constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->init("oxactions");
    }

    /**
     * Adds an article to this actions
     *
     * @param string $articleId id of the article to be added
     */
    public function addArticle($articleId)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "select max(oxsort) 
                from oxactions2article 
                where oxactionid = :oxactionid and oxshopid = :oxshopid";

        $params = [
            ':oxactionid' => $this->getId(),
            ':oxshopid' => $this->getShopId()
        ];
        $iSort = ((int)$oDb->getOne($sQ, $params)) + 1;

        $oNewGroup = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
        $oNewGroup->init('oxactions2article');
        $oNewGroup->oxactions2article__oxshopid = new \OxidEsales\Eshop\Core\Field($this->getShopId());
        $oNewGroup->oxactions2article__oxactionid = new \OxidEsales\Eshop\Core\Field($this->getId());
        $oNewGroup->oxactions2article__oxartid = new \OxidEsales\Eshop\Core\Field($articleId);
        $oNewGroup->oxactions2article__oxsort = new \OxidEsales\Eshop\Core\Field($iSort);
        $oNewGroup->save();
    }

    /**
     * Removes an article from this actions
     *
     * @param string $articleId id of the article to be removed
     *
     * @return bool
     */
    public function removeArticle($articleId)
    {
        // remove actions from articles also
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sDelete = "delete from oxactions2article where oxactionid = :oxactionid and oxartid = :oxartid and oxshopid = :oxshopid";
        $iRemovedArticles = $oDb->execute($sDelete, [
            ':oxactionid' => $this->getId(),
            ':oxartid' => $articleId,
            ':oxshopid' => $this->getShopId()
        ]);

        return (bool) $iRemovedArticles;
    }

    /**
     * Removes article action, returns true on success. For
     * performance - you can not load action object - just pass
     * action ID.
     *
     * @param string $articleId Object ID
     *
     * @return bool
     */
    public function delete($articleId = null)
    {
        $articleId = $articleId ? $articleId : $this->getId();
        if (!$articleId) {
            return false;
        }

        // remove actions from articles also
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sDelete = "delete from oxactions2article where oxactionid = :oxactionid and oxshopid = :oxshopid";
        $oDb->execute($sDelete, [
            ':oxactionid' => $articleId,
            ':oxshopid' => $this->getShopId()
        ]);

        return parent::delete($articleId);
    }

    /**
     * return time left until finished
     *
     * @return int
     */
    public function getTimeLeft()
    {
        $iNow = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
        $iFrom = strtotime($this->oxactions__oxactiveto->value);

        return $iFrom - $iNow;
    }

    /**
     * return time left until start
     *
     * @return int
     */
    public function getTimeUntilStart()
    {
        $iNow = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
        $iFrom = strtotime($this->oxactions__oxactivefrom->value);

        return $iFrom - $iNow;
    }

    /**
     * start the promotion NOW!
     */
    public function start()
    {
        $this->oxactions__oxactivefrom = new \OxidEsales\Eshop\Core\Field(date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));
        if ($this->oxactions__oxactiveto->value && ($this->oxactions__oxactiveto->value != '0000-00-00 00:00:00')) {
            $iNow = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
            $iTo = strtotime($this->oxactions__oxactiveto->value);
            if ($iNow > $iTo) {
                $this->oxactions__oxactiveto = new \OxidEsales\Eshop\Core\Field('0000-00-00 00:00:00');
            }
        }
        $this->save();
    }

    /**
     * stop the promotion NOW!
     */
    public function stop()
    {
        $this->oxactions__oxactiveto = new \OxidEsales\Eshop\Core\Field(date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));
        $this->save();
    }

    /**
     * check if this action is active
     *
     * @return bool
     */
    public function isRunning()
    {
        if (!(
            $this->oxactions__oxactive->value
              && $this->oxactions__oxtype->value == 2
              && $this->oxactions__oxactivefrom->value != '0000-00-00 00:00:00'
        )
        ) {
            return false;
        }
        $iNow = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
        $iFrom = strtotime($this->oxactions__oxactivefrom->value);
        if ($iNow < $iFrom) {
            return false;
        }

        if ($this->oxactions__oxactiveto->value != '0000-00-00 00:00:00') {
            $iTo = strtotime($this->oxactions__oxactiveto->value);
            if ($iNow > $iTo) {
                return false;
            }
        }

        return true;
    }

    /**
     * get long description, parsed through smarty
     *
     * @return string
     */
    public function getLongDesc()
    {
        /** @var \OxidEsales\Eshop\Core\UtilsView $oUtilsView */
        $oUtilsView = \OxidEsales\Eshop\Core\Registry::getUtilsView();
        return $oUtilsView->parseThroughSmarty($this->oxactions__oxlongdesc->getRawValue(), $this->getId() . $this->getLanguage(), null, true);
    }

    /**
     * return assigned banner article
     *
     * @return oxArticle
     */
    public function getBannerArticle()
    {
        $sArtId = $this->fetchBannerArticleId();

        if ($sArtId) {
            $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

            if ($this->isAdmin()) {
                $oArticle->setLanguage(\OxidEsales\Eshop\Core\Registry::getLang()->getEditLanguage());
            }

            if ($oArticle->load($sArtId)) {
                return $oArticle;
            }
        }

        return null;
    }


    /**
     * Fetch the oxobjectid of the article corresponding this action.
     *
     * @return string The id of the oxobjectid belonging to this action.
     */
    protected function fetchBannerArticleId()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $articleId = $database->getOne(
            'select oxobjectid from oxobject2action ' .
            'where oxactionid = :oxactionid and oxclass = :oxclass',
            [
                ':oxactionid' => $this->getId(),
                ':oxclass' => 'oxarticle'
            ]
        );

        return $articleId;
    }

    /**
     * Returns assigned banner article picture url
     *
     * @return string
     */
    public function getBannerPictureUrl()
    {
        if (isset($this->oxactions__oxpic) && $this->oxactions__oxpic->value) {
            $sPromoDir = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->normalizeDir(\OxidEsales\Eshop\Core\UtilsFile::PROMO_PICTURE_DIR);

            return $this->getConfig()->getPictureUrl($sPromoDir . $this->oxactions__oxpic->value, false);
        }
    }

    /**
     * Returns assigned banner link. If no link is defined and article is
     * assigned to banner, article link will be returned.
     *
     * @return string
     */
    public function getBannerLink()
    {
        $sUrl = null;

        if (isset($this->oxactions__oxlink) && $this->oxactions__oxlink->value) {
            /** @var \OxidEsales\Eshop\Core\UtilsUrl $oUtilsUlr */
            $oUtilsUlr = \OxidEsales\Eshop\Core\Registry::getUtilsUrl();
            $sUrl = $oUtilsUlr->addShopHost($this->oxactions__oxlink->value);
            $sUrl = $oUtilsUlr->processUrl($sUrl);
        } else {
            if ($oArticle = $this->getBannerArticle()) {
                // if article is assigned to banner, getting article link
                $sUrl = $oArticle->getLink();
            }
        }

        return $sUrl;
    }

    /**
     * Returns true if Action is default.
     *
     * @return bool
     */
    public function isDefault()
    {
        return '0' === $this->oxactions__oxtype->value;
    }
}
