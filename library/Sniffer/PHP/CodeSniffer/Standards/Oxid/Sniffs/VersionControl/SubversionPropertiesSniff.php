<?php
/**
 * Generic_Sniffs_VersionControl_SubversionPropertiesSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Jack Bates <ms419@freezone.co.uk>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: SubversionPropertiesSniff.php 284038 2009-07-14 00:17:39Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_VersionControl_SubversionPropertiesSniff.
 *
 * Tests that the correct Subversion properties are set.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Jack Bates <ms419@freezone.co.uk>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.2
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Oxid_Sniffs_VersionControl_SubversionPropertiesSniff extends Generic_Sniffs_VersionControl_SubversionPropertiesSniff
{

    /**
     * The Subversion properties that should be set.
     *
     * @var array
     */
    protected $properties = array(
                             'svn:keywords'  => 'LastChangedDate LastChangedRevision LastChangedBy HeadURL Id',
                            );

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Make sure this is the first PHP open tag so we don't process the
        // same file twice.
        $prevOpenTag = $phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1));
        if ($prevOpenTag !== false) {
            return;
        }

        $path       = $phpcsFile->getFileName();
        $properties = $this->properties($path);

        foreach (($properties + $this->properties) as $key => $value) {

            if (isset($properties[$key]) === false
                && isset($this->properties[$key]) === true
            ) {
                $error = 'Missing Subversion property "'.$key.'" = "'.$this->properties[$key].'"';
                $phpcsFile->addError($error, $stackPtr);
                continue;
            }

            if ($properties[$key] !== $this->properties[$key]) {
                $error = 'Subversion property "'.$key.'" = "'.$properties[$key].'" does not match "'.$this->properties[$key].'"';
                $phpcsFile->addError($error, $stackPtr);
            }
        }//end foreach

    }//end process()
}
