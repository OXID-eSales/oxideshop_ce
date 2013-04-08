<?php if (!defined('IN_WPRO')) exit; ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $langEngine->langCode ?>" lang="<?php echo $langEngine->langCode ?>" dir="<?php echo $langEngine->get('conf','dir') ?>">
<head>
<title><?php echo $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langEngine->get('conf','charset') ?>" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<script type="text/javascript">/*<![CDATA[ */
document.write('<base href="'+(String(document.location).replace(/(^(http|https):\/\/[^\/]*)[\s\S]*/i, '$1'))+'<?php echo addslashes($EDITOR->editorURL) ?>" />');
var strWrongSize = "<?php echo addslashes($langEngine->get('core','JSWrongSize'))?>";
var strWrongFormat = "<?php echo addslashes($langEngine->get('core','JSWrongFormat'))?>";
var wproIframeDialogs = <?php echo $EDITOR->iframeDialogs ? 'true' : 'false' ?>;
var frameID = <?php echo intval($frameID); ?>;
var openerID = <?php echo ($openerID===NULL) ? 'null': intval($openerID) ; ?>;
var wproEmbedded = <?php echo $DIALOG->embedded ? 'true' : 'false' ?>;
/* ]]>*/</script>
<?php if (WPRO_COMPILE_JS_INCLUDES) : ?>
<script type="text/javascript" src="<?php echo htmlspecialchars($EDITOR->editorLink('core/compileSharedJS.php?iframeDialogs=1&v='.$EDITOR->version)) ?>"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars($EDITOR->editorLink('core/compileCoreDialogJS.php?v='.$EDITOR->version)) ?>"></script>
<?php else : ?>
<script type="text/javascript" src="js/dialogEditorShared_src.js"></script>
<script type="text/javascript" src="core/js/wproPMenu_src.js"></script>
<script type="text/javascript" src="core/js/dialog_src.js"></script>
<?php if ($EDITOR->iframeDialogs) : ?>
<script src="core/js/dragiframe_src.js" type="text/javascript"></script>
<?php endif ?>
<?php endif ?>
<script type="text/javascript">/*<![CDATA[ */
dialog.width = <?php echo intval($DIALOG->width) ?>;
dialog.height = <?php echo intval($DIALOG->height) ?>;
dialog.URL = '<?php echo addslashes($EDITOR->editorURL) ?>';
dialog.route = '<?php echo addslashes($EDITOR->route) ?>';
dialog.browserType = '<?php echo addslashes($EDITOR->_browserType) ?>';
dialog.browserVersion = <?php echo preg_replace("/[^0-9.]/si", '', $EDITOR->_browserVersion) ?>;
dialog._setBrowserTypeStrings();
dialog.phpsid = '<?php echo $EDITOR->appendSid ? addslashes(strip_tags(defined('SID') ? SID : '')) : '' ?>';
dialog.fullURLs = <?php echo $EDITOR->fullURLs ? 'true' : 'false' ?>;
dialog.urlFormat = '<?php echo addslashes($EDITOR->urlFormat) ?>';
dialog.encodeURLs = <?php echo $EDITOR->encodeURLs ? 'true' : 'false' ?>;
dialog.baseURL = '<?php echo addslashes($EDITOR->baseURL) ?>';
dialog.themeURL = '<?php echo addslashes($themeURL) ?>';
dialog.appendToQueryStrings = '<?php echo addslashes($EDITOR->appendToQueryStrings) ?>';
dialog.sid = '<?php echo addslashes(isset($DIALOG->sess)?$DIALOG->sess->sessionName:'wprosid')?>=<?php echo addslashes(isset($DIALOG->sess)?$DIALOG->sess->sessionId:'')?>';
dialog.init();
/* ]]>*/</script>
<link rel="stylesheet" href="core/css/editor.css" type="text/css" />
<link rel="stylesheet" href="<?php echo htmlspecialchars($themeURL).'editor.css' ?>" type="text/css" />
<link rel="stylesheet" href="core/css/dialog.css" type="text/css" />
<link rel="stylesheet" href="<?php echo htmlspecialchars($themeURL).'dialog.css' ?>" type="text/css" />
<link rel="stylesheet" href="<?php echo htmlspecialchars($langURL).'dialog.css' ?>" type="text/css" />
<?php $headContent->display(); ?>
</head>
<body class="<?php echo htmlspecialchars($EDITOR->theme) ?><?php echo empty($classIsolator)?'':' '.htmlspecialchars($classIsolator) ?>">
<div class="wproDialog wproDialogEditorShared<?php echo empty($classIsolator)?'':' '.htmlspecialchars($classIsolator) ?>">

<!-- hidden menus -->
<div id="hiddenMenus" class="wproHiddenMenus wproEditor"></div>


<?php if ($DIALOG->formTags) : ?>
<form action="<?php echo htmlspecialchars($formAction) ?>" name="dialogForm" id="dialogForm" <?php if (!empty($formEnctype)) : ?>enctype="<?php echo htmlspecialchars($formEnctype) ?>" <?php endif ?>method="<?php echo htmlspecialchars($formMethod) ?>"<?php if ($formOnSubmit) : ?> onsubmit="<?php echo htmlspecialchars($formOnSubmit) ?>"<?php endif ?> onclick="dialog.PMenu.closePMenu()">
<?php endif ?>

<?php if ($EDITOR->iframeDialogs && !$DIALOG->chromeless) : ?>
<div class="titleBar" id="dialogTitleBar" unselectable="on">
<img class="closeButton" onclick="<?php echo htmlspecialchars($DIALOG->closeFunction) ?>" src="<?php echo htmlspecialchars($themeURL).'buttons/close.gif' ?>" alt="" title="" />
<?php echo $title ?>
</div>
<?php endif ?>

<div id="bodyHolder" class="bodyHolder">

<script type="text/javascript">
/*<![CDATA[ */
wproLoadMessage('dialog', null, null, '<?php echo addslashes($EDITOR->langEngine->get('core', 'pleaseWait'))?>', '<?php echo addslashes($themeURL).'misc/loader.gif' ?>', true);
/*dialog.showLoadMessage();*/
/* ]]>*/
</script>


<?php if (!empty($bodyInclude)) require($bodyInclude); ?>
<?php echo $bodyContent; ?>

</div>

<?php if (count($options) > 0 && !$DIALOG->chromeless) : ?>
<div class="buttonHolderContainer">
<div class="buttonHolder">
<?php foreach ($options as $k => $v) : ?>
<?php echo $this->HTMLInput($v); ?>
<?php endforeach ?>
</div>
</div>
<?php endif ?>

<?php if ($DIALOG->formTags) : ?>
</form>
<?php endif ?>

</div>
</body>
</html>
<script type="text/javascript">
/*<![CDATA[ */
wp_load();
/* ]]>*/
</script>
