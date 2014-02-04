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

/**
 * Class for validating input
 *
 */
class oxInputValidator extends oxSuperCfg
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
     * oxInputValidator instance
     *
     * @var oxDeliveryList
     */
    private static $_instance = null;

    /**
     * Required fields for credit card payment
     *
     * @var array
     */
    protected $_aRequiredCCFields = array( 'kktype',
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

    /**
     * Possible credit card types
     *
     * @var array
     */
    protected $_aPossibleCCType = array( 'mcd', // Master Card
                                         'vis', // Visa
                                         'amx', // American Express
                                         'dsc', // Discover
                                         'dnc', // Diners Club
                                         'jcb', // JCB
                                         'swi', // Switch
                                         'dlt', // Delta
                                         'enr'  // EnRoute
                                        );

    /**
     * Required fields for debit cards
     *
     * @var array
     */
    protected $_aRequiredDCFields = array( 'lsbankname',
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
     * Returns oxInputValidator instance
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxInputValidator") instead
     *
     * @return oxInputValidator
     */
    static function getInstance()
    {
        return oxRegistry::get("oxInputValidator");
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
    public function validateBasketAmount( $dAmount )
    {
        $dAmount = str_replace( ',', '.', $dAmount );

        if ( !is_numeric( $dAmount ) || $dAmount < 0) {
            /**
             * @var oxArticleInputException $oEx
             */
            $oEx = oxNew( 'oxArticleInputException' );
            $oEx->setMessage('ERROR_MESSAGE_INPUT_INVALIDAMOUNT');
            throw $oEx;
        }

        if ( !oxRegistry::getConfig()->getConfigParam( 'blAllowUnevenAmounts' ) ) {
            $dAmount = round( ( string ) $dAmount );
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
    public function checkLogin( $oUser, $sLogin, $aInvAddress )
    {
        // check only for users with password during registration
        // if user wants to change user name - we must check if passwords are ok before changing
        if ( $oUser->oxuser__oxpassword->value && $sLogin != $oUser->oxuser__oxusername->value ) {

            // on this case password must be taken directly from request
            $sNewPass = (isset( $aInvAddress['oxuser__oxpassword']) && $aInvAddress['oxuser__oxpassword'] )?$aInvAddress['oxuser__oxpassword']:oxConfig::getParameter( 'user_password' );
            if ( !$sNewPass ) {

                // 1. user forgot to enter password
                $oEx = oxNew( 'oxInputException' );
                $oEx->setMessage('ERROR_MESSAGE_INPUT_NOTALLFIELDS');

                return $this->_addValidationError( "oxuser__oxpassword", $oEx );
            } else {

                // 2. entered wrong password
                if ( !$oUser->isSamePassword( $sNewPass ) ) {
                    $oEx = oxNew( 'oxUserException' );
                    $oEx->setMessage('ERROR_MESSAGE_USER_PWDDONTMATCH');

                    return $this->_addValidationError( "oxuser__oxpassword", $oEx );
                }
            }
        }

        if ( $oUser->checkIfEmailExists( $sLogin ) ) {
            //if exists then we do now allow to do that
            $oEx = oxNew( 'oxUserException' );
            $oLang = oxRegistry::getLang();
            $oEx->setMessage( sprintf( $oLang->translateString( 'ERROR_MESSAGE_USER_USEREXISTS', $oLang->getTplLanguage() ), $sLogin ) );

            return $this->_addValidationError( "oxuser__oxusername", $oEx );
        }
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
    public function checkEmail(  $oUser, $sEmail )
    {
        // missing email address (user login name) ?
        if ( !$sEmail ) {
            $oEx = oxNew( 'oxInputException' );
            $oEx->setMessage('ERROR_MESSAGE_INPUT_NOTALLFIELDS');

            return $this->_addValidationError( "oxuser__oxusername", $oEx );
        }

        // invalid email address ?
        if ( !oxRegistry::getUtils()->isValidEmail( $sEmail ) ) {
            $oEx = oxNew( 'oxInputException' );
            $oEx->setMessage( 'ERROR_MESSAGE_INPUT_NOVALIDEMAIL' );

            return $this->_addValidationError( "oxuser__oxusername", $oEx );
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
     * @return null
     */
    public function checkPassword( $oUser, $sNewPass, $sConfPass, $blCheckLength = false )
    {
        //  no password at all
        if ( $blCheckLength && getStr()->strlen( $sNewPass ) == 0 ) {
            $oEx = oxNew( 'oxInputException' );
            $oEx->setMessage('ERROR_MESSAGE_INPUT_EMPTYPASS');

            return $this->_addValidationError( "oxuser__oxpassword", $oEx );
        }

        //  password is too short ?
        if ( $blCheckLength &&  getStr()->strlen( $sNewPass ) < 6 ) {
            $oEx = oxNew( 'oxInputException' );
            $oEx->setMessage('ERROR_MESSAGE_PASSWORD_TOO_SHORT');

            return $this->_addValidationError( "oxuser__oxpassword", $oEx );
        }

        //  passwords do not match ?
        if ( $sNewPass != $sConfPass ) {
            $oEx = oxNew( 'oxUserException' );
            $oEx->setMessage('ERROR_MESSAGE_USER_PWDDONTMATCH');

            return $this->_addValidationError( "oxuser__oxpassword", $oEx );
        }
    }

    /**
     * Checking if all required fields were filled. In case of error
     * exception is thrown
     *
     * @param oxUser $oUser       active user
     * @param array  $aInvAddress billing address
     * @param array  $aDelAddress delivery address
     *
     * @return null
     */
    public function checkRequiredFields( $oUser, $aInvAddress, $aDelAddress )
    {
        // collecting info about required fields
        $aMustFields = array( 'oxuser__oxfname',
                              'oxuser__oxlname',
                              'oxuser__oxstreetnr',
                              'oxuser__oxstreet',
                              'oxuser__oxzip',
                              'oxuser__oxcity' );

        // config should override default fields
        $aMustFillFields = $this->getConfig()->getConfigParam( 'aMustFillFields' );
        if ( is_array( $aMustFillFields ) ) {
            $aMustFields = $aMustFillFields;
        }

        // assuring data to check
        $aInvAddress = is_array( $aInvAddress )?$aInvAddress:array();
        $aDelAddress = is_array( $aDelAddress )?$aDelAddress:array();

        // collecting fields
        $aFields = array_merge( $aInvAddress, $aDelAddress );


        // check delivery address ?
        $blCheckDel = false;
        if ( count( $aDelAddress ) ) {
            $blCheckDel = true;
        }

        // checking
        foreach ( $aMustFields as $sMustField ) {

            // A. not nice, but we keep all fields info in one config array, and must support backward compatibility.
            if ( !$blCheckDel && strpos( $sMustField, 'oxaddress__' ) === 0 ) {
                continue;
            }

            if ( isset( $aFields[$sMustField] ) && is_array( $aFields[$sMustField] ) ) {
                $this->checkRequiredArrayFields( $oUser, $sMustField, $aFields[$sMustField] );
            } elseif ( !isset( $aFields[$sMustField] ) || !trim( $aFields[$sMustField] ) ) {
                   $oEx = oxNew( 'oxInputException' );
                   $oEx->setMessage('ERROR_MESSAGE_INPUT_NOTALLFIELDS');

                   $this->_addValidationError( $sMustField, $oEx );
            }
        }
    }

    /**
     * Checks if all values are filled up
     *
     * @param oxUser $oUser        active user
     * @param string $sFieldName   checking field name
     * @param array  $aFieldValues field values
     *
     * @return null
     */
    public function checkRequiredArrayFields( $oUser, $sFieldName, $aFieldValues )
    {
        foreach ( $aFieldValues as $sValue ) {
            if ( !trim( $sValue ) ) {
                $oEx = oxNew( 'oxInputException' );
                $oEx->setMessage('ERROR_MESSAGE_INPUT_NOTALLFIELDS');

                $this->_addValidationError( $sFieldName, $oEx );
            }
        }
    }

    /**
     * Checks if user defined countries (billing and delivery) are active
     *
     * @param oxUser $oUser       active user
     * @param array  $aInvAddress billing address info
     * @param array  $aDelAddress delivery address info
     *
     * @return null
     */
    public function checkCountries( $oUser, $aInvAddress, $aDelAddress )
    {
        $sBillCtry = isset( $aInvAddress['oxuser__oxcountryid'] ) ? $aInvAddress['oxuser__oxcountryid'] : null;
        $sDelCtry  = isset( $aDelAddress['oxaddress__oxcountryid'] ) ? $aDelAddress['oxaddress__oxcountryid'] : null;

        if ( $sBillCtry || $sDelCtry ) {
            $oDb = oxDb::getDb();

            if ( ( $sBillCtry == $sDelCtry ) || ( !$sBillCtry && $sDelCtry ) || ( $sBillCtry && !$sDelCtry ) ) {
                $sBillCtry = $sBillCtry ? $sBillCtry : $sDelCtry;
                $sQ = "select oxactive from oxcountry where oxid = ".$oDb->quote( $sBillCtry )." ";
            } else {
                $sQ = "select ( select oxactive from oxcountry where oxid = ".$oDb->quote( $sBillCtry )." ) and
                              ( select oxactive from oxcountry where oxid = ".$oDb->quote( $sDelCtry )." ) ";
            }

            if ( !$oDb->getOne( $sQ ) ) {
                $oEx = oxNew( 'oxUserException' );
                $oEx->setMessage('ERROR_MESSAGE_INPUT_NOTALLFIELDS' );

                $this->_addValidationError( "oxuser__oxpassword", $oEx );
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
    public function checkVatId( $oUser, $aInvAddress )
    {
        if ( $aInvAddress['oxuser__oxustid'] ) {

            if (!($sCountryId = $aInvAddress['oxuser__oxcountryid'])) {
                // no country
                return;
            }
            $oCountry = oxNew('oxcountry');
            if ( $oCountry->load( $sCountryId ) && $oCountry->isForeignCountry() && $oCountry->isInEU() ) {

                    if ( strncmp( $aInvAddress['oxuser__oxustid'], $oCountry->oxcountry__oxisoalpha2->value, 2 ) ) {
                        $oEx = oxNew( 'oxInputException' );
                        $oEx->setMessage( 'VAT_MESSAGE_ID_NOT_VALID' );

                        return $this->_addValidationError( "oxuser__oxustid", $oEx );
                    }

            }
        }
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
        $oErr = null;
        $aErr = reset( $this->_aInputValidationErrors );
        if ( is_array( $aErr ) ) {
            $oErr = reset( $aErr );
        }
        return $oErr;
    }

    /**
     * Validates payment input data for credit card and debit note
     *
     * @param string $sPaymentId the payment id of current payment
     * @param array  &$aDynValue values of payment
     *
     * @return bool
     */
    public function validatePaymentInputData( $sPaymentId, & $aDynValue )
    {
        $mxValidationResult = true;

        switch( $sPaymentId ) {
            case 'oxidcreditcard':
                $mxValidationResult = false;

                $blAllCreditCardInformationSet = $this->_isAllBankInformationSet( $this->_aRequiredCCFields, $aDynValue );
                $blCreditCardTypeExist = in_array( $aDynValue['kktype'], $this->_aPossibleCCType );

                if ( $blAllCreditCardInformationSet && $blCreditCardTypeExist ) {
                    $oCardValidator = oxNew( "oxccvalidator" );
                    $mxValidationResult = $oCardValidator->isValidCard(
                                                    $aDynValue['kknumber'],
                                                    $aDynValue['kktype'],
                                                    $aDynValue['kkmonth'].substr( $aDynValue['kkyear'], 2, 2 )
                    );
                }
                break;

            case "oxiddebitnote":
                $mxValidationResult = false;

                if ( $this->_isAllBankInformationSet( $this->_aRequiredDCFields, $aDynValue ) ) {
                    $mxValidationResult = $this->_validateDebitNote( $aDynValue );
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
    protected function _addValidationError( $sFieldName, $oErr )
    {
        return $this->_aInputValidationErrors[$sFieldName][] = $oErr;
    }

    /**
     * @param $aDebitInformation
     *
     * @return bool|int
     */
    protected function _validateDebitNote( $aDebitInformation )
    {
        $aDebitInformation = $this->_cleanDebitInformation( $aDebitInformation );
        $sBankCode = $aDebitInformation['lsblz'];
        $sAccountNumber = $aDebitInformation['lsktonr'];
        $oSepaValidator = oxNew( "oxSepaValidator" );

        if ( empty( $sBankCode ) || $oSepaValidator->isValidBIC( $sBankCode ) ) {
            $mxValidationResult = true;
            if ( !$oSepaValidator->isValidIBAN( $sAccountNumber ) ) {
                $mxValidationResult = self::INVALID_ACCOUNT_NUMBER;
            }
        } else {
            $mxValidationResult = self::INVALID_BANK_CODE;
            if ( !oxRegistry::getConfig()->getConfigParam( 'blSkipDebitOldBankInfo' ) ) {
                $mxValidationResult = $this->_validateOldDebitInfo( $aDebitInformation );
            }
        }

        return $mxValidationResult;
    }

    /**
     * @param $aDebitInfo
     * @return bool|int
     */
    protected function _validateOldDebitInfo( $aDebitInfo )
    {
        $oStr       = getStr();
        $aDebitInfo = $this->_fixAccountNumber( $aDebitInfo );

        $mxValidationResult = true;

        if ( !$oStr->preg_match( "/^\d{5,8}$/", $aDebitInfo['lsblz'] ) ) {
            // Bank code is invalid
            $mxValidationResult = self::INVALID_BANK_CODE;
        }

        if ( true === $mxValidationResult && !$oStr->preg_match( "/^\d{10,12}$/", $aDebitInfo['lsktonr'] ) ) {
            // Account number is invalid
            $mxValidationResult = self::INVALID_ACCOUNT_NUMBER;
        }


        return $mxValidationResult;
    }

    /**
     * If account number is shorter than 10, add zeros in front of number.
     * @param $aDebitInfo
     * @return array
     */
    protected function _fixAccountNumber( $aDebitInfo )
    {
        $oStr = getStr();

        if ( $oStr->strlen( $aDebitInfo['lsktonr'] ) < 10 ) {
            $sNewNum = str_repeat(
                           '0', 10 - $oStr->strlen( $aDebitInfo['lsktonr'] )
                       ) . $aDebitInfo['lsktonr'];
            $aDebitInfo['lsktonr'] = $sNewNum;
        }

        return $aDebitInfo;
    }

    /**
     * @param array $aRequiredFields fields must be set.
     * @param array $aBankInformation actual information.
     *
     * @return bool
     */
    protected function _isAllBankInformationSet( $aRequiredFields, $aBankInformation )
    {
        $blResult = true;
        foreach ( $aRequiredFields as $sFieldName ) {
            if ( !isset( $aBankInformation[$sFieldName] ) || !trim( $aBankInformation[$sFieldName] ) ) {
                $blResult = false;
                break;
            }
        }

        return $blResult;
    }

    /**
     * Clean up spaces.
     * @param $aDebitInformation
     * @return mixed
     */
    protected function _cleanDebitInformation( $aDebitInformation )
    {
        $aDebitInformation['lsblz']   = str_replace( ' ', '', $aDebitInformation['lsblz'] );
        $aDebitInformation['lsktonr'] = str_replace( ' ', '', $aDebitInformation['lsktonr'] );

        return $aDebitInformation;
    }
}