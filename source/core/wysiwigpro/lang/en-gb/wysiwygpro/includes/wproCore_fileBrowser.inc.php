<?php 
if (!defined('IN_WPRO')) exit;
$lang=array();
$lang['wproCore_fileBrowser'] = array();
// buttons
$lang['wproCore_fileBrowser']['JSInsert'] = 'Insert';
$lang['wproCore_fileBrowser']['JSApply'] = 'Apply';
$lang['wproCore_fileBrowser']['upload'] = 'Upload';
$lang['wproCore_fileBrowser']['preview'] = 'Preview';

// general interface
$lang['wproCore_fileBrowser']['nopreview'] = 'Click "Load preview" to preview this file if your browser supports it, or to download the file if it does not.';
$lang['wproCore_fileBrowser']['linkText'] = 'Link text:';
$lang['wproCore_fileBrowser']['screenTip'] = 'Screen tip:';
$lang['wproCore_fileBrowser']['alternateText'] = 'Alternate text:';
$lang['wproCore_fileBrowser']['previewInNewWindow'] = 'Preview in new window';
$lang['wproCore_fileBrowser']['loadPreview'] = 'Load preview';
$lang['wproCore_fileBrowser']['bookmarks'] = 'Bookmarks:';
$lang['wproCore_fileBrowser']['topOfPage'] = 'Top of the page';
$lang['wproCore_fileBrowser']['emailAddress'] = 'E-mail address';
$lang['wproCore_fileBrowser']['emailSubject'] = 'Initial subject:';
$lang['wproCore_fileBrowser']['emailMessage'] = 'Initial message:';
$lang['wproCore_fileBrowser']['chooseFileType'] = 'File type:';
$lang['wproCore_fileBrowser']['back'] = 'Back';
$lang['wproCore_fileBrowser']['up'] = 'Up';
$lang['wproCore_fileBrowser']['moveTo'] = 'Move Selected To...';
$lang['wproCore_fileBrowser']['copyTo'] = 'Copy Selected To...';
$lang['wproCore_fileBrowser']['rename'] = 'Rename Selected...';
$lang['wproCore_fileBrowser']['editImage'] = 'Edit Image...';
$lang['wproCore_fileBrowser']['delete'] = 'Delete Selected';
$lang['wproCore_fileBrowser']['uploadFiles'] = 'Upload Files From Your Computer...';
$lang['wproCore_fileBrowser']['newFolder'] = 'New Folder...';
$lang['wproCore_fileBrowser']['thumbnailView'] = 'Thumbnail View';
$lang['wproCore_fileBrowser']['listView'] = 'List View';
$lang['wproCore_fileBrowser']['hide'] = 'Hide';
$lang['wproCore_fileBrowser']['details'] = 'Details';
$lang['wproCore_fileBrowser']['multipleItemsSelected'] = 'Multiple items selected.';
$lang['wproCore_fileBrowser']['noItemSelected'] = 'No item selected.';
$lang['wproCore_fileBrowser']['openFolder'] = 'Open Folder';
$lang['wproCore_fileBrowser']['pageInfo'] = 'Displaying ##pageStart## to ##pageEnd## of ##pageTotal##.';
$lang['wproCore_fileBrowser']['first'] = 'First Page...';
$lang['wproCore_fileBrowser']['next'] = 'Next Page...';
$lang['wproCore_fileBrowser']['previous'] = 'Previous Page...';
$lang['wproCore_fileBrowser']['last'] = 'Last Page...';
$lang['wproCore_fileBrowser']['jumpToPage'] = 'Jump to page...';
$lang['wproCore_fileBrowser']['name'] = 'Name:';
$lang['wproCore_fileBrowser']['type'] = 'Type:';
$lang['wproCore_fileBrowser']['size'] = 'Size:';
$lang['wproCore_fileBrowser']['modified'] = 'Modified:';
$lang['wproCore_fileBrowser']['dimensions'] = 'Dimensions:';
$lang['wproCore_fileBrowser']['prefixIcon'] = 'Prefix with file icon';
$lang['wproCore_fileBrowser']['appendTypeSize'] = 'Append file type and size';
$lang['wproCore_fileBrowser']['url'] = 'URL:';
$lang['wproCore_fileBrowser']['target'] = 'Open in:';
$lang['wproCore_fileBrowser']['sameWindow'] = 'Same window';
$lang['wproCore_fileBrowser']['newWindow'] = 'New window';
$lang['wproCore_fileBrowser']['parentWindow'] = 'Parent window';
$lang['wproCore_fileBrowser']['topWindow'] = 'Top window';
$lang['wproCore_fileBrowser']['namedWindow'] = 'Named window:';
$lang['wproCore_fileBrowser']['windowOptions'] = 'Window Options...';
$lang['wproCore_fileBrowser']['windowName'] = 'Window name:';
$lang['wproCore_fileBrowser']['windowDefaultAppearance'] = 'Use browser default:';
$lang['wproCore_fileBrowser']['showLocationBar'] = 'Show location bar:';
$lang['wproCore_fileBrowser']['showMenuBar'] = 'Show menu bar:';
$lang['wproCore_fileBrowser']['showToolBar'] = 'Show toolbars:';
$lang['wproCore_fileBrowser']['showStatusBar'] = 'Show status bar:';
$lang['wproCore_fileBrowser']['showScrollbars'] = 'Scroll-bars:';
$lang['wproCore_fileBrowser']['resizable'] = 'Resizable:';
$lang['wproCore_fileBrowser']['nameColumn'] = 'Name';
$lang['wproCore_fileBrowser']['typeColumn'] = 'Type';
$lang['wproCore_fileBrowser']['sizeColumn'] = 'Size';
$lang['wproCore_fileBrowser']['modifiedColumn'] = 'Date Modified';
$lang['wproCore_fileBrowser']['selectAll'] = 'Select all';
$lang['wproCore_fileBrowser']['deselectAll'] = 'Deselect all';
$lang['wproCore_fileBrowser']['noFiles'] = 'There are no files or folders to display in this directory.';
$lang['wproCore_fileBrowser']['JSNoFiles'] = 'There are no files or folders to display in this directory.';
$lang['wproCore_fileBrowser']['constrainProportions'] = 'Constrain';
$lang['wproCore_fileBrowser']['accessKey'] = 'Access key:';
$lang['wproCore_fileBrowser']['backgroundColor'] = 'Background:';
$lang['wproCore_fileBrowser']['windowMode'] = 'Window Mode:';
$lang['wproCore_fileBrowser']['transparent'] = 'Transparent';
$lang['wproCore_fileBrowser']['flashVars'] = 'Flash Vars:';
$lang['wproCore_fileBrowser']['youtube'] = 'You Tube Video';
$lang['wproCore_fileBrowser']['youtubeHelp'] = 'Enter the URL of your video into the URL field below.<br /><br />
Example: http://www.youtube.com/watch?v=04QcsUJJurk';
$lang['wproCore_fileBrowser']['flvPlayer'] = 'FLV & MP3 Media Player';
$lang['wproCore_fileBrowser']['showPlaylist'] = 'Show playlist';

/* general errors */
$lang['wproCore_fileBrowser']['quotaExceeded'] = '<strong>You have exceeded your disk quota.</strong> <br />You must delete some of your existing files to make space.';
$lang['wproCore_fileBrowser']['badFolderPath'] = 'The folder path contains illegal characters. This folder cannot be accessed.';
$lang['wproCore_fileBrowser']['JSBadFolderPath'] = 'The folder path contains illegal characters. This folder cannot be accessed.';
$lang['wproCore_fileBrowser']['fileNotExistError'] = 'The file does not exist. It may have been deleted by another user.';
$lang['wproCore_fileBrowser']['JSFileNotExistError'] = 'The file does not exist. It may have been deleted by another user.';
$lang['wproCore_fileBrowser']['folderNotExistError'] = 'The folder does not exist. It may have been deleted by another user.';
$lang['wproCore_fileBrowser']['JSFolderNotExistError'] = 'The folder does not exist. It may have been deleted by another user.';
$lang['wproCore_fileBrowser']['JSOverwritePermissionsError'] = 'Sorry you do not have permission to overwrite files in this directory.';
$lang['wproCore_fileBrowser']['JSOverwriteError'] = 'One or more files or folders cannot be overwritten, they may be in use by another application, or the server may not have permission to delete them.';
$lang['wproCore_fileBrowser']['fileExistsError'] = 'A file or folder with this name already exists in the destination directory and you do not have permission to overwrite it.';
$lang['wproCore_fileBrowser']['chooseFilesToOverwrite'] = 'Some files have the same name as files already in the destination folder, please choose which files you would like to overwrite:';
$lang['wproCore_fileBrowser']['JSReservedNameError'] = 'This name is reserved. Please choose another.';
$lang['wproCore_fileBrowser']['unknownError'] = 'An unknown error occurred, the file may be in use by another application, or the server may not have permission to modify it.';
$lang['wproCore_fileBrowser']['fileNameError'] = 'Bad file name, please choose a name consisting of only letters, numbers and single spaces.';
$lang['wproCore_fileBrowser']['JSFileNameError'] = 'Bad file name, please choose a name consisting of only letters, numbers and single spaces.';
$lang['wproCore_fileBrowser']['fileNameTakenError'] = 'A file with this name already exists, please choose another name.';
$lang['wproCore_fileBrowser']['JSFileNameTakenError'] = 'A file with this name already exists, please choose another name.';
$lang['wproCore_fileBrowser']['uploadExceedsQuota'] = '<strong>Uploading these files exceeds your disk quota.</strong> <br />Try uploading smaller files or deleting some of your existing files.';

/* image editor */
$lang['wproCore_fileBrowser']['resizeToFitWithin'] = 'Resize to fit within:';
$lang['wproCore_fileBrowser']['custom'] = 'Custom...';
$lang['wproCore_fileBrowser']['go'] = 'Go';
$lang['wproCore_fileBrowser']['previousImage'] = 'Previous Image';
$lang['wproCore_fileBrowser']['nextImage'] = 'Next Image';
$lang['wproCore_fileBrowser']['rotateLeft'] = 'Rotate Anti-Clockwise';
$lang['wproCore_fileBrowser']['rotateRight'] = 'Rotate Clockwise';
$lang['wproCore_fileBrowser']['save'] = 'Save';
$lang['wproCore_fileBrowser']['saveAs'] = 'Save As...';
$lang['wproCore_fileBrowser']['JSRotateWarning'] = 'Rotating this image may result in a loss of picture quality. Proceed?';
$lang['wproCore_fileBrowser']['JSSaveChanges'] = 'Save changes?';
$lang['wproCore_fileBrowser']['editPermissionsError'] = 'Sorry you do not have permission to edit images in this directory.';
$lang['wproCore_fileBrowser']['JSEditPermissionsError'] = 'Sorry you do not have permission to edit images in this directory.';
$lang['wproCore_fileBrowser']['JSEditMemoryError'] = 'This image is too large. There is not enough memory available to complete the task.';
$lang['wproCore_fileBrowser']['JSSaveActionError'] = 'The image could not be saved, it may be in use by another application, or the server may not have permission to modify it.';
$lang['wproCore_fileBrowser']['JSEditActionError'] = 'The image could not be edited, it may be in use by another application, or the server may not have permission to modify it.';
$lang['wproCore_fileBrowser']['editImageExtensionError'] = 'Images of this type cannot be edited.';

/* move-copy */
$lang['wproCore_fileBrowser']['selectDestinationFolder'] = 'Please choose a destination folder:';
$lang['wproCore_fileBrowser']['overwrite'] = 'Overwrite existing files.';
$lang['wproCore_fileBrowser']['goToDestination'] = 'Go to destination folder when finished.';
$lang['wproCore_fileBrowser']['moveErrorsOccurred'] = 'Some errors occurred while attempting to move or copy your file(s):';
$lang['wproCore_fileBrowser']['insideItselfError'] = 'A folder cannot be moved or copied inside of itself.';
$lang['wproCore_fileBrowser']['allOtherFilesMovedSuccessfully'] = 'All other files were moved or copied successfully.';
$lang['wproCore_fileBrowser']['moveSourcePermissionsError'] = 'Sorry you do not have permission to move files or folders from the source directory.';
$lang['wproCore_fileBrowser']['copySourcePermissionsError'] = 'Sorry you do not have permission to copy files or folders from the source directory.';
$lang['wproCore_fileBrowser']['moveDestinationPermissionsError'] = 'Sorry you do not have permission to move files to the destination directory.';
$lang['wproCore_fileBrowser']['copyDestinationPermissionsError'] = 'Sorry you do not have permission to copy files to the destination directory.';
$lang['wproCore_fileBrowser']['moveReservedNameError'] = 'This file name is reserved in the destination directory.';

/* new folder */
$lang['wproCore_fileBrowser']['enterNewFolderName'] = 'Please enter a name for the new folder:';
$lang['wproCore_fileBrowser']['JSNewFolderPermissionsError'] = 'Sorry you do not have permission to create new folders in this directory.';
$lang['wproCore_fileBrowser']['JSFolderExistsError'] = 'A folder with this name already exists. Please choose a different name.';
$lang['wproCore_fileBrowser']['JSNewFolderActionError'] = 'The folder could not be created. The server may not have permission to modify this directory.';

/* rename */
$lang['wproCore_fileBrowser']['JSRenamePermissionsError'] = 'Sorry you do not have permission to rename files or folders in this directory.';
$lang['wproCore_fileBrowser']['JSRenameFilesPermissionsError'] = 'Sorry you do not have permission to rename files in this directory.';
$lang['wproCore_fileBrowser']['JSRenameFoldersPermissionsError'] = 'Sorry you do not have permission to rename folders in this directory.';
$lang['wproCore_fileBrowser']['JSRenameFileNotFound'] = 'The following file or folder could not be found, it may have been deleted by another user:';
$lang['wproCore_fileBrowser']['JSRenameActionError'] =  'One or more files or folders cannot be renamed, they may be in use by another application, or the server may not have permission to modify them.';
$lang['wproCore_fileBrowser']['nameTaken'] = '<em>##oldname##</em> could not be renamed <em>##newname##</em> because a file with this name already exists in this location.';
$lang['wproCore_fileBrowser']['illegalCharacters'] = '<em>##oldname##</em> could not be renamed <em>##newname##</em> because it contains illegal characters. Only letters, numbers and single spaces are allowed.';
$lang['wproCore_fileBrowser']['nameReserved'] = '<em>##oldname##</em> could not be renamed <em>##newname##</em> because this name has been reserved.';
$lang['wproCore_fileBrowser']['enterNewName'] = 'Please enter a new name for <em>##oldname##</em>:';

/* upload */
$lang['wproCore_fileBrowser']['JSUploadFailed'] = 'Upload failed. 
It appears you were attempting to upload a file or files that were too large. 
Most web browsers do not support uploading of more than 8 MB of data. 
This server cannot accept more than ##maxsize## of data.';
$lang['wproCore_fileBrowser']['chooseFiles'] = 'Choose Files:';
$lang['wproCore_fileBrowser']['uploadInProgress'] = 'Upload in progress. Please Wait...';
$lang['wproCore_fileBrowser']['filesMustBe'] = 'Files must be in ##extensions## format.';
$lang['wproCore_fileBrowser']['smallerThan'] = 'Each file must be smaller than ##maxsize##.';
$lang['wproCore_fileBrowser']['imageDimensions'] = 'Image dimensions must be less than ##maxwidth## x ##maxheight##.';
$lang['wproCore_fileBrowser']['noToUpload'] = 'Number of files to upload:';
$lang['wproCore_fileBrowser']['upTo5'] = 'Up to 5';
$lang['wproCore_fileBrowser']['upTo10'] = 'Up to 10';
$lang['wproCore_fileBrowser']['upTo15'] = 'Up to 15';
$lang['wproCore_fileBrowser']['upTo20'] = 'Up to 20';
$lang['wproCore_fileBrowser']['resizeLargerThan'] = 'Resize images larger than:';
$lang['wproCore_fileBrowser']['combinedSize'] = 'Total combined file-size for all files must be less than ##maxsize##.';
$lang['wproCore_fileBrowser']['uploadPermissionsError'] = 'Sorry you do not have permission to upload files to this directory.';
$lang['wproCore_fileBrowser']['JSUploadPermissionsError'] = 'Sorry you do not have permission to upload files to this directory.';
$lang['wproCore_fileBrowser']['uploadErrorsOccurred'] = 'Some errors occurred while uploading your files, the following files were not uploaded:';
$lang['wproCore_fileBrowser']['dimensionsTooLarge'] = 'This image is too large and could not be resized. Image dimensions must be less than ##maxwidth## x ##maxheight##.';
$lang['wproCore_fileBrowser']['dimensionsTooLargeNoGD'] = 'This image is too large. Image dimensions must be less than ##maxwidth## x ##maxheight##.';
$lang['wproCore_fileBrowser']['badExtension'] = 'Files of this type are not allowed in this directory, only ##extensions## file types please.';
$lang['wproCore_fileBrowser']['tooLarge'] = 'This file is too large, files must be less than ##maxsize##.';
$lang['wproCore_fileBrowser']['uploadUnknownError'] = 'An unknown error occurred while uploading this file. Check that the server has permission to modify this directory.';
$lang['wproCore_fileBrowser']['allOtherFilesUploadedSuccessfully'] = 'All other files were uploaded successfully.';
$lang['wproCore_fileBrowser']['uploadExceedsQuota'] = '<strong>Uploading these files exceeds your disk quota.</strong> <br />Try uploading smaller files or deleting some of your existing files.';

/* delete */
$lang['wproCore_fileBrowser']['JSDeleteWarning'] = 'Warning: The selected files and folders will be permanently deleted. Proceed?';
$lang['wproCore_fileBrowser']['JSDeletePermissionsError'] = 'Sorry you do not have permission to delete files or folders in this directory.';
$lang['wproCore_fileBrowser']['JSDeleteFoldersPermissionsError'] = 'Sorry you do not have permission to delete folders in this directory.';
$lang['wproCore_fileBrowser']['JSDeleteFilesPermissionsError'] = 'Sorry you do not have permission to delete files in this directory.';
$lang['wproCore_fileBrowser']['JSDeleteActionError'] = 'One or more files or folders cannot be deleted, they may be in use by another application, or the server may not have permission to delete them.';

/* outlook bar */
$lang['wproCore_fileBrowser']['pageOnThisSite'] = 'Page on this site';
$lang['wproCore_fileBrowser']['properties'] = 'Properties';
$lang['wproCore_fileBrowser']['webLocation'] = 'Web location';
$lang['wproCore_fileBrowser']['emailAddress2'] = 'E-mail address';
$lang['wproCore_fileBrowser']['placeInThisDocument'] = 'Place in this document';

/* file plugins */
$lang['wproCore_fileBrowser']['showall'] = 'Show all';
$lang['wproCore_fileBrowser']['noborder'] = 'No border';
$lang['wproCore_fileBrowser']['exactfit'] = 'Exact fit';
$lang['wproCore_fileBrowser']['noscale'] = 'No scale';
$lang['wproCore_fileBrowser']['scale'] = 'Scale:';

$lang['wproCore_fileBrowser']['autoplay'] = 'Auto play:';
$lang['wproCore_fileBrowser']['loop'] = 'Loop:';
$lang['wproCore_fileBrowser']['controller'] = 'Controller:';
$lang['wproCore_fileBrowser']['posterMovie'] = 'Poster movie:';

$lang['wproCore_fileBrowser']['resetDimensions'] = 'Reset dimensions';
$lang['wproCore_fileBrowser']['border'] = 'Border:';
$lang['wproCore_fileBrowser']['textFlow'] = 'Text flow:';
$lang['wproCore_fileBrowser']['textTop'] = 'Text top';
$lang['wproCore_fileBrowser']['textMiddle'] = 'Text middle';
$lang['wproCore_fileBrowser']['textBottom'] = 'Text bottom';
$lang['wproCore_fileBrowser']['floatLeft'] = 'Float left';
$lang['wproCore_fileBrowser']['floatRight'] = 'Float right';

$lang['wproCore_fileBrowser']['image'] = 'Image';
$lang['wproCore_fileBrowser']['positioning'] = 'Positioning:';
$lang['wproCore_fileBrowser']['distanceToText'] = 'Distance to surrounding text:';
$lang['wproCore_fileBrowser']['left'] = 'Left:';
$lang['wproCore_fileBrowser']['right'] = 'Right:';
$lang['wproCore_fileBrowser']['top'] = 'Top:';
$lang['wproCore_fileBrowser']['bottom'] = 'Bottom:';
$lang['wproCore_fileBrowser']['positioningPreview'] = 'Positioning preview:';

$lang['wproCore_fileBrowser']['objectTag'] = 'Object tag';
$lang['wproCore_fileBrowser']['classid'] = 'Class ID:';
$lang['wproCore_fileBrowser']['codebase'] = 'Base:';
$lang['wproCore_fileBrowser']['data'] = 'Source (data):';
$lang['wproCore_fileBrowser']['codetype'] = 'Code type:';
$lang['wproCore_fileBrowser']['type'] = 'Type:';
$lang['wproCore_fileBrowser']['archive'] = 'Archive:';
$lang['wproCore_fileBrowser']['standby'] = 'Standby text:';
$lang['wproCore_fileBrowser']['declare'] = 'Declare:';
$lang['wproCore_fileBrowser']['usemap'] = 'UseMap:';
$lang['wproCore_fileBrowser']['parameters'] = 'Parameters:';
$lang['wproCore_fileBrowser']['embedTag'] = 'Embed tag';
$lang['wproCore_fileBrowser']['src'] = 'Source:';
$lang['wproCore_fileBrowser']['pluginspage'] = 'Plug-ins page:';
$lang['wproCore_fileBrowser']['name'] = 'Name:';
$lang['wproCore_fileBrowser']['value'] = 'Value:';
$lang['wproCore_fileBrowser']['alternateContent'] = 'Alternate content:';

/* file definitions */
$lang['files'] = array();
$lang['files']['folder'] = 'File Folder';
$lang['files']['imageFolder'] = 'Images';
$lang['files']['documentFolder'] = 'Documents';
$lang['files']['mediaFolder'] = 'Media';
$lang['files']['thumbFolder'] = 'Thumbnail Folder';
$lang['files']['html'] = 'Web Page';
$lang['files']['pdf'] = 'Adobe Acrobat PDF';
$lang['files']['doc'] = 'Word Document';
$lang['files']['docx'] = 'Word 2007 Document';
$lang['files']['xl'] = 'Excel Spreadsheet';
$lang['files']['xlsx'] = 'Excel 2007 Spreadsheet';
$lang['files']['ppt'] = 'PowerPoint Presentation';
$lang['files']['pptx'] = 'PowerPoint 2007 Presentation';
$lang['files']['pps'] = 'PowerPoint Slide Show';
$lang['files']['ppsx'] = 'PowerPoint 2007 Slide Show';
$lang['files']['rtf'] = 'Rich Text';
$lang['files']['txt'] = 'Plain Text';
$lang['files']['zip'] = 'Zip Archive';
$lang['files']['tar'] = 'Tar Archive';
$lang['files']['gzip'] = 'Gzip Archive';
$lang['files']['bzip'] = 'Bzip Archive';
$lang['files']['sit'] = 'Stuffit Archive';
$lang['files']['dmg'] = 'Disk Image';
$lang['files']['swf'] = 'Flash Movie';
$lang['files']['flv'] = 'Flash Video';
$lang['files']['wmv'] = 'Windows Media';
$lang['files']['mp3'] = 'MP3 Audio';
$lang['files']['mp4'] = 'MP4 Video/Audio File';
$lang['files']['rm'] = 'Real Media';
$lang['files']['mov'] = 'QuickTime';
$lang['files']['xspf'] = 'Flash Media Playlist';
$lang['files']['asx'] = 'Windows Media Playlist';
$lang['files']['wpl'] = 'Windows Media Playlist';
$lang['files']['jpg'] = 'JPEG Image';
$lang['files']['gif'] = 'GIF Image';
$lang['files']['png'] = 'PNG Image';
$lang['files']['exe'] = 'Application';
$lang['files']['file'] = 'File';
$lang['files']['other'] = 'Other Media';
?>