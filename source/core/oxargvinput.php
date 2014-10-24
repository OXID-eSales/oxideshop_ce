<?php
/**
 * This file is part of OXID Console.
 *
 * OXID Console is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID Console is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID Console.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    OXID Professional services
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

/**
 * Argv Input
 */
class oxArgvInput implements oxIConsoleInput
{

    /**
     * @var array
     */
    protected $_aOptions = array();

    /**
     * @var string[]
     */
    protected $_aArguments = array();

    /**
     * @var oxConsoleOutput
     */
    protected $_oConsoleOutput;

    /**
     * Constructor
     *
     * @param array $aArgv
     */
    public function __construct(array $aArgv = null)
    {
        if (null === $aArgv) {
            $aArgv = $_SERVER['argv'];
        }

        // stripping application name
        array_shift($aArgv);

        $this->_parseTokens($aArgv);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstArgument()
    {
        return $this->getArgument(0);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->_aOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return $this->_aArguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($mOption)
    {
        if (!is_array($mOption)) {
            $mOption = array($mOption);
        }

        foreach ($mOption as $sOptionName) {
            if (isset($this->_aOptions[$sOptionName])) {
                return $this->_aOptions[$sOptionName];
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption($mOption)
    {
        if (!is_array($mOption)) {
            $mOption = array($mOption);
        }

        foreach ($mOption as $sOptionName) {
            if (array_key_exists($sOptionName, $this->_aOptions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getArgument($iOffset)
    {
        if (isset($this->_aArguments[$iOffset])) {
            return $this->_aArguments[$iOffset];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function prompt($sTitle = null)
    {
        $oOutput = $this->_getConsoleOutput();

        if (null !== $sTitle) {
            $oOutput->write($sTitle . ': ');
        }

        return trim(fgets($oOutput->getStream()));
    }

    /**
     * Parse tokens to options and arguments
     *
     * @param string[] $aTokens
     */
    protected function _parseTokens(array $aTokens)
    {
        foreach ($aTokens as $sToken) {

            if ('--' === substr($sToken, 0, 2)) {
                $this->_parseLongOption($sToken);
            } else if ($sToken && '-' == $sToken[0]) {
                $this->_parseShortOption($sToken);
            } else {
                $this->_parseArgument($sToken);
            }
        }
    }

    /**
     * Parse long option from a token
     *
     * @param $sToken
     */
    protected function _parseLongOption($sToken)
    {
        $sOptionLine = substr($sToken, 2);
        if (!$sOptionLine) {
            return;
        }

        $aOption = explode('=', $sOptionLine, 2);
        if (!isset($aOption[1])) {
            $aOption[1] = true;
        }

        $this->_aOptions[$aOption[0]] = $aOption[1];
    }

    /**
     * Parse short option from a token
     *
     * @param $sToken
     */
    protected function _parseShortOption($sToken)
    {
        $sOptionLine = substr($sToken, 1);
        if (!$sOptionLine) {
            return;
        }

        foreach (str_split($sOptionLine) as $sOption) {
            $this->_aOptions[$sOption] = true;
        }
    }

    /**
     * Parse argument from a token
     *
     * @param $sToken
     */
    protected function _parseArgument($sToken)
    {
        if ($sToken) {
            $this->_aArguments[] = $sToken;
        }
    }

    /**
     * Get console output
     *
     * @return oxConsoleOutput
     */
    protected function _getConsoleOutput()
    {
        if (null === $this->_oConsoleOutput) {
            $this->_oConsoleOutput = oxNew('oxConsoleOutput');
        }

        return $this->_oConsoleOutput;
    }
}