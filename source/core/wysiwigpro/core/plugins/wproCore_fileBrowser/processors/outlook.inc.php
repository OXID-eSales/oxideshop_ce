<?php
if (!defined('IN_WPRO')) exit;
				
				$DIALOG->classIsolator = 'wproCore_fileBrowser_outlook';
				
				$chooser = isset($params['chooser']) ? true : false;
								
				// display the outlook bar.
				$DIALOG->title='Outlook';
				$DIALOG->headContent->add('<link rel="stylesheet" href="core/plugins/wproCore_fileBrowser/css/dialog.css" type="text/css" />');
				$this->addDialogJS();
				$DIALOG->headContent->add('<style type="text/css">body, .bodyHolder, #bodyholder {margin:0px;padding:0px;border:0px;}</style>');
				$DIALOG->bodyInclude = WPRO_DIR.'core/plugins/wproCore_fileBrowser/tpl/outlook.tpl.php';
				$DIALOG->chromeless = true;
				$DIALOG->embedded = true;
				
				$mode = preg_replace("/[^a-z]/si", '', isset($params['mode']) ? $params['mode'] : '');
				$current = preg_replace("/[^a-z0-9]/si", '', isset($params['current']) ? $params['current'] : '');
				
				$filesOnly = isset($params['filesOnly']) ? $params['filesOnly'] : false;
				$DIALOG->template->assign('filesOnly', $filesOnly);
				
				if (empty($mode)) {
					exit();
				}
				
				$this->instanceSupport($params);
				
				switch (strtolower($mode)) {
					case 'image' :
						$dirs = $EDITOR->getDirectories('image');
						break;
					case 'media' :
						$dirs = $EDITOR->getDirectories('media');
						break;
					case 'document' :
						$dirs = $EDITOR->getDirectories('document');
						break;
					case 'link' :
						$dirs = $EDITOR->getDirectories();
						break;
				}
				
				// remove dirs that don't match required permissions
				$requiredPermissions = preg_replace("/[^a-z,]/si", '', isset($params['requiredPermissions']) ? $params['requiredPermissions'] : '');
				if (!empty($requiredPermissions)) {
					$requiredPermissions = explode(',',$requiredPermissions);
					$dirs2 = array();
					foreach($dirs as $d) {
						$ok = false;
						$notOK = false;
						foreach($requiredPermissions as $p) {
							if (isset($d->$p)) {
								if ($d->$p) {
									$ok = true;
								} else {
									$notOK = true;
								}
							}
						}
						if ($ok && !$notOK) {
							array_push($dirs2, $d);
						}
					} 
					$dirs = $dirs2;
				}
				
				$DIALOG->template->assign('dirs', $dirs);
				$DIALOG->template->assign('mode', $mode);
				$DIALOG->assign('chooser', $chooser);
				
				// URL of dir to highlight
				if ($current=='image') {
					$dir = $EDITOR->getDirectories('image');
				} else if ($current=='media') {
					$dir = $EDITOR->getDirectories('media');
				} else if ($current=='document') {
					$dir = $EDITOR->getDirectories('document');
				}
				if (isset($dir)) {
					if (isset($dir[0])) {
						$current = $dir[0]->id;
					}
				}
				$DIALOG->template->assign('current', $current);
?>