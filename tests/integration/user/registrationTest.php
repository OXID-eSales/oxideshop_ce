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

class Integration_User_registrationTest extends OxidTestCase
{
    public function testRegisterNewUserAndLoginAfterwards()
    {
        $sGermanyId = 'a7c40f631fc920687.20179984';
        $this->setRequestParam('userLoginName', 'someTestName@oxid-esales.com');
        $this->setRequestParam('lgn_usr', 'someTestName@oxid-esales.com');

        $this->setRequestParam('lgn_pwd', 'someTestPassword');
        $this->setRequestParam('lgn_pwd2', 'someTestPassword');
        $this->setRequestParam('passwordLength', 'someTestPassword');
        $this->setRequestParam('userPasswordConfirm', 'someTestPassword');


        $this->setRequestParam('invadr',
            array(
                'oxuser__oxsal' => 'Mr',
                'oxuser__oxfname' => 'SomeTestName',
                'oxuser__oxlname' => 'SomeTestSurname',
                'oxuser__oxstreet' => 'SomeTestStreet',
                'oxuser__oxstreetnr' => '23',
                'oxuser__oxzip' => '44444',
                'oxuser__oxcity' => 'SomeTestCoty',
                'oxuser__oxcountryid' => $sGermanyId
            ));

        $oRegister = new register();
        $oCmpUser = new oxcmp_user();
        $oCmpUser->setParent($oRegister);
        $this->assertSame('register?success=1', $oCmpUser->registeruser());
    }
}
