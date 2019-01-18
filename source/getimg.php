<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * In case you need to extend current generator class:
 *   - create some alternative file;
 *   - edit htaccess file and replace getimg.php with your custom handler;
 *   - add here function "getGeneratorInstanceName()" which returns name of your generator class;
 *   - implement class and required methods which extends "oxdynimggenerator" class
 *   e.g.:
 *
 *     file name "testgenerator.php"
 *
 *     function getGeneratorInstanceName()
 *     {
 *         return "testImageGenerator";
 *     }
 *     include_once "oxdynimggenerator.php";
 *     class testImageGenerator extends oxdynimggenerator.php {...}
*/

// including generator class
require_once __DIR__ . "/bootstrap.php";

// rendering requested image
OxidEsales\EshopCommunity\Core\DynamicImageGenerator::getInstance()->outputImage();
