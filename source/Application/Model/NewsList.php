<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * News list manager.
 * Creates news objects, fetches its data.
 *
 *
 * @deprecated since v.5.3.0 (2016-06-17); The Admin Menu: Customer Info -> News feature will be moved to a module in v6.0.0
 *
 */
class NewsList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'oxnews';

    /**
     * Ref. to user object
     */
    protected $_oUser = null;

    /**
     * Loads news stored in DB, filtered by user groups, returns array, filled with
     * objects, that keeps news data.
     *
     * @param integer $iFrom  number from which start selecting
     * @param integer $iLimit Limit of records to fetch from DB(default 0)
     */
    public function loadNews($iFrom = 0, $iLimit = 10)
    {
        if ($iLimit) {
            $this->setSqlLimit($iFrom, $iLimit);
        }

        $sNewsViewName = getViewName('oxnews');
        $oBaseObject = $this->getBaseObject();
        $sSelectFields = $oBaseObject->getSelectFields();

        if ($oUser = $this->getUser()) {
            // performance - only join if user is logged in
            $sSelect = "select $sSelectFields from $sNewsViewName ";
            $sSelect .= "left join oxobject2group on oxobject2group.oxobjectid=$sNewsViewName.oxid where ";
            $sSelect .= "oxobject2group.oxgroupsid in ( select oxgroupsid from oxobject2group where oxobjectid='" . $oUser->getId() . "' ) or ";
            $sSelect .= "( oxobject2group.oxgroupsid is null ) ";
        } else {
            $sSelect = "select $sSelectFields, oxobject2group.oxgroupsid from $sNewsViewName ";
            $sSelect .= "left join oxobject2group on oxobject2group.oxobjectid=$sNewsViewName.oxid where oxobject2group.oxgroupsid is null ";
        }

        $sSelect .= " and " . $oBaseObject->getSqlActiveSnippet();
        $sSelect .= " and $sNewsViewName.oxshortdesc <> '' ";
        $sSelect .= " group by $sNewsViewName.oxid order by $sNewsViewName.oxdate desc ";

        $this->selectString($sSelect);
    }

    /**
     * Returns count of all entries.
     *
     * @return integer $iRecCnt
     */
    public function getCount()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sNewsViewName = getViewName('oxnews');
        $oBaseObject = $this->getBaseObject();
        //$sSelectFields = $oBaseObject->getSelectFields();

        if ($oUser = $this->getUser()) {
            // performance - only join if user is logged in
            $sSelect = "select COUNT($sNewsViewName.`oxid`) from $sNewsViewName ";
            $sSelect .= "left join oxobject2group on oxobject2group.oxobjectid=$sNewsViewName.oxid where ";
            $sSelect .= "oxobject2group.oxgroupsid in ( select oxgroupsid from oxobject2group where oxobjectid='" . $oUser->getId() . "' ) or ";
            $sSelect .= "( oxobject2group.oxgroupsid is null ) ";
        } else {
            $sSelect = "select COUNT($sNewsViewName.`oxid`) from $sNewsViewName ";
            $sSelect .= "left join oxobject2group on oxobject2group.oxobjectid=$sNewsViewName.oxid where oxobject2group.oxgroupsid is null ";
        }

        $sSelect .= " and " . $oBaseObject->getSqlActiveSnippet();

        // loading only if there is some data
        $iRecCnt = (int) $oDb->getOne($sSelect);

        return $iRecCnt;
    }

    /**
     * News list user setter
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser user object
     */
    public function setUser($oUser)
    {
        $this->_oUser = $oUser;
    }

    /**
     * News list user getter
     *
     * @return \OxidEsales\Eshop\Application\Model\User
     */
    public function getUser()
    {
        if ($this->_oUser == null) {
            $this->_oUser = parent::getUser();
        }

        return $this->_oUser;
    }
}
