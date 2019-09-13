<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Core;

/**
 * Settings handler class.
 */
class SettingsHandler extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Module type.
     *
     * e.g. 'module' or 'theme'
     *
     * @var string
     */
    protected $moduleType;

    /**
     * Sets the Module type
     *
     * @param string $moduleType can be either 'module' or 'theme'
     *
     * @return oxSettingsHandler
     */
    public function setModuleType($moduleType)
    {
        $this->moduleType = $moduleType;

        return $this;
    }

    /**
     * Get settings and module id and starts import process.
     *
     * Run module settings import logic only if it has settings array
     * On empty settings array, it will remove the settings.
     *
     * @param object $module Module or Theme Object
     */
    public function run($module)
    {
        $moduleSettings = $module->getInfo('settings');
        $isTheme = $this->isTheme($module->getId());
        if (!$isTheme || ($isTheme && is_array($moduleSettings))) {
            $this->addModuleSettings($moduleSettings, $module->getId());
        }
    }

    /**
     * Adds settings to database.
     *
     * @param array  $moduleSettings Module settings array
     * @param string $moduleId       Module id
     */
    protected function addModuleSettings($moduleSettings, $moduleId)
    {
        $this->removeNotUsedSettings($moduleSettings, $moduleId);
        $config = $this->getConfig();
        $shopId = $config->getShopId();
        $moduleConfigs = $this->getModuleConfigs($moduleId);
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        if (is_array($moduleSettings)) {
            foreach ($moduleSettings as $setting) {
                $oxid = \OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUId();

                $module = $this->getModuleConfigId($moduleId);
                $name = $setting["name"];
                $type = $setting["type"];

                if ($this->isTheme($moduleId)) {
                    $value = array_key_exists($name, $moduleConfigs) ? $moduleConfigs[$name] : $setting["value"];
                } else {
                    $value = is_null($config->getConfigParam($name)) ? $setting["value"] : $config->getConfigParam($name);
                }

                $group = $setting["group"];

                $constraints = "";
                if (isset($setting["constraints"]) && $setting["constraints"]) {
                    $constraints = $setting["constraints"];
                } elseif (isset($setting["constrains"]) && $setting["constrains"]) {
                    $constraints = $setting["constrains"];
                }

                $position = 1;
                if (isset($setting["position"])) {
                    $position = $setting["position"];
                }

                $config->saveShopConfVar($type, $name, $value, $shopId, $module);

                $deleteSql = "DELETE FROM `oxconfigdisplay` WHERE OXCFGMODULE = :oxcfgmodule AND OXCFGVARNAME = :oxcfgvarname";
                $insertSql = "INSERT INTO `oxconfigdisplay` (`OXID`, `OXCFGMODULE`, `OXCFGVARNAME`, `OXGROUPING`, `OXVARCONSTRAINT`, `OXPOS`) " .
                             "VALUES (:oxid, :oxcfgmodule, :oxcfgvarname, :oxgrouping, :oxvarconstraint, :oxpos)";

                $db->execute($deleteSql, [
                    ':oxcfgmodule' => $module,
                    ':oxcfgvarname' => $name,
                ]);
                $db->execute($insertSql, [
                    ':oxid' => $oxid,
                    ':oxcfgmodule' => $module,
                    ':oxcfgvarname' => $name,
                    ':oxgrouping' => $group,
                    ':oxvarconstraint' => $constraints,
                    ':oxpos' => $position,
                ]);
            }
        }
    }

    /**
     * Check if module is theme.
     *
     * @param string $moduleId
     * @return bool
     */
    protected function isTheme($moduleId)
    {
        $moduleConfigId = $this->getModuleConfigId($moduleId);
        $themeTypeCondition = "@^" . Config::OXMODULE_THEME_PREFIX . "@i";
        return (bool)preg_match($themeTypeCondition, $moduleConfigId);
    }

    /**
     * Removes configs which are removed from module metadata
     *
     * @param array  $moduleSettings Module settings
     * @param string $moduleId       Module id
     */
    protected function removeNotUsedSettings($moduleSettings, $moduleId)
    {
        $moduleConfigs = array_keys($this->getModuleConfigs($moduleId));
        $moduleSettings = $this->parseModuleSettings($moduleSettings);

        $configsToRemove = array_diff($moduleConfigs, $moduleSettings);
        if (!empty($configsToRemove)) {
            $this->removeModuleConfigs($moduleId, $configsToRemove);
        }
    }

    /**
     * Returns module configuration from database
     *
     * @param string $moduleId Module id
     *
     * @return array key=>value
     */
    protected function getModuleConfigs($moduleId)
    {
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $config = $this->getConfig();
        $shopId = $config->getShopId();
        $module = $this->getModuleConfigId($moduleId);

        $decodeValueQuery = $config->getDecodeValueQuery();
        $moduleConfigsQuery = "SELECT oxvarname, oxvartype, {$decodeValueQuery} as oxvardecodedvalue FROM oxconfig WHERE oxmodule = :oxmodule AND oxshopid = :oxshopid";
        $dbConfigs = $db->getAll($moduleConfigsQuery, [
            ':oxmodule' => $module,
            ':oxshopid' => $shopId
        ]);

        $result = [];
        foreach ($dbConfigs as $oneModuleConfig) {
            $result[$oneModuleConfig['oxvarname']] = $config->decodeValue($oneModuleConfig['oxvartype'], $oneModuleConfig['oxvardecodedvalue']);
        }

        return $result;
    }

    /**
     * Parses module config variable names to array from module settings
     *
     * @param array $moduleSettings Module settings
     *
     * @return array
     */
    protected function parseModuleSettings($moduleSettings)
    {
        $settings = [];

        if (is_array($moduleSettings)) {
            foreach ($moduleSettings as $setting) {
                $settings[] = $setting['name'];
            }
        }

        return $settings;
    }

    /**
     * Removes module configs from database
     *
     * @param string $moduleId        Module id
     * @param array  $configsToRemove Configs to remove
     */
    protected function removeModuleConfigs($moduleId, $configsToRemove)
    {
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $quotedConfigsToRemove = array_map([$db, 'quote'], $configsToRemove);
        $deleteSql = "DELETE
                       FROM `oxconfig`
                       WHERE oxmodule = :oxmodule AND
                             oxshopid = :oxshopid AND
                             oxvarname IN (" . implode(", ", $quotedConfigsToRemove) . ")";

        $db->execute($deleteSql, [
            ':oxmodule' => $this->getModuleConfigId($moduleId),
            ':oxshopid' => $this->getConfig()->getShopId(),
        ]);
    }

    /**
     * Get config tables specific module id
     *
     * @param string $moduleId
     * @return string
     */
    protected function getModuleConfigId($moduleId)
    {
        return $this->moduleType . ':' . $moduleId;
    }
}
