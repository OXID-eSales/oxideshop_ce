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
 * In this file, the content for help popups is stored:
 *
 * Syntax for identifier: HELP_TABNAME_INPUTNAME, e.g. HELP_SHOP_CONFIG_BIDIRECTCROSS.
 * !!!The INPUTNAME is same as in lang.php for avoiding even more different Identifiers.!!!
 * In some cases, in lang.php GENERAL_ identifiers are used. In this file, always the tab name is used.
 *
 *
 * HTML Tags for markup (same as in online manual):
 * <span class='navipath_or_inputname'>...</span> for names of input fields, selectlists and Buttons, e.g. <span class='navipath_or_inputname'>Active</span>
 * <span class='userinput_or_code'>...</span> for input in input fields (also options in selectlists) and code
 * <span class='filename_filepath_or_italic'>...</span> for filenames, filepaths and other italic stuff
 * <span class='warning_or_important_hint'>...</span> for warning and important things
 * <ul> and <li> for lists
 */

$aLang =  array(
'charset'                                     => 'ISO-8859-15',

'HELP_SHOP_SYSTEM_OTHERCOUNTRYORDER'          => 'Here you can set if orders can be made in countries for which no shipping costs are defined:' .
                                                 '<ul><li>If the setting is checked, users can order: The users are notified that they are informed about the shipping costs manually.</li>' .
                                                 '<li>If the setting is unchecked, users from countries for which no shipping costs are defined cannot order.</li></ul>',

'HELP_SHOP_SYSTEM_DISABLENAVBARS'             => 'If this setting is checked, most navigation elements aren\'t shown during checkout. Thereby users aren\'t distracted unnecessarily during checkout.',

'HELP_SHOP_SYSTEM_DEFAULTIMAGEQUALITY'        => 'Recommended settings are from 40-80:<br>' .
                                                 '<ul><li>Under 40, the compression gets clearly visible and the pictures are blurred.</li>'.
                                                 '<li>Above 80 hardly any quality improvement can be detected, but the filesize increases enormously.</li></ul><br>'.
                                                 'The default value is 75.',

'HELP_SHOP_SYSTEM_DENIEDDYNGROUPS'            => 'Enable this option to filtering user groups, which are handled over "dgr" param in the URL.',

'HELP_SHOP_SYSTEM_LDAP'                       => 'Please edit file core/oxldap.php.',

'HELP_SHOP_SYSTEM_SHOWVARIANTREVIEWS'         => 'This setting affects how reviews for variants are handled: If the setting is checked, remarks from variants are also shown at the parent product.',

'HELP_SHOP_SYSTEM_VARIANTSSELECTION'          => 'In eShop there are many lists for assigning products, e.g. assigning products to discounts. If this setting is checked, variants are shown in these lists, too.',

'HELP_SHOP_SYSTEM_VARIANTPARENTBUYABLE'       => 'This setting affects if parent products can be bought:' .
                                                 '<ul><li>If the setting is checked, the parent products can be bought, too.</li>' .
                                                 '<li>If the setting is unchecked, only variants of the parent product can be bought.</li></ul>',

'HELP_SHOP_SYSTEM_VARIANTINHERITAMOUNTPRICE'  => 'Here you can set whether scales prices are inherited from the parent product: If the setting is checked, the scale prices of the parent product are also used for its variants.',

'HELP_SHOP_SYSTEM_ISERVERTIMESHIFT'           => 'The server the eShop is running on can be in a different time zone. With this setting the time shift can be adjusted: Enter the amount of hours that are to be added/subtracted from the server time, e. g. <kdb>+2</kdb> or <kdb>-2</kdb>',

'HELP_SHOP_SYSTEM_INLINEIMGEMAIL'             => 'If the setting is checked, the pictures in e-mails are sent together with the e-mail. If the setting is unchecked, the pictures are downloaded by the e-mail program when the e-mail is opened.',

'HELP_SHOP_SYSTEM_SHOP_LOCATION'              => 'Please choose the market to which shop is focused. According to it, additional eCommerce Services will be loaded from OXID server. Do not forget to enable option "Load additional Information from OXID server" in Master Settings -> Core Settings -> Settings -> Administration',

'HELP_SHOP_SYSTEM_UTILMODULE'                 => 'Please enter your custom PHP file here, which will overwrite eShop functions at shop start.',


'HELP_SHOP_CACHE_ENABLED'                     => 'If Dynamic content caching is active, additional contents are cached for increasing performance. Deactivate this setting as long as you adapt the Shop (writing modules, adapting templates and so on).',

'HELP_SHOP_CACHE_LIFETIME'                    => 'Here you set how many seconds cached contents are saved at most before they are recreated. The default setting is 36000 seconds.',

'HELP_SHOP_CACHE_CLASSES'                     => 'Here you set which view classes are cached.<br> Only change this setting if you are familiar with the caching mechanisms!',

'HELP_SHOP_CACHE_REVERSE_PROXY_ENABLED'       => 'Activates a caching HTTP Reverse Proxy. Note: Do not use "Dynamic Content Caching" at the same time, as it could slow down the performance.',

'HELP_SHOP_CACHE_LAYOUT_CACHE_AGE'            => 'Sets lifetime of page layout in seconds. This will be send via HTTP headers, specifying header value "Age".',



'HELP_SHOP_CONFIG_ORDEROPTINEMAIL'            => 'If double-opt-in is active, users get an e-mail with a confirmation link when they register for the newsletter. Only if this confirmation link is used the user is registered for the newsletter.<br>' .
                                                 'Double-opt-in protects users from unwanted registrations. Without double-opt-in, any e-mail address can be registered for the newsletter. With double-opt-in, the owner of the e-mail address has to confirm the registration.',

'HELP_SHOP_CONFIG_BIDIRECTCROSS'              => 'With crossselling you can offer fitting products for a product: If e.g. to a car tires are assigned as crossselling product, the tires are shown with the car.<br>' .
                                                 'If bidirectional crossselling is activated, it works in both directions: The car is shown with the tires, too.',

'HELP_SHOP_CONFIG_STOCKONDEFAULTMESSAGE'      => 'For each product you can set up a message if the product is on stock.<br>' .
                                                 'If this setting is active, a message is shown if no specific message for for a product is entered. The default message <span class="filename_filepath_or_italic">Ready for shipping</span> is shown.',

'HELP_SHOP_CONFIG_STOCKOFFDEFAULTMESSAGE'     => 'For each product you can set up a message if the product is not in stock.<br>' .
                                                 'If this setting is active, a message is shown if no specific message for for a product is entered. The default message <span class="filename_filepath_or_italic">This item is not in stock and must be back-ordered</span> is shown.',

'HELP_SHOP_CONFIG_OVERRIDEZEROABCPRICES'      => 'You can set up special prices for specific users: For each product you can enter A, B and C prices. If users are in the user group <span class="filename_filepath_or_italic">Price A</span>, the A price is shown to them instead of the normal price.<br>' .
                                                 'If this setting is checked, the normal product price is used if no A, B or C price is available.<br>' .
                                                 'You should activate this setting if you are using A, B and C prices: Otherwise 0,00 is displayed to the according users if no A, B or C price is set.',

'HELP_SHOP_CONFIG_SEARCHFIELDS'               => 'Here you can define the database fields in which the product search searches. Enter one field per row.<br>' .
                                                 'The most common entries are:' .
                                                 '<ul><li>oxtitle = Title</li>' .
                                                 '<li>oxshortdesc = Short description</li>' .
                                                 '<li>oxsearchkeys = Search terms entered for each product</li>' .
                                                 '<li>oxartnum = Product number</li>' .
                                                 '<li>oxtags    = Tags entered for each product</li></ul>',

'HELP_SHOP_CONFIG_SORTFIELDS'                 => 'Here you can define the database fields which can be used for sorting product lists. Enter one field per row.<br>' .
                                                 'The most common entries are:' .
                                                 '<ul><li>oxtitle = Title</li>' .
                                                 '<li>oxprice = Price</li>' .
                                                 '<li>oxvarminprice = The lowest price if variants with different prices are used.</li>' .
                                                 '<li>oxartnum = Product numbers</li>' .
                                                 '<li>oxrating = Rating of the products</li>' .
                                                 '<li>oxstock = Stock</li></ul>',

'HELP_SHOP_CONFIG_MUSTFILLFIELDS'             => 'Here you can set the mandatory fields for user registration. Enter one field per row.<br>' .
                                                 'The most common entries are:' .
                                                 '<ul><li>oxuser__oxfname = First name</li>' .
                                                 '<li>oxuser__oxlname = Last name</li>' .
                                                 '<li>oxuser__oxstreet = Street</li>' .
                                                 '<li>oxuser__oxstreetnr = House number</li>' .
                                                 '<li>oxuser__oxzip = ZIP</li>' .
                                                 '<li>oxuser__oxcity = City</li>' .
                                                 '<li>oxuser__oxcountryid = Country</li>' .
                                                 '<li>oxuser__oxfon = Telephone number</li></ul><br>' .
                                                 'You can also define the mandatory fields if users enter a different delivery address. The most common entries are:' .
                                                 '<ul><li>oxaddress__oxfname = First name</li>' .
                                                 '<li>oxaddress__oxlname = Last name</li>' .
                                                 '<li>oxaddress__oxstreet = Street</li>' .
                                                 '<li>oxaddress__oxstreetnr = House number</li>' .
                                                 '<li>oxaddress__oxzip = ZIP</li>' .
                                                 '<li>oxaddress__oxcity = City</li>' .
                                                 '<li>oxaddress__oxcountryid = Country</li>' .
                                                 '<li>oxaddress__oxfon = Telephone number</li></ul>',

'HELP_SHOP_CONFIG_USENEGATIVESTOCK'           => 'With <span class="navipath_or_inputname">Allow negative Stock Values</span> you can define how stock levels are calculated of products are out of stock:<br>' .
                                                 '<ul><li>If the setting is checked, negative stock values are calculated if further units are bought.</li>' .
                                                 '<li>If the setting is unchecked, the stock value never falls below 0, even if further units are bought.</li></ul>',

'HELP_SHOP_CONFIG_NEWARTBYINSERT'             => 'On the front page of your eShop the newest products are shown in <span class="filename_filepath_or_italic">Just arrived!</span>.  This setting determines how the newest products are calculated: by date of creation or by date of last change in admin/last order.',

'HELP_SHOP_CONFIG_LOAD_DYNAMIC_PAGES'         => 'If this setting is checked, additional information about other OXID products is shown in the menu, e.g. about OXID eFire. Which information is loaded depends on the market of your eShop.',


'HELP_SHOP_CONFIG_DELETERATINGLOGS'           => 'If users rate a product, they cannot rate the product again. Here you can set after how many days users are allowed to rate a product again. Leave empty to disable - products can be rated only once per user.',

'HELP_SHOP_CONFIG_DISABLEONLINEVATIDCHECK'    => 'The online VAT ID check is executed if a customer from a foreign country inside the EU enters a VAT ID when ordering. If the VAT ID is valid, no VAT is calculated for this order.<br>'.
                                                 'If the check is disabled, the normal VAT for the according country is always calculated.',

'HELP_SHOP_CONFIG_ALTVATIDCHECKINTERFACEWSDL' => 'Here you can enter an alternative URL for the online VAT ID check.',

'HELP_SHOP_CONFIG_PSLOGIN'                    => 'Private Sales Login transforms regular shop into members ' .
                                                 'only shop. This helps to develop customer communities with similar interests. ' .
                                                 'This setting restricts access to all eShop pages.',

'HELP_SHOP_CONFIG_BASKETEXCLUDE'              => 'Allows to insert products only from one (root) category, if category change is ' .
                                                 'detected user is asked to finalize order (go to checkout) or continue shopping ' .
                                                 '(cart is cleaned in this case). Using this feature in conjunction with ' .
                                                 ' properly organized category structure you can implement split carts for different suppliers.',

'HELP_SHOP_CONFIG_BASKETRESERVATION'          => 'When disabling this option eShop reduces product stock exactly at time' .
                                                 'when order is confirmed by customer and checkout is finished.<br><br> ' .
                                                 'With this option enabled this functionality changes: Product stock reduces ' .
                                                 'and is being <b>reserved</b> when product is put into cart. Reservation is '.
                                                 'canceled when cart content is bought or cart expires.',

'HELP_SHOP_CONFIG_BASKETRESERVATIONTIMEOUT'   => 'After this timeout the reserved products are returned to stock and customer\'s cart is cleared.',

'HELP_SHOP_CONFIG_INVITATION'                 => 'Invitation is used for inviting your ' .
                                                 'friends to website and getting credit points for inviting.',

'HELP_SHOP_CONFIG_POINTSFORINVITATION'        => 'The amount of credit points user gets who was invited by already ' .
                                                 'registered user. Invited user must register to get these points. Aquired ' .
                                                 'credit points are stored in user record and can be used by shop owner for any purpose.',

'HELP_SHOP_CONFIG_POINTSFORREGISTRATION'      => 'The amount of credit points user gets who invited somebody to shop. ' .
                                                 'User gets credit points only if invited user registers into shop. Aquired ' .
                                                 'credit points are stored in user record and can be used by shop owner for any purpose.',

'HELP_SHOP_CONFIG_SHOP_CONFIG_FACEBOOKAPPID'  => 'To connect your website to Facebook you need to enter the Application ID. ' .
                                                 'How to connect your website to Facebook, you can read in ' .
                                                 '<a href="http://wiki.oxidforge.org/Tutorials/Connecting_website_to_facebook" target="_blank">tutorial</a>.',

'HELP_SHOP_CONFIG_SHOP_CONFIG_FACEBOOKCONFIRM'=> 'To protect customer privacy, the display of the Facebook social plugins must be explicitly confirmed. Only after confirmation data is shared with Facebook.',

'HELP_SHOP_CONFIG_SHOP_CONFIG_FBSECRETKEY'    => 'To ensure secure connection between your site and Facebook, you must ' .
                                                 'enter the Secure Key which you get when registering your website to ' .
                                                 'Facebook. Read in <a href="http://wiki.oxidforge.org/Tutorials/Connecting_website_to_facebook" target="_blank">tutorial</a> ' .
                                                 'how to connect your website to Facebook.',

'HELP_SHOP_CONFIG_FBCOMMENTS'                 => "Comments box easily enables shop visitors to comment on your shop's content.",

'HELP_SHOP_CONFIG_FBFACEPILE'                 => "Facepile shows profile pictures of shops's visitor's friends who " .
                                                 "have already signed up in Facebook for your site.",

'HELP_SHOP_CONFIG_FBINVITE'                   => 'Shows visitor\'s friends list and allows them to invite friends to your eShop.',

'HELP_SHOP_CONFIG_FBSHARE'                    => 'Shows Facebook share button to share your website content.',

'HELP_SHOP_CONFIG_FBLIKE'                     => 'Enables users to make connections to your pages and share content back to their friends on Facebook with one click.',

'HELP_SHOP_CONFIG_FACEBOOKCONNECT'            => 'Show Facebook Connect box which allows users to log in into eShop using theirs Facebook accunt data.',


'HELP_SHOP_CONFIG_ATTENTION'                  => 'Caution: Even if encryption is used, Credit Card contracts usually prohibit this strictly!',

'HELP_SHOP_CONFIG_SHOWTSINTERNATIONALFEESMESSAGE' => 'In the 4th step of the checkout process the CMS page "oxtsinternationalfees" is additionally displayed.',

'HELP_SHOP_CONFIG_SHOWTSCODMESSAGE'           => 'In the 4th step of the checkout process the CMS page "oxtscodmessage" is additionally displayed.',

'HELP_SHOP_CONFIG_SHOWTAGS'                   => 'If not checked, no tags will be displayed in eShop. Pages that could be called via tag URL, are not accessible.',

'HELP_SHOP_CONFIG_DOWNLOADS'                  => 'Shop with downloadable products. Activate here, that products can be ordered and downloaded.',

'HELP_SHOP_CONFIG_DOWNLOADS_PATH'             => 'Path where files of downloadable products are stored.',

'HELP_SHOP_CONFIG_MAX_DOWNLOADS_COUNT'        => 'Here you can define how many times user can download from the same link after order. This is the default setting for all products.'.
                                                 'You can change this value for every file of product in Administer Products -> Products -> Downloads.',

'HELP_SHOP_CONFIG_LINK_EXPIRATION_TIME_UNREGISTERED' => 'Here you can define how many times user can download from the same link, if user ordered without registration. This is the default setting for all products.'.
                                                 'You can change this value for every file of product in Administer Products -> Products -> Downloads.',

'HELP_SHOP_CONFIG_LINK_EXPIRATION_TIME'       => 'Specify the time in hours, the download link is valid after order. This is the default setting for all products.'.
                                                 'You can change this value for every file of product in Administer Products -> Products -> Downloads.',

'HELP_SHOP_CONFIG_DOWNLOAD_EXPIRATION_TIME'   => 'Specify the time in hours, the download link is valid after the first download. This is the default setting for all products.'.
                                                 'You can change this value for every file of product in Administer Products -> Products -> Downloads.',

'HELP_SHOP_MALL_MALLMODE'                     => 'Here you can set what is shown as front page of this eShop:' .
                                                 '<ul><li><span class="navipath_or_inputname">Show shop selector</span>: A page where the different eShops can be selected is shown.</li>' .
                                                 '<li><span class="navipath_or_inputname">Show main shop front page</span>: The normal front page of this Shop is shown.</li></ul>',

'HELP_SHOP_MALL_PRICEADDITION'                => 'You can define a surcharge on all product prices in this shop: Enter the surcharge and select if its a percental (<span class="userinput_or_code">%</span>) or absolute (<span class="userinput_or_code">abs</span>).',



'HELP_SHOP_PERF_NEWESTARTICLES'               => 'A list of newest products are shown in <span class="filename_filepath_or_italic">Just arrived!</span>. Here you can set how the list is generated:' .
                                                 '<ul><li><span class="userinput_or_code">inactive</span>: The list is not shown.</li>' .
                                                 '<li><span class="userinput_or_code">manual</span>: You can define the products in <span class="navipath_or_inputname">Customer Info -> Promotions -></span> in the promotion <span class="filename_filepath_or_italic">Just arrived!</span>.</li>' .
                                                 '<li><span class="userinput_or_code">automatic</span>: The products are calculated automatically.</li></ul>',

'HELP_SHOP_PERF_TOPSELLER'                    => 'A list of most often sold products is shown in <span class="filename_filepath_or_italic">Top of the Shop</span>. Here you can set how the list is generated:' .
                                                 '<ul><li><span class="userinput_or_code">inactive</span>: The list is not shown.</li>' .
                                                 '<li><span class="userinput_or_code">manual</span>: You can define the products in <span class="navipath_or_inputname">Customer Info -> Promotions -></span> in the promotion <span class="filename_filepath_or_italic">Top of the Shop</span>.</li>' .
                                                 '<li><span class="userinput_or_code">automatic</span>: The products are calculated automatically.</li></ul>',

'HELP_SHOP_PERF_LOADFULLTREE'                 => 'If this setting is checked, the complete category tree is shown in the category navigation (all categories are expanded). This only works if the category navigation is not shown at top.',

'HELP_SHOP_PERF_LOADACTION'                   => 'If this setting is checked, promotions like <span class="filename_filepath_or_italic">Just arrived!</span> and <span class="filename_filepath_or_italic">Top of the Shop</span> are loaded and shown.',

'HELP_SHOP_PERF_LOADREVIEWS'                  => 'Users can rate and comment products. If this setting is checked, the existing reviews/comments are loaded and shown with the product.',

'HELP_SHOP_PERF_USESELECTLISTPRICE'           => 'In selection lists surcharges/discounts can be set up. If this setting is checked, the surcharges/discounts are loaded and applied. If unchecked, the surcharges/discounts aren\'t applied.',

'HELP_SHOP_PERF_DISBASKETSAVING'              => 'The shopping cart of registered users is saved. When they visit your eShop again, the shopping cart contents are loaded. If you activate this setting, the shopping carts aren\'t saved any more.',

'HELP_SHOP_PERF_LOADDELIVERY'                 => 'If you deactivate this setting, no shipping costs are calculated: The shipping costs are always 0.00 EUR.',

'HELP_SHOP_PERF_LOADPRICE'                    => 'If you deactivate this setting, no product prices are calculated: No prices are shown.',

'HELP_SHOP_PERF_PARSELONGDESCINSMARTY'        => 'If this setting is active, the descriptions of products and categories are parsed trough Smarty: You can use Smarty tags (e. g. for using variables) <br>',

'HELP_SHOP_PERF_LOADATTRIBUTES'               => 'Normally attributes are only loaded in the detail view of a product. If the setting is active, the attributes are always loaded with a product.<br>' .
                                                 'This setting can be useful if you want to adept templates, e. g. showing the attributes in product lists also.',

'HELP_SHOP_PERF_LOADSELECTLISTSINALIST'       => 'Normally selection lists are only shown in the detail view of a product. If you activate this setting, the selection lists are also shown in product lists (e. g. search results, categories).',

'HELP_SHOP_PERF_CHECKIFTPLCOMPILE'            => 'If this setting is activated the eShop checks on each call if any templates were changed. If so, the output is recalculated. Activate this setting when adapting templates, deactivate it if the eShop is live for better performance.',

'HELP_SHOP_PERF_CLEARCACHEONLOGOUT'           => 'Usually the complete cache is emptied as soon as you save any changes in the eShop admin. This can lead to performance problems in admin. If this setting is activated, the cache is only emptied when you log out from eShop admin.',





'HELP_SHOP_SEO_TITLEPREFIX'                   => 'Each page has a title. this title is shown in the top bar of the browser window. With <span class="navipath_or_inputname">Title Prefix</span> and <span class="navipath_or_inputname">Title Suffix</span> you can fill in text before and after page titles:<br>' .
                                                 '<ul><li>In <span class="navipath_or_inputname">Title Prefix</span>, enter the text to be displayed in front of the title.</li></ul>',

'HELP_SHOP_SEO_TITLESUFFIX'                   => 'Each page has a title. this title is shown in the top bar of the browser window. With <span class="navipath_or_inputname">Title Prefix</span> and <span class="navipath_or_inputname">Title Suffix</span> you can fill in text before and after page titles:<br>' .
                                                 '<ul><li>In <span class="navipath_or_inputname">Title Suffix</span> enter the text to be displayed behind the title.</li></ul>',

'HELP_SHOP_SEO_IDSSEPARATOR'                  => 'The separator is used if category names and product names consist of several words. The separator is used instead of spaces, e.g. www.youreshop.com/category-name-of-several-words<br>' .
                                                 'If no separator is entered, - is used.',

'HELP_SHOP_SEO_SAFESEOPREF'                   => 'If several products have the same name and are in the same category, they would get the same SEO URL. For avoiding this, the SEO Suffix is attached. If no SEO Suffix is defined, <span class="filename_filepath_or_italic">oxid</span> is used.',

'HELP_SHOP_SEO_RESERVEDWORDS'                 => 'Some URLs are defined in OXID eShop, like www.youreshop.com/admin for accessing eShop admin. If a category was named <span class="filename_filepath_or_italic">admin</span> the SEO URL would be www.youreshop.com/admin too - the category couldn\'t be accessed. Therefore the SEO suffix is attached to these URLs. You can define here which URLs are suffixed automatically.',

'HELP_SHOP_SEO_SKIPTAGS'                      => 'If no META tags are defined for products and categories, the META tags are created automatically. thereby very common words can be omitted. All words entered here are omitted when creating the META tags.',

'HELP_SHOP_SEO_STATICURLS'                    => 'For special pages (e. g. general terms and conditions) you can enter fixed SEO URLs. When selecting a static URL, the normal URL is shown in <span class="navipath_or_inputname">Standard URL</span>. In the input fields below you can define a SEO URL for each language.',



'HELP_SHOP_MAIN_PRODUCTIVE'                   => 'Non-productive eShop mode is intended for eShop installation, configuration, template customization and module debugging phase. As soon as productive mode is turned <span class="warning_or_important_hint">ON</span>, the cache handling and the error reporting behavior is optimized for the live shop.<br>' .
                                                 '<span class="warning_or_important_hint">Activate this setting when the eShop is launched.</span><br>' .
                                                 'Find other important information for going live with OXID eShop in our <a href="http://wiki.oxidforge.org/Tutorials/Check_before_going_live" target="_blank">OXIDforge</a>.',

'HELP_SHOP_MAIN_ACTIVE'                       => 'With <span class="navipath_or_inputname">Active</span> you can enable/disable the complete eShop. If the eShop is disabled, a message saying the eShop is temporary offline is displayed to the users. This can be useful for maintenance.',

'HELP_SHOP_MAIN_INFOEMAIL'                    => 'All e-mails sent via the contact page are sent to this e-mail address.',

'HELP_SHOP_MAIN_ORDEREMAIL'                   => 'When users order they receive an e-mail with a summary of the order. Answers to this e-mail are sent to <span class="navipath_or_inputname">Order e-mail reply</span>.',

'HELP_SHOP_MAIN_OWNEREMAIL'                   => 'When users order, you receive an e-mail with a summary of the order. These e-mails are sent to <span class="navipath_or_inputname">Order e-mails to</span>.',

'HELP_SHOP_MAIN_SMTPSERVER'                   => 'SMTP data is needed for sending e-mails (e.g. sending customers an order confirmation e-mail).',

'HELP_ARTICLE_MAIN_ALDPRICE'                  => 'With <span class="navipath_or_inputname">Alt. Prices</span> you can set up special prices for certain users (user groups "Price A", "Price B" and "Price C").',

'HELP_ARTICLE_MAIN_VAT'                       => 'Here you can enter a special VAT for this product. This VAT is used for this product in all later calculations (cart, order, invoice)',

'HELP_ARTICLE_MAIN_TAGS'                      => 'Here you can enter tags for the product. From these tags the tag cloud on the front page is generated. The tags are separated by a comma.',

'HELP_ARTICLE_EXTEND_UNITQUANTITY'            => 'With <span class="navipath_or_inputname">Quantity</span> and <span class="navipath_or_inputname">Unit</span> you can set the price per quantity unit. The price per quantity unit is calculated and displayed with the product (e.g. 1.43 EUR per liter). In <span class="navipath_or_inputname">Quantity</span>, enter the amount of the product (e.g. <span class="userinput_or_code">1.5</span>), in <span class="navipath_or_inputname">Unit</span> define the according quantity unit (e.g. <span class="userinput_or_code">liter</span>). You can choose unit type from given values or, by selecting blank unit type "-", enter unit type manually. If you wish to append existing type list, please follow this <a href="http://wiki.oxidforge.org/Tutorials/Adding_new_unit_types" target="_blank">link</a> for instructions.',

'HELP_ARTICLE_EXTEND_EXTURL'                  => 'In <span class="navipath_or_inputname">External URL</span> you can enter a link where further information about the product is available (e. g. on the manufacturer\'s website). In <span class="navipath_or_inputname">Text for external URL</span> you can enter the text which is linked, e .g. <span class="userinput_or_code">Further information on the manufacturer\'s website</span>.',

'HELP_ARTICLE_EXTEND_TPRICE'                  => 'In <span class="navipath_or_inputname">RRP</span> you can enter the recommended retail price of the manufacturer. If you enter the RRP it is shown to the users: Above the product price <span class="filename_filepath_or_italic">Reduced from RRP now only</span> is displayed.',

'HELP_ARTICLE_EXTEND_QUESTIONEMAIL'           => 'At <span class="navipath_or_inputname">Alt. Contact</span> you can enter an e-mail address. If users submit questions on this product, they will be sent to this e-mail address. If no e-mail address is entered, the query will be send to the normal info e-mail address.',

'HELP_ARTICLE_EXTEND_NONMATERIAL'             => 'Setting is inherited from Parent product to Variants and applies to the entire product.',

'HELP_ARTICLE_EXTEND_FREESHIPPING'            => 'Setting is inherited from Parent product to Variants and applies to the entire product.',

'HELP_ARTICLE_EXTEND_BLFIXEDPRICE'            => 'Price Alert can be turned off for this product.',

'HELP_ARTICLE_EXTEND_SKIPDISCOUNTS'           => 'If <span class="navipath_or_inputname">Skip all negative discounts</span> is active, negative allowances will not be calculated for this product. These include discounts and vouchers.',

'HELP_ARTICLE_EXTEND_TEMPLATE'                => 'The detail view of a product can be displayed with a different template. For doing so, enter path and name of the template to be used.',

'HELP_ARTICLE_EXTEND_ISCONFIGURABLE'          => 'If the product is customizable, an additional input field is displayed on the products detail page and in the shopping cart. Here customers can enter text for customizing the product.<br><br>'.
                                                 'A typical example are t-shirts which can be imprinted with custom text. In the input field customers can enter the text to be printed on the t-shirt.',

'HELP_ARTICLE_EXTEND_UPDATEPRICE'             => 'Prices can be changed on a defined time. Given fields update standard prices. If you leave price values "0", prices will not be updated.',

'HELP_ARTICLE_FILES_MAX_DOWNLOADS_COUNT'      => 'Here you can define how many times user can download from the same link after order. For this file you can overwrite the default setting, which was defined in Master Settings -> Core Setting -> Settings -> Downloads for all products.',

'HELP_ARTICLE_FILES_LINK_EXPIRATION_TIME_UNREGISTERED' => 'Here you can define how many times user can download from the same link, if user ordered without registration. For this file you can overwrite the default setting, which was defined in Master Settings -> Core Setting -> Settings -> Downloads for all products.',

'HELP_ARTICLE_FILES_LINK_EXPIRATION_TIME'     => 'Specify the time in hours, the download link is valid after order. For this file you can overwrite the default setting, which was defined in Master Settings -> Core Setting -> Settings -> Downloads for all products.',

'HELP_ARTICLE_FILES_NEW'                      => 'Enter the name of a via FTP transferred file or upload a new file here. Note that large files should be uploaded via FTP. File size limitation is valid only when file is uploaded via admin. This limitation depends on the PHP settings of the server and might be edited only there.',

'HELP_ARTICLE_FILES_DOWNLOAD_EXPIRATION_TIME' => 'Specify the time in hours, the download link is valid after the first download. For this file you can overwrite the default setting, which was defined in Master Settings -> Core Setting -> Settings -> Downloads for all products.',

'HELP_ARTICLE_PICTURES_ICON'                  => 'Icons are the smallest pictures of a product. For example, they are used in the shopping cart.<br>'.
                                                 'Uploading custom icon will override icon, generated from the first product picture.<br>' .
                                                 'After uploading, the filename is shown in Icon. If no icon is uploaded yet, --- is displayed.',

'HELP_ARTICLE_PICTURES_THUMB'                 => 'Thumbnails are small product pictures. For example, they are used in product lists (categories, search results).<br>' .
                                                 'Uploading custom thumbnail will override the thumbnail generated from the first product picture.<br>' .
                                                 'After uploading, the filename is shown in Thumbnail. If no thumbnail is uploaded yet, ---- is displayed.',

'HELP_ARTICLE_PICTURES_PIC1'                  => 'Pictures are used in the detail view of a product. You can upload up to 7 pictures per product. After uploading, the filename is shown in the accordant input field. If no picture is uploaded yet, --- is displayed.<br>' .
                                                 'Pictures with a maximum of 2 MB or 1500 * 1500 pixel resolution can be uploaded. This restriction is to avoid problems with the PHP memory limit. After uploading, the main picture, zoom picture, thumbnail and icon will be generated automatically.',

'HELP_ARTICLE_PICTURES_ZOOM1'                 => 'Zoom pictures are extra large pictures which can be opened from the detail view of a product. <br>' .
                                                 'You can upload zoom pictures in <span class="navipath_or_inputname">Zoom X upload</span>. After uploading, the filename is shown in <span class="navipath_or_inputname">Zoom X</span>. If no zoom picture is uploaded yet, <span class="userinput_or_code">nopic.jpg</span> is displayed.',

'HELP_ARTICLE_STOCK_REMINDACTIV'              => 'Setting is inherited from Parent product to Variants and applies to the entire product.',

'HELP_ARTICLE_STOCK_STOCKFLAG'                => 'At <span class="navipath_or_inputname">Delivery status</span> you can select from 4 settings:' .
                                                 '<ul><li><span class="userinput_or_code">Standard</span>: The product can then also be ordered if it is sold out.</li>' .
                                                 '<li><span class="userinput_or_code">External storehouse</span>: The product can always be purchased and is always displayed as <span class="filename_filepath_or_italic">in stock</span>. (The stock level cannot be given for external storehouse. Therefore, the product is always shown as <span class="filename_filepath_or_italic">in stock</span>).</li>' .
                                                 '<li><span class="userinput_or_code">If out of stock, offline</span>: The product is not displayed if it is sold out.</li>' .
                                                 '<li><span class="userinput_or_code">If out of stock, not orderable</span>: The product is displayed if it is sold out but it cannot be ordered.</li></ul>',

'HELP_ARTICLE_IS_DOWNLOADABLE'                => 'Files of this product can be downloaded.',

'HELP_ARTICLE_STOCK_REMINDAMAOUNT'            => 'With <span class="navipath_or_inputname">Send e-mail if stock falls below value</span> you can specify that an e-mail will be sent as soon as the stock level falls below the value entered. Select the check box and then enter the level at which you want to be notified.',

'HELP_ARTICLE_STOCK_DELIVERY'                 => 'Here you can enter the date when the product will be available again if it is sold out. The format is year-month-day, e. g. 2009-02-16.',

'HELP_ARTICLE_SEO_FIXED'                      => 'You can let the eShop recalculate the SEO URLs. A product page gets a new SEO URL if e. g. the title of the product has changed. The setting <span class="navipath_or_inputname">Fixed URL</span> prevents this: If it is active, the old SEO URL is kept and no new SEO URL is calculated.',

'HELP_ARTICLE_SEO_KEYWORDS'                   => 'These keywords are integrated in the HTML sourcecode of the product page (META keywords). This information is used by search engines. Suitable keywords for the product can be entered here. If it\'s left blank, the keywords are generated automatically.',

'HELP_ARTICLE_SEO_DESCRIPTION'                => 'This description is integrated in the HTML sourcecode of the product page (META description). This text is often displayed in result pages of search engines. A suitable description can be entered here. If it\'s left blank, the description is generated automatically.',

'HELP_ARTICLE_SEO_ACTCAT'                     => 'You can define several SEO URLs for products: For certain categories and manufacturer pages. With <span class="navipath_or_inputname">Active Category/Vendor</span> you can select the SEO URL you want to edit.',

'HELP_ARTICLE_STOCK_STOCKTEXT'                => 'Here you can enter a message which is shown if the product is in stock.',

'HELP_ARTICLE_STOCK_NOSTOCKTEXT'              => 'Here you can enter a message which is shown if the product is out of stock.',

'HELP_ARTICLE_STOCK_AMOUNTPRICE_AMOUNTFROM'   => 'In <span class="navipath_or_inputname">Quantity From/To/bis</span> you can set which quantity range the scale price is valid for.',

'HELP_ARTICLE_STOCK_AMOUNTPRICE_PRICE'        => 'Here you can set the price for the quantity entered above. You can enter the price absolutely or as percental discount.<br> ' .
                                                 'Further information about scale prices can be found in the <a href="http://www.oxid-esales.com/en/resources/help-faq/eshop-manual/setting-graduated-prices" target="_blank">scale price article in the manual.</a>.',

'HELP_ARTICLE_VARIANT_VARNAME'                => '<span class="navipath_or_inputname">Name of Selection</span> defines how the selection of the variants is labeled , e.g. <span class="userinput_or_code">Color</span> or <span class="userinput_or_code">Size</span>.',

'HELP_ATTRIBUTE_MAIN_DISPLAYINBASKET'         => 'If checked, this attribute\'s value will be shown in cart and order overview below the product title.',

'HELP_CATEGORY_MAIN_HIDDEN'                   => 'With <span class="navipath_or_inputname">Hidden</span> <ou can define if this category is shown to users. If a category is hidden it is not shown to the users, even if it is active.',

'HELP_CATEGORY_MAIN_PARENTID'                 => 'In <span class="navipath_or_inputname">Subcategory Of</span> you specify the point at which the category is to appear:<br>' .
                                                 '<ul><li>If the category is not to be a subcategory of any other category, then select <span class="userinput_or_code">--</span> Off.</li>' .
                                                 '<li>If the category is to be a subcategory of another category, then select the appropriate category.</li></ul>',

'HELP_CATEGORY_MAIN_EXTLINK'                  => 'With <span class="navipath_or_inputname">External Link</span>, you can enter a link that opens when users click on the category. <span class="warning_or_important_hint">Use this function only if you want to display a link in the category navigation. It causes the category to lose its normal function!</span>',

'HELP_CATEGORY_MAIN_PRICEFROMTILL'            => 'With <span class="navipath_or_inputname">Price From/To</span> you can specify that <span class="warning_or_important_hint">all</span> products in a certain price range are shown in this category. Enter the lower limit in the first entry field and the upper limit in the second entry field. Then <span class="warning_or_important_hint">all products of the eShop</span> within this price range are shown in this category.',

'HELP_CATEGORY_MAIN_DEFSORT'                  => 'With <span class="navipath_or_inputname">Fast Sorting</span> you specify the manner in which the products in the category will be sorted.',

'HELP_CATEGORY_MAIN_SORT'                     => 'You can use <span class="navipath_or_inputname">Sorting</span> to define the order in which categories are displayed: The category with the lowest number is displayed at the top, and the category with the highest number at the bottom.',

'HELP_CATEGORY_MAIN_THUMB'                    => 'With <span class="navipath_or_inputname">Picture</span> and <span class="navipath_or_inputname">Upload Picture</span> you can upload a picture for this category. The picture is shown at top of the category is viewed. Select the picture in <span class="navipath_or_inputname">Upload Picture</span>. When clicking on <span class="navipath_or_inputname">Save</span>, the picture is uploaded. After uploading, the filename of the picture is shown in <span class="navipath_or_inputname">Picture</span>.',

'HELP_CATEGORY_MAIN_PROMOTION_ICON'           => 'With <span class="navipath_or_inputname">promotion icon</span> and <span class="navipath_or_inputname">upload icon</span> you can upload a category picture for promotion on start page. For displaying category promotion see <span class="navipath_or_inputname">Master Settings -> Customer Info -> Top offer in categories</span>',

'HELP_CATEGORY_MAIN_SKIPDISCOUNTS'            => '<li>If <span class="navipath_or_inputname">Skip all negative discounts</span> is active, negative allowances will not be calculated for any products in this category.',



'HELP_CATEGORY_SEO_FIXED'                     => 'You can let the eShop recalculate the SEO URLs. A category page gets a new SEO URL if e. g. the title of the category has changed. The setting <span class="navipath_or_inputname">Fixed URL</span> prevents this: If it is active, the old SEO URL is kept and no new SEO URL is calculated.',

'HELP_CATEGORY_SEO_KEYWORDS'                  => 'These keywords are integrated in the HTML sourcecode of the category page (META keywords). This information is used by search engines. Suitable keywords for the category can be entered here. If it\'s left blank, the keywords are generated automatically.',

'HELP_CATEGORY_SEO_DESCRIPTION'               => 'This description is integrated in the HTML sourcecode of the category page (META description). This text is often displayed in result pages of search engines. A suitable description can be entered here. If it\'s left blank, the description is generated automatically.',

'HELP_CATEGORY_SEO_SHOWSUFFIX'                => 'With this setting you can specify if the title suffix is shown in the browser window title when the category page is opened. The title suffix can be set in <span class="navipath_or_inputname">Master Settings -> Core Settings -> SEO -> Title Suffix</span>.',
'HELP_CONTENT_MAIN_SNIPPET'                   => 'If you select <span class="navipath_or_inputname">Snippet</span> you can include this CMS page within other CMS pages by its ident: <span class="userinput_or_code">[{ oxcontent ident=ident_of_the_cms_page }]</span>',

'HELP_CONTENT_MAIN_MAINMENU'                  => 'If you select <span class="navipath_or_inputname">Upper Menu</span>, a link to this CMS page is shown in the upper menu (At Terms and About Us).',

'HELP_CONTENT_MAIN_CATEGORY'                  => 'If you select <span class="navipath_or_inputname">Category</span>, a link to this CMS page is shown in the category navigation below the other categories.',

'HELP_CONTENT_MAIN_MANUAL'                    => 'If you select <span class="navipath_or_inputname">Manually</span>, a link is created which you can use to include this CMS page in other CMS pages. The link is shown below when you click on <span class="navipath_or_inputname">Save</span>',
'HELP_CONTENT_SEO_FIXED'                      => 'You can let the eShop recalculate the SEO URLs. A CMS page gets a new SEO URL if e. g. the title of the CMS page has changed. The setting <span class="navipath_or_inputname">Fixed URL</span> prevents this: If it is active, the old SEO URL is kept and no new SEO URL is calculated.',

'HELP_CONTENT_SEO_KEYWORDS'                   => 'These keywords are integrated in the HTML sourcecode of the CMS page (META keywords). This information is used by search engines. Suitable keywords for the CMS page can be entered here. If it\'s left blank, the keywords are generated automatically.',

'HELP_CONTENT_SEO_DESCRIPTION'                => 'This description is integrated in the HTML sourcecode of the CMS page (META description). This text is often displayed in result pages of search engines. A suitable description can be entered here. If it\'s left blank, the description is generated automatically.',



'HELP_DELIVERY_MAIN_COUNTRULES'               => 'Under <span class="navipath_or_inputname">Calculation Rules</span> you can select how often the price is calculated:' .
                                                 '<ul><li><span class="userinput_or_code">Once per cart</span>: Price is calculated once for the entire order.</li>' .
                                                 '<li><span class="userinput_or_code">Once for each different product</span>: Price is calculated once for each different product in the shopping cart. It makes no difference what quantity of a product is ordered.</li>' .
                                                 '<li><span class="userinput_or_code">For each product</span>: price is calculated for each product in the shopping cart.</li></ul>',

'HELP_DELIVERY_MAIN_CONDITION'                => 'In <span class="navipath_or_inputname">Condition</span> you can specify that the shipping cost rule applies only to a certain condition. You can choose from among 4 conditions:' .
                                                 '<ul><li><span class="userinput_or_code">Amount</span>: Number of products in the shopping cart.</li>' .
                                                 '<li><span class="userinput_or_code">Size</span>: Total size of all products. In order for this setting to be used properly, the size must be entered for products.</li>' .
                                                 '<li><span class="userinput_or_code">Weight</span>: Total weight of the order in kilograms. In order for this setting to be used properly, the weight must be entered for products.</li>' .
                                                 '<li><span class="userinput_or_code">Price</span>: Purchase price of the order.</li></ul>' .
                                                 'You can use the entry fields <span class="navipath_or_inputname">>=</span> (greater than or equal to) and <span class="navipath_or_inputname"><=</span> (less than or equal to) to specify the range to which the condition is to apply. A larger number must be entered for <span class="navipath_or_inputname"><=</span> than for <span class="navipath_or_inputname">-></span>.',

'HELP_DELIVERY_MAIN_PRICE'                    => 'You can use <span class="navipath_or_inputname">Price Surcharge/Discount</span> to specify the magnitude of the shipping costs. The price can be calculated in two different ways:' .
                                                 '<ul><li>With <span class="userinput_or_code">abs</span>, the price is specified absolutely (e.g.: with <span class="userinput_or_code">6.90</span>, a price of EUR 6.90 is calculated).</li>' .
                                                 '<li>With <span class="userinput_or_code">%</span>, the price is specified relative to the purchase price (e.g.: With <span class="userinput_or_code">10</span>, a price of 10% of the purchase price is calculated).</li></ul>',

'HELP_DELIVERY_MAIN_ORDER'                    => 'You can use <span class="navipath_or_inputname">Order of rule processing</span> to specify the order in which the shipping cost rules will be run. The shipping cost rule with the lowest number is run first. The order is important if the setting <span class="navipath_or_inputname">Don\'t calculate further rules if this rule matches</span> is used.',

'HELP_DELIVERY_MAIN_FINALIZE'                 => 'You can use <span class="navipath_or_inputname">Don\'t calculate further rules if this rule matches</span> to specify that no further rules are to be run if this shipping cost rule is valid and is being run. For this option, the order in which the shipping cost rules are run is important. It is specified through the <span class="navipath_or_inputname">Order of Rule processing</span>.',



'HELP_DELIVERYSET_MAIN_POS'                   => '<span class="navipath_or_inputname">Sorting</span> specifies the order in which the shipping methods are displayed to users: The shipping method with the lowest number is displayed at the top.',



'HELP_DISCOUNT_MAIN_PRICE'                    => 'You can use <span class="navipath_or_inputname">Purchase Price</span> to specify that the discount is only valid for certain purchase prices. If the discount is to be valid for all purchase prices, enter <span class="userinput_or_code">0</span> in <span class="navipath_or_inputname">From</span> and <span class="userinput_or_code">0</span> in <span class="navipath_or_inputname">To</span>.',

'HELP_DISCOUNT_MAIN_AMOUNT'                   => 'You can use <span class="navipath_or_inputname">Quantity</span> to specify that the discount is only valid for certain purchase quantities. If you want the discount to be valid for all purchase quantities, enter <span class="userinput_or_code">0</span> in <span class="navipath_or_inputname">From</span> and <span class="userinput_or_code">0</span> in <span class="navipath_or_inputname">To</span>.',

'HELP_DISCOUNT_MAIN_REBATE'                   => 'In <span class="navipath_or_inputname">Discount</span>, you specify the magnitude of the discount. You can use the selection list after the entry field to specify whether the discount is to be applied as an absolute discount or as a percentage discount:' .
                                                 '<ul>' .
                                                 '<li><span class="userinput_or_code">abs</span>: The discount is an absolute discount, e.g. EUR 5.</li>' .
                                                 '<li><span class="userinput_or_code">%</span>: The discount is a percentage discount, e.g. 10 percent of the purchase price.</li>' .
                                                 '<li><span class="userinput_or_code">itm</span>: Discount in the form of a free product. Set the amount of this product. Choose "Multiply" if the amount of discounted product in shopping cart should be multiplied with the amount of the free product.</li>'.
                                                 '</ul>',



'HELP_GENERAL_SEO_ACTCAT'                     => 'You can define several SEO URLs for products: For certain categories and manufacturer pages. With <span class="navipath_or_inputname">Active Category/Vendor</span> you can select the SEO URL you want to edit.',

'HELP_GENERAL_SEO_FIXED'                      => 'The eShop automatically recalculate the SEO URLs. For example, a product page gets a new SEO URL if the title of the product has changed. The setting <span class="navipath_or_inputname">Fixed URL</span> prevents the recalculation: If it is active, the old SEO URL is kept and no new SEO URL is generated.',

'HELP_GENERAL_SEO_SHOWSUFFIX'                 => 'With this setting you can specify if the title suffix is shown in the browser window title when the according page is opened. The title suffix can be set in <span class="navipath_or_inputname">Master Settings -> Core Settings -> SEO -> Title Suffix</span>.',

'HELP_GENERAL_SEO_OXKEYWORDS'                 => 'These keywords are integrated in the HTML sourcecode (META keywords). This information is used by search engines. Suitable keywords for the product can be entered here. If it\'s left blank, the keywords are generated automatically.',

'HELP_GENERAL_SEO_OXDESCRIPTION'              => 'This description is integrated in the HTML sourcecode (META description). This text is often displayed in result pages of search engines. A suitable description can be entered here. If it\'s left blank, the description is generated automatically.',



'HELP_GENIMPORT_FIRSTCOLHEADER'               => 'Activate this setting if the first line of the CSV file contains the names of the database columns the CSV values are to be assigned to. The values are automatically assigned to the database columns.',

'HELP_GENIMPORT_REPEATIMPORT'                 => 'If this setting is active, step one is shown after successful import so you can start the next import immediately.',

'HELP_LANGUAGE_DEFAULT'                       => 'Default language is used when shop is unable to detect language in other ways: language id is not defined by URL, can\'t be detected by browser, is not defined in session etc. Default language can only be disabled, deleting it is <u>not possible</u>.',

'HELP_LANGUAGE_ACTIVE'                        => "This option defines language availability in eShop's frontend: if it is activated - language is available in frontend. For working in admin area this language is always available; even if it is disabled for frontend.",

'HELP_PAYMENT_MAIN_SORT'                      => 'In <span class="navipath_or_inputname">Sorting</span> you can specify the order in which the payment methods are to be displayed to users: The payment method with the lowest sort number is displayed on top.',

'HELP_PAYMENT_MAIN_FROMBONI'                  => 'You can use <span class="navipath_or_inputname">Min. Credit Rating</span> to specify that payment methods are only available to users who have a certain credit index or higher. You can enter the credit rating for each user in <span class="navipath_or_inputname">Administer Users -> Users -> Extended</span>.',

'HELP_PAYMENT_MAIN_SELECTED'                  => 'You can use <span class="navipath_or_inputname">Selected</span> to define which payment method is be selected as the default method if the user can choose between several payment methods.',

'HELP_PAYMENT_MAIN_AMOUNT'                    => 'You can use <span class="navipath_or_inputname">Purchase Price</span> to specify that the payment method is only valid for certain purchase prices. The <span class="navipath_or_inputname">from</span> and <span class="navipath_or_inputname">to</span> fields allow you to set a range.<br>' .
                                                 'If the payment method is to be valid for any purchase price, you must specify a condition that is always met: Enter <span class="userinput_or_code">0</span> in the <span class="navipath_or_inputname">from</span>  and <span class="userinput_or_code">99999999</span> in the <span class="navipath_or_inputname">to</span> field.',

'HELP_PAYMENT_MAIN_ADDPRICE'                  => 'In <span class="navipath_or_inputname">Price Surcharge/Reduction</span>, the price is entered for the payment method. The price can be specified in two different ways:' .
                                                 '<ul><li>With <span class="userinput_or_code">abs</span> the price is entered for the payment method (e.g.: if you enter <span class="userinput_or_code">7.50</span> a price of EUR 7.50 is calculated.)</li>' .
                                                 '<li>With <span class="userinput_or_code">%</span>, the price is calculated relative to the purchase price (e.g.: if you enter <span class="userinput_or_code">2</span>, the price is 2 percent of the purchase price)</li></ul>',

'HELP_PAYMENT_MAIN_ADDSUMRULES'               => 'When calculating price surcharge or reduction, cart value is being used as base. Define what costs will be included when calculating cart value.',

'HELP_SELECTLIST_MAIN_TITLEIDENT'             => 'In <span class="navipath_or_inputname">Working Title</span>, you can enter an additional name that is not displayed to users of your eShop. You can use the working title to differentiate between similar selection lists (e.g., Sizes for trousers and Sizes for shirts).',

'HELP_SELECTLIST_MAIN_FIELDS'                 => 'All available options are displayed in the <span class="navipath_or_inputname">Fields</span> list. You can use the entry fields to the right to set up new options.',

'HELP_USER_MAIN_HASPASSWORD'                  => 'Here you can distinguish if users registered when ordering:' .
                                                 '<ul><li>If a password is set, the user registered.</li>' .
                                                 '<li>If no password is set, the user ordered without registering.</li></ul>',

'HELP_USER_PAYMENT_METHODS'                   => 'On this tab you can:'.
                                                 '<ul><li>Browse and manage existing user payment methods.'.
                                                 '<li>Create new payment methods and set default values, for example direct debit.</li></ul>',

'HELP_USER_EXTEND_NEWSLETTER'                 => 'This setting shows if the user subscribed to the newsletter.',

'HELP_USER_EXTEND_EMAILFAILED'                => 'If no e-mails can be sent to the e-mail address of this user, check this setting. Then no newsletters are sent to this user any more. Other e-mails are still sent.',

'HELP_USER_EXTEND_DISABLEAUTOGROUP'           => 'Users are automatically assigned to certain user groups. This setting prevents this: If checked, the users isn\'t automatically added to any user group.',

'HELP_USER_EXTEND_BONI'                       => 'Here you can enter a numerical value for the credit rating of the user. With the credit rating you can influence which payment methods are available to this user.',



'HELP_MANUFACTURER_MAIN_ICON'                 => 'With <span class="navipath_or_inputname">Icon</span> and <span class="navipath_or_inputname">Upload Icon</span> you can upload a picture for this manufacturer (e. g. the logo).In <span class="navipath_or_inputname">Upload Icon</span>, select the Picture you want to upload. When clicking on <span class="navipath_or_inputname">Save</span> the picture is uploaded. After uploading, the filename is shown in <span class="navipath_or_inputname">Icon</span>.',



'HELP_MANUFACTURER_SEO_FIXED'                 => 'You can let the eShop recalculate the SEO URLs. A manufacturer page gets a new SEO URL if e. g. the title of the manufacturer has changed. The setting <span class="navipath_or_inputname">Fixed URL</span> prevents this: If it is active, the old SEO URL is kept and no new SEO URL is calculated.',

'HELP_MANUFACTURER_SEO_KEYWORDS'              => 'These keywords are integrated in the HTML sourcecode of the manufacturer page (META keywords). This information is used by search engines. Suitable keywords for the manufacturer can be entered here. If left blank, the keywords are generated automatically.',

'HELP_MANUFACTURER_SEO_DESCRIPTION'           => 'This description is integrated in the HTML sourcecode of the manufacturer page (META description). This text is often displayed in result pages of search engines. A suitable description can be entered here. If left blank, the description is generated automatically.',

'HELP_MANUFACTURER_SEO_SHOWSUFFIX'            => 'With this setting you can specify if the title suffix is shown in the browser window title when the manufacturer page is opened. The title suffix can be set in <span class="navipath_or_inputname">Master Settings -> Core Settings -> SEO -> Title Suffix</span>.',

'HELP_VOUCHERSERIE_MAIN_DISCOUNT'             => 'In <span class="navipath_or_inputname">Discount</span>, you specify the magnitude of the discount. You can use the selection list after the entry field to specify whether the discount is to be applied as an absolute discount or as a percentage discount:' .
                                                 '<ul>' .
                                                 '<li><span class="userinput_or_code">abs</span>: The discount is an absolute discount, e.g. EUR 5.</li>' .
                                                 '<li><span class="userinput_or_code">%</span>: The discount is a percentage discount, e.g. 10 percent of the purchase price.</li>' .
                                                 '</ul>',



'HELP_VOUCHERSERIE_MAIN_ALLOWSAMESERIES'      => 'Here you can set whether users are allowed to use several coupons of this coupon series in a single order.',

'HELP_VOUCHERSERIE_MAIN_ALLOWOTHERSERIES'     => 'Here you can set if users are allowed to use coupons together with coupons of other coupon series in a single order.',

'HELP_VOUCHERSERIE_MAIN_SAMESEROTHERORDER'    => 'Here you can set if users can use coupons of this coupon series in multiple orders.',

'HELP_VOUCHERSERIE_MAIN_RANDOMNUM'            => 'If this setting is active a random number is calculated for each coupon.',

'HELP_VOUCHERSERIE_MAIN_VOUCHERNUM'           => 'Here you can enter a coupon number. This number is used when creating new coupons if <span class="navipath_or_inputname">Random Numbers</span> is deactivated. All Coupons get the same coupon numberon.',

'HELP_VOUCHERSERIE_MAIN_CALCULATEONCE'        => 'If you assign products or categories to your coupon, deactivate this option to calculate this coupon to each product per cart position. Activate if the coupon shall be valid only once per cart position.',

'HELP_WRAPPING_MAIN_PICTURE'                  => 'With <span class="navipath_or_inputname">Picture</span> and <span class="navipath_or_inputname">Upload Picture</span> you can upload a picture for the gift wrapping. In <span class="navipath_or_inputname">Upload Picture</span>, select the picture to upload. When clicking on <span class="navipath_or_inputname">Save</span>, the picture is uploaded. After uploading, the filename is shown in <span class="navipath_or_inputname">Picture</span>.',



'HELP_DYN_TRUSTED_RATINGS_ID'                 => 'You will receive your Trusted Shops ID for Customer Ratings in the order conformation e-mail. If you are already a member of Trusted Shops, please use your known Trusted Shops ID. The green light indicates that the Customer Ratings have been verified and enabled after saving your settings.',
'HELP_DYN_TRUSTED_RATINGS_WIDGET'             => 'Enable the Customer Ratings Widget by switching this option on.',
'HELP_DYN_TRUSTED_RATINGS_THANKYOU'           => 'Enable the button "Write a review!" on "Order completed" page subsequent to an order by switching this option on.',
'HELP_DYN_TRUSTED_RATINGS_ORDEREMAIL'         => 'Enable the button "Write a review!" in "Order confirmation" e-mail subsequent to an order by switching this option on.',
'HELP_DYN_TRUSTED_RATINGS_ORDERSENDEMAIL'     => 'Enable the button "Write a review!" in "Order sent" e-mail after dispatch of the order.',
'HELP_DYN_TRUSTED_TSID'                       => 'Trusted Shops ID of the online shop (for language).',
'HELP_DYN_TRUSTED_USER'                       => 'A user name (wsUser) for the Trusted Shops webservice is required if you offer the Trusted Shops Buyer Protection Excellence to your customers. Buyer Protection Classic does not require a user name.',
'HELP_DYN_TRUSTED_PASSWORD'                   => 'A password (wsPassword) for the Trusted Shops webservice is required if you offer the Trusted Shops Buyer Protection Excellence to your customers. Buyer Protection Classic does not require a password.',
'HELP_DYN_TRUSTED_TESTMODUS'                  => 'Test environment ("Sandbox") on. After completion of the certification Trusted Shops will send you login details by e-mail.',
'HELP_DYN_TRUSTED_ACTIVE'                     => 'Check this option to display the Trusted Shops Seal on shop.',
'HELP_DYN_TRUSTED_TSPAYMENT'                  => 'Assign the shop offered payment types at the appropriate payment to Trusted Store.',

'HELP_PROMOTIONS_BANNER_PICTUREANDLINK'       => 'Upload banner image and enter banner URL which will be used when clicking on banner. If product is assigned to banner and URL is not entered, link to assigned product will be used when clicking on banner.',
'HELP_SHOP_PERF_SEO_CACHE'                    => 'Enabled SEO cache increases performance, but requires a lot of disk space in /tmp directory.',

'HELP_ALTERNATIVE_IMAGE_SERVER_NOTE'          => 'Alternative URL to remote image server can be specified in configuration file config.inc.php by setting <i>sAltImageUrl</i> and <i>sSSLAltImageUrl</i>.<br> Thus all product pictures will be loaded from this alternative server instead of the local one. However, uploaded files will be stored locally. In this case synchronization to external server has to be done manually or with custom scripts.',

'HELP_SHOP_RDFA_SUBMIT_URL'                   => 'Submits your shop URL to GR-Notify page. There the URL is saved and forwarded to Linked Open Commerce & Semantic Web search engines and endpoints.',
'HELP_SHOP_RDFA_CONTENT_OFFERER'              => 'Select here, in which content page eShop main information is displayed, e.g. "About Us".',
'HELP_SHOP_RDFA_CONTENT_PAYMENT'              => 'Select here, in which content page not to RDFa assigned payment information is displayed, e.g. "Terms and Conditions". To assign your payment methods to RDFa payment methods in general go to: Shop Settings -> Payment Methods -> RDFa.',
'HELP_SHOP_RDFA_CONTENT_DELIVERY'             => 'Select here, in which content page not to RDFa assigned shipping information is displayed, e.g. "Shipping and charges". To assign your shipping methods to RDFa payment methods in general go to: Shop Settings -> Shipping Methods -> RDFa.',
'HELP_SHOP_RDFA_VAT'                          => 'This option specifies whether the tax (VAT) is included in the price and delivery/payment costs or not.',
'HELP_SHOP_RDFA_DURATION_PRICES'              => 'Specify here the time of the validity of the costs of products, payment and shipping (e.g. 1 day, 1 week).',
'HELP_SHOP_RDFA_LOGO_URL'                     => 'The Web address (URL) of a logo or image.',
'HELP_SHOP_RDFA_GEO_LONGITUDE'                => 'The longitude of the store as part of geo position. Please enter numbers only.',
'HELP_SHOP_RDFA_GEO_LATITUDE'                 => 'The latitude of the store as part of geo position. Please enter numbers only.',
'HELP_SHOP_RDFA_GLN'                          => 'Global Location Number (GLN) for the company. The Global Location Number is a thirteen-digit number used to identify parties and physical locations.',
'HELP_SHOP_RDFA_NAICS'                        => 'North American Industry Classification System (NAICS) code for your company. See http://www.census.gov/eos/www/naics/.',
'HELP_SHOP_RDFA_ISIC'                         => 'The International Standard of Industrial Classification of All Economic Activities (ISIC) code for your company. See http://unstats.un.org/unsd/cr/registry/isic-4.asp.',
'HELP_SHOP_RDFA_DUNS'                         => 'The Dun & Bradstreet D-U-N-S is a nine-digit number used to identify legal entities.',
'HELP_SHOP_RDFA_SHOW_PRODUCTSTOCK'            => 'If this option is on means, that the real product stock is shown.',
'HELP_SHOP_RDFA_RATING_MIN'                   => 'Possible minimum value refer to the scale used for ratings in your shop. This value is not the lowest current rating of a product!',
'HELP_SHOP_RDFA_RATING_MAX'                   => 'Possible maximum value refer to the scale used for ratings in your shop. This value is not the highest current rating of a product!',
'HELP_SHOP_RDFA_COND'                         => 'Select here, what term describes the condition of the products (new, used or refurbished).',
'HELP_SHOP_RDFA_FNC'                          => 'Select the business function of the products here. For example, are they offered to sell, to lease or to repair?',
'HELP_SHOP_RDFA_COSTUMER'                     => 'The types of customers for which shop products are valid (End user, Reseller, Business and/or Public).',
'HELP_SHOP_RDFA_DURATION_OFFERINGS'           => 'This property specifies the time of the validity of the products, e.g. 1 day, 1 week or 1 month.',

'HELP_SHOP_CONFIG_ADDITIONAL_SERVICE_VAT_CALCULATION_BIGGEST_NET'  => 'The VAT rate of the products, which have the biggest net value in shopping cart, is used for calculating.',
'HELP_SHOP_CONFIG_ADDITIONAL_SERVICE_VAT_CALCULATION_PROPORTIONAL' => 'The percentage of products in cart with the same VAT rate is used for calculating.',
'HELP_SHOP_CONFIG_VIEWNETPRICE'               => 'In frontend product prices are shown as net prices.',
'HELP_SHOP_CONFIG_ENTERNETPRICE'              => 'In admin area product prices must be entered as net prices.',

'HELP_REVERSE_PROXY_GET_FRONTEND'             => 'Checks if Reverse Proxy is available for the frontend. Header of the shop\'s start page is veryfied.',
'HELP_REVERSE_PROXY_GET_BACKEND'              => 'Admin area is displayed without Reverse Proxy. Varnish header could not be received.',

'HELP_SHOP_CONFIG_DEBIT_OLD_BANK_INFORMATION_NOT_ALLOWED' => 'Only IBAN and BIC can be entered during the checkout. Bank account number and the bank code can only be entered if this check box is not activated.',

);
