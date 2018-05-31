# Change Log for OXID eShop 6 Community Edition

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

### Changed

- source/Application/views/admin/tpl/shop_license.tpl

### Deprecated

### Removed
- \OxidEsales\EshopCommunity\Application\Controller\Admin\AdminController::getServiceUrl()
- class \OxidEsales\EshopCommunity\Application\Controller\Admin\DynEconda
- class \OxidEsales\EshopCommunity\Application\Controller\Admin\DynamicInterface
- class \OxidEsales\EshopCommunity\Application\Controller\Admin\DynamicScreenController
- class \OxidEsales\EshopCommunity\Application\Controller\Admin\DynamicScreenList
- class \OxidEsales\EshopCommunity\Application\Controller\Admin\DynamicScreenLocal
- \OxidEsales\EshopCommunity\Application\Controller\Admin\NavigationTree::_addDynLinks()
- \OxidEsales\EshopCommunity\Application\Controller\Admin\NavigationTree::_checkDynFile()
- \OxidEsales\EshopCommunity\Application\Controller\Admin\NavigationTree::_getDynMenuLang()
- \OxidEsales\EshopCommunity\Application\Controller\Admin\NavigationTree::_getDynMenuUrl()
- \OxidEsales\EshopCommunity\Application\Component\Widget::getCompareItemsCnt()
- \OxidEsales\EshopCommunity\Core\Config::getRevision()
- \OxidEsales\EshopCommunity\Core\Controller\BaseController::getRevision()
- template source/Application/views/admin/tpl/dyn_econda.tpl
- template source/Application/views/admin/tpl/dynscreen.tpl
- template source/Application/views/admin/tpl/dynscreen_list.tpl
- template source/Application/views/admin/tpl/dynscreen_local.tpl
- `sOXIDPHP` parameter in `config.inc.php`
- \OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::writeExceptionToLog
- \OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::displayOfflinePage
- \OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::setLogFileName
- \OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::getLogFileName
- \OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::setIDebug
- \OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::$_sFileName
- \OxidEsales\EshopCommunity\Core\Exception\StandardException::debugOut
- \OxidEsales\EshopCommunity\Core\Exception\StandardException::setLogFileName
- \OxidEsales\EshopCommunity\Core\Exception\StandardException::getLogFileName
- \OxidEsales\EshopCommunity\Core\Exception\StandardException::$_sFileName
- \OxidEsales\EshopCommunity\Core\OnlineCaller::_castExceptionAndWriteToLog
- \OxidEsales\EshopCommunity\Core\Utils::writeToLog
- `writeToLog` in `bootstrap.php`

### Fixed

### Security

## [6.x.x] - Unreleased

### Added
- New blocks in `admin/tpl/voucherserie_groups.tpl`
  - `admin_voucherserie_relations`
  - `admin_voucherserie_groups_form`
  - `admin_voucherserie_categories_form`
  - `admin_voucherserie_articles_form`
- PSR3 Logger:
    - New settings:
        - `sLogLevel` in `config.inc.php`
    - New methods:
        - `OxidEsales\EshopCommunity\Core\Registry::getLogger`
        - `getLogger` in `overridablefunctions.php`

### Changed
- Persparams filter simplified to allow arrays in persparams [PR-641](https://github.com/OXID-eSales/oxideshop_ce/pull/641)
- Method visibility changed from private to protected [PR-636](https://github.com/OXID-eSales/oxideshop_ce/pull/636):
  - `OxidEsales\EshopCommunity\Core\Session::isSerializedBasketValid`
  - `OxidEsales\EshopCommunity\Core\Session::isClassInSerializedObject`
  - `OxidEsales\EshopCommunity\Core\Session::isClassOrNullInSerializedObjectAfterField`
  - `OxidEsales\EshopCommunity\Core\Session::isUnserializedBasketValid`
- Name attribute added to no wysiwyg textarea fields in admin

### Deprecated
- `writeToLog` in `bootstrap.php`
- `\OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::$_iDebug`
- `\OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::setIDebug`
- `\OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::writeExceptionToLog`
- `\OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::displayOfflinePage`
- `\OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::displayDebugMessage`
- `\OxidEsales\EshopCommunity\Core\Exception\ExceptionHandler::getFormattedException`
- `\OxidEsales\EshopCommunity\Core\Exception\StandardException::debugOut`
- `\OxidEsales\EshopCommunity\Core\OnlineCaller::_castExceptionAndWriteToLog`

### Removed

### Fixed
- Fixed notices and performance improved in FontendController::isVatIncluded [PR-642](https://github.com/OXID-eSales/oxideshop_ce/pull/642)
- Use error_404_handler in article list controller in place of outdated 404 handling [PR-643](https://github.com/OXID-eSales/oxideshop_ce/pull/643)

## [6.2.0] - 2018-03-27

### Added
- Possibility to delete shipping address via new method:
  - `OxidEsales\Eshop\Application\Component\UserComponent::deleteShippingAddress`
- Possibility to delete user account via new methods:
  - `OxidEsales\EshopCommunity\Application\Controller\AccountController::deleteAccount()`
  - `OxidEsales\EshopCommunity\Application\Controller\AccountController::isUserAllowedToDeleteOwnAccount()`
  - `OxidEsales\EshopCommunity\Application\Controller\AccountController::getAccountDeletionStatus()`
- Possibility for shop users to manage their reviews, configurable by admin:
  - New classes:
    - `OxidEsales\EshopCommunity\Application\Controller\AccountReviewController`
  - New methods:
    - `OxidEsales\EshopCommunity\Application\Controller\AccountController::isUserAllowedToManageOwnReviews`
    - `OxidEsales\EshopCommunity\Application\Controller\AccountController::getReviewAndRatingItemsCount`
    - `OxidEsales\EshopCommunity\Application\Controller\CompareController::isUserAllowedToManageOwnReviews`
    - `OxidEsales\EshopCommunity\Application\Controller\CompareController::getReviewAndRatingItemsCount`
    - `OxidEsales\EshopCommunity\Application\Model\Review::getProductReviewItemsCntByUserId`
    - `OxidEsales\EshopCommunity\Application\Model\Review::getReviewAndRatingListByUserId`    
  - New language constants in `Application/translations/[de/en]/lang.php`:
    - `ERROR_REVIEW_AND_RATING_NOT_DELETED`
    - `MY_REVIEWS`
  - New language constants in `Application/views/admin/[de/en]/lang.php`:
    - `SHOP_CONFIG_ALLOW_USERS_MANAGE_REVIEWS`
    - `SHOP_CONFIG_ALLOW_USERS_MANAGE_PRODUCT_REVIEWS`
- For displaying recommendations feature new method introduced:
  - `OxidEsales\EshopCommunity\Core\ViewConfig::getShowSuggest()`
- New settings which are configurable in admin area:
  - `blAllowSuggestArticle` - it's possible to disable recommendation feature.
  - `blAllowUsersToDeleteTheirAccount` - it's possible to allow users to delete their account.
  - `blAllowUsersToManageTheirReviews` - it's possible to allow users to manage their reviews.
- New methods:
  - `OxidEsales\EshopCommunity\Application\Model\User::isMallAdmin()`
  - `OxidEsales\EshopCommunity\Core\Registry::getRequest` [PR-626](https://github.com/OXID-eSales/oxideshop_ce/pull/626)
- Filter by working title in admin Selection lists list [PR-632](https://github.com/OXID-eSales/oxideshop_ce/pull/632)
- Article _oAmountPriceInfo list have unit prices calculated if quantity set for product [PR-619](https://github.com/OXID-eSales/oxideshop_ce/pull/619)
  - `fbrutamountprice` and `fnetamountprice` available for usage in template
  - prices already preformatted with current language/currency settings

### Changed
- Loading for non widget classes via `widget.php` entry point have been declined. To allow a class to be loaded
via `widget.php` it must extend `\OxidEsales\Eshop\Application\Component\Widget\WidgetController`.
- `SeoEncoderArticle::_prepareArticleTitle` now uses `_getUrlExtension()` method in place of hardcoded `.html` extension [PR-634](https://github.com/OXID-eSales/oxideshop_ce/pull/634). 
- Add ^ to version constraint on doctrine/dbal [PR-635](https://github.com/OXID-eSales/oxideshop_ce/pull/635)
- Model performance micro optimizations [PR-646](https://github.com/OXID-eSales/oxideshop_ce/pull/646)

### Deprecated
- Recommendations feature will be moved to separate module:
  - `OxidEsales\EshopCommunity\Application\Controller\SuggestController`
  - `OxidEsales\EshopCommunity\Core\ViewConfig::getShowSuggest()`
  - Config option - `blAllowSuggestArticle`
  - Language constants: `SHOP_CONFIG_ALLOW_SUGGEST_ARTICLE`, `HELP_SHOP_CONFIG_ALLOW_SUGGEST_ARTICLE`
- `sOXIDPHP` parameter in `config.inc.php`

### Removed

### Fixed
- Banner image upload is not working [PR-624](https://github.com/OXID-eSales/oxideshop_ce/pull/624)
- imagecreatefromjpeg can't handle sequential jpeg's correctly [PR-627](https://github.com/OXID-eSales/oxideshop_ce/pull/627)
- Support large module list in oxconfig table [PR-633](https://github.com/OXID-eSales/oxideshop_ce/pull/633)
- Use flow theme logo image in offline page [PR-637](https://github.com/OXID-eSales/oxideshop_ce/pull/637)
- Use correct performance checkbox for index page manufacturers [PR-625](https://github.com/OXID-eSales/oxideshop_ce/pull/625)
- VAT message for b2b users with valid company id [PR-495](https://github.com/OXID-eSales/oxideshop_ce/pull/495)

## [6.1.0] - 2018-01-23

### Added
- Added classes:
  - Core\Form\FormFieldsTrimmer
  - Core\Form\FormFieldsTrimmerInterface
- Template blocks:
  - admin_article_variant_selectlist
  - admin_article_variant_extended
  - admin_article_variant_language_edit
  - admin_article_variant_bottom_extended
  - admin_order_remark_type
  - admin_user_remark_type
- New methods:
  - `OxidEsales\EshopCommunity\Core\InputValidator::addValidationError`
  - `OxidEsales\EshopCommunity\Application\Controller\Admin\ActionsMain::checkAccessToEditAction()`
  - `OxidEsales\EshopCommunity\Application\Controller\Admin\AdminController::isNewEditObject()`
  - `OxidEsales\EshopCommunity\Application\Model\Actions::isDefault()`
  - `OxidEsales\EshopCommunity\Core\Model\BaseModel::isPropertyLoaded()`
  - `OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::disableTextEditor()`
  - `OxidEsales\EshopCommunity\Application\Controller\TextEditorHandler::isTextEditorDisabled()`
  - `OxidEsales\EshopCommunity\Application\Controller\Admin\AdminDetailsController::configureTextEditorHandler()`
  - `OxidEsales\EshopCommunity\Application\Controller\Admin\AdminDetailsController::getTextEditorHandler()`

### Changed
- In voucher series generation, if Coupon Number radio button checked, the number is marked as Required now. [PR-476](https://github.com/OXID-eSales/oxideshop_ce/pull/476)
- Display full field names in product filter dropdown. [PR-614](https://github.com/OXID-eSales/oxideshop_ce/pull/614)
- Use getAdminTplLanguageArray() in Admin only. [PR-592](https://github.com/OXID-eSales/oxideshop_ce/pull/592)
- Delivery dates from past shouldn't be displayed. [PR-543](https://github.com/OXID-eSales/oxideshop_ce/pull/543)
- Readme.md and Contributing.md files are updated.
- CSS adapted in OXID eShop Setup to reflect new design, extracted styles to separate file `Setup/out/src/main.css`
- The function isset on a not loaded property of a model with lazy loading loads the property if it's possible and returns true. To check if property is loaded use BaseModel::isPropertyLoaded()
- admin template `article_main.tpl`

### Deprecated
- \OxidEsales\EshopCommunity\Application\Controller\Admin\ArticleSeo::_getSaveObjectId
- \OxidEsales\EshopCommunity\Application\Component\Widget\ServiceMenu::getCompareItemsCnt
- \OxidEsales\EshopCommunity\Core\Utils::strRot13
- \OxidEsales\EshopCommunity\Core\InputValidator::_addValidationError
- \OxidEsales\EshopCommunity\Application\Model\Order::ORDER_STATE_INVALIDDElADDRESSCHANGED
- \OxidEsales\EshopCommunity\Application\Model\Diagnostics::$_sRevision
- \OxidEsales\EshopCommunity\Application\Model\Diagnostics::getRevision()
- \OxidEsales\EshopCommunity\Application\Model\Diagnostics::setRevision()
- \OxidEsales\EshopCommunity\Application\Model\FileChecker::$_sRevision
- \OxidEsales\EshopCommunity\Application\Model\FileChecker::setRevision()
- \OxidEsales\EshopCommunity\Application\Model\FileChecker::getRevision()
- \OxidEsales\EshopCommunity\Core\Config::getRevision()
- \OxidEsales\EshopCommunity\Core\Controller\BaseController::getRevision()

### Removed
- 'Your market' selection was removed from Setup wizard, as this value is no longer evaluated
- Database transaction was removed from finalizeOrder method. Fixes bug [#6736](https://bugs.oxid-esales.com/view.php?id=6736)

### Fixed
- [Missing translations](https://bugs.oxid-esales.com/view.php?id=6721)
- [Manufacturer Seo urls not properly stored in database](https://bugs.oxid-esales.com/view.php?id=6694)
- [Change robots.txt entry from "Disallow: /agb/" to "Disallow: /AGB/"](https://bugs.oxid-esales.com/view.php?id=6703)
- [Not trimmed ZIP-Codes"](https://bugs.oxid-esales.com/view.php?id=6693)
- [admin/oxajax.php needs to handle module not namespaced ajax container classes](https://bugs.oxid-esales.com/view.php?id=6729)
- [Compare links in lists do not work correctly](https://bugs.oxid-esales.com/view.php?id=5354)
- [Transparent gif looses transparency when generated to different size](https://bugs.oxid-esales.com/view.php?id=3194)
- Additional cache variable for Article::getAttributesDisplayableInBasket [PR-616](https://github.com/OXID-eSales/oxideshop_ce/pull/616)
- [RSS feed for categories not sorted desc by time](https://bugs.oxid-esales.com/view.php?id=6739)
- [VariantHandler always uses brutto price for MD Variants](https://bugs.oxid-esales.com/view.php?id=6761)
- Expire SEO links for correct shop id [PR-594](https://github.com/OXID-eSales/oxideshop_ce/pull/594)
- [Module with namespaces not working on Windows](https://bugs.oxid-esales.com/view.php?id=6737)

## [6.0.0] - 2017-11-17

### Fixed
- [in source/admin/oxajax.php ControllerClassNameResolver is not used for resolving container. Can't create custom drag&drop in mod](https://bugs.oxid-esales.com/view.php?id=6668)
- [admin/oxajax.php needs to handle module namespaced ajax container classes](https://bugs.oxid-esales.com/view.php?id=6711)
- [Disabled controls are not clearly visible as not writable](https://bugs.oxid-esales.com/view.php?id=6702)

## [6.0.0-rc.3] - 2017-11-02

### Changed
- `\OxidEsales\Eshop\Application\Controller\FrontendController::getUserSelectedSorting()`
checks if element to sort is configured in Admin.
- Removed `exec()` calls in setup.
- Pagination has been changed:
for example previously it was "Geschenke/100/", now it will be "Geschenke?pgNr=99".
In addition these pages come with "robots" meta tag "noindex, follow".

### Deprecated
- `\OxidEsales\Eshop\Application\Controller\Admin\AdminController::$_sShopVersion`
- `\OxidEsales\Eshop\Application\Controller\Admin\AdminController::_getShopVersionNr()`
- `\OxidEsales\Eshop\Core\Config::getVersion()`
- In `oxshops` table field - `OXVERSION` is deprecated. This field value will not be updated anymore and will contain
"6.0.0" value. To retrieve correct shop version `OxidEsales\Eshop\Core\ShopVersion::getVersion()` must be used.
- `\OxidEsales\Eshop\Core\Config::getEdition()`
- In `oxshops` table field - `OXEDITION` is deprecated. To retrieve OXID eShop edition
facts component should be used: `\OxidEsales\Facts\Facts::getEdition()`.
- `\OxidEsales\Eshop\Application\Controller\Admin\ShopRdfa::submitUrl()`, because GR-Notify page feature was removed.
- `\OxidEsales\Eshop\Application\Controller\Admin\ShopRdfa::getHttpResponseCode()`, because GR-Notify page feature was
removed.
- Template block in *Application/views/admin/tpl/shop_rdfa.tpl* - `admin_shop_rdfa_submiturl`, because GR-Notify page
feature was removed.
- Config option blLoadDynContents as it's part of dynamic pages.
- `\OxidEsales\Eshop\Core\ShopControl::$_blHandlerSet`. This property is not used anymore.
- `\OxidEsales\Eshop\Core\WidgetControl::$_blHandlerSet`. This property is not used anymore.

### Removed
- Dynamic pages related code including.
- GR-Notify page feature.

### Fixed
- https://bugs.oxid-esales.com/view.php?id=6474 with PR#457
- https://bugs.oxid-esales.com/view.php?id=6155 with PR#431
- https://bugs.oxid-esales.com/view.php?id=6579 with PR#487
- https://bugs.oxid-esales.com/view.php?id=6465 with PR #458
- https://bugs.oxid-esales.com/view.php?id=6683
- https://bugs.oxid-esales.com/view.php?id=6695
- https://bugs.oxid-esales.com/view.php?id=6716

### Security
- https://bugs.oxid-esales.com/view.php?id=6678


## [6.0.0-rc.2] - 2017-08-15

### Added
- Integrate new Admin UI from digidesk backend UI Kit
- ddoe/wysiwyg-editor-module was added as requirement of OXID eShop Community Edition in composer.json
- Grace period reset email is sent on grace period reset.
- User and admin sessions are detached on E_ERROR type errors (in register_shutdown_function).
- Translation for GENERAL_ARTICLE_OXVARMAXPRICE, [Pull Request 572](https://github.com/OXID-eSales/oxideshop_ce/pull/572), [Pull Request 573](https://github.com/OXID-eSales/oxideshop_ce/pull/573)
- Added mkdir if folders not exist in _copyFile method, [Pull Request 590](https://github.com/OXID-eSales/oxideshop_ce/pull/590)

### Changed
- language constant `HELP_SHOP_CONFIG_SETORDELETECURRENCY`, [Pull Request 547](https://github.com/OXID-eSales/oxideshop_ce/pull/547)
- language constant `SHOP_CONFIG_SETORDELETECURRENCY`, [Pull Request 547](https://github.com/OXID-eSales/oxideshop_ce/pull/547)
- template `admin/tpl/shop_config.tpl`, [Pull Request 547](https://github.com/OXID-eSales/oxideshop_ce/pull/547)
- Css from admin login page moved to `out/admin/src/login.css`, [Pull Request 558](https://github.com/OXID-eSales/oxideshop_ce/pull/558)
- Database migrations and views regeneration is operating system independent which makes OXID eShop installable on Windows.  
- Classes of the `\OxidEsales\Eshop\` namespace are real (empty) classes now and called [`unified namespace classes`](http://oxid-eshop-developer-documentation.readthedocs.io/en/latest/modules/using_namespaces_in_modules.html).
- [Pull Request 557: Remove duplicate directory separator](https://github.com/OXID-eSales/oxideshop_ce/pull/557)
- [Pull Request 561: Fixup basket wrapping calculation](https://github.com/OXID-eSales/oxideshop_ce/pull/561)
- Introduce colon and ellipsis, [Pull Request 579](https://github.com/OXID-eSales/oxideshop_ce/pull/579), [Pull Request 581](https://github.com/OXID-eSales/oxideshop_ce/pull/581)

### Deprecated
- iUtfMode in config.inc.php. This property will be removed in the future as the shop will always use UTF-8.
- Class Core/Email: Rename $SMTP_PORT to $smtpPort, [Pull Request 563](https://github.com/OXID-eSales/oxideshop_ce/pull/563)

### Removed
- Azure theme was extracted from the OXID eShop CE repository to [separate repository](https://github.com/OXID-eSales/azure-theme).
  - Azure theme should not be used for new projects.
  - In case there is a need to use azure theme, install it via command: `composer require oxid-esales/azure-theme:^1.4.1`.

### Fixed
- Date formatting in EXCEPTION_LOG.txt: textual representation of the day was replaced by numerical representation (01 to 31)
- iUtfMode in config.inc.php: backwards compatibility restored. This setting was removed, but it is introduced again, as some modules still might use it.
- BaseModel::_update(): backwards compatibility restored. Returns always true on success or throws an exception. 
- Removed duplicate directory separators in vendor directory calculation methods, [Pull Request 557](https://github.com/OXID-eSales/oxideshop_ce/pull/557)
- BaseController::executeFunction throws ERROR_MESSAGE_SYSTEMCOMPONENT_CLASSNOTFOUND for metadata v2 modules in some cases, [#0006627](https://bugs.oxid-esales.com/view.php?id=6627)
- Template directories local class cache is cleared on smarty reinitialization [Change](https://github.com/OXID-eSales/oxideshop_ce/blob/90bf9facc7f7d80f48f72e631555d0ac29a3e061/source/Core/UtilsView.php#L82)
- Change primary key of database table `oxstates` to composite, [#0005029](https://bugs.oxid-esales.com/view.php?id=5029)
- Issue with basket reservations causing wrong stock levels in high load scenarios, [#0006102](https://bugs.oxid-esales.com/view.php?id=6102)
- Deactivating a module which extends basket causes shop maintenance mode, [#0006659](https://bugs.oxid-esales.com/view.php?id=6659)
- Pass along shopid to call to _loadFromDb(), [Pull Request 571](https://github.com/OXID-eSales/oxideshop_ce/pull/571)

## [6.0.0-rc.1] - 2017-04-07

### Added
- [Pull Request 425: Compatibility with Apache 2.4](https://github.com/OXID-eSales/oxideshop_ce/pull/425)
- [Metadata version 2.0](http://oxid-eshop-developer-documentation.readthedocs.io/en/latest/modules/metadata/version20.html)
- Added classes and methods:
  - ModuleChainsGenerator::getActiveChain()
  - ModuleList::parseModuleChains()
  - Core\Module\ModuleTranslationPathFinder

### Changed
- [Pull Request 550: replace intval with typecast](https://github.com/OXID-eSales/oxideshop_ce/pull/550)
- [Pull Request 555: Removed a commented debugging line](https://github.com/OXID-eSales/oxideshop_ce/pull/555)  
- Module section `extend` in the file metadata.php gets validated since metadata version 2.0.
- Database columns were changed due to unification of OXID eShop editions.
- In case OXID development tools are installed, IDE Helper generator will be run on every composer install/update.
- Not loadable module classes are now shown in `Problematic files` section.
- Only backwards compatible classes (e.g oxarticle) or classes from virtual namespace can be extended by modules.
- PayPal module, which is compatible with OXID eShop 6, has been added to the compilation.
- Changed templates and blocks:
  - block `admin_order_overview_total`, file `admin/tpl/order_overview.tpl`.
  - template `admin/tpl/order_article.tpl` 
  - template `admin/tpl/order_overview.tpl`  
  - template `admin/tpl/include/order_info.tpl`  

### Deprecated
- Azure theme is deprecated and in next release it will be removed from compilation.
- Deprecated classes and methods: Search for the notation `@deprecated` in the sourcecode. At a later time, please 
  use [this overview of source code documentation](https://oxidforge.org/en/source-code-documentation-overview), 
  pick the version you need and follow the link to it. On the navigation to the left hand side you will find a link 
  called `Deprecated list` which leads you to the wanted information.

### Removed
- config.inc.php options `iUtfMode`, `sDefaultDatabaseConnection` and `blSkipEuroReplace` because shop is utf-8.
- config.inc.php option `vendorDirectory`. Instead the constant VENDOR_PATH was introduced.

### Fixed
- Module deactivation/deletion/cleanup issues fixed which occured because of namespaces in modules.


## [6.0-beta.3] - 2017-03-14

See 
- [OXID eShop v6.0.0 Beta3 is published](https://oxidforge.org/en/oxid-eshop-v6-0-0-beta3-is-published.html)


## [6.0-beta.2] - 2017-12-13

See 
- [OXID eShop v6.0.0 Beta2 is published](https://oxidforge.org/en/oxid-eshop-v6-0-0-beta2-published.html)


## [6.0-beta.1] - 2016-11-30

See 
- [OXID eShop v6.0.0 Beta1 released](https://oxidforge.org/en/oxid-eshop-v6-0-0-beta1-released.html)
- [OXID eShop v6.0.0 Beta1: Overview of Changes](https://oxidforge.org/en/oxid-eshop-v6-0-0-beta1-overview-of-changes.html)
- [OXID eShop v6.0.0 Beta1: Detailed Code Changelog](https://oxidforge.org/en/oxid-eshop-v6-0-0-beta1-detailed-code-changelog.html)


[6.2.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.1.0...v6.2.0
[6.1.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0.0...v6.1.0
[6.0.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0.0-rc.3...v6.0.0
[6.0.0-rc.3]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0.0-rc.2...v6.0.0-rc.3
[6.0.0-rc.2]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0.0-rc.1...v6.0.0-rc.2
[6.0.0-rc.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0-beta.3...v6.0.0-rc.1
[6.0-beta.3]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0-beta.2...v6.0-beta.3
[6.0-beta.2]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0-beta.1...v6.0-beta.2
[6.0-beta.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0-beta.1...v6.0-beta.2
