# Change Log for OXID eShop Community Edition Core Component

## v7.2.0 - 2024-10-14

### Added
- Translations for Change language and currency
- New parameter `oxid_esales.email.disable_order_emails` to enable and disable sending order emails

### Deprecated
- Filesystem module cache related services and interface will be refactored and some of them will be removed
- `OxidEsales\EshopCommunity\Application\Model\Attribute::_sTitle` property

### Fixed
- User registration in the Private Sales mode
- New item in basket message display [#0007548](https://bugs.oxid-esales.com/view.php?id=7548) [PR-964](https://github.com/OXID-eSales/oxideshop_ce/pull/964)
- Multiple language creation [#0007683](https://bugs.oxid-esales.com/view.php?id=7683)
- Remove unnecessary `<small>` tags from CHF currency
- Existing sessions should be destroyed on password change [#0007324](https://bugs.oxid-esales.com/view.php?id=7324)
- Forgot password message

### Changed
- Changed the exported user file name from `Export_recipient_` to `Export_user_recipient_status_` to better reflect the content of the export.
- User needs to log in again after changing his password

### Removed
- Obsolete demo data and images from the `source/out` directory
- PHP v8.1 support
