<?php

// Since we do not want to bootstrap the whole shop for unittest
// we define some simple implementations for common functions

if (!function_exists('oxNew')) {
    function oxNew($className)
    {
        return new $className();
    }
}

if (!function_exists('startProfile')) {
    function startProfile($name)
    {
    }
}

if (!function_exists('stopProfile')) {
    function stopProfile($name)
    {
    }
}