<?php ob_start(); 
if (!defined('IN_WPRO')) define('IN_WPRO', true);
require_once(dirname(__FILE__).'/config.inc.php');
/* Routine for auto setting $EDITOR_URL, if this routine fails you will need to set it manually in the config file */
$EDITOR_URL = WPRO_EDITOR_URL;
$wp_dir_name = str_replace(array('\\','\\\\','//'),'/',WPRO_DIR);
if (empty($EDITOR_URL)) {	
	$wp_script_filename = isset($_SERVER["SCRIPT_FILENAME"]) ? str_replace(array('\\','\\\\','//'),'/',$_SERVER["SCRIPT_FILENAME"]) : str_replace(array('\\','\\\\','//'),'/',$_SERVER["PATH_TRANSLATED"]);
	$wp_script_name = $_SERVER["SCRIPT_NAME"];
	$EDITOR_URL = preg_replace( '/'.str_replace('/','\/',quotemeta(preg_replace( '/'.str_replace('/','\/',quotemeta($wp_script_name)).'/i', '', $wp_script_filename))).'/i' , '', $wp_dir_name);
	/*if (strtolower($EDITOR_URL) == strtolower($wp_dir_name)) {die('<div><b>WysiwygPro config error</b>: You MUST set the $EDITOR_URL variable in config.inc.php</div>');}*/
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>WysiwygPro Install Check</title>
<style type="text/css">
body {
	background-color: #E9EFF5;
}
body, td, li { 
	font-family: verdana;
	font-size: 11px;
}
h1 {
	font-size: 18px;
	padding-bottom: 0px;
	margin-bottom: 0px;
}
h2 {
	font-size: 14px;
	padding-bottom: 0px;
	margin-bottom: 0px;
}
p {
	margin-top: 0px;
	padding-top: 0px;
}
th {
	text-align: left;
	font-size: 11px;
}
table {
	background-color: #f7f7f7;
	border-top: 1px solid #cccccc;
	border-left: 1px solid #cccccc;
}
td, th {
	border-right: 1px solid #cccccc;
	border-bottom: 1px solid #cccccc;
}
th {
	background-color: #eeeeee;
}
p, table, h1, h2 {
	width: 90%;
}
</style>
</head>
<body>
<h1>WysiwygPro Install Check</h1>
<p>This script checks whether WysiwygPro will work correctly on this server. </p>
<form>
  <input type="button" name="button" value="Re-check" onclick="document.location.reload();" />
</form>
<hr />
<ol>
  <li>
    <h2>URL Check</h2>
    <p>WysiwygPro will now attempt to auto detect the URL of your wysiwygPro folder. 
      If the value displayed below does not match the actual URL of your wysiwygPro 
      folder then please open <strong>config.inc.php</strong> in a text editor 
      and set the WPRO_EDITOR_URL variable to the full URL of your WysiwygPro folder. 
      You will also need to do this if you intend to use WysiwygPro from a virtual 
      directory, Apache re-write rule or any other situation where the URL is 
      not directly related to the underlying filesystem. Note: you can also set 
      this at run-time using the editorURL property. The URL should be absolute from the root of your website, it will not include the domain name: </p>
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <th>wysiwygPro folder URL</th>
      </tr>
      <tr>
        <td><p>
            <?php 
if ($EDITOR_URL == $wp_dir_name) {
 	echo '<b><font color="red">WysiwygPro could not auto detect the URL of the WysiwygPro folder, please set the WPRO_EDITOR_URL constant in config.inc.php</font></b>';
} else {
	if (substr($EDITOR_URL, strlen($EDITOR_URL)-1) != '/') {$EDITOR_URL .= '/';}
	echo '<b><font color="green">'.$EDITOR_URL.'</font></b>';
}
?>
          </p></td>
      </tr>
    </table>
	<p>&nbsp;</p>
  </li>
  <li>
    <h2>PHP Environment Check</h2>
    <p>These settings are recommended for PHP in order to ensure full compatibility 
      with WysiwygPro. However only errors marked with a status of &quot;Critical&quot;, 
      will prevent WysiwygPro from operating. You will need to correct any critical 
      errors before using WysiwygPro on this server.</p>
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <th>Requirement</th>
        <th>Actual</th>
        <th>Status</th>
      </tr>
      <tr>
        <td>PHP version newer than 4.2.0</td>
        <?php $phpversion = phpversion(); ?>
        <td><?php echo $phpversion < '4.2' ? '<b><font color="red">'.$phpversion.'</font></b>' : '<b><font color="green">'.$phpversion.'</font></b>';?></td>
        <td><?php echo $phpversion < '4.2' ? '<font color="red"><b>Critical</b><br />You must upgrade PHP.</font>' : '<b><font color="green">OK</font></b>';?></td>
      </tr>
      <tr>
        <td>File uploads enabled.</td>
        <td><?php echo ini_get('file_uploads') ? '<b><font color="green">Yes</font></b>' : '<b><font color="red">No</font></b>';?></td>
        <td><?php echo ini_get('file_uploads') ? '<b><font color="green">OK</font></b>' : '<b>Not Critical</b><br />Users will not be able to upload files.';?></td>
      </tr>
      <tr>
        <td>XML Support.</td>
        <td><?php echo extension_loaded('xml') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';?></td>
        <td><?php echo extension_loaded('xml') ? '<b><font color="green">OK</font></b>' : '<b>Not Critical</b>';?></td>
      </tr>
      <tr>
        <td>GD Image Support.</td>
        <td><?php echo @function_exists('imagecreate') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';?></td>
        <td><?php echo @function_exists('imagecreate') ? '<b><font color="green">OK</font></b>' : '<b>Not Critical</b><br />The file manager will not show image thumbnails and image editing will be unavailable.';?></td>
      </tr>
      <tr>
        <td>cURL Support. </td>
        <td><?php echo defined('CURLOPT_PORT') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>';?></td>
        <td><?php echo defined('CURLOPT_PORT') ? '<b><font color="green">OK</font></b>' : '<b>Not Critical</b><br />The loadValueFromURL method will not be able to work over an SSL connection.';?></td>
      </tr>
    </table>
    <p>&nbsp;</p>
  </li>
  <li>
    <h2>Directory File Permissions</h2>
    <p>WysiwygPro's temp directory will need to be made writable if you intend 
      to set up spellchecking on your server using Aspell rather than the default 
      HTTP service or if you need to use WysiwygPro's built in session engine 
      rather than PHP's session engine. Most applications will not need to make 
      this directory writable.<br />
      Note: you can configure the location of the temp directory in <strong>config.inc.php</strong></p>
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <th>WysiwygPro Temp Directory</th>
        <th>Status</th>
      </tr>
      <tr>
        <td>Location not displayed for security reasons.
          <?php /*echo str_replace(array('\\','\\\\','//'),'/',$TEMP_DIR);*/ ?>
        </td>
        <td><?php 
		if (file_exists(WPRO_TEMP_DIR) && !is_file(WPRO_TEMP_DIR)) {
			echo is_writable(WPRO_TEMP_DIR) ? '<b><font color="green">Writable</font></b>' : '<b>Not Writable</b>';
		} else {
			echo '<b><font color="red">This directory does not exist. Please change the WPRO_TEMP_DIR variable in config.inc.php</font></b>';
		}
		?>
        </td>
      </tr>
    </table>
    <p>&nbsp;</p>
  </li>
  <li>
    <h2><strong>Attempting to Display the Editor...</strong></h2>
    <p>WysiwygPro will now attempt to display an instance of the editor below. 
      If the editor displays then you have successfully installed WysiwygPro. 
      If the editor does not display please check all the settings above and check 
      that you are using a supported web browser. For further information please 
      visit the online documentation at <a href="http://www.wysiwygpro.com/developer/"  target="wysiwygproDocs">http://www.wysiwygpro.com/developer/</a></p>
    <?php
require_once(dirname(__FILE__).'/wysiwygPro.class.php');
$editor = new wysiwygPro();
$editor->name = 'testEditor';
$editor->value = '<p><b><font face="Verdana" color="green" size="4">Congratulations you are now ready to start using WysiwygPro in your applications and websites!</font></b></p>
    <p><font face="Verdana" size="2">To learn how to incorporate WysiwygPro into your website or web application 
      please visit the online documentation at <a href="http://www.wysiwygpro.com/developer/" target="wysiwygproDocs">http://www.wysiwygpro.com/developer/</a></font></p>
';
$editor->width='';
$editor->display('');
?>
  </li>
</ol>
<hr />
<p>WysiwygPro version: <?php echo $editor->version; ?></p>
</body>
</html>
