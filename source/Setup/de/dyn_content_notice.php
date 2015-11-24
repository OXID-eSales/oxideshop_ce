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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

require_once "lang.php";

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
    <title><?php echo( $aLang['HEADER_META_MAIN_TITLE'] ) ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo( $aLang['charset'] ) ?>">
    <style type="text/css">
       body, p, td, tr, ol, ul, input, textarea {font:11px/130% Trebuchet MS, Tahoma, Verdana, Arial, Helvetica, sans-serif;}
    </style>
</head>    

<body>
     <p>
     <?php
        echo $aLang['LOAD_DYN_CONTENT_NOTICE'];
     ?>
     </p>
</body>
    
</html>
