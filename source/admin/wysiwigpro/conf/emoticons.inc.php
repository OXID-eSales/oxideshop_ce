<?php
if (!defined('IN_WPRO')) exit;
if (!isset($emoticonURL)) exit;
/* Info for the default emoticons in the $EMOTICON_DIR */
$emoticons = array(
	//      File name                           width         height          label
	$emoticonURL.'smiley1.gif' => array('width'=>'17','height'=>'17','label'=>'smile'),
	$emoticonURL.'smiley2.gif' => array('width'=>'17','height'=>'17','label'=>'wink'),
	$emoticonURL.'smiley3.gif' => array('width'=>'17','height'=>'17','label'=>'shocked'),
	$emoticonURL.'smiley4.gif' => array('width'=>'17','height'=>'17','label'=>'big_smile'),
	$emoticonURL.'smiley5.gif' => array('width'=>'17','height'=>'17','label'=>'confused'),
	$emoticonURL.'smiley6.gif' => array('width'=>'17','height'=>'17','label'=>'unhappy'),
	$emoticonURL.'smiley7.gif' => array('width'=>'17','height'=>'17','label'=>'angry'),
	$emoticonURL.'smiley8.gif' => array('width'=>'17','height'=>'17','label'=>'clown'),
	
	$emoticonURL.'smiley9.gif' => array('width'=>'17','height'=>'17','label'=>'embarrassed'),
	$emoticonURL.'smiley10.gif' => array('width'=>'17','height'=>'17','label'=>'star'),
	$emoticonURL.'smiley11.gif' => array('width'=>'17','height'=>'17','label'=>'dead'),
	$emoticonURL.'smiley12.gif' => array('width'=>'17','height'=>'17','label'=>'sleepy'),
	$emoticonURL.'smiley13.gif' => array('width'=>'17','height'=>'17','label'=>'disapprove'),
	$emoticonURL.'smiley14.gif' => array('width'=>'17','height'=>'17','label'=>'approve'),
	$emoticonURL.'smiley15.gif' => array('width'=>'17','height'=>'17','label'=>'evil_smile'),
	$emoticonURL.'smiley16.gif' => array('width'=>'17','height'=>'17','label'=>'cool'),
);
?>