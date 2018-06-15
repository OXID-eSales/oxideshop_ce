<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Debug information formatter
 */
class DebugInfo
{
    /**
     * format template data for debug view
     *
     * @param array $viewData template data
     *
     * @return string
     */
    public function formatTemplateData($viewData = [])
    {
        $log = '';
        reset($viewData);
        foreach ($viewData as $viewName => $viewDataObject) {
            // show debbuging information
            $log .= "TemplateData[$viewName] : <br />\n";
            $log .= print_r($viewDataObject, 1);
        }

        return $log;
    }

    /**
     * format memory usage
     *
     * @return string
     */
    public function formatMemoryUsage()
    {
        $log = '';
        if (function_exists('memory_get_usage')) {
            $kb = ( int ) (memory_get_usage() / 1024);
            $mb = round($kb / 1024, 3);
            $log .= 'Memory usage: ' . $mb . ' MB';

            if (function_exists('memory_get_peak_usage')) {
                $peakKb = ( int ) (memory_get_peak_usage() / 1024);
                $peakMb = round($peakKb / 1024, 3);
                $log .= ' (peak: ' . $peakMb . ' MB)';
            }
            $log .= '<br />';

            if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
                $kb = ( int ) (memory_get_usage(true) / 1024);
                $mb = round($kb / 1024, 3);
                $log .= 'System memory usage: ' . $mb . ' MB';

                if (function_exists('memory_get_peak_usage')) {
                    $peakKb = ( int ) (memory_get_peak_usage(true) / 1024);
                    $peakMb = round($peakKb / 1024, 3);
                    $log .= ' (peak: ' . $peakMb . ' MB)';
                }
                $log .= '<br />';
            }
        }

        return $log;
    }

    /**
     * format execution times
     *
     * @param double $dTotalTime total time
     *
     * @return string
     */
    public function formatExecutionTime($dTotalTime)
    {
        $log = 'Execution time:' . round($dTotalTime, 4) . '<br />';
        global $aProfileTimes;
        global $executionCounts;
        if (is_array($aProfileTimes)) {
            $log .= "----------------------------------------------------------<br>" . PHP_EOL;
            arsort($aProfileTimes);
            $log .= "<table cellspacing='10px' style='border: 1px solid #000'>";
            foreach ($aProfileTimes as $key => $val) {
                $log .= "<tr><td style='border-bottom: 1px dotted #000;min-width:300px;'>Profile $key: </td><td style='border-bottom: 1px dotted #000;min-width:100px;'>" . round($val, 5) . "s</td>";
                if ($dTotalTime) {
                    $log .= "<td style='border-bottom: 1px dotted #000;min-width:100px;'>" . round($val * 100 / $dTotalTime, 2) . "%</td>";
                }
                if ($executionCounts[$key]) {
                    $log .= " <td style='border-bottom: 1px dotted #000;min-width:50px;padding-right:30px;' align='right'>" . $executionCounts[$key] . "</td>"
                             . "<td style='border-bottom: 1px dotted #000;min-width:15px; '>*</td>"
                             . "<td style='border-bottom: 1px dotted #000;min-width:100px;'>" . round($val / $executionCounts[$key], 5) . "s</td>" . PHP_EOL;
                } else {
                    $log .= " <td colspan=3 style='border-bottom: 1px dotted #000;min-width:100px;'> not stopped correctly! </td>" . PHP_EOL;
                }
                $log .= '</tr>';
            }
            $log .= "</table>";
        }

        return $log;
    }

    /**
     * general info (debug title)
     *
     * @return string
     */
    public function formatGeneralInfo()
    {
        $log = "cl=" . \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView()->getClassName();
        if (($fnc = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView()->getFncName())) {
            $log .= " fnc=$fnc";
        }

        return $log;
    }

    /**
     * Forms view name and timestamp to.
     *
     * @return string
     */
    public function formatTimeStamp()
    {
        $log = '';
        $className = \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView()->getClassName();
        $log .= "<div id='" . $className . "_executed'>Executed: " . date('Y-m-d H:i:s') . "</div>";
        $log .= "<div id='" . $className . "_timestamp'>Timestamp: " . microtime(true) . "</div>";

        return $log;
    }
}
