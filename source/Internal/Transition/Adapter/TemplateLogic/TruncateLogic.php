<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class TruncateLogic
{
    /**
     * @param string  $sString
     * @param integer $iLength
     * @param string  $sSufix
     * @param bool    $blBreakWords
     * @param bool    $middle
     *
     * @return string
     */
    public function truncate(string $sString = null, int $iLength = 80, string $sSufix = '...', bool $blBreakWords = false, bool $middle = false): string
    {
        if ($iLength == 0) {
            return '';
        } elseif ($iLength > 0 && getStr()->strlen($sString) > $iLength) {
            $iLength -= getStr()->strlen($sSufix);

            $sString = str_replace(['&#039;', '&quot;'], ["'", '"'], $sString);

            if (!$blBreakWords) {
                $sString = getStr()->preg_replace('/\s+?(\S+)?$/', '', getStr()->substr($sString, 0, $iLength + 1));
            }

            $sString = getStr()->substr($sString, 0, $iLength) . $sSufix;

            return str_replace(["'", '"'], ['&#039;', '&quot;'], $sString);
        }

        return $sString ?: '';
    }
}
