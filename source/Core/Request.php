<?php

namespace OxidEsales\Eshop\Core;

class Request
{

    /**
     * Returns value of parameter stored in POST,GET.
     * For security reasons performed oxconfig->checkParamSpecialChars().
     * use $blRaw very carefully if you want to get unescaped
     * parameter.
     *
     * @param string $name  Name of parameter.
     * @param bool   $blRaw Get unescaped parameter.
     *
     * @deprecated on b-dev (2015-06-10);
     * Use oxConfig::getRequestEscapedParameter() or oxConfig::getRequestRawParameter().
     *
     * @return mixed
     */
    public function getRequestParameter($name, $blRaw = false)
    {
        if ($blRaw) {
            $sValue = $this->getRequestRawParameter($name);
        } else {
            $sValue = $this->getRequestEscapedParameter($name);
        }

        return $sValue;
    }


    /**
     * Returns raw value of parameter stored in POST,GET.
     *
     * @param string $name         Name of parameter.
     * @param string $defaultValue Default value if no value provided.
     *
     * @return mixed
     */
    public function getRequestRawParameter($name, $defaultValue = null)
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
        $value = $this->getRequestRawParameter($name, $defaultValue);

        // TODO: remove this after special chars concept implementation
        $isAdmin = false;//$this->isAdmin() && $this->getSession()->getVariable("blIsAdmin");
        if ($value !== null && !$isAdmin) {
            $this->checkParamSpecialChars($value);
        }

        return $value;
    }


    /**
     * Checks if passed parameter has special chars and replaces them.
     * Returns checked value.
     *
     * @param mixed &$sValue value to process escaping
     * @param array $aRaw    keys of unescaped values
     *
     * @return mixed
     */
    public function checkParamSpecialChars(& $sValue, $aRaw = null)
    {
        if (is_object($sValue)) {
            return $sValue;
        }

        if (is_array($sValue)) {
            $newValue = array();
            foreach ($sValue as $sKey => $sVal) {
                $sValidKey = $sKey;
                if (!$aRaw || !in_array($sKey, $aRaw)) {
                    $this->checkParamSpecialChars($sValidKey);
                    $this->checkParamSpecialChars($sVal);
                    if ($sValidKey != $sKey) {
                        unset ($sValue[$sKey]);
                    }
                }
                $newValue[$sValidKey] = $sVal;
            }
            $sValue = $newValue;
        } elseif (is_string($sValue)) {
            $sValue = str_replace(
                array('&', '<', '>', '"', "'", chr(0), '\\', "\n", "\r"),
                array('&amp;', '&lt;', '&gt;', '&quot;', '&#039;', '', '&#092;', '&#10;', '&#13;'),
                $sValue
            );
        }

        return $sValue;
    }

}