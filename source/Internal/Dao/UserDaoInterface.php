<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 30.08.17
 * Time: 14:39
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;

interface UserDaoInterface
{

    const VAT_REGION_HOME_COUNTRY = 1;
    const VAT_REGION_EU = 2;
    const VAT_REGION_OUTSIDE_EU = 3;

    public function getVatRegion($userId);

    public function getUserCountryId($userId);

    public function ustIdExist($userId);

}