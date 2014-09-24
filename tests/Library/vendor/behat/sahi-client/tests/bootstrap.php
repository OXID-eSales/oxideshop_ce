<?php

if (!is_dir($vendor = __DIR__.'/../vendor')) {
    die('Install dependencies first');
}

require($vendor.'/autoload.php');
