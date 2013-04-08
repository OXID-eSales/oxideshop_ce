<?php if (!defined('IN_WPRO')) exit; ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langEngine->get('conf','charset') ?>" />
<style type="text/css">body{padding:0px;margin:0px;border:0px;}</style>
<script type="text/javascript">
var wproIframeDialogs = <?php echo $EDITOR->iframeDialogs ? 'true' : 'false' ?>;
var frameID = <?php echo isset($_GET['frameID']) ? intval($_GET['frameID']) : 0; ?>;
</script>
<?php if ($EDITOR->iframeDialogs) : ?>
<script src="core/js/dragiframe_src.js" type="text/javascript"></script>
<?php endif ?>
</head>
<body>
<iframe src="<?php echo htmlspecialchars($EDITOR->editorLink('dialog.php?inframe=true&'.$_SERVER["QUERY_STRING"])); ?>" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>
</body>
</html>