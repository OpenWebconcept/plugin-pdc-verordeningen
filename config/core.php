<?php

return [

	/**
	 * Service Providers.
	 */
	'providers'    => [
		/**
		 * Global providers.
		 */
		OWC\PDC\Verordeningen\PostType\VerordeningenPostTypeServiceProvider::class,
		OWC\PDC\Verordeningen\Shortcode\ShortcodeServiceProvider::class,
		/**
		 * Providers specific to the admin.
		 */
		'admin' => [
			OWC\PDC\Verordeningen\Admin\QuickEdit\QuickEditServiceProvider::class,
			OWC\PDC\Verordeningen\Metabox\MetaboxServiceProvider::class,
		],
	],

	/**
	 * Dependencies upon which the plugin relies.
	 *
	 * Should contain: label, version, file.
	 */
	'dependencies' => [
		[
			'type'    => 'plugin',
			'label'   => 'OpenPDC Base',
			'file'    => 'pdc-base/pdc-base.php',
			'version' => '2.0.0',
			
		]
	]
];
