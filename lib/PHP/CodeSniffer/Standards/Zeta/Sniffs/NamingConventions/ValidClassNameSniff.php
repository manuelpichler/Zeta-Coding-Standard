<?php
/**
 * Zeta_Sniffs_NamingConventions_ValidClassNameSniff.
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

/**
 * Zeta_Sniffs_NamingConventions_ValidClassNameSniff.
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
class Zeta_Sniffs_NamingConventions_ValidClassNameSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLASS, T_INTERFACE);
    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being processed.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $className = $phpcsFile->findNext(T_STRING, $stackPtr);
        $name      = trim($tokens[$className]['content']);

        // Make sure the first letter is lower case.
        if (preg_match('|^[a-z]|', $name) === 0) {
            $error = ucfirst($tokens[$stackPtr]['content']).' name must begin with a lower case letter';
            $phpcsFile->addError($error, $stackPtr);
        }

        // Check that each new word starts with a captial as well, but don't
        // check the first word, as it is checked above.
        $validName = true;
        $nameBits  = explode('_', $name);
        $firstBit  = array_shift($nameBits);
        foreach ($nameBits as $bit) {
            if ($bit === '' || $bit{0} !== strtoupper($bit{0})) {
                $validName = false;
                break;
            }
        }

        if ($validName !== true) {
            // Strip underscores because they cause the suggested name
            // to be incorrect.
            $nameBits = explode('_', trim($name, '_'));
            $firstBit = array_shift($nameBits);

            $newName = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
            foreach ($nameBits as $bit) {
                $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
            }

            $newName = rtrim($newName, '_');
            $error   = ucfirst($tokens[$stackPtr]['content'])." name is not valid; consider $newName instead";
            $phpcsFile->addError($error, $stackPtr);
        }
    }//end process()
}//end class
