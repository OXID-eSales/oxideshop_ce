<div class="messageExitHolder">
<div class="messageExitBorder outset">
<div class="messageExitTitleBar"><?php echo $title ?></div>
<div class="messageExitBody">
<div class="messageExitLeftCol">
<img src="<?php echo $themeURL.'/misc/'.$icon.'.gif' ?>" width="32" height="32" alt="" />
</div>
<div class="messageExitRightCol">
<?php echo $msg ?>
</div>
<div class="messageExitButtons">
<?php foreach ($options as $k => $v) : ?>
<?php echo $this->HTMLInput($v); ?>
<?php endforeach; $options = array(); ?>
</div>
</div>
</div>
</div>