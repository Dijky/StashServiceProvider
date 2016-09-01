# StashServiceProvider
A Pimple 3 ServiceProvider integrating the Stash caching component

## Usage

```php

// Register the Dijky\Pimple\Provider\StashServiceProvider\StashServiceProvider
$app->register(new StashServiceProvider());

// Set options for default driver
$app['stashes.options'] = array();
$app['stashes.driver.class'] = array();

if(Stash\Driver\Apc::isAvailable()) {
    $app['stashes.driver.class']['default'] = 'Apc';
	$app['stashes.options']['default'] = array(
		'ttl' => 24*60*60,
		'namespace' => sha1($app['name'])
	);
} else {
    $app['stashes.driver.class']['default'] = 'FileSystem';
	$app['stashes.options']['default'] = array(
		'path' => __DIR__ . '/cache/stash/',
		'dirSplit' => 2,
		'filePermissions' => 0666,
		'dirPermissions' => 0777
	);
}

// ...


$app['stashes']['default'] instanceof Stash\Pool    // true
$app['stash'] === $app['stashes']['default']        // true
```
## Changelog

### New in 2.0

- Supports Silex ~2.0 / Pimple ~3.0
- [BC BREAK] Dropped support for Silex 1.x / Pimple 1.x
- [BC BREAK] Changed namespace to `Dijky\Pimple\Provider\StashServiceProvider` (formerly `Mashkin\Silex\...`)
- No dependency on Silex (only Pimple)
- Dijky takes over because original author (Mashkin) abandons the project
