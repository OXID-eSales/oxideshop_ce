<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Order article list manager.
 *
 */
class OrderArticleList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Class constructor, initiates class constructor (parent::oxbase()).
     */
    public function __construct()
    {
        parent::__construct('oxorderarticle');
    }

    /**
     * Copies passed to method product into $this.
     *
     * @param string $sOxId object id
     *
     * @return null
     */
    public function loadOrderArticlesForUser($sOxId)
    {
        if (!$sOxId) {
            $this->clear();

            return;
        }

        $sSelect = "SELECT oxorderarticles.* FROM oxorder ";
        $sSelect .= "left join oxorderarticles on oxorderarticles.oxorderid = oxorder.oxid ";
        $sSelect .= "left join oxarticles on oxorderarticles.oxartid = oxarticles.oxid ";
        $sSelect .= "WHERE oxorder.oxuserid = :oxuserid";

        $this->selectString($sSelect, [
            ':oxuserid' => $sOxId
        ]);
    }
}
