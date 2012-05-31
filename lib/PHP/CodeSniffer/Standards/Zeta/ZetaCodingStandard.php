<?php
/**
 * PHP_CodeSniffer_Standards_EZC_EZCCodingStandard.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer_Standards_EZC
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception(
        'Class PHP_CodeSniffer_Standards_CodingStandard not found'
    );
}

/**
 * PHP_CodeSniffer_Standards_EZC_EZCCodingStandard.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer_Standards_EZC
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   Release: 0.1.8
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Standards_Zeta_ZetaCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{
    /**
     * Return a list of external sniffs to include with this standard.
     *
     * The EZCCodingStandard standard is an extension of the PEAR standard.
     *
     * @return array
     */
    public function getIncludedSniffs()
    {
        return array(
            'PEAR',
        );
    }//end getIncludedSniffs()


    /**
     * Return a list of external sniffs to exclude from this standard.
     *
     * @return array
     */
    public function getExcludedSniffs()
    {
        return array(
            'Generic/Sniffs/Formatting/MultipleStatementAlignmentSniff.php',
            'PEAR/Sniffs/Files/LineLengthSniff.php',
            'PEAR/Sniffs/NamingConventions/ValidClassNameSniff.php',
            'PEAR/Sniffs/NamingConventions/ValidFunctionNameSniff.php',
            'PEAR/Sniffs/NamingConventions/ValidVariableNameSniff.php',
            'PEAR/Sniffs/ControlStructures/ControlSignatureSniff.php',
            'PEAR/Sniffs/Functions/FunctionCallSignatureSniff.php',
            'PEAR/Sniffs/Commenting/FileCommentSniff.php',
            'PEAR/Sniffs/Commenting/ClassCommentSniff.php',
            'PEAR/Sniffs/Commenting/FunctionCommentSniff.php',
            'PEAR/Sniffs/WhiteSpace/ScopeClosingBraceSniff.php',
            'PEAR/Sniffs/WhiteSpace/ScopeIndentSniff.php',
        );

    }//end getExcludedSniffs()
}//end class
?>
