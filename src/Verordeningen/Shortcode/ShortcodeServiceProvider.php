<?php
/**
 * Provider which handles the registration of the shortcode generator.
 */

namespace OWC\PDC\Verordeningen\Shortcode;

use OWC\PDC\Base\Foundation\ServiceProvider;

/**
 * Provider which handles the registration of the shortcode generator.
 */
class ShortcodeServiceProvider extends ServiceProvider
{

    /**
     * Shortcode to be registered.
     *
     * @var string $shortcode
     */
    protected static $shortcode = 'pdc::verordeningen';

    /**
     * Register the shortcode.
     *
     * @return void
     */
    public function register()
    {
        $shortcode = new Shortcode();
        add_shortcode(self::$shortcode, [$shortcode, 'addShortcode']);
    }

    /**
     * The generation of the shortcode.
     *
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
