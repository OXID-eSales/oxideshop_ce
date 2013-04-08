<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

//simple tests
$aRes = array();

$aRes[] = 'standart tests';
$aRes[] = "testing commented ones";


$aRes[] = "!EE";

$aRes[] = "PE";


$aRes[] = "CE";



$aRes[] = "!PE_PE";




$aRes[] = "testing normal ones";


$aRes[] = "!EE";

$aRes[] = "PE";


$aRes[] = "CE";



$aRes[] = "!PE_PE";


$aRes[] = "testing smarty ones";


$aRes[] = "!EE";

$aRes[] = "PE";



$aRes[] = "!PE_PE";

$aRes[] = "PE_CE";




$aRes[] = '';
$aRes[] = 'single && tests';

$aRes[] = "testing commented ones";


$aRes[] = "!EE && PE"; // same as PE



$aRes[] = "testing normal ones";


$aRes[] = "!EE && PE"; // same as PE



$aRes[] = "testing smarty ones";


$aRes[] = "!EE && PE"; // same as PE




$aRes[] = '';
$aRes[] = 'single || tests';

$aRes[] = "testing commented ones";

$aRes[] = "EE || PE";

$aRes[] = "!EE || PE"; // same as !EE

$aRes[] = "PE || PE_PE"; //same as PE



$aRes[] = "testing normal ones";

$aRes[] = "EE || PE";

$aRes[] = "!EE || PE"; // same as !EE

$aRes[] = "PE || PE_PE"; //same as PE



$aRes[] = "testing smarty ones";

$aRes[] = "EE || PE";

$aRes[] = "!EE || PE";

$aRes[] = "PE || PE_PE";


$aRes[] = "\ntesting tag in tag";
    $aRes[] = "1!EE";
        $aRes[] = "3CE";
    $aRes[] = "7!PE";
        $aRes[] = "9CE";
    $aRes[] = "10!PE";

foreach ($aRes as $line) {
    echo $line ,"\n";
}
