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

class Integration_User_loginTest extends OxidTestCase
{
    public function testLoginWithOldPassword()
    {
        $sUser = '_testUserName@oxid-esales.com';
        $sPassword = '_testPassword';
        // Password encoded with old algorithm
        $sOldEncodedPassword = '4bb11fbb0c6bf332517a7ec397e49f1c';
        $sOldSalt = '3262383936333839303439393466346533653733366533346137326666393632';

        $oUser = new oxUser();
        $oUser->oxuser__oxid = new oxField('_test', oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField($sUser, oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField($sOldEncodedPassword, oxField::T_RAW);
        $oUser->oxuser__oxpasssalt = new oxField($sOldSalt, oxField::T_RAW);
        $oUser->save();

        $this->setRequestParam('lgn_usr', $sUser);
        $this->setRequestParam('lgn_pwd', $sPassword);
        $oCmpUser = new oxcmp_user();
        $oCmpUser->login();
        $this->assertSame($oUser->getId(), oxRegistry::getSession()->getVariable('usr'), 'User ID is missing in session.');
        $this->assertNull(oxRegistry::getSession()->getVariable('Errors'), 'User did not logged in successfully.');

        $oUser->load($oUser->getId());
        $this->assertNotSame($sOldEncodedPassword, $oUser->oxuser__oxpassword->value, 'Old and new passwords must not match.');
        $this->assertNotSame($sOldSalt, $oUser->oxuser__oxpasssalt->value, 'Old and new salt must not match.');
        $oUser->delete();
    }

    public function testLoginWithNewPassword()
    {
        $oDbRestore = $this->_getDbRestore();
        $oDbRestore->restoreTable('oxuser');


    }
}
