<?php
if (!defined('IN_WPRO')) exit;
if (!defined('WPRO_DIR')) define('WPRO_DIR', dirname(dirname(dirname(__FILE__))).'/');
require_once(dirname(__FILE__).'/wproCore.class.php');
class wproSession extends wproCore {
	
	//var $filePerms = 0644;
	var $filePerms = 0644;
	var $dirPerms = 0771;
	var $sessionId = '';
	var $ipHash = '';
	var $data = array();
	var $doSave = false;
	var $file = '';
	var $fs = NULL;
	var $destroyed = false;
	var $prefix = 'wpro_';
	var $sessionName = 'wprosid';
	
	var $usePHPEngine = false;
	
	var $corePlugins = array(
			'wproCore_bookmark',
			'wproCore_codeCleanup',
			'wproCore_colorPicker',
			'wproCore_defaults',
			'wproCore_direction',
			'wproCore_emoticons',
			'wproCore_fileBrowser',
			'wproCore_find',
			'wproCore_fullWindow',
			//'wproCore_help',
			'wproCore_list',
			'wproCore_ruler',
			'wproCore_snippets',
			'wproCore_specialCharacters',
			'wproCore_spellchecker',
			'wproCore_styleWithCSS',
			'wproCore_table',
			'wproCore_tagEditor',
			'wproCore_v2Compat',
			'wproCore_zoom',
			);
	
	/*
	constructor
	*/
	function wproSession () {
		// load defaults
		require_once(WPRO_DIR.'config.inc.php');
		include_once(dirname(__FILE__).'/wproFilesystemBase.class.php');
		$this->fs = new wproFileSystemBase();
		if (strtoupper(WPRO_SESSION_ENGINE) == 'PHP') {
			$this->usePHPEngine = true;
		}
	}
	
	function getTempDir() {
		$return = '';
		if (!is_writable(WPRO_TEMP_DIR)) {
			if (isset($_ENV["TMP"])) {
				if (is_writable($_ENV["TMP"])) {
					$return = $_ENV["TMP"].'/';
				} else {
					exit('<strong>WysiwygPro config error</strong>: Please make the WPRO_TEMP_DIR writable.');
				}
			}
		} else {
			$return = WPRO_TEMP_DIR;
		}
		$return = $this->addTrailingSlash($return);
		return $return;
	}
	
	/* creates the IP hash 
	IP hash is used to reduce the possibility of session hijacking. Its not perfect, but better than nothing.
	*/
	function encodeIp() {
		// IP, browser and host must match throughout the session.
		/*if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  			$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		if (isset($_SERVER['HTTP_X_FORWARDED'])) {
  			$client_ip = $_SERVER['HTTP_X_FORWARDED'];
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
 			$client_ip = $_SERVER['HTTP_CLIENT_IP'];
		} else { */
			$client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		/*}*/
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] :'';
		
		// also due to browser JavaScript security the host must match or WP will fail with errors.
		// so we might as well ensure it in the session
		$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		
		// uncomment for less accurate ip address checking (Needed for AOL??)
		$test = explode('.', $client_ip);
		$client_ip = (isset($test[0])?$test[0]:'').'.'.(isset($test[1])?$test[1]:'').'.'.(isset($test[2])?$test[2]:''); 
		
		// preg replace characters because some CMS like ModX parse the _SERVER vars for template syntax
		$this->ipHash = md5(preg_replace("/[^A-Za-z0-9\.]/si", '', $client_ip.$user_agent.(isset($host)?$host:'')));
	}
	
	/*
	checks for a valid session, if one exists it retrieves all session data
	else it returns false
	*/
	function load() {
		
		if ($this->usePHPEngine) {	
			// start session
			require_once(WPRO_DIR.'conf/customSessHandlers.inc.php');
			if (!isset($_SESSION)) {
				session_start();
			}
		}
				
		// get IP hash
		$this->encodeIp();
		
		// find session id (if one exists)
		$this->sessionId = isset($_REQUEST[$this->sessionName]) ? $_REQUEST[$this->sessionName] : '';
						
		if (!preg_match('/^[A-Za-z0-9]+$/D', $this->sessionId) || strlen($this->sessionId) != 32) {
			return false;
		}
		
		// if sid is empty create new sid
		if (empty($this->sessionId)) {
			// create new sid, maybe not would be less secure?
			return false;
		} else {
			// check that sid has not expired
			if ($this->_expired() ) {
				return false;
			} elseif ($this->usePHPEngine) {
				//check for valid data in session and load...
				$key = md5($this->ipHash.$this->sessionId);
				if (isset($_SESSION[$this->prefix.'_wpxTime_'.$key])) {
					$_SESSION[$this->prefix.'_wpxTime_'.$key] = time();
					if (isset($_SESSION[$this->prefix.'_wpxPlugins_'.$key])) {
						$plugins = unserialize(base64_decode($_SESSION[$this->prefix.'_wpxPlugins_'.$key]));
						$plugins = array_merge($this->corePlugins, $plugins);
						foreach ($plugins as $plugin) {
							if (substr($plugin, 0, 9) == 'wproCore_') {
								$dir = WPRO_DIR.'core/plugins/';
							} else {
								$dir = WPRO_DIR.'plugins/';
							}
							$this->fs->includeFileOnce($plugin, $dir, '/plugin.php');
						}
					} else {
						return false;
					}
					if (isset($_SESSION[$this->prefix.'_wpxData_'.$key])) {
						$this->data = unserialize(base64_decode($_SESSION[$this->prefix.'_wpxData_'.$key]));
					} else {
						return false;
					}
					if (!WPRO_REDUCED_SESSION) {
						if (isset($_SESSION[$this->prefix.'_wpxEditor_'.$key])) {
							$editor = unserialize(base64_decode($_SESSION[$this->prefix.'_wpxEditor_'.$key]));
						} else {
							return false;
						}
					}
				} else {
					return false;
				}
			} else {
				// check for a valid session file and load...
				if (file_exists($this->file)) {
					// validate file
					$data = $this->fs->getContents($this->file);
					$match = "/^\<\?php\s+if \(!defined\('IN_WPRO'\)\) exit\(\)\;\n[$]wpxPlugins = \"[^\"]+\"\;\s+[$]wpxData = \"[^\"]+\"\;\s+[$]wpxEditor = \"[^\"]+\"\;\s+\?>$/Di";
					if (!preg_match($match, $data)) {
						return false;
					}
				} else {
					return false;
				}
				if (@include($this->file)) {
					touch($this->file);
					if (isset($wpxPlugins)) {
						$plugins = unserialize(base64_decode($wpxPlugins));
						$plugins = array_merge($this->corePlugins, $plugins);
						foreach ($plugins as $plugin) {
							if (substr($plugin, 0, 9) == 'wproCore_') {
								$dir = WPRO_DIR.'core/plugins/';
							} else {
								$dir = WPRO_DIR.'plugins/';
							}
							$this->fs->includeFileOnce($plugin, $dir, '/plugin.php');
						}
					} else {
						return false;
					}
					if (isset($wpxData)) {
						$this->data = unserialize(base64_decode($wpxData));
					} else {
						return false;
					} 
					if (!WPRO_REDUCED_SESSION) {
						if (isset($wpxEditor)) {
							$editor = unserialize(base64_decode($wpxEditor));
						} else {
							return false;
						}
					}
				} else {
					return false;
				}
			}
			if (WPRO_REDUCED_SESSION) {
				$editor = new wysiwygPro();
				$editor->_makeEditor();
			}
			$this->registerShutdown();
			return $editor;
		}
	}
	
	/* function for checking if a valid session exists */
	function exists() {
		
		if ($this->usePHPEngine) {	
			// start session
			require_once(WPRO_DIR.'conf/customSessHandlers.inc.php');
			if (!isset($_SESSION)) {
				session_start();
			}
		}
				
		// get IP hash
		$this->encodeIp();
		
		// find session id (if one exists)
		$this->sessionId = isset($_REQUEST[$this->sessionName]) ? $_REQUEST[$this->sessionName] : '';
						
		if (!preg_match('/^[A-Za-z0-9]+$/D', $this->sessionId) || strlen($this->sessionId) != 32) {
			return false;
		}
		
		if (empty($this->sessionId)) {
			return false;
		} else {
			// check that sid has not expired
			if ($this->_expired() ) {
				return false;
			} else {
				return true;
			}
		}
	}
	
	function registerShutdown() {
		//register_shutdown_function (array(&$this, 'shutdown'));	
	}	
	function shutdown(&$editor) {
		if ($this->doSave && !empty($this->data)) {
			$this->save($editor);
		}
	}
	
	// check if current session file has expired
	function _expired () {
		
		if ($this->usePHPEngine) {
			if (isset($_SESSION[$this->prefix.'_wpxTime_'.$this->sessionId])) {
				if ($_SESSION[$this->prefix.'_wpxTime_'.$this->sessionId]  >= (time () - WPRO_SESS_LIFETIME)) {
					return false;
				} else {
					return true;
				}
			} else {
				return false;
			}
		} else {
			
			$this->file = $this->fs->fileName($this->getTempDir().$this->prefix.md5($this->ipHash.$this->sessionId).'.php');
			
			if (!$this->fs->fileExists($this->file)) {
				return true;
			} else {
				if ($this->fs->fileModTime($this->file)  >= (time () - WPRO_SESS_LIFETIME)) {
					return false;
				} else {
					return true;
				}
			
			}
		}
	}
	
	// create session id
	function create ($id='') {
		if (empty($this->ipHash)) {
			$this->encodeIP();
		}
		if ($this->usePHPEngine) {
			if (!isset($_SESSION)) {
				@session_start();
			}
			// ensures editor id changes every hour and is unique to every PHP session.
			$const = '';
			@$d = date('Ymdh', time());
			if (!isset($_SESSION['wpro_sid_'.$d])) {
				$_SESSION['wpro_sid_'.$d] = uniqid(rand(), true);
			}
			$const = $_SESSION['wpro_sid_'.$d];
			if (!empty($id)) {
				$this->sessionId = md5($id.$const);
			} else if (!empty($this->sessionId)) {
				$this->sessionId = md5($this->sessionId.$const);
			} else {
				$this->sessionId = md5(uniqid(rand(), true));
			}
		} else {
			$this->sessionId = md5(uniqid(rand(), true));
			$file = $this->fs->fileName($this->getTempDir().$this->prefix.md5($this->ipHash.$this->sessionId).'.php');
			if (!file_exists($file)) {
				$this->file = $file;
			} else {
				$this->ipHash = '';
				$this->sessionId = '';
				$this->create();
			}
		}
	}
	
	/* 
	save $editor to session store 
	*/
	function save($editor) {
		
		if (defined('WPRO_SERIALIZED_EDITOR')) {
			$editor = unserialize(WPRO_SERIALIZED_EDITOR);
		}
		
		// save can only be done once!!
		$this->doSave = false;
		
		if ($this->destroyed) {
			return false;
		} else {
			
			if (!WPRO_REDUCED_SESSION) {
				
				$editor->unloadPlugin($editor->theme.'Theme');
				
				// reduce plugins
				foreach ($this->corePlugins as $k) {
				//echo $k;
					unset($editor->plugins[$k]);
				}
				
				/*if (fileperms($this->getTempDir()) != $this->dirPerms) {
					@chmod($this->getTempDir(), octdec($this->dirPerms));
				}*/
				$pn = array();
				foreach($editor->plugins as $k=>$v) {
					array_push($pn, $k);
				}
								
			} else {
				$pn = array();
				$editor = array();
			}
			
			$plugins = base64_encode(serialize($pn));
			$editorS = base64_encode(serialize($editor));
			$data = base64_encode(serialize($this->data));
			
			if ($this->usePHPEngine) {
				/*
				$_SESSION[$this->prefix.$this->sessionId]['wpxPlugins'] = $pn;
				$_SESSION[$this->prefix.$this->sessionId]['wpxData'] = $this->data;
				$_SESSION[$this->prefix.$this->sessionId]['wpxEditor'] = $editor;
				$_SESSION[$this->prefix.$this->sessionId]['wpxTime'] = time();
				*/
				$key = md5($this->ipHash.$this->sessionId);
				$_SESSION[$this->prefix.'_wpxPlugins_'.$key] = $plugins;
				$_SESSION[$this->prefix.'_wpxData_'.$key] = $data;
				$_SESSION[$this->prefix.'_wpxEditor_'.$key] = $editorS;
				$_SESSION[$this->prefix.'_wpxTime_'.$key] = time();
				$this->runGC();
				return true;
				//echo $this->prefix.$this->sessionId;
			} else {
			
				
				
// save data to file	
$code = '<'.'?php
if (!defined(\'IN_WPRO\')) exit();
$wpxPlugins = "'.$plugins.'";
$wpxData = "'.$data.'";
$wpxEditor = "'.$editorS.'";		
?'.'>';
				
				if ($this->fs->writeFile($this->file, $code)) {
					$this->fs->chmod($this->file, $this->filePerms);
					$this->runGC();
					return true;
				} else {
					return false;
				}
			}
		}
	}
	
	/* nonce tokens */
	function addNonce($token, $expires=0) {
		if (empty($expires)) $expires = time()+3600;
		if (empty($this->data['nonces'])) {
			$this->data['nonces'] = array();
		}
		$this->data['nonces'][md5($token)] = intval($expires);
		$this->nonceGC ();
		$this->doSave = true;
	}
	
	/* the nonce token can only be used once, once checked it gets DELETED!!! */
	function checkNonce($token) {
		if (empty($this->data['nonces'])) {
			return false;
		}
		if (!is_string($token)) {
			return false;
		}
		$key = md5($token);
		if (isset($this->data['nonces'][$key])) {
			if ($this->data['nonces'][$key] > time()) {
				unset($this->data['nonces'][$key]);
				$this->doSave = true;
				return true;
			} else {
				unset($this->data['nonces'][$key]);
				$this->doSave = true;
				return false;
			}
		}
		return false;
	}
	
	function nonceGC () {
		if (rand(0,5) == 1) {
			if (!empty($this->data['nonces'])) {
				$curTime = time();
				foreach ($this->data['nonces'] as $token => $time) {
					if ($curTime > $time) {
						unset($this->data['nonces'][$token]);
					}
				}
				$this->doSave = true;
			}
		}
	}
	
	
	function destroy() {
		
		if ($this->usePHPEngine) {
			//$key = md5($this->ipHash.$this->sessionId);
			//unset($_SESSION[$this->prefix.'_wpxTime_'.$key]);
			//unset($_SESSION[$this->prefix.'_wpxPlugins_'.$key]);
			//unset($_SESSION[$this->prefix.'_wpxData_'.$key]);
			//unset($_SESSION[$this->prefix.'_wpxEditor_'.$key]);
		} else {
			$this->fs->delete($this->file);
		}
		$this->destoryed = true;
	
	}
		
	function runGC () {
		// call sess gc once in every X editor requests.
		// we can call this fairly often since the editor probably isn't used in every php request.
		if (rand(0,5) == 1) {
			$this->gc();
		}
	}
	
	// deletes expired session files.
	function gc () {
		if ($this->usePHPEngine) {
			if (!empty($_SESSION)) {
				$curKey = '';
				if (!empty($this->sessionId) && !empty($this->ipHash)) {
					$curKey = md5($this->ipHash.$this->sessionId);
				}
				$toDelete = array();
				$sessions = array();
				foreach ($_SESSION as $k => $v) {
					if (substr($k, 0, strlen($this->prefix.'_wpxTime_')) == $this->prefix.'_wpxTime_') {
						$id = substr($k, strlen($this->prefix.'_wpxTime_'));
						$sessions[$id] = $_SESSION[$k];
						if ($_SESSION[$k] < (time () - WPRO_SESS_LIFETIME)) {
							array_push($toDelete, $id);
						}
					}
				}
				if (count($sessions) > WPRO_MAX_SESSIONS) {
					asort($sessions, SORT_NUMERIC);
					$i = 0;
					$n = count($sessions);
					foreach ($sessions as $id => $time) {
						array_push($toDelete, $id);
						if ($i>=$n-WPRO_MAX_SESSIONS) break;
						$i++;
					}			
				}				
				foreach ($toDelete as $k) {
					if ($k == $curKey) continue;
					unset($_SESSION[$this->prefix.'_wpxTime_'.$k]);
					unset($_SESSION[$this->prefix.'_wpxPlugins_'.$k]);
					unset($_SESSION[$this->prefix.'_wpxData_'.$k]);
					unset($_SESSION[$this->prefix.'_wpxEditor_'.$k]);
				}
			}
		} else {
			
			$directory = $this->getTempDir();
			$bhandle = opendir($directory);
			while (false !== ($file = readdir($bhandle))) {
				if (is_file($directory.$file)
				&& (substr($file, 0, strlen($this->prefix)) == $this->prefix) 
				&& ($this->fs->fileModTime($directory.$file)  < (time () - WPRO_SESS_LIFETIME))
				) {
					$this->fs->delete($directory.$file);
				}
			}
			closedir($bhandle);
		}
		
	}	

}
?>