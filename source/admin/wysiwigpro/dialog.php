<?php
/* generates dialog windows! */
if (!isset($_GET['dialog'])) exit();
if (!defined('IN_WPRO')) define('IN_WPRO', true);
require_once(dirname(__FILE__).'/config.inc.php');
require_once(WPRO_DIR.'core/libs/common.inc.php');
require_once(WPRO_DIR.'core/libs/wproDialog.class.php');

$DIALOG = new wproDialog();
$EDITOR->triggerEvent('onLoadDialog');
$pluginName = isset($_GET['dialog']) ? $_GET['dialog'] : '';
$DIALOG->loadPlugin($pluginName, true);
$DIALOG->runPluginAction($pluginName, isset($_GET['action']) ? $_GET['action'] : 'default', array_merge($_GET, $_POST));
$DIALOG->display();
?>