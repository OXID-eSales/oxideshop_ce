<?php
if (!defined('IN_WPRO')) exit;

				$plugin = isset($params['plugin']) ? $params['plugin'] : '';
				$url = isset($params['url']) ? $params['url'] : '';
				if (empty($plugin) || empty($url)) {
					require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
					$msg = new wproMessageExit();
					$msg->msgCode = WPRO_CRITICAL;
					$msg->msg = 'Sorry not enough parameters.';
					$msg->alert();
					exit;
				}
				
				// load the plugin
				if ($this->loadEmbedPlugin($plugin) 
				&& is_object($this->embedPlugins[$plugin]) 
				&& method_exists($this->embedPlugins[$plugin], 'displayPreview')) {
				
					$DIALOG->baseTemplate = dirname(dirname(__FILE__)).'/tpl/mediapreview.tpl.php';
					$DIALOG->bodyContent = $this->embedPlugins[$plugin]->displayPreview($url);
					
				} else {
					require_once(WPRO_DIR.'core/libs/wproMessageExit.class.php');
					$msg = new wproMessageExit();
					$msg->msgCode = WPRO_CRITICAL;
					$msg->msg = 'The plugin could not be loaded.';
					$msg->alert();
					exit;
				}

?>