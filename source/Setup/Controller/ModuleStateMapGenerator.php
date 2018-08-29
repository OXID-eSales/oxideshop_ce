<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Setup\Controller;

use OxidEsales\Eshop\Core\SystemRequirements;

/**
 * Class ModuleStateMapGenerator.
 *
 * Accepts SystemRequirementsInfo as primary source of data and converts it to be compatible with setup's view
 * component which displays the system requirements (Used in Controller).
 *
 * It also accepts the following custom functions to help and deal with:
 *
 *   - ModuleStateHtmlClass converter to map module state integer value to custom HTML class strings;
 *   - ModuleNameTranslate to translate module id to it's full name/title;
 *   - ModuleGroupNameTranslate to translate group module id to it's full name/title;
 */
class ModuleStateMapGenerator
{
    const MODULE_ID_KEY = 'module';
    const MODULE_STATE_KEY = 'state';
    const MODULE_NAME_KEY = 'modulename';
    const MODULE_STATE_HTML_CLASS_KEY = 'class';

    /** @var array Raw array taken from `SystemRequirements::getSystemInfo` */
    private $systemRequirementsInfo = [];

    /** @var \Closure Change given module state into HTML class to be displayed during Setup */
    private $moduleStateHtmlClassConvertFunction = null;

    /** @var \Closure Translate module id into module name */
    private $moduleNameTranslateFunction = null;

    /** @var \Closure Translate module group id into module name */
    private $moduleGroupNameTranslateFunction = null;

    /**
     * ModuleStateMapGenerator constructor.
     *
     * @param array $systemRequirementsInfo
     */
    public function __construct($systemRequirementsInfo = [])
    {
        $this->systemRequirementsInfo = $systemRequirementsInfo;
    }

    /**
     * Returns module state map with all applied external functions.
     *
     * In case a function is not set it will be just skipped.
     *
     * @return array Module State Map in a form of
     * [
     *   'Translated group name' => [
     *     MODULE_ID_KEY => 'moduleId',
     *     MODULE_STATE_KEY => 'moduleState',
     *     MODULE_NAME_KEY => 'Translated module name',
     *     MODULE_STATE_HTML_CLASS_KEY => 'html class',
     *   ],
     *   ...
     * ]
     */
    public function getModuleStateMap()
    {
        $moduleStateMap = $this->convertFromSystemRequirementsInfo();
        $moduleStateMap = $this->applyModuleStateHtmlClassConvertFunction($moduleStateMap);
        $moduleStateMap = $this->applyModuleNameTranslateFunction($moduleStateMap);
        $moduleStateMap = $this->applyModuleGroupNameTranslateFunction($moduleStateMap);

        return $moduleStateMap;
    }

    /**
     * Convert a raw array taken from `SystemRequirements::getSystemInfo` into a format described in
     * `getModuleStateMap`.
     *
     * @return array
     */
    private function convertFromSystemRequirementsInfo()
    {
        $moduleStateMap = [];

        $iteration = SystemRequirements::iterateThroughSystemRequirementsInfo($this->systemRequirementsInfo);
        foreach ($iteration as list($groupId, $moduleId, $moduleState)) {
            $moduleStateMap[$groupId][] = [
                self::MODULE_ID_KEY => $moduleId,
                self::MODULE_STATE_KEY => $moduleState,
            ];
        }

        return $moduleStateMap;
    }

    /**
     * Apply function which converts module state into HTML class of given state.
     *
     * @param array $moduleStateMap An array of format described in `getModuleStateMap`.
     *
     * @return array An array of format described in `getModuleStateMap`.
     */
    private function applyModuleStateHtmlClassConvertFunction($moduleStateMap)
    {
        return $this->applyModuleStateMapFilterFunction(
            $moduleStateMap,
            $this->moduleStateHtmlClassConvertFunction,
            function ($moduleData, $convertFunction) {
                $moduleState = $moduleData[self::MODULE_STATE_KEY];
                $moduleData[self::MODULE_STATE_HTML_CLASS_KEY] = $convertFunction($moduleState);

                return $moduleData;
            }
        );
    }

    /**
     * Apply function which translates module id into module name.
     *
     * @param array $moduleStateMap An array of format described in `getModuleStateMap`.
     *
     * @return array An array of format described in `getModuleStateMap`.
     */
    private function applyModuleNameTranslateFunction($moduleStateMap)
    {
        return $this->applyModuleStateMapFilterFunction(
            $moduleStateMap,
            $this->moduleNameTranslateFunction,
            function ($moduleData, $translateFunction) {
                $moduleId = $moduleData[self::MODULE_ID_KEY];
                $moduleData[self::MODULE_NAME_KEY] = $translateFunction($moduleId);

                return $moduleData;
            }
        );
    }

    /**
     * Apply function which translates module group id into module group name.
     *
     * @param array $moduleStateMap An array of format described in `getModuleStateMap`.
     *
     * @return array An array of format described in `getModuleStateMap`.
     */
    private function applyModuleGroupNameTranslateFunction($moduleStateMap)
    {
        $moduleGroupNameTranslateFilterFunction = $this->moduleGroupNameTranslateFunction;

        if (!$moduleGroupNameTranslateFilterFunction) {
            return $moduleStateMap;
        }

        $translatedModuleStateMap = [];

        foreach ($this->iterateThroughModuleStateMapByGroup($moduleStateMap) as list($groupId, $modules)) {
            $groupName = $moduleGroupNameTranslateFilterFunction($groupId);
            $translatedModuleStateMap[$groupName] = $modules;
        }

        return $translatedModuleStateMap;
    }

    /**
     * Sets function which knows how to convert given module state to Html class.
     *
     * Single argument is given to the provided function as the state of module.
     *
     * @param \Closure $function
     * @throws \Exception
     */
    public function setModuleStateHtmlClassConvertFunction($function)
    {
        $this->validateClosure($function);
        $this->moduleStateHtmlClassConvertFunction = $function;
    }

    /**
     * Sets function which defines how module name should be translated.
     *
     * Single argument is given to the provided function as the module id.
     *
     * @param \Closure $function
     * @throws \Exception
     */
    public function setModuleNameTranslateFunction($function)
    {
        $this->validateClosure($function);
        $this->moduleNameTranslateFunction = $function;
    }

    /**
     * Sets function which defines how module group name should be translated.
     *
     * Single argument is given to the provided function as the module group id.
     *
     * @param \Closure $function
     * @throws \Exception
     */
    public function setModuleGroupNameTranslateFunction($function)
    {
        $this->validateClosure($function);
        $this->moduleGroupNameTranslateFunction = $function;
    }

    /**
     * Yield with [groupId, module_info_array] by iterating through given module state map.
     *
     * @param array $moduleStateMap An array of format described in `getModuleStateMap`.
     * @return \Generator Iterator which yields [groupId, module_info_array].
     */
    private function iterateThroughModuleStateMapByGroup($moduleStateMap)
    {
        foreach ($moduleStateMap as $groupId => $modules) {
            yield [$groupId, $modules];
        }
    }

    /**
     * Yield with [groupId, moduleIndex of module_info_array, module_info_array] by iterating through
     * given module state map.
     *
     * @param array $moduleStateMap An array of format described in `getModuleStateMap`.
     * @return \Generator Iterator which yields [groupId, moduleIndex of module_info_array, module_info_array]
     */
    private function iterateThroughModuleStateMap($moduleStateMap)
    {
        foreach ($this->iterateThroughModuleStateMapByGroup($moduleStateMap) as list($groupId, $modules)) {
            foreach ($modules as $moduleIndex => $moduleData) {
                yield [$groupId, $moduleIndex, $moduleData];
            }
        }
    }

    /**
     * Apply filter function to update the contents of module state map.
     *
     * @param array    $moduleStateMap               An array of format described in `getModuleStateMap`.
     * @param \Closure $helpFunction                 Help function which will be passed to moduleStateMapUpdateFunction
     *                                               as 2nd argument.
     * @param \Closure $moduleStateMapUpdateFunction Function which will be used to modify contents of module state map.
     *
     * @return array An array of format described in `getModuleStateMap`.
     */
    private function applyModuleStateMapFilterFunction($moduleStateMap, $helpFunction, $moduleStateMapUpdateFunction)
    {
        if (!$helpFunction) {
            return $moduleStateMap;
        }

        foreach ($this->iterateThroughModuleStateMap($moduleStateMap) as list($groupId, $moduleIndex, $moduleData)) {
            $moduleStateMap[$groupId][$moduleIndex] = $moduleStateMapUpdateFunction($moduleData, $helpFunction);
        }

        return $moduleStateMap;
    }

    /**
     * Validate input to check if it's a Closure.
     *
     * @param \Closure $object Given input argument to check.
     *
     * @throws \Exception Thrown when given argument does not match a Closure object.
     */
    private function validateClosure($object)
    {
        if (!$object instanceof \Closure) {
            throw new \Exception('Given argument must be an instance of Closure.');
        }
    }
}
