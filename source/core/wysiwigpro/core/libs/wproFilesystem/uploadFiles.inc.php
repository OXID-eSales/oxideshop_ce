<?php
/* function include file */
if (!defined('IN_WPROFILESYSTEM')) exit;

		$errors = array();
		$errors['fatal'] = array(); // an array of files that failed to upload
		$errors['resized'] = array(); // images resized to maximum allowed size
		$errors['renamed'] = array(); // an array of files which had to be slightly re-named
		$errors['overwrite'] = array(); // an array of files where a file with that name already exists
		$errors['succeeded'] = array(); // array of files successfully uploaded, if files were renamed this has the renamed name not the original.
		
		
		$GDExtensions = array('.jpg','.jpeg','.gif','.png'); // filetypes that can be resized with GD
		
		if (substr($folder, strlen($folder)-1) != '/') {
			$folder.='/';
		}
		
		require_once(WPRO_DIR.'core/libs/wproImageEditor.class.php');
		$imageEditor = new wproImageEditor();
		
		if (isset($_FILES[$field])) {
			$num = count($_FILES[$field]['tmp_name']);
			for ($i=0; $i<=$num - 1; $i++) {
				if (is_uploaded_file($_FILES[$field]['tmp_name'][$i])) {
				
					if (empty($_FILES[$field]['name'][$i])) continue;
					
					$extension = strrchr(strtolower($_FILES[$field]['name'][$i]),'.');
					
					// check filetype against accepted files
					if (!$this->extensionOK($extension, $extensions)) {
						
						// bad extension...
						$errors['fatal'][$_FILES[$field]['name'][$i]] = 'badExtension';
						@unlink($_FILES[$field]['tmp_name'][$i]);
						if ($stopOnError) return $errors;
						continue;
						
					} else if ($_FILES[$field]['size'][$i] >= $sizeLimit) {
						
						// bad size
						$errors['fatal'][$_FILES[$field]['name'][$i]] = 'badSize';
						@unlink($_FILES[$field]['tmp_name'][$i]);
						if ($stopOnError) return $errors;
						continue;
					
					} else if ($_FILES[$field]['size'][$i] == 0) {
						
						// bad size
						$errors['fatal'][$_FILES[$field]['name'][$i]] = 'badSize';
						@unlink($_FILES[$field]['tmp_name'][$i]);
						if ($stopOnError) return $errors;
						continue;						
						
					} else {
						
						// fix bad file names
						$name = $this->makeFileNameOK($_FILES[$field]['name'][$i]);
						$goodName = $name;
						
						if (!$name) {
							continue;
						}
						
						// check filters
						if ($this->filterMatch($name, $filters)){
							$errors['fatal'][$_FILES[$field]['name'][$i]] = 'reserved';
							@unlink($_FILES[$field]['tmp_name'][$i]);
							if ($stopOnError) return $errors;
							continue;
						}
						
						// was file renamed?
						if ($name != $_FILES[$field]['name'][$i]) $errors['rename'][$_FILES[$field]['name'][$i]] = $name;
						
						// does this file already exist?
						if (file_exists($folder.$name) && !$overwrite) {
							// file with this name already exists
							$tempName = $this->resolveDuplicate(uniqid('_WPROTEMP_').$extension, $folder);
							$errors['overwrite'][$_FILES[$field]['name'][$i]] = $tempName;
							$name = $tempName;
							//@move_uploaded_file($_FILES[$field]['tmp_name'][$i], $folder.$name.'.WPTEMP');
							//@unlink($_FILES[$field]['tmp_name'][$i]);
							if ($stopOnError) return $errors;
						}
						
						// try to move file to final destination...
						if (!@move_uploaded_file($_FILES[$field]['tmp_name'][$i], $folder.$name)) {
							// failed to move file
							$errors['fatal'][$_FILES[$field]['name'][$i]] = 'unknown';
							@unlink($_FILES[$field]['tmp_name'][$i]);
							if ($stopOnError) return $errors;
							
						} else {
							// if image check size...
							if ($chkimgwidth) {
								if (in_array($extension, $GDExtensions)) {
									list ($width, $height) = @getimagesize($folder.$name);
									if ($width > $maxwidth || $height > $maxheight) {
										// image too large
										// if GD library is installed re-size image to maximum acceptable size...
										if ($resizedTo = @$imageEditor->proportionalResize ($folder.$name, $folder.$name, $maxwidth, $maxheight)) {
											$errors['resized'][$_FILES[$field]['name'][$i]] = $resizedTo;
											$name = basename($resizedTo[2]);
											if (isset($errors['overwrite'][$_FILES[$field]['name'][$i]])) {
												if ($errors['overwrite'][$_FILES[$field]['name'][$i]] != $name) {
													unset($errors['overwrite'][$_FILES[$field]['name'][$i]]);
													if (file_exists($folder.$name) && !$overwrite) {
														$errors['overwrite'][$goodName.'.png'] = basename($resizedTo[2]);
													} else {
														$this->rename($folder.basename($resizedTo[2]), $folder.$goodName.'.png');
													}
												}
											}
										} else {
											$errors['fatal'][$_FILES[$field]['name'][$i]] = 'badDimensions';
											@unlink($folder.$name);
											if ($stopOnError) return $errors;
											continue;
										}
									}
								}
							}
							
							array_push($errors['succeeded'], $name);
							if (!empty($chmod)) {
								$this->chmod($folder.$name, $chmod);
							}
							if ($changeGroup) {
								// make group the same as folder if possible
								if (@filegroup($folder) != @filegroup($folder.$name)) {
									if (@filegroup($folder)) {
										@chgrp ( $folder.$name, @filegroup($folder) );
									}
								}
							}
						}					
						
					}
					@unlink($_FILES[$field]['tmp_name'][$i]);
				} else {
					if (!empty($_FILES[$field]['name'][$i])) {
						$errors['fatal'][$_FILES[$field]['name'][$i]] = $_FILES[$field]['error'][$i];
					}
				}
			}		
		}
		return $errors;

?>