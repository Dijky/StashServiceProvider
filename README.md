# StashServiceProvider
A Pimple 3 ServiceProvider integrating the Stash caching component

## Usage

Find a simple example of usage in [example.php](example/example.php).

## Changelog

### 2.0.1

- Replaced incomplete and broken example with a working, simplistic `example/example.php`

### 2.0

- Supports Silex ~2.0 / Pimple ~3.0
- [BC BREAK] Dropped support for Silex 1.x / Pimple 1.x
- [BC BREAK] Changed namespace to `Dijky\Pimple\Provider\StashServiceProvider` (formerly `Mashkin\Silex\...`)
- No dependency on Silex (only Pimple)
- Dijky takes over because original author (Mashkin) abandons the project
