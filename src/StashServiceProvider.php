<?php

/*
 * This file is part of StashServiceProvider
 *
 * (c) Mashkin <git@mashkin.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mashkin\Pimple\Provider\StashServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Stash\Pool;

class StashServiceProvider implements ServiceProviderInterface
{
	public function register(Container $c)
	{
		if (!isset($c['stash.driver.default_class'])) {
			$c['stash.driver.default_class'] = 'Ephemeral';
		}

		if (!isset($c['stash.default_options'])) {
			$c['stash.default_options'] = array();
		}

		$c['stashes.options_initializer'] = $c->protect(function () use ($c) {
			if (!isset($c['stashes.driver.class'])) {
				$c['stashes.driver.class'] = array();
			}

			if (!isset($c['stashes.options'])) {
				$c['stashes.options'] = array();
			}

			$tmp = $c['stashes.options'];

			if (isset($c['stash.options'])) {
				$tmp['default'] = $c['stash.options'];
			}

			if ($tmp instanceof Container) {
				$keys = $tmp->keys();
			} else {
				$keys = array_keys($tmp);
			}

			foreach ($keys as $name) {
				$tmp[$name] = array_replace($c['stash.default_options'], $tmp[$name]);

				if (!isset($c['stashes.driver.class'][$name])) {
					$c['stashes.driver.class'][$name] = $c['stash.driver.default_class'];
				}

				if (!isset($c['stashes.default'])) {
					$c['stashes.default'] = $name;
				}
			}

			$c['stashes.options'] = $tmp;
		});

		$c['stashes.driver'] = function ($c) {
			$c['stashes.options_initializer']();
			$drivers = new Container();

			if ($c['stashes.options'] instanceof Container) {
				$keys = $c['stashes.options']->keys();
			} else {
				$keys = array_keys($c['stashes.options']);
			}

			foreach ($keys as $name) {
				$options = $c['stashes.options'][$name];
				$drivers[$name] = $drivers->share(function ($drivers) use ($c, $name, $options) {
					$class = $c['stashes.driver.class'][$name];
					if (substr($class, 0 , 1) !== '\\') {
						$class = sprintf('\\Stash\\Driver\\%s', $class);
					}
					$driver = new $class($options);
					return $driver;
				});
			}

			return $drivers;
		};

		$c['stashes'] = function ($c) {
			$stashes = new Container();

			if ($c['stashes.driver'] instanceof Container) {
				$keys = $c['stashes.driver']->keys();
			} else {
				$keys = array_keys($c['stashes.driver']);
			}

			foreach ($keys as $name) {
				$driver = $c['stashes.driver'][$name];
				if ($c['stashes.default'] === $name) {
					$driver = $c['stash.driver'];
				}

				$stashes[$name] = function ($stashes) use ($driver) {
					return new Pool($driver);
				};
			}

			return $stashes;
		};

		$c['stash.driver'] = $c->factory(function ($c) {
			$drivers = $c['stashes.driver'];
			return $drivers[$c['stashes.default']];
		});

		$c['stash'] = $c->factory(function ($c) {
			$stashes = $c['stashes'];
			return $stashes[$c['stashes.default']];
		});
	}
}
