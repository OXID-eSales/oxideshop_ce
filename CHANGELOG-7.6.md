# Change Log for OXID eShop Community Edition Core Component

## v7.6.0 - Unreleased

### Added

### Changed
- Console commands return consistent exit codes on success (0) and failure (1)
- Default bcrypt password hashing cost was increased [#0007727](https://bugs.oxid-esales.com/view.php?id=7727)

### Deprecated
- `Argon2IPasswordHashService`

### Fixed
- Random collision in `RandomTokenGenerator` tests

### Removed