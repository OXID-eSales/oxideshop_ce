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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Article file link manager.
 *
 * @package model
 */
class oxOrderFileList extends oxList
{
    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'oxorderfile';

    /**
     * Returns orders
     *
     * @param string $sUserId - user id
     *
     * @return oxlist
     */
    public function loadUserFiles( $sUserId )
    {
        $oOrderFile = $this->getBaseObject();
        $sFields = $oOrderFile->getSelectFields();
        $sShopId = $this->getConfig()->getShopId();

        $oOrderFile->addFieldName('oxorderfiles__oxarticletitle');
        $oOrderFile->addFieldName('oxorderfiles__oxarticleartnum');
        $oOrderFile->addFieldName('oxorderfiles__oxordernr');
        $oOrderFile->addFieldName('oxorderfiles__oxorderdate');

        $sSql = "SELECT " . $sFields . " ,
                      `oxorderarticles`.`oxtitle` AS `oxorderfiles__oxarticletitle`,
                      `oxorderarticles`.`oxartnum` AS `oxorderfiles__oxarticleartnum`,
                      `oxfiles`.`oxpurchasedonly` AS `oxorderfiles__oxpurchasedonly`,
                      `oxorder`.`oxordernr` AS `oxorderfiles__oxordernr`,
                      `oxorder`.`oxorderdate` AS `oxorderfiles__oxorderdate`,
                      IF( `oxorder`.`oxpaid` != '0000-00-00 00:00:00', 1, 0 ) AS `oxorderfiles__oxispaid`
                    FROM `oxorderfiles`
                        LEFT JOIN `oxorderarticles` ON `oxorderarticles`.`oxid` = `oxorderfiles`.`oxorderarticleid`
                        LEFT JOIN `oxfiles` ON `oxfiles`.`oxid` = `oxorderfiles`.`oxfileid`
                        LEFT JOIN `oxorder` ON `oxorder`.`oxid` = `oxorderfiles`.`oxorderid`
                    WHERE `oxorder`.`oxuserid` = '". $sUserId ."'
                        AND `oxorderfiles`.`oxshopid` = '". $sShopId ."'
                        AND `oxorder`.`oxstorno` = 0
                        AND `oxorderarticles`.`oxstorno` = 0
                    ORDER BY `oxorder`.`oxordernr`";

        $this->selectString($sSql);
    }

    /**
     * Returns oxorderfiles list
     *
     * @param string $sOrderId - order id
     *
     * @return oxlist
     */
    public function loadOrderFiles( $sOrderId )
    {
            $oOrderFile = $this->getBaseObject();
            $sFields = $oOrderFile->getSelectFields();
            $sShopId = $this->getConfig()->getShopId();

            $oOrderFile->addFieldName('oxorderfiles__oxarticletitle');
            $oOrderFile->addFieldName('oxorderfiles__oxarticleartnum');

            $sSql = "SELECT " . $sFields . " ,
                      `oxorderarticles`.`oxtitle` AS `oxorderfiles__oxarticletitle`,
                      `oxorderarticles`.`oxartnum` AS `oxorderfiles__oxarticleartnum`,
                      `oxfiles`.`oxpurchasedonly` AS `oxorderfiles__oxpurchasedonly`
                    FROM `oxorderfiles`
                        LEFT JOIN `oxorderarticles` ON `oxorderarticles`.`oxid` = `oxorderfiles`.`oxorderarticleid`
                        LEFT JOIN `oxfiles` ON `oxfiles`.`oxid` = `oxorderfiles`.`oxfileid`
                    WHERE `oxorderfiles`.`oxorderid` = '". $sOrderId ."' AND `oxorderfiles`.`oxshopid` = '". $sShopId ."'
                        AND `oxorderarticles`.`oxstorno` = 0";

            $this->selectString($sSql);
    }
}
