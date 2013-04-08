<?php
if (!defined('IN_WPRO')) exit;

class wproImageEditor {

	var $adjustMemoryLimit = true; // set to false to prevent memory limit alteration...
	var $fileCHMOD = 0;
	
	/* rotates an image, accepts only 90, 180 or 270 degrees as options, any other value will return false and do nothing (360 and 0 = no rotation) */
	function rotate($file, $outputfile='', $degrees, $jpgQuality=100) {
		
		if ( !file_exists( $file )) return false;
		
		list ($origwidth, $origheight) = @getimagesize($file);
		
		$extension = strrchr(strtolower($file),'.');
		
		$degrees = 360 - $degrees;
		
		switch ($degrees) {
			case 90 :
			case 270 :
				$new_w = $origheight;
				$new_h = $origwidth;
				break;
			case 180:
				$new_h = $origwidth;
				$new_w = $origheight;
				break;
			default :
				return false;
		}
		switch($extension) {
			case '.jpg':
			case '.jpeg':
				return $this->_imageRotateJpeg($file, $outputfile, $degrees, $new_w, $new_h, $origwidth, $origheight, $jpgQuality);
				break;
			case '.png':
				return $this->_imageRotatePng($file, $outputfile, $degrees, $new_w, $new_h, $origwidth, $origheight);
				break;
			case '.gif':
				return $this->_imageRotateGif($file, $outputfile, $degrees, $new_w, $new_h, $origwidth, $origheight);
				break;
			default :
				return false;
		}
		
		
	}
	
	function _imageRotateJpeg( $file, $output, $degrees, $width, $height, $origwidth, $origheight, $quality=100 ) {
					
		if (!function_exists('imagejpeg')) return false;
		
		if (function_exists('imagetypes')) {
			if (!(imagetypes() & IMG_JPG)) return false;
		}
		
		// check and set required memory to process this image
		if (!$this->_setMemoryForImage($file)) {
			return false;
		}
		
		// get the image pointer to the original image
		if (!$imageToRotate = @imagecreatefromjpeg($file)) {
			return false;
		}
		
		// if width and height have changed do resize
		/*if ($width != $origwidth || $height != $origheight) {
			
			//create the blank limited-palette image
			if (!$base_image = $this->_imageCreateBase($width, $height)) {
				return false;
			}
			
			// resize the image (because imagerotate will scale the image to fit within its original dimensions and we do not want this)
			if (function_exists('imagecopyresampled')) {
				if (!@imagecopyresampled($base_image, $imageToRotate, 0, 0, 0, 0, $width, $height, $origwidth, $origheight)) {
					@imagecopyresized($base_image, $imageToRotate, 0, 0, 0, 0, $width, $height, $origwidth, $origheight);
				}
			} else {
				@imagecopyresized($base_image, $imageToRotate, 0, 0, 0, 0, $width, $height, $origwidth, $origheight);
			}	
			
			// rotate the resized image
			$rotated = imagerotate($base_image, $degrees, 0);	
			
		} else {*/
			// rotate the image	
			$rotated = imagerotate($imageToRotate, $degrees, 0);
		//}
		
		if (empty($output)) {
			header("Content-type: image/jpeg", true);
			if (@imagejpeg($rotated, '', $quality)) {
				$return = array($width, $height, $output);
			}
		} else {
			$fh=@fopen($output,'w');
			@fclose($fh);
			if (@imagejpeg($rotated, $output, $quality)) { //image destination
				$return = array($width, $height, $output);
				if (!empty($this->fileCHMOD)) {
					$fs = new wproFilesystem();
					$fs->chmod($output, $this->fileCHMOD);
				}
			}
		}
		
		//if (isset($base_image)) @imagedestroy($base_image);
		@imagedestroy($imageToRotate);
		@imagedestroy($rotated);
			
		return $return;
		
	} 
	
	function _imageRotatePng( $file, $output, $degrees, $width, $height, $origwidth, $origheight) {
					
		if (!function_exists('imagepng')) return false;
		
		if (function_exists('imagepng')) {
			if (!(imagetypes() & IMG_PNG)) return false;
		}
		
		// check and set required memory to process this image
		if (!$this->_setMemoryForImage($file)) {
			return false;
		}
		
		// get the image pointer to the original image
		if (!$imageToRotate = @imagecreatefrompng($file)) {
			return false;
		}
		
		// if width and height have changed do resize
		/*if ($width != $origwidth || $height != $origheight) {
			
			//create the blank limited-palette image
			if (!$base_image = $this->_imageCreateBase($width, $height)) {
				return false;
			}
			
			// resize the image (because imagerotate will scale the image to fit within its original dimensions and we do not want this)
			if (function_exists('imagecopyresampled')) {
				if (!@imagecopyresampled($base_image, $imageToRotate, 0, 0, 0, 0, $width, $height, $origwidth, $origheight)) {
					@imagecopyresized($base_image, $imageToRotate, 0, 0, 0, 0, $width, $height, $origwidth, $origheight);
				}
			} else {
				@imagecopyresized($base_image, $imageToRotate, 0, 0, 0, 0, $width, $height, $origwidth, $origheight);
			}	
			
			// rotate the resized image
			$rotated = imagerotate($base_image, $degrees, -1);	
			
		} else {*/
			// rotate the image	
			$rotated = imagerotate($imageToRotate, $degrees, -1);
		//}
		
		if (empty($output)) {
			header("Content-type: image/x-png", true);
			if (@imagepng($rotated)) {
				$return = array($width, $height, $output);
			}
		} else {
			$fh=@fopen($output,'w');
			@fclose($fh);
			if (@imagepng($rotated, $output)) { //image destination
				$return = array($width, $height, $output);
				if (!empty($this->fileCHMOD)) {
					$fs = new wproFilesystem();
					$fs->chmod($output, $this->fileCHMOD);
				}
			}
		}
		
		//if (isset($base_image)) @imagedestroy($base_image);
		@imagedestroy($imageToRotate);
		@imagedestroy($rotated);
			
		return $return;
		
	} 
	
	function _imageRotateGif( $file, $output, $degrees, $width, $height, $origwidth, $origheight) {
					
		if (!function_exists('imagegif') && (!function_exists('imagecreatefromgif') || !function_exists('imagepng') ) ) return false;
		
		$canGif = true;
		
		// more robust gif support check for PHP > 4.0.2
		if (function_exists('imagetypes')) {
			if (!(imagetypes() & IMG_GIF)) {
				$canGif = false;
				if (!(imagetypes() & IMG_PNG)) return false;
			}
		}
		
		//$extension = strrchr(strtolower($file),'.');
		
		// check and set required memory to process this image
		if (!$this->_setMemoryForImage($file)) {
			return false;
		}
		
		// get the image pointer to the original image
		if (!$imageToRotate = @imagecreatefromgif($file)) {
			return false;
		}
		
		// if width and height have changed do resize
		/*if ($width != $origwidth || $height != $origheight) {
			
			//create the blank limited-palette image
			if (!$base_image = $this->_imageCreateBase($width, $height)) {
				return false;
			}
			
			if (function_exists('imagecopyresampled')) {
				if (!@imagecopyresampled($base_image, $imageToRotate, 0, 0, 0, 0, $width, $height, $origwidth, $origheight)) {
					@imagecopyresized($base_image, $imageToRotate, 0, 0, 0, 0, $width, $height, $origwidth, $origheight);
				}
			} else {
				@imagecopyresized($base_image, $imageToRotate, 0, 0, 0, 0, $width, $height, $origwidth, $origheight);
			}
			
			// rotate the resized image
			$rotated = imagerotate($base_image, $degrees, -1);	
			
		} else {*/
			// rotate the image	
			$rotated = imagerotate($imageToRotate, $degrees, -1);
		//}
		
		if (!function_exists('imagegif') || !$canGif ) {
			$outputFunction = 'imagepng';
			$header = 'Content-type: image/x-png';
			//$output = str_replace($extension, '.png', $output);
		} else {
			$outputFunction = 'imagegif';
			$header = 'Content-type: image/gif';
		}
		
		$return = false;
		$doIt = true;
		$deleteOrig = false;
		if (empty($output)) {
			header($header, true);
			if (@$outputFunction($rotated)) {
				$return = array($width, $height, $output);
			}
		} else {
			if ($outputFunction == 'imagepng') {
				// extension on the output file must be changed
				//$name = substr($output, 0, strlen($output) - strlen($extension));
				$name = $output.'.png';
				if (file_exists($name)) {
					$return = false;
					$doIt = false;
				} else {
					if ($file == $output) {
						$deleteOrig = true;
					}					
					$output = $name;	
				}
			}
			$fh=@fopen($output,'w');
			@fclose($fh);
			if ($doIt) {
				if (@$outputFunction($rotated, $output)) { //image destination
					if ($outputFunction == 'imagepng') {
						if ($deleteOrig) {
							@unlink($file);
						}
					}
					$return = array($width, $height, $output);
					if (!empty($this->fileCHMOD)) {
						$fs = new wproFilesystem();
						$fs->chmod($output, $this->fileCHMOD);
					}
				}
			}
		}
		//if (isset($base_image)) @imagedestroy($base_image);
		@imagedestroy($rotated);
		@imagedestroy($imageToRotate);
			
		return $return;
		
	} 
	
	/* uses GD to crop an image */
	function crop ($file, $outputfile='', $posx, $posy, $width, $height, $jpgQuality=100) {
		if ( !file_exists( $file )) return false;
		
		$extension = strrchr(strtolower($file),'.');
		
		list ($origwidth, $origheight) = @getimagesize($file);
		
		// check dimensions
		if ($width + $posx > $origwidth) {
			$width = $origwidth - $posx;
		}
		if ($height + $posy > $origheight) {
			$height = $origheight - $posy;
		}
						
		switch($extension) {
			case '.jpg':
			case '.jpeg':
				return $this->_imageResizeJpeg($file, $outputfile, $origwidth, $origheight, $width, $height, $posx, $posy, $jpgQuality);
				break;
			case '.png':
				return $this->_imageResizePng($file, $outputfile, $origwidth, $origheight, $width, $height, $posx, $posy);
				break;
			case '.gif':
				return $this->_imageResizeGif($file, $outputfile, $origwidth, $origheight, $width, $height, $posx, $posy);
				break;
			default :
				return false;
		}
			
	}
	
	/* resizes an image without maintaining aspect ratio */
	function resize ($file, $outputfile='', $width=500, $height=500, $jpgQuality=100) {
		
		if ( !file_exists( $file )) return false;
		
		$extension = strrchr(strtolower($file),'.');
		
		list ($origwidth, $origheight) = @getimagesize($file);
		
		if ((($origwidth != $width) || ($origheight != $height)) || empty($outputfile) ) {    
						
			switch($extension) {
				case '.jpg':
				case '.jpeg':
					return $this->_imageResizeJpeg($file, $outputfile, $origwidth, $origheight, $width, $height, 0, 0, $jpgQuality);
					break;
				case '.png':
					return $this->_imageResizePng($file, $outputfile, $origwidth, $origheight, $width, $height, 0, 0);
					break;
				case '.gif':
					return $this->_imageResizeGif($file, $outputfile, $origwidth, $origheight, $width, $height, 0, 0);
					break;
				default :
					return false;
			}
			
		} else {
			@copy($file, $outputfile);
			return true;
		}
	}
	
	/* uses the GD library to re-size an image, maintaining aspect ratio 
	returns false on failure, or the new dimensions on success.
	NOTE: only scales down, NOT up!!
	*/
	function proportionalResize ($file, $outputfile='', $maxwidth=0, $maxheight=0, $jpgQuality=100) {
		
		if ( !file_exists( $file )) return false;
		
		$extension = strrchr(strtolower($file),'.');
		
		list ($origwidth, $origheight) = @getimagesize($file);
		
		if ((($origwidth > $maxwidth) || ($origheight > $maxheight)) || empty($outputfile) ) {    
			
			list ($new_w, $new_h) = $this->getProportionalSize($origwidth, $origheight, $maxwidth, $maxheight);
			
			switch($extension) {
				case '.jpg':
				case '.jpeg':
					return $this->_imageResizeJpeg($file, $outputfile, $origwidth, $origheight, $new_w, $new_h, 0, 0, $jpgQuality);
					break;
				case '.png':
					return $this->_imageResizePng($file, $outputfile, $origwidth, $origheight, $new_w, $new_h, 0, 0);
					break;
				case '.gif':
					return $this->_imageResizeGif($file, $outputfile, $origwidth, $origheight, $new_w, $new_h, 0, 0);
					break;
				default :
					return false;
			}
			
		} else {
			@copy($file, $outputfile);
			return array($origwidth, $origheight, $outputfile);
		}
	}
	
	function getProportionalSize($origwidth, $origheight, $maxwidth=0, $maxheight=0) {
		if (empty($maxwidth)) {
			$maxwidth = $origwidth;
		}
		if (empty($maxheight)) {
			$maxheight = $origheight;
		}
		if (($origwidth > $maxwidth) || ($origheight > $maxheight)) {    
			if (($origwidth > $maxwidth) && ($origheight > $maxheight)) {
				if ( ($origwidth/$maxwidth) > ($origheight/$maxheight) ) {
					$newscale = $maxwidth / $origwidth;
				} else {
					$newscale = $maxheight / $origheight;
				}			
			} else if ( $origwidth > $maxwidth ) {
				$newscale = $maxwidth / $origwidth;
			} else {
				$newscale = $maxheight / $origheight;
			}
			
			//calculate the new aspect ratio
			$new_w = round(abs($origwidth * $newscale));
			$new_h = round(abs($origheight * $newscale));
			return array($new_w, $new_h);
		} else {
			return array($origwidth, $origheight);
		}
	}
	
	
	////////////////////////////////////////////////////////
	// Get and try to set memory required to process image
	////////////////////////////////////////////////////////
	
	function _returnBytes($val) {
	   if (!empty($val)) {
		   $val = trim($val);
		   $last = strtolower(preg_replace("/^[0-9]+\s*([A-Za-z]+)$/si", "$1", $val));//strtolower($val{strlen($val)-1});
		   $val = preg_replace("/[^0-9]/si", "", $val);
		   switch($last) {
			   // The 'G' modifier is available since PHP 5.1.0
			   case 't':
			   case 'tb':
					$val *= 1024;
			   case 'g':
			   case 'gb':
				   $val *= 1024;
			   case 'm':
			   case 'mb':
				   $val *= 1024;
			   case 'k':
			   case 'kb':
				   $val *= 1024;
		   }
		}
	   return $val;
	}
	
	function _memory_get_usage() {
		$memory_usage = 0;
		// the memroy_get_usage function is not available on all platforms
		// so make up a realistic figure if it is not
		if (function_exists('memory_get_usage')) {
			$memory_usage = memory_get_usage(true);
		} else {
			$memory_usage = 8 * 1048576;
		}
		return $memory_usage;
	}
	
	function _setMemoryForImage( $filename ){
		
		if (ini_get('memory_limit') === '-1') {
			return true;
		} else {
		
			$imageInfo = getimagesize($filename);
			$MB = 1048576;  // number of bytes in 1M
			$K64 = 65536;    // number of bytes in 64K
			$TWEAKFACTOR = 2;  // Or whatever works for you
			$memoryNeeded = round( 
			( $imageInfo[0] * $imageInfo[1]
				* $imageInfo['bits']
				* (isset($imageInfo['channels'])?($imageInfo['channels']/8):1)
				+ $K64
				) * $TWEAKFACTOR
			);
			
			// ini_get('memory_limit') only works if compiled with "--enable-memory-limit"
			// so if memory_limit does not return a value the default memory limit is 16MB (as of PHP 5) so we'll stick with that.
			// To find out what yours is, view your php.ini file.
			if (!$memoryLimit = $this->_returnBytes(ini_get('memory_limit'))) {
				$memoryLimit = 16 * $MB;
			}
			
			$memoryUsed = $this->_memory_get_usage();
					
			// if memory usage is greater than memory limit then there must be a problem
			// we are probably using on older PHP version
			// invent a conservative figure and go from there
			if ($memoryUsed>$memoryLimit) {
				$memoryUsed = 8*$MB;
			}		
			
			if (($memoryUsed + $memoryNeeded) > $memoryLimit) {
				if ($this->adjustMemoryLimit) {
					$newLimit = ceil(($memoryUsed + $memoryNeeded)/$MB);
					// add a bit more to be safe
					if (ini_set( 'memory_limit', $newLimit + 2 . 'M' )) {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return true;
			}
			
		}
	}
	
	////////////////////////
	// create base image
	///////////////////////
	
	function _imageCreateBase($width, $height) {
		
		if (function_exists('imagecreatetruecolor') && function_exists('imagecreate')) {
			if ($base_image = @imagecreatetruecolor($width, $height)) {		
				return $base_image;
			} else if ($base_image = @imagecreate($width, $height)) {
				return $base_image;
			}
		} else if (function_exists('imagecreate')) {
			if ($base_image = @imagecreate($width, $height)) {
				return $base_image;
			}
		}
		return false;
	}
	
	////////////////////////
	// Resize a jpeg image
	///////////////////////
	
	function _imageResizeJpeg( $file, $output, $origwidth, $origheight, $width, $height, $cropX=0, $cropY=0, $quality=100 ) {
					
		if (!function_exists('imagejpeg')) return false;
		
		if (function_exists('imagetypes')) {
			if (!(imagetypes() & IMG_JPG)) return false;
		}
		
		// check and set required memory to process this image
		if (!$this->_setMemoryForImage($file)) {
			return false;
		}
		
		//create the blank limited-palette image
		if (!$base_image = $this->_imageCreateBase($width, $height)) {
			return false;
		}
				
		// get the image pointer to the original image
		if (!$imageToResize = @imagecreatefromjpeg($file)) {
			return false;
		}
		
		if (function_exists('imagecopyresampled')) {
			if (!@imagecopyresampled($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight)) {
				@imagecopyresized($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight);
			}
		} else {
			@imagecopyresized($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight);
		}
		/*if (empty($output)) {
			header("Content-type: image/jpeg", true);
		} else {
			$fh=@fopen($output,'w');
			@fclose($fh);
		}
		$return = false;
		//create the resized image
		if (@imagejpeg($base_image, $output, $quality)) {
			$return = array($width, $height, $output, '');
		} */
		$return = false;
		if (empty($output)) {
			header("Content-type: image/jpeg", true);
			if (@imagejpeg($base_image, '', $quality)) {
				$return = array($width, $height, $output);
			}
		} else {
			$fh=@fopen($output,'w');
			@fclose($fh);
			if (@imagejpeg($base_image, $output, $quality)) { //image destination
				$return = array($width, $height, $output);
				if (!empty($this->fileCHMOD)) {
					$fs = new wproFilesystem();
					$fs->chmod($output, $this->fileCHMOD);
				}
			}
		}
		@imagedestroy($base_image);
		@imagedestroy($imageToResize);
			
		return $return;
		
	} 
	
	
	////////////////////////
	// Resize a PNG image
	///////////////////////
	
	function _imageResizePng( $file, $output, $origwidth, $origheight, $width, $height, $cropX=0, $cropY=0 ) {
	
		if (!function_exists('imagepng')) return false;
		
		if (function_exists('imagetypes')) {
			if (!(imagetypes() & IMG_PNG)) return false;
		}
		
		// check and set required memory to process this image
		if (!$this->_setMemoryForImage($file)) {
			return false;
		}
		
		//create the blank limited-palette image
		if (!$base_image = $this->_imageCreateBase($width, $height)) {
			return false;
		}
		
		// get the image pointer to the original image
		if(!$imageToResize = @imagecreatefrompng($file)) {
			return false;
		}
		
		if (function_exists('imagecopyresampled')) {
			if (!@imagecopyresampled($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight)) {
				@imagecopyresized($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight);
			}
		} else {
			@imagecopyresized($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight);
		}
		$return = false;
		if (empty($output)) {
			header("Content-type: image/x-png", true);
			if (@imagepng($base_image)) {
				$return = array($width, $height, $output);
			}
		} else {
			$fh=@fopen($output,'w');
			@fclose($fh);
			if (@imagepng($base_image, $output)) { //image destination
				$return = array($width, $height, $output);
				if (!empty($this->fileCHMOD)) {
					$fs = new wproFilesystem();
					$fs->chmod($output, $this->fileCHMOD);
				}
			}
		}
		@imagedestroy($base_image);
		@imagedestroy($imageToResize);
			
		return $return;
	} 
	
		////////////////////////
	// Resize a GIF image
	///////////////////////
	
	function _imageResizeGif( $file, $output, $origwidth, $origheight, $width, $height, $cropX=0, $cropY=0) {
		
		if (!function_exists('imagegif') && (!function_exists('imagecreatefromgif') || !function_exists('imagepng') ) ) return false;
		
		$canGif = true;
		
		// more robust gif support check for PHP > 4.0.2
		if (function_exists('imagetypes')) {
			if (!(imagetypes() & IMG_GIF)) {
				$canGif = false;
				if (!(imagetypes() & IMG_PNG)) return false;
			}
		}	
		
		//$extension = strrchr(strtolower($file),'.');
		
		// check and set required memory to process this image
		if (!$this->_setMemoryForImage($file)) {
			return false;
		}
		
		//create the blank limited-palette image
		if (!$base_image = $this->_imageCreateBase($width, $height)) {
			return false;
		}
		
		// get the image pointer to the original image
		if (!$imageToResize = @imagecreatefromgif($file)) {
			return false;
		}
		
		if (function_exists('imagecopyresampled')) {
			if (!@imagecopyresampled($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight)) {
				@imagecopyresized($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight);
			}
		} else {
			@imagecopyresized($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight);
		}
		if (!function_exists('imagegif') || !$canGif ) {
			$outputFunction = 'imagepng';
			$header = 'Content-type: image/x-png';
			//$output = str_replace($extension, '.png', $output);
		} else {
			$outputFunction = 'imagegif';
			$header = 'Content-type: image/gif';
		}
		
		$return = false;
		$doIt = true;
		$deleteOrig = false;
		if (empty($output)) {
			header($header, true);
			if (@$outputFunction($base_image)) {
				$return = array($width, $height, $output);
			}
		} else {
			if ($outputFunction == 'imagepng') {
				// extension on the output file must be changed
				//$name = substr($output, 0, strlen($output) - strlen($extension));
				$name = $output.'.png';
				if (file_exists($name)) {
					$return = false;
					$doIt = false;
				} else {
					if ($file == $output) {
						$deleteOrig = true;
					}					
					$output = $name;	
				}
			}
			$fh=@fopen($output,'w');
			@fclose($fh);
			if ($doIt) {
				if (@$outputFunction($base_image, $output)) { //image destination
					if ($outputFunction == 'imagepng') {
						if ($deleteOrig) {
							@unlink($file);
						}
					}
					$return = array($width, $height, $output);
					if (!empty($this->fileCHMOD)) {
						$fs = new wproFilesystem();
						$fs->chmod($output, $this->fileCHMOD);
					}
				}
			}
		}
		@imagedestroy($base_image);
		@imagedestroy($imageToResize);
			
		return $return;
	}
}

?>