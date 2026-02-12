**Important:** Session authentication is intended for AJAX requests only. Do not use it for API endpoints — use token-based authentication instead.

## Usage

Add `#[SessionUser]` to a controller method or class:

```php
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\SessionUser;

class MyController
{
    #[SessionUser]
    public function profile(Request $request): Response
    {
        // ...
    }

    #[SessionUser(roles: ['ROLE_ADMIN'])]
    public function dashboard(Request $request): Response
    {
        // admin-only
    }
}
```
