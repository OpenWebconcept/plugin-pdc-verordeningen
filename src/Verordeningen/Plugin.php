<?php

namespace OWC\PDC\Verordeningen;

use OWC\PDC\Verordeningen\Plugin\BasePlugin;

class Plugin extends BasePlugin
{

	/**
	 * Name of the plugin.
	 *
	 * @var string
	 */
	const NAME = 'pdc-verordeningen';

	/**
	 * Version of the plugin.
	 * Used for setting versions of enqueue scripts and styles.
	 *
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * Boot the plugin.
	 * Called on plugins_loaded event
	 */
	public function boot()
	{
		$this->config->setProtectedNodes(['core']);
		$this->config->boot();

		$this->bootServiceProviders('register');

		$this->bootServiceProviders('register', is_admin() ? 'admin' : 'frontend');

		$this->bootServiceProviders('boot');

		$this->bootServiceProviders('boot', is_admin() ? 'admin' : 'frontend');

		$this->loader->addAction('init', $this, 'filterPlugin', 9);

		$this->loader->register();
	}
}
