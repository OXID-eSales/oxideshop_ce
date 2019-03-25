<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\User;

use oxField;
use OxidEsales\TestingLibrary\UnitTestCase;
use oxUser;

abstract class UserTestCase extends UnitTestCase
{
    /**
     * Password encoded with old algorithm.
     *
     * @var string
     */
    protected $_sOldEncodedPassword = '4bb11fbb0c6bf332517a7ec397e49f1c';

    /**
     * Salt generated with old algorithm.
     *
     * @var string
     */
    protected $_sOldSalt = '3262383936333839303439393466346533653733366533346137326666393632';

    /**
     * Password encoded with new algorithm.
     *
     * @var string
     */
    protected $_sNewEncodedPassword = 'b016e37ac8ec71449b475e84a941e3c39a27fb8f0710d4b47d6116ad6a6afcaa0c17006a4c01ffc67f3db95772fe001584cb4ce7e5bacd74198c24d1851841d5';

    /**
     * Salt generated with new algorithm.
     *
     * @var string
     */
    protected $_sNewSalt = '56784f8ffc657fff84915b93e12a626e';

    /** @var string */
    protected $_sDefaultUserName = '_testUserName@oxid-esales.com';

    /** @var string */
    protected $_sDefaultUserPassword = '_testPassword';

    /** @var bool */
    protected $_blSkipCustomTearDown = false;

    public function tearDown()
    {
        if (!$this->_blSkipCustomTearDown) {
            $oDbRestore = $this->_getDbRestore();
            $oDbRestore->restoreTable('oxuser');
            $oDbRestore->restoreTable('oxshops');
        }
        parent::tearDown();
    }

    /**
     * Creates user with the default credentials for given shop.
     *
     * MD5 encoded password style is used for legacy shops
     *
     * @param string $sRight            OXRIGHTS column value ('malladmin', 'user' or <shopid>)
     * @param int    $iShopId           Shop ID
     * @param bool   $blMd5EncodedStyle Use MD5 encoded (legacy) password encryption
     *
     * @return oxUser
     */
    protected function _createDefaultUser($sRight, $iShopId, $blMd5EncodedStyle = true)
    {
        if ($blMd5EncodedStyle) {
            $sPassVal = $this->_sOldEncodedPassword;
            $sSaltVal = $this->_sOldSalt;
        } else {
            $sPassVal = $this->_sNewEncodedPassword;
            $sSaltVal = $this->_sNewSalt;
        }

        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxusername = new oxField('_testUserName@oxid-esales.com', oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField($sPassVal, oxField::T_RAW);
        $oUser->oxuser__oxpasssalt = new oxField($sSaltVal, oxField::T_RAW);
        $oUser->save();

        $oUserFromBase = oxNew('oxBase');
        $oUserFromBase->init('oxUser');
        $oUserFromBase->load($oUser->getId());
        $oUserFromBase->oxuser__oxshopid = new oxField($iShopId, oxField::T_RAW);
        $oUserFromBase->oxuser__oxrights = new oxField($sRight, oxField::T_RAW);
        $oUserFromBase->save();

        return $oUser;
    }

    protected function _createSecondSubShop()
    {
        $oShop = oxNew('oxShop');
        $oShop->save();
    }

    /**
     * @param string $sUserName
     * @param string $sUserPassword
     *
     * @return string
     */
    protected function _login($sUserName = null, $sUserPassword = null)
    {
        if (is_null($sUserName)) {
            $sUserName = $this->_sDefaultUserName;
        }
        if (is_null($sUserPassword)) {
            $sUserPassword = $this->_sDefaultUserPassword;
        }
        $this->_setLoginParametersToRequest($sUserName, $sUserPassword);
        $oCmpUser = oxNew('oxcmp_user');
        return $oCmpUser->login();
    }

    /**
     * @param string $sUserName
     * @param string $sUserPassword
     */
    private function _setLoginParametersToRequest($sUserName, $sUserPassword)
    {
        $this->setRequestParameter('lgn_usr', $sUserName);
        $this->setRequestParameter('lgn_pwd', $sUserPassword);
    }
}
