<?php

class _config {
    function __construct(){
        if (file_exists('_version_define.php')) {
            include "_version_define.php";
        }
        include "config.inc.php";
        include "core/oxconfk.php";
    }
}
$_cfg = new _config();
echo "clearing tmp files...";
foreach (glob($_cfg->sCompileDir."/*") as $filename) {
    if (is_file($filename)){
        unlink($filename);
    }
    if (is_dir($filename)){
        rmdir($filename);
    }
    //echo "* ".$filename."\n";
}
echo " done.";