<?php

class [{$oScaffold->sVendor}][{$oScaffold->sModuleName}]Module extends oxModule
{

    /**
     * Loads data on object construction
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        parent::__construct();
        $this->load('[{$oScaffold->sModuleId}]');
    }

    /**
     * Returns the module instance
     *
     * @codeCoverageIgnore
     *
     * @return $this
     */
    public static function getInstance()
    {
        return oxRegistry::get('[{$oScaffold->sVendor}][{$oScaffold->sModuleName}]Module');
    }

    /**
     * On module activation callback
     * Calls install.sql.tpl or install.sql script from docs folder
     *
     * @codeCoverageIgnore
     */
    public static function onActivate()
    {
        try {
            self::_runQueryFromFile('install.sql');
        } catch (oxFileException $oEx) {
        }
    }

    /**
     * On module deactivation callback
     * Calls uninstall.sql.tpl or uninstall.sql script from docs folder
     *
     * @codeCoverageIgnore
     */
    public static function onDeactivate()
    {
        if (function_exists('module_enabled_count') && module_enabled_count('[{$oScaffold->sModuleId}]') < 2) {
            try {
                self::_runQueryFromFile('uninstall.sql');
            } catch (oxFileException $oEx) {
            }
        }
    }

    /**
     * Run query from given file
     *
     * @codeCoverageIgnore
     *
     * @param string $sFilename
     *
     * @throws oxFileException
     */
    protected static function _runQueryFromFile($sFilename)
    {
        $sContent = static::_getContentFromFile($sFilename);
        $oDb      = oxDb::getDb();

        foreach (explode(';', $sContent) as $sQuery) {
            $sQuery = trim($sQuery);
            if (!$sQuery) {
                continue;
            }

            $oDb->execute($sQuery);
        }
    }

    /**
     * Get content from file
     *
     * File must be placed in docs directory. If file with your given name + .tpl exist then
     * it will be processed with Smarty with publicly available object of $oModule with
     * this object instance
     *
     * @codeCoverageIgnore
     *
     * @param string $sFilename
     *
     * @return string
     * @throws oxFileException
     */
    protected static function _getContentFromFile($sFilename)
    {
        $sFilePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . $sFilename;

        if (file_exists($sFilePath . '.tpl')) {
            /** @var Smarty $oSmarty */
            $oSmarty = oxRegistry::get('oxUtilsView')->getSmarty();
            $oSmarty->assign('oModule', static::getInstance());

            return $oSmarty->fetch($sFilename . '.tpl');

        } else if (!file_exists($sFilePath)) {
            /** @var oxFileException $oEx */
            $oEx = oxNew('oxFileException');
            $oEx->setFileName($sFilePath);
            $oEx->setMessage('File does not exist');
            throw $oEx;
        }

        return file_get_contents($sFilePath);
    }

    /**
     * Does column exist in specific table?
     *
     * @codeCoverageIgnore
     *
     * @param string $sTable Table name
     * @param string $sColumn Column name
     *
     * @return bool
     */
    protected static function _columnExists($sTable, $sColumn)
    {
        $oConfig = oxRegistry::getConfig();
        $sDbName = $oConfig->getConfigParam('dbName');
        $sSql    = "SELECT 1
                    FROM information_schema.COLUMNS
                    WHERE
                    TABLE_SCHEMA = %s
                    AND TABLE_NAME = %s
                    AND COLUMN_NAME = %s";

        $oDb = oxDb::getDb();

        return (bool) $oDb->getOne(sprintf(
            $sSql,
            $oDb->quote($sDbName),
            $oDb->quote($sTable),
            $oDb->quote($sColumn)
        ));
    }
}