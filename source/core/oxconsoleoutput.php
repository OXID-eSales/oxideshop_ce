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
 * Console output
 */
class oxConsoleOutput implements oxIOutput
{

    /**
     * @var resource
     */
    protected $_oStream;

    /**
     * Constructor
     *
     * Opens up output stream
     */
    public function __construct()
    {
        $sStream = 'php://stdout';
        if (!$this->_hasStdoutSupport()) {
            $sStream = 'php://output';
        }

        $this->_oStream = fopen($sStream, 'w');
    }

    /**
     * {@inheritdoc}
     */
    public function write($sMessage)
    {
        if (!@fwrite($this->_oStream, $sMessage)) {
            /** @var oxConsoleException $oEx */
            $oEx = oxNew('oxConsoleException');
            $oEx->setMessage('Could not write to output');
            throw $oEx;
        }

        fflush($this->_oStream);
    }

    /**
     * {@inheritdoc}
     */
    public function writeLn($sMessage = '')
    {
        $this->write($sMessage . PHP_EOL);
    }

    /**
     * Get stream
     *
     * @return resource
     */
    public function getStream()
    {
        return $this->_oStream;
    }

    /**
     * Returns true if current environment supports writing console output to
     * STDOUT.
     *
     * IBM iSeries (OS400) exhibits character-encoding issues when writing to
     * STDOUT and doesn't properly convert ASCII to EBCDIC, resulting in garbage
     * output.
     *
     * @author Fabien Potencier <fabien@symfony.com>
     *
     * @return boolean
     */
    protected function _hasStdoutSupport()
    {
        return ('OS400' != php_uname('s'));
    }
}