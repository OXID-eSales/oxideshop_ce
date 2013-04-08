<?php
/**
 * Parses and verifies the doc comments for files.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: FileCommentSniff.php 287726 2009-08-26 05:13:12Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found');
}

/**
 * Parses and verifies the doc comments for files.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
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

class Oxid_Sniffs_Commenting_FileCommentSniff extends PEAR_Sniffs_Commenting_FileCommentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Tags in correct order and related info.
     *
     * @var array
     */
    protected $tags = array(
                       'link'       => array(
                                        'required'       => true,
                                        'allow_multiple' => true,
                                        'order_text'     => 'precedes @package',
                                       ),

                       'package'    => array(
                                        'required'       => true,
                                        'allow_multiple' => false,
                                        'order_text'     => 'follows @link',
                                       ),
                       'copyright'  => array(
                                        'required'       => true,
                                        'allow_multiple' => true,
                                        'order_text'     => 'follows @package',
                                       ),
                       'version'    => array(
                                        'required'       => true,
                                        'allow_multiple' => false,
                                        'order_text'     => 'follows @copyright',
                                       ),
                );

    /**
     * Valid package names.
     *
     * @var array
     */
     protected $packages = array('main', 'admin', 'core', 'views', 'lang', 'utils', 'modules', 'tests', 'smarty_plugins', 'setup');

    /**
     * Default copyright message.
     *
     * @var string
     */
     protected $copyright = "(c) OXID eSales AG 2003-#OXID_VERSION_YEAR#";

    /**
     * Default version tag comment fragment.
     *
     * @var string
     */
     protected $version = 'OXID_VERSION_';

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
        $this->currentFile = $phpcsFile;

        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1)) !== false) {
                return;
            }
        }

        $tokens = $phpcsFile->getTokens();

        // Find the next non whitespace token.
        $commentStart
            = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        // Allow if statements at the top of the file, for version tagging.
        if ($tokens[$commentStart]['code'] === T_IF) {
            $colon = $phpcsFile->findNext(T_COLON, ($commentStart + 1));
            $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($colon + 1), null, true);
        }

        // Ignore version tag comments at the top of the file header.
        if ($tokens[$commentStart]['code'] === T_COMMENT) {
            if (strstr($tokens[$commentStart]['content'], $this->version) !== false) {
                $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($commentStart + 1), null, true);
            }
        }

        $errorToken = ($commentStart);
        if (isset($tokens[$errorToken]) === false) {
            $errorToken--;
        }

        if ($tokens[$commentStart]['code'] === T_CLOSE_TAG) {
            // We are only interested if this is the first open tag.
            return;
        } else if ($tokens[$commentStart]['code'] === T_COMMENT) {
            $error = 'You must use "/**" style comments for a file comment';
            $phpcsFile->addError($error, $errorToken);
            return;
        } else if ($commentStart === false
            || $tokens[$commentStart]['code'] !== T_DOC_COMMENT
        ) {
            $phpcsFile->addError('Missing file doc comment', $errorToken);
            return;
        } else {

            // Extract the header comment docblock.
            $commentEnd = $phpcsFile->findNext(
                T_DOC_COMMENT,
                ($commentStart + 1),
                null,
                true
            );

            $commentEnd--;

            // Check if there is only 1 doc comment between the
            // open tag and class token.
            $nextToken   = array(
                            T_ABSTRACT,
                            T_CLASS,
                            T_FUNCTION,
                            T_DOC_COMMENT,
                           );

            $commentNext = $phpcsFile->findNext($nextToken, ($commentEnd + 1));
            if ($commentNext !== false
                && $tokens[$commentNext]['code'] !== T_DOC_COMMENT
            ) {
                // Found a class token right after comment doc block.
                $newlineToken = $phpcsFile->findNext(
                    T_WHITESPACE,
                    ($commentEnd + 1),
                    $commentNext,
                    false,
                    $phpcsFile->eolChar
                );

                if ($newlineToken !== false) {
                    $newlineToken = $phpcsFile->findNext(
                        T_WHITESPACE,
                        ($newlineToken + 1),
                        $commentNext,
                        false,
                        $phpcsFile->eolChar
                    );

                    if ($newlineToken === false) {
                        // No blank line between the class token and the doc block.
                        // The doc block is most likely a class comment.
                        $error = 'Missing file doc comment';
                        $phpcsFile->addError($error, $errorToken);
                        return;
                    }
                }
            }//end if

            $comment = $phpcsFile->getTokensAsString(
                $commentStart,
                ($commentEnd - $commentStart + 1)
            );

            // Parse the header comment docblock.
            try {
                $this->commentParser = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment, $phpcsFile);
                $this->commentParser->parse();
            } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
                $line = ($e->getLineWithinComment() + $commentStart);
                $phpcsFile->addError($e->getMessage(), $line);
                return;
            }

            $comment = $this->commentParser->getComment();
            if (is_null($comment) === true) {
                $error = 'File doc comment is empty';
                $phpcsFile->addError($error, $commentStart);
                return;
            }

            // No extra newline before short description.
            $short        = $comment->getShortComment();
            $newlineCount = 0;
            $newlineSpan  = strspn($short, $phpcsFile->eolChar);
            if ($short !== '' && $newlineSpan > 0) {
                $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
                $error = "Extra $line found before file comment short description";
                $phpcsFile->addError($error, ($commentStart + 1));
            }

            $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

            // Exactly one blank line between short and long description.
            $long = $comment->getLongComment();
            if (empty($long) === false) {
                $between        = $comment->getWhiteSpaceBetween();
                $newlineBetween = substr_count($between, $phpcsFile->eolChar);
                if ($newlineBetween !== 2) {
                    $error = 'There must be exactly one blank line between descriptions in file comment';
                    $phpcsFile->addError($error, ($commentStart + $newlineCount + 1));
                }

                $newlineCount += $newlineBetween;
            }

            // Exactly one blank line before tags.
            $tags = $this->commentParser->getTagOrders();
            if (count($tags) > 1) {
                $newlineSpan = $comment->getNewlineAfter();
                if ($newlineSpan !== 2) {
                    $error = 'There must be exactly one blank line before the tags in file comment';
                    if ($long !== '') {
                        $newlineCount += (substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1);
                    }

                    $phpcsFile->addError($error, ($commentStart + $newlineCount));
                    $short = rtrim($short, $phpcsFile->eolChar.' ');
                }
            }

            // Check each tag.
            $this->processTags($commentStart, $commentEnd);
        }//end if

    }

    /**
     * Process the package tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processPackage($errorPos)
    {
        $package  = $this->commentParser->getPackage();
        if ($package !== null) {
            $content = $package->getContent();
            if ($content !== '') {
                if (!in_array($content, $this->packages)) {
                    $error  = "Package name \"$content\" is not valid; consider using one of \"".implode('","', $this->packages)."\" instead";
                    $this->currentFile->addError($error, $errorPos);
                }
            } else {
                $error = '@package tag must contain a name';
                $this->currentFile->addError($error, $errorPos);
            }
        }
    }

    /**
     * Process the copyright tags.
     *
     * @param int $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processCopyrights($commentStart)
    {
        $copyrights = $this->commentParser->getCopyrights();
        foreach ($copyrights as $copyright) {
            $errorPos = ($commentStart + $copyright->getLine());
            $content  = $copyright->getContent();
            if ($content !== '') {
                if ($content !== $this->copyright) {
                    $error = '@copyright tag must contain "'.$this->copyright.'"';
                    $this->currentFile->addError($error, $errorPos);
                }
            } else {
                $error = '@copyright tag must contain a year and the name of the copyright holder';
                $this->currentFile->addError($error, $errorPos);
            }
        }
    }
}
