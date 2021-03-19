<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Checkout;

use oxField;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use oxRegistry;

class ChangeDeliveryAddressTest extends \OxidTestCase
{
    const TEST_ARTICLE_ID = '1951';

    /**
     * Fixture setUp.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Fixture tearDown.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxuserbaskets');
        $this->cleanUpTable('oxuserbasketitems');

        parent::tearDown();
    }

    /**
     * Verify that the oxVatSelector respects user country changes.
     */
    public function testVatSelectorOnActiveUserCountryChange()
    {
        //create active user
        $user = $this->createActiveUser(); //Switzerland user

        //assert zero VAT for Switzerland
        $vatSelector = oxRegistry::get('oxVatSelector');
        $this->assertSame(0, $vatSelector->getUserVat($user));
        $this->assertSame(0, $vatSelector->getUserVat($user, true)); //no cache

        //Change to german address
        $this->changeUser();

        //verify that the active user was updated
        $user = oxNew('oxUser');
        $user->loadActiveUser();
        $this->assertSame('Hahnentritt', $user->oxuser__oxlname->value);
        $this->assertSame('a7c40f631fc920687.20179984', $user->oxuser__oxcountryid->value);

        //verify german VAT
        $vatSelector = oxRegistry::get('oxVatSelector');
        $this->assertFalse($vatSelector->getUserVat($user, true));
        $this->assertFalse($vatSelector->getUserVat($user));
    }

    /**
     * Test basket calculation when user country changes during checkout.
     * Test case when we explicitly set user via oxBasket::setBasketUser.
     */
    public function testBasketCalculationOnUserCountryChangeExplicitlySetBasketUser()
    {
        //no user logged in atm, create a basket
        $basket = oxNew('oxBasket');
        $basket->addToBasket(self::TEST_ARTICLE_ID, 1); //14 EUR
        $this->getSession()->setBasket($basket);

        //create user, as soon at it is set in session, it is available for basket as well
        $user = $this->createActiveUser(); //Switzerland user
        $basket->setBasketUser($user);

        //verify basket calculation results
        $basket->calculateBasket(true);
        $this->assertSame(11.76, $basket->getNettoSum());
        $this->assertSame(11.76, $basket->getBruttoSum()); //no VAT for Switzerland

        //Change to german address
        $this->changeUser();

        //verify that the basket user is up to date
        $basket = $this->getSession()->getBasket();
        $this->assertSame('Hahnentritt', $basket->getUser()->oxuser__oxlname->value);
        $this->assertSame('Hahnentritt', $basket->getBasketUser()->oxuser__oxlname->value);
        $basket->calculateBasket(true); //basket calculation triggers basket item user update

        //check basket calculation results, should now be with VAT due to german delivery address
        $this->assertSame(11.76, $basket->getNettoSum());
        $this->assertSame(14.0, $basket->getBruttoSum());
    }

    /**
     * Test basket calculation when user country changes during checkout.
     */
    public function testBasketCalculationOnUserCountryChange()
    {
        //no user logged in atm, create a basket
        $basket = oxNew('oxBasket');
        $basket->addToBasket(self::TEST_ARTICLE_ID, 1); //14 EUR
        $this->getSession()->setBasket($basket);

        //create user, as soon at it is set in session, it is available for basket as well
        $this->createActiveUser(); //Switzerland user

        //verify basket calculation results
        $basket->calculateBasket(true);
        $this->assertSame(11.76, $basket->getNettoSum());
        $this->assertSame(11.76, $basket->getBruttoSum()); //no VAT for Switzerland

        //Change to german address
        $this->changeUser();

        //verify that the basket user is up to date
        $basket = $this->getSession()->getBasket();
        $basket->calculateBasket(true); //basket calculation triggers basket item user update

        //check basket calculation results, should now be with VAT due to german delivery address
        $this->assertSame(11.76, $basket->getNettoSum());
        $this->assertSame(14.0, $basket->getBruttoSum());
    }

    /**
     * Insert test user, set to session
     */
    private function createActiveUser()
    {
        $sTestUserId = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);

        $user = oxNew('oxUser');
        $user->setId($sTestUserId);

        $user->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $user->oxuser__oxrights = new oxField('user', oxField::T_RAW);
        $user->oxuser__oxshopid = new oxField(ShopIdCalculator::BASE_SHOP_ID, oxField::T_RAW);
        $user->oxuser__oxusername = new oxField('testuser@oxideshop.dev', oxField::T_RAW);
        $user->oxuser__oxpassword = new oxField(
            'c630e7f6dd47f9ad60ece4492468149bfed3da3429940181464baae99941d0ffa5562' .
                                                'aaecd01eab71c4d886e5467c5fc4dd24a45819e125501f030f61b624d7d',
            oxField::T_RAW
        ); //password is asdfasdf
        $user->oxuser__oxpasssalt = new oxField('3ddda7c412dbd57325210968cd31ba86', oxField::T_RAW);
        $user->oxuser__oxcustnr = new oxField('667', oxField::T_RAW);
        $user->oxuser__oxfname = new oxField('Erna', oxField::T_RAW);
        $user->oxuser__oxlname = new oxField('Helvetia', oxField::T_RAW);
        $user->oxuser__oxstreet = new oxField('Dorfstrasse', oxField::T_RAW);
        $user->oxuser__oxstreetnr = new oxField('117', oxField::T_RAW);
        $user->oxuser__oxcity = new oxField('Oberbuchsiten', oxField::T_RAW);
        $user->oxuser__oxcountryid = new oxField('a7c40f6321c6f6109.43859248', oxField::T_RAW);
        $user->oxuser__oxzip = new oxField('4625', oxField::T_RAW);
        $user->oxuser__oxsal = new oxField('MRS', oxField::T_RAW);
        $user->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $user->oxuser__oxboni = new oxField('1000', oxField::T_RAW);
        $user->oxuser__oxcreate = new oxField('2015-05-20 22:10:51', oxField::T_RAW);
        $user->oxuser__oxregister = new oxField('2015-05-20 22:10:51', oxField::T_RAW);
        $user->oxuser__oxboni = new oxField('1000', oxField::T_RAW);

        $user->save();

        $this->getSession()->setVariable('usr', $user->getId());

        return $user;
    }

    /**
     * Test helper, change user to german address.
     */
    private function changeUser()
    {
        //now change the user address
        $rawValues = array('oxuser__oxfname'     => 'Erna',
                           'oxuser__oxlname'     => 'Hahnentritt',
                           'oxuser__oxstreetnr'  => '117',
                           'oxuser__oxstreet'    => 'Landstrasse',
                           'oxuser__oxzip'       => '22769',
                           'oxuser__oxcity'      => 'Hamburg',
                           'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984');

        $this->setRequestParameter('invadr', $rawValues);
        $this->setRequestParameter('stoken', $this->getSession()->getSessionChallengeToken());

        $userComponent = oxNew('oxcmp_user');
        $this->assertSame('payment', $userComponent->changeUser());
    }
}
