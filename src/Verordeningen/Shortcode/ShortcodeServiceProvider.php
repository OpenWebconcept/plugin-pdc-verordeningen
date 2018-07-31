<?php

namespace OWC\PDC\Verordeningen\Shortcode;

use OWC\PDC\Base\Foundation\ServiceProvider;

class ShortcodeServiceProvider extends ServiceProvider
{

	/**
	 * @var string
	 */
	protected static $shortcode = 'pdc::verordeningen';

	/**
	 * Register the shortcode.
	 */
	public function register()
	{
		$shortcode = new Shortcode();
		add_shortcode(self::$shortcode, [$shortcode, 'addShortcode']);
	}

	/**
	 * @param null $id
	 *
	 * @return string
	 */
	public static function generateShortcode($id = null)
	{
		$shortcode = sprintf('[%s id="%d"]', self::$shortcode, $id);

		return sprintf('<code>%s</code>', $shortcode);
	}
}
