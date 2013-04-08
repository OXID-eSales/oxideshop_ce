<?php
if (!defined('IN_WPRO')) define('IN_WPRO', true);
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.inc.php');
require_once(WPRO_DIR.'core/libs/common.inc.php');
header('Content-Type: text/html; charset='.$EDITOR->langEngine->get('conf','charset'));
$EDITOR->langEngine->loadFile('wysiwygpro/includes/wproCore_spellchecker.inc.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $EDITOR->langEngine->get('wproCore_spellchecker', 'contacting')?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $EDITOR->langEngine->get('conf','charset')?>">
<style type="text/css">body {font-family:Verdana, Arial, Helvetica, sans-serif;font-size:11px;}</style>
</head>

<body>

<p><?php echo $EDITOR->langEngine->get('wproCore_spellchecker', 'contacting')?></p>
</body>
</html>
