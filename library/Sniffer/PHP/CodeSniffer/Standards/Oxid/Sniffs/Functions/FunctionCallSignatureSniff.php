<?php
/**
 * PEAR_Sniffs_Functions_FunctionCallSignatureSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: FunctionCallSignatureSniff.php 284575 2009-07-22 02:58:19Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * PEAR_Sniffs_Functions_FunctionCallSignatureSniff.
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

class Oxid_Sniffs_Functions_FunctionCallSignatureSniff extends PEAR_Sniffs_Functions_FunctionCallSignatureSniff
{
    /**
     * Processes single-line calls.
     *
     * @param PHP_CodeSniffer_File $phpcsFile   The file being scanned.
     * @param int                  $stackPtr    The position of the current token
     *                                          in the stack passed in $tokens.
     * @param int                  $openBracket The position of the openning bracket
     *                                          in the stack passed in $tokens.
     * @param array                $tokens      The stack of tokens that make up
     *                                          the file.
     *
     * @return void
     */
    public function processSingleLineCall(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $openBracket, $tokens)
    {
        // Ignore this: $value = my_function().
        if (($tokens[($openBracket + 1)]['code'] !== T_CLOSE_PARENTHESIS) && ($tokens[($openBracket + 1)]['code'] !== T_WHITESPACE)) {
            // Checking this: $value = my_function([*]...).
            $error = 'Single space after opening parenthesis of function call is required';
            $phpcsFile->addError($error, $stackPtr);
        }

        // Ignore this: $value = my_function().
        $closer = $tokens[$openBracket]['parenthesis_closer'];
        if (($tokens[($closer - 1)]['code'] !== T_OPEN_PARENTHESIS) && ($tokens[($closer - 1)]['code'] !== T_WHITESPACE)) {
            // Checking this: $value = my_function(...[*]).
            $error = 'Single space before closing parenthesis of function call is required';
            $phpcsFile->addError($error, $stackPtr);
        }
    }//end processSingleLineCall()

}