<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
  In case you need to extend current generator class:
    - create some alternative file;
    - edit htaccess file and replace core/utils/getimg.php with your custom handler;
    - add here function "getGeneratorInstanceName()" which returns name of your generator class;
    - implement class and required methods which extends "oxdynimggenerator" class
    e.g.:

      file name "testgenerator.php"

      function getGeneratorInstanceName()
      {
          return "testImageGenerator";
      }
      include_once "oxdynimggenerator.php";
      class testImageGenerator extends oxdynimggenerator.php {...}
*/

// including generator class
require_once "core/oxdynimggenerator.php";

// rendering requested image
oxDynImgGenerator::getInstance()->outputImage();
