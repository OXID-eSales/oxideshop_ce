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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Integration_Checkout_ChangeDeliveryAddressTest extends OxidTestCase
{

    /**
     * Fixture setUp.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Fixture tearDown.
     */
    protected function tearDown()
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
        $oUser = $this->_createActiveUser(); //Switzerland user

        //assert zero VAT for Switzerland
        $oVatSelector = oxRegistry::get('oxVatSelector');
        $this->assertSame(0, $oVatSelector->getUserVat($oUser));
        $this->assertSame(0, $oVatSelector->getUserVat($oUser, true)); //no cache

        //Change to german address
        $this->_changeUser();

        //verify that the active user was updated
        $oUser = oxNew('oxUser');
        $oUser->loadActiveUser();
        $this->assertSame('Hahnentritt', $oUser->oxuser__oxlname->value);
        $this->assertSame('a7c40f631fc920687.20179984', $oUser->oxuser__oxcountryid->value);

        //verify german VAT
        $oVatSelector = oxRegistry::get('oxVatSelector');

        //Potential problem when using only oxuser.oxid as cache key: cache prevents getting correct VAT result
        //Updated user still has the same oxid, so oxVatSelector will fetch last result from cache
        #$this->assertSame(0, $oVatSelector->getUserVat($oUser));

        $this->assertFalse($oVatSelector->getUserVat($oUser, true));
        $this->assertFalse($oVatSelector->getUserVat($oUser));

    }

    /**
     * Test basket calculation when user country changes during checkout.
     * Test case when we explicitly set user via oxBasket::setBasketUser.
     */
    public function testBasketCalculationOnUserCountryChangeExplicitlySetBasketUser()
    {
        //no user logged in atm, create a basket
        $oBasket = $this->getProxyClass('oxBasket');
        $oBasket->addToBasket($this->getTestArticleId(), 1); //8 EUR
        $this->getSession()->setBasket($oBasket);

        //create user, as soon at it is set in session, it is available for basket as well
        $oUser = $this->_createActiveUser(); //Switzerland user
        $oBasket->setBasketUser($oUser);

        //verify basket calculation results
        $oBasket->calculateBasket(true);
        $this->assertSame(6.72, $oBasket->getNettoSum());
        $this->assertSame(6.72, $oBasket->getBruttoSum()); //no VAT for Switzerland

        //Change to german address
        $this->_changeUser();

        //verify that the basket user is up to date
        $oBasket = $this->getSession()->getBasket();
        $this->assertSame('Hahnentritt', $oBasket->getUser()->oxuser__oxlname->value);
        $this->assertSame('Hahnentritt', $oBasket->getBasketUser()->oxuser__oxlname->value);
        $oBasket->calculateBasket(true); //basket calculation triggers basket item user update

        //check basket calculation results, should now be with VAT due to german delivery address
        $this->assertSame(6.72, $oBasket->getNettoSum());
        $this->assertSame(8.0, $oBasket->getBruttoSum());
    }

    /**
     * Test basket calculation when user country changes during checkout.
     */
    public function testBasketCalculationOnUserCountryChange()
    {
        //no user logged in atm, create a basket
        $oBasket = $this->getProxyClass('oxBasket');
        $oBasket->addToBasket($this->getTestArticleId(), 1); //8 EUR
        $this->getSession()->setBasket($oBasket);

        //create user, as soon at it is set in session, it is available for basket as well
        $this->_createActiveUser(); //Switzerland user

        //verify basket calculation results
        $oBasket->calculateBasket(true);
        $this->assertSame(6.72, $oBasket->getNettoSum());
        $this->assertSame(6.72, $oBasket->getBruttoSum()); //no VAT for Switzerland

        //Change to german address
        $this->_changeUser();

        //verify that the basket user is up to date
        $oBasket = $this->getSession()->getBasket();
        $oBasket->calculateBasket(true); //basket calculation triggers basket item user update

        //check basket calculation results, should now be with VAT due to german delivery address
        $this->assertSame(6.72, $oBasket->getNettoSum());
        $this->assertSame(8.0, $oBasket->getBruttoSum());
    }

    /**
     * Insert test user, set to session
     */
    private function _createActiveUser()
    {
        $sTestUserId = substr_replace(oxUtilsObject::getInstance()->generateUId(), '_', 0, 1);

        $oUser = oxNew('oxUser');
        $oUser->setId($sTestUserId);

        $oUser->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $oUser->oxuser__oxrights = new oxField('user', oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField('oxbaseshop', oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('testuser@oxideshop.dev', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField('c630e7f6dd47f9ad60ece4492468149bfed3da3429940181464baae99941d0ffa5562' .
                                                'aaecd01eab71c4d886e5467c5fc4dd24a45819e125501f030f61b624d7d',
            oxField::T_RAW); //password is asdfasdf
        $oUser->oxuser__oxpasssalt = new oxField('3ddda7c412dbd57325210968cd31ba86', oxField::T_RAW);
        $oUser->oxuser__oxcustnr = new oxField('667', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('Erna', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('Helvetia', oxField::T_RAW);
        $oUser->oxuser__oxstreet = new oxField('Dorfstrasse', oxField::T_RAW);
        $oUser->oxuser__oxstreetnr = new oxField('117', oxField::T_RAW);
        $oUser->oxuser__oxcity = new oxField('Oberbuchsiten', oxField::T_RAW);
        $oUser->oxuser__oxcountryid = new oxField('a7c40f6321c6f6109.43859248', oxField::T_RAW);
        $oUser->oxuser__oxzip = new oxField('4625', oxField::T_RAW);
        $oUser->oxuser__oxsal = new oxField('MRS', oxField::T_RAW);
        $oUser->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $oUser->oxuser__oxboni = new oxField('1000', oxField::T_RAW);
        $oUser->oxuser__oxcreate = new oxField('2015-05-20 22:10:51', oxField::T_RAW);
        $oUser->oxuser__oxregister = new oxField('2015-05-20 22:10:51', oxField::T_RAW);
        $oUser->oxuser__oxboni = new oxField('1000', oxField::T_RAW);

        $oUser->save();

        $this->getSession()->setVariable('usr', $oUser->getId());

        return $oUser;
    }

    /**
     * Test helper, change user to german address.
     */
    private function _changeUser()
    {
        //now change the user address
        $aRawValues = array('oxuser__oxfname'     => 'Erna',
                            'oxuser__oxlname'     => 'Hahnentritt',
                            'oxuser__oxstreetnr'  => '117',
                            'oxuser__oxstreet'    => 'Landstrasse',
                            'oxuser__oxzip'       => '22769',
                            'oxuser__oxcity'      => 'Hamburg',
                            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984');

        $this->setRequestParam('invadr', $aRawValues);
        $this->setRequestParam('stoken', $this->getSession()->getSessionChallengeToken());

        $oUserComponent = oxNew('oxcmp_user');
        $this->assertSame('payment', $oUserComponent->changeUser());
    }

    /**
     * Test helper to provide test data depending on shop edition.
     *
     * @return string
     */
    private function getTestArticleId()
    {
        $return = '1127';
        $return = '9f542c530b33a7128.25390419';

        return $return;
    }
}
