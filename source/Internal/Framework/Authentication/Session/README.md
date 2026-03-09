**Important:** Session authentication is intended for AJAX requests only. Do not use it for API endpoints — use token-based authentication instead.

## Usage

### Frontend routes

Add `#[SessionUser]` to a controller method or class.

```php
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\SessionUser;

class MyController
{
    #[SessionUser]
    public function profile(Request $request): Response
    {
        $user = $request->attributes->get('_user');
    }
}
```

### Admin routes

Add `#[AdminSessionUser]` to restrict a route to admin sessions.

```php
use OxidEsales\EshopCommunity\Internal\Framework\Authentication\Session\AdminSessionUser;

class MyAdminController
{
    #[AdminSessionUser]
    public function overview(Request $request): Response
    {
        // any authenticated admin
    }

    #[AdminSessionUser(roles: ['ROLE_ADMIN_MALL'])]
    public function globalSettings(Request $request): Response
    {
        // malladmin only
    }
}
```
