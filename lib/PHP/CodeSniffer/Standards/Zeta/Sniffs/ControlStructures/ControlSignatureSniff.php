<?php
/**
 * Zeta_Sniffs_ControlStructures_ControlSignatureSniff.
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

if (class_exists('PEAR_Sniffs_ControlStructures_ControlSignatureSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception(
        'Class PEAR_Sniffs_ControlStructures_ControlSignatureSniff not found'
    );
}

/**
 * Verifies that control statements conform to their coding standards.
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
class Zeta_Sniffs_ControlStructures_ControlSignatureSniff extends PEAR_Sniffs_ControlStructures_ControlSignatureSniff
{
    /**
     * Returns the patterns that this test wishes to verify.
     *
     * @return array(string)
     */
    protected function getPatterns()
    {
        return array(
            'do...EOL{EOL...}EOL...while (...);EOL',
            'while (abc)EOL...{EOL',
            'for (abc)EOL...{EOL',
            'if (abc)...{EOL',
            'foreach (abc)EOL...{EOL',
            '} else if (abc)...{EOL',
            '}EOL...elseif (...)EOL...{EOL',
            '}... else ...{EOL',
            'do...EOL{EOL',
        );
    }//end getPatterns()
}//end class
