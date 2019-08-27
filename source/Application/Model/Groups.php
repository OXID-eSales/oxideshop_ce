<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Group manager.
 * Base class for user groups. Does nothing special yet.
 *
 */
class Groups extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /**
     * Name of current class
     *
     * @var string
     */
    protected $_sClassName = 'oxgroups';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxgroups');
    }

    /**
     * Deletes user group from database. Returns true/false, according to deleting status.
     *
     * @param string $sOXID Object ID (default null)
     *
     * @return bool
     */
    public function delete($sOXID = null)
    {
        if (!$sOXID) {
            $sOXID = $this->getId();
        }
        if (!$sOXID) {
            return false;
        }

        parent::delete($sOXID);

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        // deleting related data records
        $sDelete = 'delete from oxobject2group where oxobject2group.oxgroupsid = :oxid';
        $oDb->execute($sDelete, [
            ':oxid' => $sOXID
        ]);

        $sDelete = 'delete from oxobject2delivery where oxobject2delivery.oxobjectid = :oxid';
        $oDb->execute($sDelete, [
            ':oxid' => $sOXID
        ]);

        $sDelete = 'delete from oxobject2discount where oxobject2discount.oxobjectid = :oxid';
        $oDb->execute($sDelete, [
            ':oxid' => $sOXID
        ]);

        $sDelete = 'delete from oxobject2payment where oxobject2payment.oxobjectid = :oxid';
        $rs = $oDb->execute($sDelete, [
            ':oxid' => $sOXID
        ]);

        return $rs->EOF;
    }
}
