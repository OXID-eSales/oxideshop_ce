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
 * Theme Information
 */
$aTheme = array(
    'id'           => 'azure',
    'title'        => 'Azure',
    'description'  => 'Azure theme by OXID eSales AG',
    'thumbnail'    => 'theme.jpg',
    'version'      => '1.3.1',
    'author'       => 'OXID',
    //Define the following variables for the custom child themes:
    //'parentTheme'    => 'azure',
    //'parentVersions' => array('0.5','0.6'),
    
    'settings' => array(
        array('group' => 'images',          'name' => 'sManufacturerIconsize',          'type' => 'str',    'constraints' => '',                    'value' => '100*100'),
        array('group' => 'images',          'name' => 'sCatIconsize',                   'type' => 'str',    'constraints' => '',                    'value' => '168*100'),
        array('group' => 'images',          'name' => 'sCatPromotionsize',              'type' => 'str',    'constraints' => '',                    'value' => '370*107'),
        array('group' => 'images',          'name' => 'sIconsize',                      'type' => 'str',    'constraints' => '',                    'value' => '87*87'),
        array('group' => 'images',          'name' => 'sThumbnailsize',                 'type' => 'str',    'constraints' => '',                    'value' => '185*150'),
        array('group' => 'images',          'name' => 'sCatThumbnailsize',              'type' => 'str',    'constraints' => '',                    'value' => '748*150'),
        array('group' => 'images',          'name' => 'sZoomImageSize',                 'type' => 'str',    'constraints' => '',                    'value' => '665*665'),
        array('group' => 'images',          'name' => 'aDetailImageSizes',              'type' => 'aarr',   'constraints' => '',                    'value' => array(
                                                                                                                                                                    'oxpic1' => '380*340',
                                                                                                                                                                    'oxpic2' => '380*340',
                                                                                                                                                                    'oxpic3' => '380*340',
                                                                                                                                                                    'oxpic4' => '380*340',
                                                                                                                                                                    'oxpic5' => '380*340',
                                                                                                                                                                    'oxpic6' => '380*340',
                                                                                                                                                                    'oxpic7' => '380*340',
                                                                                                                                                                    'oxpic8' => '380*340',
                                                                                                                                                                    'oxpic9' => '380*340',
                                                                                                                                                                    'oxpic10' => '380*340',
                                                                                                                                                                    'oxpic11' => '380*340',
                                                                                                                                                                    'oxpic12' => '380*340',
                                                                                                                                                                )
        ),
        array('group' => 'features',        'name' => 'bl_showCompareList',             'type' => 'bool',   'constraints' => '',                    'value' => '1'),
        array('group' => 'features',        'name' => 'bl_showListmania',               'type' => 'bool',   'constraints' => '',                    'value' => '1'),
        array('group' => 'features',        'name' => 'bl_showWishlist',                'type' => 'bool',   'constraints' => '',                    'value' => '1'),
        array('group' => 'features',        'name' => 'bl_showVouchers',                'type' => 'bool',   'constraints' => '',                    'value' => '1'),
        array('group' => 'features',        'name' => 'bl_showGiftWrapping',            'type' => 'bool',   'constraints' => '',                    'value' => '1'),
        array('group' => 'display',         'name' => 'blShowBirthdayFields',           'type' => 'bool',   'constraints' => '',                    'value' => '1'),
        array('group' => 'display',         'name' => 'iTopNaviCatCount',               'type' => 'str',    'constraints' => '',                    'value' => '4'),
        array('group' => 'display',         'name' => 'sDefaultListDisplayType',        'type' => 'select', 'constraints' => 'infogrid|line|grid',  'value' => 'infogrid'),
        array('group' => 'display',         'name' => 'sStartPageListDisplayType',      'type' => 'select', 'constraints' => 'infogrid|line|grid',  'value' => 'infogrid'),
        array('group' => 'display',         'name' => 'blShowListDisplayType',          'type' => 'bool',   'constraints' => '',                    'value' => '1'),
        array('group' => 'display',         'name' => 'iNewBasketItemMessage',          'type' => 'select', 'constraints' => '0|1|2|3',             'value' => '1'),
        array('group' => 'display',         'name' => 'aNrofCatArticles',               'type' => 'arr',    'constraints' => '',                    'value' => array('10', '20', '50', '100')),
        array('group' => 'display',         'name' => 'aNrofCatArticlesInGrid',         'type' => 'arr',    'constraints' => '',                    'value' => array('12', '16', '24', '32')),
    ),
);