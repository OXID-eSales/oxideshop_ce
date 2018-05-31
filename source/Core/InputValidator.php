<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Class for validating input
 */
class InputValidator extends \OxidEsales\Eshop\Core\Base
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
    protected $_aRequiredCCFields = ['kktype',
                                          'kknumber',
                                          'kkmonth',
                                          'kkyear',
                                          'kkname',
                                          'kkpruef'
    ];

    /**
     * Input validation errors
     *
     * @var array
     */
    protected $_aInputValidationErrors = [];


    protected $_oCompanyVatInValidator = null;

    /**
     * Possible credit card types
     *
     * @var array
     */
    protected $_aPossibleCCType = ['mcd', // Master Card
                                        'vis', // Visa
                                        'amx', // American Express
                                        'dsc', // Discover
                                        'dnc', // Diners Club
                                        'jcb', // JCB
                                        'swi', // Switch
                                        'dlt', // Delta
                                        'enr' // EnRoute
    ];

    /**
     * Required fields for debit cards
     *
     * @var array
     */
    protected $_aRequiredDCFields = ['lsbankname',
                                          'lsktonr',
                                          'lsktoinhaber'
    ];

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
             * @var \OxidEsales\Eshop\Core\Exception\ArticleInputException $oEx
             */
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ArticleInputException::class);
            $oEx->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_INVALIDAMOUNT'));
            throw $oEx;
        }

        if (!\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blAllowUnevenAmounts')) {
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
     * @param \OxidEsales\Eshop\Application\Model\User $oUser       active user
     * @param string                                   $sLogin      user preferred login name
     * @param array                                    $aInvAddress user information
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
            $sNewPass = (isset($aInvAddress['oxuser__oxpassword']) && $aInvAddress['oxuser__oxpassword']) ? $aInvAddress['oxuser__oxpassword'] : \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('user_password');
            if (!$sNewPass) {
                // 1. user forgot to enter password
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
                $oEx->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

                return $this->addValidationError("oxuser__oxpassword", $oEx);
            } else {
                // 2. entered wrong password
                if (!$oUser->isSamePassword($sNewPass)) {
                    $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\UserException::class);
                    $oEx->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH'));

                    return $this->addValidationError("oxuser__oxpassword", $oEx);
                }
            }
        }

        if ($oUser->checkIfEmailExists($sLogin)) {
            //if exists then we do not allow to do that
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\UserException::class);
            $oEx->setMessage(sprintf(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_USER_USEREXISTS'), $sLogin));

            return $this->addValidationError("oxuser__oxusername", $oEx);
        }

        return $sLogin;
    }

    /**
     * Checks if email (used as login) is not empty and is
     * valid.
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser  active user
     * @param string                                   $sEmail user email/login
     *
     * @return null
     */
    public function checkEmail($oUser, $sEmail)
    {
        // missing email address (user login name) ?
        if (!$sEmail) {
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $oEx->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

            return $this->addValidationError("oxuser__oxusername", $oEx);
        }

        // invalid email address ?
        if (!oxNew(\OxidEsales\Eshop\Core\MailValidator::class)->isValidEmail($sEmail)) {
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $oEx->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOVALIDEMAIL'));

            return $this->addValidationError("oxuser__oxusername", $oEx);
        }
    }

    /**
     * Checking if user password is fine. In case of error
     * exception is thrown
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser         active user
     * @param string                                   $sNewPass      new user password
     * @param string                                   $sConfPass     retyped user password
     * @param bool                                     $blCheckLength option to check password length
     *
     * @return oxException|null
     */
    public function checkPassword($oUser, $sNewPass, $sConfPass, $blCheckLength = false)
    {
        //  no password at all
        if ($blCheckLength && getStr()->strlen($sNewPass) == 0) {
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $oEx->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_EMPTYPASS'));

            return $this->addValidationError("oxuser__oxpassword", $oEx);
        }

        if ($blCheckLength && getStr()->strlen($sNewPass) < $this->getPasswordLength()) {
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $oEx->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_TOO_SHORT'));

            return $this->addValidationError("oxuser__oxpassword", $oEx);
        }

        //  passwords do not match ?
        if ($sNewPass != $sConfPass) {
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\UserException::class);
            $oEx->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH'));

            return $this->addValidationError("oxuser__oxpassword", $oEx);
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
     * @param \OxidEsales\Eshop\Application\Model\User $oUser            active user
     * @param array                                    $aBillingAddress  billing address
     * @param array                                    $aDeliveryAddress delivery address
     */
    public function checkRequiredFields($oUser, $aBillingAddress, $aDeliveryAddress)
    {
        /** @var \OxidEsales\Eshop\Application\Model\RequiredAddressFields $oRequiredAddressFields */
        $oRequiredAddressFields = oxNew(\OxidEsales\Eshop\Application\Model\RequiredAddressFields::class);

        /** @var \OxidEsales\Eshop\Application\Model\RequiredFieldsValidator $oFieldsValidator */
        $oFieldsValidator = oxNew(\OxidEsales\Eshop\Application\Model\RequiredFieldsValidator::class);

        /** @var \OxidEsales\Eshop\Application\Model\User $oUser */
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oBillingAddress = $this->_setFields($oUser, $aBillingAddress);
        $oFieldsValidator->setRequiredFields($oRequiredAddressFields->getBillingFields());
        $oFieldsValidator->validateFields($oBillingAddress);
        $aInvalidFields = $oFieldsValidator->getInvalidFields();

        if (!empty($aDeliveryAddress)) {
            /** @var \OxidEsales\Eshop\Application\Model\Address $oDeliveryAddress */
            $oDeliveryAddress = $this->_setFields(oxNew(\OxidEsales\Eshop\Application\Model\Address::class), $aDeliveryAddress);
            $oFieldsValidator->setRequiredFields($oRequiredAddressFields->getDeliveryFields());
            $oFieldsValidator->validateFields($oDeliveryAddress);
            $aInvalidFields = array_merge($aInvalidFields, $oFieldsValidator->getInvalidFields());
        }

        foreach ($aInvalidFields as $sField) {
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $oEx->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

            $this->addValidationError($sField, $oEx);
        }
    }

    /**
     * Creates oxAddress object from given array.
     *
     * @param \OxidEsales\Eshop\Application\Model\User|oxAddress $oObject
     * @param array                                              $aFields
     *
     * @return \OxidEsales\Eshop\Application\Model\User|oxAddress
     */
    private function _setFields($oObject, $aFields)
    {
        $aFields = is_array($aFields) ? $aFields : [];
        foreach ($aFields as $sKey => $sValue) {
            $oObject->$sKey = oxNew('oxField', $sValue);
        }

        return $oObject;
    }

    /**
     * Checks if user defined countries (billing and delivery) are active
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser       active user
     * @param array                                    $aInvAddress billing address info
     * @param array                                    $aDelAddress delivery address info
     */
    public function checkCountries($oUser, $aInvAddress, $aDelAddress)
    {
        $sBillCtry = isset($aInvAddress['oxuser__oxcountryid']) ? $aInvAddress['oxuser__oxcountryid'] : null;
        $sDelCtry = isset($aDelAddress['oxaddress__oxcountryid']) ? $aDelAddress['oxaddress__oxcountryid'] : null;

        if ($sBillCtry || $sDelCtry) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

            if (($sBillCtry == $sDelCtry) || (!$sBillCtry && $sDelCtry) || ($sBillCtry && !$sDelCtry)) {
                $sBillCtry = $sBillCtry ? $sBillCtry : $sDelCtry;
                $sQ = "select oxactive from oxcountry where oxid = " . $oDb->quote($sBillCtry) . " ";
            } else {
                $sQ = "select ( select oxactive from oxcountry where oxid = " . $oDb->quote($sBillCtry) . " ) and
                              ( select oxactive from oxcountry where oxid = " . $oDb->quote($sDelCtry) . " ) ";
            }

            if (!$oDb->getOne($sQ)) {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\UserException::class);
                $oEx->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

                $this->addValidationError("oxuser__oxcountryid", $oEx);
            }
        }
    }

    /**
     * Checks if user passed VAT id is valid. Exception is thrown
     * if id is not valid
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser       active user
     * @param array                                    $aInvAddress user input array
     *
     * @return null
     */
    public function checkVatId($oUser, $aInvAddress)
    {
        if ($this->_hasRequiredParametersForVatInCheck($aInvAddress)) {
            $oCountry = $this->_getCountry($aInvAddress['oxuser__oxcountryid']);

            if ($oCountry && $oCountry->isInEU()) {
                $oVatInValidator = $this->getCompanyVatInValidator($oCountry);

                /** @var \OxidEsales\Eshop\Application\Model\CompanyVatIn $oVatIn */
                $oVatIn = oxNew('oxCompanyVatIn', $aInvAddress['oxuser__oxustid']);

                if (!$oVatInValidator->validate($oVatIn)) {
                    /** @var \OxidEsales\Eshop\Core\Exception\InputException $oEx */
                    $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
                    $oEx->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('VAT_MESSAGE_' . $oVatInValidator->getError()));

                    return $this->addValidationError("oxuser__oxustid", $oEx);
                }
            }
        } elseif ($aInvAddress['oxuser__oxustid'] && !$aInvAddress['oxuser__oxcompany']) {
            /** @var \OxidEsales\Eshop\Core\Exception\InputException $oEx */
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $oEx->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('VAT_MESSAGE_COMPANY_MISSING'));

            return $this->addValidationError("oxuser__oxcompany", $oEx);
        }
    }


    /**
     * Load and return oxCountry
     *
     * @param string $sCountryId
     *
     * @return \OxidEsales\Eshop\Application\Model\Country
     */
    protected function _getCountry($sCountryId)
    {
        $oCountry = oxNew(\OxidEsales\Eshop\Application\Model\Country::class);
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
                    $oCardValidator = oxNew(\OxidEsales\Eshop\Core\CreditCardValidator::class);
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
     * @param string     $sFieldName field name
     * @param \Exception $oErr       exception
     *
     * @return \Exception
     */
    public function addValidationError($sFieldName, $oErr)
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
        $oSepaValidator = oxNew(\OxidEsales\Eshop\Core\SepaValidator::class);

        if (empty($sBankCode) || $oSepaValidator->isValidBIC($sBankCode)) {
            $mxValidationResult = true;
            if (!$oSepaValidator->isValidIBAN($sAccountNumber)) {
                $mxValidationResult = self::INVALID_ACCOUNT_NUMBER;
            }
        } else {
            $mxValidationResult = self::INVALID_BANK_CODE;
            if (!\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blSkipDebitOldBankInfo')) {
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
     * @param \OxidEsales\Eshop\Core\CompanyVatInValidator $oCompanyVatInValidator validator
     */
    public function setCompanyVatInValidator($oCompanyVatInValidator)
    {
        $this->_oCompanyVatInValidator = $oCompanyVatInValidator;
    }

    /**
     * Return VAT IN validator
     *
     * @param \OxidEsales\Eshop\Application\Model\Country $oCountry country according which VAT id should be checked
     *
     * @return \OxidEsales\Eshop\Core\CompanyVatInValidator
     */
    public function getCompanyVatInValidator($oCountry)
    {
        if (is_null($this->_oCompanyVatInValidator)) {
            /** @var \OxidEsales\Eshop\Core\CompanyVatInValidator $oVatInValidator */
            $oVatInValidator = oxNew('oxCompanyVatInValidator', $oCountry);

            /** @var  oxCompanyVatInCountryChecker $oValidator */
            $oValidator = oxNew(\OxidEsales\Eshop\Core\CompanyVatInCountryChecker::class);

            $oVatInValidator->addChecker($oValidator);

            /** @var \OxidEsales\Eshop\Core\OnlineVatIdCheck $oOnlineValidator */
            if (!\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam("blVatIdCheckDisabled")) {
                $oOnlineValidator = oxNew(\OxidEsales\Eshop\Core\OnlineVatIdCheck::class);
                $oVatInValidator->addChecker($oOnlineValidator);
            }

            $this->setCompanyVatInValidator($oVatInValidator);
        }

        return $this->_oCompanyVatInValidator;
    }
}
