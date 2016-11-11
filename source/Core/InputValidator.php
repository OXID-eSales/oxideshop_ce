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

namespace OxidEsales\EshopCommunity\Core;

use oxRegistry;
use oxDb;

/**
 * Class for validating input
 */
class InputValidator extends \oxSuperCfg
{

    /**
     * Invalid account number error code for template.
     */
    const INVALID_ACCOUNT_NUMBER = -5;

    /**
     * Invalid bank number error code for template.
     */
    const INVALID_BANK_CODE = -4;

    /**
     * Required fields for credit card payment
     *
     * @var array
     */
    protected $_aRequiredCCFields = array('kktype',
                                          'kknumber',
                                          'kkmonth',
                                          'kkyear',
                                          'kkname',
                                          'kkpruef'
    );

    /**
     * Input validation errors
     *
     * @var array
     */
    protected $_aInputValidationErrors = array();


    protected $_oCompanyVatInValidator = null;

    /**
     * Possible credit card types
     *
     * @var array
     */
    protected $_aPossibleCCType = array('mcd', // Master Card
                                        'vis', // Visa
                                        'amx', // American Express
                                        'dsc', // Discover
                                        'dnc', // Diners Club
                                        'jcb', // JCB
                                        'swi', // Switch
                                        'dlt', // Delta
                                        'enr' // EnRoute
    );

    /**
     * Required fields for debit cards
     *
     * @var array
     */
    protected $_aRequiredDCFields = array('lsbankname',
                                          'lsktonr',
                                          'lsktoinhaber'
    );

    /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     *
     */
    public function __construct()
    {
    }

    /**
     * Validates basket amount
     *
     * @param float $dAmount amount of article
     *
     * @throws oxArticleInputException if amount is not numeric or smaller 0
     *
     * @return float
     */
    public function validateBasketAmount($dAmount)
    {
        $dAmount = str_replace(',', '.', $dAmount);

        if (!is_numeric($dAmount) || $dAmount < 0) {
            /**
             * @var oxArticleInputException $oEx
             */
            $oEx = oxNew('oxArticleInputException');
            $oEx->setMessage(oxRegistry::getLang()->translateString('ERROR_MESSAGE_INPUT_INVALIDAMOUNT'));
            throw $oEx;
        }

        if (!oxRegistry::getConfig()->getConfigParam('blAllowUnevenAmounts')) {
            $dAmount = round(( string ) $dAmount);
        }

        //negative amounts are not allowed
        //$dAmount = abs($dAmount);

        return $dAmount;
    }

    /**
     * Checks if user name does not break logic:
     *  - if user wants to UPDATE his login name, performing check if
     *    user entered correct password
     *  - additionally checking for user name duplicates. This is usually
     *    needed when creating new users.
     * On any error exception is thrown.
     *
     * @param oxUser $oUser       active user
     * @param string $sLogin      user preferred login name
     * @param array  $aInvAddress user information
     *
     * @return string login name
     */
    public function checkLogin($oUser, $sLogin, $aInvAddress)
    {
        $sLogin = (isset($aInvAddress['oxuser__oxusername'])) ? $aInvAddress['oxuser__oxusername'] : $sLogin;

        // check only for users with password during registration
        // if user wants to change user name - we must check if passwords are ok before changing
        if ($oUser->oxuser__oxpassword->value && $sLogin != $oUser->oxuser__oxusername->value) {
            // on this case password must be taken directly from request
            $sNewPass = (isset($aInvAddress['oxuser__oxpassword']) && $aInvAddress['oxuser__oxpassword']) ? $aInvAddress['oxuser__oxpassword'] : oxRegistry::getConfig()->getRequestParameter('user_password');
            if (!$sNewPass) {
                // 1. user forgot to enter password
                $oEx = oxNew('oxInputException');
                $oEx->setMessage(oxRegistry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

                return $this->_addValidationError("oxuser__oxpassword", $oEx);
            } else {
                // 2. entered wrong password
                if (!$oUser->isSamePassword($sNewPass)) {
                    $oEx = oxNew('oxUserException');
                    $oEx->setMessage(oxRegistry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH'));

                    return $this->_addValidationError("oxuser__oxpassword", $oEx);
                }
            }
        }

        if ($oUser->checkIfEmailExists($sLogin)) {
            //if exists then we do not allow to do that
            $oEx = oxNew('oxUserException');
            $oEx->setMessage(sprintf(oxRegistry::getLang()->translateString('ERROR_MESSAGE_USER_USEREXISTS'), $sLogin));

            return $this->_addValidationError("oxuser__oxusername", $oEx);
        }

        return $sLogin;
    }

    /**
     * Checks if email (used as login) is not empty and is
     * valid.
     *
     * @param oxUser $oUser  active user
     * @param string $sEmail user email/login
     *
     * @return null
     */
    public function checkEmail($oUser, $sEmail)
    {
        // missing email address (user login name) ?
        if (!$sEmail) {
            $oEx = oxNew('oxInputException');
            $oEx->setMessage(oxRegistry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

            return $this->_addValidationError("oxuser__oxusername", $oEx);
        }

        // invalid email address ?
        if (!oxNew('oxMailValidator')->isValidEmail($sEmail)) {
            $oEx = oxNew('oxInputException');
            $oEx->setMessage(oxRegistry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOVALIDEMAIL'));

            return $this->_addValidationError("oxuser__oxusername", $oEx);
        }
    }

    /**
     * Checking if user password is fine. In case of error
     * exception is thrown
     *
     * @param oxUser $oUser         active user
     * @param string $sNewPass      new user password
     * @param string $sConfPass     retyped user password
     * @param bool   $blCheckLength option to check password length
     *
     * @return oxException|null
     */
    public function checkPassword($oUser, $sNewPass, $sConfPass, $blCheckLength = false)
    {
        //  no password at all
        if ($blCheckLength && getStr()->strlen($sNewPass) == 0) {
            $oEx = oxNew('oxInputException');
            $oEx->setMessage(oxRegistry::getLang()->translateString('ERROR_MESSAGE_INPUT_EMPTYPASS'));

            return $this->_addValidationError("oxuser__oxpassword", $oEx);
        }

        if ($blCheckLength && getStr()->strlen($sNewPass) < $this->getPasswordLength()) {
            $oEx = oxNew('oxInputException');
            $oEx->setMessage(oxRegistry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_TOO_SHORT'));

            return $this->_addValidationError("oxuser__oxpassword", $oEx);
        }

        //  passwords do not match ?
        if ($sNewPass != $sConfPass) {
            $oEx = oxNew('oxUserException');
            $oEx->setMessage(oxRegistry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH'));

            return $this->_addValidationError("oxuser__oxpassword", $oEx);
        }
    }

    /**
     * min length of password
     *
     * @return int
     */
    public function getPasswordLength()
    {
        return $this->getConfig()->getConfigParam("iPasswordLength") ?: 6;
    }

    /**
     * Checking if all required fields were filled. In case of error
     * exception is thrown
     *
     * @param oxUser $oUser            active user
     * @param array  $aBillingAddress  billing address
     * @param array  $aDeliveryAddress delivery address
     */
    public function checkRequiredFields($oUser, $aBillingAddress, $aDeliveryAddress)
    {
        /** @var oxRequiredAddressFields $oRequiredAddressFields */
        $oRequiredAddressFields = oxNew('oxRequiredAddressFields');

        /** @var oxRequiredFieldsValidator $oFieldsValidator */
        $oFieldsValidator = oxNew('oxRequiredFieldsValidator');

        /** @var oxUser $oUser */
        $oUser = oxNew('oxUser');
        $oBillingAddress = $this->_setFields($oUser, $aBillingAddress);
        $oFieldsValidator->setRequiredFields($oRequiredAddressFields->getBillingFields());
        $oFieldsValidator->validateFields($oBillingAddress);
        $aInvalidFields = $oFieldsValidator->getInvalidFields();

        if (!empty($aDeliveryAddress)) {
            /** @var oxAddress $oDeliveryAddress */
            $oDeliveryAddress = $this->_setFields(oxNew('oxAddress'), $aDeliveryAddress);
            $oFieldsValidator->setRequiredFields($oRequiredAddressFields->getDeliveryFields());
            $oFieldsValidator->validateFields($oDeliveryAddress);
            $aInvalidFields = array_merge($aInvalidFields, $oFieldsValidator->getInvalidFields());
        }

        foreach ($aInvalidFields as $sField) {
            $oEx = oxNew('oxInputException');
            $oEx->setMessage(oxRegistry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

            $this->_addValidationError($sField, $oEx);
        }
    }

    /**
     * Creates oxAddress object from given array.
     *
     * @param oxUser|oxAddress $oObject
     * @param array            $aFields
     *
     * @return oxUser|oxAddress
     */
    private function _setFields($oObject, $aFields)
    {
        $aFields = is_array($aFields) ? $aFields : array();
        foreach ($aFields as $sKey => $sValue) {
            $oObject->$sKey = oxNew('oxField', $sValue);
        }

        return $oObject;
    }

    /**
     * Checks if user defined countries (billing and delivery) are active
     *
     * @param oxUser $oUser       active user
     * @param array  $aInvAddress billing address info
     * @param array  $aDelAddress delivery address info
     */
    public function checkCountries($oUser, $aInvAddress, $aDelAddress)
    {
        $sBillCtry = isset($aInvAddress['oxuser__oxcountryid']) ? $aInvAddress['oxuser__oxcountryid'] : null;
        $sDelCtry = isset($aDelAddress['oxaddress__oxcountryid']) ? $aDelAddress['oxaddress__oxcountryid'] : null;

        if ($sBillCtry || $sDelCtry) {
            $oDb = oxDb::getDb();

            if (($sBillCtry == $sDelCtry) || (!$sBillCtry && $sDelCtry) || ($sBillCtry && !$sDelCtry)) {
                $sBillCtry = $sBillCtry ? $sBillCtry : $sDelCtry;
                $sQ = "select oxactive from oxcountry where oxid = " . $oDb->quote($sBillCtry) . " ";
            } else {
                $sQ = "select ( select oxactive from oxcountry where oxid = " . $oDb->quote($sBillCtry) . " ) and
                              ( select oxactive from oxcountry where oxid = " . $oDb->quote($sDelCtry) . " ) ";
            }

            if (!$oDb->getOne($sQ)) {
                $oEx = oxNew('oxUserException');
                $oEx->setMessage(oxRegistry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

                $this->_addValidationError("oxuser__oxcountryid", $oEx);
            }
        }
    }

    /**
     * Checks if user passed VAT id is valid. Exception is thrown
     * if id is not valid
     *
     * @param oxUser $oUser       active user
     * @param array  $aInvAddress user input array
     *
     * @return null
     */
    public function checkVatId($oUser, $aInvAddress)
    {
        if ($this->_hasRequiredParametersForVatInCheck($aInvAddress)) {
            $oCountry = $this->_getCountry($aInvAddress['oxuser__oxcountryid']);

            if ($oCountry && $oCountry->isInEU()) {
                $oVatInValidator = $this->getCompanyVatInValidator($oCountry);

                /** @var oxCompanyVatIn $oVatIn */
                $oVatIn = oxNew('oxCompanyVatIn', $aInvAddress['oxuser__oxustid']);

                if (!$oVatInValidator->validate($oVatIn)) {
                    /** @var oxInputException $oEx */
                    $oEx = oxNew('oxInputException');
                    $oEx->setMessage(oxRegistry::getLang()->translateString('VAT_MESSAGE_' . $oVatInValidator->getError()));

                    return $this->_addValidationError("oxuser__oxustid", $oEx);
                }
            }
        } elseif ($aInvAddress['oxuser__oxustid'] && !$aInvAddress['oxuser__oxcompany']) {
            /** @var oxInputException $oEx */
            $oEx = oxNew('oxInputException');
            $oEx->setMessage(oxRegistry::getLang()->translateString('VAT_MESSAGE_COMPANY_MISSING'));

            return $this->_addValidationError("oxuser__oxcompany", $oEx);
        }
    }


    /**
     * Load and return oxCountry
     *
     * @param string $sCountryId
     *
     * @return oxCountry
     */
    protected function _getCountry($sCountryId)
    {
        $oCountry = oxNew('oxCountry');
        $oCountry->load($sCountryId);

        return $oCountry;
    }

    /**
     * Returns error array if input validation for current field and rule reported an error
     *
     * @return array
     */
    public function getFieldValidationErrors()
    {
        return $this->_aInputValidationErrors;
    }

    /**
     * Returns first user input validation error
     *
     * @return exception
     */
    public function getFirstValidationError()
    {
        $aErr = reset($this->_aInputValidationErrors);
        if (is_array($aErr)) {
            return reset($aErr);
        }
    }

    /**
     * Validates payment input data for credit card and debit note
     *
     * @param string $sPaymentId the payment id of current payment
     * @param array  $aDynValue  values of payment
     *
     * @return bool
     */
    public function validatePaymentInputData($sPaymentId, & $aDynValue)
    {
        $mxValidationResult = true;

        switch ($sPaymentId) {
            case 'oxidcreditcard':
                $mxValidationResult = false;

                $blAllCreditCardInformationSet = $this->_isAllBankInformationSet($this->_aRequiredCCFields, $aDynValue);
                $blCreditCardTypeExist = in_array($aDynValue['kktype'], $this->_aPossibleCCType);

                if ($blAllCreditCardInformationSet && $blCreditCardTypeExist) {
                    $oCardValidator = oxNew("oxccvalidator");
                    $mxValidationResult = $oCardValidator->isValidCard(
                        $aDynValue['kknumber'],
                        $aDynValue['kktype'],
                        $aDynValue['kkmonth'] . substr($aDynValue['kkyear'], 2, 2)
                    );
                }
                break;

            case "oxiddebitnote":
                $mxValidationResult = false;

                if ($this->_isAllBankInformationSet($this->_aRequiredDCFields, $aDynValue)) {
                    $mxValidationResult = $this->_validateDebitNote($aDynValue);
                }

                break;
        }

        return $mxValidationResult;
    }

    /**
     * Used to collect user validation errors. This method is called from all of
     * the input checking functionality to report found error.
     *
     * @param string    $sFieldName field name
     * @param exception $oErr       exception
     *
     * @return exception
     */
    protected function _addValidationError($sFieldName, $oErr)
    {
        return $this->_aInputValidationErrors[$sFieldName][] = $oErr;
    }

    /**
     * Validates debit note.
     *
     * @param array $aDebitInformation Debit information
     *
     * @return bool|int
     */
    protected function _validateDebitNote($aDebitInformation)
    {
        $aDebitInformation = $this->_cleanDebitInformation($aDebitInformation);
        $sBankCode = $aDebitInformation['lsblz'];
        $sAccountNumber = $aDebitInformation['lsktonr'];
        $oSepaValidator = oxNew("oxSepaValidator");

        if (empty($sBankCode) || $oSepaValidator->isValidBIC($sBankCode)) {
            $mxValidationResult = true;
            if (!$oSepaValidator->isValidIBAN($sAccountNumber)) {
                $mxValidationResult = self::INVALID_ACCOUNT_NUMBER;
            }
        } else {
            $mxValidationResult = self::INVALID_BANK_CODE;
            if (!oxRegistry::getConfig()->getConfigParam('blSkipDebitOldBankInfo')) {
                $mxValidationResult = $this->_validateOldDebitInfo($aDebitInformation);
            }
        }

        return $mxValidationResult;
    }

    /**
     * Validates old debit info.
     *
     * @param array $aDebitInfo Debit info
     *
     * @return bool|int
     */
    protected function _validateOldDebitInfo($aDebitInfo)
    {
        $oStr = getStr();
        $aDebitInfo = $this->_fixAccountNumber($aDebitInfo);

        $mxValidationResult = true;

        if (!$oStr->preg_match("/^\d{5,8}$/", $aDebitInfo['lsblz'])) {
            // Bank code is invalid
            $mxValidationResult = self::INVALID_BANK_CODE;
        }

        if (true === $mxValidationResult && !$oStr->preg_match("/^\d{10,12}$/", $aDebitInfo['lsktonr'])) {
            // Account number is invalid
            $mxValidationResult = self::INVALID_ACCOUNT_NUMBER;
        }


        return $mxValidationResult;
    }

    /**
     * If account number is shorter than 10, add zeros in front of number.
     *
     * @param array $aDebitInfo Debit info
     *
     * @return array
     */
    protected function _fixAccountNumber($aDebitInfo)
    {
        $oStr = getStr();

        if ($oStr->strlen($aDebitInfo['lsktonr']) < 10) {
            $sNewNum = str_repeat(
                '0',
                10 - $oStr->strlen($aDebitInfo['lsktonr'])
            ) . $aDebitInfo['lsktonr'];
            $aDebitInfo['lsktonr'] = $sNewNum;
        }

        return $aDebitInfo;
    }

    /**
     * Checks if all bank information is set.
     *
     * @param array $aRequiredFields  fields must be set.
     * @param array $aBankInformation actual information.
     *
     * @return bool
     */
    protected function _isAllBankInformationSet($aRequiredFields, $aBankInformation)
    {
        $blResult = true;
        foreach ($aRequiredFields as $sFieldName) {
            if (!isset($aBankInformation[$sFieldName]) || !trim($aBankInformation[$sFieldName])) {
                $blResult = false;
                break;
            }
        }

        return $blResult;
    }

    /**
     * Clean up spaces.
     *
     * @param array $aDebitInformation Debit information
     *
     * @return mixed
     */
    protected function _cleanDebitInformation($aDebitInformation)
    {
        $aDebitInformation['lsblz'] = str_replace(' ', '', $aDebitInformation['lsblz']);
        $aDebitInformation['lsktonr'] = str_replace(' ', '', $aDebitInformation['lsktonr']);

        return $aDebitInformation;
    }

    /**
     * Check if all need parameters entered
     *
     * @param array $aInvAddress Address
     *
     * @return bool
     */
    protected function _hasRequiredParametersForVatInCheck($aInvAddress)
    {
        return $aInvAddress['oxuser__oxustid'] && $aInvAddress['oxuser__oxcountryid'] && $aInvAddress['oxuser__oxcompany'];
    }

    /**
     * VAT IN validator setter
     *
     * @param oxCompanyVatInValidator $oCompanyVatInValidator validator
     */
    public function setCompanyVatInValidator($oCompanyVatInValidator)
    {
        $this->_oCompanyVatInValidator = $oCompanyVatInValidator;
    }

    /**
     * Return VAT IN validator
     *
     * @param oxCountry $oCountry country according which VAT id should be checked
     *
     * @return oxCompanyVatInValidator
     */
    public function getCompanyVatInValidator($oCountry)
    {
        if (is_null($this->_oCompanyVatInValidator)) {
            /** @var oxCompanyVatInValidator $oVatInValidator */
            $oVatInValidator = oxNew('oxCompanyVatInValidator', $oCountry);

            /** @var  oxCompanyVatInCountryChecker $oValidator */
            $oValidator = oxNew('oxCompanyVatInCountryChecker');

            $oVatInValidator->addChecker($oValidator);

            /** @var oxOnlineVatIdCheck $oOnlineValidator */
            if (!oxRegistry::getConfig()->getConfigParam("blVatIdCheckDisabled")) {
                $oOnlineValidator = oxNew('oxOnlineVatIdCheck');
                $oVatInValidator->addChecker($oOnlineValidator);
            }

            $this->setCompanyVatInValidator($oVatInValidator);
        }

        return $this->_oCompanyVatInValidator;
    }
}
