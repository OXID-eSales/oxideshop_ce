<?php
if (!defined('IN_WPRO')) exit;
/**
 * wproTemplate class Copyright (c) 2004 Chris Bolt
 */
 
function wproTemplateJSSourceFilter($str) {
	return preg_replace('/(<script [^>]*src="[^"]*)_src\.js/', "$1.js", $str);
}
function wproTemplateCompressHTML($str) {

	//$str = preg_replace("/<\!--.*?-->/si", '', $str); // comment blocks
/*
	$str = preg_replace("/(<script[^>]*>[\s]*\/\*<\!\[CDATA\[[\s]*\*\/)(.*?)\/\*[\s]*(\]\]>|<\/XMLCDATA>)\*\/[\s]*<\/script>/smie", "str_replace('\\\"','\"','$1').wproTemplateCompressJS(str_replace('\\\"','\"','$2')).'/'.'* ]]>*'.'/</script>'", $str);
*/
	$str =preg_replace("/^\s*/smi", '', $str); // starting white space
	$str =preg_replace("/\s*$/smi", '', $str); // ending white space
	$str = preg_replace("/^[\s]+$/si", '', $str); // blank lines
	
	$str = preg_replace("/[\r\n]/si", '', $str); // line returns
	
	//echo '<pre>'.htmlspecialchars($str).'</pre>';
	
	return $str;
}
function wproTemplateCompressJS($str) {
		
	$str = preg_replace("/^\s*\/\//smi", '', $str); // comment lines
	$str = preg_replace("/\/\*(.*?)\*\//smi", '', $str); // comment blocks
	$str =preg_replace("/^\s+/smi", '', $str); // starting white space
	$str =preg_replace("/\s+$/smi", '', $str); // ending white space
	//$str =preg_replace("/([^:\-\\\])\/\/(.*?)$/smi", "$1", $str); // comments at end of lines
	//$str =preg_replace("/\s*(\(|\)|=|}|{|\+|\?|:)\s*/smi", "$1", $str); // extra white space.
	
	$str = preg_replace("/^[\s]+$/smi", '', $str); // blank lines
	$str = preg_replace("/[\r\n]/si", '', $str); // line returns
	
	$str =preg_replace("/([^;{])\s*$/smi", "$1;", $str); // add trailing ;

	$str = preg_replace("/^;/smi", '', $str); // crap
	$str = preg_replace("/;\s*;+/smi", ';', $str); // crap

	//echo '<pre>'.htmlspecialchars($str).'</pre>';
	return $str;
}
function wproTemplateVersionAppend($str) {
	global $EDITOR;
	$str = preg_replace('/(<script [^>]*src="[^"]*\.js)"/', "$1?v=".$EDITOR->version."\"", $str);
	$str = preg_replace('/(<link [^>]*href="[^"]*\.css)"/', "$1?v=".$EDITOR->version."\"", $str);
	return $str;
}
require_once(dirname(__FILE__).'/wproCore.class.php');
class wproTemplate extends wproCore {
		
	function wproTemplate () {
		$this->addOutputFilter('wproTemplateCompressHTML');
	}
	
	/* URL of theme folder */
	var $themeURL = '';
	var $editorURL = '';
	
	var $templates = array();
	
		/* general template API */
	var $vars = array(); /// Holds all the template variables
	var $refVars = array();
	
	var $stringMode = false; // used when output buffering cannot.
	
	var $path; /// Path to the templates
	/**
	 * Set a template variable.
	 *
	 * @param string $name name of the variable to set
	 * @param mixed $value the value of the variable
	 *
	 * @return void
	 */
	function assign($name, $value) {
		$this->vars[$name] = $value;
	}
	
	function assignByRef($name, &$value) {
		$this->refVars[$name] = &$value;
	}

	/**
	 * Set a bunch of variables at once using an associative array.
	 *
	 * @param array $vars array of vars to set
	 * @param bool $clear whether to completely overwrite the existing vars
	 *
	 * @return void
	 */
	function bulkAssign($vars, $clear = false) {
		if($clear) {
			$this->vars = $vars;
		} else {
			if( is_array($vars) ) {
				$this->vars = array_merge($this->vars, $vars);
			}
		}
	}
	/**
	 * Open, parse, and return the template file.
	 *
	 * @param string string the template file name
	 *
	 * @return string
	 */
	function fetch($file) {
	
		if (isset($this->templates[md5($file)])) {
			$file = $this->templates[md5($file)];
		}
		
		
		extract($this->vars);          // Extract the vars to local namespace
		
		// extract referenced vars
		foreach ($this->refVars as $k => $v) {
			$$k = $v;
		}
		
		if (!$this->stringMode) {
			ob_start();                    // Start output buffering
			//echo $this->path . $file;
			include($this->path . $file);  // Include the file
			$contents = ob_get_contents(); // Get the contents of the buffer
			ob_end_clean();                // End buffering and discard
		} else {
			// included file should set the contents variable.
			include($this->path . $file);  // Include the file
		}
		// strip line returns from content, added by Chris Bolt on 12 November 2004
		// output encoding, added by Chris Bolt on 20 July 2005
		//include_once(WPRO_DIR.'core/libs/wproCore.class.php');
		//$c = new wproCore();
		//$contents = $c->fixCharacters($contents);
		
		foreach ($this->outputFilters as $f) {
			if (is_array($f)) {
				if (is_object($f[0])) {
					if (method_exists($f[0], $f[1])) {
						$contents = $f[0]->$f[1]($contents);
					}
				}
			} else if (function_exists($f)) {
				$contents = $f($contents);
			}
		}
		
		return $contents;              // Return the contents
	}
	
	function display($file) {
		echo $this->fetch($file);
	}
	
	var $outputFilters = array();
	function addOutputFilter ($func) {
		array_push($this->outputFilters, $func);
	}
	
	function registerTemplate($name, $file) {
		$this->templates[md5($name)] = $file;
	}
	
	//function dateFormat() {
	
	//}
	
	function truncate($string, $length = 100, $etc = '...', $breakWords = false){
		if ($length == 0) {
			return '';
		}
		if (strlen($string) > $length) {
			$length -= strlen($etc);
			if (!$breakWords) {
				$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
			}
			return substr($string, 0, $length).$etc;
		} else {
			return $string;
		}
	}

	/* user interface elements */
	var $accessKeys = array();
	/* pass it a label string and it will return the first letter unassigned to an accesskey, and assign that letter to an access key */
	//function getAccessKey($label) {
		
	//}
	
	function underlineAccessKey ($label, $key) {
		return $label;//preg_replace("/(".quotemeta($key).")/smi", '<span style="text-decoration:underline">$1</span>', $label, 1);
	}
	
	function createHTMLLabel() {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_HTMLLabel.class.php');
		$o = new wproTemplate_HTMLLabel();
		$o->template = &$this;
		return $o;
	}
	/* displays a basic html input element */
	function HTMLLabel($label, $for='', $accessKey='') {
		$f = $this->createHTMLLabel();
		if (!empty($for)) $f->for = $for;
		if (!empty($label)) $f->label = $label;
		if (!empty($accessKey)) $f->accessKey = $accessKey;
		return $f->fetch();
	}
	function HTMLInput($attrs) {
		$f = $this->createHTMLInput();
		$f->attributes = $attrs;
		return $f->fetch();
	}
	function createHTMLInput () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_HTMLInput.class.php');
		$o = new wproTemplate_HTMLInput();
		$o->template = &$this;
		return $o;
	}
	function createUIURLChooser () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_UIURLChooser.class.php');
		$o = new wproTemplate_UIURLChooser();
		$o->template = &$this;
		return $o;
	}
	/*function HTMLSelect($attrs) {
		$f = $this->createHTMLInput();
		$f->attributes = $attrs;
		return $f->fetch();
		
		$f = $this->createHTMLInput();
		$f->attributes = $attrs;
		return $f->fetch();
	}*/
	function createHTMLSelect () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_HTMLSelect.class.php');
		$o = new wproTemplate_HTMLSelect();
		$o->template = &$this;
		return $o;
	}
	
	function createHTMLCheckboxes () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_HTMLCheckboxes.class.php');
		$o = new wproTemplate_HTMLCheckboxes();
		$o->template = &$this;
		return $o;
	}
	
	function createHTMLRadios () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_HTMLRadios.class.php');
		$o = new wproTemplate_HTMLRadios();
		$o->template = &$this;
		return $o;
	}
	
	function createHTMLSelectDate () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_HTMLSelectDate.class.php');
		$o = new wproTemplate_HTMLSelectDate();
		$o->template = &$this;
		return $o;
	}
	
	function createHTMLSelectTime () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_HTMLSelectTime.class.php');
		$o = new wproTemplate_HTMLSelectTime();
		$o->template = &$this;
		return $o;
	}
	
	function _escapeReturns($str) {
		return str_replace(array("\n","\r"), array('\n','\r'), $str);
	}
	
	function wproJSAttrSafe($str) {
		return $this->_escapeReturns(str_replace(array('&quot;', '"'), '\&quot;', $str));
	}
	function wproJSArray($inputStyles, $index='assoc') {
		$str = '';
		if (substr(strtolower($index), 0, 5) == 'assoc') {
			$str.= '{';
			$num = count($inputStyles)-1; $i=0; 
			foreach($inputStyles as $tag => $label) {
				$str.= "'".addslashes($tag)."':\"".$this->_escapeReturns(addslashes($label)).'"';
				if ($i < $num) {$str.= ',';$i++;}
			}
			$str.= '};';
		} else {
			$str.= '[';
			$num = count($inputStyles)-1; $i=0; 
			foreach($inputStyles as $label) {
				$str.= "'".$this->_escapeReturns(addslashes($label))."'";
				if ($i < $num) {$str.= ',';$i++;}
			}
			$str.= '];';
		}
		return $str;
	}
	
	function createUISelect () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_UISelect.class.php');
		static $uid = 0;
		$uid ++;
		$o = new wproTemplate_UISelect();
		$o->template = &$this;
		$o->uid = $uid;
		return $o;
	}
	
	function createUIDropDown () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_UIDropDown.class.php');
		static $uid = 0;
		$uid ++;
		$o = new wproTemplate_UIDropDown();
		$o->template = &$this;
		$o->uid = $uid;
		return $o;
	}
	
	function createUITabbed () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_UITabbed.class.php');
		static $uid = 0;
		$uid ++;
		$o = new wproTemplate_UITabbed();
		$o->template = &$this;
		$o->uid = $uid;
		return $o;
	}
	
	function createUI2ColTable () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_UI2ColTable.class.php');
		$o = new wproTemplate_UI2ColTable();
		$o->template = &$this;
		return $o;
	}
	
	/*function UI4ColTable ($column1, $column2) {
		if (is_array($column1) && is_array($column2)) {
			$tpl = & new wproTemplate();
			$tpl->bulkAssign(array(
				'column1Options' => $column1,
				'column2Options' => $column2,
			));
			return $tpl->fetch( WPRO_DIR.'core/tpl/UI4ColTable.tpl.php' );
		}	
	}*/
	
	/*function UIColorPicker ($name, $value='', $onChange='') {
		static $uid = 0;
		$uid ++;
		$tpl = & new wproTemplate();
		$tpl->bulkAssign(array(
			'name' => $name,
			'color' => $value,
			'UID' => 'cpUI'.$uid,
			'onChange' => $onChange,
		));
		return $tpl->fetch( WPRO_DIR.'core/tpl/UIColorPicker.tpl.php' );
	}*/
	function createUIColorPicker () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_UIColorPicker.class.php');
		static $uid = 0;
		$uid ++;
		$o = new wproTemplate_UIColorPicker();
		$o->template = &$this;
		$o->uid = $uid;
		return $o;
	}
	
	function createUIBordersAndShading () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_UIBordersAndShading.class.php');
		static $uid = 0;
		$uid ++;
		$o = new wproTemplate_UIBordersAndShading();
		$o->template = &$this;
		$o->uid = $uid;
		return $o;
	}
	
	/*function UIImageSelect ($name, $options = array(), $onChange='', $selected = null, $width='32', $height='32') {
		if (is_array($options)) {
			static $uid = 0;
			$uid ++;
			$tpl = & new wproTemplate();
			$tpl->bulkAssign(array(
				'name' => $name,
				'UID' => 'isUI'.$uid,
				'options' => $options,
				'onChange' => $onChange,
				'selected' => $selected,
				'width' => $width,
				'height' => $height,
			));
			return $tpl->fetch( WPRO_DIR.'core/tpl/UIImageSelect.tpl.php' );
		}
	}*/
	function createUIImageRadio () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_UIImageRadio.class.php');
		static $uid = 0;
		$uid ++;
		$o = new wproTemplate_UIImageRadio();
		$o->template = &$this;
		$o->uid = $uid;
		return $o;
	}
	
	function createUITree () {
		require_once(dirname(__FILE__).'/wproTemplate/wproTemplate_UITree.class.php');
		static $uid = 0;
		$uid ++;
		$o = new wproTemplate_UITree();
		$o->template = &$this;
		$o->uid = $uid;
		return $o;
	}
	
}

?>