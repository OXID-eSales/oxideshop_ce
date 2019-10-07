<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class SmartWordwrapLogic
{
    /**
     * @param string $string
     * @param int    $length
     * @param string $break
     * @param int    $cutRows
     * @param int    $tolerance
     * @param string $etc
     *
     * @return string
     */
    public function wrapWords($string, $length, $break, $cutRows, $tolerance, $etc)
    {

        $wrapTag = "<wrap>";
        $wrapChars = ["-"];
        $afterWrapChars = ["-" . $wrapTag];


        $string = trim($string);

        if (strlen($string) <= $length) {
            return $string;
        }

        //trying to wrap without cut
        $str = wordwrap($string, $length, $wrapTag, false);
        $arr = explode($wrapTag, $str);

        $alt = [];

        $ok = true;
        foreach ($arr as $row) {
            if (strlen($row) > ($length + $tolerance)) {
                $tmpstr = str_replace($wrapChars, $afterWrapChars, $row);
                $tmparr = explode($wrapTag, $tmpstr);

                foreach ($tmparr as $altrow) {
                    array_push($alt, $altrow);

                    if (strlen($altrow) > ($length + $tolerance)) {
                        $ok = false;
                    }
                }
            } else {
                array_push($alt, $row);
            }
        }

        $arr = $alt;

        if (!$ok) {
            //trying to wrap with cut
            $str = wordwrap($string, $length, $wrapTag, true);
            $arr = explode($wrapTag, $str);
        }

        if ($cutRows && count($arr) > $cutRows) {
            $arr = array_splice($arr, 0, $cutRows);

            if (strlen($arr[$cutRows - 1] . $etc) > $length + $tolerance) {
                $arr[$cutRows - 1] = substr($arr[$cutRows - 1], 0, $length - strlen($etc));
            }

            $arr[$cutRows - 1] = $arr[$cutRows - 1] . $etc;
        }

        return implode($break, $arr);
    }
}
