# Change Log for OXID eShop 6 Community Edition

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

### Changed

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
- template source/Application/views/admin/tpl/dyn_econda.tpl
- template source/Application/views/admin/tpl/dynscreen.tpl
- template source/Application/views/admin/tpl/dynscreen_list.tpl
- template source/Application/views/admin/tpl/dynscreen_local.tpl

### Fixed
- [Missing translations](https://bugs.oxid-esales.com/view.php?id=6721)
- [Manufacturer Seo urls not properly stored in database](https://bugs.oxid-esales.com/view.php?id=6694)
- [Change robots.txt entry from "Disallow: /agb/" to "Disallow: /AGB/"](https://bugs.oxid-esales.com/view.php?id=6703)

### Security

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


[Unreleased]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0.0...HEAD
[6.0.0]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0.0-rc.3...v6.0.0
[6.0.0-rc.3]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0.0-rc.2...v6.0.0-rc.3
[6.0.0-rc.2]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0.0-rc.1...v6.0.0-rc.2
[6.0.0-rc.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0-beta.3...v6.0.0-rc.1
[6.0-beta.3]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0-beta.2...v6.0-beta.3
[6.0-beta.2]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0-beta.1...v6.0-beta.2
[6.0-beta.1]: https://github.com/OXID-eSales/oxideshop_ce/compare/v6.0-beta.1...v6.0-beta.2
