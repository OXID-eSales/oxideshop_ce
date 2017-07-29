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
        $sSelect .= "WHERE oxorder.oxuserid = " . \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sOxId);

        $this->selectString($sSelect);
    }
}
