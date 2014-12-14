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
'HEADER_META_MAIN_TITLE'                        => 'OXID eShop installation wizard',
'HEADER_TEXT_SETUP_NOT_RUNS_AUTOMATICLY'        => 'If setup does not continue in a few seconds, please click ',
'FOOTER_OXID_ESALES'                            => '&copy; OXID eSales AG 2003 - '.@date("Y"),

'TAB_0_TITLE'                                   => 'System Requirements',
'TAB_1_TITLE'                                   => 'Welcome',
'TAB_2_TITLE'                                   => 'License conditions',
'TAB_3_TITLE'                                   => 'Database',
'TAB_4_TITLE'                                   => 'Directory & login',
'TAB_5_TITLE'                                   => 'License',
'TAB_6_TITLE'                                   => 'Finish',

'TAB_0_DESC'                                    => 'Checking if your system fits the requirements',
'TAB_1_DESC'                                    => 'Welcome to OXID eShop installation wizard',
'TAB_2_DESC'                                    => 'Confirm license conditions',
'TAB_3_DESC'                                    => 'Test database connection, building tables',
'TAB_4_DESC'                                    => 'Configuring directories and selecting admin login',
'TAB_5_DESC'                                    => 'Apply license key',
'TAB_6_DESC'                                    => 'Installation succeeded',

'HERE'                                          => 'here',

'ERROR_NOT_AVAILABLE'                           => 'ERROR: %s not found!',
'ERROR_NOT_WRITABLE'                            => 'ERROR: %s not writeable!',
'ERROR_DB_CONNECT'                              => 'ERROR: No database connection possible!',
'ERROR_OPENING_SQL_FILE'                        => 'ERROR: Can not open SQL file %s!',
'ERROR_FILL_ALL_FIELDS'                         => 'ERROR: Please fill in all needed fields!',
'ERROR_COULD_NOT_CREATE_DB'                     => 'ERROR: Database not available and also cannot be created!',
'ERROR_DB_ALREADY_EXISTS'                       => 'ERROR: Seems there is already OXID eShop installed in database %s. Please delete it prior continuing!',
'ERROR_BAD_SQL'                                 => 'ERROR: Issue while inserting this SQL statements: ',
'ERROR_BAD_DEMODATA'                            => 'ERROR: Issue while inserting this SQL statements: ',
'ERROR_CONFIG_FILE_IS_NOT_WRITABLE'             => 'ERROR: %s/config.inc.php'.' not writeable!',
'ERROR_BAD_SERIAL_NUMBER'                       => 'ERROR: Wrong license key!',
'ERROR_COULD_NOT_OPEN_CONFIG_FILE'              => 'Could not open %s for reading! Please consult our FAQ, forum or contact OXID Support staff!',
'ERROR_COULD_NOT_FIND_FILE'                     => 'Setup could not find %s !',
'ERROR_COULD_NOT_READ_FILE'                     => 'Setup could not open %s for reading!',
'ERROR_COULD_NOT_WRITE_TO_FILE'                 => 'Setup could not write to %s!',
'ERROR_PASSWORD_TOO_SHORT'                      => 'Password is too short!',
'ERROR_PASSWORDS_DO_NOT_MATCH'                  => 'Passwords do not match!',
'ERROR_USER_NAME_DOES_NOT_MATCH_PATTERN'        => 'Please enter a valid e-mail address!',
'ERROR_MYSQL_VERSION_DOES_NOT_FIT_REQUIREMENTS' => 'MySQL version does not fit system requiremens',

'ERROR_VIEWS_CANT_CREATE'                       => 'ERROR: Can\'t create views. Please check your database user privileges.',
'ERROR_VIEWS_CANT_SELECT'                       => 'ERROR: Can\'t select from views. Please check your database user privileges.',
'ERROR_VIEWS_CANT_DROP'                         => 'ERROR: Can\'t drop views. Please check your database user privileges.',

'MOD_PHP_EXTENNSIONS'                           => 'PHP extensions',
'MOD_PHP_CONFIG'                                => 'PHP configuration',
'MOD_SERVER_CONFIG'                             => 'Server configuration',

'MOD_MOD_REWRITE'                               => 'Apache mod_rewrite module',
'MOD_SERVER_PERMISSIONS'                        => 'Files/folders access rights',
'MOD_ALLOW_URL_FOPEN'                           => 'allow_url_fopen or fsockopen to port 80',
'MOD_PHP4_COMPAT'                               => 'Zend compatibility mode must be off',
'MOD_PHP_VERSION'                               => 'PHP version at least 5.3.25',
'MOD_REQUEST_URI'                               => 'REQUEST_URI set',
'MOD_LIB_XML2'                                  => 'LIB XML2',
'MOD_PHP_XML'                                   => 'DOM',
'MOD_J_SON'                                     => 'JSON',
'MOD_I_CONV'                                    => 'ICONV',
'MOD_TOKENIZER'                                 => 'Tokenizer',
'MOD_BC_MATH'                                   => 'BCMath',
'MOD_MYSQL_CONNECT'                             => 'MySQL client connector for MySQL 5',
'MOD_GD_INFO'                                   => 'GDlib v2 [v1] incl. JPEG support',
'MOD_INI_SET'                                   => 'ini_set allowed',
'MOD_REGISTER_GLOBALS'                          => 'register_globals must be off',
'MOD_MAGIC_QUOTES_GPC'                          => 'magic_quotes_gpc must be off',
'MOD_ZEND_OPTIMIZER'                            => 'Zend Guard Loader installed',
'MOD_ZEND_PLATFORM_OR_SERVER'                   => 'Zend Platform or Zend Server installed',
'MOD_MB_STRING'                                 => 'mbstring',
'MOD_CURL'                                      => 'cURL',
'MOD_OPEN_SSL'                                  => 'OpenSSL',
'MOD_SOAP'                                      => 'SOAP',
'MOD_UNICODE_SUPPORT'                           => 'UTF-8 support',
'MOD_FILE_UPLOADS'                              => 'File uploads are enabled (file_uploads)',
'MOD_BUG53632'                                  => 'Possible issues on server due to PHP Bugs',
'MOD_SESSION_AUTOSTART'                         => 'session.auto_start must be off',
'MOD_MEMORY_LIMIT'                              => 'PHP Memory limit (min. 14MB, 30MB recommended)',

'STEP_0_ERROR_TEXT'                             => 'Your system does not fit system requirements',
'STEP_0_ERROR_URL'                              => 'http://www.oxid-esales.com/en/products/community-edition/system-requirements',
'STEP_0_TEXT'                                   => '<ul class="req">'.
                                                   '<li class="pass"> - Your system fits the requirement.</li>'.
                                                   '<li class="pmin"> - The requirement is not or only partly fit. The OXID eShop will work anyway and can be installed..</li>'.
                                                   '<li class="fail"> - Your system doesn\'t fit the requirement. The OXID eShop will not work without it and cannot be installed.</li>'.
                                                   '<li class="null"> - The requirement could  not be checked.'.
                                                   '</ul>',
'STEP_0_DESC'                                   => 'In this step we check if your system fits the requirements:',
'STEP_0_TITLE'                                  => 'System requirements check',

'STEP_1_TITLE'                                  => 'Welcome',
'STEP_1_DESC'                                   => 'Welcome to installation wizard of OXID eShop',
'STEP_1_TEXT'                                   => 'Please read carefully the following instructions to guarantee a smooth installation.
                                                    Wishes for best success in using your OXID eShop by',
'STEP_1_ADDRESS'                                => 'OXID eSales AG<br>
                                                    Bertoldstr. 48<br>
                                                    79098 Freiburg<br>
                                                    Deutschland<br>',
'STEP_1_CHECK_UPDATES'                          => 'Check for available updates regularly',
'BUTTON_BEGIN_INSTALL'                          => 'Start installation',
'BUTTON_PROCEED_INSTALL'                        => 'Proceed with setup',

'STEP_2_TITLE'                                  => 'License conditions',
'BUTTON_RADIO_LICENCE_ACCEPT'                   => 'I accept license conditions.',
'BUTTON_RADIO_LICENCE_NOT_ACCEPT'               => 'I do not accept license conditions.',
'BUTTON_LICENCE'                                => 'Continue',

'STEP_3_TITLE'                                  => 'Database',
'STEP_3_DESC'                                   => 'Database is going to be created and needed tables are written. Please provide some information:',
'STEP_3_DB_HOSTNAME'                            => 'Database hostname or IP',
'STEP_3_DB_USER_NAME'                           => 'Database username',
'STEP_3_DB_PASSWORD'                            => 'Database password',
'STEP_3_DB_PASSWORD_SHOW'                       => 'Show password',
'STEP_3_DB_DATABSE_NAME'                        => 'Database name',
'STEP_3_DB_DEMODATA'                            => 'Demodata',
'STEP_3_UTFMODE'                                => 'Use UTF-8 character encoding',
'STEP_3_UTFNOTSUPPORTED'                        => 'The OXID eShop cannot operate in UTF-8 mode, due to:',
'STEP_3_UTFNOTSUPPORTED1'                       => ' missing mbstring PHP module',
'STEP_3_UTFNOTSUPPORTED2'                       => ' installed PCRE does not support UTF-8',
'STEP_3_UTFINFO'                                => 'UTF-8 character encoding provides better support for special chars than other character encodings. This is particulary important for multilingual eShops. However, UTF-8 is slightly slower than the standard encoding (ISO 8859-15). <br /> If you plan to provide your eShop in many different languages, you should use UTF-8. If you just want to provide a few languages using related symbol sets (e. g. English, German, French), you don\'t need UTF-8.',
'STEP_3_CREATE_DB_WHEN_NO_DB_FOUND'             => 'If database does not exist, it\'s going to be created',
'BUTTON_RADIO_INSTALL_DB_DEMO'                  => 'Install demodata',
'BUTTON_RADIO_NOT_INSTALL_DB_DEMO'              => 'Do <strong>not</strong> install demodata',
'BUTTON_DB_INSTALL'                             => 'Create database now',

'STEP_3_1_TITLE'                                => 'Database - being created ...',
'STEP_3_1_DB_CONNECT_IS_OK'                     => 'Database connection successfully tested ...',
'STEP_3_1_DB_CREATE_IS_OK'                      => 'Database %s successfully created ...',
'STEP_3_1_CREATING_TABLES'                      => 'Creating tables, applying data ...',

'STEP_3_2_TITLE'                                => 'Database - tables are beeing created ...',
'STEP_3_2_CONTINUE_INSTALL_OVER_EXISTING_DB'    => 'If you want to overwrite all existing data and install anyway click ',
'STEP_3_2_CREATING_DATA'                        => 'Database successfully created. Please wait ...',

'STEP_4_TITLE'                                  => 'Setting up OXID eShop directories and URL',
'STEP_4_DESC'                                   => 'Please provide neccesary data for running OXID eShop:',
'STEP_4_SHOP_URL'                               => 'Shop URL',
'STEP_4_SHOP_DIR'                               => 'Directory for OXID eShop',
'STEP_4_SHOP_TMP_DIR'                           => 'Directory for temporary data',
'STEP_4_ADMIN_LOGIN_NAME'                       => 'Administrator e-mail (used as login name)',
'STEP_4_ADMIN_PASS'                             => 'Administrator password',
'STEP_4_ADMIN_PASS_CONFIRM'                     => 'Confirm Administrator password',
'STEP_4_ADMIN_PASS_MINCHARS'                    => 'freely selectable, min. 6 chars',

'STEP_4_1_TITLE'                                => 'Directories - being created ...',
'STEP_4_1_DATA_WAS_WRITTEN'                     => 'Check and writing data successful. Please wait ...',
'BUTTON_WRITE_DATA'                             => 'Save and continue',

'STEP_5_TITLE'                                  => 'OXID eShop license',
'STEP_5_DESC'                                   => 'Please confirm license key:',
'STEP_5_LICENCE_KEY'                            => 'License key',
'STEP_5_LICENCE_DESC'                           => 'The provided key is valid for 30 days. After this period all of your changes remain if you insert a valid license key.',
'BUTTON_WRITE_LICENCE'                          => 'Save license key',

'STEP_5_1_TITLE'                                => 'License key is being inserted ...',
'STEP_5_1_SERIAL_ADDED'                         => 'License key successfully saved. Please wait ...',

'STEP_6_TITLE'                                  => 'OXID eShop successfully installed',
'STEP_6_DESC'                                   => 'Your OXID eShop has been installed successfully.',
'STEP_6_LINK_TO_SHOP'                           => 'Continue to your OXID eShop',
'STEP_6_LINK_TO_SHOP_ADMIN_AREA'                => 'Continue to your OXID eShop admin interface',
'STEP_6_TO_SHOP'                                => 'To Shop',
'STEP_6_TO_SHOP_ADMIN'                          => 'To admin interface',

'ATTENTION'                                     => 'Attention, important',
'SETUP_DIR_DELETE_NOTICE'                       => 'Due to security reasons remove setup directory if not yet done during installation.',
'SETUP_CONFIG_PERMISSIONS'                      => 'Due to security reasons put your config.inc.php file to read-only mode!',

'SELECT_SETUP_LANG'                             => 'Installation language',
'SELECT_SHOP_LOCATION'                          => 'Your market',
'SELECT_PLEASE_CHOOSE'                          => 'Please choose',
'SELECT_SHOP_LOCATION_HINT'                     => 'Please choose the market to which shop is focused. According to it, additional eCommerce Services will be loaded from OXID server, if you allow that.',
'SELECT_DELIVERY_COUNTRY'                       => 'Main delivery country',
'SELECT_DELIVERY_COUNTRY_HINT'                  => 'If needed, activate easily more delivery countries in admin.',
'SELECT_SHOP_LANG'                              => 'Shop language',
'SELECT_SHOP_LANG_HINT'                         => 'If needed, activate easily more languages in admin.',
'SELECT_SETUP_LANG_SUBMIT'                      => 'Select',
'USE_DYNAMIC_PAGES'                             => 'Enable connection with the OXID servers. You find more information in our ',
'PRIVACY_POLICY'                                => 'privacy statements',

'LOAD_DYN_CONTENT_NOTICE'                       => '<p>If checkbox is set, you will see an additional menu in the admin area of your OXID eShop.</p><p>In that menu you get further information about eCommerce services like Google product search.</p> <p>You can change these settings at any time.</p>',
'ERROR_SETUP_CANCELLED'                         => 'Setup has been cancelled because you didn\'t accept the license conditions.',
'BUTTON_START_INSTALL'                          => 'Restart setup',
);
