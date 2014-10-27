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

$aLang = array(
'charset'                                       => 'ISO-8859-15',

'SHOP_THEME_GROUP_images'                       => 'Images',
'SHOP_THEME_GROUP_features'                     => 'Features',
'SHOP_THEME_GROUP_display'                      => 'Display',

'SHOP_THEME_sIconsize'                          => 'Product icon size (width*height)',//SHOP_CONFIG_ICONSIZE
'HELP_SHOP_THEME_sIconsize'                     => 'Product icons are the smallest pictures of a product. They are used: <br>' .
                                                 '<ul><li>in the shopping cart.</li>' .
                                                 '<li>if products are shown in the right menu (e.g. in <span class="filename_filepath_or_italic">TOP of the Shop</span> and <span class="filename_filepath_or_italic">Bargain</span>).</li></ul>' .
                                                 'For avoiding design issues caused by too big icons the icons are resized. Enter the maximum size for icons here.',

'SHOP_THEME_sThumbnailsize'                     => 'Product thumbnail size (width*height)',//SHOP_CONFIG_THUMBNAILSIZE
'HELP_SHOP_THEME_sThumbnailsize'                => 'Product thumbnails are small product pictures. They are used:<br>' .
                                                 '<ul><li>in product lists.</li>' .
                                                 '<li>in promotions displayed in the middle of the front page, e. g. <span class="filename_filepath_or_italic">Just arrived!</span>.</li></ul>' .
                                                 'For avoiding design issues caused by too big thumbnails the thumbnails are resized. Enter the maximum size for thumbnails here.',

'SHOP_THEME_sZoomImageSize'                     => 'Product zoom picture size (width*height)',//SHOP_CONFIG_ZOOMIMAGESIZE
'SHOP_THEME_sCatThumbnailsize'                  => 'Category thumbnail size (width*height)',//SHOP_CONFIG_CATEGORYTHUMBNAILSIZE
'HELP_SHOP_THEME_sCatThumbnailsize'             => 'In category view, the picture of the selected category is displayed in the defined size.',//SHOP_CONFIG_CATEGORYTHUMBNAILSIZE
'SHOP_THEME_aDetailImageSizes'                  => 'Product picture size (width*height)',//SHOP_CONFIG_DETAILIMAGESIZE

'SHOP_THEME_sManufacturerIconsize'              => 'Manufacturer\'s/brand logo size', // Check if this is really manufacturer or if it is more like "brand"
'HELP_SHOP_THEME_sManufacturerIconsize'         => 'Manufacturer\'s/brand logo is shown on start page in manufacturer\'s slider.',

'SHOP_THEME_sCatIconsize'                       => 'Size of a subcategory\'s icon (width*height)',
'HELP_SHOP_THEME_sCatIconsize'                  => 'In category view, the category pictures of subcategories are displayed in the defined size.',

'SHOP_THEME_sCatPromotionsize'                  => 'Category picture size for promotion on startpage (width*height)',
'HELP_SHOP_THEME_sCatPromotionsize'             => 'Category promotion on start page needs special size for category pictures. Define size of those pictures here.',

'SHOP_THEME_bl_showOpenId'                      => 'Use Open ID',         //SHOP_CONFIG_SHOWOPENID
'SHOP_THEME_bl_showGiftWrapping'                => 'Use gift wrapping',   //SHOP_CONFIG_SHOWGIFTWRAPPING
'SHOP_THEME_bl_showVouchers'                    => 'Use vouchers',        //SHOP_CONFIG_SHOWVOUCHERS
'SHOP_THEME_bl_showWishlist'                    => 'Use gift registry',   //SHOP_CONFIG_SHOWWISHLIST
'SHOP_THEME_bl_showCompareList'                 => 'Use compare list',    //SHOP_CONFIG_SHOWCOMPARELIST
'SHOP_THEME_bl_showListmania'                   => 'Use listmania',       //SHOP_CONFIG_SHOWLISTMANIA
'SHOP_THEME_blShowBirthdayFields'               => 'Display input fields for date of birth when users enter their personal data',//SHOP_CONFIG_SHOWBIRTHDAYFIELDS

'SHOP_THEME_iTopNaviCatCount'                   => 'Amount of categories that is displayed at top',//SHOP_CONFIG_TOPNAVICATCOUNT
'SHOP_THEME_iNewBasketItemMessage'              => 'Select action when product is added to cart',//SHOP_SYSTEM_SHOWNEWBASKETITEMMESSAGE
'HELP_SHOP_THEME_iNewBasketItemMessage'         => 'When customer adds products to cart, OXID eShop can behave differently. Set up what shall happen to give proper feedback to customer.',//SHOP_SYSTEM_SHOWNEWBASKETITEMMESSAGE
'SHOP_THEME_iNewBasketItemMessage_0'            => 'None',
'SHOP_THEME_iNewBasketItemMessage_1'            => 'Display message',
'SHOP_THEME_iNewBasketItemMessage_2'            => 'Open popup',
'SHOP_THEME_iNewBasketItemMessage_3'            => 'Open basket',

'SHOP_THEME_blShowListDisplayType'              => 'Display product list type selector',
'HELP_SHOP_THEME_blShowListDisplayType'         => 'Decide if the visitor of your store can select the type of the product list in store front. If this options is not activated, your visitors will see the lists displayed like you adjusted in the drop box "Default product list type".',
'SHOP_THEME_sDefaultListDisplayType'            => 'Default product list type',
'SHOP_THEME_sDefaultListDisplayType_grid'       => 'Grid',
'SHOP_THEME_sDefaultListDisplayType_line'       => 'List',
'SHOP_THEME_sDefaultListDisplayType_infogrid'   => 'Double grid',
'SHOP_THEME_sStartPageListDisplayType'          => 'Product list type on Start page',
'SHOP_THEME_sStartPageListDisplayType_grid'     => 'Grid',
'SHOP_THEME_sStartPageListDisplayType_line'     => 'List',
'SHOP_THEME_sStartPageListDisplayType_infogrid' => 'Double grid',

'SHOP_THEME_aNrofCatArticlesInGrid'             => 'Grid view: Number of products which can be shown in a product lists (category pages, search results)<br><br>Attention: A large number of products per page (above 100) can cause performance loss!',
'SHOP_THEME_aNrofCatArticles'                   => 'Number of products which can be shown in a product lists (category pages, search results)<br><br>Attention: A large number of products per page (above 100) can cause performance loss!',

);
