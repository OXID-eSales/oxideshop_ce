<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Str;

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
    public function truncate(
        string $sString = null,
        int $iLength = 80,
        string $sSufix = '...',
        bool $blBreakWords = false,
        bool $middle = false
    ): string {
        if ($iLength == 0) {
            return '';
        } elseif ($iLength > 0 && Str::getStr()->strlen($sString) > $iLength) {
            $iLength -= Str::getStr()->strlen($sSufix);

            $sString = str_replace(['&#039;', '&quot;'], ["'", '"'], $sString);

            if (!$blBreakWords) {
                $sString = Str::getStr()->preg_replace(
                    '/\s+?(\S+)?$/',
                    '',
                    Str::getStr()->substr($sString, 0, $iLength + 1)
                );
            }

            $sString = Str::getStr()->substr($sString, 0, $iLength) . $sSufix;

            return str_replace(["'", '"'], ['&#039;', '&quot;'], $sString);
        }

        return $sString ?: '';
    }
}
