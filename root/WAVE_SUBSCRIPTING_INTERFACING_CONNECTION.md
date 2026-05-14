# Wave: Subscripting + Interfacing Connection

## Added

- `BridgeTarget::SCREEN_SUBSCRIPTING_PRESENTATION`
- `SubscriptionPresentationToInterfacingScreenBridgeInterface`
- `SubscriptionPresentationToInterfacingScreenBridge`
- `services_subscripting_interfacing.yaml`
- unit test for shape-based payload bridge
- bridge/audit docs

## Runtime checks

```bash
composer dump-autoload
php bin/console lint:container
phpunit --filter SubscriptionPresentationToInterfacingScreenBridgeTest
```
