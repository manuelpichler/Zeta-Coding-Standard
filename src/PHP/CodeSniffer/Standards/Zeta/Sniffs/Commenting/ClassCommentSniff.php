<?php
/**
 * Parses and verifies the doc comments for classes.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer_Standards_Zeta
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception(
        'Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found'
    );
}

/**
 * Parses and verifies the doc comments for classes.
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
 * @package   PHP_CodeSniffer_Standards_Zeta
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   Release: 0.1.8
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Zeta_Sniffs_Commenting_ClassCommentSniff extends Zeta_Sniffs_Commenting_FileCommentSniff
{
    /**
     * List of allowed class comment annotations.
     *
     * @type array<array>
     * @var array(string=>array) $tags
     */
    protected $tags = array(
        'package' => array(
            'required'       => true,
            'allow_multiple' => false
        ),
        'version' => array(
            'required'       => true,
            'allow_multiple' => false
        ),
        'mainclass' => array(
            'required'       => false,
            'allow_multiple' => false
        ),
        'uses' => array(
            'required'       => false,
            'allow_multiple' => true
        ),
        'see' => array(
            'required'       => false,
            'allow_multiple' => true
        ),
        'tutorial' => array(
            'required'       => false,
            'allow_multiple' => false
        ),
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLASS);
    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->currentFile = $phpcsFile;

        $tokens = $phpcsFile->getTokens();
        $find   = array(T_ABSTRACT, T_WHITESPACE, T_FINAL);

        // Extract the class comment docblock.
        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);

        if ($commentEnd !== false && $tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a class comment', $stackPtr);
            return;
        } else if ($commentEnd === false 
                || $tokens[$commentEnd]['code'] !== T_DOC_COMMENT) {
                    
            $phpcsFile->addError('Missing class doc comment', $stackPtr);
            return;
        }

        $commentStart = ($phpcsFile->findPrevious(T_DOC_COMMENT, ($commentEnd - 1), null, true) + 1);
        $commentNext  = $phpcsFile->findPrevious(T_WHITESPACE, ($commentEnd + 1), $stackPtr, false, $phpcsFile->eolChar);

        // Distinguish file and class comment.
        $prevClassToken = $phpcsFile->findPrevious(T_CLASS, ($stackPtr - 1));
        if ($prevClassToken === false) {
            // This is the first class token in this file, need extra checks.
            $prevNonComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($commentStart - 1), null, true);
            if ($prevNonComment !== false) {
                $prevComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($prevNonComment - 1));
                if ($prevComment === false) {
                    // There is only 1 doc comment between open tag and class token.
                    $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($commentEnd + 1), $stackPtr, false, $phpcsFile->eolChar);
                    if ($newlineToken !== false) {
                        $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($newlineToken + 1), $stackPtr, false, $phpcsFile->eolChar);
                        if ($newlineToken !== false) {
                            // Blank line between the class and the doc block.
                            // The doc block is most likely a file comment.
                            $phpcsFile->addError('Missing class doc comment', ($stackPtr + 1));
                            return;
                        }
                    }//end if
                }//end if
            }//end if
        }//end if

        $comment = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));


        // Parse the class comment.docblock.
        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser2_ClassCommentParser($comment, $phpcsFile);
            
            $this->commentParser->registerTag('mainclass', array(
                'single' => true,
                'class'  => 'PHP_CodeSniffer_CommentParser2_LeafElement',
                'name'   => 'mainclass',
            ));
            $this->commentParser->registerTag('tutorial', array(
                'single' => true,
                'class'  => 'PHP_CodeSniffer_CommentParser_SingleElement',
                'name'   => 'tutorial',
            ));
            $this->commentParser->registerTag('uses', array(
                'single' => false,
                'class'  => 'PHP_CodeSniffer_CommentParser_SingleElement',
                'name'   => 'usess'
            ));
            $this->commentParser->registerTag('property', array(
                'single' => false,
                'class'  => 'PHP_CodeSniffer_CommentParser2_PropertyElement',
                'name'   => 'properties'
            ));
            $this->commentParser->registerTag('property-read', array(
                'single' => false,
                'class'  => 'PHP_CodeSniffer_CommentParser2_PropertyElement',
                'name'   => 'properties'
            ));
            $this->commentParser->registerTag('property-write', array(
                'single' => false,
                'class'  => 'PHP_CodeSniffer_CommentParser2_PropertyElement',
                'name'   => 'properties'
            ));
            
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getLineWithinComment() + $commentStart);
            $phpcsFile->addError($e->getMessage(), $line);
            return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
            $error = 'Class doc comment is empty';
            $phpcsFile->addError($error, $commentStart);
            return;
        }

        // No extra newline before short description.
        $short        = $comment->getShortComment();
        $newlineCount = 0;
        $newlineSpan  = strspn($short, $phpcsFile->eolChar);
        if ($short !== '' && $newlineSpan > 0) {
            $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
            $error = "Extra $line found before class comment short description";
            $phpcsFile->addError($error, ($commentStart + 1));
        }

        $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

        // Exactly one blank line between short and long description.
        $long = $comment->getLongComment();
        if (empty($long) === false) {
            $between        = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, $phpcsFile->eolChar);
            if ($newlineBetween !== 2) {
                $error = 'There must be exactly one blank line between descriptions in class comment';
                $phpcsFile->addError($error, ($commentStart + $newlineCount + 1));
            }

            $newlineCount += $newlineBetween;
        }

        // Exactly one blank line before tags.
        $tags = $this->commentParser->getTagOrders();
        if (count($tags) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = 'There must be exactly one blank line before the tags in class comment';
                if ($long !== '') {
                    $newlineCount += (substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1);
                }

                $phpcsFile->addError($error, ($commentStart + $newlineCount));
                $short = rtrim($short, $phpcsFile->eolChar.' ');
            }
        }

        $this->processProperties($commentStart);
        
        // Check for unknown/deprecated tags.
        $unknownTags = $this->commentParser->getUnknown();
        foreach ($unknownTags as $errorTag) {
            $error = "@$errorTag[tag] tag is not allowed in class comment";
            $phpcsFile->addWarning($error, ($commentStart + $errorTag['line']));
        }

        // Check each tag.
        $this->processTags($commentStart, $commentEnd);

    }//end process()

    /**
     * Process virtual class properties.
     *
     * @param int $commentStart The position in the stack where
     *                          the comment started.
     *
     * @return void
     */
    protected function processProperties( $commentStart )
    {
        $params = $this->commentParser->getProperties();

        $foundParams = array();
        
        // The used comment indent style. This style must be consistent
        // in the same class comment.
        $indentStyle  = null;
        // Marks the first position with a mix of two different indent styles
        $indentMixPos = -1;
        
        if (empty($params) === false) {

            if (substr_count($params[0]->getWhitespaceBefore(), $this->currentFile->eolChar) !== 2) {
                $error    = 'First property tag requires a blank newline before it';
                $errorPos = ($params[0]->getLine() + $commentStart);
                $this->currentFile->addError($error, $errorPos);
            }

            $previousParam      = null;
            $spaceBeforeVar     = 10000;
            $spaceBeforeComment = 10000;
            $longestType        = 0;
            $longestVar         = 0;

            foreach ($params as $param) {
                
                $paramComment = trim($param->getComment());
                $errorPos     = ($param->getLine() + $commentStart);

                $spaceCount = substr_count($param->getWhitespaceBeforeVarName(), ' ');
                if ($spaceCount < $spaceBeforeVar) {
                    $spaceBeforeVar = $spaceCount;
                    $longestType    = $errorPos;
                }
                
                $newlined   = false;
                $whitespace = $param->getWhitespaceBeforeComment();
                
                if (preg_match('/(\n\r|\n|\r)([ ]*)/', $whitespace, $match)) {
                    $newlined   = true;
                    $whitespace = $match[2];
                }
                
                if ($indentStyle === null) {
                    $indentStyle = $newlined;
                } else if ($indentMixPos === -1 && $indentStyle !== $newlined) {
                    $indentMixPos = $errorPos;
                }

                $spaceCount = substr_count($whitespace, ' ');

                if ($spaceCount < $spaceBeforeComment && $paramComment !== '') {
                    $spaceBeforeComment = $spaceCount;
                    $longestVar         = $errorPos;
                }

                // Make sure they are in the correct order,
                // and have the correct name.
                $pos = $param->getPosition();

                $paramName = ($param->getVarName() !== '') ? $param->getVarName() : '[ UNKNOWN ]';

                if ($newlined === false && $previousParam !== null) {
                    $previousName = ($previousParam->getVarName() !== '') ? $previousParam->getVarName() : 'UNKNOWN';
                    
                    $len1 = strlen($param->getTag() . $param->getWhitespaceBeforeType());
                    $len2 = strlen($previousParam->getTag() . $previousParam->getWhitespaceBeforeType());
                    
                    // Check that all type alignments
                    if ($len1 !== $len2) {
                        $type1 = $param->getType();
                        $type2 = $previousParam->getType();
                        
                        $error = 'Property types '.$type1.' ('.$len1.') and '.$type2.' ('.$len2.') do not align';
                        $this->currentFile->addError($error, $errorPos);                  
                    }

                    // Check to see if the parameters align properly.
                    if ($param->alignsWith($previousParam) === false) {
                        $error = 'Properties '.$previousName.' ('.($pos - 1).') and '.$paramName.' ('.$pos.') do not align';
                        $this->currentFile->addError($error, $errorPos);
                    }
                }

                if ($param->getVarName() === '') {
                    $error = 'Missing parameter name at position '.$pos;
                     $this->currentFile->addError($error, $errorPos);
                }

                if ($param->getType() === '') {
                    $error = 'Missing type at position '.$pos;
                    $this->currentFile->addError($error, $errorPos);
                }

                if ($paramComment === '') {
                    $error = 'Missing comment for param "'.$paramName.'" at position '.$pos;
                    $this->currentFile->addError($error, $errorPos);
                }
                
                if ($newlined) {
                    // The first comment token must align with the parameter
                    // token: strlen(' ' + '@' + tag + ' ') === whitespaces
                    if (strlen($whitespace) != (strlen($param->getTag()) + 3)) {
                        $error = 'The property comment must align with the property type';
                        $this->currentFile->addError($error, $errorPos);
                    }
                    
                    // Compare line indention
                    $lines = preg_split('(\n\r|\r|\n)', $paramComment);
                    for ($i = 1, $c = count($lines); $i < $c; $i++) {
                        // Ignore empty lines
                        if (trim($lines[$i]) === '') {
                            continue;
                        } else if (strpos($lines[$i], $whitespace) !== 0) {
                            $error = 'Property comment lines do not align.';
                            $this->currentFile->addError($error, $errorPos + $i);
                        }
                    }
                }

                $previousParam = $param;

            }//end foreach
            
            if ($indentMixPos > -1) {
                $error = 'Mixed style of property indention';
                $this->currentFile->addError($error, $indentMixPos);
            }

            if ($spaceBeforeVar !== 1 && $spaceBeforeVar !== 10000 && $spaceBeforeComment !== 10000) {
                $error = 'Expected 1 space after the longest type';
                $this->currentFile->addError($error, $longestType);
            }

            if ($newlined === false) {
                if ($spaceBeforeComment !== 1 && $spaceBeforeComment !== 10000) {
                    $error = 'Expected 1 space after the longest variable name';
                    $this->currentFile->addError($error, $longestVar);
                }
            }
        }//end if
    }//end processParams()

}//end class
