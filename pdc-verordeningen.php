<?php

/**
 * Plugin Name:       PDC Verordeningen
 * Plugin URI:        https://www.openwebconcept.nl
 * Description:       PDC Verordeningen
 * Version:           1.0.8
 * Author:            Yard Internet
 * Author URI:        https://www.yardinternet.nl/
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       pdc-verordeningen
 * Domain Path:       /languages
 */

use OWC\PDC\Verordeningen\Autoloader;
use OWC\PDC\Verordeningen\Foundation\Plugin;

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
    die;
}

/**
 * Autoload plugin files if not already loaded
 */
if (! class_exists(\OWC\PDC\Verordeningen\Foundation\Plugin::class)) {
    $composerAutoload = __DIR__ . '/vendor/autoload.php';
    if (file_exists($composerAutoload)) {
        require_once $composerAutoload;
    } else {
        require_once __DIR__ . '/autoloader.php';
        $autoloader = new Autoloader();
    }
}

/**
 * Begin execution of the plugin
 *
 * This hook is called once any activated plugins have been loaded. Is generally used for immediate filter setup, or
 * plugin overrides. The plugins_loaded action hook fires early, and precedes the setup_theme, after_setup_theme, init
 * and wp_loaded action hooks.
 */
add_action('plugins_loaded', function () {
    add_action('after_setup_theme', function () {
        (new Plugin(__DIR__))->boot();
    });
}, 10);
