# Change Log for OXID eShop Community Edition Core Component

## v7.6.0 - Unreleased

### Added

### Changed
- Increased bcrypt password hashing cost from 10 to 12 to improve resistance against brute-force attacks; existing password hashes will be transparently rehashed on next login.

### Deprecated
- `Argon2IPasswordHashService` is deprecated and will be removed in a future major version; use `BcryptPasswordHashService` instead.

### Fixed

### Removed