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

/**
 * Module information
 */
$aModule = array(
    'id'           => 'test8',
    'title'        => 'Test module #8 (in vendor dir, does not extend any class)',
    'description'  => 'Adds PayPal logo in partner box',
    'thumbnail'    => 'module.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales',
    'url'          => 'http://www.oxid-esales.com',
    'email'        => 'info@oxid-esales.com',
    'extend'       => array(
    ),
    'blocks'    => array(
        array("template"=>"widget/sidebar/partners.tpl", "block"=>"partner_logos", "file"=>"oepaypalpartnerbox.tpl"),
    ),
    
);