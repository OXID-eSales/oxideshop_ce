<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Factory class responsible for redirecting string handling functions to specific
 * string handling class. String handler basically is intended for dealing with multibyte string
 * and is NOT supposed to replace all string handling functions.
 * We use the handler for shop data and user input, but prefer not to use it for ascii strings
 * (eg. field or file names).
 */
class Str
{
    /**
     * Specific string handler
     *
     * @var object
     */
    protected static $_oHandler;

    /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     *
     * @return null;
     */
    public function __construct()
    {
    }

    /**
     * Static method initializing new string handler or returning the existing one.
     *
     * @return StrRegular|StrMb
     */
    public static function getStr()
    {
        if (!isset(self::$_oHandler)) {
            //let's init now non-static instance of oxStr to get the instance of str handler
            self::$_oHandler = oxNew(\OxidEsales\Eshop\Core\Str::class)->_getStrHandler();
        }

        return self::$_oHandler;
    }

    /**
     * Non static getter returning str handler. The sense of getStr() and _getStrHandler() is
     * to be possible to call this method statically ( \OxidEsales\Eshop\Core\Str::getStr() ), yet leaving the
     * possibility to extend it in modules by overriding _getStrHandler() method.
     *
     * @return oxStrRegular|oxStrMb
     */
    protected function _getStrHandler()
    {
        if (function_exists('mb_strlen')) {
            return oxNew(\OxidEsales\Eshop\Core\StrMb::class);
        }

        return oxNew(\OxidEsales\Eshop\Core\StrRegular::class);
    }
}
