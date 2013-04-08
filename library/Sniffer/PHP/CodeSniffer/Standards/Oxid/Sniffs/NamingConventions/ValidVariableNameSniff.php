<?php
/**
 * Squiz_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: ValidVariableNameSniff.php 261899 2008-07-02 05:08:16Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractVariableSniff not found');
}

/**
 * Squiz_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * Checks the naming of variables and member variables.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.2
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Oxid_Sniffs_NamingConventions_ValidVariableNameSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{
    /**
     * Tokens to ignore so that we can find a DOUBLE_COLON.
     *
     * @var array
     */
    private $_ignore = array(
        T_WHITESPACE,
        T_COMMENT,
    );

    /**
     * PHP reserved vars
     *
     * @var array
     */
    private $_phpReservedVars = array(
        '_SERVER',
        '_GET',
        '_POST',
        '_REQUEST',
        '_SESSION',
        '_ENV',
        '_COOKIE',
        '_FILES',
        'GLOBALS',
    );

    /**
     * Third party library variables ...
     *
     * oxid change
     *
     * @var array
     */
    protected $_aThirdPartyVars = array(
        'ADODB_FETCH_MODE',
        'ADODB_CACHE_DIR',
        'ADODB_DRIVER',
        'ADODB_SESSION_TBL',
        'ADODB_SESSION_CONNECT',
        'ADODB_SESSION_DRIVER',
        'ADODB_SESSION_USER',
        'ADODB_SESSION_PWD',
        'ADODB_SESSION_DB',
        'ADODB_SESS_LIFE',
        'ADODB_SESS_DEBUG',
        'EOF',

        //PHPMailer
        'SMTP_PORT',
        'Subject',
        'Body',
        'AltBody',
        'From',
        'FromName',
        'CharSet',
        'Mailer',
        'ErrorInfo',

        //OpenID
        'extra_headers',
        'curl_user_agent',
        'new_headers',
    );

    /**
     * Third party object properties ...
     *
     * oxid change
     *
     * @var array
     */
    protected $_aThirdPartyProperties = array(
        //AdoDB
        'EOF',
        'fldmax_length',
        'name',
        'max_length',
        'char',
        'scale',
        'not_null',
        'primary_key',
        'auto_increment',
        'binary',
        'unsigned',
        'has_default',
        'default_value',

        //PDF
        'HREF',

        //SMARTY
        '_tpl_vars',
        'tpl_vars',
        'template_dir',
        'security_settings',
        'php_handling',
        'compile_check',
        'force_compile',
        'default_template_handler_func',
        'secure_dir',
        'caching',
        'compile_dir',
        'cache_dir',
        'template_dir',
        'compile_id',
        'left_delimiter',
        'right_delimiter',
        'current_file',
        'auto_literal',
        'plugins_dir',

        //PHPMailer
        'Host',
        'Username',
        'Password',
        'From',
        'FromName',
        'Subject',
        'Body',
        'AltBody',
        'CharSet',
        'Mailer',
        'rec_email',
        'rec_name',
        'send_subject',
        'send_email',
        'send_name',
        'send_id',
        'ErrorInfo',
        'error_count',
        'SMTP_PORT',
        'WordWrap',

        //OpenID
        'handle',
        'secret',
        'issued',
        'lifetime',
        'negotiator',
        'fetcher',
        'assoc_type',
        '_use_assocs',
        'use_assocs',
        'session_types',
    );

    /**
     * most of them are from ERP interface ..
     *
     * @var array
     */
    protected $_aDeprecatedVars = array(
        //ERP
        '_aDbLayer2ShopDbVersions',
        '_aStatistics',
        '_iIdx',
        '_aDbLayer2ShopDbVersions',
        'ERROR_USER_NO_RIGHTS',
        'ERROR_USER_WRONG',
        'ERROR_WRONG_SHOPID',
        'ERROR_OBJECT_NOT_EXISTING',
        'ERROR_USER_EXISTS',
        'ERROR_NO_INIT',
        'ERROR_DELETE_NO_EMPTY_CATEGORY',
        'ERROR_OBJECT_NOT_EXISTING',
        'MODE_IMPORT',
        'MODE_DELETE',
        'CAN_NOT_IMPORT_SALT',
        'ERROR_USER_WRONG',
        'ERROR_USER_NO_RIGHTS',
        'ERROR_NO_INIT',
        'ERROR_USER_NO_RIGHTS',

        //LDAP
        'sr_result'
   );

    /**
     * deprecated properties ..
     *
     * @var array
     */
    protected $_aDeprecatedProperties = array(
        'nossl_dimagedir',
        'ssl_dimagedir',
        'chosen_selectlist',

        //ERP
        'STATUS',
        '_aStatistics',
        '_iIdx',

        //Pagination
        'NrOfPages',
    );

    /**
     * These variables are from config.inc.php, and historically we should leave them as is
     *
     * @var array
     */
    protected $_aConfigVars = array(
        'ShopURL',
        'SSLShopURL',
        'AdminSSLURL',
        'ShopDir',
        'CompileDir',
        'Debug',
        'AdminEmail',
        'MultiShopTables',
        'dbHost',
        'dbName',
        'dbUser',
        'dbPwd',
        'dbType',
        'sShopURL',
        'sSSLShopURL',
        'sAdminSSLURL',
        'sShopDir',
        'sCompileDir',
        'iDebug',
        'sAdminEmail',
        'blSessionUseCookies',
        'blSessionEnforceCookies',
        'blNativeImages',
        'aMultiShopTables',
        'NrOfPages'
    );

    protected $_sObjectFieldPattern = '/ox[a-z0-9]+__ox[a-z0-9]+/';

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    protected function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens     = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');
        // Ignoring reserved php, third party, depredated and config variables
        if (    in_array( $varName, $this->_phpReservedVars ) === true ||
                in_array( $varName, $this->_aThirdPartyVars ) === true ||
                in_array( $varName, $this->_aDeprecatedVars ) === true ||
                in_array( $varName, $this->_aConfigVars  )    === true ||
                preg_match($this->_sObjectFieldPattern, $varName) ) {
            return;
        }

        $objOperator = $phpcsFile->findNext(array(T_WHITESPACE), ($stackPtr + 1), null, true);
        if ($tokens[$objOperator]['code'] === T_OBJECT_OPERATOR) {
            // Check to see if we are using a variable from an object.
            $var = $phpcsFile->findNext(array(T_WHITESPACE), ($objOperator + 1), null, true);
            if ($tokens[$var]['code'] === T_STRING) {
                // Either a var name or a function call, so check for bracket.
                $bracket = $phpcsFile->findNext(array(T_WHITESPACE), ($var + 1), null, true);

                if ($tokens[$bracket]['code'] !== T_OPEN_PARENTHESIS) {
                    $objVarName = $tokens[$var]['content'];

                    // There is no way for us to know if the var is public or private,
                    // so we have to ignore a leading underscore if there is one and just
                    // check the main part of the variable name.
                    $originalVarName = $objVarName;
                    if (substr($objVarName, 0, 1) === '_') {
                        $objVarName = substr($objVarName, 1);
                    }


                    // Ignoring third party and internal database fields
                    if (    preg_match( $this->_sObjectFieldPattern, $objVarName ) ||
                            in_array( $objVarName, $this->_aThirdPartyProperties ) === true) {
                        return;
                    } else if (in_array( $objVarName, $this->_aDeprecatedProperties ) === true) {
                        $warning = "Variable \"$originalVarName\" is depracated";
                        $phpcsFile->addWarning($warning, $stackPtr);
                    } else if (PHP_CodeSniffer::isCamelCaps($objVarName, false, true, false) === false) {
                        $error = "Variable \"$originalVarName\" is not in valid camel caps format";
                        $phpcsFile->addError($error, $var);
                    } else if (PHP_CodeSniffer::isCamelCaps($objVarName, false, true, false) === false) {
                        $error = "Variable \"$originalVarName\" is not in valid camel caps format";
                        $phpcsFile->addError($error, $var);

                    } else if (preg_match('|\d|', $objVarName)) {
                        //$warning = "Variable \"$originalVarName\" contains numbers but this is discouraged";
                        //$phpcsFile->addWarning($warning, $stackPtr);
                    }

                }//end if
            }//end if
        }//end if

        // There is no way for us to know if the var is public or private,
        // so we have to ignore a leading underscore if there is one and just
        // check the main part of the variable name.
        $originalVarName = $varName;
        if (substr($varName, 0, 1) === '_') {
            $objOperator = $phpcsFile->findPrevious(array(T_WHITESPACE), ($stackPtr - 1), null, true);
            if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
                // The variable lives within a class, and is referenced like
                // this: MyClass::$_variable, so we don't know its scope.
                $inClass = true;
            } else {
                $inClass = $phpcsFile->hasCondition($stackPtr, array(T_CLASS, T_INTERFACE));
            }

            if ($inClass === true) {
                $varName = substr($varName, 1);
            }
        }

        if (PHP_CodeSniffer::isCamelCaps($varName, false, true, false) === false) {
            $error = "Variable \"$originalVarName\" is not in valid camel caps format";
            $phpcsFile->addError($error, $stackPtr);
        } else if (preg_match('|\d|', $varName)) {
            //$warning = "Variable \"$originalVarName\" contains numbers but this is discouraged";
            //$phpcsFile->addWarning($warning, $stackPtr);
        }

    }//end processVariable()


    /**
     * Processes class member variables.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    protected function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $varName     = ltrim($tokens[$stackPtr]['content'], '$');
        $memberProps = $phpcsFile->getMemberProperties($stackPtr);
        $public      = ($memberProps['scope'] === 'public');

        // ox:  third party, depredated and config variables
        if ( in_array( $varName, $this->_aThirdPartyVars ) === true ||
             in_array( $varName, $this->_aConfigVars  )    === true ) {
            return;
        }
        if ( in_array( $varName, $this->_aDeprecatedVars ) === true ) {
            $warning = "Variable \"$originalVarName\" is depracated";
                $phpcsFile->addWarning($warning, $stackPtr);
            return;
        }

        if ($public === true) {
            if (substr($varName, 0, 1) === '_') {
                $error = "Public member variable \"$varName\" must not contain a leading underscore";
                    $phpcsFile->addError($error, $stackPtr);
                return;
            }
        } else {
            if (substr($varName, 0, 1) !== '_') {
                $scope = ucfirst($memberProps['scope']);
                $error = "$scope member variable \"$varName\" must contain a leading underscore";
                    $phpcsFile->addError($error, $stackPtr);
                return;
            }
        }

        if (PHP_CodeSniffer::isCamelCaps($varName, false, $public, false) === false) {
            $error = "Variable \"$varName\" is not in valid camel caps format";
            $phpcsFile->addError($error, $stackPtr);
        } else if (preg_match('|\d|', $varName)) {
            //$warning = "Variable \"$varName\" contains numbers but this is discouraged";
            //$phpcsFile->addWarning($warning, $stackPtr);
        }

    }//end processMemberVar()


    /**
     * Processes the variable found within a double quoted string.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the double quoted
     *                                        string.
     *
     * @return void
     */
    protected function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (preg_match_all('|[^\\\]\$([a-zA-Z0-9_]+)|', $tokens[$stackPtr]['content'], $matches) !== 0) {
            foreach ($matches[1] as $varName) {
                // ox: Ignoring reserved php, third party, depredated and config variables
                if (
                     in_array( $varName, $this->_phpReservedVars ) === true ||
                     in_array( $varName, $this->_aThirdPartyVars ) === true ||
                     in_array( $varName, $this->_aDeprecatedVars ) === true ||
                     in_array( $varName, $this->_aConfigVars  )    === true ||
                     preg_match($this->_sObjectFieldPattern, $varName) ) {
                    continue;
                }

                // There is no way for us to know if the var is public or private,
                // so we have to ignore a leading underscore if there is one and just
                // check the main part of the variable name.
                $originalVarName = $varName;
                if (substr($varName, 0, 1) === '_') {
                    if ($phpcsFile->hasCondition($stackPtr, array(T_CLASS, T_INTERFACE)) === true) {
                        $varName = substr($varName, 1);
                    }
                }

                if (PHP_CodeSniffer::isCamelCaps($varName, false, true, false) === false) {
                    $varName = $matches[0];
                    $error   = "Variable \"$originalVarName\" is not in valid camel caps format";
                    $phpcsFile->addError($error, $stackPtr);
                } else if (preg_match('|\d|', $varName)) {
                    //$warning = "Variable \"$originalVarName\" contains numbers but this is discouraged";
                    //$phpcsFile->addWarning($warning, $stackPtr);
                }
            }
        }//end if

    }//end processVariableInString()


}//end class

?>

