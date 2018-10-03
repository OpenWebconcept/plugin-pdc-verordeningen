<?php
/**
 * Provider which handles registration of posttype.
 */

namespace OWC\PDC\Verordeningen\PostType;

use OWC\PDC\Base\Foundation\ServiceProvider;
use OWC\PDC\Verordeningen\Shortcode\ShortcodeServiceProvider;

/**
 * Provider which handles registration of posttype.
 */
class VerordeningenPostTypeServiceProvider extends ServiceProvider
{

    /**
     * Prefix of the posttype.
     *
     * @var string $prefix
     */
    protected $prefix = '_pdc-verordening';

    /**
     * Name of posttype.
     *
     * @var string $postType
     */
    protected $postType = 'pdc-verordeningen';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->plugin->loader->addAction('init', $this, 'registerPostType');
    }

    /**
     * Register the Verordeningen posttype.
     *
     * @return void
     */
    public function registerPostType()
    {

        if (!function_exists('register_extended_post_type')) {
            require_once $this->plugin->getRootPath() . '/src/Verordeningen/vendor/johnbillion/extended-cpts/extended-cpts.php';
        }

        $labels = [
            'name'               => __('Verordeningen', 'pdc-verordeningen'),
            'singular_name'      => __('Verordening', 'pdc-verordeningen'),
            'menu_name'          => __('Verordeningen', 'pdc-verordeningen'),
            'name_admin_bar'     => __('Verordeningen', 'pdc-verordeningen'),
            'add_new'            => __('Add new verordening', 'pdc-verordeningen'),
            'add_new_item'       => __('Add new verordening', 'pdc-verordeningen'),
            'new_item'           => __('New verordening', 'pdc-verordeningen'),
            'edit_item'          => __('Edit verordening', 'pdc-verordeningen'),
            'view_item'          => __('View verordening', 'pdc-verordeningen'),
            'all_items'          => __('All verordeningen', 'pdc-verordeningen'),
            'search_items'       => __('Search verordeningen', 'pdc-verordeningen'),
            'parent_item_colon'  => __('Parent verordeningen:', 'pdc-verordeningen'),
            'not_found'          => __('No verordeningen found.', 'pdc-verordeningen'),
            'not_found_in_trash' => __('No verordeningen found in Trash.', 'pdc-verordeningen'),
        ];

        $args = [
            'labels'             => $labels,
            'description'        => __('Verordeningen', 'pdc-verordeningen'),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'supports'           => ['title'],
            'show_in_feed'       => false,
            'archive'            => false,
            'admin_cols'         => [
                'link'         => [
                    'title'    => __('Verordening link', 'pdc-verordeningen'),
                    'meta_key' => "{$this->prefix}-link",
                ],
                'new-link'     => [
                    'title'    => __('Verordening new link', 'pdc-verordeningen'),
                    'meta_key' => "{$this->prefix}-new-link",
                ],
                'active-date'  => [
                    'title'       => __('Date new verordening active', 'pdc-verordeningen'),
                    'meta_key'    => "{$this->prefix}-active-date",
                    'date_format' => 'd/m/Y',
                ],
                'code-preview' => [
                    'title'    => __('Verordening shortcode', 'pdc-verordeningen'),
                    'function' => function () {
                        echo ShortcodeServiceProvider::generateShortcode(get_the_ID());
                    },
                ],
                'published'    => [
                    'title'       => __('Published', 'pdc-verordeningen'),
                    'post_field'  => 'post_date',
                    'date_format' => 'd M Y',
                ],
            ],
        ];

        return register_extended_post_type($this->postType, $args, $labels);
    }
}
