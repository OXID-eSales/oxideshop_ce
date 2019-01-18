<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Order delivery set manager.
 *
 */
class DeliverySet extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /**
     * Current object class name
     *
     * @var string
     */
    protected $_sClassName = 'oxdeliveryset';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxdeliveryset');
    }

    /**
     * Delete this object from the database, returns true on success.
     *
     * @param string $sOxId Object ID(default null)
     *
     * @return bool
     */
    public function delete($sOxId = null)
    {
        if (!$sOxId) {
            $sOxId = $this->getId();
        }
        if (!$sOxId) {
            return false;
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sOxIdQuoted = $oDb->quote($sOxId);
        $oDb->execute('delete from oxobject2payment where oxobjectid = ' . $sOxIdQuoted);
        $oDb->execute('delete from oxobject2delivery where oxdeliveryid = ' . $sOxIdQuoted);
        $oDb->execute('delete from oxdel2delset where oxdelsetid = ' . $sOxIdQuoted);

        return parent::delete($sOxId);
    }

    /**
     * returns delivery set id
     *
     * @param string $sTitle delivery name
     *
     * @return string
     */
    public function getIdByName($sTitle)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "SELECT `oxid` FROM `" . getViewName('oxdeliveryset') . "` WHERE  `oxtitle` = " . $oDb->quote($sTitle);
        $sId = $oDb->getOne($sQ);

        return $sId;
    }
}
