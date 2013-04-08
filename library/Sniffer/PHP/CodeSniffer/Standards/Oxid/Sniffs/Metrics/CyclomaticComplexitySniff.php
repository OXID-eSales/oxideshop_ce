<?php
/**
 * Checks the cyclomatic complexity (McCabe) for functions.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: CyclomaticComplexitySniff.php 240469 2007-07-30 04:55:35Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('Generic_Sniffs_Metrics_CyclomaticComplexitySniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_Metrics_CyclomaticComplexitySniff not found');
}

/**
 * Checks the cyclomatic complexity (McCabe) for functions.
 *
 * The cyclomatic complexity (also called McCabe code metrics)
 * indicates the complexity within a function by counting
 * the different paths the function includes.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Johann-Peter Hartmann <hartmann@mayflower.de>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2007 Mayflower GmbH
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.2
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Oxid_Sniffs_Metrics_CyclomaticComplexitySniff extends Generic_Sniffs_Metrics_CyclomaticComplexitySniff
{
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * OXID Ignores switch cases.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->currentFile = $phpcsFile;

        $tokens = $phpcsFile->getTokens();

        // Ignore abstract methods.
        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        // Detect start and end of this function definition.
        $start = $tokens[$stackPtr]['scope_opener'];
        $end   = $tokens[$stackPtr]['scope_closer'];

        // Predicate nodes for PHP.
        $find = array(
                 //'T_CASE', // <- OXID CC ignores switch cases
                 'T_DEFAULT',
                 'T_CATCH',
                 'T_IF',
                 'T_FOR',
                 'T_FOREACH',
                 'T_WHILE',
                 'T_DO',
                 'T_ELSEIF',
                );

        $complexity = 1;

        // Iterate from start to end and count predicate nodes.
        for ($i = ($start + 1); $i < $end; $i++) {
            if (in_array($tokens[$i]['type'], $find) === true) {
                $complexity++;
            }
        }

        if ($complexity > $this->absoluteComplexity) {
            $error = "Function's cyclomatic complexity ($complexity) exceeds allowed maximum of ".$this->absoluteComplexity;
            $phpcsFile->addError($error, $stackPtr);
        } else if ($complexity > $this->complexity) {
            $warning = "Function's cyclomatic complexity ($complexity) exceeds ".$this->complexity.'; consider refactoring the function';
            $phpcsFile->addWarning($warning, $stackPtr);
        }

        return;

    }//end process()


}//end class

?>
