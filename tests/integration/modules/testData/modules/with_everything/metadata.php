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
 * @package   main
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 * @version   SVN: $Id: metadata.php 25466 2010-02-01 14:12:07Z alfonsas $
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'with_everything',
    'title'        => 'Test extending 1 shop class',
    'description'  => 'Module testing extending 1 shop class',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend'       => array(
        'oxarticle' => 'with_everything/myarticle',
        'oxorder' => array(
            'with_everything/myorder1',
            'with_everything/myorder2',
            'with_everything/myorder3',
        ),
        'oxuser' => 'with_everything/myuser',
    ),
    'blocks' => array(
        array('template' => 'page/checkout/basket.tpl',  'block'=>'basket_btn_next_top',    'file'=>'/views/blocks/page/checkout/myexpresscheckout.tpl'),
        array('template' => 'page/checkout/basket.tpl',  'block'=>'basket_btn_next_bottom', 'file'=>'/views/blocks/page/checkout/myexpresscheckout.tpl'),
        array('template' => 'page/checkout/payment.tpl', 'block'=>'select_payment',         'file'=>'/views/blocks/page/checkout/mypaymentselector.tpl'),
    ),
    'templates' => array(
        'order_special.tpl'      => 'with_everything/views/admin/tpl/order_special.tpl',
        'user_connections.tpl'   => 'with_everything/views/tpl/user_connections.tpl',
    ),
    'files' => array(
        'myException'  => 'with_everything/core/exception/myexception.php',
        'myConnection' => 'with_everything/core/exception/myconnection.php',
    ),

);