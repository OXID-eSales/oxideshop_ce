<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 11.07.17
 * Time: 13:40
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;

use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\OxidLegacyServiceInterface;

class UserDao extends BaseDao implements UserDaoInterface
{

    public function __construct(Connection $connection,
                                ContextInterface $context,
                                OxidLegacyServiceInterface $legacyService)
    {
        parent::__construct('oxusers', $connection, $context, $legacyService);
    }

    public function getVatRegion($userId)
    {

        $countryId = $this->getUserCountryId($userId);
        if (!$countryId || in_array($countryId, $this->context->getHomeCountryIds())) {
            return self::VAT_REGION_HOME_COUNTRY;
        }
        $oxVatStatus = $this->getCountryVatCode($countryId);
        if ($oxVatStatus == 0) {
            return self::VAT_REGION_OUTSIDE_EU;
        }
        if ($oxVatStatus == 1) {
            return self::VAT_REGION_EU;
        }
        throw new \Exception('VAT region not determinable!');
    }

    private function getCountryVatCode($countryId)
    {

        $query = $this->createQueryBuilder();
        $query->select('oxvatstatus')
            ->from('oxcountry')
            ->where($query->expr()->eq('oxid', ':countryid'))
            ->setParameter(':countryid', $countryId);
        $sth = $query->execute();
        $res = $sth->fetchAll();

        return $res[0]['oxvatstatus'];
    }

    public function getUserCountryId($userId)
    {

        $countryId = null;
        if ($this->context->useShippingAddressForVatCountry()) {
            return $this->getUserCountryIdFromShippingAddress($userId);
        } else {
            return $this->getUserCountryIdFromUserRecord($userId);
        }
    }

    private function getUserCountryIdFromShippingAddress($userId)
    {

        $shippingAdressId = $this->legacyService->getSelectedShippingAddressId();
        if (!$shippingAdressId) {
            return $this->getUserCountryIdFromUserRecord($userId);
        }

        $query = $this->createQueryBuilder();
        $query->select('oxcountryid')
            ->from('oxaddress')
            ->where($query->expr()->eq('oxid', ':addressid'))
            ->setParameter(':addressid', $shippingAdressId);
        $sth = $query->execute();
        $res = $sth->fetchAll();

        if (sizeof($res) != 1) {
            return $this->getUserCountryIdFromUserRecord($userId);
        }

        return $res[0]['oxcountryid'];
    }

    private function getUserCountryIdFromUserRecord($userId)
    {

        $query = $this->createQueryBuilder();
        $query->select('oxcountryid')
            ->from('oxuser')
            ->where($query->expr()->eq('oxid', ':id'))
            ->setParameter(':id', $userId);
        $sth = $query->execute();
        $res = $sth->fetchAll();
        if (sizeof($res) != 1) {
            throw new \Exception('Did not find unique user for userid ' . $userId);
        }

        return $res[0]['oxcountryid'];
    }

    public function ustIdExist($userId)
    {
        $query = $this->createQueryBuilder();
        $query->select('oxid')
            ->from($this->tablename)
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('oxid', ':userid'),
                    $query->expr()->neq('oxustid', '')
                )
            )
            ->setParameter(':userId', $userId);

        $sth = $query->execute();
        $res = $sth->fetchAll();

        return sizeof($res) == 1;
    }

    public function getPriceGroup($userId) {

        $query = $this->createQueryBuilder();
        $query->select('oxgroupsid')
            ->from('oxobject2group')
            ->where(
                $query->expr()->eq('oxobjectid', ':userid')
            )
            ->orderBy('oxgroupsid', 'DESC')
            ->setParameter(':userid', $userId);

        $sth = $query->execute();
        foreach($sth->fetchAll() as $row) {
            $matches = [];
            if (preg_match('/^oxidprice([abc])$/', $row['oxgroupsid'], $matches)) {
                return $matches[1];
            }
        }
        return null;

    }
}