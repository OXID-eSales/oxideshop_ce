<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Exception\ArticleInputException;
use OxidEsales\Eshop\Core\Exception\StandardException;

/**
 * Class for validating input.
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
     * Required fields for credit card payment.
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
     * Input validation errors.
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
     * Required fields for debit cards.
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
     * Validates basket amount.
     *
     * @param float $amount Amount of article.
     *
     * @throws ArticleInputException If amount is not numeric or smaller 0.
     *
     * @return float
     */
    public function validateBasketAmount($amount)
    {
        $amount = str_replace(',', '.', $amount);

        if (!is_numeric($amount) || $amount < 0) {
            /**
             * @var \OxidEsales\Eshop\Core\Exception\ArticleInputException $exception
             */
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\ArticleInputException::class);
            $exception->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_INVALIDAMOUNT'));
            throw $exception;
        }

        if (!\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blAllowUnevenAmounts')) {
            $amount = round(( string ) $amount);
        }

        //negative amounts are not allowed
        //$dAmount = abs($dAmount);

        return $amount;
    }

    /**
     * Checks if user name does not break logic:
     *  - if user wants to UPDATE his login name, performing check if
     *    user entered correct password
     *  - additionally checking for user name duplicates. This is usually
     *    needed when creating new users.
     * On any error exception is thrown.
     *
     * @param User   $user       Active user.
     * @param string $login      User preferred login name.
     * @param array  $invAddress User information.
     *
     * @return string login name
     */
    public function checkLogin($user, $login, $invAddress)
    {
        $login = (isset($invAddress['oxuser__oxusername'])) ? $invAddress['oxuser__oxusername'] : $login;

        // check only for users with password during registration
        // if user wants to change user name - we must check if passwords are ok before changing
        if ($user->oxuser__oxpassword->value && $login != $user->oxuser__oxusername->value) {
            // on this case password must be taken directly from request
            $newPassword = (isset($invAddress['oxuser__oxpassword']) && $invAddress['oxuser__oxpassword']) ? $invAddress['oxuser__oxpassword'] : \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('user_password');
            if (!$newPassword) {
                // 1. user forgot to enter password
                $exception = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
                $exception->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

                return $this->_addValidationError("oxuser__oxpassword", $exception);
            } else {
                // 2. entered wrong password
                if (!$user->isSamePassword($newPassword)) {
                    $exception = oxNew(\OxidEsales\Eshop\Core\Exception\UserException::class);
                    $exception->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH'));

                    return $this->_addValidationError("oxuser__oxpassword", $exception);
                }
            }
        }

        if ($user->checkIfEmailExists($login)) {
            //if exists then we do not allow to do that
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\UserException::class);
            $exception->setMessage(sprintf(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_USER_USEREXISTS'), $login));

            return $this->_addValidationError("oxuser__oxusername", $exception);
        }

        return $login;
    }

    /**
     * Checks if email (used as login) is not empty and is
     * valid.
     *
     * @param User   $user  Active user.
     * @param string $email User email/login.
     *
     * @return null
     */
    public function checkEmail($user, $email)
    {
        // missing email address (user login name) ?
        if (!$email) {
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $exception->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

            return $this->_addValidationError("oxuser__oxusername", $exception);
        }

        // invalid email address ?
        if (!oxNew(\OxidEsales\Eshop\Core\MailValidator::class)->isValidEmail($email)) {
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $exception->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOVALIDEMAIL'));

            return $this->_addValidationError("oxuser__oxusername", $exception);
        }
    }

    /**
     * Checking if user password is fine. In case of error
     * exception is thrown
     *
     * @param User   $user                      Active user.
     * @param string $newPassword               New user password.
     * @param string $confirmationPassword      Retyped user password.
     * @param bool   $shouldCheckPasswordLength Option to check password length.
     *
     * @return Exception\StandardException|null
     */
    public function checkPassword($user, $newPassword, $confirmationPassword, $shouldCheckPasswordLength = false)
    {
        //  no password at all
        if ($shouldCheckPasswordLength && getStr()->strlen($newPassword) == 0) {
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $exception->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_EMPTYPASS'));

            return $this->_addValidationError("oxuser__oxpassword", $exception);
        }

        if ($shouldCheckPasswordLength && getStr()->strlen($newPassword) < $this->getPasswordLength()) {
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $exception->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_TOO_SHORT'));

            return $this->_addValidationError("oxuser__oxpassword", $exception);
        }

        //  passwords do not match ?
        if ($newPassword != $confirmationPassword) {
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\UserException::class);
            $exception->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH'));

            return $this->_addValidationError("oxuser__oxpassword", $exception);
        }
    }

    /**
     * Min length of password.
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
     * @param User  $user            Active user.
     * @param array $billingAddress  Billing address.
     * @param array $deliveryAddress Delivery address.
     */
    public function checkRequiredFields($user, $billingAddress, $deliveryAddress)
    {
        /** @var \OxidEsales\Eshop\Application\Model\RequiredAddressFields $requiredAddressFields */
        $requiredAddressFields = oxNew(\OxidEsales\Eshop\Application\Model\RequiredAddressFields::class);

        /** @var \OxidEsales\Eshop\Application\Model\RequiredFieldsValidator $fieldsValidator */
        $fieldsValidator = oxNew(\OxidEsales\Eshop\Application\Model\RequiredFieldsValidator::class);

        /** @var User $user */
        $user = oxNew(User::class);
        $billingAddress = $this->_setFields($user, $billingAddress);
        $fieldsValidator->setRequiredFields($requiredAddressFields->getBillingFields());
        $fieldsValidator->validateFields($billingAddress);
        $invalidFields = $fieldsValidator->getInvalidFields();

        if (!empty($deliveryAddress)) {
            /** @var \OxidEsales\Eshop\Application\Model\Address $deliveryAddress */
            $deliveryAddress = $this->_setFields(oxNew(\OxidEsales\Eshop\Application\Model\Address::class), $deliveryAddress);
            $fieldsValidator->setRequiredFields($requiredAddressFields->getDeliveryFields());
            $fieldsValidator->validateFields($deliveryAddress);
            $invalidFields = array_merge($invalidFields, $fieldsValidator->getInvalidFields());
        }

        foreach ($invalidFields as $sField) {
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $exception->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

            $this->_addValidationError($sField, $exception);
        }
    }

    /**
     * Creates oxAddress object from given array.
     *
     * @param User|Address $object
     * @param array        $fields
     *
     * @return User|Address
     */
    private function _setFields($object, $fields)
    {
        $fields = is_array($fields) ? $fields : [];
        foreach ($fields as $sKey => $sValue) {
            $object->$sKey = oxNew('oxField', $sValue);
        }

        return $object;
    }

    /**
     * Checks if user defined countries (billing and delivery) are active.
     *
     * @param User  $user            Active user.
     * @param array $invAddress      Billing address info.
     * @param array $deliveryAddress Delivery address info.
     */
    public function checkCountries($user, $invAddress, $deliveryAddress)
    {
        $billingCountry = isset($invAddress['oxuser__oxcountryid']) ? $invAddress['oxuser__oxcountryid'] : null;
        $deliveryCountry = isset($deliveryAddress['oxaddress__oxcountryid']) ? $deliveryAddress['oxaddress__oxcountryid'] : null;

        if ($billingCountry || $deliveryCountry) {
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

            if (($billingCountry == $deliveryCountry) || (!$billingCountry && $deliveryCountry) || ($billingCountry && !$deliveryCountry)) {
                $billingCountry = $billingCountry ? $billingCountry : $deliveryCountry;
                $query = "select oxactive from oxcountry where oxid = :oxbillingid";
                $params = [
                    ':oxbillingid' => $billingCountry
                ];
            } else {
                $query = "select ( select oxactive from oxcountry where oxid = :oxbillingid ) and
                              ( select oxactive from oxcountry where oxid = :oxdeliveryid ) ";
                $params = [
                    ':oxbillingid' => $billingCountry,
                    ':oxdeliveryid' => $deliveryCountry,
                ];
            }

            if (!$database->getOne($query, $params)) {
                $exception = oxNew(\OxidEsales\Eshop\Core\Exception\UserException::class);
                $exception->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));

                $this->_addValidationError("oxuser__oxcountryid", $exception);
            }
        }
    }

    /**
     * Checks if user passed VAT id is valid. Exception is thrown
     * if id is not valid.
     *
     * @param User  $user       Active user.
     * @param array $invAddress User input array.
     *
     * @return null
     */
    public function checkVatId($user, $invAddress)
    {
        if ($this->_hasRequiredParametersForVatInCheck($invAddress)) {
            $country = $this->_getCountry($invAddress['oxuser__oxcountryid']);

            if ($country && $country->isInEU()) {
                $vatInValidator = $this->getCompanyVatInValidator($country);

                /** @var \OxidEsales\Eshop\Application\Model\CompanyVatIn $oVatIn */
                $oVatIn = oxNew('oxCompanyVatIn', $invAddress['oxuser__oxustid']);

                if (!$vatInValidator->validate($oVatIn)) {
                    /** @var \OxidEsales\Eshop\Core\Exception\InputException $exception */
                    $exception = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
                    $exception->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('VAT_MESSAGE_' . $vatInValidator->getError()));

                    return $this->_addValidationError("oxuser__oxustid", $exception);
                }
            }
        } elseif ($invAddress['oxuser__oxustid'] && !$invAddress['oxuser__oxcompany']) {
            /** @var \OxidEsales\Eshop\Core\Exception\InputException $exception */
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $exception->setMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('VAT_MESSAGE_COMPANY_MISSING'));

            return $this->_addValidationError("oxuser__oxcompany", $exception);
        }
    }


    /**
     * Load and return Country object.
     *
     * @param string $countryId
     *
     * @return \OxidEsales\Eshop\Application\Model\Country
     */
    protected function _getCountry($countryId)
    {
        $country = oxNew(\OxidEsales\Eshop\Application\Model\Country::class);
        $country->load($countryId);

        return $country;
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
     * Returns first user input validation error.
     *
     * @return StandardException
     */
    public function getFirstValidationError()
    {
        $aErr = reset($this->_aInputValidationErrors);
        if (is_array($aErr)) {
            return reset($aErr);
        }
    }

    /**
     * Validates payment input data for credit card and debit note.
     *
     * @param string $paymentId    The payment id of current payment.
     * @param array  $dynamicValue Values of payment.
     *
     * @return bool
     */
    public function validatePaymentInputData($paymentId, &$dynamicValue)
    {
        $validationResult = true;

        switch ($paymentId) {
            case 'oxidcreditcard':
                $validationResult = false;

                $idAllCreditCardInformationSet = $this->_isAllBankInformationSet($this->_aRequiredCCFields, $dynamicValue);
                $doesCreditCardTypeExist = in_array($dynamicValue['kktype'], $this->_aPossibleCCType);

                if ($idAllCreditCardInformationSet && $doesCreditCardTypeExist) {
                    $cardValidator = oxNew(\OxidEsales\Eshop\Core\CreditCardValidator::class);
                    $validationResult = $cardValidator->isValidCard(
                        $dynamicValue['kknumber'],
                        $dynamicValue['kktype'],
                        $dynamicValue['kkmonth'] . substr($dynamicValue['kkyear'], 2, 2)
                    );
                }
                break;

            case "oxiddebitnote":
                $validationResult = false;

                if ($this->_isAllBankInformationSet($this->_aRequiredDCFields, $dynamicValue)) {
                    $validationResult = $this->_validateDebitNote($dynamicValue);
                }

                break;
        }

        return $validationResult;
    }

    /**
     * Used to collect user validation errors. This method is called from all of
     * the input checking functionality to report found error.
     *
     * @deprecated since v6.0.0(2017-12-22); Use addValidationError.
     *
     * @param string            $fieldName Field name.
     * @param StandardException $error     Exception.
     *
     * @return StandardException
     */
    protected function _addValidationError($fieldName, $error)
    {
        return $this->addValidationError($fieldName, $error);
    }

    /**
     * Used to collect user validation errors. This method is called from all of
     * the input checking functionality to report found error.
     *
     * @param string            $fieldName
     * @param StandardException $error
     *
     * @return StandardException
     */
    public function addValidationError($fieldName, $error)
    {
        return $this->_aInputValidationErrors[$fieldName][] = $error;
    }

    /**
     * Validates debit note.
     *
     * @param array $debitInformation Debit information
     *
     * @return bool|int
     */
    protected function _validateDebitNote($debitInformation)
    {
        $debitInformation = $this->_cleanDebitInformation($debitInformation);
        $bankCode = $debitInformation['lsblz'];
        $accountNumber = $debitInformation['lsktonr'];
        $sepaValidator = oxNew(SepaValidator::class);

        if ($sepaValidator->isValidBIC($bankCode)) {
            $validateResult = true;
            if (!$sepaValidator->isValidIBAN($accountNumber)) {
                $validateResult = self::INVALID_ACCOUNT_NUMBER;
            }
        } else {
            $validateResult = self::INVALID_BANK_CODE;
            if (!\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blSkipDebitOldBankInfo')) {
                $validateResult = $this->_validateOldDebitInfo($debitInformation);
            }
        }

        return $validateResult;
    }

    /**
     * Validates old debit info.
     *
     * @param array $debitInfo Debit info
     *
     * @return bool|int
     */
    protected function _validateOldDebitInfo($debitInfo)
    {
        $stringHelper = getStr();
        $debitInfo = $this->_fixAccountNumber($debitInfo);

        $validationResult = true;

        if (!$stringHelper->preg_match("/^\d{5,8}$/", $debitInfo['lsblz'])) {
            // Bank code is invalid
            $validationResult = self::INVALID_BANK_CODE;
        }

        if (true === $validationResult && !$stringHelper->preg_match("/^\d{10,12}$/", $debitInfo['lsktonr'])) {
            // Account number is invalid
            $validationResult = self::INVALID_ACCOUNT_NUMBER;
        }


        return $validationResult;
    }

    /**
     * If account number is shorter than 10, add zeros in front of number.
     *
     * @param array $debitInfo Debit info.
     *
     * @return array
     */
    protected function _fixAccountNumber($debitInfo)
    {
        $oStr = getStr();

        if ($oStr->strlen($debitInfo['lsktonr']) < 10) {
            $sNewNum = str_repeat(
                '0',
                10 - $oStr->strlen($debitInfo['lsktonr'])
            ) . $debitInfo['lsktonr'];
            $debitInfo['lsktonr'] = $sNewNum;
        }

        return $debitInfo;
    }

    /**
     * Checks if all bank information is set.
     *
     * @param array $requiredFields  fields must be set.
     * @param array $bankInformation actual information.
     *
     * @return bool
     */
    protected function _isAllBankInformationSet($requiredFields, $bankInformation)
    {
        $isSet = true;
        foreach ($requiredFields as $fieldName) {
            if (!isset($bankInformation[$fieldName]) || !trim($bankInformation[$fieldName])) {
                $isSet = false;
                break;
            }
        }

        return $isSet;
    }

    /**
     * Clean up spaces.
     *
     * @param array $debitInformation Debit information.
     *
     * @return mixed
     */
    protected function _cleanDebitInformation($debitInformation)
    {
        $debitInformation['lsblz'] = str_replace(' ', '', $debitInformation['lsblz']);
        $debitInformation['lsktonr'] = str_replace(' ', '', $debitInformation['lsktonr']);

        return $debitInformation;
    }

    /**
     * Check if all need parameters entered.
     *
     * @param array $invAddress Address.
     *
     * @return bool
     */
    protected function _hasRequiredParametersForVatInCheck($invAddress)
    {
        return $invAddress['oxuser__oxustid'] && $invAddress['oxuser__oxcountryid'] && $invAddress['oxuser__oxcompany'];
    }

    /**
     * VAT IN validator setter.
     *
     * @param \OxidEsales\Eshop\Core\CompanyVatInValidator $companyVatInValidator validator
     */
    public function setCompanyVatInValidator($companyVatInValidator)
    {
        $this->_oCompanyVatInValidator = $companyVatInValidator;
    }

    /**
     * Return VAT IN validator.
     *
     * @param \OxidEsales\Eshop\Application\Model\Country $country Country according which VAT id should be checked.
     *
     * @return \OxidEsales\Eshop\Core\CompanyVatInValidator
     */
    public function getCompanyVatInValidator($country)
    {
        if (is_null($this->_oCompanyVatInValidator)) {
            /** @var \OxidEsales\Eshop\Core\CompanyVatInValidator $vatInValidator */
            $vatInValidator = oxNew('oxCompanyVatInValidator', $country);

            /** @var \OxidEsales\Eshop\Core\CompanyVatInCountryChecker $validator */
            $validator = oxNew(\OxidEsales\Eshop\Core\CompanyVatInCountryChecker::class);

            $vatInValidator->addChecker($validator);

            /** @var \OxidEsales\Eshop\Core\OnlineVatIdCheck $onlineValidator */
            if (!\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam("blVatIdCheckDisabled")) {
                $onlineValidator = oxNew(\OxidEsales\Eshop\Core\OnlineVatIdCheck::class);
                $vatInValidator->addChecker($onlineValidator);
            }

            $this->setCompanyVatInValidator($vatInValidator);
        }

        return $this->_oCompanyVatInValidator;
    }
}
