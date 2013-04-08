<?php
if (!defined('IN_WPRO')) exit;
/* provides links to tag help info in W3C */
$tagInfo = array (
	// 'tag name'          			 'external link for further info'              'can this tag have child nodes?'
	'A' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/links.html#edef-A', 'canHaveChildren' => true),
	'ABBR' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-ABBR', 'canHaveChildren' => true),
	'ACRONYM' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-ACRONYM', 'canHaveChildren' => true),
	'ADDRESS' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-ADDRESS', 'canHaveChildren' => true),
	'APPLET' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/objects.html#edef-APPLET', 'canHaveChildren' => true),
	'AREA' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/objects.html#edef-AREA'),
	'B' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/graphics.html#edef-B', 'canHaveChildren' => true),
	'BASE' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/links.html#edef-BASE'),
	'BASEFONT' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/graphics.html#edef-BASEFONT'),
	'BDO' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/dirlang.html#edef-BDO'),
	'BIG' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/graphics.html#edef-BIG', 'canHaveChildren' => true),
	'BLOCKQUOTE' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-BLOCKQUOTE', 'canHaveChildren' => true),
	'BODY' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-BODY', 'canHaveChildren' => true),
	'BR' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-BR'),
	'BUTTON' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/forms.html#edef-BUTTON', 'canHaveChildren' => true),
	'CAPTION' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/tables.html#edef-CAPTION', 'canHaveChildren' => true),
	'CENTER' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/graphics.html#edef-CENTER', 'canHaveChildren' => true),
	'CITE' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-CITE', 'canHaveChildren' => true),
	'CODE' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-CODE', 'canHaveChildren' => true),
	'COL' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/tables.html#edef-COL', 'canHaveChildren' => true),
	'COLGROUP' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/tables.html#edef-COLGROUP', 'canHaveChildren' => true),
	'DD' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/lists.html#edef-DD', 'canHaveChildren' => true),
	'DEL' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-del', 'canHaveChildren' => true),
	'DFN' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-DFN', 'canHaveChildren' => true),
	'DIR' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/lists.html#edef-DIR', 'canHaveChildren' => true),
	'DIV' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-DIV', 'canHaveChildren' => true),
	'DL' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/lists.html#edef-DL', 'canHaveChildren' => true),
	'DT' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/lists.html#edef-DT', 'canHaveChildren' => true),
	'EM' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-EM', 'canHaveChildren' => true),
	'EMBED' => array ('canHaveChildren' => true),
	'FIELDSET' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/forms.html#edef-FIELDSET', 'canHaveChildren' => true),
	'FONT' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/graphics.html#edef-FONT', 'canHaveChildren' => true),
	'FORM' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/forms.html#edef-FORM', 'canHaveChildren' => true),
	'FRAME' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/frames.html#edef-FRAME'),
	'FRAMESET' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/frames.html#edef-FRAMESET', 'canHaveChildren' => true),
	'H1' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-H1', 'canHaveChildren' => true),
	'H2' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-H2', 'canHaveChildren' => true),
	'H3' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-H3', 'canHaveChildren' => true),
	'H4' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-H4', 'canHaveChildren' => true),
	'H5' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-H5', 'canHaveChildren' => true),
	'H6' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-H6', 'canHaveChildren' => true),
	'HEAD' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-HEAD', 'canHaveChildren' => true),
	'HR' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/graphics.html#edef-HR'),
	'HTML' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-HTML'),
	'I' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/graphics.html#edef-I', 'canHaveChildren' => true),
	'IFRAME' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/frames.html#edef-IFRAME'),
	'IMG' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/objects.html#edef-IMG'),
	'INPUT' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/forms.html#edef-INPUT'),
	'INS' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-ins', 'canHaveChildren' => true),
	'ISINDEX' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/forms.html#edef-ISINDEX'),
	'KBD' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-KBD', 'canHaveChildren' => true),
	'LABEL' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/forms.html#edef-LABEL', 'canHaveChildren' => true),
	'LEGEND' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/forms.html#edef-LEGEND', 'canHaveChildren' => true),
	'LI' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/lists.html#edef-LI', 'canHaveChildren' => true),
	'LINK' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/links.html#edef-LINK', 'canHaveChildren' => true),
	'MAP' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/objects.html#edef-MAP'),
	'MENU' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/lists.html#edef-MENU', 'canHaveChildren' => true),
	'META' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-META'),
	'NOFRAMES' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/frames.html#edef-NOFRAMES', 'canHaveChildren' => true),
	'NOSCRIPT' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/scripts.html#edef-NOSCRIPT', 'canHaveChildren' => true),
	'OBJECT' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/objects.html#edef-OBJECT', 'canHaveChildren' => true),
	'OL' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/lists.html#edef-OL', 'canHaveChildren' => true),
	'OPTGROUP' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/forms.html#edef-OPTGROUP', 'canHaveChildren' => true),
	'OPTION' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/forms.html#edef-OPTION', 'canHaveChildren' => true),
	'P' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-P', 'canHaveChildren' => true),
	'PARAM' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/objects.html#edef-PARAM'),
	'PRE' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-PRE', 'canHaveChildren' => true),
	'Q' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-Q', 'canHaveChildren' => true),
	'S' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/graphics.html#edef-S', 'canHaveChildren' => true),
	'SAMP' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-SAMP', 'canHaveChildren' => true),
	'SCRIPT' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/scripts.html#edef-SCRIPT', 'canHaveChildren' => true),
	'SELECT' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/forms.html#edef-SELECT', 'canHaveChildren' => true),
	'SMALL' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/graphics.html#edef-SMALL', 'canHaveChildren' => true),
	'SPAN' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-SPAN', 'canHaveChildren' => true),
	'STRIKE' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/graphics.html#edef-STRIKE', 'canHaveChildren' => true),
	'STRONG' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-STRONG', 'canHaveChildren' => true),
	'STYLE' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/styles.html#edef-STYLE', 'canHaveChildren' => true),
	'SUB' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-SUB', 'canHaveChildren' => true),
	'SUP' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-SUP', 'canHaveChildren' => true),
	'TABLE' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/tables.html#edef-TABLE', 'canHaveChildren' => true),
	'TBODY' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/tables.html#edef-TBODY', 'canHaveChildren' => true),
	'TD' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/tables.html#edef-TD', 'canHaveChildren' => true),
	'TEXTAREA' => array ('helpLink' => 'http://www.w3.org/TR/html4/interact/forms.html#edef-TEXTAREA', 'canHaveChildren' => true),
	'TFOOT' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/tables.html#edef-TFOOT', 'canHaveChildren' => true),
	'TH' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/tables.html#edef-TH', 'canHaveChildren' => true),
	'THEAD' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/tables.html#edef-THEAD', 'canHaveChildren' => true),
	'TITLE' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/global.html#edef-TITLE', 'canHaveChildren' => true),
	'TR' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/tables.html#edef-TR', 'canHaveChildren' => true),
	'TT' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/graphics.html#edef-TT', 'canHaveChildren' => true),
	'U' => array ('helpLink' => 'http://www.w3.org/TR/html4/present/graphics.html#edef-U', 'canHaveChildren' => true),
	'UL' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/lists.html#edef-UL', 'canHaveChildren' => true),
	'VAR' => array ('helpLink' => 'http://www.w3.org/TR/html4/struct/text.html#edef-VAR', 'canHaveChildren' => true),
);

// HTML attributes as defined by the W3: http://www.w3.org/TR/html4/index/attributes.html

// DTD key:
// L = only in loose DTD, won't be used if 'STRICT' DTD is enabled

$attributes = array (
	
	'General' => array (
		
		
		'Type:  ' => array(
			'name' => 'type',
			'values' => array('text'=>'text','password'=>'password','checkbox'=>'checkbox','radio'=>'radio','submit'=>'submit','reset'=>'reset','file'=>'file','hidden'=>'hidden','image'=>'image','button'=>'button'),
			'tags' => array('INPUT')),
		'Type:   ' => array(
			'name' => 'type',
			'values' => array('submit'=>'submit','reset'=>'reset','button'=>'button'),
			'tags' => array('BUTTON'),
			),
		'Name:  ' => array(
			'name' => 'name',
			'tags' => array('FORM','INPUT'),
			'type' => 'text',
			),
		'Value:  ' => array(
			'name' => 'value',
			'tags' => array('INPUT'),
			'type' => 'text',
			),
		
		
		// link and source attributes
		'Src:' => array(
			'name' => 'src',
			'tags' => array('SCRIPT','INPUT','FRAME','IFRAME'),
			'type' => 'file',
			),
		'Src: ' => array(
			'name' => 'src',
			'tags' => array('IMG'),
			'type' => 'image',
			),
					
		'Action:' => array(
			'name' => 'action',
			'tags' => array('FORM'),
			'type' => 'text',
			),
		'Href:' => array(
			'name' => 'href',
			'tags' => array('A','AREA','LINK','BASE'),
			'type' => 'link',
			),
		'HerfLang:' => array(
			'name' => 'hrefLang',
			'tags' => array('A','LINK'),
			'type' => 'text',
			),
			
		'ClassId:' => array(
			'name' => 'classId',
			'tags' => array('OBJECT'),
			'type' => 'file',
			),
		'CodeBase:' => array(
			'name' => 'codeBase',
			'tags' => array('APPLET'),
			'type' => 'text',
			'dtd' => 'L'),
		'CodeBase: ' => array(
			'name' => 'codeBase',
			'tags' => array('OBJECT'),
			'type' => 'file',),
		'Code:' => array(
			'name' => 'code',
			'tags' => array('APPLET'),
			'type' => 'text',
			'dtd' => 'L'),
		
		'CodeType:' => array(
			'name' => 'codeType',
			'tags' => array('OBJECT'),
			'type' => 'text',
			),	
			
		
		'Data:' => array(
			'name' => 'data',
			'tags' => array('OBJECT'),
			'type' => 'media',
			),
			
		'Type: ' => array(
			'name' => 'type',
			'tags' => array('A','LINK','OBJECT','PARAM','SCRIPT','STYLE'),
			'type' => 'text',
			),
			
		'Language:' => array(
			'name' => 'language',
			'tags' => array('SCRIPT'),
			'type' => 'text',
			'dtd' => 'L'),
			
		// alternate sources
		'Archive: ' => array(
			'name' => 'archive',
			'tags' => array('OBJECT'),
			'type' => 'text',
			),
		'Archive:' => array(
			'name' => 'archive',
			'tags' => array('APPLET'),
			'type' => 'text',
			'dtd' => 'L'),
			
			
			
		// data types
		'EncType:' => array(
			'name' => 'encType',
			'tags' => array('FORM'),
			'type' => 'text',
			),
		'Accept-Charset:' => array(
			'name' => 'accept-charset',
			'tags' => array('FORM'),
			'type' => 'text',
			),
		'Accept:' => array(
			'name' => 'accept',
			'tags' => array('FORM', 'INPUT'),
			'type' => 'text',
			),
		'Charset:' => array(
			'name' => 'charset',
			'tags' => array('A','LINK','SCRIPT'),
			'type' => 'text',
			),
		'Type:' => array(
			'name' => 'type',
			'values' => array('1' => '1', 'a'=>'a', 'A'=>'A', 'i'=>'i', 'I'=>'I'),
			'tags' => array('LI','OL','UL'),
			'dtd' => 'L'),
		
		'Media:' => array(
			'name' => 'media',
			'tags' => array('STYLE','LINK'),
			'type' => 'text',
			),
			
		'Target:' => array(
			'name' => 'target',
			'tags' => array('A', 'AREA', 'BASE', 'FORM', 'LINK'),
			'type' => 'text',
			'dtd' => 'L'
			),
			
		'HTTP-Equiv:' => array(
			'name' => 'http-equiv',
			'tags' => array('META'),
			'type' => 'text',
			),
		
		'Method:' => array(
			'name' => 'method',
			'values' => array('GET' => 'GET', 'POST'=>'POST'),
			'tags' => array('FORM'),
			'type' => 'text',
			),
		
		
		// data values
		'Value: ' => array(
			'name' => 'value',
			'tags' => array('OPTION','PARAM','BUTTON'),
			'type' => 'text',
			),
		'Value:' => array(
			'name' => 'value',
			'tags' => array('LI'),
			'type' => 'numeral',
			'dtd' => 'L'),	
			
		'ValueType:' => array(
			'name' => 'valueType',
			'values' => array('DATA' => 'DATA','REF' => 'REF','OBJECT' => 'OBJECT'),
			'tags' => array('PARAM')),	
		'Content:' => array(
			'name' => 'content',
			'tags' => array('META'),
			'type' => 'text',
			),
			
		
		// identification	
		/*'Name: ' => array(
			'name' => 'name',
			'tags' => array('APPLET'),
			'type' => 'text',
			'dtd' => 'L'),
		'Name:' => array(
			'name' => 'name',
			'tags' => array('BUTTON', 'TEXTAREA','SELECT','FRAME','IFRAME','IMG','A','OBJECT','MAP','PARAM','META'),
			'type' => 'text',
			),*/
			
		/*'Name:' => array(
			'name' => 'name',
			'tags' => array('A', 'APPLET','FORM','FRAME','IFRAME','IMG','MAP'),
			'dtd' => 'XL'),*/
			
		'For:' => array(
			'name' => 'for',
			'tags' => array('LABEL'),
			'type' => 'text',
			),
			
		// some misc
		'Declare:' => array(
			'name' => 'declare',
			'type' => 'boolean',
			'tags' => array('OBJECT')),
			
			
		// appearance ?		
		
		'Size:' => array(
			'name' => 'size',
			'tags' => array('INPUT','SELECT'),
			'type' => 'numeral',
			),
		
			
		
		// alternat text etc	
		/*'Abbr:' => array(
			'name' => 'abbr',
			'tags' => array('TD','TH'),
			'type' => 'text'
			),*/	
		'Alt: ' => array(
			'name' => 'alt',
			'tags' => array('APPLET'),
			'type' => 'text',
			'dtd' => 'L'),
		'Alt:' => array(
			'name' => 'alt',
			'tags' => array('AREA','IMG','INPUT'),
			'type' => 'text',
			),
		'Label:' => array(
			'name' => 'label',
			'tags' => array('OPTION','OPTGROUP'),
			'type' => 'text',
			),
		

		/*'Axis:' => array(
			'name' => 'axis',
			'tags' => array('TD','TH'),
			'type' => 'text',
			),*/
		
		
		// form input etc
		'Checked:' => array(
			'name' => 'checked',
			'type' => 'boolean',
			'tags' => array('INPUT')),
		'Selected:' => array(
			'name' => 'selected',
			'type' => 'boolean',
			'tags' => array('OPTION')),
		'MaxLength:' => array(
			'name' => 'maxLength',
			'tags' => array('INPUT'),
			'type' => 'numeral',
			),
		'Multiple:' => array(
			'name' => 'multiple',
			'type' => 'boolean',
			'tags' => array('SELECT')),
		'ReadOnly:' => array(
			'name' => 'readOnly',
			'type' => 'boolean',
			'tags' => array('TEXTAREA','INPUT')),
		'Disabled:' => array(
			'name' => 'disabled',
			'type' => 'boolean',
			'tags' => array('TEXTAREA','INPUT')),
		
		
		
		// image maps
		'UseMap:' => array(
			'name' => 'useMap',
			'tags' => array('IMG', 'INPUT', 'OBJECT'),
			'type' => 'text',
			),
		/*'Ismap:' => array(
			'name' => 'isMap',
			'type' => 'boolean',
			'tags' => array('IMG','INPUT')),*/
		'Shape:' => array(
			'name' => 'shape',
			'values' => array('rect' => 'rect','circle' => 'circle','poly' => 'poly'),
			'tags' => array('A','AREA')),
		'Coords:' => array(
			'name' => 'coords',
			'tags' => array('AREA'),
			'type' => 'text',
			),
		
		
		// reviewing
		'Cite:' => array(
			'name' => 'cite',
			'tags' => array('BLOCKQUOTE','Q','DEL','INS'),
			'type' => 'text',
			),
		'Datetime:' => array(
			'name' => 'dateTime',
			'tags' => array('DEL','INS'),
			'type' => 'text',
			),
		
		
		// misc
		'Compact:' => array(
			'name' => 'compact',
			'tags' => array('DIR','DL','MENU','OL','UL'),
			'type' => 'boolean',
			'dtd' => 'L'),
			
		
			
		'Defer:' => array(
			'name' => 'defer',
			'tags' => array('SCRIPT'),
			'type' => 'text',
			),
		

		
		
		/*'Headers:' => array(
			'name' => 'headers',
			'tags' => array('TD','TH'),
			'type' => 'text',
			),*/
		
		'NoHref:' => array(
			'name' => 'noHref',
			'type' => 'boolean',
			'tags' => array('AREA')),
		
		'Object:' => array(
			'name' => 'object',
			'tags' => array('APPLET'),
			'type' => 'text',
			'dtd' => 'L'),
		'Profile:' => array(
			'name' => 'profile',
			'tags' => array('HEAD'),
			'type' => 'text',
			),
		'Prompt:' => array(
			'name' => 'prompt',
			'tags' => array('ISINDEX'),
			'type' => 'text',
			'dtd' => 'L'),
		
		'Rel:' => array(
			'name' => 'rel',
			'tags' => array('A','LINK'),
			'type' => 'text',
			),
		'Rev:' => array(
			'name' => 'rev',
			'tags' => array('A','LINK'),
			'type' => 'text',
			),
			
		'Scheme:' => array(
			'name' => 'scheme',
			'tags' => array('META'),
			'type' => 'text',
			),
		'Scope:' => array(
			'name' => 'scope',
			'tags' => array('TD','TH'),
			'values' => array('col' => 'col','colgroup' => 'colgroup','row' => 'row', 'rowgroup' => 'rowgroup'),
			),

		'Span:' => array(
			'name' => 'span',
			'tags' => array('COL','COLGROUP'),
			'type' => 'text',
			),
			
		
		'Start:' => array(
			'name' => 'start',
			'tags' => array('OL'),
			'type' => 'text',
			'dtd' => 'L'),
		'Start:' => array(
			'name' => 'start',
			'tags' => array('OL'),
			'type' => 'text',
			),
				
		'Version:' => array(
			'name' => 'version',
			'tags' => array('HTML'),
			'type' => 'text',
			'dtd' => 'L'),
		
			
	),
	
	'Appearance' => array (
		// width and height attributes
		'Width: ' => array(
			'name' => 'width',
			'tags' => array('IFRAME', 'IMG','OBJECT','TABLE','COL','COLGROUP'),
			'type' => 'numeral',
			),
		'Width:' => array(
			'name' => 'width',
			'tags' => array('HR','TD','TH','APPLET','PRE'),
			'type' => 'numeral',
			'dtd' => 'L'),
		'Height: ' => array(
			'name' => 'height',
			'tags' => array('IFRAME','TD','TH','APPLET'),
			'type' => 'numeral',
			'dtd' => 'L'),
		'Height:' => array(
			'name' => 'height',
			'tags' => array('IMG','OBJECT'),
			'type' => 'numeral',
			),
		
		'Size: ' => array(
			'name' => 'size',
			'tags' => array('HR'),
			'type' => 'numeral',
			'dtd' => 'L'),
		'Size:  ' => array(
			'name' => 'size',
			'tags' => array('FONT','BASEFONT'),
			'values' => array('1' => '1','2' => '2','3' => '3','4' => '4','5' => '5','6' => '6','7' => '7'),
			'dtd' => 'L'),
			
		'Cols:' => array(
			'name' => 'cols',
			'tags' => array('TEXTAREA','FRAMESET'),
			'type' => 'numeral',
			),
		'Rows:' => array(
			'name' => 'rows',
			'tags' => array('FRAMESET','TEXTAREA'),
			'type' => 'numeral',
			),
			
			
		// borders spacing and padding
		'Border: ' => array(
			'name' => 'border',
			'tags' => array('IMG','OBJECT'),
			'type' => 'numeral',
			'dtd' => 'L'),
		'Border:' => array(
			'name' => 'border',
			'tags' => array('TABLE'),
			'type' => 'numeral',
			),
		'Frame:' => array(
			'name' => 'frame',
			'tags' => array('TABLE'),
			'values' => array('void'=>'void','above'=>'above','below'=>'below','hsides'=>'hsides','lhs'=>'lhs','rhs'=>'rhs','vsides'=>'vsides','box'=>'box','border'=>'border'),
			),
		'Rules:' => array(
			'name' => 'rules',
			'tags' => array('TABLE'),
			'values' => array('none'=>'none','groups'=>'groups','rows'=>'rows','cols'=>'cols','all'=>'all'),
			),
		'FrameBorder:' => array(
			'name' => 'frameBorder',
			'tags' => array('FRAME','IFRAME'),
			'type' => 'numeral',
			),
		'NoShade:' => array(
			'name' => 'noShade',
			'type' => 'boolean',
			'tags' => array('HR'),
			'dtd' => 'L'),
			
		'CellPadding:' => array(
			'name' => 'cellPadding',
			'tags' => array('TABLE'),
			'type' => 'numeral',
			),
		'CellSpacing:' => array(
			'name' => 'cellsPacing',
			'tags' => array('TABLE'),
			'type' => 'numeral',
			),
		
		'MarginHeight:' => array(
			'name' => 'marginHeight',
			'tags' => array('FRAME','IFRAME'),
			'type' => 'numeral',
			),
		'MarginWidth:' => array(
			'name' => 'marginWidth',
			'tags' => array('FRAME','IFRAME'),
			'type' => 'numeral',
			),
			
		'HSpace:' => array(
			'name' => 'hSpace',
			'tags' => array('APPLET','IMG','OBJECT'),
			'type' => 'numeral',
			'dtd' => 'L'),
		'VSpace:' => array(
			'name' => 'vSpace',
			'tags' => array('APPLET', 'IMG', 'OBJECT'),
			'type' => 'numeral',
			'dtd' => 'L'),
			
		// alignment
		'Align: ' => array(
			'name' => 'align',
			'values' => array('top'=>'top','bottom'=>'bottom','left'=>'left','right'=>'right'),
			'tags' => array('CAPTION'),
			'dtd' => 'L'),
		'Align:  ' => array(
			'name' => 'align',
			'values' => array('bottom'=>'bottom','middle'=>'middle','left'=>'left','right'=>'right'),
			'tags' => array('APPLET','IFRAME','IMG','INPUT','OBJECT'),
			'dtd' => 'L'),
		'Align:   ' => array(
			'name' => 'align',
			'values' => array('top'=>'top','bottom'=>'bottom','left'=>'left','right'=>'right'),
			'tags' => array('LEGEND'),
			'dtd' => 'L'),
		'Align:    ' => array(
			'name' => 'align',
			'values' => array('left'=>'left','center'=>'center','right'=>'right'),
			'tags' => array('TABLE'),
			'dtd' => 'L'),
		'Align:     ' => array(
			'name' => 'align',
			'values' => array('left'=>'left','center'=>'center','right'=>'right'),
			'tags' => array('HR'),
			'dtd' => 'L'),
		'Align:      ' => array(
			'name' => 'align',
			'values' => array('left'=>'left','center'=>'center','right'=>'right','justify'=>'justify'),
			'tags' => array('DIV','H1','H2','H3','H4','H5','H6','P'),
			'dtd' => 'L'),
		'Align:       ' => array(
			'name' => 'align',
			'values' => array('left'=>'left','center'=>'center','right'=>'right','justify'=>'justify',/*'char'=>'char'*/),
			'tags' => array('COL','COLGROUP','TBODY','TD','TFOOT','TH','THEAD','TR')),
		
		'VAlign:' => array(
			'name' => 'valign',
			'tags' => array('COL', 'COLGROUP', 'TBODY', 'TD', 'TFOOT', 'TH', 'THEAD', 'TR'),
			'values' => array('top'=>'top','middle'=>'middle','bottom'=>'bottom'),
			),

		
		/*'Char:' => array(
			'name' => 'char',
			'tags' => array('COL','COLGROUP','TBODY','TD','TFOOT','TH','TR'),
			'type' => 'text',
			),
		'Charoff:' => array(
			'name' => 'charOff',
			'tags' => array('COL','COLGROUP','TBODY','TD','TFOOT','TH','TR'),
			'type' => 'text',
			),*/
			
		// other layout tags
		'NoResize:' => array(
			'name' => 'noResize',
			'type' => 'boolean',
			'tags' => array('FRAME')),
		'ColSpan:' => array(
			'name' => 'colSpan',
			'tags' => array('TD','TH'),
			'type' => 'numeral',
			),
		'RowSpan:' => array(
			'name' => 'rowSpan',
			'tags' => array('TD','TH'),
			'type' => 'numeral',
			),
		'NoWrap:' => array(
			'name' => 'noWrap',
			'type' => 'boolean',
			'tags' => array('TD', 'TH'),
			'dtd' => 'L'),
		'Clear:' => array(
			'name' => 'clear',
			'values' => array('left'=>'left','all'=>'all','right'=>'right'),
			'tags' => array('BR'),
			'dtd' => 'L'),
		'Scrolling:' => array(
			'name' => 'scrolling',
			'values' => array('yes' => 'yes','no' => 'no','auto' => 'auto'),
			'tags' => array('FRAME','IFRAME')),
		
		// backgrounds
		'Background:' => array(
			'name' => 'background',
			'tags' => array('BODY','TD','TABLE'),
			'type' => 'image',
			'dtd' => 'L'),
		'BGColor:' => array(
			'name' => 'bgColor',
			'tags' => array('TABLE','TR','TD','TH','BODY'),
			'type' => 'color',
			'dtd' => 'L'),
			
		// fonts
		'Face:' => array(
			'name' => 'face',
			'tags' => array('FONT','BASEFONT'),
			'type' => 'text',
			'dtd' => 'L'),
		
		// link and text  colors
		'Color:' => array(
			'name' => 'color',
			'tags' => array('BASEFONT','FONT'),
			'type' => 'color',
			'dtd' => 'L'),
		'Text:' => array(
			'name' => 'text',
			'tags' => array('BODY'),
			'type' => 'color',
			'dtd' => 'L'),
		'Link:' => array(
			'name' => 'link',
			'tags' => array('BODY'),
			'type' => 'color',
			'dtd' => 'L'),
		'ALink:' => array(
			'name' => 'aLink',
			'tags' => array('BODY'),
			'type' => 'color',
			'dtd' => 'L'),
		'VLink:' => array(
			'name' => 'vLink',
			'tags' => array('BODY'),
			'type' => 'color',
			'dtd' => 'L'),
			
		// object tag
		'Standby:' => array(
			'name' => 'standby',
			'tags' => array('OBJECT'),
			'type' => 'text',
			),
		
	),

	'CSS/Accessibility' => array (
		
		
		'Name: ' => array(
			'name' => 'name',
			'tags' => array('APPLET'),
			'type' => 'text',
			'dtd' => 'L'),
		'Name:' => array(
			'name' => 'name',
			'tags' => array('BUTTON', 'TEXTAREA','SELECT','FRAME','IFRAME','IMG','A','OBJECT','MAP','PARAM','META'),
			'type' => 'text',
			),
		'ID:' => array(
			'name' => 'id',
			'!tags' => array('BASE', 'HEAD', 'HTML', 'META', 'SCRIPT', 'STYLE', 'TITLE'),
			'type' => 'text',
			),
		'Class:' => array(
			'name' => 'class',
			'!tags' => array('BASE','BASEFONT','HEAD','HTML','META','PARAM','SCRIPT','STYLE','TITLE'),
			'type' => 'text',
			),
		
		'Style:' => array(
			'name' => 'style',
			'!tags' => array('BASE','BASEFONT','HEAD','HTML','META','PARAM','SCRIPT','STYLE','TITLE'),
			'type' => 'text',
			),
		'Title:' => array(
			'name' => 'title',
			'!tags' => array('BASE', 'BASEFONT', 'HEAD', 'HTML', 'META', 'PARAM', 'SCRIPT', 'TITLE'),
			'type' => 'text',
			),
		'AccessKey:' => array(
			'name' => 'accessKey',
			'tags' => array('A','AREA','BUTTON','INPUT','LABEL','LEGEND','TEXTAREA'),
			'type' => 'text',
			),	
		'TabIndex:' => array (
			'name' => 'tabIndex',
			'tags' => array('A', 'AREA', 'BUTTON', 'INPUT', 'OBJECT', 'SELECT', 'TEXTAREA'),
			'type' => 'text',
			),
		'LongDesc:' => array (
			'name' => 'longDesc',
			'tags' => array('IMG','FRAME'),
			'type' => 'file',
			),
				
		'Summary:' => array (
			'name' => 'summary',
			'tags' => array('TABLE'),
			'type' => 'longtext',
			),

	),
	
	'Language' => array (
		'Lang:' => array (
			'name' => 'lang',
			'!tags' => array('APPLET','BASE','BASEFONT','BR','FRAME','FRAMESET','IFRAME','PARAM','SCRIPT'),
			'type' => 'text',
			),
		'Dir:' => array(
			'name' => 'dir', 
			'!tags' => array('APPLET','BASE','BASEFONT','BDO','BR','FRAME','FRAMESET','IFRAME','PARAM','SCRIPT'),
			'values' => array('ltr'=>'ltr','rtl'=>'rtl')),
	),
	
)



?>