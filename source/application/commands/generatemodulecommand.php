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
 * Generate module command
 */
class GenerateModuleCommand extends oxConsoleCommand
{

    /**
     * @var string Directory path where modules are stored
     */
    protected $_sModuleDir;

    /**
     * @var string Templates dir
     */
    protected $_sTemplatesDir;

    /**
     * @var Smarty
     */
    protected $_oSmarty;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('g:module');
        $this->setDescription('Generate new module scaffold');

        $this->_oSmarty = oxRegistry::get('oxUtilsView')->getSmarty();
        $this->_sModuleDir = OX_BASE_PATH . 'modules' . DIRECTORY_SEPARATOR;
        $this->_sTemplatesDir = __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR
            . 'module' . DIRECTORY_SEPARATOR;
    }

    /**
     * {@inheritdoc}
     */
    public function help(oxIOutput $oOutput)
    {
        $oOutput->writeLn('Usage: g:module');
        $oOutput->writeLn();
        $oOutput->writeLn('This command is used for generating module scaffold');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(oxIOutput $oOutput)
    {
        $oScaffold = $this->_buildScaffold($oOutput);
        $this->_generateModule($oScaffold);

        $oOutput->writeLn('Module generated successfully');
    }

    /**
     * Generate module from scaffold object
     *
     * @param object $oScaffold
     */
    protected function _generateModule($oScaffold)
    {
        $oSmarty = $this->_getSmarty();
        $oSmarty->assign('oScaffold', $oScaffold);

        if ($oScaffold->sVendor) {
            $this->_generateVendorDir($oScaffold->sVendor);
        }

        $sModuleDir = $this->_getModuleDir($oScaffold->sVendor, $oScaffold->sModuleName);
        $this->_copyAndParseDir(
            $this->_sTemplatesDir, $sModuleDir, array(
                '_prefix_' => strtolower($oScaffold->sVendor . $oScaffold->sModuleName)
            )
        );
    }

    /**
     * Copies files from directory, parses all files and puts
     * parsed content to another directory
     *
     * @param string $sFrom Directory from
     * @param string $sTo Directory to
     * @param array $aNameMap What should be changed in file name?
     */
    protected function _copyAndParseDir($sFrom, $sTo, array $aNameMap = array())
    {
        $oFileInfos = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sFrom, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        if (!file_exists($sTo)) {
            mkdir($sTo);
        }

        foreach ($oFileInfos as $oFileInfo) {
            $sFilePath = (string)$oFileInfo;
            $aReplace = array(
                'search' => array_merge(array($sFrom), array_keys($aNameMap)),
                'replace' => array_merge(array($sTo), array_values($aNameMap))
            );
            $sNewPath = str_replace($aReplace['search'], $aReplace['replace'], $sFilePath);
            $this->_copyAndParseFile($sFilePath, $sNewPath);
        }
    }

    /**
     * Copies file from one directory to another, parses file if original
     * file extension is .tpl
     *
     * @param $sFrom
     * @param $sTo
     */
    protected function _copyAndParseFile($sFrom, $sTo)
    {
        $this->_createMissingFolders($sTo);

        $sTo = preg_replace('/\.tpl$/', '', $sTo);
        if (preg_match('/\.tpl$/', $sFrom)) {
            $oSmarty = $this->_getSmarty();
            $sContent = $oSmarty->fetch($sFrom);
        } else {
            $sContent = file_get_contents($sFrom);
        }

        file_put_contents($sTo, $sContent);
    }

    /**
     * Create missing folders of file path
     *
     * @param string $sFilePath
     */
    protected function _createMissingFolders($sFilePath)
    {
        $sPath = dirname($sFilePath);

        if (!file_exists($sPath)) {
            mkdir($sPath, 0777, true);
        }
    }

    /**
     * Generate vendor directory
     *
     * @param string $sVendor
     */
    protected function _generateVendorDir($sVendor)
    {
        $sVendorDir = $this->_sModuleDir . $sVendor . DIRECTORY_SEPARATOR;
        if (!file_exists($sVendorDir)) {
            mkdir($sVendorDir);

            // Generate vendor metadata file
            file_put_contents($sVendorDir . 'vendormetadata.php', '<?php');
        }
    }

    /**
     * Build scaffold object from user inputs
     *
     * @param oxIOutput $oOutput
     *
     * @return stdClass
     */
    protected function _buildScaffold(oxIOutput $oOutput)
    {
        $oScaffold = new stdClass();
        $oScaffold->sVendor = strtolower($this->_getUserInput('Vendor', true));

        $blFirstRequest = true;

        do {

            if (!$blFirstRequest) {
                $oOutput->writeLn('Module path or id is taken with given title');
            } else {
                $blFirstRequest = false;
            }

            $oScaffold->sModuleTitle = $this->_getUserInput('Title');
            $oScaffold->sModuleName = str_replace(' ', '', ucwords($oScaffold->sModuleTitle));
            $oScaffold->sModuleId = $oScaffold->sVendor . strtolower($oScaffold->sModuleName);

        } while (!$this->_modulePathAvailable($oScaffold->sVendor, $oScaffold->sModuleName)
            || !$this->_moduleIdAvailable($oScaffold->sModuleId));

        $oScaffold->sModuleDir = $this->_getModuleDir($oScaffold->sVendor, $oScaffold->sModuleName);
        $oScaffold->sAuthor = $this->_getUserInput('Author', true);
        $oScaffold->sUrl = $this->_getUserInput('Url', true);
        $oScaffold->sEmail = $this->_getUserInput('Email', true);

        return $oScaffold;
    }

    /**
     * Get module dir
     *
     * @param string $sVendor
     * @param string $sModuleName
     *
     * @return string
     */
    protected function _getModuleDir($sVendor, $sModuleName)
    {
        $sModuleDir = $this->_sModuleDir;
        if ($sVendor) {
            $sModuleDir .= strtolower($sVendor) . DIRECTORY_SEPARATOR;
        }

        return $sModuleDir . strtolower($sModuleName) . DIRECTORY_SEPARATOR;
    }

    /**
     * Module path available?
     *
     * @param string $sVendor
     * @param string $sModuleName
     *
     * @return bool
     */
    protected function _modulePathAvailable($sVendor, $sModuleName)
    {
        return !is_dir($this->_getModuleDir($sVendor, $sModuleName));
    }

    /**
     * Is module id available?
     *
     * @param string $sModuleId
     *
     * @return bool
     */
    protected function _moduleIdAvailable($sModuleId)
    {
        return !array_key_exists($sModuleId, oxRegistry::getConfig()->getConfigParam('aModulePaths'));
    }

    /**
     * Get user input
     *
     * @param string $sText
     * @param bool $bAllowEmpty
     *
     * @return string
     */
    protected function _getUserInput($sText, $bAllowEmpty = false)
    {
        do {
            $sTitle = $sText . ($bAllowEmpty ? '' : ' *');
            $sInput = $this->getInput()->prompt($sTitle);
        } while (!$bAllowEmpty && !$sInput);

        return $sInput;
    }

    /**
     * Get Smarty
     *
     * @return Smarty
     */
    protected function _getSmarty()
    {
        return $this->_oSmarty;
    }
}