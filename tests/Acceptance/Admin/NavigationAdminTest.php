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

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;

class NavigationAdminTest extends AdminTestCase
{
    /**
     * testing newsletter activation in admin
     *
     * @group frontendAdmin
     */
    public function testFrontendNewsletterAdmin()
    {
        $aUserData = $this->_getUserData();
        $sUserId = $this->callShopSC('oxuser', 'save', null, $aUserData);
        $aSubscriberInfo = $this->_getSubscriberInfo( $sUserId );
        $this->callShopSC('oxNewsSubscribed', 'save', null, $aSubscriberInfo);

        //checking if user was created
        $this->loginAdmin( "Administer Users", "Users" );
        $this->clickAndWait( "link=Cust No." );
        $this->clickAndWait( "nav.last" );
        $this->clickAndWaitFrame( "link=example01@oxid-esa...", "edit" );
        $this->openTab( "Extended" );
        //because user did not confirm newsletter by email, it is off. setting it on for testing unsubscribe option
        $this->assertEquals( "off", $this->getValue( "//input[@name='editnews' and @value='1']" ) );
        $this->check( "//input[@name='editnews' and @value='1']" );
        $this->clickAndWait( "save" );
        $this->assertEquals( "on", $this->getValue( "//input[@name='editnews' and @value='1']" ) );
    }

    /**
     * @return array
     */
    protected function _getUserData()
    {
        $aData = array(
            "oxusername" => "example01@oxid-esales.dev",
            'oxpassword' => 'password',
            "oxustid" => "",
            "oxmobfon" => "111-111111-1",
            "oxprivfon" => "111111111",
            "oxbirthdate" => rand(1960, 2000) . '-' . rand(10, 12) . '-' . rand(10, 28),
        );

        return $aData;
    }

    /**
     * @param $sUserId
     * @return array
     */
    protected function _getSubscriberInfo( $sUserId )
    {
        $aParameters = array(
            'OXSAL' => 'MRS',
            'OXUSERID' => $sUserId,
            'OXFNAME' => 'name_¨Äßü?',
            'OXLNAME' => 'surname_¨Äßü?',
            'OXEMAIL' => 'example01@oxid-esales.dev',
            'OXDBOPTIN' => 2
        );

        return $aParameters;
    }
}
