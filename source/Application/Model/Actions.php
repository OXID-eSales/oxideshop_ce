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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\Eshop\Application\Model;

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
class Actions extends \oxI18n
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
        $oDb = oxDb::getDb();
        $sQ = "select max(oxsort) from oxactions2article where oxactionid = " . $oDb->quote($this->getId()) . " and oxshopid = '" . $this->getShopId() . "'";
        $iSort = ((int) $oDb->getOne($sQ)) + 1;

        $oNewGroup = oxNew('oxBase');
        $oNewGroup->init('oxactions2article');
        $oNewGroup->oxactions2article__oxshopid = new oxField($this->getShopId());
        $oNewGroup->oxactions2article__oxactionid = new oxField($this->getId());
        $oNewGroup->oxactions2article__oxartid = new oxField($articleId);
        $oNewGroup->oxactions2article__oxsort = new oxField($iSort);
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
        $oDb = oxDb::getDb();
        $sDelete = "delete from oxactions2article where oxactionid = " . $oDb->quote($this->getId()) . " and oxartid = " . $oDb->quote($articleId) . " and oxshopid = '" . $this->getShopId() . "'";
        $iRemovedArticles = $oDb->execute($sDelete);

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
        $oDb = oxDb::getDb();
        $sDelete = "delete from oxactions2article where oxactionid = " . $oDb->quote($articleId) . " and oxshopid = '" . $this->getShopId() . "'";
        $oDb->execute($sDelete);

        return parent::delete($articleId);
    }

    /**
     * return time left until finished
     *
     * @return int
     */
    public function getTimeLeft()
    {
        $iNow = oxRegistry::get("oxUtilsDate")->getTime();
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
        $iNow = oxRegistry::get("oxUtilsDate")->getTime();
        $iFrom = strtotime($this->oxactions__oxactivefrom->value);

        return $iFrom - $iNow;
    }

    /**
     * start the promotion NOW!
     */
    public function start()
    {
        $this->oxactions__oxactivefrom = new oxField(date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime()));
        if ($this->oxactions__oxactiveto->value && ($this->oxactions__oxactiveto->value != '0000-00-00 00:00:00')) {
            $iNow = oxRegistry::get("oxUtilsDate")->getTime();
            $iTo = strtotime($this->oxactions__oxactiveto->value);
            if ($iNow > $iTo) {
                $this->oxactions__oxactiveto = new oxField('0000-00-00 00:00:00');
            }
        }
        $this->save();
    }

    /**
     * stop the promotion NOW!
     */
    public function stop()
    {
        $this->oxactions__oxactiveto = new oxField(date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime()));
        $this->save();
    }

    /**
     * check if this action is active
     *
     * @return bool
     */
    public function isRunning()
    {
        if (!($this->oxactions__oxactive->value
              && $this->oxactions__oxtype->value == 2
              && $this->oxactions__oxactivefrom->value != '0000-00-00 00:00:00'
        )
        ) {
            return false;
        }
        $iNow = oxRegistry::get("oxUtilsDate")->getTime();
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
        /** @var oxUtilsView $oUtilsView */
        $oUtilsView = oxRegistry::get("oxUtilsView");
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
            $oArticle = oxNew('oxArticle');

            if ($this->isAdmin()) {
                $oArticle->setLanguage(oxRegistry::getLang()->getEditLanguage());
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
        $database = oxDb::getDb();

        $articleId = $database->getOne(
            'select oxobjectid from oxobject2action ' .
            'where oxactionid=' . $database->quote($this->getId()) .
            ' and oxclass="oxarticle"'
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
            $sPromoDir = oxRegistry::get("oxUtilsFile")->normalizeDir(oxUtilsFile::PROMO_PICTURE_DIR);

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
            /** @var oxUtilsUrl $oUtilsUlr */
            $oUtilsUlr = oxRegistry::get("oxUtilsUrl");
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
}
