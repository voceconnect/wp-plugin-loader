# WP Plugin Loader

WP Plugin Loader provides an interface for loading plugin dependencies.

## Installation
Add WP Plugin loader as a dependency with Composer:
> composer require voceconnect/wp-plugin-loader

Then ensure that your composer autoload file is properly included:

```php
require_once 'vendor/autoload.php';
```

Initialize the PluginLoader:

```php
$pluginLoader = new Voce\PluginLoader\PluginLoader();
$pluginLoader->registerHooks();
```

## Loading Plugins
To be sure that a plugin is loaded, pass the plugin details to the `wp_load_plugin` action.  For example, the below will load the 'Hello Dolly' plugin:

```php
do_action('wp_load_plugin', 'hello-dolly', 'hello.php');
```

Alternatively, the action name can be retrieved from the class constant `LOAD_ACTION`:

```php
do_action(Voce\PluginLoader\PluginLoader::LOAD_ACTION, 'hello-dolly', 'hello.php');
```

...of course this is much shorter if you're already using the `use` statement.