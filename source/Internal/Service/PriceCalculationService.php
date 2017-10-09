<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 08.08.17
 * Time: 13:46
 */

namespace OxidEsales\EshopCommunity\Internal\Service;


use OxidEsales\EshopCommunity\Internal\Dao\DiscountDaoInterface;
use OxidEsales\EshopCommunity\Internal\Dao\PriceInformationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Dao\SelectListDaoInterface;
use OxidEsales\EshopCommunity\Internal\Dao\UserDaoInterface;
use OxidEsales\EshopCommunity\Internal\DataObject\Discount;
use OxidEsales\EshopCommunity\Internal\DataObject\SelectList;
use OxidEsales\EshopCommunity\Internal\DataObject\SimplePrice;
use OxidEsales\EshopCommunity\Internal\DataObject\User;
use OxidEsales\EshopCommunity\Internal\Utilities\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utilities\OxidLegacyServiceInterface;

class PriceCalculationService implements PriceCalculationServiceInterface
{

    /** @var PriceInformationDaoInterface $priceInformationDao */
    private $priceInformationDao;

    /** @var UserDaoInterface UserDao */
    private $userDao;

    /** @var DiscountDaoInterface */
    private $discountDao;

    /** @var  SelectListDaoInterface */
    private $selectListDao;

    /** @var ContextInterface $context */
    private $context;

    /** @var OxidLegacyServiceInterface $legacyService */
    private $legacyService;

    public function __construct(PriceInformationDaoInterface $priceInformationDao,
                                UserDaoInterface $userDao,
                                DiscountDaoInterface $discountDao,
                                SelectListDaoInterface $selectListDao,
                                ContextInterface $context,
                                OxidLegacyServiceInterface $legacyService)
    {
        $this->priceInformationDao = $priceInformationDao;
        $this->userDao = $userDao;
        $this->discountDao = $discountDao;
        $this->selectListDao = $selectListDao;
        $this->context = $context;
        $this->legacyService = $legacyService;
    }

    /**
     * In the current implementation the base price might be derived from three different
     * sources:
     *
     * First, it might be the price stored in oxarticles.oxprice
     *
     * Now, the current user might be in a special usergroup. Then this might
     * be the usergroup price stored in oxarticles.oxprice[abc]
     *
     * And third, there might be some bulk price stored in oxprice2article, then
     * this trumps the first two ways.
     *
     * The bulk price might be twofold: Absolute or relative. When relative, the
     * discount is applied to the already established price. If absolute, it
     * overrides it.
     *
     * @param     $articleId
     * @param int $amount
     *
     * @return double
     */
    public function getRawDatabasePrice($articleId, $userId, $shopId = 1, $amount = 1)
    {

        $basicPriceInformation = $this->priceInformationDao->getBasicPriceInformation($articleId, $shopId);

        $basePrice = $basicPriceInformation->getBasePrice();

        $userPriceGroup = $this->getUserPriceGroup($userId);

        if ($userPriceGroup) {
            $groupPrice = $basicPriceInformation->getGroupPrice($userPriceGroup);
            if ($groupPrice == 0.0) {
                if (!$this->context->overrideZeroGroupPrices()) {
                    $basePrice = 0.0;
                }
            } else {
                $basePrice = $groupPrice;
            }
        }

        if ($amount > 1) {
            $bulkPriceInformation = $this->priceInformationDao->getBulkPriceInformation($amount, $articleId);
            $bulkPrice = $bulkPriceInformation->calculateBulkPrice($basePrice);
            if ($bulkPrice < $basePrice) {
                $basePrice = $bulkPrice;
            }
        }

        return $basePrice;
    }

    /**
     * First, we get a price object from the legacy service. This
     * already has set the *view* mode for the price, i.e. pre-tax
     * or net.
     *
     * Then we get the stored base price. This might be pre-tax or
     * net. What it is, we get from the context. Now we calculate
     * the price so that it matches the *view* mode. I.e., if the
     * view mode is *net* and the database value is *pre-tax*, we
     * calculate the net value from the pre-tax value and vice versa.
     * If view mode and database mode match, we just add the baseprice
     * to the price object.
     *
     * @param string $articleId
     * @param int    $shopId
     * @param int    $amount
     *
     * @return SimplePrice
     */
    public function getSimplePrice($articleId, $userId, $shopId = 1, $amount = 1)
    {

        return new SimplePrice(
            $this->getRawDatabasePrice($articleId, $userId, $shopId, $amount),
            $this->getArticleVat($articleId),
            $this->isUserVatTaxable($userId),
            $this->context->dbPricesAreNetPrices(),
            $articleId,
            $userId,
            $shopId,
            $amount);
    }


    /**
     * @param string $articleId
     * @param string $userId
     * @param int    $shopId
     *
     * @return Discount[]
     */
    public function getArticleDiscounts($articleId, $amount, $userId, $shopId)
    {

        return $this->discountDao->getArticleDiscounts($articleId, $amount, $userId, $shopId);
    }

    private function getUserPriceGroup($userId)
    {
        if ($userId === null) {
            return null;
        }

        // No need to query the database if user is already loaded
        $currentUser = $this->context->getUser();
        if ($currentUser && $currentUser->getId() == $userId) {
        //if ($currentUser) {
            return $currentUser->getPriceGroup();
        }

        return $this->userDao->getPriceGroup($userId);

    }

    public function getArticleVat($articleId, $shopId = 1)
    {

        $basicPriceInformation = $this->priceInformationDao->getBasicPriceInformation($articleId, $shopId);

        if (($vat = $basicPriceInformation->getVat()) !== null) {
            return $vat;
        }

        if ($this->context->shopUsesCategoryVat()) {

            if ($vat = $this->priceInformationDao->getVatFromCategory($articleId, $shopId)) {
                return $vat;
            }
        }

        return $this->context->getDefaultVat();
    }

    /**
     * The legacy code has some really improvised functions for this. It
     * has a crude function getArticleUserVat() in the VatSelector class.
     * This might return a value or false. And the only value may be 0.
     *
     * When false is returned, the normal VAT (as determined by getArticleVat)
     * is used, otherwise a VAT of 0 is used, that means, no VAT is added.
     * This happens when the user lives within the EU and a UStId is provided
     * or the user lives outside the EU.
     *
     * We now improve this by introducing a method that determines if
     * VAT should be applied for the user or not. Then the whole getPrice()
     * mechanism may be formulated much more clearly.
     *
     * The algorithm for this method is: Determine the region of the
     * user. There are three cases: It might be the home country of the
     * shop or in the EU or outside the EU. In the home country the
     * user is taxable, outside the EU not. Within the EU it depends
     * on him having an UStId or not.
     *
     * @param User $user
     *
     * @return boolean
     */
    public function isUserVatTaxable($userId)
    {
        if (! $userId) {
            return true;
        }

        $region = $this->userDao->getVatRegion($userId);
        if ($region == UserDaoInterface::VAT_REGION_HOME_COUNTRY) {
            return true;
        };
        if ($region == UserDaoInterface::VAT_REGION_OUTSIDE_EU) {
            return false;
        }

        return $this->userDao->ustIdExist($userId);
    }

    /**
     * @param $articleId
     *
     * @return SelectList
     */
    public function getSelectList($articleId) {

        return $this->selectListDao->getSelectListForArticle($articleId);

    }
}
