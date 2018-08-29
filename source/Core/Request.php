<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Core;

/**
 * Request represents an HTTP request.
 */
class Request
{
    /**
     * Returns raw value of parameter stored in POST,GET.
     *
     * @param string $name         Name of parameter.
     * @param string $defaultValue Default value if no value provided.
     *
     * @return mixed
     */
    public function getRequestParameter($name, $defaultValue = null)
    {
        if (isset($_POST[$name])) {
            $value = $_POST[$name];
        } elseif (isset($_GET[$name])) {
            $value = $_GET[$name];
        } else {
            $value = $defaultValue;
        }

        return $value;
    }

    /**
     * Returns escaped value of parameter stored in POST,GET.
     *
     * @param string $name         Name of parameter.
     * @param string $defaultValue Default value if no value provided.
     *
     * @return mixed
     */
    public function getRequestEscapedParameter($name, $defaultValue = null)
    {
        $value = $this->getRequestParameter($name, $defaultValue);

        // TODO: remove this after special chars concept implementation
        $isAdmin = Registry::getConfig()->isAdmin() && Registry::getSession()->getVariable("blIsAdmin");
        if ($value !== null && !$isAdmin) {
            $this->checkParamSpecialChars($value);
        }

        return $value;
    }

    /**
     * Returns request url, which was executed to render current page view
     *
     * @param string $sParams     Parameters to object
     * @param bool   $blReturnUrl If return url
     *
     * @return string
     */
    public function getRequestUrl($sParams = '', $blReturnUrl = false)
    {
        $requestUrl = '';
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']) {
                $rawRequestUrl = $_SERVER['REQUEST_URI'];
            } else {
                $rawRequestUrl = $_SERVER['SCRIPT_URI'];
            }

            // trying to resolve controller file name
            if ($rawRequestUrl && ($iPos = stripos($rawRequestUrl, '?')) !== false) {
                $string = getStr();
                // formatting request url
                $requestUrl = 'index.php' . $string->substr($rawRequestUrl, $iPos);

                // removing possible session id
                $requestUrl = $string->preg_replace('/(&|\?)(force_)?(admin_)?sid=[^&]*&?/', '$1', $requestUrl);
                $requestUrl = $string->preg_replace('/(&|\?)stoken=[^&]*&?/', '$1', $requestUrl);
                $requestUrl = $string->preg_replace('/&$/', '', $requestUrl);
                $requestUrl = str_replace('&', '&amp;', $requestUrl);
            }
        }

        return $requestUrl;
    }

    /**
     * Checks if passed parameter has special chars and replaces them.
     * Returns checked value.
     *
     * @param mixed $sValue value to process escaping
     * @param array $aRaw   keys of unescaped values
     *
     * @return mixed
     */
    public function checkParamSpecialChars(& $sValue, $aRaw = null)
    {
        if (is_object($sValue)) {
            return $sValue;
        }

        if (is_array($sValue)) {
            $newValue = [];
            foreach ($sValue as $sKey => $sVal) {
                $sValidKey = $sKey;
                if (!$aRaw || !in_array($sKey, $aRaw)) {
                    $this->checkParamSpecialChars($sValidKey);
                    $this->checkParamSpecialChars($sVal);
                    if ($sValidKey != $sKey) {
                        unset($sValue[$sKey]);
                    }
                }
                $newValue[$sValidKey] = $sVal;
            }
            $sValue = $newValue;
        } elseif (is_string($sValue)) {
            $sValue = str_replace(
                ['&', '<', '>', '"', "'", chr(0), '\\', "\n", "\r"],
                ['&amp;', '&lt;', '&gt;', '&quot;', '&#039;', '', '&#092;', '&#10;', '&#13;'],
                $sValue
            );
        }

        return $sValue;
    }
}
