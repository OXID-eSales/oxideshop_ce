<?php
if (!defined('IN_WPRO')) exit;
if (!isset($extension)) exit;

/*
Default, built-in file definitions

icon = name of icon file in theme icons folder
descrition = name of the key in the 'files' group in the language file (or the description if you don't care for translations)
preview = can this type of file be safely previewed?
*/
switch($extension) {
	case '.html':
		$info['icon'] = 'htm';
		$info['description'] = 'html';
		$info['preview'] = 1;
		break;
	case '.htm':
		$info['icon'] = 'htm';
		$info['description'] = 'html';
		$info['preview'] = 1;
		break;
	case '.pdf':
		$info['icon'] = 'pdf';
		$info['description'] = 'pdf';
		$info['preview'] = 0;
		break;
	case '.rtf':
		$info['icon'] = 'rtf';
		$info['description'] = 'rtf';
		$info['preview'] = 0;
		break;
	case '.txt':
		$info['icon'] = 'txt';
		$info['description'] = 'txt';
		$info['preview'] = 1;
		break;
	// office files
	case '.doc':
		$info['icon'] = 'doc';
		$info['description'] = 'doc';
		$info['preview'] = 0;
		break;
	case '.docx':
		$info['icon'] = 'docx';
		$info['description'] = 'docx';
		$info['preview'] = 0;
		break;
	case '.xls':
		$info['icon'] = 'xl';
		$info['description'] = 'xl';
		$info['preview'] = 0;
		break;
	case '.xlsx':
		$info['icon'] = 'xlsx';
		$info['description'] = 'xlsx';
		$info['preview'] = 0;
		break;
	case '.ppt':
		$info['icon'] = 'ppt';
		$info['description'] = 'ppt';
		$info['preview'] = 0;
		break;
	case '.pptx':
		$info['icon'] = 'pptx';
		$info['description'] = 'pptx';
		$info['preview'] = 0;
		break;
	case '.pps':
		$info['icon'] = 'pps';
		$info['description'] = 'pps';
		$info['preview'] = 0;
		break;
	case '.ppsx':
		$info['icon'] = 'ppsx';
		$info['description'] = 'ppsx';
		$info['preview'] = 0;
		break;
	// compression file types
	case '.zip':
		$info['icon'] = 'zip';
		$info['description'] = 'zip';
		$info['preview'] = 0;
		break;
	case '.tar':
		$info['icon'] = 'zip';
		$info['description'] = 'tar';
		$info['preview'] = 0;
		break;
	case '.gzip':
		$info['icon'] = 'zip';
		$info['description'] = 'gzip';
		$info['preview'] = 0;
		break;
	case '.bzip':
		$info['icon'] = 'zip';
		$info['description'] = 'bzip';
		$info['preview'] = 0;
		break;
	case '.sit':
		$info['icon'] = 'zip';
		$info['description'] = 'sit';
		$info['preview'] = 0;
		break;
	case '.dmg':
		$info['icon'] = 'zip';
		$info['description'] = 'dmg';
		$info['preview'] = 0;
		break;
	// media files
	case '.swf':
		$info['icon'] = 'swf';
		$info['description'] = 'swf';
		$info['preview'] = 0;
		break;
	case '.flv':
		$info['icon'] = 'flv';
		$info['description'] = 'flv';
		$info['preview'] = 0;
		break;
	case '.wmv':
		$info['icon'] = 'wmv';
		$info['description'] = 'wmv';
		$info['preview'] = 0;
		break;
	case '.wma':
		$info['icon'] = 'wmv';
		$info['description'] = 'wmv';
		$info['preview'] = 0;
		break;
	case '.wax':
		$info['icon'] = 'wmv';
		$info['description'] = 'wmv';
		$info['preview'] = 0;
		break;
	case '.wvx':
		$info['icon'] = 'wmv';
		$info['description'] = 'wmv';
		$info['preview'] = 0;
		break;
	case '.asf':
		$info['icon'] = 'wmv';
		$info['description'] = 'wmv';
		$info['preview'] = 0;
		break;
	case '.rm':
		$info['icon'] = 'rm';
		$info['description'] = 'rm';
		$info['preview'] = 0;
		break;
	case '.mov':
		$info['icon'] = 'mov';
		$info['description'] = 'mov';
		$info['preview'] = 0;
		break;
	case '.mp3':
		$info['icon'] = 'mp3';
		$info['description'] = 'mp3';
		$info['preview'] = 0;
		break;
	case '.mp4':
		$info['icon'] = 'flv';
		$info['description'] = 'mp4';
		$info['preview'] = 0;
		break;
	case '.h264':
		$info['icon'] = 'flv';
		$info['description'] = 'mp4';
		$info['preview'] = 0;
		break;
	// playlists
	case '.asx':
		$info['icon'] = 'unknown';
		$info['description'] = 'asx';
		$info['preview'] = 0;
		break;
	case '.xspf':
		$info['icon'] = 'unknown';
		$info['description'] = 'xspf';
		$info['preview'] = 0;
		break;
	case '.wpl':
		$info['icon'] = 'unknown';
		$info['description'] = 'wpl';
		$info['preview'] = 0;
		break;
	// image file types
	case '.jpg':
		$info['icon'] = 'jpg';
		$info['description'] = 'jpg';
		$info['preview'] = 1;
		break;
	case '.jpeg':
		$info['icon'] = 'jpg';
		$info['description'] = 'jpg';
		$info['preview'] = 1;
		break;
	case '.gif':
		$info['icon'] = 'gif';
		$info['description'] = 'gif';
		$info['preview'] = 1;
		break;
	case '.png':
		$info['icon'] = 'png';
		$info['description'] = 'png';
		$info['preview'] = 1;
		break;
	// executable file types
	case '.exe':
		$info['icon'] = 'exe';
		$info['description'] = 'exe';
		$info['preview'] = 0;
		break;
	// default;	
	default: 
		$info['icon'] = 'unknown';
		$info['description'] = strtoupper(str_replace('.', '', $extension)).' ##file##';
		$info['preview'] = 0;
		break;	
}
?>