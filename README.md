# rest-action
Rest(ful) aware PSR-15 Middleware library


## Getting started

RestAction provide an easy way to build versioned API endpoints and support proactive content negotiation.

```php
$action = new RestAction();

/*
 * Assume our RestAction has `application/xml` and `application/json` available
 * as media types, in order of highest-to-lowest preference for delivery
 */
$action->registerSerializer('application/*json', JsonSerializer::class);

/*
 * Assume our RestAction has two Endpoints version available with
 * requests like `https://api.example/users` with `API-VERSION:1.0.0` or `API-VERSION:2.0.0` header
 *
 * Note that it support Semver matching, so the resource is also available with
 * requests like `https://api.example/users` with `API-VERSION:1` or `API-VERSION:2` header
 */
$action->registerEndpoint('1.0.0', UsersEndpointV1::class);
$action->registerEndpoint('2.0.0', UsersEndpointV2::class);
```
