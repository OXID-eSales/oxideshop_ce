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

# recursively remove a directory
function rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}
if ($_cfg->sCompileDir) {
    foreach(glob($_cfg->sCompileDir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
}

echo " done.";