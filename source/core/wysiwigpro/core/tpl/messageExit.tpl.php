<?php if (!defined('IN_WPRO')) exit; ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript">
document.write('<base href="'+(String(document.location).replace(/(^(http|https):\/\/[^\/]*)[\s\S]*/i, '$1'))+'<?php echo addslashes($editorURL) ?>" />');
var wproIframeDialogs = <?php echo $iframeDialogs ? 'true' : 'false' ?>;
var frameID = <?php echo intval($frameID); ?>;
var openerID = <?php echo ($openerID===NULL) ? 'null': intval($openerID) ; ?>;
var wproEmbedded = false;
</script>
<script type="text/javascript" src="js/dialogEditorShared_src.js"></script>
<script type="text/javascript" src="core/js/dialog_src.js"></script>
<script type="text/javascript">
dialog.init();
</script>
<link rel="stylesheet" href="<?php echo $themeURL.'/dialog.css' ?>" type="text/css" />
<link rel="stylesheet" href="core/css/dialog.css" type="text/css" />
</head>

<body>
<form action="<?php echo $action ?>" name="dialogForm" id="dialogForm" method="get"<?php if (!empty($jsAction)): ?> onsubmit="<?php echo $jsAction ?>"<?php endif ?>>
<?php require(dirname(__FILE__).'/messageExitBody.tpl.php'); ?>
</form>
</body>
</html>