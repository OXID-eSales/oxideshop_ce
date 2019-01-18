<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\ViewHelper;

/**
 * Class for preparing JavaScript.
 */
class JavaScriptRenderer
{
    /**
     * Renders all registered JavaScript snippets and files.
     *
     * @param string $widget      Widget name
     * @param bool   $forceRender Force rendering of scripts.
     * @param bool   $isDynamic   Force rendering of scripts.
     *
     * @return string
     */
    public function render($widget, $forceRender, $isDynamic = false)
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $output = '';
        $suffix = $isDynamic ? '_dynamic' : '';
        $filesParameterName = \OxidEsales\Eshop\Core\ViewHelper\JavaScriptRegistrator::FILES_PARAMETER_NAME . $suffix;
        $scriptsParameterName = \OxidEsales\Eshop\Core\ViewHelper\JavaScriptRegistrator::SNIPPETS_PARAMETER_NAME . $suffix;

        $isAjaxRequest = $this->isAjaxRequest();
        $forceRender = $this->shouldForceRender($forceRender, $isAjaxRequest);

        if (!$widget || $forceRender) {
            if (!$isAjaxRequest) {
                $files = $this->prepareFilesForRendering($config->getGlobalParameter($filesParameterName), $widget);
                $output .= $this->formFilesOutput($files, $widget);
                $config->setGlobalParameter($filesParameterName, null);
                if ($widget) {
                    $dynamicIncludes = (array)$config->getGlobalParameter(\OxidEsales\Eshop\Core\ViewHelper\JavaScriptRegistrator::FILES_PARAMETER_NAME . '_dynamic');
                    $output .= $this->formFilesOutput($dynamicIncludes, $widget);
                    $config->setGlobalParameter(\OxidEsales\Eshop\Core\ViewHelper\JavaScriptRegistrator::FILES_PARAMETER_NAME . '_dynamic', null);
                }
            }

            // Form output for adds.
            $snippets = (array)$config->getGlobalParameter($scriptsParameterName);
            $scriptOutput = $this->formSnippetsOutput($snippets, $widget, $isAjaxRequest);
            $config->setGlobalParameter($scriptsParameterName, null);
            if ($widget) {
                $dynamicScripts = (array) $config->getGlobalParameter(\OxidEsales\Eshop\Core\ViewHelper\JavaScriptRegistrator::SNIPPETS_PARAMETER_NAME . '_dynamic');
                $scriptOutput .= $this->formSnippetsOutput($dynamicScripts, $widget, $isAjaxRequest);
                $config->setGlobalParameter(\OxidEsales\Eshop\Core\ViewHelper\JavaScriptRegistrator::SNIPPETS_PARAMETER_NAME . '_dynamic', null);
            }
            $output .= $this->enclose($scriptOutput, $widget, $isAjaxRequest);
        }

        return $output;
    }

    /**
     * Returns if it is ajax request.
     *
     * @return bool
     */
    protected function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Returns whether rendering of scripts should be forced.
     *
     * @param bool $forceRender
     * @param bool $isAjaxRequest
     *
     * @return bool
     */
    protected function shouldForceRender($forceRender, $isAjaxRequest)
    {
        return $isAjaxRequest ? true : $forceRender;
    }

    /**
     * Returns files list for rendering.
     *
     * @param array  $files
     * @param string $widget
     *
     * @return array
     */
    protected function prepareFilesForRendering($files, $widget)
    {
        return (array) $files;
    }

    /**
     * Form output for includes.
     *
     * @param array  $includes String files to include.
     * @param string $widget   Widget name.
     *
     * @return string
     */
    protected function formFilesOutput($includes, $widget)
    {
        if (!count($includes)) {
            return '';
        }

        ksort($includes); // Sort by priority.
        $usedSources = [];
        $widgets = [];
        $widgetTemplate = "WidgetsHandler.registerFile('%s', '%s');";
        $scriptTemplate = '<script type="text/javascript" src="%s"></script>';
        foreach ($includes as $priority) {
            foreach ($priority as $source) {
                if (!in_array($source, $usedSources)) {
                    $widgets[] = sprintf(($widget ? $widgetTemplate : $scriptTemplate), $source, $widget);
                    $usedSources[] = $source;
                }
            }
        }
        $output = implode(PHP_EOL, $widgets);
        if ($widget && !empty($output)) {
            $output = <<<JS
<script type='text/javascript'>
    window.addEventListener('load', function() {
        $output
    }, false)
</script>
JS;
        }

        return $output;
    }

    /**
     * Forms how javascript should look like when output.
     * If varnish is active, javascript should be passed to WidgetsHandler instead of direct call.
     *
     * @param array  $scripts     Scripts to execute (from add).
     * @param string $widgetName  Widget name.
     * @param bool   $ajaxRequest Is ajax request.
     *
     * @return string
     */
    protected function formSnippetsOutput($scripts, $widgetName, $ajaxRequest)
    {
        $preparedScripts = [];
        foreach ($scripts as $script) {
            if ($widgetName && !$ajaxRequest) {
                $sanitizedScript = $this->sanitize($script);
                $script = "WidgetsHandler.registerFunction('$sanitizedScript', '$widgetName');";
            }
            $preparedScripts[] = $script;
        }

        return implode(PHP_EOL, $preparedScripts);
    }

    /**
     * Sanitize javascript, which will be passed to WidgetsHandler.
     *
     * @param string $scripts
     *
     * @return string
     */
    protected function sanitize($scripts)
    {
        return strtr($scripts, ['\\' => '\\\\', "'" => "\\'", "\r" => '', "\n" => '\n']);
    }

    /**
     * Enclose with script tag or add in function for wiget.
     *
     * @param string $scriptsOutput javascript to be enclosed.
     * @param string $widget        widget name.
     * @param bool   $isAjaxRequest is ajax request
     *
     * @return string
     */
    protected function enclose($scriptsOutput, $widget, $isAjaxRequest)
    {
        if ($scriptsOutput) {
            if ($widget && !$isAjaxRequest) {
                $scriptsOutput = "window.addEventListener('load', function() { $scriptsOutput }, false )";
            }

            return "<script type='text/javascript'>$scriptsOutput</script>";
        }
    }
}
