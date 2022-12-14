<?php
/**
 * Provider which handles the metabox registration.
 */

namespace OWC\PDC\Verordeningen\Metabox;

use OWC\PDC\Base\Foundation\ServiceProvider;

/**
 * Provider which handles the metabox registration.
 */
class MetaboxServiceProvider extends ServiceProvider
{

    /**
     * Register the providers.
     *
     * @return void
     */
    public function register()
    {
        $this->plugin->loader->addFilter('rwmb_meta_boxes', $this, 'registerMetaboxes', 10, 1);
    }

    /**
     * Register metaboxes.
     *
     * @param array $metaboxes
     *
     * @return array
     */
    public function registerMetaboxes($metaboxes)
    {

        $prefix = '_pdc-verordening';

        $metaboxes[] = [
            'id' => 'pdc-verordeningen',
            'title' => __('Verordening settings', 'pdc-verordeningen'),
            'post_types' => ['pdc-verordeningen'],
            'context' => 'normal',
            'priority' => 'high',
            'autosave' => true,
            'fields' => [
                [
                    'id' => "{$prefix}-link",
                    'name' => __('Verordening link', 'pdc-verordeningen'),
                    'desc' => __('URL including http(s)://', 'pdc-verordeningen'),
                    'type' => 'text',
                ],
                [
                    'id' => "{$prefix}-new-link",
                    'name' => __('Verordening new link', 'pdc-verordeningen'),
                    'desc' => __('URL including http(s)://', 'pdc-verordeningen'),
                    'type' => 'text',
                ],
                [
                    'id' => "{$prefix}-active-date",
                    'name' => esc_html__('Date new verordening active', 'pdc-verordeningen'),
                    'type' => 'date',
                    'js_options' => [
                        'dateFormat' => esc_html__('dd-mm-yy', 'pdc-verordeningen'),
                        'altFormat' => 'yy-mm-dd',
                        'changeMonth' => true,
                        'changeYear' => true,
                        'showButtonPanel' => true,
                        'minDate' => 0,
                    ],
                    'desc' => esc_html__('(dd-mm-yy)', 'pdc-verordeningen'),
                ],
            ],
        ];

        return $metaboxes;
    }
}
