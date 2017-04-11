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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'extending_3_blocks',
    'title'        => 'Test extending only 3 blocks classes',
    'description'  => 'Module testing extending 3 blocks classes',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'blocks' => array(
        array('template' => 'page/checkout/basket.tpl',  'block'=>'basket_btn_next_top',    'file'=>'/views/blocks/page/checkout/myexpresscheckout.tpl'),
        array('template' => 'page/checkout/basket.tpl',  'block'=>'basket_btn_next_bottom', 'file'=>'/views/blocks/page/checkout/myexpresscheckout.tpl'),
        array('template' => 'page/checkout/payment.tpl', 'block'=>'select_payment',         'file'=>'/views/blocks/page/checkout/mypaymentselector.tpl'),
    ),
);