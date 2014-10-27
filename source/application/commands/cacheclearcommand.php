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
 * Cache Clear command
 *
 * Clears out OXID cache from tmp folder
 */
class CacheClearCommand extends oxConsoleCommand
{

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('cache:clear');
        $this->setDescription('Clear OXID cache from tmp folder');
    }

    /**
     * {@inheritdoc}
     */
    public function help(oxIOutput $oOutput)
    {
        $oOutput->writeLn('Usage: cache:clear [options]');
        $oOutput->writeLn();
        $oOutput->writeLn('This command clears out contents of OXID tmp folder');
        $oOutput->writeLn('It applies following rules:');
        $oOutput->writeLn(' * Does not delete .htaccess');
        $oOutput->writeLn(' * Does not delete smarty directory but its contents by default');
        $oOutput->writeln();
        $oOutput->writeLn('Available options:');
        $oOutput->writeLn('  -s, --smarty     Clears out only smarty cache');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(oxIOutput $oOutput)
    {
        $oInput = $this->getInput();
        $sTmpDir = $this->_appendDirectorySeparator(oxRegistry::getConfig()->getConfigParam('sCompileDir'));
        if (!is_dir($sTmpDir)) {
            $oOutput->writeLn('Seems that compile directory does not exist');
        }

        $oOutput->writeLn('Clearing OXID cache...');

        $this->_clearDirectory($sTmpDir . 'smarty');
        if (!$oInput->hasOption(array('s', 'smarty'))) {
            // If there are no options for clearing smarty cache only
            $this->_clearDirectory($sTmpDir, array('.htaccess', 'smarty'));
        }

        $oOutput->writeLn('Cache cleared successfully');
    }

    /**
     * Clear files in given directory, except those which
     * are in $aKeep array
     *
     * @param string $sDir
     * @param array $aKeep
     */
    protected function _clearDirectory($sDir, $aKeep = array())
    {
        $sDir = $this->_appendDirectorySeparator($sDir);

        foreach (glob($sDir . '*') as $sFilePath) {
            $sFileName = basename($sFilePath);
            if (in_array($sFileName, $aKeep)) {
                continue;
            }

            is_dir($sFilePath)
                ? $this->_removeDirectory($sFilePath)
                : unlink($sFilePath);
        }
    }

    /**
     * Remove directory
     *
     * @param string $sPath
     */
    protected function _removeDirectory($sPath)
    {
        if (!is_dir($sPath)) {
            return;
        }

        $oIterator = new RecursiveDirectoryIterator($sPath, RecursiveDirectoryIterator::SKIP_DOTS);
        $oFiles = new RecursiveIteratorIterator($oIterator, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($oFiles as $oFile) {
            if ($oFile->getFilename() == '.' || $oFile->getFilename() === '..') {
                continue;
            }

            $oFile->isDir()
                ? rmdir($oFile->getRealPath())
                : unlink($oFile->getRealPath());
        }

        rmdir($sPath);
    }

    /**
     * Append directory separator to path
     *
     * @param string $sPath
     *
     * @return string
     */
    protected function _appendDirectorySeparator($sPath)
    {
        if (substr($sPath, -1) != DIRECTORY_SEPARATOR) {
            return $sPath . DIRECTORY_SEPARATOR;
        }

        return $sPath;
    }
}
