# Change Log for OXID eShop Community Edition Core Component

## v7.2.0 - unreleased

### Added
- Translations for Change language and currency
- Applying multiple vouchers will not reduce the basket value below zero

### Deprecated
- Filesystem module cache related services and interface will be refactored and some of them will be removed
- `OxidEsales\EshopCommunity\Application\Model\Attribute::_sTitle` property

### Fixed
- User registration in the Private Sales mode
- New item in basket message display [#0007548](https://bugs.oxid-esales.com/view.php?id=7548) [PR-964](https://github.com/OXID-eSales/oxideshop_ce/pull/964)
- Remove unnecessary `<small>` tags from CHF currency

### Changed
-  

### Removed
- Obsolete demo data and images from the `source/out` directory
- PHP v8.1 support
