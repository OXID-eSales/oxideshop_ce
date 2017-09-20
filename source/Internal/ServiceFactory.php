<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 17.07.17
 * Time: 15:12
 */

namespace OxidEsales\EshopCommunity\Internal;


use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Dao\ArticleDaoInterface;
use OxidEsales\EshopCommunity\Internal\Dao\ArticleDao;
use OxidEsales\EshopCommunity\Internal\Dao\DiscountDao;
use OxidEsales\EshopCommunity\Internal\Dao\DiscountDaoInterface;
use OxidEsales\EshopCommunity\Internal\Dao\PriceInformationDao;
use OxidEsales\EshopCommunity\Internal\Dao\PriceInformationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Dao\UserDao;
use OxidEsales\EshopCommunity\Internal\Dao\UserDaoInterface;
use OxidEsales\EshopCommunity\Internal\Facade\PriceCalculationFacade;
use OxidEsales\EshopCommunity\Internal\Facade\PriceCalculationFacadeInterface;
use OxidEsales\EshopCommunity\Internal\Service\PriceCalculationService;
use OxidEsales\EshopCommunity\Internal\Service\PriceCalculationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Utilities\Context;
use OxidEsales\EshopCommunity\Internal\Utilities\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utilities\OxidLegacyService;
use OxidEsales\EshopCommunity\Internal\Utilities\OxidLegacyServiceInterface;

class ServiceFactory
{

    private static $instance;

    // Basic OXID stuff that is not refactored
    private $config;
    private $language;

    /** @var  Connection $connection */
    private $connection;

    // Wrapper for evil not directly refactorable legacy code
    private $context;
    /** @var  ContextInterface $context */
    private $legacyService;
    /** @var  OxidLegacyServiceInterface $legacyService */


    // New internal classes
    /** @var ArticleDaoInterface $articleDao */
    private $articleDao;

    /** @var  UserDaoInterface $userDao */
    private $userDao;

    /** @var  PriceInformationDaoInterface $priceInformationDao */
    private $priceInformationDao;

    /** @var  DiscountDaoInterface $priceInformationDao */
    private $discountDao;

    /** @var  PriceCalculationServiceInterface $priceCalculationService */
    private $priceCalculationService;

    /** @var  PriceCalculationFacadeInterface $priceCalculationFacade */
    private $priceCalculationFacade;

    /**
     * The config should not be accessed through this factory. Use the
     * context utility to access the config in a shielded way.
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    private function getConfig()
    {

        if (!$this->config) {
            $this->config = Registry::getConfig();
        }

        return $this->config;
    }

    /**
     * The language should not be accessed through this factory
     *
     * @return \OxidEsales\Eshop\Core\Language
     */
    private function getLanguage()
    {

        if (!$this->language) {
            $this->language = Registry::getLang();
        }

        return $this->language;
    }

    private function getConnection()
    {

        if (!$this->connection) {
            // Ha - doing really evil stuff is fun!
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $r = new \ReflectionMethod(Database::class, 'getConnection');
            $r->setAccessible(true);
            $this->connection = $r->invoke($database);
        }

        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);

        return $this->connection;
    }

    /**
     * @return ContextInterface
     */
    public function getContext()
    {

        if (!$this->context) {
            $this->context = new Context($this->getConfig(), $this->getLanguage(), $this->getConnection());
        }

        return $this->context;
    }

    /**
     * @return OxidLegacyServiceInterface
     */
    public function getLegacyService()
    {

        if (!$this->legacyService) {
            $this->legacyService = new OxidLegacyService($this->getConfig(), $this->getContext());
        }

        return $this->legacyService;
    }

    /**
     * @return ArticleDaoInterface
     */
    public function getArticleDao()
    {

        if (!$this->articleDao) {
            $this->articleDao = new ArticleDao($this->getConnection(), $this->getContext(), $this->getLegacyService());
        }

        return $this->articleDao;
    }

    /**
     * @return UserDaoInterface
     */
    public function getUserDao()
    {

        if (!$this->userDao) {
            $this->userDao = new UserDao($this->getConnection(), $this->getContext(), $this->getLegacyService());
        }

        return $this->userDao;
    }

    /**
     * @return PriceInformationDaoInterface
     */
    public function getPriceInformationDao()
    {

        if (!$this->priceInformationDao) {
            $this->priceInformationDao = new PriceInformationDao(
                $this->getConnection(),
                $this->getContext(),
                $this->getLegacyService()
            );
        }

        return $this->priceInformationDao;
    }

    /**
     * @return DiscountDaoInterface
     */
    public function getDiscountDao()
    {

        if (!$this->discountDao) {
            $this->discountDao = new DiscountDao(
                $this->getConnection(),
                $this->getPriceInformationDao(),
                $this->getUserDao(),
                $this->getContext(),
                $this->getLegacyService()
            );
        }

        return $this->discountDao;
    }

    /**
     * @return PriceCalculationService
     */
    public function getPriceCalculationService()
    {

        if (!$this->priceCalculationService) {
            $this->priceCalculationService = new PriceCalculationService(
                $this->getPriceInformationDao(),
                $this->getUserDao(),
                $this->getDiscountDao(),
                $this->getContext(),
                $this->getLegacyService()
            );
        }

        return $this->priceCalculationService;
    }

    /**
     * @return PriceCalculationFacade|PriceCalculationFacadeInterface
     */
    public function getPriceCalculationFacade()
    {

        if (!$this->priceCalculationFacade) {
            $this->priceCalculationFacade = new PriceCalculationFacade(
                $this->getContext(),
                $this->getLegacyService(),
                $this->getPriceCalculationService()
            );
        }

        return $this->priceCalculationFacade;
    }


    /**
     * @return ServiceFactory
     */
    static function getInstance()
    {
        if (!ServiceFactory::$instance) {
            ServiceFactory::$instance = new ServiceFactory();
        }

        return ServiceFactory::$instance;
    }

    static function reset()
    {

        ServiceFactory::$instance = null;
    }

}
