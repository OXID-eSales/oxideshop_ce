<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 15.08.17
 * Time: 11:46
 */

namespace OxidEsales\EshopCommunity\Internal\DataObject;

/**
 * Class User
 *
 * This class is a proxy/adapter class wrapped around a legacy user class.
 * Eventually this should be refactored to be used with a DAO.
 *
 * @package OxidEsales\EshopCommunity\Internal\DataObject
 */
class User implements UserInterface
{

    /**
     * @var \OxidEsales\Eshop\Application\Model\User
     */
    private $legacyUser;

    public function __construct(\OxidEsales\Eshop\Application\Model\User $legacyUser)
    {

        $this->legacyUser = $legacyUser;
    }

    public function getId()
    {
        return $this->legacyUser->getId();
    }

    /**
     * This returns the price group of the user. In theory the user may be in
     * several price groups (what does not make any sense), so we have a hard
     * coded precedency: C tops B tops A.
     *
     * If the user is in no group, null is returned. This is kind of ugly, but
     * the whole construct is.
     *
     * @return null|string
     */
    public function getPriceGroup()
    {

        foreach (['c', 'b', 'a'] as $priceGroup) {
            if ($this->legacyUser->inGroup('oxidprice' . strtolower($priceGroup))) {
                return $priceGroup;
            }
        }

        return null;
    }

}