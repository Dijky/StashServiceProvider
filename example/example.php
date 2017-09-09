<?php
use Pimple\Container;
use Dijky\Pimple\Provider\StashServiceProvider\StashServiceProvider;

require '../vendor/autoload.php';

$app = new Container();

// Register the Dijky\Pimple\Provider\StashServiceProvider\StashServiceProvider
$app->register(new StashServiceProvider());

// Set options for default driver
$app['stashes.options'] = array();
$app['stashes.driver.class'] = array();

if(Stash\Driver\Apc::isAvailable()) {
    $app['stashes.driver.class'] = array(
		'default' => 'Apc'
	);
	$app['stashes.options'] = array(
		'default' => array(
			'ttl' => 24*60*60, // 24 hours
			'namespace' => 'example'
		)
	);
} else {
    $app['stashes.driver.class'] = array(
		'default' => 'FileSystem'
	);
	$app['stashes.options'] = array(
		'default' => array(
			'path' => __DIR__ . '/cache/stash/',
			'dirSplit' => 2,
			'filePermissions' => 0666,
			'dirPermissions' => 0777
		)
	);
}

// ...


var_dump($app['stashes']['default'] instanceof Stash\Pool);    // true
var_dump($app['stash'] === $app['stashes']['default']);        // true