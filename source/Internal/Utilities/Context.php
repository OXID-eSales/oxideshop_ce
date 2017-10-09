<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 19.07.17
 * Time: 10:21
 */

namespace OxidEsales\EshopCommunity\Internal\Utilities;


use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Request;
use OxidEsales\EshopCommunity\Internal\DataObject\User;

class Context implements ContextInterface
{

    /** @var Config $config */
    private $config;

    /** @var Language $languageHelper */
    private $languageHelper;

    /** @var  User $user */
    private $user;

    /** @var Connection $connection */
    private $connection;

    /** @var boolean $shopUsesCategoryVat */
    private $shopUsesCategoryVat = null;

    public function __construct(Config $config, Language $language, Connection $connection)
    {

        $this->config = $config;
        $this->languageHelper = $language;
        $this->connection = $connection;
    }

    /**
     * @return Config;
     */
    private function getConfig()
    {

        return $this->config;
    }

    /**
     * @return Language
     */
    private function getLanguageHelper()
    {

        return $this->languageHelper;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        if (!$this->user) {
            $legacyUser = $this->getConfig()->getUser();
            if ($legacyUser) {
                $this->user = new User($legacyUser);
            }
        }

        return $this->user;
    }

    /**
     * The user in the context may change, for example if you
     * recalculate a bill not for the logged in user. Then you
     * need to change the user in the context. Afterwards you
     * need to reset the user.
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function resetUser()
    {

        $this->user = null;
    }

    /**
     * @param $paramName
     *
     * @return mixed
     */
    private function getConfigParam($paramName)
    {
        return $this->getConfig()->getConfigParam($paramName);
    }

    /**
     * @return string
     */
    public function getCurrentLanguageAbbrevitation()
    {

        return $this->getLanguageHelper()->getLanguageAbbr();
    }

    /**
     * @return int
     */
    public function getCurrentLanguageId()
    {

        return $this->getLanguageHelper()->getBaseLanguage();
    }

    /**
     * @return int
     */
    public function getShopId()
    {

        return $this->getConfig()->getShopId();
    }

    /**
     * @return bool
     */
    public function useTimeCheck()
    {

        return $this->getConfigParam('blUseTimeCheck');
    }

    /**
     * @return bool
     */
    public function isVariantParentBuyable()
    {
        return $this->getConfigParam('blVariantParentBuyable');
    }

    /**
     * @return bool
     */
    public function useStock()
    {
        return $this->getConfigParam('blUseStock');
    }

    /**
     * @return bool
     */
    public function overrideZeroGroupPrices()
    {
        return $this->getConfigParam('blOverrideZeroABCPrices');
    }

    /** @return double */
    public function getDefaultVat()
    {

        return $this->getConfigParam('dDefaultVAT');
    }

    /** @return string */
    public function getHomeCountryIds()
    {

        return $this->getConfigParam('aHomeCountry');
    }

    /**
     * Returns if the shop is configured to display net prices or not.
     *
     * @return bool
     */
    public function displayNetPrices()
    {
        return $this->getConfigParam('blShowNetPrice');
    }

    /**
     * For some reasons (during basket calculation) it is necessary
     * to overwrite the config option (I don't understand the
     * rationale of this, but tests do check it)
     *
     * @return bool
     */
    public function dbPricesAreNetPrices()
    {
        return $this->getConfigParam('blEnterNetPrice');
    }

    /**
     * @return bool
     */
    public function useShippingAddressForVatCountry()
    {
        return $this->getConfigParam('blShippingCountryVat');
    }

    public function loadPriceInformation() {

        return $this->getConfigParam('bl_perfLoadPrice');
    }

    /**
     * @param      $parameterName
     * @param null $default
     *
     * @throws \Exception
     * @return string
     */
    public function getRequestParameter($parameterName, $default = null)
    {

        $request = new Request();
        $param = $request->getRequestParameter($parameterName, $default);
        if ($param === null) {
            throw new \Exception("Did not find request parameter $parameterName");
        }

        return $param;
    }

    /** @return bool */
    public function shopUsesCategoryVat()
    {

        if ($this->shopUsesCategoryVat !== null) {
            return $this->shopUsesCategoryVat;
        }

        $query = $this->connection->createQueryBuilder();
        $query->select('oxid')
            ->from('oxcategories')
            ->where($query->expr()->isNotNull('oxvat'))
            ->setFirstResult(0)
            ->setMaxResults(1);

        $sth = $query->execute();
        $result = $sth->fetchAll();
        $this->shopUsesCategoryVat = sizeof($result) > 0;

        return $this->shopUsesCategoryVat;
    }

    public function getCurrencyRate()
    {

        $currency = $this->config->getActShopCurrencyObject();

        return $currency->rate;
    }

}
