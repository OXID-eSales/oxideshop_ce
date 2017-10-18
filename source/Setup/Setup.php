<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup;

/**
 * The setup manager class.
 */
class Setup extends Core
{
    /**
     * Current setup step title
     *
     * @var string
     */
    protected $_sTitle = null;

    /**
     * Installation process status message
     *
     * @var string
     */
    protected $_sMessage = null;

    /**
     * Current setup step index
     *
     * @var int
     */
    protected $_iCurrStep = null;

    /** @var int Next step index */
    protected $_iNextStep = null;

    /**
     * Setup steps index array
     *
     * @var array
     */
    protected $_aSetupSteps = [
        'STEP_SYSTEMREQ'   => 100, // 0
        'STEP_WELCOME'     => 200, // 1
        'STEP_LICENSE'     => 300, // 2
        'STEP_DB_INFO'     => 400, // 3
        'STEP_DB_CONNECT'  => 410, // 31
        'STEP_DIRS_INFO'   => 500, // 4
        'STEP_DIRS_WRITE'  => 510, // 41
        'STEP_DB_CREATE'   => 520, // 42
        'STEP_FINISH'      => 700, // 6
    ];

    /**
     * Returns current setup step title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_sTitle;
    }

    /**
     * Current setup step title setter
     *
     * @param string $sTitle title
     */
    public function setTitle($sTitle)
    {
        $this->_sTitle = $sTitle;
    }

    /**
     * Returns installation process status message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_sMessage;
    }

    /**
     * Sets installation process status message
     *
     * @param string $sMsg status message
     */
    public function setMessage($sMsg)
    {
        $this->_sMessage = $sMsg;
    }

    /**
     * Returns current setup step index
     *
     * @return int
     */
    public function getCurrentStep()
    {
        if ($this->_iCurrStep === null) {
            if (($this->_iCurrStep = $this->getInstance("Utilities")->getRequestVar("istep")) === null) {
                $this->_iCurrStep = $this->getStep('STEP_SYSTEMREQ');
            }
            $this->_iCurrStep = (int) $this->_iCurrStep;
        }

        return $this->_iCurrStep;
    }

    /**
     * Returns next setup step ident
     *
     * @return int
     */
    public function getNextStep()
    {
        return $this->_iNextStep;
    }

    /**
     * Current setup step setter
     *
     * @param int $iStep current setup step index
     */
    public function setNextStep($iStep)
    {
        $this->_iNextStep = $iStep;
    }

    /**
     * Checks if config file is alleady filled with data
     *
     * @return bool
     */
    public function alreadySetUp()
    {
        $blSetUp = false;
        $sConfig = join("", file(getShopBasePath() . "config.inc.php"));
        if (strpos($sConfig, "<dbHost>") === false) {
            $blSetUp = true;
        }

        return $blSetUp;
    }

    /**
     * Decides if leave or delete Setup directory dependent from configuration.
     *
     * @return bool
     */
    public function deleteSetupDirectory()
    {
        $blDeleteSetupDirectory = true;

        $sConfig = join("", file(getShopBasePath() . "config.inc.php"));
        if (strpos($sConfig, "this->blDelSetupDir = false;") !== false) {
            $blDeleteSetupDirectory = false;
        }

        return $blDeleteSetupDirectory;
    }

    /**
     * Returns default shop id
     *
     * @return mixed
     */
    public function getShopId()
    {
        return 1;
    }

    /**
     * Returns setup steps index array
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->_aSetupSteps;
    }

    /**
     * Returns setup step index
     *
     * @param string $sStepId setup step identifier
     *
     * @return int
     */
    public function getStep($sStepId)
    {
        $steps = $this->getSteps();
        return isset($steps[$sStepId]) ? $steps[$sStepId] : null;
    }

    /**
     * $iModuleState - module status:
     * -1 - unable to datect, should not block
     *  0 - missing, blocks setup
     *  1 - fits min requirements
     *  2 - exists required or better
     *
     * @param int $iModuleState module state
     *
     * @return string
     */
    public function getModuleClass($iModuleState)
    {
        switch ($iModuleState) {
            case 2:
                $sClass = 'pass';
                break;
            case 1:
                $sClass = 'pmin';
                break;
            case -1:
                $sClass = 'null';
                break;
            default:
                $sClass = 'fail';
                break;
        }
        return $sClass;
    }
}
